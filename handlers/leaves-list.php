<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';
require_once 'leaves.php';

$con = new pdo_db();

$and = " ";

$year_month = "";

if (isset($_POST['filter']['year'])) if ($_POST['filter']['year'] != "") $year_month = $_POST['filter']['year']."-";
if (isset($_POST['filter']['month'])) $year_month .= $_POST['filter']['month']."-";

if ($year_month != "") $and = " AND leave_date LIKE '$year_month%' ";

$leaves = $con->getData("SELECT DISTINCT leaves.id, leaves.leave_type, leaves.remarks FROM leaves LEFT JOIN leaves_dates ON leaves.id = leaves_dates.leave_id WHERE employee_id = ".$_POST['employee_id'].$and);

foreach ($leaves as $key => $leave) {
	
	$leaves[$key]['leave_type'] = leaveDescription($leave['leave_type']);
	$leaves[$key]['from'] = "";
	$leaves[$key]['to'] = "";
	
	$leaves_dates = $con->getData("SELECT * FROM leaves_dates WHERE leave_id = ".$leave['id']);
	
	if (count($leaves_dates)) {
		
		$leaves[$key]['from'] = date("F j, Y",strtotime($leaves_dates[0]['leave_date']))." (".$leaves_dates[0]['leave_duration'].")";
		$leaves[$key]['to'] = date("F j, Y",strtotime($leaves_dates[count($leaves_dates)-1]['leave_date']))." (".$leaves_dates[count($leaves_dates)-1]['leave_duration'].")";
		
	};
	
};

echo json_encode($leaves);

?>