<article class="clearfix">

<?php
if (!empty($pub_issue)) {
	
	echo "<h2>{$pub_issue['pub_issue_title']}</h2>";
	
	if (!empty($pub_issue['pub_issue_description'])) {
		
		echo "<div class=\"pub_content pub_box\">" . nl2br($pub_issue['pub_issue_description']) . "</div>";
	
	}
	
	echo '<div class="pub_content pub_box">';
	echo "<p><strong>" . __('Format') . "</strong>: {$pub_issue['pub_format_name']}</p>";
	echo "<p><strong>" . __('Layout') . "</strong>: {$pub_issue['pub_layout_type_title']}</p>";
	echo "<p><strong>" . __('Duplication') . "</strong>: {$pub_issue['pub_duplication_type_title']}</p>";
	echo "<p><strong>" . __('Year') . "</strong>: {$pub_issue['pub_issue_year']}</p>";
	echo "<p><strong>" . __('Pages') . "</strong>: {$pub_issue['pub_issue_pages']}</p>";
	echo '</div>';
	
	if (!empty($pub_issue['pub_languages'])) {
		
		echo '<div class="pub_content pub_box">';
		
		echo '<p>';
		
		echo '<strong>' . __('Languages') . '</strong>: ';
		
		echo implode(', ', $pub_issue['pub_languages']);
		
		echo '</p>';
		
		echo '</div>';
	}
	
	echo '<div class="pub_content pub_box">';
	
	if (!empty($pub_issue['content_data'])) {
		
		foreach ($pub_issue['content_data'] as $title => $data) {
			
			if (!empty($data)) {
				echo "<p><strong>{$title}</strong>: ";
				echo implode(', ', $data);
				echo "</p>";
			}
		}
	}
	
	if (!empty($pub_issue['content_values'])) {
		
		echo '<p>';
		
		echo '<strong>' . implode('</strong>, <strong>', $pub_issue['content_values']) . '</strong>';
		
		echo '</p>';
	}
	
	echo '</div>';
	
	if (!empty($pub_issue_images)) {
		
		echo '<div class="pub_gallery pub_content pub_box">';
		
		foreach ($pub_issue_images as $image_id => $image_data) {
			echo "<a class=\"gallery_item\" href=\"{$url['www']}usr/media/pub_issue/{$image_data['image_file']}\">";
			echo "<img src=\"{$url['www']}usr/media/pub_issue_t/{$image_data['image_thumb']}\" alt=\"{$pub_issue['pub_issue_title']}\" />";
			echo "</a>";
		}
		
		echo '</div>';
	}
	
	echo '<div class="content_nav">';
	
	if (!empty($user['isEditor'])) {
		
		if (!empty($pub_id)) {
			
			echo "<p><a href=\"{$url['www']}Pub/Edit/PubIssueModify/issue/{$pub_issue['pub_issue_id']}/pub/{$pub_id}\">" . __('Modify issue') . "</a></p>";
			
			echo "<p><a href=\"{$url['www']}Pub/Edit/PubIssueAdd/pub/{$pub_id}\">" . __('Add issue') . "</a></p>";
			
			echo "<p><a href=\"{$url['www']}Pub/Edit/PubIssueImagesEdit/id/{$pub_issue['pub_issue_id']}/pub/{$pub_id}\">" . __('Manage issue images') . "</a></p>";
		}
	
	}
	
	if (!empty($pub_id)) {
		echo "<p><a href=\"{$url['www']}Pub/App/PubView/pub/{$pub_id}\">" . __('View all issues') . "</a></p>";
	}
	
	echo "<p><a href=\"{$url['www']}Pub/App/PubList\">" . __('View all publications') . "</a></p>";
	
	echo '</div>';
	
	echo "<div class=\"issue_lists\">";
	
	echo "	<h4>" . __('Lists') . "</h4>";
	
	if (!empty($item_lists)) {
		
		echo __('This issue is in the following lists') . ':';
		
		foreach ($item_lists as $list) {
			
			echo '<div class="list_item">';
			echo "{$list['list_type_title']}: ";
			echo "<strong><a href=\"{$url['www']}Lists/App/Details/list/{$list['list_id']}\"> {$list['list_title']}</a></strong> ";
			echo "(<strong>{$list['user_name']}</strong>)";
			echo "</div>";
		}
	} else {
		
		echo '<p>' . __('No lists') . '</p>';
	}
	
	echo "	<h4>" . __('Your lists') . "</h4>";
	
	if (!empty($user['Logged'])) {
		
		if (!empty($user_lists)) {
			
			foreach ($user_lists as $list) {
				
				echo '<div class="list_item">';
				echo "{$list['list_type_title']}: ";
				echo "<strong><a href=\"{$url['www']}Lists/App/Details/list/{$list['list_id']}\"> {$list['list_title']}</a></strong>";
				echo ' (';
				if (!empty($list['item_in_list'])) {
					echo __('already in list');
				} else {
					echo "<a href=\"{$url['www']}Lists/App/AddItem/list/{$list['list_id']}/item/{$pub_issue_id}\"> ";
					echo __('add');
					echo '</a>';
				}
				echo ')';
				echo "</div>";
			}
		
		} else {
			
			echo '<p>' . __('No lists') . '</p>';
		}
	
	} else {
		
		echo "<a href=\"{$url['www']}Users/App/Account\">" . __('Login', 'App') . '</a> ' . __('or', 'App') . ' ';
		echo "<a href=\"{$url['www']}Users/App/Register\">" . __('register', 'App') . '</a> ' . __('to manage your lists', 'App');
	}
	
	echo '</div>';

} else {
	
	echo "<p class=\"ko\">{$strings['no_data']}</p>";
}
?>

</article>
