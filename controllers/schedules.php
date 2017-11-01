<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';

switch ($_GET['r']) {
	
	case "start":
	
		$con = new pdo_db();
		
		$sql = "SELECT * FROM schedules";
		$schedules = $con->getData($sql);
		
		echo json_encode($schedules);
	
	break;

	case "new":

		$con = new pdo_db("schedules");
		
		$schedule = $con->insertData(array("description"=>$_POST['description']));
		
		echo $con->insertId;
	
	break;
	
	case "update":
	
		$con = new pdo_db("schedules");
		$con1 = new pdo_db("schedule_details");
		
		$_POST['flexible'] = ($_POST['flexible']=="Yes")?1:0;
		$schedule = $con->updateData(array("id"=>$_POST['id'],"description"=>$_POST['description'],"flexible"=>$_POST['flexible']),"id");
		
		// check for schedule_details entry		
		$sql = "SELECT * FROM schedule_details WHERE schedule_id = $_POST[id]";
		$results = $con1->getData($sql);
		
		if ($con1->rows > 0) { // update

			foreach ($_POST['details'] as $key => $value) {

				$_POST['details'][$key]['schedule_id'] = $_POST['id'];
				$_POST['details'][$key]['morning_in'] = ($_POST['details'][$key]['morning_in']==null)?"00:00:00":date("H:i:s",strtotime($_POST['details'][$key]['morning_in']));
				$_POST['details'][$key]['morning_cutoff'] = ($_POST['details'][$key]['morning_cutoff']==null)?"00:00:00":date("H:i:s",strtotime($_POST['details'][$key]['morning_cutoff']));
				$_POST['details'][$key]['morning_out'] = ($_POST['details'][$key]['morning_out']==null)?"00:00:00":date("H:i:s",strtotime($_POST['details'][$key]['morning_out']));
				$_POST['details'][$key]['lunch_break_cutoff'] = ($_POST['details'][$key]['lunch_break_cutoff']==null)?"00:00:00":date("H:i:s",strtotime($_POST['details'][$key]['lunch_break_cutoff']));
				$_POST['details'][$key]['afternoon_in'] = ($_POST['details'][$key]['afternoon_in']==null)?"00:00:00":date("H:i:s",strtotime($_POST['details'][$key]['afternoon_in']));
				$_POST['details'][$key]['afternoon_cutoff'] = ($_POST['details'][$key]['afternoon_cutoff']==null)?"00:00:00":date("H:i:s",strtotime($_POST['details'][$key]['afternoon_cutoff']));
				$_POST['details'][$key]['afternoon_out'] = ($_POST['details'][$key]['afternoon_out']==null)?"00:00:00":date("H:i:s",strtotime($_POST['details'][$key]['afternoon_out']));
				
			}		
		
			$schedule_details = $con1->updateDataMulti($_POST['details'],"id");
			
		} else { // add

			foreach ($_POST['details'] as $key => $value) {
				
				unset($_POST['details'][$key]['id']);
				$_POST['details'][$key]['schedule_id'] = $_POST['id'];
				$_POST['details'][$key]['morning_in'] = date("H:i:s",strtotime($_POST['details'][$key]['morning_in']));
				$_POST['details'][$key]['morning_cutoff'] = date("H:i:s",strtotime($_POST['details'][$key]['morning_cutoff']));				
				$_POST['details'][$key]['morning_out'] = date("H:i:s",strtotime($_POST['details'][$key]['morning_out']));
				$_POST['details'][$key]['lunch_break_cutoff'] = date("H:i:s",strtotime($_POST['details'][$key]['lunch_break_cutoff']));				
				$_POST['details'][$key]['afternoon_in'] = date("H:i:s",strtotime($_POST['details'][$key]['afternoon_in']));
				$_POST['details'][$key]['afternoon_cutoff'] = date("H:i:s",strtotime($_POST['details'][$key]['afternoon_cutoff']));				
				$_POST['details'][$key]['afternoon_out'] = date("H:i:s",strtotime($_POST['details'][$key]['afternoon_out']));
				
			}
			
			$schedule_details = $con1->insertDataMulti($_POST['details']);
			
		}
	
	break;
	
	case "cancel":

		$con = new pdo_db("schedules");		
		$delete = $con->deleteData(array("id"=>implode(",",$_POST['id'])));
	
	break;
	
	case "view":
	
		$con = new pdo_db();
		$schedule = $con->getData("SELECT * FROM schedules WHERE id = $_POST[id]");
		$schedule_details = $con->getData("SELECT * FROM schedule_details WHERE schedule_id = $_POST[id]");
		
		$schedule[0]['flexible'] = ($schedule[0]['flexible'])?"Yes":"No";
		
		foreach ($schedule_details as $key => $value) {
			unset($schedule_details[$key]['schedule_id']);
		}
		
		$schedule[0]['details'] = $schedule_details;
		
		echo json_encode($schedule[0]);
	
	break;
	
}

?>