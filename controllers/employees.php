<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';
require_once '../dat_files.php';
require_once '../analyze.php';

// header("Content-type: application/json");

switch ($_GET['r']) {
	
	case "start":
	
		$con = new pdo_db();
		
		$sql = "SELECT id, empid, CONCAT(first_name, ' ', last_name) full_name FROM employees WHERE is_built_in != 1";
		$employees = $con->getData($sql);
		
		echo json_encode($employees);
	
	break;
	
	case "new":

		$con = new pdo_db("employees");
		
		$employee = $con->insertData(array("is_built_in"=>0));
		
		echo $con->insertId;
	
	break;
	
	case "cancel":

		$con = new pdo_db("employees");
		$picture = "../pictures/".$_POST['empid'].".jpg";
		if (file_exists($picture)) unlink($picture);
		
		$delete = $con->deleteData(array("id"=>implode(",",$_POST['id'])));
	
	break;
	
	case "upload_profile_picture":
		
		$dir = "../pictures/";
		
		move_uploaded_file($_FILES['file']['tmp_name'],$dir."$_GET[empid]$_GET[en]");

	break;
	
	case "update":
		
		$_POST['birthday'] = (isset($_POST['birthday'])) ? date("Y-m-d",strtotime($_POST['birthday'])) : "0000-00-00";
		$_POST['schedule_id'] = $_POST['schedule_id']['id'];
		
		$con = new pdo_db("employees");
		
		if (isset($_POST['has_profile_pic'])) unset($_POST['has_profile_pic']);
		$update = $con->updateData($_POST,'id');
		
	break;
	
	case "view":
	
		$con = new pdo_db();
		
		$employee = $con->getData("SELECT *, (SELECT description FROM schedules WHERE id = schedule_id) description FROM employees WHERE id = $_POST[id]");
		$picture = "../pictures/".$employee[0]['empid'].".jpg";
		$employee[0]['schedule_id'] = array("id"=>$employee[0]['schedule_id'],"description"=>$employee[0]['description']);
		unset($employee[0]['description']);
		$employee[0]['has_profile_pic'] = file_exists($picture);
		
		echo json_encode($employee[0]);
	
	break;
	
	case "dtr":

		/*
		**	check for dtr
		*/
		$con = new pdo_db("dtr");
		$datef = $_POST['year']."-".$_POST['month'];
		$dtr = $con->getData("SELECT * FROM dtr WHERE eid = $_POST[id] AND ddate LIKE '$datef%'");
		
		$date = $_POST['year']."-".$_POST['month']."-01";
		$start = date("Y-m-d",strtotime($date));
		$end = date("Y-m-t",strtotime($date));
		
		if (count($dtr) == 0) {
			
			$dtr = [];
			$analyze = new log_order($con,$_POST['id']);		
			while (strtotime($start) <= strtotime($end)) {
			
			$logs = $con->getData("SELECT * FROM backlogs WHERE pers_id = '".empid($con,$_POST['id'])."' AND date = '$start'");

				/*
				** analyze timein/timeout
				*/
				$analyzed = array(
					"morning_in"=>"00:00:00",
					"morning_out"=>"00:00:00",
					"afternoon_in"=>"00:00:00",
					"afternoon_out"=>"00:00:00"
				);

				foreach ($logs as $log) {
					$allotment = $analyze->allot($start,$log['log']);
					$prop = array_keys($allotment);
					$analyzed[$prop[0]] = $allotment[$prop[0]];
				};
				
				$dtr[] = array("ddate"=>date("Y-m-d",strtotime($start)),
						"eid"=>$_POST['id'],
						"morning_in"=>$analyzed['morning_in'],
						"morning_out"=>$analyzed['morning_out'],
						"afternoon_in"=>$analyzed['afternoon_in'],
						"afternoon_out"=>$analyzed['afternoon_out'],
						"tardiness"=>0
						);
				
				$start = date("Y-m-d", strtotime("+1 day", strtotime($start)));	
				
			};

			$build_dtr = $con->insertDataMulti($dtr);
			
			$dtr = $con->getData("SELECT * FROM dtr WHERE eid = $_POST[id] AND ddate LIKE '$datef%'");			
			
		};
		
		if ($_POST['regen']) {
			
			$_dtr = $dtr;			
			$analyze = new log_order($con,$_POST['id']);									
			
			foreach ($_dtr as $key => $d) {
				
				$logs = $con->getData("SELECT * FROM backlogs WHERE pers_id = '".empid($con,$_POST['id'])."' AND date = '".$d['ddate']."'");

				$analyzed = array(
					"morning_in"=>"00:00:00",
					"morning_out"=>"00:00:00",
					"afternoon_in"=>"00:00:00",
					"afternoon_out"=>"00:00:00"
				);

				foreach ($logs as $log) {
					$allotment = $analyze->allot($d['ddate'],$log['log']);
					$prop = array_keys($allotment);
					$analyzed[$prop[0]] = $allotment[$prop[0]];
				};					
				
				$_dtr[$key]['morning_in'] = $analyzed['morning_in'];
				$_dtr[$key]['morning_out'] = $analyzed['morning_out'];
				$_dtr[$key]['afternoon_in'] = $analyzed['afternoon_in'];
				$_dtr[$key]['afternoon_out'] = $analyzed['afternoon_out'];
				unset($_dtr[$key]['eid']);
				unset($_dtr[$key]['ddate']);
				
			};
			
			$build_dtr = $con->updateDataMulti($_dtr,'id');
			
			$dtr = $con->getData("SELECT * FROM dtr WHERE eid = $_POST[id] AND ddate LIKE '$datef%'");			
			
		};
		
		foreach ($dtr as $key => $value) {
			
			$dtr[$key]['sdate'] = date("j",strtotime($value['ddate']));
			$dtr[$key]['day'] = date("l",strtotime($value['ddate']));
			$dtr[$key]['morning_in'] = date("H:i:s",strtotime($value['morning_in']));
			$dtr[$key]['morning_out'] = date("H:i:s",strtotime($value['morning_out']));
			$dtr[$key]['afternoon_in'] = date("H:i:s",strtotime($value['afternoon_in']));
			$dtr[$key]['afternoon_out'] = date("H:i:s",strtotime($value['afternoon_out']));
			unset($dtr[$key]['eid']);
		};
		
		echo json_encode($dtr);
	
	break;
	
	case "schedules":
	
		$con = new pdo_db();
		
		$results = $con->getData("SELECT * FROM schedules");
		
		$schedules[0] = array("id"=>0,"description"=>"Default");
		foreach ($results as $result) {
			$schedules[] = $result;
		}

		echo json_encode($schedules);
	
	break;
	
	case "list":
		
		$con = new pdo_db();
		$employees_list = $con->getData("SELECT id, CONCAT(first_name, ' ', middle_name, ' ', last_name) employee_fullname FROM employees WHERE is_built_in = 0");
		
		echo json_encode($employees_list);
	
	break;
	
	case "manageDtr":
	
		$con = new pdo_db();
		$dtr = $con->getData("SELECT *, (SELECT employees.empid FROM employees WHERE employees.id = dtr.eid) pers_id FROM dtr WHERE id = $_POST[id]");

		$ddate = $dtr[0]['ddate'];
		$pers_id = $dtr[0]['pers_id'];
		
		foreach ($dtr as $key => $value) {
			$dtr[$key]['edit'] = true;
			$dtr[$key]['morning_in'] = date("h:i:s A",strtotime($value['morning_in']));
			$dtr[$key]['morning_out'] = date("h:i:s A",strtotime($value['morning_out']));
			$dtr[$key]['afternoon_in'] = date("h:i:s A",strtotime($value['afternoon_in']));
			$dtr[$key]['afternoon_out'] = date("h:i:s A",strtotime($value['afternoon_out']));
			unset($dtr[$key]['eid']);
			unset($dtr[$key]['ddate']);
			unset($dtr[$key]['tardiness']);
			unset($dtr[$key]['pers_id']);
		}
		
		$backlogs = $con->getData("SELECT log, machine FROM backlogs WHERE pers_id = '$pers_id' AND date = '$ddate'");
		
		foreach ($backlogs as $key => $value) {
			$backlogs[$key]['log'] = date("h:i:s A",strtotime($backlogs[$key]['log']));
			$backlogs[$key]['machine'] = getLocation($backlogs[$key]['machine']);
			$backlogs[$key]['assignment'] = "";
		}
		
		echo json_encode(array("dtr_specific"=>$dtr[0],"backlogs"=>$backlogs));
	
	break;
	
	case "saveDtr":
	
		$con = new pdo_db("dtr");

		$_POST['morning_in'] = date("H:i:s",strtotime($_POST['morning_in']));
		$_POST['morning_out'] = date("H:i:s",strtotime($_POST['morning_out']));
		$_POST['afternoon_in'] = date("H:i:s",strtotime($_POST['afternoon_in']));
		$_POST['afternoon_out'] = date("H:i:s",strtotime($_POST['afternoon_out']));
		unset($_POST['edit']);
		unset($_POST['pers_id']);
		
		$dtr = $con->updateData($_POST,'id');
	
	break;
	
	case "assignLog":
	
		$con = new pdo_db("dtr");
		
		$log = array("id"=>$_POST['id']);
		$log[$_POST['log']['assignment']] = date("H:i:s",strtotime("2000-01-01 ".$_POST['log']['log']));
		
		$update = $con->updateData($log,"id");
	
	break;
	
}

function empid($con,$id) {
	
	$empid = $con->getData("SELECT empid FROM employees WHERE id = $id");
	
	return $empid[0]['empid'];
	
};

?>