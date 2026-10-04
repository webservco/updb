<?php if (!empty($comments['show'])) { ?>
<article class="comments clearfix">

	<h4><?php echo __('Comments', 'App'); ?></h4>
	
	<?php
	if (!empty($comments['list'])) {
		
		foreach ($comments['list'] as $comment) {
			
			echo '<div class="comment">';
			echo "<strong>{$comment['user_name']} (" . date("H:i d.m 'y", strtotime($comment['comment_date'])) . ')</strong>';
			if (!empty($user['Logged']) && $comment['user_id'] == $user['Id']) {
				echo " (<a href=\"{$url['current']}/comment/{$comment['comment_id']}\">" . __('edit', 'App') . "</a>)";
			}
			echo '<br />' . nl2br($comment['comment_content']);
			echo '</div>';
		}
	} else {
		
		echo '<p>' . __('No comments yet', 'App') . '</p>';
	}
	
	if (!empty($comments['form'])) {
		
		echo '<div class="comment_form">';
		
		if (!empty($comments['form']['errors']['_result'])) {
			
			echo '<p class="ko">';
			echo '' . __('Error', 'App') . '!<br />';
			
			foreach ($comments['form']['errors']['_result'] as $message) {
				
				echo "{$message}<br />";
			}
			
			echo '</p>';
		}
		
		echo '<form enctype="multipart/form-data" method="post" action="">';
		
		echo '<fieldset>';
		
		foreach ($comments['form']['meta'] as $field_id => $meta) {
			
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
					
					echo '	<option value="">' . __('Please select', 'App') . '</option>';
					
					if (!empty($meta['options'])) {
						
						foreach ($meta['options'] as $value => $title) {
							
							echo "<option value=\"{$value}\"";
							
							if (!empty($select_multiple)) {
								
								if (!empty($comments['form']['data'][$field_id][$value])) {
									echo ' selected="selected"';
								}
							
							} else {
								
								if ($comments['form']['data'][$field_id] == $value) {
									
									echo ' selected="selected"';
								}
							}
							
							echo ">{$title}</option>";
						}
					}
					
					echo "</select>";
					
					break;
				
				case 'textarea' :
					
					echo "<textarea name=\"{$field_id}\" id=\"{$field_id}\" title=\"{$meta['description']}\">{$comments['form']['data'][$field_id]}</textarea>";
					
					break;
				
				case 'checkbox' :
					
					if (!empty($meta['options'])) { // multiple checkboxes
						
						foreach ($meta['options'] as $value => $title) {
							
							echo "<br /><input name=\"{$field_id}[]\" type=\"{$meta['type']}\" title=\"{$meta['description']}\" value=\"{$value}\" ";
							if (!empty($comments['form']['data'][$field_id][$value])) {
								echo " checked=\"checked\"";
							}
							echo "/>";
							echo "&#160;{$title}";
						}
					} else {
						
						echo "<input name=\"{$field_id}\" id=\"{$field_id}\" type=\"{$meta['type']}\" title=\"{$meta['description']}\"";
						
						if (!empty($comments['form']['data'][$field_id])) {
							echo " checked=\"checked\"";
						}
						
						echo " />";
					}
					
					break;
				
				case 'text' :
				case 'submit' :
				default :
					
					echo "<input name=\"{$field_id}\" id=\"{$field_id}\" type=\"{$meta['type']}\" title=\"{$meta['description']}\" value=\"{$comments['form']['data'][$field_id]}\" />";
					
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
			if (!empty($comments['form']['errors'][$field_id])) {
				
				echo "<p class=\"form_error ko\">";
				
				foreach ($comments['form']['errors'][$field_id] as $error_msg) {
					
					echo "{$error_msg}<br />";
				}
				
				echo "</p>";
			}
			
			echo "</div>";
		
		} // iteration
		
		echo "</fieldset>";
		
		echo "<fieldset><div>";
		
		echo '<input type="submit" name="submit" value="' . __('Submit', 'App') . '" />';
		
		echo '&#160;&#160;&#160;';
		
		echo '<input type="reset" name="reset" value="' . __('Reset', 'App') . '" />';
		
		echo "</div></fieldset>";
		
		echo "</form>";
		
		echo '</div>';
	
	} else {
		
		echo "<a href=\"{$url['www']}Users/App/Account/next/{$comments['next']}\">" . __('Login', 'App') . '</a> ' . __('or', 'App') . ' ';
		echo "<a href=\"{$url['www']}Users/App/Register/next/{$comments['next']}\">" . __('register', 'App') . '</a> ' . __('to post comments', 'App');
	}
	?>
	</article>
<?php } ?>