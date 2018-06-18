<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$tables = array("travel_orders","travel_orders_dates");

$dels = $_POST['dates']['dels'];
$travel_orders_dates = $_POST['dates']['data'];
unset($_POST['dates']);
$travel_order = $_POST;

$con = new pdo_db($tables[0]);

if ($travel_order['id']) { # update
	
	$travel_order['system_log'] = "CURRENT_TIMESTAMP";
	$update = $con->updateData($travel_order,'id');
	$travel_order_id = $travel_order['id'];
	
} else { # insert
	
	unset($travel_order['id']);
	$travel_order['system_log'] = "CURRENT_TIMESTAMP";
	$insert = $con->insertData($travel_order);
	$travel_order_id = $con->insertId;
	
};

if (count($dels)) {
	
	$con->table = $tables[1];	
	$delete = $con->deleteData(array("id"=>implode(",",$dels)));	
	
};

if (count($travel_orders_dates)) {
	
	$con->table = $tables[1];
	
	foreach ($travel_orders_dates as $date) {
	
		unset($date['disabled']);
		$date['to_date'] = date("Y-m-d",strtotime($date['to_date']));
	
		if ($date['id']) { # update
			
			$date['system_log'] = "CURRENT_TIMESTAMP";
			$update = $con->updateData($date,'id');
			
		} else { # insert
			
			$date['to_id'] = $travel_order_id;
			$date['system_log'] = "CURRENT_TIMESTAMP";
			$insert = $con->insertData($date);

		}

	};

};

?>