<?php

include("../PHP/common/common-define.php");
include("../PHP/common/common-functions.php");
include("../PHP/common/data-export-functions.php");

session_start();
checkIsLegal(isset($_POST["compsname"]), null, 1);
$compsname = $_POST["compsname"];

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$inputValue = getIsStart($compsname, $dbc);
$judgeNumber = count(getJudges($compsname, $dbc));
$tableTitle = getCompsysScoreInfo($compsname, $dbc);
$compsysScoreInfo = getSerializedScore($compsname, $dbc);

mysqli_close($dbc);

$finalInfoArray = finalInformatoinToArray($tableTitle, $compsysScoreInfo, $judgeNumber);

?>

<div id="compsName" style="display: none"><?php echo $compsname; ?></div>
<!-- <div class="ordinary-header">
	<div class="compsname" id="compsname"></div>
	<div class="header-line-continer"><div class="header-line"></div></div>
</div> -->
<div class="ordinary-body" id="ordinary-body">
	<div id="message" class="message-box">
		<div class="fun-title">竞赛详细信息</div>
		<div class="information">
			<p><b>竞赛名称：</b> <?php echo $compsname; ?></p>
			<p><b>评委数量：</b> <?php echo $judgeNumber; ?>个</p>
			<p><b>注：</b></p>
			<p>1. 当且仅当提交评分的评委人数不低于<span style="color: #f00">5人</span>时，系统在进行最终得分的结算时将会自动<span style="color: #f00">去掉一个最高分和一个最低分</span>再求平均值。</p>
			<p>2. 当提交评分的评委人数少于5人时，即使比赛的评委总数不少于5人，系统在进行最终得分的结算时也不会自动去掉一个最高分和一个最低分，而是直接求平均值。</p>
		</div>
	</div>
	<div id="time" class="message-box">
		<div class="fun-title">竞赛倒计时</div>
		<input class="button" type="button" style="right: 20px" id="stratOrEnd" value="<?php echo $inputValue; ?>" onclick="makeCompStartOrEnd()" />
		<div class="ordinary-body-timer">
			<!-- <p>现在时间：<span id="timeNow"></span></p> -->
			<p><span id="timeLast"></span></p>
		</div>
	</div>
	<div id="score" class="message-box">
		<input class="button" type="button" style="right: 162px" value="刷&emsp;新" onclick="flushTable()" />
		<input class="button" type="button" style="right: 20px" value="导出当前表" onclick="explorCurrentTable()" />
		<div class="fun-title">实时详细得分信息</div>
		<div id="score-container" style="width: 1018px; border: 1px solid #E3E3E3; z-index: 0">
			<div class="score-buttons">
				<ul id="groups"><?php displayGroupName($tableTitle); ?></ul>
			</div>
			<?php displayFinalInformation($tableTitle, $finalInfoArray, $judgeNumber); ?>
		</div>
	</div>
	<div id="info" class="message-box">
		<div class="fun-title">站内消息</div>
		<div class="information">
<?php

echo getFileContent("Messages/ordinary-administrator-station-message.html", "r");	// 读取二进制文件时，需要将第二个参数设置成'rb'

?>
		</div>
	</div>
</div>
<div class="ordinary-footer">
	<div class="footer-copyright">
		版权所有&copy;&nbsp;Copyright&nbsp;2018&emsp;内容维护：彭剑桥&emsp;&emsp;
		<a class="footer-copyright" href="http://www.miitbeian.gov.cn" target='_blank'>冀ICP备18027949号</a>
		<span>&nbsp;|&nbsp;</span>
		<a class="footer-copyright" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=13092802000135" target='_blank'>冀公网安备13092802000135号</a>
	</div>
</div>

<?php

function displayGroupName($title) {
	// 选手分组输出子函数

	/// 函数实现：
	foreach ($title[0] as $groupName) { echo "<li class=\"groupItem\" onclick=\"clickGroups(this.id)\">".$groupName."</li>"; }
}

function displayFinalInformation($title, $information, $number) {
	// 竞赛成绩信息统一建表输出子函数

	/// 函数实现：
	// 分别输出各组的表格
	for ($groupIndex = 0; $groupIndex < count($information); $groupIndex++) {
		$detailNumber = count($title[1][$groupIndex]);	// 标题栏项目的数目

		echo "<table id=\"table-".$groupIndex."\"><thead><tr>";	// 解析表格标题栏
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
		echo "<table id=\"table-".(count($title[0]) - 1)."\"><thead><tr>";	// 解析表格标题栏
		foreach (end($title[1]) as $title_item) { echo "<th>".$title_item."</th>"; }
		echo "</tr></thead>";

		// 将信息矩阵解析成总分分组形式
		$totalInformation = totalInformationToArray($title, $information);

		// 名次排序
		$sortRule = array_column($totalInformation, count($totalInformation[0]) - 1);
		array_multisort($sortRule, SORT_DESC, $totalInformation);

		echo "<tbody>";
		for ($playerRow = 0; $playerRow < count($totalInformation); $playerRow++) {
			echo "<tr>"."<td width=\"40px\">".($playerRow + 1)."</td>";
			for ($col = 0; $col < count($totalInformation[$playerRow]); $col++) {
				echo ($col == 0 ? "<td width=\"160px\">" : "<td width=\"100px\">").($col == 0 ? $totalInformation[$playerRow][$col] : number_format(round($totalInformation[$playerRow][$col], 2), 2, ".", ""))."</td>";
			}
			echo "</tr>";
		}

		echo "</tbody></table>";
	}
}

?>