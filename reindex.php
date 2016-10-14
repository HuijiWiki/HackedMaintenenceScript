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
putenv("DISABLE_SEARCH_UPDATE = true");
foreach($arr as $val){
	$conf = '/var/www/virtual/'.$val['domain_prefix'].'/LocalSettings.php';
	$command3 = "php ../extensions/CirrusSearch/maintenance/updateSearchIndexConfig.php --conf=".$conf;
	exec($command3);
}
	
putenv("DISABLE_SEARCH_UPDATE = false");
foreach($arr as $val){
	$conf = '/var/www/virtual/'.$val['domain_prefix'].'/LocalSettings.php';
	$command4 = "php ../extensions/CirrusSearch/maintenance/forceSearchIndex.php --skipLinks --indexOnSkip --conf=".$conf.' &';
	exec($command4);
	$command5 = "php ../extensions/CirrusSearch/maintenance/forceSearchIndex.php --skipParse --conf=".$conf.' &'; 
	exec($command5);
}
