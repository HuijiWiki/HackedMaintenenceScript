<?php
require_once ("../Maintenance.php");
class AddLessFile extends Maintenance {
	public function __construct(){
		parent::__construct();
	}
	public function execute() {
		$path = '/var/www/virtual';
        $allPrefix = Huiji::getInstance()->getSitePrefixes(true);
        foreach ($allPrefix as $value) {
        	$filePath = $path.'/'.$value.'/style/';
        	if(!is_dir($filePath)){
			    	mkdir($filePath);
			    	chmod($filePath, 0777);
			    	chmod($filePath.'SiteColor.less', 0777);
			    }
			file_put_contents($filePath.'SiteColor.less', ''); 
        }
	}
}
$maintClass = 'AddLessFile';
require_once RUN_MAINTENANCE_IF_MAIN;
