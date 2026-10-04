<aside id="right">
<?php
if (!empty($stats)) {
	
	if (!empty($stats['pub'])) {
		
		echo '<div class="stats_pub">';
		
		foreach ($stats['pub']['content']['data'] as $item) {
			
			echo '<div class="stats_issue_content stats_box stats_box_right">';
			
			echo "	<h4>{$item['title']}</h4>";
			
			if (!empty($item['content'])) {
				
				echo '	<ul>';
				
				foreach ($item['content'] as $title => $total) {
					
					echo "	<li>{$title}: {$total}</li>";
				}
				
				echo '	</ul>';
			}
			
			echo '</div>';
		}
		
		echo '	<div class="stats_issue_content stats_box stats_box_right">';
		
		echo '		<h4>' . __('Other', 'App') . '</h4>';
		
		echo '		<ul>';
		
		foreach ($stats['pub']['content']['values'] as $item) {
			
			echo "		<li>{$item['title']}: {$item['total']}</li>";
		}
		
		echo '		</ul>';
		
		echo '	</div>';
		
		echo '</div>';
	}
}
?>
</aside>
