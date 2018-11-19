<?php

include("../PHP/common/common-define.php");
include("../PHP/common/common-functions.php");

session_start();
checkIsLegal(isset($_POST["compsname"]), null, 1);
$compsname = $_POST["compsname"];

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$judgeNumber = count(getJudges($compsname, $dbc));
$tableTitle = getCompsysScoreInfo($compsname, $dbc);
$compsysScoreInfo = getSerializedScore($compsname, $dbc);

mysqli_close($dbc);

$finalInfoArray = finalInformatoinToArray($tableTitle, $compsysScoreInfo, $judgeNumber);

?>

<div class="ordinary-header">
	<div class="compsname" id="compsname"><?php echo $compsname; ?></div>
	<div class="header-line-continer"><div class="header-line"></div></div>
</div>
<div class="ordinary-body">
	<div id="time" class="time">
		<div class="ordinary-body-timer">
			比赛倒计时
			时分秒
		</div>
		<div style="margin: 0 auto; width: 300px; height: 2pc">
			<input style="float: left" class="button" type="button" name="stratOrEnd" value="立即开始" />
			<input style="float: right" class="button" type="button" name="stratOrEnd" value="立即结束" />
		</div>
	</div>
	<div id="score" class="score">
		<input class="flush" type="button" name="itemFlush" value="刷&emsp;新" />
		<div class="no-data" id="no-data">暂无数据</div>
		<div id="score-container" style="width: 100%; border: 2px solid #BACDEE; z-index: 0; margin-top: 20px">
			<div class="score-buttons" style="background: #BACDEE">
				<ul id="groups">
					<?php displayGroupName($tableTitle); ?>
				</ul>
			</div>
				<?php displayFinalInformation($tableTitle, $finalInfoArray, $judgeNumber); ?>
			</div>
<?php

//print_r($groupName);

?>
		</div>
	</div>
</div>
<div class="ordinary-footer"></div>

<?php

function getJudges($compsName, $DBC) {
	// 评委姓名查询子函数

	/// 函数实现：
	$result = mysqli_query($DBC, "SELECT users_nickname FROM users_info WHERE comps_name = '$compsName' AND users_authority = 4");

	$judges = Array();
	while($row = mysqli_fetch_assoc($result)) { array_push($judges, array_values($row)[0]); }	// 将数据转换成数组
	return $judges;
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
		array_push($info_item, "总分", "最终得分");
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
		// 若剩下的行数为1（所有项都相同）且总组数大于1，则添加“总分”组
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

function displayGroupName($title) {
	// 选手分组输出子函数

	/// 函数实现：
	foreach ($title[0] as $groupName) { echo "<li onclick=\"clickGroups(this.id)\">".$groupName."</li>"; }
}

function getScoresByNamAndGroup($rawScoreInfomation, $name, $group) {
	// 分数查询子函数

	/// 函数功能：
	// 在所有参赛队（或个人）信息集合中查询制定分组的指定参赛队（或个人）并返回符合条件的全部记录
	// 返回值为全部信息的元素的集合，每个元素共有3列，具体结构如下：
	// 第0列：该评分的评分细则；
	// 第1列：对应的详细得分；
	// 第2列：总分。

	// 注：
	// 本函数一定有返回值，若查询结果为空则返回一个空数组。

	/// 函数实现：
	$scoreInformation = Array();
	foreach ($rawScoreInfomation as $rawScoreInfomation_item) {
		if ($rawScoreInfomation_item[0] == $name && $rawScoreInfomation_item[1] == $group) {
			array_push($scoreInformation, Array($rawScoreInfomation_item[3][0],
				$rawScoreInfomation_item[3][1], $rawScoreInfomation_item[4]));
		}
	}
	return $scoreInformation;
}

function getSingleFirstScore($rawScoreInfomation, $name, $group) {
	// 分数查询子函数

	/// 函数功能：
	// 在所有参赛队（或个人）信息集合中查询制定分组的指定参赛队（或个人）并返回查询到的所有成绩的第一条记录
	// 返回值共有3列，具体结构如下：
	// 第0列：该评分的评分细则；
	// 第1列：对应的详细得分；
	// 第2列：总分。

	// 注：
	// 本函数一定有返回值，若查询结果为空则返回一个空数组。
	// 本函数用于兼容测试

	/// 函数实现：
	$scoreInformation = getScoresByNamAndGroup($rawScoreInfomation, $name, $group);
	return count($scoreInformation) > 0 ? $scoreInformation[0] : Array();
}

function getSingleAverageScore($rawScoreInfomation, $name, $group) {
	// 分数查询子函数

	/// 函数功能：
	// 在所有参赛队（或个人）信息集合中查询制定分组的指定参赛队（或个人）并对全部符合条件的记录进行求平均值合并
	// 返回值为全部信息的元素的集合，每个元素共有3列，具体结构如下：
	// 第0列：该评分的评分细则；
	// 第1列：数组，每一个元素均为一条记录中的详细得分及总分；
	// 第2列：最终得分，若评委人数大于等于5人则采用去掉最高分和最低分的方法，否则直接求平均数。

	// 注：
	// 本函数一定有返回值，若查询结果为空则返回一个空记录。

	/// 函数实现：

	$rawInfo = getScoresByNamAndGroup($rawScoreInfomation, $name, $group);
	$itemNumber = count($rawInfo);

	if ($itemNumber > 0) {
		$scoreInformation = Array();
		array_push($scoreInformation, $rawInfo[0][0]);
		$rubricScore = Array();
		$scoreSums = Array();
		foreach ($rawInfo as $rawInfo_item) {
			array_push($rawInfo_item[1], $rawInfo_item[2]);
			array_push($rubricScore, $rawInfo_item[1]);
			array_push($scoreSums, $rawInfo_item[2]);
		}
		$sum = array_sum($scoreSums);
		if ($itemNumber >= 5) {
			// 若评委人数大于等于5人则采用去掉最高分和最低分的方法，否则直接求平均数
			$sum -= max($scoreSums) + min($scoreSums);
			$itemNumber -= 2;
		}
		array_push($scoreInformation, $rubricScore, $sum / $itemNumber);
	}
	else {
		$scoreInformation = Array("", Array(), 0);
	}

	return $scoreInformation;
}

function finalInformatoinToArray($title, $score, $number) {
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
			$detailedScore = getSingleAverageScore($score, $title[2][$groupIndex][$row], $title[0][$groupIndex]);
			for ($recordNumber = count($detailedScore[1]); $recordNumber < $number; $recordNumber++) {
				// 将得分记录数量扩充至评委数量，不足的用0补齐
				$scoreCache = Array();
				for ($col = 0; $col < count($title[1][$groupIndex]) - 3; $col++) { array_push($scoreCache, "0"); }	// 填充0
				array_push($detailedScore[1], $scoreCache);
			}
			array_push($rowCache, $detailedScore[1], $detailedScore[2]);	// 将得分记录和总平均分写入行中
			array_push($groupCache, $rowCache);	// 向矩阵中添加一整行
		}
		array_push($finalArray, $groupCache);	// 向数组中写入一个矩阵
	}

	return $finalArray;
}

function findScoreFromFinalInformation($information, $groupIndex, $name) {
	// 最终得分查询子函数

	/// 函数功能：
	// 在竞赛成绩的矩阵信息中的指定分组（序号）中查询指定参赛队（或个人）的最终得分
	// 返回值为一个浮点型变量

	// 函数实现：
	foreach ($information[$groupIndex] as $groupInfo_item) { if ($groupInfo_item[0] == $name) { return $groupInfo_item[3]; } }
	return 0;	// 未找到记录时的默认值
}

function totalInformationToArray($title, $information) {
	// 竞赛“总分”组信息重解析子函数

	/// 函数功能：
	// 将信息矩阵解析成总分分组形式，返回值为一个矩阵，其内容为总分表格中除去“编号”列的所有数据

	// 函数实现：
	$totalInformation = Array();

	foreach (end($title[2]) as $playerList_item) {
		$totalItem = Array();
		for ($groupIndex = 0; $groupIndex < count($title[0]) - 1; $groupIndex++) {
			array_push($totalItem, findScoreFromFinalInformation($information, $groupIndex, $playerList_item));
		}
		array_push($totalItem, array_sum($totalItem));
		array_unshift($totalItem, $playerList_item);
		array_push($totalInformation, $totalItem);
	}

	return $totalInformation;
}

function displayFinalInformation($title, $information, $number) {
	// 竞赛成绩信息统一建表输出子函数

	/// 函数实现：
	// 分别输出各组的表格
	for ($groupIndex = 0; $groupIndex < count($information); $groupIndex++) {
		$detailNumber = count($title[1][$groupIndex]);	// 标题栏项目的数目

		echo "<table class=\"dynamic-page-score-value\"><thead><tr>";	// 解析表格标题栏
		for ($col = 0; $col < $detailNumber; $col++) { echo "<th>".$title[1][$groupIndex][$col]."</th>"; }
		echo "</tr></thead>";

		// 名次排序
		$sortRule = array_column($information[$groupIndex], '3');
		array_multisort($sortRule, SORT_DESC, $information[$groupIndex]);

		echo "<tbody>";
		for ($playerRow = 0; $playerRow < count($title[2][$groupIndex]); $playerRow++) {	// 向表格中按行写入参赛者信息
			for ($judgeRow = 0; $judgeRow < $number; $judgeRow++) {	// 写入该参赛队（或个人）的每一条得分记录
				echo "<tr>";

				// 输出编号、参赛队（或个人）名称和作品名称
				if ($judgeRow == 0) { echo "<td rowspan=\"".$number."\" width=\"40px\">".($playerRow + 1)."</td><td rowspan=\"".$number."\" width=\"160px\">".$information[$groupIndex][$playerRow][0]."</td><td rowspan=\"".$number."\" width=\"160px\">".$information[$groupIndex][$playerRow][1]."</td>"; }

				// 输出当前正在写入的参赛队（或个人）的详细得分和总分
				for ($col = 0; $col < $detailNumber - 4; $col++) { echo "<td width=\"100px\">".$information[$groupIndex][$playerRow][2][$judgeRow][$col]."</td>"; }

				// 输出最终得分
				if ($judgeRow == 0) { echo "<td rowspan=\"".$number."\" width=\"100px\">".number_format(round($information[$groupIndex][$playerRow][3], 2), 2, ".", "")."</td>"; }

				echo "</tr>";
			}
		}
		echo "</tbody></table>";
	}

	// 输出“总分”组.首先判断是否存在“总分”分组，不相等则说明存在“总分”组
	if (count($title[2]) != count($title[3])) {
		echo "<table class=\"dynamic-page-score-value\"><tr>";	// 解析表格标题栏
		foreach (end($title[1]) as $title_item) { echo "<th>".$title_item."</th>"; }
		echo "</tr>";

		// 将信息矩阵解析成总分分组形式
		$totalInformation = totalInformationToArray($title, $information);

		// 名次排序
		$sortRule = array_column($totalInformation, count($totalInformation[0]) - 1);
		array_multisort($sortRule, SORT_DESC, $totalInformation);

		for ($playerRow = 0; $playerRow < count($totalInformation); $playerRow++) {
			echo "<tr>"."<td width=\"40px\">".($playerRow + 1)."</td>";
			for ($col = 0; $col < count($totalInformation[$playerRow]); $col++) {
				echo ($col == 0 ? "<td width=\"160px\">" : "<td width=\"100px\">").($col == 0 ? $totalInformation[$playerRow][$col] : number_format(round($totalInformation[$playerRow][$col], 2), 2, ".", ""))."</td>";
			}
			echo "</tr>";
		}

		echo "</table>";
	}
}

?>