<?php

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

function getGroupNameFromeCompetitorInfo($src) {
	$groupinfo = preg_split("/(@g@)/", $src);
	return array_values(array_filter($groupinfo, function($var) { return !($var & 1); }, ARRAY_FILTER_USE_KEY));
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