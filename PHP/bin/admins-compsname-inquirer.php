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
		default:
			echo "failed";
			exit();
	}

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	$result = mysqli_query($dbc, $query);
	mysqli_close($dbc);

	if (mysqli_num_rows($result) > 0) {
		echo $username."@c@";
		while($row = mysqli_fetch_assoc($result)) {
			echo "<div onclick=\"getPageByCompsname(this.innerHTML)\">".$row['comps_name']."</div>";
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