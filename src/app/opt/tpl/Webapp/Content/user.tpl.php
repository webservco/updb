<article>
	<h3><?php echo __('Get data from another module');?></h3>
	<?php
	if (!empty($user_info)) {
		
		foreach ($user_info as $key => $value) {
			
			echo "<strong>{$key}</strong>: {$value}<br />";
		}
		
	} else {
	
		echo $strings['no_data'];
	}
	?>
</article>