<!DOCTYPE html>
<html lang="<?php echo $i18n['lang']; ?>">
	<head>
		<meta charset="utf-8">
		
		<title>
			<?php echo $strings['title']; ?> - <?php echo $strings['title_app']; ?>
		</title>
		
		<meta name="description" content="">
		
		<meta name="viewport" content="width=device-width">
		
		<?php /*
		<link rel="stylesheet" href="<?php echo $url['skin']; ?>css/normalize.css">
		<link rel="stylesheet" href="<?php echo $url['skin']; ?>css/main.css">
		*/ ?>
		<link rel="stylesheet" href="<?php echo $url['skin']; ?>css/reset.css">
		
		<link rel="stylesheet" href="<?php echo $url['skin']; ?>css/layout.css">
		
		
		<link rel="stylesheet" href="<?php echo $url['skin']; ?>ui-lightness/jquery-ui-1.10.3.custom.css">
		<link rel="stylesheet" href="<?php echo $url['skin']; ?>css/photoswipe.css">
		
		<?php /*
		<script src="<?php echo $url['scripts']; ?>vendor/modernizr-2.6.2.min.js"></script>
		*/?>
		
		<!--[if lt IE 9]>
		<script src="<?php echo $url['scripts']; ?>vendor/html5shiv.js"></script>
		<![endif]-->
		
	</head>
	<body>
		<!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

		<?php echo $tpl_header; ?>
	
		<main>
	
			<?php echo $tpl_right; ?>
	
			<?php echo $tpl_left; ?>
		
			<section id="content">
				<?php echo $tpl_content; ?>

                <?php echo $tpl_downloads; ?>
			
				<?php echo $tpl_comments; ?>
			</section>
		
		</main>

		<?php echo $tpl_footer; ?>

		<?php echo $tpl_scripts; ?>

	</body>
</html>
