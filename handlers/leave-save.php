<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';
require_once 'leaves.php';

$tables = array("leaves","leaves_dates");

$dels = $_POST['dates']['dels'];
$leaves_dates = $_POST['dates']['data'];
unset($_POST['dates']);

$leave = $_POST;
$leave['leave_type'] = $leave['leave_type']['id'];

$con = new pdo_db($tables[0]);

if ($leave['id']) { # update

	$leave['system_log'] = "CURRENT_TIMESTAMP";
	$update = $con->updateData($leave,'id');
	$leave_id = $leave['id'];
	
} else { # insert
	
	unset($leave['id']);
	$leave['system_log'] = "CURRENT_TIMESTAMP";
	$insert = $con->insertData($leave);
	$leave_id = $con->insertId;
	
};

if (count($dels)) {
	
	$con->table = $tables[1];	
	$delete = $con->deleteData(array("id"=>implode(",",$dels)));	
	
};

if (count($leaves_dates)) {
	
	$con->table = $tables[1];
	
	foreach ($leaves_dates as $date) {
	
		unset($date['disabled']);
		$date['leave_date'] = date("Y-m-d",strtotime($date['leave_date']));
	
		if ($date['id']) { # update
			
			$date['system_log'] = "CURRENT_TIMESTAMP";
			$update = $con->updateData($date,'id');
			
		} else { # insert
			
			$date['leave_id'] = $leave_id;
			$date['system_log'] = "CURRENT_TIMESTAMP";
			$insert = $con->insertData($date);

		}

	};

};

?>