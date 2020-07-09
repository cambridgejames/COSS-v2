<?php

include("../common/common-define.php");
include("../common/common-functions.php");

checkIsLegal(isset($_POST["compsID"]), null, 1);
$compsID = $_POST["compsID"];
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$query = "SELECT comps_name FROM comps_info WHERE comps_id like '$compsID%'";
//SELECT comps_name FROM comps_info WHERE comps_id = '$compsID'
$query_to_save = mysqli_real_escape_string($dbc, trim($query));

mysqli_set_charset($dbc, "utf8");
$result = mysqli_query($dbc, $query);

$access_to = "";
if ($result && mysqli_num_rows($result) > 0) {
    $access_to = "visitor";
	while($row = mysqli_fetch_assoc($result)) {
		echo "<div onclick=\"getScores(this.innerHTML)\">".$row['comps_name']."</div>";
	}
}
else {
	echo "failed";
}

// 接下来存储用户本次请求的相关信息
$current_ip = mysqli_real_escape_string($dbc, trim(real_ip()));
$current_url = mysqli_real_escape_string($dbc, trim(real_url()));
$current_time = date("Y-m-d H:i:s");
$query = "INSERT INTO sql_injection (user_ip, request_url, user_content, access_to, create_time) VALUES ('$current_ip', '$current_url', '$query_to_save', '$access_to', '$current_time')";
$result = mysqli_query($dbc, $query);

mysqli_close($dbc);

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