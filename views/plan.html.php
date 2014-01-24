<div class="wrap">
	<h1><?php _e('Add/Edit Subscription Plan', 'konnichiwa')?></h1>
	
	<form method="post" onsubmit="return konnichiwaValidate(this);">
		<p><label><?php _e('Plan name', 'konnichiwa')?></label> <input type="text" name="name" size="50" value="<?php echo @$plan->name?>"></p>
		<p><label><?php _e('Optional description', 'konnichiwa')?></label> <?php wp_editor(stripslashes(@$plan->description), 'description')?></p>
		<p><label><?php _e('Price', 'konnichiwa')?></label> <?php echo KONN_CURRENCY?> <input type="text" name="price" size="6" value="<?php echo @$plan->price?>"></p>
		<p><label><?php _e('Duration', 'konnichiwa')?></label> <input type="text" name="duration" size="4" value="<?php echo @$plan->duration?>"> 
		<select name="duration_unit">
			<option value="day" <?php if(!empty($plan->id) and $plan->duration_unit == 'day') echo 'selected'?>><?php _e('days', 'konnichiwa')?></option>
			<option value="week" <?php if(!empty($plan->id) and $plan->duration_unit == 'week') echo 'selected'?>><?php _e('weeks', 'konnichiwa')?></option>
			<option value="month" <?php if(!empty($plan->id) and $plan->duration_unit == 'month') echo 'selected'?>><?php _e('months', 'konnichiwa')?></option>
		</select></p>
		<p><input type="submit" value="<?php _e('Save This Plan', 'konnichiwa')?>"></p>
		<input type="hidden" name="ok" value="1">
	</form>
</div>

<script type="text/javascript" >
function konnichiwaValidate(frm) {
	if(frm.name.value == '') {
		alert("<?php _e('Please enter name.', 'konnichiwa')?>");
		frm.name.focus();
		return false;
	}
	
	if(frm.duration.value == '' || isNaN(frm.duration.value) || frm.duration.value <= 0) {
		alert("<?php _e('Please enter positive number for duration.', 'konnichiwa')?>");
		frm.duration.focus();
		return false;
	}
	
	return true;
}
</script>