<?php

include("../common/common-define.php");
include("../common/common-functions.php");
include("../common/data-export-functions.php");

checkIsLegal(isset($_POST['compsname']), null, 1);
$compsname = $_POST['compsname'];

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_set_charset($dbc, "utf8");

$judgeNumber = count(getJudges($compsname, $dbc));
$tableTitle = getCompsysScoreInfo($compsname, $dbc);
$compsysScoreInfo = getSerializedScore($compsname, $dbc);

mysqli_close($dbc);

$finalInfoArray = finalInformatoinToArray($tableTitle, $compsysScoreInfo, $judgeNumber);
$groupName = displayGroupName($tableTitle);
$finalInformation = displayFinalInformation($tableTitle, $finalInfoArray, $judgeNumber);

echo $judgeNumber."@c@".$groupName."@c@".$finalInformation;

function displayGroupName($title) {
	// 选手分组输出子函数

	/// 函数实现：
	$returnedInfo = "";
	foreach ($title[0] as $groupName) { $returnedInfo .= "<li class=\"groupItem\" onclick=\"clickGroups(this.id)\">".$groupName."</li>"; }
	return $returnedInfo;
}

function displayFinalInformation($title, $information, $number) {
	// 竞赛成绩信息统一建表输出子函数

	/// 函数实现：
	// 分别返回各组的表格
	$returnedInfo = "";

	for ($groupIndex = 0; $groupIndex < count($information); $groupIndex++) {
		$detailNumber = count($title[1][$groupIndex]);	// 标题栏项目的数目

		$returnedInfo .= "<table id=\"table-".$groupIndex."\"><thead><tr>";	// 解析表格标题栏
		for ($col = 0; $col < $detailNumber; $col++) { $returnedInfo .= "<th>".$title[1][$groupIndex][$col]."</th>"; }
		$returnedInfo .= "</tr></thead>";

		// 名次排序
		$sortRule = array_column($information[$groupIndex], '3');
		array_multisort($sortRule, SORT_DESC, $information[$groupIndex]);

		$returnedInfo .= "<tbody>";
		for ($playerRow = 0; $playerRow < count($title[2][$groupIndex]); $playerRow++) {	// 向表格中按行写入参赛者信息
			for ($judgeRow = 0; $judgeRow < $number; $judgeRow++) {	// 写入该参赛队（或个人）的每一条得分记录
				$returnedInfo .= "<tr>";

				// 输出编号、参赛队（或个人）名称和作品名称
				if ($judgeRow == 0) { $returnedInfo .= "<td rowspan=\"".$number."\" width=\"40px\">".($playerRow + 1)."</td><td rowspan=\"".$number."\" width=\"160px\">".$information[$groupIndex][$playerRow][0]."</td><td rowspan=\"".$number."\" width=\"160px\">".$information[$groupIndex][$playerRow][1]."</td>"; }

				// 输出当前正在写入的参赛队（或个人）的详细得分和总分
				for ($col = 0; $col < $detailNumber - 4; $col++) { $returnedInfo .= "<td width=\"100px\">".$information[$groupIndex][$playerRow][2][$judgeRow][$col]."</td>"; }

				// 输出最终得分
				if ($judgeRow == 0) { $returnedInfo .= "<td rowspan=\"".$number."\" width=\"100px\">".number_format(round($information[$groupIndex][$playerRow][3], 2), 2, ".", "")."</td>"; }

				$returnedInfo .= "</tr>";
			}
		}
		$returnedInfo .= "</tbody></table>";
	}

	// 输出“总分”组.首先判断是否存在“总分”分组，不相等则说明存在“总分”组
	if (count($title[2]) != count($title[3])) {
		$returnedInfo .= "<table id=\"table-".(count($title[0]) - 1)."\"><thead><tr>";	// 解析表格标题栏
		foreach (end($title[1]) as $title_item) { $returnedInfo .= "<th>".$title_item."</th>"; }
		$returnedInfo .= "</tr></thead>";

		// 将信息矩阵解析成总分分组形式
		$totalInformation = totalInformationToArray($title, $information);

		// 名次排序
		$sortRule = array_column($totalInformation, count($totalInformation[0]) - 1);
		array_multisort($sortRule, SORT_DESC, $totalInformation);

		$returnedInfo .= "<tbody>";
		for ($playerRow = 0; $playerRow < count($totalInformation); $playerRow++) {
			$returnedInfo .= "<tr>"."<td width=\"40px\">".($playerRow + 1)."</td>";
			for ($col = 0; $col < count($totalInformation[$playerRow]); $col++) {
				$returnedInfo .= ($col == 0 ? "<td width=\"160px\">" : "<td width=\"100px\">").($col == 0 ? $totalInformation[$playerRow][$col] : number_format(round($totalInformation[$playerRow][$col], 2), 2, ".", ""))."</td>";
			}
			$returnedInfo .= "</tr>";
		}

		$returnedInfo .= "</tbody></table>";
	}

	return $returnedInfo;
}


?>