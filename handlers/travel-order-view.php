<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db();

$travel_order = $con->getData("SELECT id, description, remarks FROM travel_orders WHERE id = ".$_POST['id']);
$travel_order[0]['dates']['dels'] = [];

$travel_orders_dates = $con->getData("SELECT id, to_date, to_duration FROM travel_orders_dates WHERE to_id = ".$travel_order[0]['id']);

foreach ($travel_orders_dates as $key => $travel_order_date) {
	
	$travel_orders_dates[$key]['disabled'] = true;

};

$travel_order[0]['dates']['data'] = $travel_orders_dates;

echo json_encode($travel_order[0]);

?>
