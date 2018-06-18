<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

$con = new pdo_db();

$and = " ";

$year_month = "";

if (isset($_POST['filter']['year'])) if ($_POST['filter']['year'] != "") $year_month = $_POST['filter']['year']."-";
if (isset($_POST['filter']['month'])) $year_month .= $_POST['filter']['month']."-";

if ($year_month != "") $and = " AND to_date LIKE '$year_month%' ";

$travel_orders = $con->getData("SELECT DISTINCT travel_orders.id, travel_orders.description, travel_orders.remarks FROM travel_orders LEFT JOIN travel_orders_dates ON travel_orders.id = travel_orders_dates.to_id WHERE employee_id = ".$_POST['employee_id'].$and);

foreach ($travel_orders as $key => $travel_order) {
	
	$travel_orders[$key]['from'] = "";
	$travel_orders[$key]['to'] = "";
	
	$travel_order_dates = $con->getData("SELECT * FROM travel_orders_dates WHERE to_id = ".$travel_order['id']);
	
	if (count($travel_order_dates)) {
		
		$travel_orders[$key]['from'] = date("F j, Y",strtotime($travel_order_dates[0]['to_date']));
		$travel_orders[$key]['to'] = date("F j, Y",strtotime($travel_order_dates[count($travel_order_dates)-1]['to_date']));
		
	};
	
};

echo json_encode($travel_orders);

?>