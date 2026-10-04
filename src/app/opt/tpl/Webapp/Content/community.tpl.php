<article>
<?php
echo '<h2>' . __('Community') . '</h2>';

echo '<h2>' . __('Lists') . '</h2>';

if (!empty($list_types)) {
	
	foreach ($list_types as $id => $title) {
		
		echo "<p><a href=\"{$url['www']}Lists/App/All/type/{$id}\">{$title}</a></p>";
	}
}

if (!empty($user['Logged'])) {
	echo "<p><a href=\"{$url['www']}Lists/App/View/user/{$user['Id']}\">" . __("My lists") . "</a></p>";
}
?>
</article>
