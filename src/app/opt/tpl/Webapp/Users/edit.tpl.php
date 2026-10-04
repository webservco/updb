<article>
	
<?php if (!empty($error_message)) { ?>
			
	<p class="strong ko"><?php echo $error_message; ?></p>
		
<?php } else if (!empty($form)) { ?>
			
	<?php
	
	echo '<form enctype="multipart/form-data" method="post" action="">';
				
	echo '<fieldset>';
				
	foreach ($form['meta'] as $field_id => $meta) {

		/**
		 * Label
		 */
		echo "<label for=\"{$field_id}\" title=\"{$meta['description']}\">";
		
		echo "{$meta['title']}&#160;";
		
		if (!empty($meta['required'])) {
			
			echo '<span class="ko">*</span>';
		}
		
		echo "</label>";
		
		
		/**
		 * Actual form field.
		 */
		
		echo '<p>';
		
		switch ($meta['type']) {
				
			case 'select' :
				
				echo "<select name=\"{$field_id}\" id=\"{$field_id}\">";
				
				echo '	<option value="">' . __('Please select') . '</option>';
				
				if (!empty($meta['options'])) {
					
					foreach ($meta['options'] as $value => $title) {
						
						echo "<option value=\"{$value}\"";
						
						if ($form['data'][$field_id] == $value) {
							
							echo ' selected="selected"';
						}
						
						echo ">{$title}</option>";
					}
				}
				
				echo "<select>";
				
				break;
				
			case 'textarea' :
				
				echo "<textarea name=\"{$field_id}\" id=\"{$field_id}\" title=\"{$meta['description']}\">{$form['data'][$field_id]}</textarea>";
				
				break;
			
			case 'text' :
			case 'checkbox' :
			case 'submit' :
			default :
				
				echo "<input name=\"{$field_id}\" id=\"{$field_id}\" type=\"{$meta['type']}\" title=\"{$meta['description']}\" value=\"{$form['data'][$field_id]}\" />";
				
				break;
			}
		
		echo '</p>';
		
		/**
		 * Field info.
		 */
		echo "<p class=\"form_info\">";
		
		echo $meta['description'];
		
		echo "</p>";
		
		/**
		 * Field errors.
		 */
		if (!empty($form['errors'][$field_id])) {
			
			echo "<p class=\"form_error ko\">";
			
			foreach ($form['errors'][$field_id] as $error_msg) {
				
				echo "{$error_msg}<br />";
			}
			
			echo "</p>";
		}
		
	} //iteration
	
	echo '<input title="" type="submit" name=\"send" id="send" value="' . __('Modify') . '" />';

	echo "</fieldset>";
	
	echo "</form>";
	?>				
			
<?php } else { ?>
		
		<p><?php echo $strings['no_data']; ?></p>		
		
<?php } ?>
	
</article>
