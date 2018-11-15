<?php

//error_reporting(E_ALL ^ E_NOTICE);
include("PHP/common/common-define.php");
include("PHP/common/common-functions.php");

session_start();
//checkIsLegal(isset($_POST["compsname"]), null, 1);
$compsname = "第六届班徽班旗设计大赛";
//$compsname = "第六届团委学生会换届大会";

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$query = "SELECT competitor_info, score_rubric FROM comps_info WHERE comps_name = '$compsname'";
$result = mysqli_query($dbc, $query);
checkIsLegal(mysqli_num_rows($result) > 0, $dbc, 2);
mysqli_data_seek($result, 0);
$row = mysqli_fetch_array($result);
$compsysScoreInfo = getCompsysScoreInfoByCompsysInfo($row[0], $row[1]);

$query = "SELECT player_name, player_group, work_name, score_detailed, score_sum FROM competitor_score WHERE  comps_name = '$compsname'";
$result = mysqli_query($dbc, $query);
checkIsLegal(mysqli_num_rows($result) > 0, $dbc, 3);
$compsysScoreInfo = Array();
while($row = mysqli_fetch_assoc($result)) { array_push($compsysScoreInfo, array_values($row)); }
// TODO: 更改compsysScoreInfo变量名为见名知意变量名，并增加数据处理和分数查询子函数。


/*for($i = 0; $i < count($compsysScoreInfo[0]); $i++) {
	echo $compsysScoreInfo[0][$i];
	echo "<table border = \"1px\">";

	echo "<tr>";
	for($j = 0; $j < count($compsysScoreInfo[1][$i]); $j++) {
		echo "<th>";
		echo $compsysScoreInfo[1][$i][$j];
		echo "</th>";
	}
	echo "</tr>";

	for($j = 0; $j < count($compsysScoreInfo[2][$i]); $j++) {
		echo "<tr><td>";
		echo $compsysScoreInfo[2][$i][$j];
		echo "</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>";
	}

	echo "</table>";
}*/

print_r($compsysScoreInfo);

function fillCompetitorsInformation($compsysInfo) {
	// 选手信息填充子函数
}

function getCompsysScoreInfoByCompsysInfo($competitorInfo, $scoreRubric) {
	// 竞赛信息提取子函数

	/// 函数功能：
	// 将竞赛信息处理成用于最终分数表格显示的数据，返回值共有四列：
	// 第0列：分组名称，用于选项卡的显示。若各组参赛人员相同，则添加“总分”组。
	// 第1列：各组的评分细则，用于表头的显示。
	// 第2列：各组的参赛人员，用于表格参赛队名称的显示。
	// 第3列：各组（不包含“总分”组）各参赛人员的作品名称。

	// 注：
	// 只需判断参赛人员的组数和作品名称的组数是否相同即可判断是否有“总分”组。

	/// 闭包函数：
	function EVEN($var) { return !($var & 1); }	// 取数组中第0,2,4……号元素
	function ODD($var) { return ($var & 1); }	// 取数组中第1,3,5……号元素

	/// 函数实现：
	$groupInfo = preg_split("/(@g@)/", $scoreRubric);

	$group = array_values(array_filter($groupInfo, "EVEN", ARRAY_FILTER_USE_KEY));
	$info = array_values(array_filter($groupInfo, "ODD", ARRAY_FILTER_USE_KEY));

	foreach($info as &$info_item) {
		// 取出评分细则并扩展为表格键名
		$info_item = array_values(array_filter(preg_split("/(@r@)/", $info_item), "EVEN", ARRAY_FILTER_USE_KEY));
		array_unshift($info_item, "编号", "选手姓名", "作品名称");
		array_push($info_item, "总分");
	}
	unset($info_item);

	// 取出参赛选手名称及作品名称并分组
	$competitor = array_values(array_filter(preg_split("/(@g@)/", $competitorInfo), "ODD", ARRAY_FILTER_USE_KEY));
	$playerName = $workName = Array();
	foreach($competitor as $competitor_item) {
		$cache = preg_split("/(@r@)/", $competitor_item);
		array_push($playerName, array_values(array_filter($cache, "EVEN", ARRAY_FILTER_USE_KEY)));
		array_push($workName, array_values(array_filter($cache, "ODD", ARRAY_FILTER_USE_KEY)));
	}

	$singleRow = array_unique($playerName, SORT_REGULAR);	// 求参赛选手名称的极大无关组
	if(count($singleRow) == 1 && count($group) > 1) {
		// 若剩下的行数为1（所有项都相同）则添加“总分”组
		array_push($group, "总分");
		array_push($info, array_merge(Array("编号", "选手姓名"), $group));
		array_push($playerName, $singleRow[0]);
	}

	return Array($group, $info, $playerName, $workName);
}

?>