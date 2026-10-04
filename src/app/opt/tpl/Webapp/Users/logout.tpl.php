<article>
	<?php if (!empty($strings['message'])) { ?>
	
	<p><?php echo $strings['message']; ?></p>
	
	<?php
	} else { 
		
		echo $strings['no_data'];
	}
	?>
</article>