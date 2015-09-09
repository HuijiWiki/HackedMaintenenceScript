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
	$command = 'php /var/www/src/maintenance/runJobs.php --conf='.$conf;
	if ($val['domain_prefix'] != 'www'){
		mysql_select_db("huiji_sites",$link);
		$sql = "UPDATE {$val['domain_prefix']}job SET  `job_token` =  '';
			UPDATE {$val['domain_prefix']}job SET  `job_token_timestamp` =  '';";

	} else {
		mysql_select_db("huiji_home",$link);
		$sql = "UPDATE job SET `job_token` =  '';
			UPDATE job SET  `job_token_timestamp` =  '';";		
	}

	$query = mysql_query($sql);
	echo $command;
	exec($command);

	$command2 = 'php /var/www/src/maintenance/generateSitemap.php --conf='.$conf.' --fspath=/var/www/virtual/'.$val['domain_prefix'].'/sitemap --urlpath=http://'.$val['domain_prefix'].'.huiji.wiki/sitemap/ --server=http://'.$val['domain_prefix'].'.huiji.wiki/';
	echo $command2;
	exec($command2);

}	
