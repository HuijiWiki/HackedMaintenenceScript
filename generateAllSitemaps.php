re_once ('/var/www/html/Confidential.php');
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
	$command = 'php /var/www/src/maintenance/runJobs.php --conf='.$conf;
	$lowDashPrefix = mysqli_real_escape_string($link, str_replace('.', '_', $val['domain_prefix']));
	$command2 = 'php /var/www/src/maintenance/generateSitemap.php --conf='.$conf.' --fspath=/var/www/virtual/'.$val['domain_prefix'].'/sitemap --urlpath=http://'.$val['domain_prefix'].'.huiji.wiki/sitemap/ --server=http://'.$val['domain_prefix'].'.huiji.wiki/';
	echo $command2;
  	exec($command2);
}
