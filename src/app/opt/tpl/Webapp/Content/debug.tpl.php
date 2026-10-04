<article>
	<p>Also check log file!</p>
	
	<?php
	$vars = get_defined_vars();
	
	echo '<table border="1" cellpadding="5" cellspacing="0" style="font-size: 75%">';
	
	foreach ($vars as $key => $value) {
		
		echo '<tr><td>';
		
		echo "<strong>&#36;{$key}</strong>";
		
		echo '</td><td><pre>';
		print_r($value);
		echo '</pre></td></tr>';
	}
	
	echo '</table>';
	?>
	
</article>