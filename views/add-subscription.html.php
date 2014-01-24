<div class="wrap">
	<h1><?php _e('Add Subscription', 'konnichiwa')?></h1>
	
	<?php if(!empty($error)):?>
		<p class="error"><?php echo $error;?></p>
	<?php endif;?>
	
	<form method="post" onsubmit="return validateKonnichiwaForm(this);">
		<div class="konnichiwa-wrap konnichiwa-form wp-admin">
			<p><label><?php _e('User login or email address:', 'konnichiwa')?></label> &nbsp; <input type="text" name="userhandle" value="<?php echo @$_POST['userhandle']?>"></p>
			<p><label><?php _e('Subscription plan:', 'konnichiwa')?></label> <select name="plan_id">
				<?php foreach($plans as $plan):?>
					<option value="<?php echo $plan->id?>" <?php if($plan->id == $_GET['plan_id']) echo 'selected'?>><?php echo $plan->name?></option>
				<?php endforeach;?>
			</select></p>
			<p><label><?php _e('Amount paid:','konnichiwa')?></label> <?php echo KONN_CURRENCY?> <input type="text" name="amt_paid" size="6" value="<?php echo @$_POST['amt_paid']?>"></p>
			
			<p><input type="submit" value="<?php _e('Save Subscription', 'konnichiwa')?>">
			<input type="button" value="<?php _e('Cancel', 'konnichiwa')?>" onclick="window.location = 'admin.php?page=konnichiwa_subs&plan_id=<?php echo $_GET['plan_id']?>&ob=<?php echo $_GET['ob']?>&dir=<?php echo $_GET['dir']?>&offset=<?php echo $_GET['offset']?>'"></p>
			<input type="hidden" name="ok" value="1">
		</div>
	</form>
</div>

<script type="text/javascript" >
function validateKonnichiwaForm(frm) {	
	if(frm.userhandle.value == '') {
		alert("<?php _e('Please enter login or email address!', 'konnichiwa')?>");
		frm.userhandle.focus();
		return false;
	}
	
	return true;
}
</script>