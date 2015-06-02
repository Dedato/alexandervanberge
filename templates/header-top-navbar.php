<header class="banner navbar navbar-default navbar-fixed-top" role="banner">
	<div class="container">
		<div class="navbar-header row">
		  <div class="col-xs-10 col-sm-12">
			  <h1><a class="navbar-brand" href="<?php echo home_url(); ?>/"><?php bloginfo('name'); ?></a></h1>
		  </div>
		  <div class="col-xs-2 visible-xs">
  		  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
    			<span class="sr-only">Toggle navigation</span>
    			<span class="icon-bar"></span>
    			<span class="icon-bar"></span>
    			<span class="icon-bar"></span>
    		</button>
		  </div>	
		</div>
		<nav class="collapse navbar-collapse" role="navigation">
			<?php if (has_nav_menu('primary_navigation')) :
			 	wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_id' => 'hoofdmenu', 'menu_class' => 'nav navbar-nav', 'depth' => '1' ));
			 	wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_id' => 'submenu', 'menu_class' => 'sub-nav navbar-nav', 'sub_menu' => true ));
			endif; ?>
		</nav>
	</div>
</header>