<?php

require_once __DIR__ . "/Maintenance.php";

class InsertUserTitle extends Maintenance {
	public function execute() {
		$dbw = wfGetDB( DB_MASTER );
		// system_gift
		$res = $dbw->select(
		        'system_gift',
		        array(
		                'gift_id', 'designation'
		            ),
		        array(),
		        __METHOD__
		        );
		if ($res) {
		    foreach ($res as $key => $value) {
		        $systemResult[$value->gift_id] = $value->designation;
		    }
		}
		//gift
		$resGift = $dbw->select(
		        'gift',
		        array(
		                'gift_id', 'designation'
		            ),
		        array(),
		        __METHOD__
		        );
		if ($resGift) {
		    foreach ($resGift as $key => $value) {
		        $giftResult[$value->gift_id] = $value->designation;
		    }
		}
		$res2 = $dbw->select(
		        'user_system_gift',
		        array(
		            'sg_gift_id', 'sg_user_id'
		            ),
		        array(),
		        __METHOD__
		    );
		if ($res2) {
		    foreach ($res2 as $key => $value) {
		        $dbw->insert(
		                'user_title',
		                array(
		                    'gift_id' => $value->sg_gift_id,
		                    'title_content' => $systemResult[$value->sg_gift_id],
		                    'user_to_id' => $value->sg_user_id,
		                    'is_open' => 1,
		                    'title_from' => 'system_gift'
		                ),
		                __METHOD__
		            );

		    }
		}

		// add user_gift
		$res3 = $dbw->select(
		        'user_gift',
		        array(
		            'ug_gift_id', 'ug_user_id_to'
		            ),
		        array(),
		        __METHOD__
		    );
		if ($res3) {
		    foreach ($res3 as $key => $value) {
		        $dbw->insert(
		                'user_title',
		                array(
		                    'gift_id' => $value->ug_gift_id,
		                    'title_content' => $giftResult[$value->ug_gift_id],
		                    'user_to_id' => $value->ug_user_id_to,
		                    'is_open' => 1,
		                    'title_from' => 'gift'
		                ),
		                __METHOD__
		            );

		    }
		}
	}
}

$maintClass = 'InsertUserTitle';

require_once RUN_MAINTENANCE_IF_MAIN;
