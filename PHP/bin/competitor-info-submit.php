<?php

include("../common/common-define.php");
include("../common/common-functions.php");

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

$result = mysqli_query($dbc, "SELECT isstart_sign FROM comps_info WHERE comps_name = '$compsname'");
checkIsLegal(mysqli_num_rows($result) > 0, $dbc, 9);
mysqli_data_seek($result, 0);
checkIsLegal(mysqli_fetch_array($result)[0] == 1, $dbc, 10);

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

?>