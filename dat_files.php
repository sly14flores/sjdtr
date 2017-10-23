<?php

$dat_files = array(
	array("machine"=>1,"location"=>"Municipal Hall","file"=>"OPH6100066092900049_attlog"),
	array("machine"=>2,"location"=>"Slaugther House","file"=>"OPH6100066092900013_attlog"),
	array("machine"=>2,"location"=>"Health Office","file"=>"OPH7030067030100041_attlog")
);

function getDeviceNo($dev) {
	
	global $dat_files;
	$no = 0;
	
	foreach ($dat_files as $i => $dat_file) {

		if (explode("_",$dat_file["file"])[0] == $dev) {
			$no = $dat_file["machine"];
			break;
		}
	
	}
	
	return $no;
	
}

function getLocation($no) {

	global $dat_files;
	$loc = "";
	
	foreach ($dat_files as $i => $dat_file) {

		if ($no == $dat_file['machine']) {
			$loc = $dat_file["location"];
			break;
		}
	
	}
	
	return $loc;
	
}

?>