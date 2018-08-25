<?php

include("../common/common-define.php");

session_start();
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "";
$compsname = isset($_POST["compsname"]) ? $_POST["compsname"] : "";

if ($username && $compsname) {
	$query1 = "SELECT works_number, competitor_info, score_rubric FROM comps_info WHERE comps_name = '$compsname'";
	$query2 = "SELECT reviewed_number FROM users_info WHERE users_nickname = '$username'";

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$result1 = mysqli_query($dbc, $query1);
	$result2 = mysqli_query($dbc, $query2);
	mysqli_close($dbc);

	if (mysqli_num_rows($result1) > 0 && mysqli_num_rows($result2) > 0) {
		mysqli_data_seek($result1, 0);
		$row1 = mysqli_fetch_array($result1);
		mysqli_data_seek($result2, 0);
		$row2 = mysqli_fetch_array($result2);
		
		$_SESSION["compsname"] = $compsname;
		
		echo $row1[0]."@c@".$row1[1]."@c@".$row1[2]."@c@".$row2[0];
	}
	else {
		echo "failed";
	}
}
else {
	echo "failed";
}

?>