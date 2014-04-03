<!DOCTYPE html>
<html>
	<head>
		<?= $this->Html->charset(); ?>
		<?= $this->Html->meta('icon'); ?>
		<title><?php echo $this->fetch('title'); ?></title>
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="description" content="">
		<link rel="apple-touch-icon" href="<?= Router::url('/') ?>b2b/images/touch-icon.png" />
		<link rel="apple-touch-icon" sizes="72x72" href="<?= Router::url('/') ?>b2b/images/touch-icon-ipad.png" />
		<link rel="apple-touch-icon" sizes="114x114" href="<?= Router::url('/') ?>b2b/images/touch-icon-iphone-retina.png" />
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
		<!--
		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-33942120-1']);
			_gaq.push(['_trackPageview']);

			(function() {
				var ga = document.createElement('script');
				ga.type = 'text/javascript';
				ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(ga, s);
			})();
		</script>
		-->
	</body>
</html>