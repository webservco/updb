<aside id="left">
<?php
if (!empty($stats)) {
	
	if (!empty($stats['pub'])) {
		
		echo '<div class="stats_pub">';
		
		echo '	<div class="stats_issue_total stats_box stats_box_left">';
		
		echo '		<h4>' . __('Statistics', 'App') . '</h4>';
		
		echo '	<ul>';
		
		echo '		<li>' . __('Publications', 'App') . ': ' . $stats['pub']['total_pubs'] . '</li>';
		
		echo '		<li>' . __('Issues', 'App') . ': ' . $stats['pub']['total_pub_issues'] . '</li>';
		
		echo '	</ul>';
		
		echo '	</div>';
		
		echo '	<div class="stats_issue_type stats_box stats_box_left">';
		
		echo '		<h4>' . __('Types', 'App') . '</h4>';
		
		echo '		<ul>';
		
		foreach ($stats['pub']['type'] as $item) {
			
			echo "		<li>{$item['title']}: {$item['total']}</li>";
		}
		
		echo '		</ul>';
		
		echo '	</div>';
		
		echo '	<div class="stats_issue_country stats_box stats_box_left">';
		
		echo '		<h4>' . __('Countries', 'App') . '</h4>';
		
		echo '		<ul>';
		
		foreach ($stats['pub']['country'] as $item) {
			
			echo "		<li>{$item['title']}: {$item['total']}</li>";
		}
		
		echo '		</ul>';
		
		echo '	</div>';
		
		echo '	<div class="stats_issue_format stats_box stats_box_left">';
		
		echo '		<h4>' . __('Formats', 'App') . '</h4>';
		
		echo '		<ul>';
		
		foreach ($stats['pub']['format'] as $item) {
			
			echo "		<li>{$item['title']}: {$item['total']}</li>";
		}
		
		echo '		</ul>';
		
		echo '	</div>';
		
		echo '	<div class="stats_issue_layout stats_box stats_box_left">';
		
		echo '		<h4>' . __('Layout', 'App') . '</h4>';
		
		echo '		<ul>';
		
		foreach ($stats['pub']['layout'] as $item) {
			
			echo "		<li>{$item['title']}: {$item['total']}</li>";
		}
		
		echo '		</ul>';
		
		echo '	</div>';
		
		echo '	<div class="stats_issue_duplication stats_box stats_box_left">';
		
		echo '		<h4>' . __('Duplication', 'App') . '</h4>';
		
		echo '		<ul>';
		
		foreach ($stats['pub']['duplication'] as $item) {
			
			echo "		<li>{$item['title']}: {$item['total']}</li>";
		}
		
		echo '		</ul>';
		
		echo '	</div>';
		
		echo '	<div class="stats_issue_year stats_box stats_box_left">';
		
		echo '		<h4>' . __('Years', 'App') . '</h4>';
		
		echo '		<ul>';
		
		foreach ($stats['pub']['year'] as $year => $total) {
			
			echo "		<li>{$year}: {$total}</li>";
		}
		
		echo '		</ul>';
		
		echo '	</div>';
		
		echo '	<div class="stats_issue_language stats_box stats_box_left">';
		
		echo '		<h4>' . __('Languages', 'App') . '</h4>';
		
		echo '		<ul>';
		
		foreach ($stats['pub']['language'] as $item) {
			
			echo "		<li>{$item['title']}: {$item['total']}</li>";
		}
		
		echo '		</ul>';
		
		echo '	</div>';
		
		echo '</div>';
	}
	
	if (!empty($stats['list'])) {
		
		echo '<div class="stats_list">';
		
		echo '	<div class="stats_box stats_box_left">';
		
		echo '		<h4>' . __('User lists', 'App') . '</h4>';
		
		echo '		<ul>';
		
		foreach ($stats['list']['types'] as $item) {
			
			echo "		<li>{$item['title']}: {$item['total']}</li>";
		}
		
		echo '		</ul>';
		
		echo '	</div>';
		
		echo '</div>';
	}
}
?>
</aside>
