<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
    <meta charset="utf-8">

    <!-- Viewport Metatag -->
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <?php echo Theme::asset()->styles(); ?>
    <?php echo Theme::asset()->scripts(); ?>

    <title><?php echo Theme::place('title') ?></title>
</head>

<body>
    <?php echo Theme::partial('header') ?>

    <!-- Start Main Wrapper -->
    <div id="mws-wrapper">

        <?php echo Theme::partial('sidebar') ?>

        <!-- Main Container Start -->
        <div id="mws-container" class="clearfix">

            <!-- Inner Container Start -->
            <div class="container">
                <?php echo Theme::breadcrumb()->render(); ?>
                <?php echo Theme::place('content') ?>
            </div>
            <!-- Inner Container End -->

            <?php echo Theme::partial('footer') ?>

        </div>
        <!-- Main Container End -->

    </div>

    <?php echo Theme::asset()->container('footer')->scripts() ?>
    <?php echo Theme::asset()->container('macro')->scripts() ?>
    <?php echo Theme::asset()->container('embed')->scripts() ?>
</body>
</html>