<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
    <meta charset="utf-8">
    <!-- Viewport Metatag -->
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <?php echo Theme::asset()->scripts(); ?>
	<?php echo Theme::asset()->styles(); ?>
	<style>
		body{
			background-image:none;
			background-color:transparent;
		}
	</style>
    <title><?php echo Theme::place('title') ?></title>
</head>

<body>
	<?php echo Theme::place('content') ?>
</body>
</html>