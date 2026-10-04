<article>
<?php
echo '<h2>' . __('Latest issues') . '</h2>';

if (!empty($latest_issues)) {

	foreach ($latest_issues as $issue) {
		echo '<div class="pub_list">';
		echo "<a class=\"pub_list_thumb\" href=\"{$url['www']}Pub/App/PubIssueView/issue/{$issue['pub_issue_id']}\">";
		echo "<img src=\"";
		echo !empty($issue['thumbnail']) ? "{$url['www']}usr/media/pub_issue_t/{$issue['thumbnail']}" : "{$url['skin']}img/no_image.jpg";
		echo "\" alt=\"{$issue['pub_issue_title']}\" />";
			echo '</a>';
				echo '<div class="pub_list_details">';
					echo "<strong><a href=\"{$url['www']}Pub/App/PubIssueView/issue/{$issue['pub_issue_id']}\">{$issue['pub_issue_title']}</a></strong>";
					echo " {$issue['pub_issue_year']}<br />";
		echo "{$issue['pub_format_name']}, {$issue['pub_layout_type_title']}, {$issue['pub_duplication_type_title']}, {$issue['pub_issue_pages']} " . __('pages');
		echo "</div>";
		echo "</div>";
	}
} else {

	echo '<p>' . __('No issues') . '</p>';
}
?>
</article>
