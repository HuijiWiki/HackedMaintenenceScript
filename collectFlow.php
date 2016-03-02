<?php

require_once __DIR__ . "/Maintenance.php";

class CollectFollow extends Maintenance {
	public function execute() {
		global $isProduction;
		$allSite = HuijiPrefix::getAllPrefixes(true);
		foreach ($allSite as $key => $prefix) {
			$prefix_huiji = 'huiji';
			if ( !is_null($prefix) ) {
			    if( $isProduction == true &&( $prefix == 'www' || $prefix == 'home') ){
			        $prefix = 'huiji_home';
			    }elseif ( $isProduction == true ) {
			        $prefix = 'huiji_sites-'.str_replace('.', '_', $prefix);
			    }else{
			        $prefix = 'huiji_'.str_replace('.', '_', $prefix);
			    }
			}else{
			    die( "error: empty $prefix;function:getAllUploadFileCount.\n" );
			}
			$dbr = wfGetDB( DB_SLAVE,$groups = array(),$wiki = $prefix );
			$dbr_huiji = wfGetDB( DB_SLAVE,$groups = array(),$wiki = $prefix_huiji );
			//1   flow_ext_ref
			$res1 = $dbr->select(
				'flow_ext_ref',
				array('*'),
				array(),
				__METHOD__
			);
			if ($res1) {
				foreach ($res1 as $value) {
					$dbr_huiji->insert(
						'flow_ext_ref',
						array(
							'ref_src_object_id'=>$value->ref_src_object_id,
							'ref_src_object_type'=>$value->ref_src_object_type,
							'ref_src_workflow_id'=>$value->ref_src_workflow_id,
							'ref_src_namespace'=>$value->ref_src_namespace,
							'ref_src_title'=>$value->ref_src_title,
							'ref_target'=>$value->ref_target,
							'ref_type'=>$value->ref_type,
							'ref_src_wiki'=>$value->ref_src_wiki,
						),
						__METHOD__
					);
				}
			}

			//2  flow_revision
			$res2 = $dbr->select(
				'flow_revision',
				array('*'),
				array(),
				__METHOD__
			);
			if ($res2) {
				foreach ($res2 as $value) {
					$dbr_huiji->insert(
						'flow_revision',
						array(
							'rev_id'=>$value->rev_id,
							'rev_type'=>$value->rev_type,
							'rev_type_id'=>$value->rev_type_id,
							'rev_user_id'=>$value->rev_user_id,
							'rev_user_ip'=>$value->rev_user_ip,
							'rev_user_wiki'=>$value->rev_user_wiki,
							'rev_parent_id'=>$value->rev_parent_id,
							'rev_flags'=>$value->rev_flags,
							'rev_content'=>$value->rev_content,
							'rev_change_type'=>$value->rev_change_type,
							'rev_mod_state'=>$value->rev_mod_state,
							'rev_mod_user_id'=>$value->rev_mod_user_id,
							'rev_mod_user_ip'=>$value->rev_mod_user_ip,
							'rev_mod_user_wiki'=>$value->rev_mod_user_wiki,
							'rev_mod_timestamp'=>$value->rev_mod_timestamp,
							'rev_mod_reason'=>$value->rev_mod_reason,
							'rev_last_edit_id'=>$value->rev_last_edit_id,
							'rev_edit_user_id'=>$value->rev_edit_user_id,
							'rev_edit_user_ip'=>$value->rev_edit_user_ip,
							'rev_edit_user_wiki'=>$value->rev_edit_user_wiki,
							'rev_content_length'=>$value->rev_content_length,
							'rev_previous_content_length'=>$value->rev_previous_content_length,
						),
						__METHOD__
					);
				}
			}

			//3  flow_subscription
			$res3 = $dbr->select(
				'flow_subscription',
				array('*'),
				array(),
				__METHOD__
			);
			if ($res3) {
				foreach ($res3 as $value) {
					$dbr_huiji->insert(
						'flow_revision',
						array(
							'subscription_workflow_id'=>$value->subscription_workflow_id,
							'subscription_user_id'=>$value->subscription_user_id,
							'subscription_user_wiki'=>$value->subscription_user_wiki,
							'subscription_create_timestamp'=>$value->subscription_create_timestamp,
							'subscription_last_updated'=>$value->subscription_last_updated,
						),
						__METHOD__
					);
				}
			}

			//4  flow_topic_list
			$res4 = $dbr->select(
				'flow_topic_list',
				array('*'),
				array(),
				__METHOD__
			);
			if ($res4) {
				foreach ($res4 as $value) {
					$dbr_huiji->insert(
						'flow_topic_list',
						array(
							'topic_list_id'=>$value->topic_list_id,
							'topic_id'=>$value->topic_id,
						),
						__METHOD__
					);
				}
			}

			//5  flow_tree_node
			$res5 = $dbr->select(
				'flow_tree_node',
				array('*'),
				array(),
				__METHOD__
			);
			if ($res5) {
				foreach ($res5 as $value) {
					$dbr_huiji->insert(
						'flow_tree_node',
						array(
							'tree_ancestor_id'=>$value->tree_ancestor_id,
							'tree_descendant_id'=>$value->tree_descendant_id,
							'tree_depth'=>$value->tree_depth,
						),
						__METHOD__
					);
				}
			}

			//6  flow_tree_revision
			$res6 = $dbr->select(
				'flow_tree_revision',
				array('*'),
				array(),
				__METHOD__
			);
			if ($res6) {
				foreach ($res6 as $value) {
					$dbr_huiji->insert(
						'flow_tree_revision',
						array(
							'tree_rev_descendant_id'=>$value->tree_rev_descendant_id,
							'tree_rev_id'=>$value->tree_rev_id,
							'tree_orig_user_id'=>$value->tree_orig_user_id,
							'tree_orig_user_ip'=>$value->tree_orig_user_ip,
							'tree_orig_user_wiki'=>$value->tree_orig_user_wiki,
							'tree_parent_id'=>$value->tree_parent_id,
						),
						__METHOD__
					);
				}
			}

			//7  flow_wiki_ref
			$res7 = $dbr->select(
				'flow_wiki_ref',
				array('*'),
				array(),
				__METHOD__
			);
			if ($res7) {
				foreach ($res7 as $value) {
					$dbr_huiji->insert(
						'flow_wiki_ref',
						array(
							'ref_src_object_id'=>$value->ref_src_object_id,
							'ref_src_object_type'=>$value->ref_src_object_type,
							'ref_src_workflow_id'=>$value->ref_src_workflow_id,
							'ref_src_namespace'=>$value->ref_src_namespace,
							'ref_src_title'=>$value->ref_src_title,
							'ref_target_namespace'=>$value->ref_target_namespace,
							'ref_target_title'=>$value->ref_target_title,
							'ref_type'=>$value->ref_type,
							'ref_target_namespace'=>$value->ref_src_wiki,
						),
						__METHOD__
					);
				}
			}
			
			//8  flow_workflow
			$res8 = $dbr->select(
				'flow_workflow',
				array('*'),
				array(),
				__METHOD__
			);
			if ($res8) {
				foreach ($res8 as $value) {
					$dbr_huiji->insert(
						'flow_workflow',
						array(
							'workflow_id'=>$value->workflow_id,
							'workflow_wiki'=>$value->workflow_wiki,
							'workflow_namespace'=>$value->workflow_namespace,
							'workflow_page_id'=>$value->workflow_page_id,
							'workflow_title_text'=>$value->workflow_title_text,
							'workflow_name'=>$value->workflow_name,
							'workflow_last_update_timestamp'=>$value->workflow_last_update_timestamp,
							'workflow_lock_state'=>$value->workflow_lock_state,
							'workflow_type'=>$value->workflow_type,
						),
						__METHOD__
					);
				}
			}
		}
	}
}

$maintClass = 'CollectFollow';

require_once RUN_MAINTENANCE_IF_MAIN;
