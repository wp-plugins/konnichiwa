<?php
// main model containing general config and UI functions
class Konnichiwa {
   static function install() {
   	global $wpdb;	
   	$wpdb -> show_errors();
   	
   	self::init();
	  
	   // subscription plans
   	if($wpdb->get_var("SHOW TABLES LIKE '".KONN_PLANS."'") != KONN_PLANS) {        
			$sql = "CREATE TABLE `" . KONN_PLANS . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `name` VARCHAR(255) NOT NULL DEFAULT '',
				  `description` TEXT,
				  `price` DECIMAL(8,2) NOT NULL DEFAULT '0.00',
				  `duration` INT UNSIGNED NOT NULL DEFAULT 0,
				  `duration_unit` VARCHAR(100) NOT NULL DEFAULT 'day'				  
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  }
	  
	  // user subscriptions
     if($wpdb->get_var("SHOW TABLES LIKE '".KONN_SUBS."'") != KONN_SUBS) {        
			$sql = "CREATE TABLE `" . KONN_SUBS . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `plan_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `date` DATE,
				  `expires` DATE,
				  `status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
				  `amt_paid` DECIMAL(10,2) NOT NULL DEFAULT '0.00'				 				  
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  }
	  
	  // usage (visits) per user, plan and day
     if($wpdb->get_var("SHOW TABLES LIKE '".KONN_USAGE."'") != KONN_USAGE) {        
			$sql = "CREATE TABLE `" . KONN_USAGE . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `plan_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `date` DATE,
				  `pageviews` INT UNSIGNED NOT NULL DEFAULT 0		  
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  }		 
	  
	  // protected content settings 
     if($wpdb->get_var("SHOW TABLES LIKE '".KONN_CONTENT."'") != KONN_CONTENT) {        
			$sql = "CREATE TABLE `" . KONN_CONTENT . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `content_type` VARCHAR(100) NOT NULL DEFAULT 'post',
				  `content_category` INT UNSIGNED NOT NULL DEFAULT 0, /* further specify by Wordpress category */
				  `protection_type` VARCHAR(100) NOT NULL DEFAULT 'none' /* none, registered, plans (list of subscription plans) */		  
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  } 
	  
	  // payments made
     if($wpdb->get_var("SHOW TABLES LIKE '".KONN_PAYMENTS."'") != KONN_PAYMENTS) {        
			$sql = "CREATE TABLE `" . KONN_PAYMENTS . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `plan_id` INT UNSIGNED NOT NULL DEFAULT 0,
				  `sub_id` INT UNSIGNED NOT NULL DEFAULT 0, /* subscription ID */
				  `date` DATE,
				  `status` VARCHAR(100) NOT NULL DEFAULT 'pending',
				  `method` VARCHAR(100) NOT NULL DEFAULT 'paypal',
				  `payment_key` VARCHAR(100) NOT NULL DEFAULT '', /* paypal txn_id etc */ 	
				  `amount` DECIMAL(10,2) NOT NULL DEFAULT '0.00'				 			  		  
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  } 
	  
	  // exit;
   }
   
   // main menu
   static function menu() {
   	add_menu_page(__('Konnichiwa!', 'chained'), __('Konnichiwa', 'chained'), "manage_options", "konnichiwa", 
   		array('Konnichiwa', "options"));
   		
   	add_submenu_page('konnichiwa', __('Subscription Plans', 'konnichiwa'), __('Subscription Plans', 'konnichiwa'), 'manage_options', 
   		'konnichiwa_plans', array('KonnichiwaPlans','manage'));	
   	add_submenu_page('konnichiwa', __('Content Access', 'konnichiwa'), __('Content Access', 'konnichiwa'), 'manage_options', 
   		'konnichiwa_content', array('KonnichiwaContents','manage'));
   	add_submenu_page('konnichiwa', __('Subscriptions', 'konnichiwa'), __('Subscriptions', 'konnichiwa'), 'manage_options', 
   		'konnichiwa_subs', array('KonnichiwaSubs','manage'));	
   	add_submenu_page('konnichiwa', __('Help', 'konnichiwa'), __('Help', 'konnichiwa'), 'manage_options', 
   		'konnichiwa_help', array('Konnichiwa','help'));	
	}
	
	// CSS and JS
	static function scripts() {
		// CSS
		//wp_register_style( 'konnichiwa-css', KONN_URL.'css/main.css?v=1');
	  //wp_enqueue_style( 'konnichiwa-css' );
   
   	wp_enqueue_script('jquery');
	   
	   // konnichiwa's own Javascript
		/*wp_register_script(
				'konnichiwa-common',
				KONN_URL.'js/konnichiwa.js',
				false,
				'0.1.0',
				false
		);
		wp_enqueue_script("konnichiwa-common");*/
	}
	
	// admin-only CSS
	static function admin_css() {
	  wp_register_style( 'konnichiwa-admin-css', KONN_URL.'css/admin.css?v=1');
	  wp_enqueue_style( 'konnichiwa-admin-css' );
	}
	
	// initialization
	static function init() {
		global $wpdb;
		load_plugin_textdomain( 'konnichiwa', false, KONN_RELATIVE_PATH."/languages/" );
		if (!session_id()) @session_start();
		
		// define table names 
		define('KONN_PLANS', $wpdb->prefix.'konnichiwa_plans');
		define('KONN_SUBS', $wpdb->prefix.'konnichiwa_subscriptions');
		define('KONN_USAGE', $wpdb->prefix.'konnichiwa_usage');
		define('KONN_CONTENT', $wpdb->prefix.'konnichiwa_content');
		define('KONN_PAYMENTS', $wpdb->prefix.'konnichiwa_payments');
		
		define( 'KONN_VERSION', get_option('konnichiwa_version'));
		$currency = get_option('konnichiwa_currency');
		$currency = empty($currency) ? 'USD' : $currency;
		define('KONN_CURRENCY', $currency);		
		
		// meta boxes
		add_action( 'add_meta_boxes', array('KonnichiwaContents', 'meta_box') );
		add_filter( 'the_content', array('KonnichiwaContents', 'access_filter') );
		
		// shortcodes
		add_shortcode('konnichiwa-plans', array('KonnichiwaShortcodes', 'plans'));
		add_shortcode('konnichiwa-subscribe', array('KonnichiwaShortcodes', 'subscribe'));
		
		// actions
		add_action('template_redirect', array('KonnichiwaSubs', 'template_redirect'));
		
		// Paypal IPN
		add_filter('query_vars', array(__CLASS__, "query_vars"));
		add_action('parse_request', array("KonnichiwaPayment", "parse_request"));
	}
	
	// handle Konnichiwa vars in the request
	static function query_vars($vars) {
		$new_vars = array('konnichiwa');
		$vars = array_merge($new_vars, $vars);
	   return $vars;
	} 	
			
	// manage general options
	static function options() {
		if(!empty($_POST['konnichiwa_payment_options'])) {
			update_option('konnichiwa_accept_other_payment_methods', $_POST['accept_other_payment_methods']);
			update_option('konnichiwa_other_payment_methods', $_POST['other_payment_methods']);
			update_option('konnichiwa_currency', $_POST['currency']);
			update_option('konnichiwa_accept_paypal', @$_POST['accept_paypal']);
			update_option('konnichiwa_paypal_id', $_POST['paypal_id']);
			
			update_option('konnichiwa_accept_stripe', @$_POST['accept_stripe']);
			update_option('konnichiwa_stripe_public', $_POST['stripe_public']);
			update_option('konnichiwa_stripe_secret', $_POST['stripe_secret']);
		}		
			
		$accept_other_payment_methods = get_option('konnichiwa_accept_other_payment_methods');
		$accept_paypal = get_option('konnichiwa_accept_paypal');
		$accept_stripe = get_option('konnichiwa_accept_stripe');
		
		$currency = get_option('konnichiwa_currency');
		$currencies=array('USD'=>'$', "EUR"=>"&euro;", "GBP"=>"&pound;", "JPY"=>"&yen;", "AUD"=>"AUD",
	   "CAD"=>"CAD", "CHF"=>"CHF", "CZK"=>"CZK", "DKK"=>"DKK", "HKD"=>"HKD", "HUF"=>"HUF",
	   "ILS"=>"ILS", "MXN"=>"MXN", "NOK"=>"NOK", "NZD"=>"NZD", "PLN"=>"PLN", "SEK"=>"SEK",
	   "SGD"=>"SGD");				
			
		require(KONN_PATH."/views/options.html.php");
	}	
	
	static function help() {
		require(KONN_PATH."/views/help.html.php");
	}	
}