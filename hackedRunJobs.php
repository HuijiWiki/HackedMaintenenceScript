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
	$conf = '/var/www/virtual/'.$val['domain_prefix'].'/LocalSettings.php';
	$command = 'php ./runJobs.php --conf='.$conf;
	exec($command);
	$command2 = 'php ./generateSitemap.php --conf='.$conf.' --fspath=/var/www/virtual/'.$val['domain_prefix'].' --urlpath=http://'.$val['domain_prefix'].'.huiji.wiki/sitemap/ --server=http://'.$val['domain_prefix'].'.huiji.wiki/';
	exec($command2);
}	
