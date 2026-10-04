<article class="clearfix">

	<h2><?php echo $strings['title']; ?></h2>
	
<?php
if (!empty($form)) {
	
	if (!empty($form['errors']['_result'])) {
		
		echo '<p class="ko">';
		echo '' . __('Error') . '!<br />';
		
		foreach ($form['errors']['_result'] as $message) {
			
			echo "{$message}<br />";
		}
		
		echo '</p>';
	}
	
	echo '<form enctype="multipart/form-data" method="post" action="">';
	
	echo '<fieldset>';
	
	foreach ($form['meta'] as $field_id => $meta) {
		
		echo "<div>";
		
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
				
				$select_multiple = ('multiple' == $meta['custom']) ? ' multiple="multiple"' : NULL;
				
				echo "<select name=\"{$field_id}";
				if (!empty($select_multiple)) {
					echo '[]';
				}
				echo "\" id=\"{$field_id}\"{$select_multiple}>";
				
				echo '	<option value="">' . __('Please select') . '</option>';
				
				if (!empty($meta['options'])) {
					
					foreach ($meta['options'] as $value => $title) {
						
						echo "<option value=\"{$value}\"";
						
						if (!empty($select_multiple)) {
							
							if (!empty($form['data'][$field_id][$value])) {
								echo ' selected="selected"';
							}
						
						} else {
							
							if ($form['data'][$field_id] == $value) {
								
								echo ' selected="selected"';
							}
						}
						
						echo ">{$title}</option>";
					}
				}
				
				echo "</select>";
				
				break;
			
			case 'textarea' :
				
				echo "<textarea name=\"{$field_id}\" id=\"{$field_id}\" title=\"{$meta['description']}\">{$form['data'][$field_id]}</textarea>";
				
				break;
			
			case 'checkbox' :
				
				if (!empty($meta['options'])) { // multiple checkboxes
					
					foreach ($meta['options'] as $value => $title) {
						
						echo "<br /><input name=\"{$field_id}[]\" type=\"{$meta['type']}\" title=\"{$meta['description']}\" value=\"{$value}\" ";
						if (!empty($form['data'][$field_id][$value])) {
							echo " checked=\"checked\"";
						}
						echo "/>";
						echo "&#160;{$title}";
					}
				} else {
					
					echo "<input name=\"{$field_id}\" id=\"{$field_id}\" type=\"{$meta['type']}\" title=\"{$meta['description']}\"";
					
					if (!empty($form['data'][$field_id])) {
						echo " checked=\"checked\"";
					}
					
					echo " />";
				}
				
				break;
			
			case 'text' :
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
		
		echo "</div>";
	
	} // iteration
	
	echo "</fieldset>";
	
	echo "<fieldset><div>";
	
	echo '<input type="submit" name="submit" value="' . __('Submit') . '" />';
	
	echo '&#160;&#160;&#160;';
	
	echo '<input type="reset" name="reset" value="' . __('Reset') . '" />';
	
	echo "</div></fieldset>";
	
	echo "</form>";

} else {
	
	echo "<p class=\"ko\">{$strings['no_data']}</p>";
}

echo '<div class="content_nav">';

if (!empty($back_location)) {
	
	echo "<p><a href=\"{$url['www']}{$back_location}\">" . __('Back') . "</a></p>";

}

echo '</div>';
?>

</article>