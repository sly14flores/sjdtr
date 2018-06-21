<?php

class log_order {
	
	var $schedules;
	var $flexible;
	
	function __construct($con,$id) {
		
		$default_schedule = $con->getData("SELECT id FROM schedules WHERE is_default = 1");
		$schedule = $con->getData("SELECT schedule_id FROM employees WHERE id = $id");
		$schedule_id = ($schedule[0]['schedule_id']==0)?$default_schedule[0]['id']:$schedule[0]['schedule_id'];
		$flexible = $con->getData("SELECT flexible FROM schedules WHERE id = ".$schedule_id);
		$this->flexible = $flexible[0]['flexible'];
		$schedules = $con->getData("SELECT * FROM schedule_details WHERE schedule_id = $schedule_id ORDER BY id");

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
				$allotment = array("afternoon_out"=>date("H:i:s",$tlog));
			} else { # In
				$allotment = array("morning_in"=>date("H:i:s",$tlog));
			}			
			
		} else {

			if ($tlog < $morning_cutoff) $allotment = array("morning_in"=>date("H:i:s",$tlog));
			if ( ($tlog >= $morning_cutoff) && ($tlog < $lunch_cutoff) ) $allotment = array("morning_out"=>date("H:i:s",$tlog));

			if ( ($tlog < $afternoon_cutoff) && ($tlog >= $lunch_cutoff) ) $allotment = array("afternoon_in"=>date("H:i:s",$tlog));
			if ($tlog >= $afternoon_cutoff) $allotment = array("afternoon_out"=>date("H:i:s",$tlog));
		
		}
		
		return $allotment;

	}
	
	function tardiness_undertime($row,$travel_order,$leave) {

		$morning_tardiness_cutoff = $row['ddate']." ".$this->schedules[date("l",strtotime($row['ddate']))]['morning_in'];
		$morning_undertime_cutoff = $row['ddate']." ".$this->schedules[date("l",strtotime($row['ddate']))]['morning_out'];
		$afternoon_tardiness_cutoff = $row['ddate']." ".$this->schedules[date("l",strtotime($row['ddate']))]['afternoon_in'];
		$afternoon_undertime_cutoff = $row['ddate']." ".$this->schedules[date("l",strtotime($row['ddate']))]['afternoon_out'];
		
		$morning_tardiness_cutoff = date("Y-m-d H:i:s",strtotime("+".$this->schedules[date("l",strtotime($row['ddate']))]['morning_grace_period']." Minutes",strtotime($morning_tardiness_cutoff)));
		$afternoon_tardiness_cutoff = date("Y-m-d H:i:s",strtotime("+".$this->schedules[date("l",strtotime($row['ddate']))]['afternoon_grace_period']." Minutes",strtotime($afternoon_tardiness_cutoff)));
		
		$morning_in = $row['ddate']." ".$row['morning_in'];
		$morning_out = $row['ddate']." ".$row['morning_out'];
		$afternoon_in = $row['ddate']." ".$row['afternoon_in'];
		$afternoon_out = $row['ddate']." ".$row['afternoon_out'];

		$morning_tardiness = (strtotime($morning_in)>strtotime($morning_tardiness_cutoff))?(strtotime($morning_in)-strtotime($morning_tardiness_cutoff)):0;
		$morning_undertime = (strtotime($morning_out)<strtotime($morning_undertime_cutoff))?(strtotime($morning_undertime_cutoff)-strtotime($morning_out)):0;
		$afternoon_tardiness = (strtotime($afternoon_in)>strtotime($afternoon_tardiness_cutoff))?(strtotime($afternoon_in)-strtotime($afternoon_tardiness_cutoff)):0;
		$afternoon_undertime = (strtotime($afternoon_out)<strtotime($afternoon_undertime_cutoff))?(strtotime($afternoon_undertime_cutoff)-strtotime($afternoon_out)):0;		
		
		if (($travel_order['to']) || ($leave['leave'])) { # on travel and leave
			if (($travel_order['duration'] == "Wholeday") || ($leave['duration'] == "Wholeday")) {
				$morning_tardiness = 0;
				$morning_undertime = 0;		
				$afternoon_tardiness = 0;
				$afternoon_undertime = 0;
			};
			if (($travel_order['duration'] == "AM") || ($leave['duration'] == "AM")) {
				$morning_tardiness = 0;
				$morning_undertime = 0;
			};
			if (($travel_order['duration'] == "PM") || ($leave['duration'] == "PM")) {
				$afternoon_tardiness = 0;
				$afternoon_undertime = 0;
			};			
		};
		
		if ($this->flexible) { # schedule is flexible
			$morning_tardiness = 0;
			$morning_undertime = 0;		
			$afternoon_tardiness = 0;
			$afternoon_undertime = 0;		
		};
		
		if (date("H:i:s",strtotime($morning_in))=="00:00:00") $morning_tardiness = 0;
		if (date("H:i:s",strtotime($morning_out))=="00:00:00") $morning_undertime = 0;
		if (date("H:i:s",strtotime($afternoon_in))=="00:00:00") $afternoon_tardiness = 0;
		if (date("H:i:s",strtotime($afternoon_out))=="00:00:00") $afternoon_undertime = 0;
		
		$tardiness = $morning_tardiness+$afternoon_tardiness;
		$undertime = $morning_undertime+$afternoon_undertime;		
		
		$row['tardiness'] = gmdate('H:i:s',$tardiness);
		$row['undertime'] = gmdate('H:i:s',$undertime);
		
		return $row;
		
	}

}

?>