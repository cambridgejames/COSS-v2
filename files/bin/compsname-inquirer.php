<?php

include("../common/common-define.php");

session_start();
$username = (isset($_SESSION['username'])) ? $_SESSION['username'] : "";
$authority = (isset($_SESSION['authority'])) ? $_SESSION['authority'] : "";

if ($username && $authority) {
	switch ($authority) {
		case 1:
		case 2:
			$query = "SELECT DISTINCT comps_name FROM comps_info";
			break;
		case 3:
			$query = "SELECT DISTINCT comps_name FROM users_info WHERE users_nickname = '$username'";
			break;
		case 4:
			$query = "SELECT DISTINCT comps_name FROM users_info WHERE users_nickname = '$username'";
			break;
		case 5:
			$query = "SELECT DISTINCT comps_name FROM comps_info WHERE UNIX_TIMESTAMP(time_start) - 3600 < UNIX_TIMESTAMP() AND UNIX_TIMESTAMP(time_start) + time_duration + 3600 > UNIX_TIMESTAMP()";
			break;
		default:
			echo "failed";
	}

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$result = mysqli_query($dbc, $query);
	mysqli_close($dbc);

	if (mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)) {
			echo "<input class=\"panner-compsname-choose button-pointer\" type=\"button\" name=\"compsname\" value=".$row['comps_name']." onclick=\"compslogin(this.value)\">";
		}
	}
	else {
		echo "failed";
	}
}
else {
	echo "failed";
}

?>