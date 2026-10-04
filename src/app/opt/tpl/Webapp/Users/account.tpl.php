<article>
		
<?php if (!empty($user['Logged'])) { ?>

		<p><?php echo __('Welcome'); ?> <strong><?php echo $user['Prop']['user_nickname']; ?></strong>!</p>
		
		<p class="clearfix">
			<a href="<?php echo $url['www']; ?>Users/App/ChangePassword" title="<?php echo __('Change password'); ?>"><?php echo __('Change password'); ?></a>

            <?php /* USER_EDIT_DISABLED Not currently used (no data to edit).)
            <br />
			<a href="<?php echo $url['www']; ?>Users/App/Edit" title="<?php echo __('Edit account'); ?>"><?php echo __('Edit account'); ?></a>
            */ ?>
		</p>
		
		<p class="clearfix">
			<a class="button" href="<?php echo $url['www']; ?>Users/App/Logout" title="<?php echo __('Logout'); ?>"><?php echo __('Logout'); ?></a>
		</p>
<?php } else { ?>
		
		<p><?php echo $strings['no_data']; ?></p>
<?php } ?>

</article>
