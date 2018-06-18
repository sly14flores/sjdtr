<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';
require_once 'leaves.php';

$con = new pdo_db();

$leave = $con->getData("SELECT id, leave_type, remarks FROM leaves WHERE id = ".$_POST['id']);
$leave[0]['leave_type'] = leaveObj($leave[0]['leave_type']);
$leave[0]['dates']['dels'] = [];

$leaves_dates = $con->getData("SELECT id, leave_date, leave_duration FROM leaves_dates WHERE leave_id = ".$leave[0]['id']);

foreach ($leaves_dates as $key => $leave_date) {
	
	$leaves_dates[$key]['disabled'] = true;

};

$leave[0]['dates']['data'] = $leaves_dates;

echo json_encode($leave[0]);

?>
