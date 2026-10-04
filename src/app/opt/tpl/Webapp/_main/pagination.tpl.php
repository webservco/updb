<?php
if (!empty($items['count'])) {

	$url_pag = "{$url['www']}{$info['location']}{$info['path_pagination']}page/";

	$prev = $items['count']['page'] - 1;
	$next = $items['count']['page'] + 1;

	echo "<div id=\"pagination\"><p>";

	if (1 != $items['count']['page']) {

		echo "<a href=\"{$url_pag}1\" title=\"&#139;&#139;\">&#139;&#139;</a>&#160;";
	}

	if (0 < $prev) {

		echo "<a href=\"{$url_pag}{$prev}\" title=\"&#139;\">&#139;</a>&#160;";
	}

	if (1 != $items['count']['pages']) {

		echo "<strong>{$items['count']['page']} / {$items['count']['pages']}</strong>&#160;";

	}

	if ($next <= $items['count']['pages']) {

		echo "<a href=\"{$url_pag}{$next}\" title=\"&#155;\">&#155;</a>&#160;";
	}

	if ($items['count']['pages'] != $items['count']['page']) {

		echo "<a href=\"{$url_pag}{$items['count']['pages']}\" title=\"&#155;&#155;\">&#155;&#155;</a>&#160;";
	}

	echo "</p></div>";
}
?>