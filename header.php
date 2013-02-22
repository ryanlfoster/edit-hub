<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	
	<meta charset="<?php bloginfo('charset'); ?>" />

	<?php if (is_search()) { ?>
	   <meta name="robots" content="noindex, nofollow" /> 
	<?php } ?>

	<title>
		   <?php bloginfo('name'); ?><?php wp_title(); ?>
	</title>
	
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/fullcalendar.css" type="text/css" media="screen"  />
	
	<!--[if lt IE 9]><link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/ie.css" type="text/css" /><![endif]-->
	<!--[if IE 8]><link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/ie8-only.css" type="text/css" /><![endif]-->
	
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php wp_head(); ?>
	
	<script type='text/javascript'>
	/* <![CDATA[ */
		jQuery(document).ready(function() {
			jQuery('#calendar').fullCalendar({
				events: ehubeventcal.events
			});
		});
	/* ]]> */
	</script>

	<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
</head>

<body <?php body_class(); ?>>
	
	<div id="page-wrap">

		<div id="header">
			<h1 id="logo"><a href="<?php echo home_url() ?>/"><img src="<?php echo get_template_directory_uri(); ?>/images/header-logo.jpg" alt="EDIT HUB" /></a></h1>
		</div>
				
		<?php wp_nav_menu( array( 'theme_location' => 'main-nav-menu', 'menu_class' => 'hub-nav', ) ); ?>		
		<div style="clear: both;"></div>
