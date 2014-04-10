<div class="wrap">
	<h1><?php _e('Manage Subscription Plans', 'konnichiwa')?></h1>
	
	<p><a href="admin.php?page=konnichiwa_plans&action=add"><?php _e('Click here to create a new plan', 'konnichiwa')?></a></p>
	
	<?php if(sizeof($plans)):?>
		<table class="widefat">
			<tr><th><?php _e('Plan name', 'konnichiwa')?></th><th><?php _e('Subscribe Shortcode', 'konnichiwa')?></th>
			<th><?php _e('Protect Shortcode', 'konnichiwa')?></th><th><?php _e('Price', 'konnichiwa')?></th><th><?php _e('Duration', 'konnichiwa')?></th>
			<th><?php _e('Edit / delete', 'konnichiwa')?></th></tr>
			<?php foreach($plans as $plan):
				$class = ("alternate" == @$class) ? '' : 'alternate';?>
				<tr class="<?php echo $class?>"><td><?php echo $plan->name?></td>
				<td><input type="text" value="[konnichiwa-subscribe <?php echo $plan->id?>]" readonly="true" onclick="this.select()"></td>
				<td><input type="text" value="[konnichiwa-protect plans='<?php echo $plan->id?>']" readonly="true" onclick="this.select()" size="25"></td>
				<td><?php echo KONN_CURRENCY.' '.$plan->price?></td>
				<td><?php echo $plan->duration.' '.$plan->duration_unit?></td>
				<td><a href="admin.php?page=konnichiwa_plans&action=edit&id=<?php echo $plan->id?>"><?php _e('Edit', 'konnichiwa')?></a>
				| <a href="#" onclick="konnichiwaConfirmDelete(<?php echo $plan->id?>);return false;"><?php _e('Delete', 'konnichiwa')?></a></td></tr>
			<?php endforeach;?>
		</table>
	<?php endif;?>
	
	<h2><?php _e('General Shortcodes', 'konnichiwa')?></h2>
	<p><?php _e('The plan shortcode is used to generate a subscribe button. The button will automatically handle the user subscription and redirect them to the payment page accordingly to your payment settings.', 'konnichiwa');?></p>
	
	<p><?php _e('It is recommended to design your own page that will list the plans with their features etc.<br> However there are basic shortcodes that you can use to automatically generate a table with all the available plans:', 'konnichiwa');?> <input type="text" value="[konnichiwa-plans vertical]" readonly="true" onclick="this.select();" size="25"> <?php _e('- generates list of the plans with their feautures ordered in columns, while', 'konnichiwa');?> <br> <input type="text" value="[konnichiwa-plans horizontal]" readonly="true" onclick="this.select();" size="25"> <?php _e('generates a horizontal table with plans. Both codes auto-generate the "Subscribe" buttons.', 'konnichiwa');?> </p>
	
	<h2><?php _e('Protect Shortcodes', 'konnichiwa')?></h2>
	
	<p><?php _e('This shortcode can be used to protect a piece of content inside a post, page or custom content that is public accessible. Here is how to use it:', 'konnichiwa')?></p>
	
	<p><?php _e('Start with', 'konnichiwa')?> <b>[konnichiwa-protect plans="x"]</b> <?php _e('(where x is the required subscription plan ID which you can get from the above table), and end with', 'konnichiwa')?> <b>[/konnichiwa-protect]</b></p>
	<p><?php _e('Put your content between both shortcodes.', 'konnichiwa');?></p>
	<p><?php _e('You can also allow multiple subscription plans by separating their plan IDs with comma (NO SPACES!). Example:', 'konnichiwa')?></p>
	<p><b>[konnichiwa-protect plans="2,5,6"]</b><?php _e('Your protected content here', 'konnichiwa')?><b>[/konnichiwa-protect]</b></p>
	
	
</div>

<script type="text/javascript" >
function konnichiwaConfirmDelete(id) {
	if(confirm("<?php _e('Are you sure?', 'konnichiwa')?>")) {
		window.location = 'admin.php?page=konnichiwa_plans&del=1&id=' + id;
	}
}
</script>