	<header>
	
		<div id="header_user">
		
		<?php if (!empty($user['Logged'])) { ?>
				<strong><?php echo $user['Prop']['user_nickname']; ?></strong> <a href="<?php echo $url['www']; ?>Users/App/Logout" title="<?php echo __('Logout', 'App'); ?>"><?php echo __('Logout', 'App'); ?></a>
		<?php } else { ?>
				<a href="<?php echo $url['www']; ?>Users/App/Register" title="<?php echo __('Register', 'App'); ?>"><?php echo __('Register', 'App'); ?></a>
				 <?php echo __('or', 'App'); ?> 
				<a href="<?php echo $url['www']; ?>Users/App/Account" title="<?php echo __('Login', 'App'); ?>"><?php echo __('Login', 'App'); ?></a>
		<?php } ?>
		
		</div>
		
		<h1><?php echo $strings['title_app']; ?></h1>
		
		<nav>
		<?php
		if (!empty($menu['web'])) {
			
			echo "<ul>\n";
			
			foreach ($menu['web'] as $menu_item) {
				
				$a_class = NULL;
				
				if (!empty($menu_item['resource'])) {
					
					if (0 === strpos("{$info['location']}{$info['path']}", $menu_item['resource']) || (empty($info['location']) && '/' == $menu_item['resource'])) {
						$a_class = ' class="active"';	
					}
				}
				
				$a_href = !empty($menu_item['href']) ? " href=\"{$menu_item['href']}\"" : NULL;
				
				$a_target = !empty($menu_item['target']) ? " target=\"{$menu_item['target']}\"" : NULL;
				
				$a_title = __($menu_item['title'], 'App');
				
				echo "<li>\n";
				
				echo "<a{$a_class}{$a_href}{$a_target} title=\"{$a_title}\">{$a_title}</a>\n";
				
				if (!empty($menu_item['submenu'])) {
					
					echo "<ul>\n";
					
					foreach ($menu_item['submenu'] as $submenu_item) {
						
						$a_class = NULL;
				
						if (!empty($su['resource'])) {
							
							if (0 === strpos($info['location'], $submenu_item['resource']) || (empty($info['location']) && '/' == $submenu_item['resource'])) {
								$a_class = ' class="active"';	
							}
						}
						
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
