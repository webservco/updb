<article>
	
		<p><?php echo __('If you forgot your password simply enter the email address you used to register an account and we will send you a new password.'); ?></p>
		
		<?php if (!empty($error_message)) { ?>
			
			<p class="strong ko"><?php echo $error_message; ?></p>
		
		<?php } else if (!empty($form)) { ?>
			
			<form class="login" enctype="multipart/form-data" method="post" action="" autocomplete="off">
			
				<fieldset>
						
					<legend></legend>
						
				<?php foreach ($form['meta'] as $field_id => $meta) { ?>
					<label class="tooltip" for="<?php echo $field_id; ?>" title="<?php echo $meta['description']; ?>">
						<?php echo $meta['title']; ?>&#160;
					</label>
						
					<p>
						<input class="tooltip" title="<?php echo $meta['description']; ?>" type="<?php echo $meta['type']; ?>" name="<?php echo $field_id; ?>" id="<?php echo $field_id; ?>" value="<?php echo $form['data'][$field_id]; ?>" /> 
					</p>
						
					<?php if (!empty($form['errors'][$field_id])) { ?>
					<p class="form_error ko">
						<?php foreach ($form['errors'][$field_id] as $error_msg) {
							echo $error_msg . '<br />';
						} ?> 
					</p>
					<?php } ?>				
				<?php } ?>				
						
				</fieldset>		
			
			</form>
			
			<p class="clear">
				<br />
				<?php echo __('Other options'); ?>:
				<br />
				<a href="<?php echo $url['www']; ?>Users/App/Account" title="<?php echo __('Login'); ?>"><?php echo __('Login'); ?></a>
				<br />
				<a href="<?php echo $url['www']; ?>Users/App/Register" title="<?php echo __('Register'); ?>"><?php echo __('Register'); ?></a>
			</p>
		
		<?php } else { ?>
			
				
		
		<?php } ?>
	
</article>