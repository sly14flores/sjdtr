<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

switch ($_GET['r']) {

	case "login":
	$con = new pdo_db();
	$sql = "SELECT id, empid, first_name, middle_name, last_name FROM employees WHERE is_built_in = 1 AND username = '".$_POST['username']."' AND password = '".$_POST['password']."'";
	$account = $con->getData($sql);
	if (($con->rows) > 0) {
		session_start();
		$_SESSION['id'] = $account[0]['id'];
		echo json_encode($account[0]);
	} else {
		echo json_encode(array("id"=>0));
	}
	break;

}

?>