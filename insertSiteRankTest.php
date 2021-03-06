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
			$viewDate[$value] = $viewResult['yesterday']*3+$viewResult['week']*2+$viewResult['month']*1;
			$editDate[$value] = $editResult['yesterday']*3+$editResult['week']*2+$editResult['month']*1;
			$eud = isset($editUserYesterday[$value])?$editUserYesterday[$value]:0;
			$euw = isset($editUserWeek[$value])?$editUserWeek[$value]:0;
			$eum = isset($editUserMonth[$value])?$editUserMonth[$value]:0;
			$editUserDate[$value] = $eud * 3 + $euw * 2 + $eum * 1;
		}
                //sort arr
		asort($viewDate);
		asort($editDate);
		asort($editUserDate);
		$i=1;
		//loop score
		$viewRes = array();
		$editRes = array();
		$editUserRes = array();
		foreach ($viewDate as $key => $value) {
			$viewRes[$key] = $i*10;
			$i++;
		}
		$j=1;
		foreach ($editDate as $key => $value) {
			$editRes[$key] = $j*10;
			$j++;
		}
		$k=1;
		foreach ($editUserDate as $key => $value) {
			$editUserRes[$key] = $k*10;
			$k++;
		}
		//highest score
		$highest = ($k-1)*100;
		//total weight must add up to 10
		$allRank = array();
		foreach ($viewRes as $key => $value) {
			$allRank[$key] = $value*3.3 + $editRes[$key]*3.0 +$editUserRes[$key]*3.7;
		}
		arsort($allRank);
                print_r($allRank);

	}
}

$maintClass = 'InsertSiteRank';
require_once RUN_MAINTENANCE_IF_MAIN;
		
