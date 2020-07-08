<?php

include("../common/common-define.php");

$username = (isset($_GET['username'])) ? $_GET['username'] : "";
$password = (isset($_GET['password'])) ? $_GET['password'] : "";

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_set_charset($dbc, "utf8");

$query = "";
$access_to = "";
$query_to_save = "";

if (empty($username) || empty($password)) {
	echo "用户名或密码不能为空";
}
else {
	// $info = mysqli_real_escape_string($dbc, trim($username));
	// $pwd = mysqli_real_escape_string($dbc, trim($password));
	//$query = "SELECT users_nickname, users_pwdhash, users_authority, comps_name FROM users_info WHERE users_nickname = '$info' OR users_phone = '$info' OR users_mailbox = '$info'";

	$info = $username;
	$pwd = mysqli_real_escape_string($dbc, trim($password));

	$query = "SELECT users_nickname, users_pwdhash, users_authority, comps_name FROM users_info WHERE (users_nickname = '$info' OR users_phone = '$info' OR users_mailbox = '$info') AND users_pwdhash = '$pwd'";
	$query_to_save = mysqli_real_escape_string($dbc, trim($query));

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
			if ($timenow < $timestart - 604800) { $access_to = "尚未到达启用时间"; }
			else if ($timenow > $timestart + $timeduration + 3600) { $access_to = "用户已过期"; }
			else { $access_to = LoginSucceed($nickname, $authority); }
		}
		else {
			$access_to = LoginSucceed($nickname, $authority);
		}
		echo $access_to;
	//}
	//else {
		//echo "用户名或密码错误";
	//}
}

// 接下来存储用户本次请求的相关信息
$current_ip = mysqli_real_escape_string($dbc, trim(real_ip()));
$current_url = mysqli_real_escape_string($dbc, trim(real_url()));
$current_time = date("Y-m-d H:i:s");
$query = "INSERT INTO sql_injection (user_ip, request_url, user_content, access_to, create_time) VALUES ('$current_ip', '$current_url', '$query_to_save', '$access_to', '$current_time')";
$result = mysqli_query($dbc, $query);

mysqli_close($dbc);

function LoginSucceed($nickname, $authority) {
	session_start();
	$_SESSION['username'] = $nickname;
	$_SESSION['authority'] = $authority;
	switch($authority) {
		case 1:
		case 2:
		case 3:
			return "administrator";
			break;
		case 4:
		case 5:
			return "succeed";
			break;
		default:
			return "用户名或密码错误";
	}
}

function real_ip() {
	$ip=FALSE;
	//客户端IP 或 NONE
	if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	}
	//多重代理服务器下的客户端真实IP地址（可能伪造）,如果没有使用代理，此字段为空
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
		if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
		for ($i = 0; $i < count($ips); $i++) {
			if (!eregi ("^(10│172.16│192.168).", $ips[$i])) {
				$ip = $ips[$i];
				break;
			}
		}
	}
	//客户端IP 或 (最后一个)代理服务器 IP
	return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}

function real_url() {
	$url = 'http://';
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
		$url = 'https://';
	}
	// 判断端口
	if ($_SERVER['SERVER_PORT'] != '80') {
		$url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
	} else {
		$url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	}

	return $url;
}

?>
