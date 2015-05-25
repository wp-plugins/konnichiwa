<div class="wrap">
	<h1><?php _e('Manage Subscriptions', 'konnichiwa')?></h1>
	
	<p><?php _e('Select subscription plan:', 'konnichiwa');?> <select onchange="window.location='admin.php?page=konnichiwa_subs&plan_id=' + this.value;">
		<option value=""><?php _e('- please select -', 'konnichiwa')?></option>
		<?php foreach($plans as $plan):?>
			<option value="<?php echo $plan->id?>" <?php if(!empty($_GET['plan_id']) and $_GET['plan_id'] == $plan->id) echo 'selected'?>><?php echo $plan->name?></option>
		<?php endforeach;?>
	</select></p>
	
	<?php if(!empty($_GET['plan_id'])):?>
		<p><a href="admin.php?page=konnichiwa_subs&action=add&plan_id=<?php echo $_GET['plan_id']?>&ob=<?php echo $ob?>&dir=<?php echo $dir?>&offset=<?php echo $offset?>"><?php _e('Click here to manually add subscription.', 'konnichiwa')?></a></p>
		
		<?php if($count):?>
			<table class="widefat">
				<tr><th><?php _e('User name', 'konnichiwa')?></th><th><?php _e('Date subscribed', 'konnichiwa')?></th>
				<th><?php _e('Expiration date', 'konnichiwa')?></th><th><?php _e('Status', 'konnichiwa')?></th><th><?php _e('Amount paid', 'konnichiwa')?></th>
				<th><?php _e('Edit', 'konnichiwa')?></th></tr>
				<?php foreach($subs as $sub):
					$class = ("alternate" == @$class) ? '' : 'alternate';?>
					<tr class="<?php echo $class?>"><td><?php echo $sub->username?></td><td><?php echo date($dateformat, strtotime($sub->date))?></td>
					<td><?php echo date($dateformat, strtotime($sub->expires))?></td>
					<td><?php echo $sub->status ? __('Active', 'konnichiwa') : __('Pending', 'konnichiwa')?></td>
					<td><?php echo KONN_CURRENCY.' '.$sub->amt_paid?></td>
					<td><a href="admin.php?page=konnichiwa_subs&action=edit&id=<?php echo $sub->id?>&plan_id=<?php echo $_GET['plan_id']?>&ob=<?php echo $ob?>&dir=<?php echo $dir?>&offset=<?php echo $offset?>"><?php _e('Edit', 'konnichiwa')?></a></td></tr>
				<?php endforeach;?>
			</table>
		<?php else:?> <p><?php _e('There are no subscriptions in this plan yet.', 'konnichiwa')?></p>		
	<?php endif; // end if $count
	else:?>
		<p><b><?php _e('Please select plan to manage subscriptions in it.', 'konnichiwa')?></b></p>
	<?php endif;?>
</div>