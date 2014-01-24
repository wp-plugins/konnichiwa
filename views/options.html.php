<div class="wrap">
	<h1><?php _e("Konnichiwa Options", 'konnichiwa')?></h1>
	
	<form method="post">
		<div class="postbox wp-admin" style="padding:10px;">
			<h2><?php _e('Payment Settings', 'konnichiwa')?></h2>
			
			<p><label><?php _e('Payment currency:', 'konnichiwa')?></label> <select name="currency">
			<?php foreach($currencies as $key=>$val):
            if($key==$currency) $selected='selected';
            else $selected='';?>
        		<option <?php echo $selected?> value='<?php echo $key?>'><?php echo $val?></option>
         <?php endforeach; ?>
			</select></p>
			
			<p><?php _e('Here you can specify payment methods that you will accept to give access to content.', 'namaste')?></p>
			
			<p><input type="checkbox" name="accept_paypal" value="1" <?php if($accept_paypal) echo 'checked'?> onclick="this.checked?jQuery('#paypalDiv').show():jQuery('#paypalDiv').hide()"> <?php _e('Accept PayPal', 'konnichiwa')?></p>
			
			<div id="paypalDiv" style="display:<?php echo $accept_paypal?'block':'none'?>;">
				<p><label><?php _e('Your Paypal ID:', 'konnichiwa')?></label> <input type="text" name="paypal_id" value="<?php echo get_option('konnichiwa_paypal_id')?>"></p>
			</div>
			
			<p><input type="checkbox" name="accept_stripe" value="1" <?php if($accept_stripe) echo 'checked'?> onclick="this.checked?jQuery('#stripeDiv').show():jQuery('#stripeDiv').hide()"> <?php _e('Accept Stripe', 'konnichiwa')?></p>
			
			<div id="stripeDiv" style="display:<?php echo $accept_stripe?'block':'none'?>;">
				<p><label><?php _e('Your Public Key:', 'konnichiwa')?></label> <input type="text" name="stripe_public" value="<?php echo get_option('konnichiwa_stripe_public')?>"></p>
				<p><label><?php _e('Your Secret Key:', 'konnichiwa')?></label> <input type="text" name="stripe_secret" value="<?php echo get_option('konnichiwa_stripe_secret')?>"></p>
			</div>
			
			<p><input type="checkbox" name="accept_other_payment_methods" value="1" <?php if($accept_other_payment_methods) echo 'checked'?> onclick="this.checked?jQuery('#otherPayments').show():jQuery('#otherPayments').hide()"> <?php _e('Accept other payment methods', 'konnichiwa')?> 
				<span class="konnichiwa_help"><?php _e('This option lets you paste your own button HTML code or other manual instructions, for example bank wire. These payments will have to be processed manually unless you can build your own script to verify them.','konnichiwa')?></span></p>
				
			<div id="otherPayments" style="display:<?php echo $accept_other_payment_methods?'block':'none'?>;">
				<p><?php _e('Enter text or HTML code for payment button(s). You can use the following variables: {{plan-id}}, {{user-id}}, {{amount}}.', 'konnichiwa')?></p>
				<textarea name="other_payment_methods" rows="8" cols="80"><?php echo stripslashes(get_option('konnichiwa_other_payment_methods'))?></textarea>			
			</div>	
			
			<p><input type="submit" value="<?php _e('Save payment settings', 'konnichiwa')?>"></p>
		</div>
		<input type="hidden" name="konnichiwa_payment_options" value="1">
		<?php echo wp_nonce_field('save_payment_options', 'nonce_payment_options');?>
	</form>
</div>	