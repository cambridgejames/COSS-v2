<?php

include("../common/common-define.php");
include("../common/common-functions.php");

checkIsLegal(isset($_POST["compsname"]), null, 1);
$compsname = $_POST["compsname"];

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$result = mysqli_query($dbc, "SELECT isstart_sign FROM comps_info WHERE comps_name = '$compsname'");
mysqli_close($dbc);

checkIsLegal(mysqli_num_rows($result) > 0, null, 2);

mysqli_data_seek($result, 0);
echo mysqli_fetch_array($result)[0] == 1 ? "succeed" : "failed";

?>