<?php

$settings = array(
	"dtr"=>array(
		"report"=>"pglu", # form43 | pglu
		"agency"=>"BDH",
	),
	"biometrics"=>array(
		"device"=>"uface402", # uface402 | uface202 | mb160
	),
);

if (isset($_GET['request'])) {
	if ($_GET['request']) echo json_encode($settings);
};

?>