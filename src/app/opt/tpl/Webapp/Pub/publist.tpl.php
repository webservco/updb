<article class="clearfix">

	<h2><?php echo $strings['title']; ?></h2>
	
<?php
if (!empty($publications)) {
	
	foreach ($publications as $pub) {
		echo '<div class="pub_list">';
		echo "<a class=\"pub_list_thumb\" href=\"{$url['www']}Pub/App/PubView/pub/{$pub['pub_id']}\">";
		echo "<img src=\"";
		echo !empty($pub['thumbnail']) ? "{$url['www']}usr/media/pub_t/{$pub['thumbnail']}" : "{$url['skin']}img/no_image.jpg";
		echo "\" alt=\"{$pub['pub_title']}\" />";
		echo '</a>';
		echo '<div class="pub_list_details">';
		echo "<strong><a href=\"{$url['www']}Pub/App/PubView/pub/{$pub['pub_id']}\">{$pub['pub_title']}</a></strong><br />";
		echo "<i>{$pub['pub_type_title']}</i><br />";
		echo "{$pub['country_name']}";
		
		if (!empty($pub['issues_summary'])) {
			
			echo '<br />' .	__('issues') . ': ' . "<strong>{$pub['issues_summary']['total']}</strong>";
			
			if (!empty($pub['issues_summary']['start'])) {
				
				echo ' (' . $pub['issues_summary']['start'];
				
				if ($pub['issues_summary']['end'] != $pub['issues_summary']['start']) {
					
					echo " - {$pub['issues_summary']['end']}";
				}
				
				echo ')';
			}
		}
		echo '</div>';
		echo "</div>";	
	}
	
} else {
	
	echo "<p class=\"ko\">{$strings['no_data']}</p>";
}

	echo '<div class="content_nav">';	

	if (!empty($user['isEditor'])) {
		
		echo "<p><a href=\"{$url['www']}Pub/Edit/PubAdd\">" . __('Add publication') . "</a></p>";
	}
	
	echo '</div>';
?>

</article>
