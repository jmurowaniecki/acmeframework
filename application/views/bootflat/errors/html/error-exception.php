<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo APP_NAME ?></title>

    <!-- Core Scripts - Include with every page -->
    <script src="<?php echo URL_JS ?>/jquery-2.1.3.min.js"></script>
    <script src="<?php echo URL_CSS ?>/bootstrap/js/bootstrap.min.js"></script>

    <!-- App Scripts - Include with every page -->
    <script src="<?php echo URL_JS ?>/app-functions.js"></script>

    <!-- CSS Assets - Include with every page -->
    <link href="<?php echo URL_CSS ?>/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo URL_CSS ?>/bootflat/css/bootflat.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo URL_CSS ?>/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

    <!-- CSS Template - Override other styles -->
    <link href="<?php echo URL_TEMPLATE ?>/styles.css" rel="stylesheet" type="text/css" />

    <style>
        body { padding: 30px; }
        h1, h2, h3, h4 { margin: 0 0 20px; }
        h5 { margin: 0 0 5px; }
        .panel-body { padding: 30px; }
    </style>

</head>

<body>

    <div class="panel panel-default">

        <div class="panel-body">

    		<h3 class="hidden-lg hidden-md"><?php echo lang('An Error was encountered')?></h3>
    		<h2 class="hidden-xs hidden-sm"><?php echo lang('An Error was encountered')?></h2>
            <h4><?php echo lang('Do not worry, this problem has been already forwarded to correction.')?></h4>

            <?php if(ENVIRONMENT != 'production') : ?>

            <div class="text-danger" style="margin-bottom:20px">

                <h5><strong><?php echo lang('Tecnical details')?></strong></h5>

                <div>&bull;&nbsp;<?php echo lang('Type') ?>: <?php echo get_class($exception); ?></div>
                <div>&bull;&nbsp;<?php echo lang('Message') ?>: <?php echo $exception->getMessage(); ?></div>
                <div>&bull;&nbsp;<?php echo lang('Filename') ?>: <?php echo $exception->getFile(); ?></div>
                <div>&bull;&nbsp;<?php echo lang('Line Number') ?>: <?php echo $exception->getLine(); ?></div>

                <?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>

                <h5 style="margin-top: 20px;"><strong><?php echo lang('Backtrace') ?></strong></h5>

                <?php foreach ($exception->getTrace() as $error): ?>
                <?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>

                    <div style="margin-bottom: 20px;">
                        <div>&bull;&nbsp;<?php echo lang('File') ?>: <?php echo $error['file']; ?></div>
                        <div>&bull;&nbsp;<?php echo lang('Line') ?>: <?php echo $error['line']; ?></div>
                        <div>&bull;&nbsp;<?php echo lang('Function') ?>: <?php echo $error['function']; ?></div>
                    </div>

                <?php endif ?>
                <?php endforeach ?>

                <?php endif ?>

            </div>

            <?php endif; ?>

    		<?php $url = $this->session->userdata('url_default') ? $this->session->userdata('url_default') : URL_ROOT; ?>
    		<a href="<?php echo $url ?>" class="btn btn-success btn-lg"><?php echo lang('Initial page')?> <i class="fa fa-arrow-circle-right fa-fw"></i></a>

    	</div>

    </div>

</body>

</html>