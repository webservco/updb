<article class="clearfix">

	<h2><?php echo $strings['title']; ?></h2>
	
<?php
if (!empty($lists)) {
	
	foreach ($lists as $list) {
		echo '<div class="list_item">';
		echo "{$list['list_type_title']}: ";
		echo "<strong><a href=\"{$url['www']}Lists/App/Details/list/{$list['list_id']}\"> {$list['list_title']}</a></strong>";
		echo " ({$list['total_items']})";
		echo " <strong>{$list['user_name']}</strong>";
		echo "</div>";
	}

} else {
	
	echo '<p>' . __('No lists') . '</p>';
}

echo '<div class="content_nav">';

echo "	<p><a href=\"{$url['www']}Lists/App/View/user/{$user['Id']}\">" . __('My lists') . "</a></p>";

if (!empty($list_types)) {
	
	foreach ($list_types as $id => $title) {
		echo "<p><a href=\"{$url['www']}Lists/App/Add/type/{$id}\">" . __('Add list') . " - {$title}</a></p>";
	}
}

echo '</div>';
?>

</article>
