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
shuffle($arr);
foreach($arr as $val){
	$conf = '/var/www/virtual/'.$val['domain_prefix'].'/LocalSettings.php';
	$command = 'php /var/www/src/maintenance/runJobs.php --procs=5 --maxtime=180 --conf='.$conf;
	/*$lowDashPrefix = mysqli_real_escape_string($link, str_replace('.', '_', $val['domain_prefix']));
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
	$query .= mysqli_query($link, $sql2);*/
	echo $command;
	exec($command);
	//$flock = "flock -n /tmp/".$val['domain_prefix'].".lock -c '".$command."'"; 
	//echo $flock;
	//echo exec($flock);

}
function PsExecute($command, $timeout = 60, $sleep = 2) { 
    // First, execute the process, get the process ID 

    $pid = PsExec($command); 

    if( $pid === false ) 
        return false; 

    $cur = 0; 
    // Second, loop for $timeout seconds checking if process is running 
    while( $cur < $timeout ) { 
        sleep($sleep); 
        $cur += $sleep; 
        // If process is no longer running, return true; 

       echo "\n ---- $cur ------ \n"; 

        if( !PsExists($pid) ) 
            return true; // Process must have exited, success! 
    } 

    // If process is still running after timeout, kill the process and return false 
    PsKill($pid); 
    return false; 
} 

function PsExec($commandJob) { 

    $command = $commandJob.' > /dev/null 2>&1 & echo $!'; 
    exec($command ,$op); 
    $pid = (int)$op[0]; 

    if($pid!="") return $pid; 

    return false; 
} 

function PsExists($pid) { 

    exec("ps ax | grep $pid 2>&1", $output); 

    while( list(,$row) = each($output) ) { 

            $row_array = explode(" ", $row); 
            $check_pid = $row_array[0]; 

            if($pid == $check_pid) { 
                    return true; 
            } 

    } 

    return false; 
} 

function PsKill($pid) { 
    exec("kill -9 $pid", $output); 
} 
