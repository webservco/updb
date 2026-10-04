
<header>

	<article class="clearfix">

		<?php if (!empty($user['Logged'])) { ?>
			<strong><?php echo $user['Prop']['user_nickname']; ?> </strong>
			&#160;<a href="<?php echo $url['www']; ?>Users/App/Logout" title="<?php echo __('Logout'); ?>"><?php echo __('Logout'); ?> </a>
			<br /> <i><?php echo __('Administration area'); ?> </i>
		<?php } else { ?>
			<a href="<?php echo $url['www']; ?>Users/App/Account" title="<?php echo __('Login'); ?>"><?php echo __('Login'); ?> </a>
		<?php } ?>

	</article>

	<h1>
		<?php echo __('Admin'); ?> - <?php echo $strings['title']; ?>
	</h1>

	<nav>
		<?php
		if (!empty($menu['web_admin'])) {

			echo "<ul>\n";

			foreach ($menu['web_admin'] as $menu_item) {

				$a_class = (!empty($menu_item['resource']) && 0 === strpos($info['location'], $menu_item['resource'])) ? ' class="active"' : NULL;
				
				$a_href = !empty($menu_item['href']) ? " href=\"{$menu_item['href']}\"" : NULL;
				
				$a_target = !empty($menu_item['target']) ? " target=\"{$menu_item['target']}\"" : NULL;
				
				$a_title = __($menu_item['title'], 'App');
				
				echo "<li>\n";
				
				echo "<a{$a_class}{$a_href}{$a_target} title=\"{$a_title}\">{$a_title}</a>\n";
				
				if (!empty($menu_item['submenu'])) {
					
					echo "<ul>\n";
					
					foreach ($menu_item['submenu'] as $submenu_item) {
						
						$a_class = (!empty($submenu_item['resource']) && 0 === strpos($info['location'], $submenu_item['resource'])) ? ' class="active"' : NULL;
						
						$a_href = !empty($submenu_item['href']) ? " href=\"{$submenu_item['href']}\"" : NULL;
						
						$a_target = !empty($submenu_item['target']) ? " target=\"{$submenu_item['target']}\"" : NULL;
						
						$a_title = __($submenu_item['title']);
						
						echo "<li>\n";
						
						echo "<a{$a_class}{$a_href}{$a_target} title=\"{$a_title}\">{$a_title}</a>\n";
						
						echo "</li>\n";
					}
					
					echo "</ul>\n";
				}
				
				echo "</li>\n";
			}

			echo "</ul>\n";

		}
		?>
	</nav>
</header>
