<?php
// provides the payment processing
class KonnichiwaPayment {
	// handle Paypal IPN request
	static function parse_request($wp) {
		// only process requests with "konnichiwa=paypal"
	   if (array_key_exists('konnichiwa', $wp->query_vars) 
	            && $wp->query_vars['konnichiwa'] == 'paypal') {
	        self::paypal_ipn($wp);
	   }	
	}
	
	// process paypal IPN
	static function paypal_ipn($wp) {
		global $wpdb;
		echo "<!-- KONNICHIWA paypal IPN -->";
		
	   $paypal_email = get_option("konnichiwa_paypal_id");
		
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
		foreach ($_POST as $key => $value) { 
		  $value = urlencode(stripslashes($value)); 
		  $req .= "&$key=$value";
		}		
		
		// post back to PayPal system to validate
		$header="";
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .="Host: www.paypal.com\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
		
		
		if($fp) {			
			fputs ($fp, $header . $req);
		   while (!feof($fp)) {
		      $res = fgets ($fp, 1024);
		     
		      if (strstr ($res, "200 OK")) {
		      	// check the payment_status is Completed
			      // check that txn_id has not been previously processed
			      // check that receiver_email is your Primary PayPal email
			      // process payment
				   $payment_completed = false;
				   $txn_id_okay = false;
				   $receiver_okay = false;
				   $payment_currency_okay = false;
				   $payment_amount_okay = false;
				   
				   if($_POST['payment_status'] == "Completed") {
				   	$payment_completed = true;
				   } 
				   else self::log_and_exit("Payment status: $_POST[payment_status]");
				   
				   // check txn_id
				   $txn_exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".KONN_PAYMENTS."
					   WHERE method='paypal' AND payment_key=%s", $_POST['txn_id']));
					if(empty($txn_id)) $txn_id_okay = true; 
					else self::log_and_exit("TXN ID exists: $txn_id");  
					
					// check receiver email
					if($_POST['business']==$paypal_email) {
						$receiver_okay = true;
					}
					else self::log_and_exit("Business email is wrong: $_POST[business]");
					
					// check payment currency
					if($_POST['mc_currency']==get_option("konnichiwa_currency")) {
						$payment_currency_okay = true;
					}
					else self::log_and_exit("Currency is $_POST[mc_currency]"); 
					
					// select subscription
					$sub = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".KONN_SUBS." WHERE id=%d", $_GET['sub_id']));
					
					// select plan
					$plan = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".KONN_PLANS." WHERE id=%d", $sub->plan_id));
					
					// check amount
					
					if($_POST['mc_gross'] >= $plan->price) {
						$payment_amount_okay = true;
					}
					else self::log_and_exit("Wrong amount: $_POST[mc_gross] when price is {$plan->price}"); 
					
					// everything OK, insert payment and activate/extend subscription
					if($payment_completed and $txn_id_okay and $receiver_okay and $payment_currency_okay 
							and $payment_amount_okay) {						
						$wpdb->query($wpdb->prepare("INSERT INTO ".KONN_PAYMENTS." SET 
							user_id=%d, plan_id=%d, sub_id=%d, date=CURDATE(), status=%s, method=%s, payment_key=%s, amount=%s",
							$_GET['user_id'], $plan->id, $sub->id, 'completed', 'paypal', $_POST['txn_id'], $plan->price));
							
						// activate or extend subscription
						KonnichiwaSubs :: activate($sub, $plan);
						exit;
					}
		     	}
		     	else self::log_and_exit("Paypal result is not 200 OK: $res");
		   }  
		   fclose($fp);  
		} 
		else self::log_and_exit("Can't connect to Paypal");
		
		exit;
	}
	
	// log paypal errors
	static function log_and_exit($msg) {
		// log
		$msg = "Paypal payment attempt failed at ".date(get_option('date_format').' '.get_option('time_format')).": ".$msg;
		$errorlog=get_option("konnichiwa_errorlog");
		$errorlog = $msg."\n".$errorlog;
		update_option("konnichiwa_errorlog",$errorlog);
		
		// throw exception as there's no need to contninue
		exit;
	}
	
	static function Stripe() {
		global $wpdb, $user_ID;
		require_once(KONN_PATH.'/lib/Stripe.php');
 
		$stripe = array(
		  'secret_key'      => get_option('konnichiwa_stripe_secret'),
		  'publishable_key' => get_option('konnichiwa_stripe_public')
		);
		 
		Stripe::setApiKey($stripe['secret_key']);		
		
		$token  = $_POST['stripeToken'];
		$sub = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".KONN_SUBS." WHERE id=%d", $_POST['sub_id']));
		$plan = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".KONN_PLANS." WHERE id=%d", $sub->plan_id));
		$fee = $plan->price;
		$user = get_userdata($user_ID);
		$currency = get_option('konnichiwa_currency');
			 
		try {
			 $customer = Stripe_Customer::create(array(
		      'email' => $user->user_email,
		      'card'  => $token
		  ));				
			
		  $charge = Stripe_Charge::create(array(
		      'customer' => $customer->id,
		      'amount'   => $fee*100,
		      'currency' => $currency
		  ));
		} 
		catch (Exception $e) {
			wp_die($e->getMessage());
		}	  
		
		// insert payment record		
		$wpdb->query($wpdb->prepare("INSERT INTO ".KONN_PAYMENTS." SET 
							user_id=%d, plan_id=%d, sub_id=%d, date=CURDATE(), status=%s, method=%s, payment_key=%s, amount=%s",
							$user_ID, $plan->id, $sub->id, 'completed', 'stripe', $customer->ID, $plan->price));	
							
		KonnichiwaSubs :: activate($sub, $plan);					
			
		// redirect to self to avoid inserting again
		konnichiwa_redirect($_SERVER['REQUEST_URI']);	
	}	
}