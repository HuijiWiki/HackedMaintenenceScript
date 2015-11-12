<?php

require_once __DIR__ . "/Maintenance.php";

class InsertSiteRank extends Maintenance {
	public function execute() {
		$allSite = HuijiPrefix::getAllPrefix();
		$today = date('Y-m-d');
		$yesterday = date('Y-m-d',strtotime('-1 days'));
		$lastWeek = date('Y-m-d',strtotime('-8 days'));
		$lastMonth = date('Y-m-d',strtotime('-31 days'));
		$ueb = new UserEditBox();
		$editUserYesterday = $ueb->getSiteEditUserCount( $yesterday, $yesterday);
		$editUserWeek = $ueb->getSiteEditUserCount( $lastWeek, $yesterday);
		$editUserMonth = $ueb->getSiteEditUserCount( $lastMonth, $yesterday);
		$viewDate = array();
		$editDate = array();
		$editUserDate = array();
		foreach ($allSite as $value) {
			$viewResult['yesterday'] = $ueb->getSiteViewCount( '', $value, $yesterday, $yesterday );
			$viewResult['week'] = $ueb->getSiteViewCount( '', $value, $lastWeek, $yesterday );
			$viewResult['month'] = $ueb->getSiteViewCount( '', $value, $lastMonth, $yesterday );
			$editResult['yesterday'] = $ueb->getSiteEditCount( '', $value, $yesterday, $yesterday );
			$editResult['week'] = $ueb->getSiteEditCount( '', $value, $lastWeek, $yesterday );
			$editResult['month'] = $ueb->getSiteEditCount( '', $value, $lastMonth, $yesterday );
			$viewDate[$value] = round($viewResult['yesterday']+$viewResult['week']/3+$viewResult['month']/10);
			$editDate[$value] = round($editResult['yesterday']+$editResult['week']/3+$editResult['month']/10);
			$editUserDate[$value] = round(isset($editUserYesterday[$value])?$editUserYesterday[$value]:0+(isset($editUserWeek[$value])?$editUserWeek[$value]:0)*2+(isset($editUserMonth[$value])?$editUserMonth[$value]:0)*3);
			echo $value.' : '.$viewResult['yesterday'].' / '.$viewResult['week'].' / '.$viewResult['month'].' : '.$editResult['yesterday'].' / '.$editResult['week'].' / '.$editResult['month'].' : '.$editUserYesterday[$value].' / '.$editUserWeek[$value].' / '.$editUserMonth[$value];
		}
	}
}

$maintClass = 'InsertSiteRank';
require_once RUN_MAINTENANCE_IF_MAIN;
		
