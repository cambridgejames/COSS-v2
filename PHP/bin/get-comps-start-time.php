<?php

include("../common/common-define.php");
include("../common/common-functions.php");

session_start();
checkIsLegal(isset($_SESSION['username']) && isset($_SESSION['authority']) && isset($_POST["compsname"]), null, 1);

$username = $_SESSION['username'];
$authority = $_SESSION['authority'];
$compsname = $_POST["compsname"];

checkIsLegal($authority <= 3, null, 2);

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_set_charset($dbc, "utf8");
$result = mysqli_query($dbc, "SELECT time_start FROM comps_info WHERE comps_name = '$compsname'");

checkIsLegal(mysqli_num_rows($result) > 0, $dbc, 5);
mysqli_close($dbc);

mysqli_data_seek($result, 0);
echo mysqli_fetch_array($result)[0];

?>