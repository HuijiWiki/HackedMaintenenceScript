<?php
require_once ('/var/www/html/Confidential.php');
$servername = Confidential::$servername;
$username = Confidential::$username;
$pwd = Confidential::$pwd;
$link=mysqli_connect("$servername","$username","$pwd");
mysqli_query($link, "SET NAMES UTF8");
mysqli_select_db($link, "huiji");
$sql = "select domain_prefix from domain";
$query = mysqli_query($link, $sql);
while ($res = mysqli_fetch_assoc($query )) {
	$arr[] = $res;
}

#putenv("DISABLE_SEARCH_UPDATE = true");
foreach($arr as $val){
	$id = str_replace('-', '*', $val['domain_prefix']).'_';
	if ($val['domain_prefix'] == "www"){
		$command = 'php /var/www/src/maintenance/update.php --wiki='.$id.' --quick --doshared';
	} else {
		$command = 'php /var/www/src/maintenance/update.php --wiki='.$id.' --quick';
	}
	exec($command);
}