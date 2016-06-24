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
  	$command3 = 'php /var/www/src/maintenance/rebuildLocalisationCache.php --lang=en,zh,zh-cn,zh-hans,zh-hant --conf='.$conf;
  	$flock = "flock -n /tmp/rebuildLocalisationCacheOn".$val['domain_prefix'].".lock -c '".$command3."'"; 
	echo $flock;
	echo exec($flock);
}
