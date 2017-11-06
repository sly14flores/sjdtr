<?php

function logsFiltered($logFile,$from,$to,$idFrom,$idTo) {
	
	$dir = "../logfiles";
	$dtr_file = "$dir/$logFile";
	
	$logs = [];
	
	if (!file_exists($dtr_file)) {
		return $logs; 
		exit();
	}
	
	$file = fopen($dtr_file,"rb");

	$line_txt = [];
	while (! feof($file)) {
		$line_txt[] = fgetcsv($file, 0, "\t");
	}		

	// trim ID
	foreach ($line_txt as $i => $row) {
		$logsUnfiltered[$i][0] = trim($line_txt[$i][0]);
		$logsUnfiltered[$i][1] = trim($line_txt[$i][1]);
		$logsUnfiltered[$i][2] = trim($line_txt[$i][2]);
		$logsUnfiltered[$i][3] = trim($line_txt[$i][3]);
	}
	
	// filter ID(s)
	if ( ($idFrom != 0) && ($idTo != 0) ) {
		
		$logIdFiltered = [];
		
		for ($id=$idFrom; $id<=$idTo; ++$id) {
			
			foreach($logsUnfiltered as $i => $row) {

				if ($id == $row[0]) $logIdFiltered[] = $row;
			
			}
			
		}
		
		$logsUnfiltered = $logIdFiltered;

	}

	// filter date range
	$day = implode("-",$from);
	while (strtotime($day) <= strtotime(implode("-",$to))) {

		$year = explode("-",$day)[0];
		$month = explode("-",$day)[1];
		$dayc = explode("-",$day)[2];

		foreach($logsUnfiltered as $i => $row) {

			$pid = $row[0];
			$log = $row[1];
			$machine = $row[2];
			$flexible = $row[3];
			
			if (preg_match("/$year-$month-$dayc/i", $log)) {
				
				$logs[] = array("date"=>"$year-$month-$dayc","pers_id"=>$pid,"log"=>$log,"machine"=>$machine,"flexible"=>$flexible);
				
			}

		}

		$day = date ("Y-m-d", strtotime("+1 day", strtotime($day)));

	}
	
	return $logs;
	
}

?>