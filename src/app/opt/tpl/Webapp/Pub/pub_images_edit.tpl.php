<article class="clearfix">

	<h2>Images</h2>
<?php

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

echo '<div><p>';

echo "<input id=\"images\" name=\"images[]\" type=\"file\" multiple=\"multiple\" accept=\"image/*\" />";

echo '<p></div>';

echo "</fieldset>";

echo "<fieldset><div>";

echo '<input type="submit" name="submit" value="' . __('Submit') . '" />';

echo '&#160;&#160;&#160;';

echo '<input type="reset" name="reset" value="' . __('Reset') . '" />';

echo "</div></fieldset>";

echo "</form>";

if (!empty($pub_images)) {
	
	echo '<ul class="sortable">';
	
	foreach ($pub_images as $image_id => $image_data) {
		echo "<li id=\"{$image_id}\">";
		echo "<a class=\"gallery_item\" href=\"{$url['www']}usr/media/{$item_type}/{$image_data['image_file']}\">";
		echo "<img src=\"{$url['www']}usr/media/{$item_type}_t/{$image_data['image_thumb']}\" alt=\"{$image_data['image_file']}\" />";
		echo "</a>";
		echo " <a href=\"{$url['www']}Pub/Edit/PubImageDelete/id/{$image_id}/next/{$delete_next}\">" . __('Delete') . "</a>";
		echo "</li>";
	}
	
	echo '</ul>';
}

echo '<div class="content_nav">';
echo "<p><a href=\"{$url['www']}{$back_location}\">" . __('Back') . "</a></p>";
echo '</div>';

?>

</article>
