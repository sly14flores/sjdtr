<?php

require_once '../leave-types.php';

function leaveDescription($id) {
	
	global $leaves_types;
	
	$leaveDescription = array();
	
	foreach ($leaves_types as $leave_type) {
		
		if ($leave_type['id'] == $id) {
			$leaveDescription = $leave_type['description'];
			break;
		};
		
	};
	
	return $leaveDescription;	
	
};

function leaveObj($id) {

	global $leaves_types;
	
	$leaveObj = array();
	
	foreach ($leaves_types as $leave_type) {
		
		if ($leave_type['id'] == $id) {
			$leaveObj = $leave_type;
			break;
		};
		
	};
	
	return $leaveObj;
	
}

?>