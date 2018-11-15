<?php

include("../PHP/common/common-define.php");
include("../PHP/common/common-functions.php");

session_start();
//checkIsLegal(isset($_POST["compsname"]), null, 1);
$compsname = $_POST["compsname"];

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$query = "SELECT  FROM competitor_score WHERE  comps_name = '$compsname'";
$result = mysqli_query($dbc, $query);
//checkIsLegal(mysqli_num_rows($result) > 0, $dbc, 2);

$query = "SELECT competitor_info, score_rubric FROM comps_info WHERE comps_name = '$compsname'";
$result = mysqli_query($dbc, $query);
checkIsLegal(mysqli_num_rows($result) > 0, $dbc, 3);

mysqli_data_seek($result, 0);
$row = mysqli_fetch_array($result);
$groupName = competitorInfoToArray($row[0]);//getGroupNameFromeCompetitorInfo($row[0]);
$score_rubric = $row[1];



//array_push($competitor_info, "总计");

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
					<?php

					/*foreach($competitor_info as $score_buttons) {
						echo "<li onclick=\"clickGroups(this.id)\">$score_buttons</li>";
					}*/

					?>
				</ul>
			</div>
			<!-- <div class="value">
				<table class="dynamic-page-score-value">
					<th>
						<td style="width: 40px;">编号1</td>
						<td style="width: 200px;">选手姓名</td>
						<td style="width: 250px;">作品名称</td>
						<td style="width: 200px;">总分</td>
					</th>
				</table>
			</div>
			<div class="value">
				<table class="dynamic-page-score-value">
					<th>
						<td style="width: 40px;">编号2</td>
						<td style="width: 200px;">选手姓名</td>
						<td style="width: 250px;">作品名称</td>
						<td style="width: 250px;">作品名称</td>
						<td style="width: 200px;">总分</td>
					</th>
				</table>
			</div>
			<div class="value">
				<table class="dynamic-page-score-value">
					<th>
						<td style="width: 40px;">编号3</td>
						<td style="width: 200px;">选手姓名</td>
						<td style="width: 200px;">选手姓名</td>
						<td style="width: 250px;">作品名称</td>
						<td style="width: 200px;">总分</td>
					</th>
				</table>
			</div> -->
<?php

print_r($groupName);

?>
		</div>
	</div>
</div>
<div class="ordinary-footer"></div>

<?php



?>