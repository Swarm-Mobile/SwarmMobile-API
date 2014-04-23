<!DOCTYPE html>
<html>
    <head>
        <?= $this->Html->charset(); ?>
        <?= $this->Html->meta('icon'); ?>
        <title><?php echo $this->fetch('title'); ?></title>
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="description" content="">
        <link rel="apple-touch-icon" href="<?= Router::url('/') ?>img/touch-icon.png" />
        <link rel="apple-touch-icon" sizes="72x72" href="<?= Router::url('/') ?>img/images/touch-icon-ipad.png" />
        <link rel="apple-touch-icon" sizes="114x114" href="<?= Router::url('/') ?>img/images/touch-icon-iphone-retina.png" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="google" content="notranslate">
        <meta http-equiv="Content-Language" content="en" />
        <?php echo $this->fetch('css'); ?>
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    </head>
    <body>
        <?php
        echo $this->fetch('content');
        echo $this->fetch('script');
        ?>
    </body>
</html>