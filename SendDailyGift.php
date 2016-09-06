<?php
require_once __DIR__ . "/Maintenance.php";
class SendDailyGift extends Maintenance {
	public function __construct(){
		parent::__construct();
		$this->addOption( 'monthly', 'if true,send monthly gift, else send weekly' );
	}
	public function execute() {
		global $wgContLang, $wgMemc;
		if ( $this->hasOption('monthly') ) {
			$period = 'monthly';
		}else{
			$period = 'weekly';
		}
		$user_count = 10;
		$dbw = wfGetDB( DB_MASTER );
		$res = $dbw->select(
			"user_points_{$period}",
			array( 'up_user_id', 'up_user_name', 'up_points' ),
			array(),
			__METHOD__,
			array( 'ORDER BY' => 'up_points DESC', 'LIMIT' => $user_count )
		);

		$last_rank = 0;
		$last_total = 0;
		$x = 1;

		$users = array();

		if ( $dbw->numRows( $res ) <= 0 ) {
			// For the initial run, everybody's a winner!
			// Yes, I know that this isn't ideal and I'm sorry about that.
			// The original code just wouldn't work if the first query
			// (the $res above) returned nothing so I had to work around that
			// limitation.
			$res = $dbw->select(
				'user_stats',
				array( 'stats_user_id', 'stats_user_name', 'stats_total_points' ),
				array(),
				__METHOD__,
				array(
					'ORDER BY' => 'stats_total_points DESC',
					'LIMIT' => $user_count
				)
			);
			foreach ( $res as $row ) {
				if ( $row->stats_total_points == $last_total ) {
					$rank = $last_rank;
				} else {
					$rank = $x;
				}
				$last_rank = $x;
				$last_total = $row->stats_total_points;
				$x++;
				$userObj = User::newFromId( $row->stats_user_id );
                $user_group = $userObj->getEffectiveGroups();
				if ( !in_array('bot', $user_group) && !in_array('bot-global',$user_group)  ) {
					$users[] = array(
						'user_id' => $row->stats_user_id,
						'user_name' => $row->stats_user_name,
						'points' => $row->stats_total_points,
						'rank' => $rank
					);
				}
			}
		} else {
			foreach ( $res as $row ) {
				if ( $row->up_points == $last_total ) {
					$rank = $last_rank;
				} else {
					$rank = $x;
				}
				$last_rank = $x;
				$last_total = $row->up_points;
				$x++;
				$userObj = User::newFromId( $row->up_user_id );
                		$user_group = $userObj->getEffectiveGroups();
				if ( !in_array('bot', $user_group) && !in_array('bot-global',$user_group)  ) {
					$users[] = array(
						'user_id' => $row->up_user_id,
						'user_name' => $row->up_user_name,
						'points' => $row->up_points,
						'rank' => $rank
					);
				}
			}
		}
		$winner_count = 0;
		$winners = '';

		if ( !empty( $users ) ) {
			$localizedUserNS = $wgContLang->getNsText( NS_USER );
			foreach ( $users as $user ) {
				if ( $user['rank'] == 1 ) {
					// Mark the user ranked #1 as the "winner" for the given
					// period
					if( $period == 'weekly' ){
						$systemGiftID = 9;
					}elseif ( $period == 'monthly' ) {
						$systemGiftID = 10;
					}
					$sg = new UserSystemGifts( $user['user_name'] );
					$sg->sendSystemGift( $systemGiftID );
					$stats = new UserStatsTrack( $user['user_id'], $user['user_name'] );
					$stats->incStatField( "points_winner_{$period}" );
					if ( $winners ) {
						$winners .= ', ';
					}
					$winners .= "[[{$localizedUserNS}:{$user['user_name']}|{$user['user_name']}]]";
					$winner_count++;
				}elseif ( $user['rank'] == 2 || $user['rank'] == 3 ) {
					if( $period == 'weekly' ){
						$systemGiftID = 13;
					}elseif ( $period == 'monthly' ) {
						$systemGiftID = 15;
					}
					$sg = new UserSystemGifts( $user['user_name'] );
					$sg->sendSystemGift( $systemGiftID );
				}else{
					if( $period == 'weekly' ){
						$systemGiftID = 14;
					}elseif ( $period == 'monthly' ) {
						$systemGiftID = 16;
					}
					$sg = new UserSystemGifts( $user['user_name'] );
					$sg->sendSystemGift( $systemGiftID );
				}
			}
			$date = date( 'Y-m-d H:i:s' );
			// Archive points from the weekly/monthly table into the archive
			// table
			$dbw->insertSelect(
				'user_points_archive',
				"user_points_{$period}",
				array(
					'up_user_name' => 'up_user_name',
					'up_user_id' => 'up_user_id',
					'up_points' => 'up_points',
					'up_period' => ( ( $period == 'weekly' ) ? 1 : 2 ),
					'up_date' => $dbw->addQuotes( $date )
				),
				'*',
				__METHOD__
			);

			// Clear the current point table to make way for the next period
			$res = $dbw->delete( "user_points_{$period}", '*', __METHOD__ );
			$key = wfGlobalCacheKey('UserStats', 'getUserRank', '10', 'week');
			$key2 = wfGlobalCacheKey('UserStats', 'getUserRank', '10', 'month');
			$wgMemc->delete($key);
			$wgMemc->delete($key2);
		}
	}
}
$maintClass = 'SendDailyGift';
require_once RUN_MAINTENANCE_IF_MAIN;
