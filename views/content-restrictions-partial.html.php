			 	 	<p><input type="radio" name="select_protection_type_<?php echo $content_type?><?php echo $catpendix?>" value="registered" <?php if(!in_array($content_type, $protected_vals) or (!empty($protected_vals[$content_type.$catcheck]) and $protected_vals[$content_type.$catcheck] =='registered')) echo 'checked'?> onclick="konnSelectProtectionType('<?php echo $content_type?>', 'registered', '<?php echo $catpendix?>');"> <?php _e('Access is given to all registered users', 'konnichiwa')?></p>
			 	 	<p><input type="radio" name="select_protection_type_<?php echo $content_type?><?php echo $catpendix?>" value="0"  <?php if(in_array($content_type, $protected_vals) and !empty($protected_vals[$content_type.$catcheck]) and $protected_vals[$content_type.$catcheck] !='registered') echo 'checked'?> onclick="konnSelectProtectionType('<?php echo $content_type?>', 'plans', '<?php echo $catpendix?>');"> <?php _e('Access is given to user with the following subscription plan(s):', 'konnichiwa')?></p>
			 	 	
			 	 	<div class="konnichiwa-wrap" id="konnContentSettings<?php echo $content_type?>ByPlan<?php echo $catpendix?>" style="display: <?php echo (in_array($content_type, $protected_vals) and !empty($protected_vals[$content_type.$catcheck]) and $protected_vals[$content_type.$catcheck] !='registered') ? 'block' : 'none';?>;">
			 	 		<?php foreach($plans as $plan):?>
			 	 			<input type="checkbox" name="<?php echo $content_type?><?php echo $catpendix?>_plans[]" value="<?php echo $plan->id?>" <?php if(!empty($protected_vals[$content_type.$catcheck]) and strstr($protected_vals[$content_type.$catcheck], "|".$plan->id."|")) echo 'checked'?>> <?php echo $plan->name?><br> 
			 	 		<?php endforeach;?>
			 	 	</div>