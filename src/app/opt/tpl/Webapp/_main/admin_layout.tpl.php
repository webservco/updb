<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->
	<head>
		<meta charset="utf-8">
		
		<title>
			<?php echo $strings['title']; ?> - <?php echo __('Admin'); ?> - <?php echo $strings['title_app']; ?>
		</title>
		
		<meta name="description" content="">
		
		<meta name="viewport" content="width=device-width">
	
		<link rel="stylesheet" href="<?php echo $url['skin']; ?>css/normalize.css">
		<link rel="stylesheet" href="<?php echo $url['skin']; ?>css/main.css">
		<link rel="stylesheet" href="<?php echo $url['skin']; ?>css/admin_layout.css">
		
		<script src="<?php echo $url['scripts']; ?>vendor/modernizr-2.6.2.min.js"></script>
		
	</head>
	<body>
		<!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

	<?php echo $tpl_admin_header; ?>
	
	<main>

		<?php echo $tpl_admin_left; ?>
		
		<section id="content">
			<?php echo $tpl_content; ?>
	
		</section>
		
		<?php echo $tpl_admin_right; ?>
	
	</main>

	<?php echo $tpl_admin_footer; ?>

	<?php echo $tpl_admin_scripts; ?>

	</body>
</html>
