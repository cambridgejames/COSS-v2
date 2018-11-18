<?php

//error_reporting(E_ALL ^ E_NOTICE);
include("PHP/common/common-define.php");
include("PHP/common/common-functions.php");

session_start();
//checkIsLegal(isset($_POST["compsname"]), null, 1);
$compsname = "第六届班徽班旗设计大赛";
//$compsname = "第六届团委学生会换届大会";

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$tableTitle = getCompsysScoreInfo($compsname, $dbc);
$compsysScoreInfo = getSerializedScore($compsname, $dbc);

// print_r($tableTitle);
// print_r($compsysScoreInfo);
// print_r(getScoresByNamAndGroup($compsysScoreInfo, "1701班", "班徽组"));
// echo displayFinalInformation($tableTitle, $compsysScoreInfo);
// print_r(finalInformatoinToArray($tableTitle, $compsysScoreInfo));

// TODO：	改造 displayFinalInformation($title, $score) 方法，使其通过矩阵组读取数据
//			函数原型改为 displayFinalInformation($title, $information).

// TODO：	改造 getSerializedScore($compsName, $DBC) 方法，使其合并所有相同组相同参赛队（或个人）的分数（求平均数）
//			函数原型不变。

// TODO：	增加导出Excel表的功能时，可以考虑重写 getSerializedScore() 和 getScoresByNamAndGroup()



function displayFinalInformation($title, $score) {
	// 竞赛成绩信息统一建表输出子函数

	// TODO: 改造函数改成从矩阵中读数据

	$finalInformation = "";

	// 判断是否存在“总分”分组
	$isTotal = count($title[2]) != count($title[3]);	// 不相等则说明存在“总分”组
	$groupNumber = $isTotal ? count($title[0]) - 1 : count($title[0]);	// 计算组的数量（不包括“总分”组）

	for ($groupIndex = 0; $groupIndex < $groupNumber; $groupIndex++) {	// 分别写入各组的表格
		$finalInformation .= "<table border = \"1px\"><tr>";	// 解析表格标题栏
		for ($col = 0; $col < count($title[1][$groupIndex]); $col++) {
			$finalInformation .= "<th>".$title[1][$groupIndex][$col]."</th>";
		}
		$finalInformation .= "</tr>";
		for ($row = 0; $row < count($title[2][$groupIndex]); $row++) {	// 向表格中按行写入参赛者信息
			$finalInformation .= "<tr><td>".$row."</td><td>".$title[2][$groupIndex][$row]."</td><td>".$title[3][$groupIndex][$row]."</td>";	// 编号、参赛队（或个人）名称和作品名称
			// 查询当前正在写入的参赛队（或个人）的详细得分和总分
			$detailedScore = getScoresByNamAndGroup($score, $title[2][$groupIndex][$row], $title[0][$groupIndex]);
			if (count($detailedScore) > 0) {	// 判断该选手是否已被评分
				// 填充选手得分
				for ($col = 0; $col < count($detailedScore[1]); $col++) {
					$finalInformation .= "<td>".$detailedScore[1][$col]."</td>";
				}
				$finalInformation .= "<td>".$detailedScore[2]."</td>";
			}
			else {
				// 填充0
				for ($col = 0; $col < count($title[1][$groupIndex]) - 3; $col++) {
					$finalInformation .= "<td>0</td>";
				}
			}
			$finalInformation .= "</tr>";
		}
		$finalInformation .= "</table>";
	}

	if ($isTotal) {
		// 在此处显示“总分”组的表格
		$summaryTitle = end($title[1]);	// 获取标题

		$finalInformation .= "<table border = \"1px\"><tr>";	// 解析表格标题栏
		for ($col = 0; $col < count($summaryTitle); $col++) {
			$finalInformation .= "<th>".$summaryTitle[$col]."</th>";
		}
		$finalInformation .= "</tr>";
		$finalInformation .= "</table>";
	}

	return $finalInformation;
}

mysqli_close($dbc);













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

	/// 嵌套函数：
	if (!function_exists('EVEN')) { function EVEN($var) { return !($var & 1); }}	// 取数组中第0,2,4……号元素
	if (!function_exists('ODD')) { function ODD($var) { return ($var & 1); }}		// 取数组中第1,3,5……号元素

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

function getCompsysScoreInfo($compsName, $DBC) {
	// 竞赛信息提取子函数

	/// 函数功能：
	// 通过封装函数在指定的数据库中查询指定名称的比赛中用于最终分数表格显示的数据，返回值共有四列：
	// 第0列：分组名称，用于选项卡的显示。若各组参赛人员相同，则添加“总分”组。
	// 第1列：各组的评分细则，用于表头的显示。
	// 第2列：各组的参赛人员，用于表格参赛队名称的显示。
	// 第3列：各组（不包含“总分”组）各参赛人员的作品名称。

	// 注：
	// 只需判断参赛人员的组数和作品名称的组数是否相同即可判断是否有“总分”组。

	/// 函数实现：
	$query = "SELECT competitor_info, score_rubric FROM comps_info WHERE comps_name = '$compsName'";
	$result = mysqli_query($DBC, $query);

	checkIsLegal(mysqli_num_rows($result) > 0, $DBC, "getCompsysScoreInfo()");

	mysqli_data_seek($result, 0);
	$row = mysqli_fetch_array($result);
	$rawTitleInformation = getCompsysScoreInfoByCompsysInfo($row[0], $row[1]);
	return $rawTitleInformation;
}

function getSerializedScore($compsName, $DBC) {
	// 选手成绩查询子函数

	/// 函数功能：
	// 在指定的数据库中查询指定名称的比赛中所有参赛人员的成绩信息
	// 返回的数组的每一行代表一个参赛人员（或队伍），共有5列，具体结构如下：
	// 第0列：参赛人姓名或参赛队名称；
	// 第1列：参赛人或参赛队所在组的名称；
	// 第2列：参赛队或参赛人员的作品名称；
	// 第3列：数组，共两行，分别为该评分的评分细则和对应的详细得分；
	// 第4列：总分。

	// 注：
	// 本函数一定有返回值，若查询结果为空则返回一个空数组。

	/// 嵌套函数：
	if (!function_exists('EVEN')) { function EVEN($var) { return !($var & 1); }}	// 取数组中第0,2,4……号元素
	if (!function_exists('ODD')) { function ODD($var) { return ($var & 1); }}		// 取数组中第1,3,5……号元素

	/// 函数实现：
	$result = mysqli_query($DBC, "SELECT player_name, player_group, work_name, score_detailed, score_sum FROM competitor_score WHERE comps_name = '$compsName'");	// 查询数据
	$rawScoreInfomation = Array();
	while($row = mysqli_fetch_assoc($result)) { array_push($rawScoreInfomation, array_values($row)); }	// 将数据转换成数组

	foreach ($rawScoreInfomation as &$rawScoreInfomation_item) {
		// 解析位于数组每行第三列的详细得分字段，生成数组
		$score_detailed = preg_split("/(@c@)|@r@/", $rawScoreInfomation_item[3]);
		$rawScoreInfomation_item[3] = Array(array_values(array_filter($score_detailed, "EVEN", ARRAY_FILTER_USE_KEY)),
			array_values(array_filter($score_detailed, "ODD", ARRAY_FILTER_USE_KEY)));

	}
	unset($rawScoreInfomation_item);

	return $rawScoreInfomation;
}

function getScoresByNamAndGroup($rawScoreInfomation, $name, $group) {
	// 分数查询子函数

	/// 函数功能：
	// 在所有参赛队（或个人）信息集合中查询制定分组的指定参赛队（或个人）并返回
	// 返回值为全部信息的一个元素，共有3列，具体结构如下：
	// 第0列：该评分的评分细则；
	// 第1列：对应的详细得分；
	// 第2列：总分。

	// 注：
	// 本函数一定有返回值，若查询结果为空则返回一个空数组。

	/// 函数实现：
	$scoreInformation = Array();
	foreach ($rawScoreInfomation as $rawScoreInfomation_item) {
		if ($rawScoreInfomation_item[0] == $name && $rawScoreInfomation_item[1] == $group) {
			array_push($scoreInformation, $rawScoreInfomation_item[3][0],
				$rawScoreInfomation_item[3][1], $rawScoreInfomation_item[4]);
			break;
		}
	}
	return $scoreInformation;
}

function finalInformatoinToArray($title, $score) {
	// 竞赛成绩信息统一处理子函数

	/// 函数功能：
	// 将表格信息和选手成绩信息转换成矩阵形式，方便取数据和计算总分
	// 返回值为一个数组，其中：
	// 数组的每个元素都是一个矩阵；
	// 每个矩阵为一个组（不包含“总分”组）的信息；
	// 每个矩阵的每行为一个选手的信息；

	/// 函数实现：
	$finalArray = Array();

	// 判断是否存在“总分”分组
	$isTotal = count($title[2]) != count($title[3]);	// 不相等则说明存在“总分”组
	$groupNumber = $isTotal ? count($title[0]) - 1 : count($title[0]);	// 计算组的数量（不包括“总分”组）

	for ($groupIndex = 0; $groupIndex < $groupNumber; $groupIndex++) {	// 分别写入各组的表格
		$groupCache = Array();	// 即将写入一行的信息
		for ($row = 0; $row < count($title[2][$groupIndex]); $row++) {	// 向表格中按行写入参赛者信息
			$rowCache = Array();
			array_push($rowCache, $title[2][$groupIndex][$row], $title[3][$groupIndex][$row]);	// 编号、参赛队（或个人）名称和作品名称
			// 查询当前正在写入的参赛队（或个人）的详细得分和总分
			$detailedScore = getScoresByNamAndGroup($score, $title[2][$groupIndex][$row], $title[0][$groupIndex]);
			if (count($detailedScore) > 0) {	// 判断该选手是否已被评分
				$rowCache = array_merge($rowCache, $detailedScore[1], Array($detailedScore[2]));	// 填充选手得分
			}
			else {
				for ($col = 0; $col < count($title[1][$groupIndex]) - 3; $col++) { array_push($rowCache, "0"); }	// 填充0
			}
			array_push($groupCache, $rowCache);	// 向矩阵中添加一整行
		}
		array_push($finalArray, $groupCache);	// 向数组中写入一个矩阵
	}

	return $finalArray;
}

?>