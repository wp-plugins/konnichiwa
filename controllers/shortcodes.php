<?php
class KonnichiwaShortcodes {
	// generates subscribe button and handles the whole subscription process for selected plan
	static function subscribe($atts) {
		global $wpdb, $user_ID, $post;
		$plan_id = intval(@$atts[0]);		
		ob_start();
		$content = '';	
		
		// select the plan		
		$plan = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".KONN_PLANS." WHERE id=%d", $plan_id));
		
		// check if already subscribed
		$sub = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".KONN_SUBS." WHERE 
			plan_id=%d AND user_id=%d AND status=1", $plan_id, $user_ID));
		
		$no_form = false; // whether to show the form 	
		if(!empty($sub->id) and strtotime($sub->expires) >= time()) {
			$content = "<p>".__('Currently subscribed!', 'konnichiwa')."</p>";
			$no_form = true;
		}			
		
		if(!empty($sub->id) and strtotime($sub->expires) < time()) {
			$content = "<p>".__('Subscription expired!', 'konnichiwa')."</p>";
			
			// can't re-subscribe for free plans
			if($plan->price <= 0) $no_form = true;		
		}		
		
		// generate the button
		if(!$no_form) include(KONN_PATH."/views/subscribe-form.html.php");
		$content .= ob_get_clean();
		return $content;
	} // end subscribe
	
	// generates the table with plans and subscription buttons
	static function plans($atts) {
		global $wpdb;
		$orientation = empty($atts[0]) ? 'vertical' : $atts[0];
		if(!in_array($orientation, array('vertical', 'horizontal'))) $orientation = 'vertical';
		
		// select plans
		$plans = $wpdb->get_results("SELECT * FROM ".KONN_PLANS." ORDER BY name");
		
		ob_start();
		include(KONN_PATH."/views/plans-table-$orientation.html.php");
		$content = ob_get_clean();
		$content = do_shortcode($content);
		return $content;
	}
	
	// protects a piece of content
	static function protect($atts, $content = null) {
		global $wpdb, $user_ID;
		
		if(!is_user_logged_in()) return __('This content is available only for registered users.', 'konnichiwa');
		if(strstr(@$atts['plans'], ",")) $plans = explode(",", @$atts['plans']);
		else $plans = array($atts['plans']);
		
		if(empty($plans)) return __('Protected content', 'konnichiwa');
		
		// get active user plans
		$subs = $wpdb->get_results($wpdb->prepare("SELECT plan_id FROM ".KONN_SUBS."
					WHERE user_id=%d AND expires >= CURDATE() AND status=1", $user_ID));
					
		foreach($subs as $sub) {
			// if even one is found we're all ok to return the content
			if(in_array($sub->plan_id, $plans)) return do_shortcode($content);
		}			
		
		// no plans found? return restricted text
		$plans = $wpdb->get_results("SELECT name FROM ".KONN_PLANS." WHERE id IN (".implode(",", $plans).")");
		$plan_names = array();
		foreach($plans as $plan) $plan_names[] = $plan->name;
		
		return sprintf(__('This content is available only for users with the following subscription plans: <b>%s</b>', 'konnichiwa'), 
			implode(", ", $plan_names)); 
	} // end protect()
}