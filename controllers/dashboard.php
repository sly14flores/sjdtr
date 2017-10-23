<?php

$_POST = json_decode(file_get_contents('php://input'), true);

require_once '../db.php';
require_once '../dat_files.php';

$dir = "../logfiles/";

switch ($_GET['r']) {

	case "upload_log":

	move_uploaded_file($_FILES['file']['tmp_name'],$dir.$_GET['fn']);

	break;
	
	case "check_log_files_existence":
	
	$files = scandir($dir);
	$found = false;
	$response = [];
	
	switch ($_POST['prefile']) {
		
		case "dat":
		
			foreach ($files as $i => $file) {
				
				foreach ($dat_files as $ii => $dat_file) { // at least 1 file exists
				
					if ($dat_file['file'].".dat" == $file) {
						$found = true;
						break 2;
					}
				
				}
				
			}
			
			if ($found) {
				$response = array(300,"Text files found...","a");
			} else {
				$response = array(400,"No text files found...","a");
			}			
		
		break;
		
		case "mdb":

			$file = "MSEEDBioOfficedb.mdb";
			
			foreach ($files as $key => $value) {
				
				if ($value == $file) {
					$found = true;
					break;
				}
				
			}
			
			if ($found) {
				$response = array(300,"Found file $file...","a");
			} else {
				$response = array(400,"File $file not found...","a");
			}
			
		break;
		
	}
	
	echo json_encode($response);
	
	break;
	
	case "check_log_file_existence":

	$response = [];

	if (file_exists($dir.$_POST['pf'])) {
		$response = array(300,"File $_POST[pf] found...","a");
	} else {
		$response = array(400,"File $_POST[pf] not found...","a");
	}
	
	echo json_encode($response);	
	
	break;
	
	case "collect_logs":
	
	$logs = [];	
	
 	$dateFrom = array("year"=>date("Y",strtotime($_POST['filter']['dateFrom'])),"month"=>date("m",strtotime($_POST['filter']['dateFrom'])),"day"=>date("d",strtotime($_POST['filter']['dateFrom'])));
	$dateTo = array("year"=>date("Y",strtotime($_POST['filter']['dateTo'])),"month"=>date("m",strtotime($_POST['filter']['dateTo'])),"day"=>date("d",strtotime($_POST['filter']['dateTo'])));

	$idFrom = (isset($_POST['filter']['idFrom'])) ? $_POST['filter']['idFrom'] : 0;
	$idTo = (isset($_POST['filter']['idTo'])) ? $_POST['filter']['idTo'] : 0;	
	
	switch ($_POST['how']) {
		
		case "preuploaded":
			
			switch ($_POST['opt']) {

				case "dat":

				require_once '../dtrImportDat.php';				
				$files = scandir($dir);

				$logs = [];
				foreach ($files as $i => $file) {
					
					if (explode(".",$file)[1] == "dat") {
						
						$dat_logs = logsFiltered($file,$dateFrom,$dateTo,$idFrom,$idTo);
						
						foreach ($dat_logs as $log) {
							
							$logs[] = $log;
							
						}
						
					}					
					
				}
					
				break;
				
				case "mdb":

					require_once '../dtrImportMSeed.php';
					$mseed = new dtrImportMSeed("DTR");
					$logs = $mseed->logsFiltered($dateFrom,$dateTo,$idFrom,$idTo);
					
				break;
				
			}
			
		break;
		
		case "upload":
			
			switch (explode(".",$_POST['opt'])[1]) {
				
				case "dat":
					
				require_once '../dtrImportDat.php';
				
				$logs = logsFiltered($_POST['opt'],$dateFrom,$dateTo,$idFrom,$idTo);
					
				break;
				
				case "mdb":

					require_once '../dtrImportMSeed.php';
					$mseed = new dtrImportMSeed("DTR");
					$logs = $mseed->logsFiltered($dateFrom,$dateTo,$idFrom,$idTo);				
				
				break;
				
			}
			
		break;
		
	}

	echo json_encode(array(array(300,"Initiating import...","a"),array("logs"=>$logs)));
	
	break;
	
	case "put_log":
		
		/*
		** backlog
		*/
		$con = new pdo_db("backlogs");
		$_POST['system_log'] = "CURRENT_TIMESTAMP";
		$exists = $con->getData("SELECT * FROM backlogs WHERE pers_id = '$_POST[pers_id]' AND log = '$_POST[log]'");
		if ($con->rows == 0) $backlog = $con->insertData($_POST);
		
		echo json_encode(array(200,"Imported ".date("h:i:s A m/d/Y",strtotime($_POST['log']))." for ".$_POST['pers_id'],'a'));
	
	break;
	
}

?>