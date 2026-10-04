<article>
	<?php
	if (!empty($item)) {
		
		echo "<strong>{$item['user_nickname']}</strong><br />{$item['user_email']}<br />";
		
	} else {
		
		echo $strings['no_data'];
	}
	?>
</article>