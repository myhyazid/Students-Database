<!doctype html public "-//W3C//DTD html 4.0 //en">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>new db | <?php echo $x->TableTitle; ?></title>

		<script src="resources/jquery/js/jquery.min.js"></script>
		<script>var $j = jQuery.noConflict();</script>
		<script src="resources/lightbox/js/prototype.js"></script>
		<script src="resources/lightbox/js/scriptaculous.js?load=effects,builder,dragdrop,controls"></script>
		<script src="resources/lightbox/js/lightbox.js"></script>
		<script src="common.js.php"></script>

		<link rel="stylesheet" type="text/css" href="resources/lightbox/css/lightbox.css" media="screen">
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="dynamic.css.php">
	</head>
	<body>
		<!-- Add header template below here .. -->

		<?php if(!$_REQUEST['Embedded']){ echo htmlUserBar(); } ?>
		<!-- process notifications -->
		<?php if(function_exists('showNotifications')) echo showNotifications(); ?>
