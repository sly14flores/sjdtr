<?php

class log_order {
	
	var $schedules;
	var $flexible;
	
	function __construct($con,$id) {
		
		$default = $con->getData("SELECT schedule_id FROM schedules WHERE schedule_default = 1");
		$schedule = $con->getData("SELECT schedule_id FROM employees WHERE id = $id");
		
		if ($schedule[0]['schedule_id'] == 0) $schedule[0]['schedule_id'] = $default[0]['schedule_id'];
			
		$flexible = $con->getData("SELECT flexible FROM schedules WHERE id = ".$schedule[0]['schedule_id']);
		$this->flexible = $flexible[0]['flexible'];
		$schedules = $con->getData("SELECT * FROM schedule_details WHERE schedule_id = ".$schedule[0]['schedule_id']." ORDER BY id");

		foreach ($schedules as $key => $schedule) {
			
			unset($schedules[$key]['id']);
			unset($schedules[$key]['schedule_id']);
			unset($schedules[$key]['day']);
			$this->schedules[$schedule['day']] = $schedules[$key];
			
		}		

	}
	
	function allot($date,$log) {

		$allotment = null;
		
		$morning_cutoff = strtotime("$date ".$this->schedules[date("l",strtotime($date))]['morning_cutoff']);
		$lunch_cutoff = strtotime("$date ".$this->schedules[date("l",strtotime($date))]['lunch_break_cutoff']);
		$afternoon_cutoff = strtotime("$date ".$this->schedules[date("l",strtotime($date))]['afternoon_cutoff']);

		$tlog = strtotime($log['log']);

		if ($this->flexible) {
			
			if ($log['flexible']) { # Out
				$allotment = array("morning_in"=>date("H:i:s",$tlog));
			} else { # In
				$allotment = array("afternoon_out"=>date("H:i:s",$tlog));
			}			
			
		} else {

			if ($tlog < $morning_cutoff) $allotment = array("morning_in"=>date("H:i:s",$tlog));
			if ( ($tlog >= $morning_cutoff) && ($tlog < $lunch_cutoff) ) $allotment = array("morning_out"=>date("H:i:s",$tlog));

			if ( ($tlog < $afternoon_cutoff) && ($tlog >= $lunch_cutoff) ) $allotment = array("afternoon_in"=>date("H:i:s",$tlog));
			if ($tlog >= $afternoon_cutoff) $allotment = array("afternoon_out"=>date("H:i:s",$tlog));
		
		}
		
		return $allotment;

	}

}

?>