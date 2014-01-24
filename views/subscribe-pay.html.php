<div class="wrap">
	<h2><?php printf(__('Subscribing in %s', 'konnichiwa'), $plan->name)?> </h2>
	
	<?php if($plan->price <= 0):?>
		<p><?php _e('You have been successfully subscribed to the selected plan!', 'konnichiwa')?></p>
		</div>
	<?php return;
	endif;?>

	<?php if($accept_paypal and $paypal_id): // generate Paypal button ?>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<p align="center">
		<input type="hidden" name="cmd" value="_xclick">
		<input type="hidden" name="business" value="<?php echo $paypal_id?>">
		<input type="hidden" name="item_name" value="<?php printf(__('Subscribe for %s', 'konnichiwa'), $plan->name)?>">
		<input type="hidden" name="item_number" value="<?php echo $sub_id?>">
		<input type="hidden" name="amount" value="<?php echo number_format($plan->price,2,".","")?>">
		<input type="hidden" name="return" value="<?php echo $_SERVER['PHP_SELF'];?>">
		<input type="hidden" name="notify_url" value="<?php echo site_url('?konnichiwa=paypal&sub_id='.$sub_id.'&user_id='.$user_ID);?>">
		<input type="hidden" name="no_shipping" value="1">
		<input type="hidden" name="no_note" value="1">
		<input type="hidden" name="currency_code" value="<?php echo $currency;?>">
		<input type="hidden" name="lc" value="US">
		<input type="hidden" name="bn" value="PP-BuyNowBF">
		<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-butcc.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</p>
	</form> 
	<?php endif;?>
	
	<?php if($accept_stripe and !empty($stripe['secret_key'])): // generate stripe button?>
	<form method="post">
		<p align="center">
	  <script src="https://checkout.stripe.com/v2/checkout.js" class="stripe-button"
	          data-key="<?php echo $stripe['publishable_key']; ?>"
	          data-amount="<?php echo $plan->price*100?>" data-description="<?php printf(__('Subscription plan %s', 'konnichiwa'), $plan->name)?>" data-currency="<?php echo $currency?>"></script>
	<input type="hidden" name="konnichiwa_stripe_pay" value="1">
	<input type="hidden" name="sub_id" value="<?php echo $sub_id?>">
	</p>
	</form>
<?php endif;?>
	
	<?php if($accept_other_payment_methods):?>
		<div><?php echo $other_payment_methods?></div>
	<?php endif;?>
</div>