<article class="clearfix">

<?php
if (!empty($list)) {
	
	echo "<h2>{$list['list_title']}</h2>";
	
	if ($user['Id'] != $list['user_id']) {
		echo "<p><strong>{$list['user_name']}</strong></p>";
	}
	
	echo "<p>{$list['list_notes']}</p>";
	
	echo "<p>" . __('Type') . ": {$list['list_type_title']}</p>";
	echo "<p>" . __('Public') . ': ';
	echo (!empty($list['list_public'])) ? __('yes') : __('no');
	echo " </p>";
	
	if (!empty($list['items'])) {
		
		foreach ($list['items'] as $item) {
			
			echo '<div class="list_item">';
			echo "<strong><a href=\"{$url['www']}Pub/App/PubIssueView/issue/{$item['item_id']}\"> {$item['item_title']}</a></strong><br />";
			echo "{$item['item_notes']}";
			echo " (<a href=\"{$url['www']}Lists/App/ModifyItem/list/{$item['list_id']}/item/{$item['item_id']}\">" . __('edit') . "</a>)";
			echo " (<a href=\"{$url['www']}Lists/App/DeleteItem/list/{$item['list_id']}/item/{$item['item_id']}\">" . __('delete') . "</a>)";
			echo '</div>';
		}
	} else {
		
		echo '<p>' . __('no items') . '</p>';
	}
	
	echo '<div class="content_nav">';
	
	echo "<p><a href=\"{$url['www']}Lists/App/View/user/{$list['user_id']}\">" . sprintf(__("View %s's lists"), $list['user_name']) . "</a></p>";
	
	if (!empty($list_owner)) {
		
		echo "<p><a href=\"{$url['www']}Lists/App/Modify/list/{$list['list_id']}\">" . __('Modify list') . "</a></p>";
		echo "<p><a href=\"{$url['www']}Lists/App/DeleteList/list/{$list['list_id']}\">" . __('Delete list') . "</a></p>";
	}
	
	echo '</div>';

} else {
	
	echo "<p class=\"ko\">{$strings['no_data']}</p>";
}

?>

</article>
