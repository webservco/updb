<article>
	
		<?php if (!empty($error_message)) { ?>
			
			<p class="strong ko"><?php echo $error_message; ?></p>
		
		<?php
		
		} else
			
			if (!empty($form)) {
				
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
				
				} // iteration
				
				echo "</fieldset>";
				
				echo '</form>';
			
			} else {
				?>
		
		<?php } ?>
	
</article>
