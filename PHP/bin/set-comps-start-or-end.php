<?php

include("../common/common-define.php");
include("../common/common-functions.php");

session_start();
checkIsLegal(isset($_SESSION['username']) && isset($_SESSION['authority']) && isset($_POST["compsname"]) && isset($_POST["isstartsign"]), null, 1);

$username = $_SESSION['username'];
$authority = $_SESSION['authority'];
$compsname = $_POST["compsname"];
$isstartsign = $_POST["isstartsign"];

checkIsLegal($authority <= 3, null, 2);

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_set_charset($dbc, "utf8");
$result = mysqli_query($dbc, "UPDATE comps_info SET isstart_sign = '$isstartsign' WHERE comps_name = '$compsname'");
mysqli_close($dbc);

checkIsLegal($result == 1, null, 3);
echo "succeed";

?>