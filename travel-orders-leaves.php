<?php

class travel_orders {
	
	var $travel_orders;
	
	function __construct($con,$employee_id,$datef) {
		
		$this->travel_orders = $con->getData("SELECT travel_orders.id, travel_orders.description, travel_orders.remarks, travel_orders_dates.to_date, travel_orders_dates.to_duration FROM travel_orders LEFT JOIN travel_orders_dates ON travel_orders.id = travel_orders_dates.to_id WHERE travel_orders.employee_id = $employee_id AND travel_orders_dates.to_date LIKE '$datef%'");		
		
	}
	
	function getTo($date) {
		
		$to = array("to"=>false,"duration"=>null);
		
		foreach ($this->travel_orders as $travel_order) {
			
			if ($travel_order['to_date'] === $date) $to = array("to"=>true,"duration"=>$travel_order['to_duration']);
			
		};
		
		return $to;
		
	}
	
	function travel_order($dtr,$travel_order) {

		$dtr['day_to'] = false;
		$dtr['morning_in_to'] = false;
		$dtr['morning_out_to'] = false;
		$dtr['afternoon_in_to'] = false;
		$dtr['afternoon_out_to'] = false;
		if ($travel_order['to']) {

			$dtr['day_to'] = true;
		
			switch ($travel_order['duration']) {
				
				case "Wholeday":
				
					$dtr['morning_in_to'] = true;
					$dtr['morning_out_to'] = true;
					$dtr['afternoon_in_to'] = true;
					$dtr['afternoon_out_to'] = true;
					
					$dtr['morning_in'] = "TO";
					$dtr['morning_out'] = "TO";
					$dtr['afternoon_in'] = "TO";
					$dtr['afternoon_out'] = "TO";
				
				break;
				
				case "AM":
				
					$dtr['morning_in_to'] = true;
					$dtr['morning_out_to'] = true;
					$dtr['afternoon_in_to'] = false;
					$dtr['afternoon_out_to'] = false;						
				
					$dtr['morning_in'] = "TO";
					$dtr['morning_out'] = "TO";				
				
				break;
				
				case "PM":
				
					$dtr['morning_in_to'] = false;
					$dtr['morning_out_to'] = false;
					$dtr['afternoon_in_to'] = true;
					$dtr['afternoon_out_to'] = true;

					$dtr['afternoon_in'] = "TO";
					$dtr['afternoon_out'] = "TO";						
				
				break;
				
			};
			
		};
		
		return $dtr;
		
	}
	
}

class leaves {
	
	var $leaves;
	
	function __construct($con,$employee_id,$datef) {
		
		$this->leaves = $con->getData("SELECT leaves.id, leaves.leave_type, leaves.remarks, leaves_dates.leave_date, leaves_dates.leave_duration FROM leaves LEFT JOIN leaves_dates ON leaves.id = leaves_dates.leave_id WHERE leaves.employee_id = $employee_id AND leaves_dates.leave_date LIKE '$datef%'");		
		
	}
	
	function getLeave($date) {
		
		$l = array("leave"=>false,"duration"=>null);
		
		foreach ($this->leaves as $leave) {
			
			if ($leave['leave_date'] === $date) $l = array("leave"=>true,"duration"=>$leave['leave_duration']);
			
		};
		
		return $l;
		
	}
	
	function leave($dtr,$leave) {

		$dtr['day_leave'] = false;
		$dtr['morning_in_leave'] = false;
		$dtr['morning_out_leave'] = false;
		$dtr['afternoon_in_leave'] = false;
		$dtr['afternoon_out_leave'] = false;
		if ($leave['leave']) {

			$dtr['day_leave'] = true;
		
			switch ($leave['duration']) {
				
				case "Wholeday":
				
					$dtr['morning_in_leave'] = true;
					$dtr['morning_out_leave'] = true;
					$dtr['afternoon_in_leave'] = true;
					$dtr['afternoon_out_leave'] = true;
					
					$dtr['morning_in'] = "Leave";
					$dtr['morning_out'] = "Leave";
					$dtr['afternoon_in'] = "Leave";
					$dtr['afternoon_out'] = "Leave";
				
				break;
				
				case "AM":
				
					$dtr['morning_in_leave'] = true;
					$dtr['morning_out_leave'] = true;
					$dtr['afternoon_in_leave'] = false;
					$dtr['afternoon_out_leave'] = false;						
				
					$dtr['morning_in'] = "Leave";
					$dtr['morning_out'] = "Leave";				
				
				break;
				
				case "PM":
				
					$dtr['morning_in_leave'] = false;
					$dtr['morning_out_leave'] = false;
					$dtr['afternoon_in_leave'] = true;
					$dtr['afternoon_out_leave'] = true;

					$dtr['afternoon_in'] = "Leave";
					$dtr['afternoon_out'] = "Leave";						
				
				break;
				
			};
			
		};
		
		return $dtr;
		
	}	
	
}

?>