<?php

include("../common/common-define.php");

session_start();
checkIsLegal(isset($_SESSION["compsname"]) && isset($_SESSION["username"]) && isset($_SESSION["authority"]), null, 1);

$compsname_raw = $_SESSION["compsname"];
$judgename_raw = $_SESSION["username"];
$users_authority = (int)$_SESSION["authority"];
checkIsLegal($users_authority == 4 && isset($_POST["compsname"]) && isset($_POST["playername"]) && isset($_POST["playergroup"]) && isset($_POST["workname"]) && isset($_POST["judgename"]) && isset($_POST["scoredetailed"]) && isset($_POST["scoresum"]) && isset($_POST["current"]), null, 2);

$compsname = urldecode($_POST["compsname"]);
$playername = urldecode($_POST["playername"]);
$playergroup = urldecode($_POST["playergroup"]);
$workname = urldecode($_POST["workname"]);
$judgename = urldecode($_POST["judgename"]);
$scoredetailed = urldecode($_POST["scoredetailed"]);
$scoresum = (int)urldecode($_POST["scoresum"]);
$current = (int)urldecode($_POST["current"]);
checkIsLegal($compsname_raw == $compsname && $judgename_raw == $judgename, null, 3);

$scorel = preg_split("/(@c@|@r@)/", $scoredetailed);
$scorer = array_values(array_filter($scorel, function($var) { return ($var & 1); }, ARRAY_FILTER_USE_KEY));
$scorem = array_sum($scorer);
checkIsLegal($scorem == $scoresum && $scoresum != 0, null, 4);

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$query = "SELECT users_id, works_number, reviewed_number FROM users_info WHERE users_nickname = '$judgename' AND users_authority = 4 AND comps_name = '$compsname'";
$result = mysqli_query($dbc, $query);
checkIsLegal(mysqli_num_rows($result) > 0, $dbc, 5);

mysqli_data_seek($result, 0);
$row = mysqli_fetch_array($result);
$userid = $row[0];
$worksnumber = (int)$row[1];
$reviewednumber = (int)$row[2];
checkIsLegal($worksnumber != $reviewednumber && $current == $reviewednumber, $dbc, 6);

$query = "SELECT competitor_info, score_rubric FROM comps_info WHERE comps_name = '$compsname'";
$result = mysqli_query($dbc, $query);
checkIsLegal(mysqli_num_rows($result) > 0, $dbc, 7);

mysqli_data_seek($result, 0);
$row = mysqli_fetch_array($result);
$competitorinfo = competitorInfoToArray($row[0]);
$scorerubric = $row[1];
checkIsLegal(($current == array_search(Array($playername, $workname, $playergroup), $competitorinfo)) && competitorScoreToArray($scorerubric, $playergroup, $scoredetailed), $dbc, 8);

$query = "INSERT INTO competitor_score (comps_name, player_name, player_group, work_name, judge_name, score_detailed, score_sum) VALUES ('$compsname', '$playername', '$playergroup', '$workname', '$judgename', '$scoredetailed', '$scoresum')";
$result1 = mysqli_query($dbc, $query);
$current++;
$query = "UPDATE users_info SET reviewed_number = '$current' WHERE users_id = '$userid'";
$result2 = mysqli_query($dbc, $query);

if ($result1 == 1 && $result2 == 1 && (int)$current < (int)$worksnumber) {
	echo $current;
}
else if ((int)$current >= (int)$worksnumber) {
	echo "finished";
}
else {
	checkIsLegal(false, $dbc, 9);
}
mysqli_close($dbc);

function competitorInfoToArray($src) {
	$groupinfo = preg_split("/(@g@)/", $src);
	$group = array_values(array_filter($groupinfo, function($var) { return !($var & 1); }, ARRAY_FILTER_USE_KEY));
	$info = array_values(array_filter($groupinfo, function($var) { return ($var & 1); }, ARRAY_FILTER_USE_KEY));

	$solution = Array();
	for ($i = 0; $i < count($group); $i++) {
		$playerwork = preg_split("/(@r@)/", $info[$i]);
		$player = array_values(array_filter($playerwork, function($var) { return !($var & 1); }, ARRAY_FILTER_USE_KEY));
		$work = array_values(array_filter($playerwork, function($var) { return ($var & 1); }, ARRAY_FILTER_USE_KEY));
		for ($j = 0; $j < count($player); $j++) {
			array_push($solution, Array($player[$j], $work[$j], $group[$i]));
		}
	}

	return $solution;
}

function competitorScoreToArray($src, $groupname, $scores) {
	$groupinfo = preg_split("/(@g@)/", $src);
	$group = array_values(array_filter($groupinfo, function($var) { return !($var & 1); }, ARRAY_FILTER_USE_KEY));
	$info = array_values(array_filter($groupinfo, function($var) { return ($var & 1); }, ARRAY_FILTER_USE_KEY));

	$isselected = false;
	for ($i = 0; $i < count($group); $i++) {
		if ($group[$i] == $groupname) {
			$isselected = true;
			$limitscore = preg_split("/(@r@)/", $info[$i]);
			break;
		}
	}
	if (!$isselected) { return false; }
	
	$limit = array_values(array_filter($limitscore, function($var) { return !($var & 1); }, ARRAY_FILTER_USE_KEY));
	$score = array_values(array_filter($limitscore, function($var) { return ($var & 1); }, ARRAY_FILTER_USE_KEY));

	$info_raw = preg_split("/(@c@|@r@)/", $scores);
	$limit_raw = array_values(array_filter($info_raw, function($var) { return !($var & 1); }, ARRAY_FILTER_USE_KEY));
	$score_raw = array_values(array_filter($info_raw, function($var) { return ($var & 1); }, ARRAY_FILTER_USE_KEY));

	if ($limit != $limit_raw) { return false; }

	for ($i = 0; $i < count($limit_raw); $i++) {
		if($score_raw[$i] < preg_split("/(@s@)/", $score[$i])[0] || $score_raw[$i] > preg_split("/(@s@)/", $score[$i])[1]) {
			return false;
		}
	}

	return $score_raw;
}

function checkIsLegal($condition, $sqlconnection = null, $code) {
	if (!$condition) {
		echo "failed"/*."--code:".$code*/;
		if (!is_null($sqlconnection)) { mysqli_close($sqlconnection); }
		exit();
	}
}

?>