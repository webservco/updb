<article class="clearfix">

<?php
if (!empty($pub)) {
	
	echo "<div class=\"pub_subtitle\"><i>{$pub['pub_type_title']}</i> ({$pub['country_name']})</div>";
	
	echo "<h2>{$pub['pub_title']}</h2>";
	
	if (!empty($pub['pub_description'])) {
		
		echo "<div class=\"pub_content\">" . nl2br($pub['pub_description']) . "</div>";
	
	}
	
	if (!empty($pub_images)) {
		
		echo '<h4>' . __('Publication images') . '</h4>';
		echo '<div class="pub_gallery pub_content">';
		
		foreach ($pub_images as $image_id => $image_data) {
			echo "<a class=\"gallery_item\" href=\"{$url['www']}usr/media/pub/{$image_data['image_file']}\">";
			echo "<img src=\"{$url['www']}usr/media/pub_t/{$image_data['image_thumb']}\" alt=\"{$pub['pub_title']}\" />";
			echo "</a>";
		}
		
		echo '</div>';
	}
	
	echo '<h4>' . __('Publication issues') . '</h4>';
	
	if (!empty($pub_issues)) {
		
		foreach ($pub_issues as $issue) {
			echo '<div class="pub_list">';
			echo "<a class=\"pub_list_thumb\" href=\"{$url['www']}Pub/App/PubIssueView/issue/{$issue['pub_issue_id']}/pub/{$pub['pub_id']}\">";
			echo "<img src=\"";
			echo !empty($issue['thumbnail']) ? "{$url['www']}usr/media/pub_issue_t/{$issue['thumbnail']}" : "{$url['skin']}img/no_image.jpg";
			echo "\" alt=\"{$issue['pub_issue_title']}\" />";
			echo '</a>';
			echo '<div class="pub_list_details">';
			echo "<strong><a href=\"{$url['www']}Pub/App/PubIssueView/issue/{$issue['pub_issue_id']}/pub/{$pub['pub_id']}\">{$issue['pub_issue_title']}</a></strong>";
			echo " {$issue['pub_issue_year']}<br />";
			echo "{$issue['pub_format_name']}, {$issue['pub_layout_type_title']}, {$issue['pub_duplication_type_title']}, {$issue['pub_issue_pages']} " . __('pages');
			echo "</div>";
			echo "</div>";
		}
	} else {
		
		echo '<p>' . __('No issues') . '</p>';
	}
	
	echo '<div class="content_nav">';
	
	if (!empty($user['isEditor'])) {
		
		echo "<p><a href=\"{$url['www']}Pub/Edit/PubIssueAdd/pub/{$pub['pub_id']}\">" . __('Add issue') . "</a></p>";
		
		echo "<p><a href=\"{$url['www']}Pub/Edit/PubModify/pub/{$pub['pub_id']}\">" . __('Modify publication') . "</a></p>";
		
		echo "<p><a href=\"{$url['www']}Pub/Edit/PubImagesEdit/id/{$pub['pub_id']}\">" . __('Manage publication images') . "</a></p>";
	
	}
	
	echo "<p><a href=\"{$url['www']}Pub/App/PubList\">" . __('View all publications') . "</a></p>";
	
	echo '</div>';

} else {
	
	echo "<p class=\"ko\">{$strings['no_data']}</p>";
}
?>

</article>
