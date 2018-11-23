<?php

include("../common/common-define.php");
include("../common/common-functions.php");

checkIsLegal(isset($_POST["compsID"]), null, 1);
$compsID = $_POST["compsID"];

$query = "SELECT comps_name FROM comps_info WHERE comps_id like '$compsID%'";
//SELECT comps_name FROM comps_info WHERE comps_id = '$compsID'
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$result = mysqli_query($dbc, $query);

if ($result && mysqli_num_rows($result) > 0) {
	while($row = mysqli_fetch_assoc($result)) {
		echo "<div onclick=\"getScores(this.innerHTML)\">".$row['comps_name']."</div>";
	}
}
else {
	echo "failed";
}
mysqli_close($dbc);

?>