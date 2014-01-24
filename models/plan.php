<?php
class KonnichiwaPlan {
	function add($vars) {
		global $wpdb;
		
		$result = $wpdb->query($wpdb->prepare("INSERT INTO ".KONN_PLANS." SET
			name=%s, description=%s, price=%s, duration=%d, duration_unit=%s", 
			$vars['name'], $vars['description'], $vars['price'], $vars['duration'], $vars['duration_unit']));
			
		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return $wpdb->insert_id;	
	} // end add
	
	function save($vars, $id) {
		global $wpdb;
		
		$result = $wpdb->query($wpdb->prepare("UPDATE ".KONN_PLANS." SET
			name=%s, description=%s, price=%s, duration=%d, duration_unit=%s WHERE id=%d", 
			$vars['name'], $vars['description'], $vars['price'], $vars['duration'], $vars['duration_unit'], $id));
			
		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return true;	
	}
	
	function delete($id) {
		global $wpdb;
		
		// delete subscriptions
		$wpdb->query($wpdb->prepare("DELETE FROM ".KONN_SUBS." WHERE plan_id=%d", $id));
		
		// delete usage
		$wpdb->query($wpdb->prepare("DELETE FROM ".KONN_USAGE." WHERE plan_id=%d", $id));
		
		// delete plan
		$wpdb->query($wpdb->prepare("DELETE FROM ".KONN_PLANS." WHERE id=%d", $id));
	}
}