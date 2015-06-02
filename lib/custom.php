<?php

/* ==========================================================================
   Output submenu of current page with wp_nav_menu parameter
   ========================================================================== */
   
// filter_hook function to react on sub_menu flag
function my_wp_nav_menu_objects_sub_menu( $sorted_menu_items, $args ) {
  if ( isset( $args->sub_menu ) ) {
    $root_id = 0;
    
    // find the current menu item
    foreach ( $sorted_menu_items as $menu_item ) {
      if ( $menu_item->current ) {
        // set the root id based on whether the current menu item has a parent or not
        $root_id = ( $menu_item->menu_item_parent ) ? $menu_item->menu_item_parent : $menu_item->ID;
        break;
      }
    }
    
    // find the top level parent
    if ( ! isset( $args->direct_parent ) ) {
      $prev_root_id = $root_id;
      while ( $prev_root_id != 0 ) {
        foreach ( $sorted_menu_items as $menu_item ) {
          if ( $menu_item->ID == $prev_root_id ) {
            $prev_root_id = $menu_item->menu_item_parent;
            // don't set the root_id to 0 if we've reached the top of the menu
            if ( $prev_root_id != 0 ) $root_id = $menu_item->menu_item_parent;
            break;
          } 
        }
      }
    }
 
    $menu_item_parents = array();
    foreach ( $sorted_menu_items as $key => $item ) {
      // init menu_item_parents
      if ( $item->ID == $root_id ) $menu_item_parents[] = $item->ID;
 
      if ( in_array( $item->menu_item_parent, $menu_item_parents ) ) {
        // part of sub-tree: keep!
        $menu_item_parents[] = $item->ID;
      } else if ( ! ( isset( $args->show_parent ) && in_array( $item->ID, $menu_item_parents ) ) ) {
        // not part of sub-tree: away with it!
        unset( $sorted_menu_items[$key] );
      }
    }
    
    return $sorted_menu_items;
  } else {
    return $sorted_menu_items;
  }
}
add_filter( 'wp_nav_menu_objects', 'my_wp_nav_menu_objects_sub_menu', 10, 2 );


/* ==========================================================================
   Add body class for Infinite Scroll
   ========================================================================== */
   
function my_body_class($classes) {
  if (is_post_type_archive('publication')) {
    $classes[] = 'infscroll';
  }
  return $classes;
}
add_filter('body_class','my_body_class');



/* ==========================================================================
   Use English for theme and Dutch for admin
   ========================================================================== */
   
function set_my_locale($locale) {
     $locale = ( is_admin() ) ? "nl_NL" : "en_US";
     setlocale(LC_ALL, $locale );
     return $locale;
}
add_filter( 'locale', 'set_my_locale' );


/* ==========================================================================
   Custom wp-login & wp-admin screen
   ========================================================================== */
   
/* Custom style Wordpress login page */
function wp_custom_login() { 
	echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('template_directory').'/assets/css/wp-admin.css" />'; 
}
// Change url logo Wordpress login page
function put_my_url(){
	return (get_home_url());
}
// Custom style Wordpress dashboard
function wp_custom_admin() { 
	echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('template_directory').'/assets/css/wp-admin.css" />'; 
}
add_action('login_head', 'wp_custom_login');
add_filter('login_headerurl', 'put_my_url');
add_action('admin_head', 'wp_custom_admin');   


/* ==========================================================================
   Role capabilities editor
   ========================================================================== */
   
// get the the role object
$role_object = get_role('editor');
// add $cap capability to this role object
$role_object->add_cap('edit_theme_options');
//$role_object->add_cap('wr2x_manage_media_columns');



/* ==========================================================================
   Disable Wordpress Image Upload Compression
   ========================================================================== */
   
add_filter('jpeg_quality', create_function('', 'return 100;'));


/* ==========================================================================
   Add Custom og:image with Yoast SEO plugin
   ========================================================================== */
   
function custom_og_image() {
  if (is_tax('portfolio-category')) {
    $term =	$wp_query->queried_object;
    $wp_query = new WP_Query( array(
  		'post_type' 					    => 'portfolio',
  		'portfolio-category'			=> $term->slug,
  		'posts_per_page'          => 1,
  		'posts_per_archive_page'	=> 1
  	));
  } elseif (is_tax('library-category')) {
    $term =	$wp_query->queried_object;
    $wp_query = new WP_Query( array(
  		'post_type' 					    => 'library',
  		'library-category'			  => $term->slug,
  		'posts_per_page'          => 1,
  		'posts_per_archive_page'	=> 1
  	));
  }
	if (have_posts()) :
	  $i = 0; // only first post
    while (have_posts()) : the_post();
		  if($i == 0 && have_rows('portfolio_image')) {
		    $rows = get_field('portfolio_image');
        $first_row    = $rows[0];
        $orientation 	= $first_row['portrait_landscape_image'];
        if ($orientation == 'portrait') {
          $portrait_img_left = $first_row['portrait_image_left'];
          $share_img         = $portrait_img_left['sizes']['photo-portrait-lg'];
        } elseif ($orientation == 'landscape') {
          $landscape_img 	= $first_row['landscape_image'];
          $share_img 	    = $landscape_img['sizes']['photo-landscape-lg'];
        }  
		  } elseif (has_term('film', 'portfolio-category')) {
		    $embed_link     = get_field('portfolio_movie_embed_link');
  		  if ($embed_link == 'embed') {
          $movie 			  = get_field('portfolio_movie_embed_url');
          $share_img    = $movie['thumbnail'];               
        } else {
          $movie_img 		= get_field('portfolio_movie_link_image');
					$share_img  	= $movie_img['sizes']['photo-landscape-lg'];
				}	
		  }
		  $i++;
		endwhile;
		wp_reset_query();
	endif;
  return $share_img;
}
add_filter('wpseo_opengraph_image', 'custom_og_image');

