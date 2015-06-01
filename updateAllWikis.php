<?php
require_once ('/var/www/html/Confidential.php');
$servername = Confidential::$servername;
$username = Confidential::$username;
$pwd = Confidential::$pwd;
$link=mysql_connect("$servername","$username","$pwd");
mysql_query("SET NAMES UTF8");
mysql_select_db("huiji",$link);
$sql = "select domain_prefix from domain";
$query = mysql_query($sql);
while ($res = mysql_fetch_assoc( $query )) {
	$arr[] = $res;
}
foreach($arr as $val){
	$command = 'php ./update.php --conf=/var/www/virtual/'.$val['domain_prefix'].'/LocalSettings.php --quick --doshared';
	exec($command);
}
