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
	$id = str_replace('-', '*', $val['domain_prefix']).'_';
  	$command = 'php /var/www/src/maintenance/rebuildLocalisationCache.php --lang=en,zh,zh-cn,zh-hans,zh-hant --wiki='.$id;
  	echo exec($command);
}
