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

#putenv("DISABLE_SEARCH_UPDATE = true");
foreach($arr as $val){
	$conf = '/var/www/virtual/'.$val['domain_prefix'].'/LocalSettings.php';
	$command = 'php ./update.php --conf='.$conf.' --quick --doshared';
	exec($command);
	$command2 = 'ln -s /var/www/src/* /var/www/virtual/'.$val['domain_prefix'].'/';
	exec($command2);
	#$command3 = "php ../extensions/CirrusSearch/maintenance/updateSearchIndexConfig.php --conf=".$conf;
	#exec($command3);
	#$command4 = 'php ./namespaceDupes.php --conf='.$conf.' --fix';
	#exec($command4);
}
/*	
putenv("DISABLE_SEARCH_UPDATE = false");


foreach($arr as $val){
	$conf = '/var/www/virtual/'.$val['domain_prefix'].'/LocalSettings.php';

	$command4 = "php ../extensions/CirrusSearch/maintenance/forceSearchIndex.php --skipLinks --indexOnSkip --conf=".$conf.' &';
	exec($command4);

	$command5 = "php ../extensions/CirrusSearch/maintenance/forceSearchIndex.php --skipParse --conf=".$conf.' &'; 
	exec($command5);
}*/
