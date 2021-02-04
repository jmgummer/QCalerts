<?php
include 'autoloader.php';
include 'public/settings/settings.php';

ini_set('max_execution_time', 15000);

date_default_timezone_set('Africa/Nairobi');
ini_set('memory_limit', '4096M');

$today = date('Y-m-d');
$monthly = date('Y-m-06');
$today = '2021-02-06';
if (strtotime($today) == strtotime($monthly) && date('D',strtotime($today)) == 'Mon' && date('w', strtotime($today)) == 1) {
	$startdate = date('Y-m-01',strtotime('- 1 month'));
	$endate = date('Y-m-t',strtotime($startdate));
	$subject = "Monthly QC Analysis $startdate - $endate";
} else if(strtotime($today) == strtotime($monthly)){
	$startdate = date('Y-m-01',strtotime('- 1 month'));
	$endate = date('Y-m-t',strtotime($startdate));
	$subject = "Monthly QC Analysis $startdate - $endate";
}else if (date('D') == 'Mon' || date('w', strtotime($today)) == 1) {
	$startdate = date('Y-m-d',strtotime('- 7 days'));
	$endate = date('Y-m-d',strtotime('yesterday'));
	$subject = "Weekly QC Analysis $startdate - $endate";
} else {
	exit('Nothin to do today');
}


$countries = ['Kenya'=>'KE','Uganda'=>'UG','Tanzania'=>'TZ'];
foreach ($countries as $country => $code) {
	$runner = new QCEntries($code);

	echo "Current Region:- $country<br>";
	$runner->generateHTML($startdate,$endate,$subject);
}