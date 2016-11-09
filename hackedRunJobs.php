<?php
require_once ('/var/www/html/Confidential.php');
$servername = Confidential::$servername;
$username = Confidential::$username;
$pwd = Confidential::$pwd;
$link = mysqli_connect("$servername","$username","$pwd","huiji");
mysqli_query($link, "SET NAMES UTF8");
mysqli_select_db($link, "huiji");
$sql = "select domain_prefix from domain";
$query = mysqli_query($link, $sql);
while ($res = mysqli_fetch_assoc( $query )) {
	$arr[] = $res;
}
foreach($arr as $val){
	$conf = '/var/www/virtual/'.$val['domain_prefix'].'/LocalSettings.php';
	$command = 'hhvm /var/www/src/maintenance/runJobs.php --memory-limit=max --conf='.$conf;
	$lowDashPrefix = mysqli_real_escape_string($link, str_replace('.', '_', $val['domain_prefix']));
	if ($val['domain_prefix'] != 'www'){
		mysqli_select_db($link, "huiji_sites");
		$sql1 = "UPDATE ".$lowDashPrefix."job SET  `job_token` =  '' WHERE `job_attempts` > 0 and `job_attempts` < 3";
		$sql2 =	"UPDATE ".$lowDashPrefix."job SET  `job_token_timestamp` =  NULL WHERE `job_attempts` > 0 and `job_attempts` < 3";

	} else {
		mysqli_select_db($link, "huiji_home");
		$sql1 = "UPDATE job SET `job_token` =  '' WHERE `job_attempts` > 0 and `job_attempts` < 3";
		$sql2 = "UPDATE job SET `job_token_timestamp` =  NULL WHERE `job_attempts` > 0 and `job_attempts` < 3";		
	}
	//echo $sql1 . $sql2;
	$query = mysqli_query($link, $sql1);
	$query .= mysqli_query($link, $sql2);
	echo $command;
	$flock = "flock -n /tmp/".$val['domain_prefix'].".lock -c '".$command."'"; 
	echo $flock;
	echo exec($flock);

}
