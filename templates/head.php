<!DOCTYPE html>
<!--[if lt IE 7]>  <html class="no-js ie ie6 lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>     <html class="no-js ie ie7 lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>     <html class="no-js ie ie8 lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9]>     <html class="no-js ie ie9 lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php wp_title('-'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="google-site-verification" content="4CtGqegjIcOrTx8_RNPy7emG4eRHp0pXk93tnf5RBF0" />
	<?php wp_head(); ?>
	<!--[if lt IE 9]>
		<script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendor/respond.min-1.4.2.js"></script>
	<![endif]-->	
	<script type="text/javascript">
		var MTUserId='e38c188d-c42a-4171-9c05-119d11cba819';
		var MTFontIds = new Array();
		MTFontIds.push("710294"); // Neue Helvetica® W01 43 Extended Light 
		(function() {
			var mtTracking = document.createElement('script');
			mtTracking.type='text/javascript';
			mtTracking.async='true';
			mtTracking.src=('https:'==document.location.protocol?'https:':'http:')+'//fast.fonts.net/lt/trackingCode.js';
			
			(document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(mtTracking);
		})();
	</script>
	<link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name'); ?> Feed" href="<?php echo esc_url(get_feed_link()); ?>">
</head>
