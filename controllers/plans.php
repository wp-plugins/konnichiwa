<?php
// subscription plans
class KonnichiwaPlans {
	static function manage() {
 		$action = empty($_GET['action']) ? 'list' : $_GET['action']; 
		switch($action) {
			case 'add':
				self :: add_plan();
			break;
			case 'edit': 
				self :: edit_plan();
			break;
			case 'list':
			default:
				self :: list_plans();	 
			break;
		}
	} // end manage()
	
	static function add_plan() {
		global $wpdb;
		$_plan = new KonnichiwaPlan();
		
		if(!empty($_POST['ok'])) {
			try {
				$pid = $_plan->add($_POST);			
				konnichiwa_redirect("admin.php?page=konnichiwa_plans");
			}
			catch(Exception $e) {
				$error = $e->getMessage();
			}
		}
		
		include(KONN_PATH.'/views/plan.html.php');
	} // end add_question
	
	static function edit_plan() {
		global $wpdb;
		$_plan = new KonnichiwaPlan();
		
		if(!empty($_POST['ok'])) {
			try {
				$_plan->save($_POST, $_GET['id']);			
				konnichiwa_redirect("admin.php?page=konnichiwa_plans");
			}
			catch(Exception $e) {
				$error = $e->getMessage();
			}
		}
		
		// select this plan
		$plan = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".KONN_PLANS." WHERE id=%d", $_GET['id']));
		include(KONN_PATH.'/views/plan.html.php');
	} // end edit_plan
	
	// list and delete questions
	static function list_plans() {
		global $wpdb;
		$_plan = new KonnichiwaPlan();
		
		if(!empty($_GET['del'])) {
			$_plan->delete($_GET['id']);			
		}
		
		$plans = $wpdb->get_results("SELECT * FROM ".KONN_PLANS." ORDER BY id");
		include(KONN_PATH."/views/plans.html.php");
	} // end list_plans	
}