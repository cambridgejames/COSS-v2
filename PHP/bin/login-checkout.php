<?php

include("../common/common-define.php");

$username = (isset($_GET['username'])) ? $_GET['username'] : "";
$password = (isset($_GET['password'])) ? $_GET['password'] : "";

if (empty($username) || empty($password)) {
	echo "用户名或密码不能为空";
}
else {
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	mysqli_set_charset($dbc, "utf8");

	// $info = mysqli_real_escape_string($dbc, trim($username));
	// $pwd = mysqli_real_escape_string($dbc, trim($password));
	//$query = "SELECT users_nickname, users_pwdhash, users_authority, comps_name FROM users_info WHERE users_nickname = '$info' OR users_phone = '$info' OR users_mailbox = '$info'";

	$info = $username;
	$pwd = trim($password);

	$query = "SELECT users_nickname, users_pwdhash, users_authority, comps_name FROM users_info WHERE (users_nickname = '$info' OR users_phone = '$info' OR users_mailbox = '$info') AND users_pwdhash = '$pwd'";

	$result = mysqli_query($dbc, $query);

	mysqli_data_seek($result, 0);
	$row = mysqli_fetch_array($result);
	$nickname = $row[0];
	$hash = $row[1];
	$authority = $row[2];
	$compsname = $row[3];

	//if (password_verify($pwd, $hash)) {
		// Check whether the account has expired
		if ($authority == 4) {
			$query = "SELECT time_start, time_duration FROM comps_info WHERE comps_name = '$compsname'";
			$result = mysqli_query($dbc, $query);

			mysqli_data_seek($result, 0);
			$row = mysqli_fetch_array($result);
			$timestart = intval(strtotime($row[0]));
			$timeduration = intval($row[1]);

			$timenow = intval(time());
			if ($timenow < $timestart - 604800) { echo "尚未到达启用时间"; }
			else if ($timenow > $timestart + $timeduration + 3600) { echo "用户已过期"; }
			else { LoginSucceed($nickname, $authority); }
		}
		else {
			LoginSucceed($nickname, $authority);
		}
	//}
	//else {
		//echo "用户名或密码错误";
	//}

	mysqli_close($dbc);
}

function LoginSucceed($nickname, $authority) {
	session_start();
	$_SESSION['username'] = $nickname;
	$_SESSION['authority'] = $authority;
	switch($authority) {
		case 1:
		case 2:
		case 3:
			echo "administrator";
			break;
		case 4:
		case 5:
			echo "succeed";
			break;
		default:
			echo "用户名或密码错误";
	}
}

?>
