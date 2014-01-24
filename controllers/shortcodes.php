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
}