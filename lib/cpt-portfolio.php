<?php
/* ==========================================================================
   Post Type
   ========================================================================== */
   
function portfolio_init() {
	register_post_type('portfolio', array(
		'hierarchical'        => false,
		'public'              => true,
		'show_in_nav_menus'   => true,
		'show_ui'             => true,
		'menu_position'		    => 5,
		'supports'            => array('title', 'editor', 'excerpt'),
		'has_archive'         => 'portfolio',
		'query_var'           => true,
		'rewrite'             => array('slug' => 'portfolio/%category%'),
		'labels'              => array(
			'name'                => __( 'Portfolio' ),
			'singular_name'       => __( 'Portfolio item' ),
			'add_new'             => __( 'Voeg portfolio item toe' ),
			'all_items'           => __( 'Portfolio' ),
			'add_new_item'        => __( 'Voeg portfolio item toe' ),
			'edit_item'           => __( 'Bewerk portfolio item' ),
			'new_item'            => __( 'Nieuw portfolio item' ),
			'view_item'           => __( 'Bekijk portfolio item' ),
			'search_items'        => __( 'Zoek portfolio items' ),
			'not_found'           => __( 'Geen portfolio items gevonden' ),
			'not_found_in_trash'  => __( 'Geen portfolio items gevonden in prullenbak' ),
			'parent_item_colon'   => __( 'Hoofd portfolio item' ),
			'menu_name'           => __( 'Portfolio' ),
		),
	));
}
/* Messages */
function portfolio_updated_messages( $messages ) {
	global $post;
	$permalink = get_permalink( $post );
	$messages['project'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Portfolio item bijgewerkt. <a target="_blank" href="%s">Bekijk portfolio item</a>'), esc_url( $permalink ) ),
		2 => __('Aangepast veld bijgewerkt.'),
		3 => __('Aangepast veld verwijderd.'),
		4 => __('Portfolio item bijgewerkt.'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Portfolio item hersteld tot revisie van %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Portfolio item gepubliceerd. <a href="%s">Bekijk portfolio item</a>'), esc_url( $permalink ) ),
		7 => __('Portfolio item bewaard.'),
		8 => sprintf( __('Portfolio item ingediend. <a target="_blank" href="%s">Voorvertoning portfolio item</a>'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9 => sprintf( __('Portfolio item gepland voor: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Voorvertoning portfolio item</a>'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		10 => sprintf( __('Portfolio item concept bijgewerkt. <a target="_blank" href="%s">Voorvertoning portfolio item</a>'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);
	return $messages;
}
add_action( 'init', 'portfolio_init' );
add_filter( 'post_updated_messages', 'portfolio_updated_messages' );



/* ==========================================================================
   Taxonomies
   ========================================================================== */

/* Portfolio category taxonomy */
function portfolio_category_taxonomy() {
  $labels = array(
    'name'              => __('Portfolio Categori&euml;n'),
    'singular_name'     => __('Portfolio Categorie'),
    'search_items'      => __('Zoek portfolio categori&euml;n'),
    'all_items'         => __('Alle portfolio categori&euml;n'),
    'parent_item'       => __('Hoofdcategorie'),
    'parent_item_colon' => __('Hoofdcategorie:'),
    'edit_item'         => __('Bewerk categorie'),
    'update_item'       => __('Werk categorie bij'),
    'add_new_item'      => __('Voeg nieuwe categorie toe'),
    'new_item_name'     => __('Nieuwe categorie naam'),
    'menu_name'         => __('Portfolio Categori&euml;n')
  );
  $args = array(
    'labels'       		=> $labels,
    'public'            => true,
    'show_ui'      		=> true,
    'show_in_nav_menus'	=> true,
    'show_admin_column'	=> true,
    'hierarchical' 		=> true,
    'query_var'    		=> true,
    'rewrite'      		=> array('slug' => 'portfolio') // Should be same as post type slug
  );
  register_taxonomy('portfolio-category', 'portfolio', $args);
}
add_action( 'init', 'portfolio_category_taxonomy' );


/* ==========================================================================
   Rewrite Rules
   ========================================================================== */

function portfolio_permalink($permalink, $post_id, $leavename) {
	// Add %category% taxonomy term to portfolio post type slug
    if (strpos($permalink, '%category%') === FALSE) return $permalink;
        // Get post
        $post = get_post($post_id);
        if (!$post) return $permalink;

        // Get taxonomy terms
        $terms = wp_get_object_terms($post->ID, 'portfolio-category');
        if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0]))
        	$taxonomy_slug = $terms[0]->slug;
        else $taxonomy_slug = 'no-category';

    return str_replace('%category%', $taxonomy_slug, $permalink);
}
add_filter('post_link', 'portfolio_permalink', 1, 3);
add_filter('post_type_link', 'portfolio_permalink', 1, 3);


add_filter('rewrite_rules_array', 'mmp_rewrite_rules');
function mmp_rewrite_rules($rules) {
    $newRules  = array();
    $newRules['portfolio/page/([0-9]{1,})/?$'] 						= 'index.php?post_type=portfolio&paged=$matches[1]';
    $newRules['portfolio/([^/]+)/([^/]+)/?$'] 						= 'index.php?portfolio=$matches[2]';
    $newRules['portfolio/([^/]+)/([^/]+)/page/([0-9]{1,})/?$'] = 'index.php?portfolio=$matches[2]&paged=$matches[3]';
    $newRules['portfolio/([^/]+)/?$']          						= 'index.php?portfolio-category=$matches[1]';
    return array_merge($newRules, $rules);
}


/* ==========================================================================
   Pagination on Single post
   ========================================================================== */
   
add_filter('redirect_canonical','my_disable_redirect_canonical');
function my_disable_redirect_canonical( $redirect_url ) {
    if ( is_singular( 'portfolio' ) || is_singular('houses') )
    $redirect_url = false;
    return $redirect_url;
}


/* ==========================================================================
   Set 'posts_per_page' for portfolio archive
   ========================================================================== */
   
/* Posts per page based on CPT */   
function portfolio_posts_per_page( $query ) {
	if( is_post_type_archive('portfolio') ):
   	if ( $query->query_vars['post_type'] == 'portfolio' ) $query->query_vars['posts_per_page'] = 8;
		return $query;
	endif;	
}
if ( !is_admin() ) add_filter('pre_get_posts', 'portfolio_posts_per_page');



/* ==========================================================================
   Create flexslider from portfolio items
   ========================================================================== */

function portfolio_slider($term) { ?>
  <article <?php post_class('col-xs-12'); ?>>
    <?php if (have_posts()) :
      $i = 0; // Slide counter ?>
      <!-- Slides -->
      <div id="slider" class="flexslider">
  		  <ul class="slides">
  		    <?php while (have_posts()) : the_post();
  		      if( have_rows('portfolio_image') ) {
    		      // ACF repeater field
    		      $rows         = get_field('portfolio_image');
              $first_row    = $rows[0];
              $orientation 	= $first_row['portrait_landscape_image'];
  						// 2 Portrait Images
  						if ($orientation == 'portrait') {
  							$portrait_img_left  = $first_row['portrait_image_left'];
  							$portrait_img_right = $first_row['portrait_image_right'];
  							// Sizes
  							$img_left_alt 	    = $portrait_img_left['alt'];
  							$img_left_cap 	    = $portrait_img_left['caption'];
  							$img_left_md_src 	  = $portrait_img_left['sizes']['photo-portrait-md'];
  							$img_left_lg_src 	  = $portrait_img_left['sizes']['photo-portrait-lg'];
  							$img_right_alt      = $portrait_img_right['alt'];
  							$img_right_cap      = $portrait_img_right['caption'];
  							$img_right_md_src 	= $portrait_img_right['sizes']['photo-portrait-md'];
  							$img_right_lg_src 	= $portrait_img_right['sizes']['photo-portrait-lg'];
                if ($i == 0) {$share_img = $img_left_lg_src;}
                // Retina Images
      					if (function_exists('wr2x_get_retina_from_url')) {
        					// Left
      						$img_left_md_2x_src 	= wr2x_get_retina_from_url($img_left_md_src);
      						$img_left_lg_2x_src 	= wr2x_get_retina_from_url($img_left_lg_src);
      						// Right
      						$img_right_md_2x_src 	= wr2x_get_retina_from_url($img_right_md_src);
      						$img_right_lg_2x_src 	= wr2x_get_retina_from_url($img_right_lg_src);
      					}
  						} elseif ($orientation == 'landscape') {
  							$landscape_img 		= $first_row['landscape_image'];
  							// Sizes
  							$img_alt      = $landscape_img['alt'];
  							$img_cap      = $landscape_img['caption'];
  							$img_md_src 	= $landscape_img['sizes']['photo-landscape-md'];
  							$img_lg_src 	= $landscape_img['sizes']['photo-landscape-lg'];
  							if ($i == 0) {$share_img = $img_lg_src;}
  							// Retina Images
      					if (function_exists('wr2x_get_retina_from_url')) {
      						$img_md_2x_src 	= wr2x_get_retina_from_url($img_md_src);
      						$img_lg_2x_src 	= wr2x_get_retina_from_url($img_lg_src);
      					}
  						} ?>
              <li class="slide <?php echo $orientation; ?>">
    						<?php if ($orientation == 'portrait') { ?>
    						  <div class="entry-image row">
    								<?php // Load first slide normally
    								if ($i == 0) { ?>
    									<div class="img left col-xs-6">
    										<picture id="img-left-<?php echo $i ?>">
    											<!--[if IE 9]><video style="display: none;"><![endif]-->
    											<source srcset="<?php echo $img_left_lg_src; if($img_left_lg_2x_src){echo ', '.$img_left_lg_2x_src.' 2x';} ?>" media="(min-width:510px)">
    											<source srcset="<?php echo $img_left_md_src; if($img_left_md_2x_src){echo ', '.$img_left_md_2x_src.' 2x';} ?>">
    											<!--[if IE 9]></video><![endif]-->
    										  <img srcset="<?php echo $img_left_lg_src; if($img_left_lg_2x_src){echo ', '.$img_left_lg_2x_src.' 2x';} ?>" alt="<?php echo $img_left_alt; ?>" />
    										</picture>
    									</div>
    									<div class="img right col-xs-6">
    										<picture id="img-right-<?php echo $i ?>">
    											<!--[if IE 9]><video style="display: none;"><![endif]-->
    											<source srcset="<?php echo $img_right_lg_src; if($img_right_lg_2x_src){echo ', '.$img_right_lg_2x_src.' 2x';} ?>" media="(min-width:510px)">
    											<source srcset="<?php echo $img_right_md_src; if($img_right_md_2x_src){echo ', '.$img_right_md_2x_src.' 2x';} ?>">
    											<!--[if IE 9]></video><![endif]-->
    											<img srcset="<?php echo $img_right_lg_src; if($img_right_lg_2x_src){echo ', '.$img_right_lg_2x_src.' 2x';} ?>" alt="<?php echo $img_right_alt; ?>" />
    										</picture>
    									</div>
    								<?php 
    								// Lazyload rest of slides with Picturefill
    								} else { ?>
    									<div class="img left col-xs-6">
    										<span id="img-left-<?php echo $i ?>" class="lazy">
    										  <!--[if IE 9]><video style="display: none;"><![endif]-->
    											<source srcset="<?php echo $img_left_lg_src; if($img_left_lg_2x_src){echo ', '.$img_left_lg_2x_src.' 2x';} ?>" media="(min-width:510px)">
    											<source srcset="<?php echo $img_left_md_src; if($img_left_md_2x_src){echo ', '.$img_left_md_2x_src.' 2x';} ?>">
    											<!--[if IE 9]></video><![endif]-->
    											<!--[if (gt IE 8)]><!-->
    											  <img data-src="<?php echo $img_left_lg_src; if($img_left_lg_2x_src){echo ', '.$img_left_lg_2x_src.' 2x';} ?>" alt="<?php echo $img_left_alt; ?>" />
    											<![endif]-->  
    											<!--[if (lt IE 9)]>
                            <img src="<?php echo $img_left_lg_src; ?>" alt="<?php echo $img_left_alt; ?>" />
                          <![endif]-->  
    										</span>
    									</div>
    									<div class="img right col-xs-6">
    										<span id="img-right-<?php echo $i ?>" class="lazy">
    											<!--[if IE 9]><video style="display: none;"><![endif]-->
    											<source srcset="<?php echo $img_right_lg_src; if($img_right_lg_2x_src){echo ', '.$img_right_lg_2x_src.' 2x';} ?>" media="(min-width:510px)">
    											<source srcset="<?php echo $img_right_md_src; if($img_right_md_2x_src){echo ', '.$img_right_md_2x_src.' 2x';} ?>">
    											<!--[if IE 9]></video><![endif]-->
    											<!--[if (gt IE 8)]><!-->
    											  <img data-src="<?php echo $img_right_lg_src; if($img_right_lg_2x_src){echo ', '.$img_right_lg_2x_src.' 2x';} ?>" alt="<?php echo $img_right_alt; ?>" />
    											<![endif]-->  
                          <!--[if (lt IE 9)]>
                            <img src="<?php echo $img_right_lg_src; ?>" alt="<?php echo $img_right_alt; ?>" />
                          <![endif]--> 
    										</span>
    									</div>
                    <?php } ?>
                  </div>
                  <div class="image-captions row">
                    <div class="caption col-xs-6"><?php echo $img_left_cap; ?></div>
                    <div class="caption col-xs-6"><?php echo $img_right_cap; ?></div>
                  </div>	

  							<?php } elseif ($orientation == 'landscape') { ?>
  							  <div class="entry-image row">
    								<?php // Load first slide normally
    								if ($i == 0) { ?>
    									<div class="img col-xs-12">
    										<picture id="img-<?php echo $i ?>">
    											<!--[if IE 9]><video style="display: none;"><![endif]-->
    											<source srcset="<?php echo $img_lg_src; if($img_lg_2x_src){echo ', '.$img_lg_2x_src.' 2x'; } ?>" media="(min-width:510px)">
    											<source srcset="<?php echo $img_md_src; if($img_md_2x_src){echo ', '.$img_md_2x_src.' 2x'; } ?>">
    											<!--[if IE 9]></video><![endif]-->
    											<img srcset="<?php echo $img_lg_src; if($img_lg_2x_src){echo ', '.$img_lg_2x_src.' 2x';} ?>" alt="<?php echo $img_alt; ?>" />
    										</picture>
    									</div>
    								<?php // Lazyload rest of slides with Picturefill
    								} else { ?>
    									<div class="img col-xs-12">
    										<span id="img-<?php echo $i ?>" class="lazy">
    											<!--[if IE 9]><video style="display: none;"><![endif]-->
    											<source srcset="<?php echo $img_lg_src; if($img_lg_2x_src){echo ', '.$img_lg_2x_src.' 2x'; } ?>" media="(min-width:510px)">
    											<source srcset="<?php echo $img_md_src; if($img_md_2x_src){echo ', '.$img_md_2x_src.' 2x'; } ?>">
    											<!--[if IE 9]></video><![endif]-->
    											<!--[if (gt IE 8)]><!-->
    											  <img data-src="<?php echo $img_lg_src; if($img_lg_2x_src){echo ', '.$img_lg_2x_src.' 2x';} ?>" alt="<?php echo $img_alt; ?>" />
    											<![endif]-->  
                          <!--[if (lt IE 9)]>
                            <img src="<?php echo $img_lg_src; ?>" alt="<?php echo $img_alt; ?>" />
                          <![endif]-->  
    										</span>
    									</div>
    								<?php } ?>
  							  </div>
  							  <div class="image-captions row">
                    <div class="caption col-xs-12"><?php echo $img_cap; ?></div>
                  </div>
  							<?php } // end landscape
                $i++; ?>
  						</li>
  						
            <?php // If it's a Video
    				} elseif (has_term('film', 'portfolio-category')) {
    				  $embed_link     = get_field('portfolio_movie_embed_link');
    				  $movie_desc     = get_field('portfolio_movie_desc');
    				  ?>
    				  <li class="slide video">
    				    <div class="entry-video row">
      						<div class="vid col-xs-12">
      						  <?php
            				if ($embed_link == 'embed') {
                      $movie 			  = get_field('portfolio_movie_embed_url');
                      $movie_url 		= $movie['url'];
                      $movie_embed 	= $movie['embed'];
                      echo $movie_embed;                      
                    } else {
                      $movie_img 		= get_field('portfolio_movie_link_image');
                      $movie_url 		= get_field('portfolio_movie_link_url');
                      // Sizes
      								$img_alt      = $movie_img['alt'];
      								$img_md_src 	= $movie_img['sizes']['photo-landscape-md'];
      								$img_lg_src 	= $movie_img['sizes']['photo-landscape-lg'];
                      // Retina Images
            					if (function_exists('wr2x_get_retina_from_url')) {
            						$img_md_2x_src 	= wr2x_get_retina_from_url($img_md_src);
            						$img_lg_2x_src 	= wr2x_get_retina_from_url($img_lg_src);
            					} ?>	
      								<div class="effect">
              					<div class="entry-image">
              						<picture>
      											<!--[if IE 9]><video style="display: none;"><![endif]-->
      											<source srcset="<?php echo $img_lg_src; if($img_lg_2x_src){echo ', '.$img_lg_2x_src.' 2x'; } ?>" media="(min-width:510px)">
      											<source srcset="<?php echo $img_md_src; if($img_md_2x_src){echo ', '.$img_md_2x_src.' 2x'; } ?>">
      											<!--[if IE 9]></video><![endif]-->
      											<img srcset="<?php echo $img_lg_src; if($img_lg_2x_src){echo ', '.$img_lg_2x_src.' 2x';} ?>" alt="<?php echo $img_alt; ?>" />
      										</picture>
              					</div>
              					<div class="overlay">
              						<a class="expand" href="<?php echo $movie_url; ?>" title="<?php _e('Watch film at ','alexandervanberge'); echo $movie_url; ?>" target="_blank"><i class="icon-eye"></i></a>
              						<a class="close-overlay hidden">x</a>
              					</div>
              				</div>	
                    <?php } ?>
      						</div>
      					</div>
      					<div class="image-captions row">
      					  <div class="caption col-xs-12"><?php if ($movie_desc) { echo $movie_desc; } ?></div>
      					</div>
    				  </li>
    				<?php }     
          endwhile; // end while have posts ?>
        </ul>
      </div>
      <footer class="row">
        <div class="scroll-nav col-xs-4 col-xs-offset-4">
          <a class="scrollto" href="#thumb-nav" title="<?php _e('Scroll to overview','alexandervanberge'); ?>"><i class="icon-arrow-down-lg"></i></a>
        </div>
        <div class="col-xs-4">
          <?php
          $share_url = get_term_link($term, 'portfolio-category'); ?>
				  <div id="shareme" data-url="<?php echo $share_url; ?>" data-text="<?php wp_title(); ?>" data-img="<?php echo $share_img; ?>"></div>
        </div> 
      </footer>
      <!-- Thumbnails -->
      <div id="thumb-nav" class="grid row">
		      <?php while (have_posts()) : the_post();
  		      if( have_rows('portfolio_image') ) {
							// ACF repeater field
    		      $rows         = get_field('portfolio_image');
              $first_row    = $rows[0];
              $orientation 	= $first_row['portrait_landscape_image'];
							// 2 Portrait Images
							if ($orientation == 'portrait') {
								$portrait_img_left  = $first_row['portrait_image_left'];
								$portrait_img_right = $first_row['portrait_image_right'];
								// Sizes
								$img_left_alt 	  = $portrait_img_left['alt'];
								$img_left_sm_src 	= $portrait_img_left['sizes']['photo-portrait-sm'];
								$img_left_md_src 	= $portrait_img_left['sizes']['photo-portrait-md'];
								$img_right_alt    = $portrait_img_right['alt'];
								$img_right_sm_src = $portrait_img_right['sizes']['photo-portrait-sm'];
								$img_right_md_src = $portrait_img_right['sizes']['photo-portrait-md'];
								// Retina Images
      					if (function_exists('wr2x_get_retina_from_url')) {
        					// Left
      						$img_left_sm_2x_src 	= wr2x_get_retina_from_url($img_left_sm_src);
      						$img_left_md_2x_src 	= wr2x_get_retina_from_url($img_left_md_src);
      						// Right
      						$img_right_sm_2x_src 	= wr2x_get_retina_from_url($img_right_sm_src);
      						$img_right_md_2x_src 	= wr2x_get_retina_from_url($img_right_md_src);
      					}
							// 1 Landscape Image	
							} elseif ($orientation == 'landscape'){
								$landscape_img 		= $first_row['landscape_image'];
								// Sizes
								$img_alt      = $landscape_img['alt'];
								$img_sm_src 	= $landscape_img['sizes']['photo-landscape-sm'];
								$img_md_src 	= $landscape_img['sizes']['photo-landscape-md'];
								// Retina Images
      					if (function_exists('wr2x_get_retina_from_url')) {
      						$img_sm_2x_src 	= wr2x_get_retina_from_url($img_sm_src);
      						$img_md_2x_src 	= wr2x_get_retina_from_url($img_md_src);
      					}
							}	?>
							<div class="slide col-xs-6 col-sm-3 col-md-3">
								<div class="entry-image row">
									<?php if ($orientation == 'portrait') { ?>
										<div class="left col-xs-6">
    									<picture>
    										<!--[if IE 9]><video style="display: none;"><![endif]-->
    										<source srcset="<?php echo $img_left_sm_src; if($img_left_sm_2x_src){echo ', '.$img_left_sm_2x_src.' 2x';} ?>" media="(min-width:768px)">
    										<source srcset="<?php echo $img_left_md_src; if($img_left_md_2x_src){echo ', '.$img_left_md_2x_src.' 2x';} ?>">
    										<!--[if IE 9]></video><![endif]-->
    										<img srcset="<?php echo $img_left_sm_src; if($img_left_sm_2x_src){echo ', '.$img_left_sm_2x_src.' 2x';} ?>" alt="<?php echo $img_left_alt; ?>" />
    									</picture>
    								</div>
    								<div class="right col-xs-6">	
    									<picture>
    										<!--[if IE 9]><video style="display: none;"><![endif]-->
    										<source srcset="<?php echo $img_right_sm_src; if($img_right_sm_2x_src){echo ', '.$img_right_sm_2x_src.' 2x';} ?>" media="(min-width:768px)">
    										<source srcset="<?php echo $img_right_md_src; if($img_right_md_2x_src){echo ', '.$img_right_md_2x_src.' 2x';} ?>">
    										<!--[if IE 9]></video><![endif]-->
    										<img srcset="<?php echo $img_right_sm_src; if($img_right_sm_2x_src){echo ', '.$img_right_sm_2x_src.' 2x';} ?>" alt="<?php echo $img_right_alt; ?>" />
    									</picture>
    								</div>
									<?php } elseif ($orientation == 'landscape') { ?>
                    <div class="col-xs-12">
    									<picture>
    										<!--[if IE 9]><video style="display: none;"><![endif]-->
    										<source srcset="<?php echo $img_sm_src; if($img_sm_2x_src){echo ', '.$img_sm_2x_src.' 2x';} ?>" media="(min-width:768px)">
    										<source srcset="<?php echo $img_md_src; if($img_md_2x_src){echo ', '.$img_md_2x_src.' 2x';} ?>">
    										<!--[if IE 9]></video><![endif]-->
    										<img srcset="<?php echo $img_sm_src; if($img_sm_2x_src){echo ', '.$img_sm_2x_src.' 2x';} ?>" alt="<?php echo $img_alt; ?>" />
    									</picture>
										</div>
									<?php } ?>
								</div>
							</div>
            <?php // Video
            } elseif (has_term('film', 'portfolio-category')) {
              $embed_link    = get_field('portfolio_movie_embed_link');
              if ($embed_link == 'embed') {
                $movie 			  = get_field('portfolio_movie_embed_url');
                $movie_url 		= $movie['url'];
                $movie_embed 	= $movie['embed'];
                $img_alt      = $movie_url;
                $img_sm_src   = $movie['thumbnail'];
                $img_md_src   = $movie['thumbnail'];
              } else {
                $movie_img 		= get_field('portfolio_movie_link_image');
                $movie_url 		= get_field('portfolio_movie_link_url');
                // Sizes
								$img_alt      = $movie_img['alt'];
								$img_sm_src 	= $movie_img['sizes']['photo-landscape-sm'];
								$img_md_src 	= $movie_img['sizes']['photo-landscape-md'];
								// Retina Images
      					if (function_exists('wr2x_get_retina_from_url')) {
      						$img_sm_2x_src 	= wr2x_get_retina_from_url($img_sm_src);
      						$img_md_2x_src 	= wr2x_get_retina_from_url($img_md_src);
      					}
              } ?>
  						<div class="slide video col-xs-6 col-sm-3 col-md-3">
    						<div class="entry-image row">
    							<div class="col-xs-12">
    								<picture>
  										<!--[if IE 9]><video style="display: none;"><![endif]-->
  										<source srcset="<?php echo $img_sm_src; if($img_sm_2x_src){echo ', '.$img_sm_2x_src.' 2x';} ?>" media="(min-width:768px)">
  										<source srcset="<?php echo $img_md_src; if($img_md_2x_src){echo ', '.$img_md_2x_src.' 2x';} ?>">
  										<!--[if IE 9]></video><![endif]-->
  										<img srcset="<?php echo $img_sm_src; if($img_sm_2x_src){echo ', '.$img_sm_2x_src.' 2x';} ?>" alt="<?php echo $img_alt; ?>" />
  									</picture>
    							</div>	
    						</div>
  						</div>	
  					<?php } ?>
					<?php endwhile;
  				wp_reset_query(); ?>
		  </div>
    <?php endif; // end if have posts ?>
  </article>   
<?php }



/* ==========================================================================
   Show most recent portfolio post
   ========================================================================== */

function get_recent_item( $post_id ) {
	$latestpost = new WP_Query(array(
		'post_type' => 'any',
		'post__in' 	=> array($post_id)
	));
	if ($latestpost->have_posts()) :
		while ($latestpost->have_posts()) : $latestpost->the_post(); ?>
			<article <?php post_class('col-xs-12'); ?>>
				<?php // Slider
				if( have_rows('portfolio_image') ) { ?>
					<div id="slider" class="flexslider">
						<ul class="slides">
							<?php
							// Slide counter
							$i = 0;
							// First image
							$rows 								= get_field('portfolio_image');
							$first_orientation 		= $rows[0]['portrait_landscape_image'];
							if ($first_orientation == 'portrait') {
								$first_portrait_img_left  = $rows[0]['portrait_image_left'];
								$first_img_lg 					  = $first_portrait_img_left['sizes']['photo-portrait-lg']; 
							} elseif ($first_orientation == 'landscape') {	
								$first_landscape_img 		 = $rows[0]['landscape_image'];
								$first_img_lg 					  = $first_landscape_img['sizes']['photo-landscape-lg'];
							}
							// Start Loop
							while( have_rows('portfolio_image') ): the_row();
								// Variables
								$rows 				= get_field('portfolio_image');
								$orientation 		= get_sub_field('portrait_landscape_image');
								// 2 Portrait Images
								if ($orientation == 'portrait') {
									$portrait_img_left  = get_sub_field('portrait_image_left');
									$portrait_img_right = get_sub_field('portrait_image_right');
									// Sizes
									$img_left_alt 	  = $portrait_img_left['alt'];
									$img_left_md_src 	= $portrait_img_left['sizes']['photo-portrait-md'];
									$img_left_lg_src 	= $portrait_img_left['sizes']['photo-portrait-lg'];
									$img_right_alt    = $portrait_img_right['alt'];
									$img_right_md_src = $portrait_img_right['sizes']['photo-portrait-md'];
									$img_right_lg_src = $portrait_img_right['sizes']['photo-portrait-lg'];
  								// Retina Images
        					if (function_exists('wr2x_get_retina_from_url')) {
          					// Left
        						$img_left_md_2x_src 	= wr2x_get_retina_from_url($img_left_md_src);
        						$img_left_lg_2x_src 	= wr2x_get_retina_from_url($img_left_lg_src);
        						// Right
        						$img_right_md_2x_src 	= wr2x_get_retina_from_url($img_right_md_src);
        						$img_right_lg_2x_src 	= wr2x_get_retina_from_url($img_right_lg_src);
        					}
								} elseif ($orientation == 'landscape') {	
									$landscape_img 		= get_sub_field('landscape_image');
									// Sizes
									$img_alt      = $landscape_img['alt'];
									$img_md_src 	= $landscape_img['sizes']['photo-landscape-md'];
									$img_lg_src 	= $landscape_img['sizes']['photo-landscape-lg'];									
  								// Retina Images
        					if (function_exists('wr2x_get_retina_from_url')) {
        						$img_md_2x_src 	= wr2x_get_retina_from_url($img_md_src);
        						$img_lg_2x_src 	= wr2x_get_retina_from_url($img_lg_src);
        					}
								} ?>
								<li class="slide <?php echo $orientation; ?> row">
									<?php if ($orientation == 'portrait') {
										// Load first slide normally
										if ($i == 0) { ?>
											<div class="img left col-xs-6">
												<picture id="img-left-<?php echo $i ?>">
													<!--[if IE 9]><video style="display: none;"><![endif]-->
													<source srcset="<?php echo $img_left_lg_src; if($img_left_lg_2x_src){echo ', '.$img_left_lg_2x_src.' 2x';} ?>" media="(min-width:510px)">
													<source srcset="<?php echo $img_left_md_src; if($img_left_md_2x_src){echo ', '.$img_left_md_2x_src.' 2x';} ?>">
													<!--[if IE 9]></video><![endif]-->
													<img srcset="<?php echo $img_left_lg_src; if($img_left_lg_2x_src){echo ', '.$img_left_lg_2x_src.' 2x';} ?>" alt="<?php echo $img_left_alt; ?>" />
												</picture>
											</div>
											<div class="img right col-xs-6">
												<picture id="img-right-<?php echo $i ?>">
													<!--[if IE 9]><video style="display: none;"><![endif]-->
													<source srcset="<?php echo $img_right_lg_src; if($img_right_lg_2x_src){echo ', '.$img_right_lg_2x_src.' 2x';} ?>" media="(min-width:510px)">
													<source srcset="<?php echo $img_right_md_src; if($img_right_md_2x_src){echo ', '.$img_right_md_2x_src.' 2x';} ?>">
													<!--[if IE 9]></video><![endif]-->
													<img srcset="<?php echo $img_right_lg_src; if($img_right_lg_2x_src){echo ', '.$img_right_lg_2x_src.' 2x';} ?>" alt="<?php echo $img_right_alt; ?>" />
												</picture>
											</div>	
										<?php 
										// Lazyload rest of slides with Picturefill
										} else { ?>
											<div class="img left col-xs-6">
												<span id="img-left-<?php echo $i ?>" class="lazy">
												  <!--[if IE 9]><video style="display: none;"><![endif]-->
													<source srcset="<?php echo $img_left_lg_src; if($img_left_lg_2x_src){echo ', '.$img_left_lg_2x_src.' 2x';} ?>" media="(min-width:510px)">
													<source srcset="<?php echo $img_left_md_src; if($img_left_md_2x_src){echo ', '.$img_left_md_2x_src.' 2x';} ?>">
													<!--[if IE 9]></video><![endif]-->
													<!--[if (gt IE 8)]><!-->
													  <img data-src="<?php echo $img_left_lg_src; if($img_left_lg_2x_src){echo ', '.$img_left_lg_2x_src.' 2x';} ?>" alt="<?php echo $img_left_alt; ?>" />
                          <![endif]-->
    											<!--[if (lt IE 9)]>
                            <img src="<?php echo $img_left_lg_src; ?>" alt="<?php echo $img_left_alt; ?>" />
                          <![endif]-->     
												</span>
											</div>
											<div class="img right col-xs-6">
												<span id="img-right-<?php echo $i ?>" class="lazy">
													<!--[if IE 9]><video style="display: none;"><![endif]-->
													<source srcset="<?php echo $img_right_lg_src; if($img_right_lg_2x_src){echo ', '.$img_right_lg_2x_src.' 2x';} ?>" media="(min-width:510px)">
													<source srcset="<?php echo $img_right_md_src; if($img_right_md_2x_src){echo ', '.$img_right_md_2x_src.' 2x';} ?>">
													<!--[if IE 9]></video><![endif]-->
													<!--[if (gt IE 8)]><!-->
													  <img data-src="<?php echo $img_right_lg_src; if($img_right_lg_2x_src){echo ', '.$img_right_lg_2x_src.' 2x';} ?>" alt="<?php echo $img_right_alt; ?>" />
                          <![endif]-->
    											<!--[if (lt IE 9)]>
                            <img src="<?php echo $img_right_lg_src; ?>" alt="<?php echo $img_right_alt; ?>" />
                          <![endif]-->     
												</span>
											</div>
										<?php } ?>
										
									<?php } elseif ($orientation == 'landscape') {
										// Load first slide normally
										if ($i == 0) { ?>
											<div class="img col-xs-12">
												<picture id="img-<?php echo $i ?>">
													<!--[if IE 9]><video style="display: none;"><![endif]-->
													<source srcset="<?php echo $img_lg_src; if($img_lg_2x_src){echo ', '.$img_lg_2x_src.' 2x'; } ?>" media="(min-width:510px)">
													<source srcset="<?php echo $img_md_src; if($img_md_2x_src){echo ', '.$img_md_2x_src.' 2x'; } ?>">
													<!--[if IE 9]></video><![endif]-->
													<img srcset="<?php echo $img_lg_src; if($img_lg_2x_src){echo ', '.$img_lg_2x_src.' 2x';} ?>" alt="<?php echo $img_alt; ?>" />
												</picture>
											</div>
										<?php // Lazyload rest of slides with Picturefill
										} else { ?>
											<div class="img col-xs-12">
												<span id="img-<?php echo $i ?>" class="lazy">
													<!--[if IE 9]><video style="display: none;"><![endif]-->
													<source srcset="<?php echo $img_lg_src; if($img_lg_2x_src){echo ', '.$img_lg_2x_src.' 2x'; } ?>" media="(min-width:510px)">
													<source srcset="<?php echo $img_md_src; if($img_md_2x_src){echo ', '.$img_md_2x_src.' 2x'; } ?>">
													<!--[if IE 9]></video><![endif]-->
													<!--[if (gt IE 8)]><!-->
													  <img data-src="<?php echo $img_lg_src; if($img_lg_2x_src){echo ', '.$img_lg_2x_src.' 2x';} ?>" alt="<?php echo $img_alt; ?>" />
                          <![endif]-->
    											<!--[if (lt IE 9)]>
                            <img src="<?php echo $img_lg_src; ?>" alt="<?php echo $img_alt; ?>" />
                          <![endif]-->     
												</span>
											</div>
										<?php } ?>
									<?php }
									$i++; ?>
								</li>
							<?php endwhile; ?>
						</ul>
						<!-- Prev / Next post link -->
						<div class="post-navigation hidden-xs">
            	<?php
            	if ( is_tax('portfolio-category') || is_singular('portfolio') ) {
            	  $in_same_tax = true;
            	  next_post_link_plus( array(
            			'format' 			=> '%link',
            			'link' 				=> '<i class="icon-arrow-left-lg"></i>',
            			'date_format' => '',
            			'tooltip' 		=> 'Go to previous portfolio item',
            			'in_same_tax' => $in_same_tax,
            			'before'			=> '<ul class="pager"><li class="previous">',
            			'after'				=> '</li></ul>'
            		));	
            		previous_post_link_plus( array(
            			'format' 			=> '%link',
            			'link' 				=> '<i class="icon-arrow-right-lg"></i>',
            			'date_format' => '',
            			'tooltip' 		=> 'Go to next portfolio item',
            			'in_same_tax' => $in_same_tax,
            			'before'			=> '<ul class="pager"><li class="next">',
            			'after'				=> '</li></ul>'
            		));
            	} elseif ( is_archive('portfolio') ) {
              	$in_same_tax = false;
            	} ?>
            </div>	
					</div>
  
					<?php 
					// Thumbnails
					$rows = get_field('portfolio_image');
					$total = count($rows);
					if( $total > 1 ) : ?>
						<div id="thumbs" class="flexslider">
							<ul class="slides">
								<?php while( have_rows('portfolio_image') ): the_row(); 
									// Variables
									$rows 				= get_field('portfolio_image');
									$orientation 		= get_sub_field('portrait_landscape_image');
									// 2 Portrait Images
									if ($orientation == 'portrait') {
										$portrait_img_left  = get_sub_field('portrait_image_left');
										$portrait_img_right = get_sub_field('portrait_image_right');
										// Sizes
										$img_left_alt 	  = $portrait_img_left['alt'];
										$img_left_xs_src 	= $portrait_img_left['sizes']['photo-portrait-xs'];
										$img_left_sm_src 	= $portrait_img_left['sizes']['photo-portrait-sm'];
										$img_right_alt    = $portrait_img_right['alt'];
										$img_right_xs_src = $portrait_img_right['sizes']['photo-portrait-xs'];
										$img_right_sm_src = $portrait_img_right['sizes']['photo-portrait-sm'];
                    // Retina Images
          					if (function_exists('wr2x_get_retina_from_url')) {
            					// Left
          						$img_left_xs_2x_src 	= wr2x_get_retina_from_url($img_left_xs_src);
          						$img_left_sm_2x_src 	= wr2x_get_retina_from_url($img_left_sm_src);
          						// Right
          						$img_right_xs_2x_src 	= wr2x_get_retina_from_url($img_right_xs_src);
          						$img_right_sm_2x_src 	= wr2x_get_retina_from_url($img_right_sm_src);
          					}
									} elseif ($orientation == 'landscape') {	
										$landscape_img 		= get_sub_field('landscape_image');
										// Sizes
										$img_alt    = $landscape_img['alt'];
										$img_xs_src = $landscape_img['sizes']['photo-landscape-xs']; 
										$img_sm_src	= $landscape_img['sizes']['photo-landscape-sm'];
										// Get Retina images
										if (function_exists('wr2x_get_retina_from_url')) {
          						$img_xs_2x_src 	= wr2x_get_retina_from_url($img_xs_src);
          						$img_sm_2x_src 	= wr2x_get_retina_from_url($img_sm_src);
										}
									} ?>
									<li class="slide">
										<div class="row">
											<?php if ($orientation == 'portrait') { ?>
												<div class="left col-xs-6">
													<picture>
														<!--[if IE 9]><video style="display: none;"><![endif]-->
														<source srcset="<?php echo $img_left_xs_src; if($img_left_xs_2x_src){echo ', '.$img_left_xs_2x_src.' 2x';} ?>" media="(min-width:510px)">
														<source srcset="<?php echo $img_left_sm_src; if($img_left_sm_2x_src){echo ', '.$img_left_sm_2x_src.' 2x';} ?>">
														<!--[if IE 9]></video><![endif]-->
														<img srcset="<?php echo $img_left_xs_src; if($img_left_xs_2x_src){echo ', '.$img_left_xs_2x_src.' 2x';} ?>" alt="<?php echo $img_left_alt; ?>" />	
													</picture>
												</div>
												<div class="right col-xs-6">
													<picture>
														<!--[if IE 9]><video style="display: none;"><![endif]-->
														<source srcset="<?php echo $img_right_xs_src; if($img_right_xs_2x_src){echo ', '.$img_right_xs_2x_src.' 2x';} ?>" media="(min-width:510px)">
														<source srcset="<?php echo $img_right_sm_src; if($img_right_sm_2x_src){echo ', '.$img_right_sm_2x_src.' 2x';} ?>">
														<!--[if IE 9]></video><![endif]-->
														<img srcset="<?php echo $img_right_xs_src; if($img_right_xs_2x_src){echo ', '.$img_right_xs_2x_src.' 2x';} ?>" alt="<?php echo $img_right_alt; ?>" />	
													</picture>
												</div>
											<?php } elseif ($orientation == 'landscape') { ?>
												<div class="col-xs-12">
													<picture>
														<!--[if IE 9]><video style="display: none;"><![endif]-->
														<source srcset="<?php echo $img_xs_src; if($img_xs_2x_src){echo ', '.$img_xs_2x_src.' 2x'; } ?>" media="(min-width:510px)">
														<source srcset="<?php echo $img_sm_src; if($img_sm_2x_src){echo ', '.$img_sm_2x_src.' 2x'; } ?>">
														<!--[if IE 9]></video><![endif]-->
														<img srcset="<?php echo $img_xs_src; if($img_xs_2x_src){echo ', '.$img_xs_2x_src.' 2x';} ?>" alt="<?php echo $img_alt; ?>" />
													</picture>
												</div>
											<?php } ?>
										</div>
									</li>
								<?php endwhile; ?>
							</ul>
						</div>
					<?php endif;

				// If it's a Video
				} elseif (has_term('film', 'portfolio-category')) {
					$movie 			  = get_field('portfolio_movie_url');
					$movie_url 		= $movie['url'];
					$movie_embed 	= $movie['embed'];
					$movie_tn 		= $movie['thumbnail'];
					$movie_desc   = get_field('portfolio_movie_desc');
					?>
					<div class="entry-video">
						<?php echo $movie_embed; ?>
						<!-- Prev / Next post link -->
						<div class="post-navigation hidden-xs">
            	<?php
            	if ( is_tax('portfolio-category') ) {
            	  $in_same_tax = true;
            	} elseif ( is_archive('portfolio') || is_single('portfolio') ) {
              	$in_same_tax = false;
            	}  
            	next_post_link_plus( array(
          			'format' 			=> '%link',
          			'link' 				=> '<i class="icon-arrow-left-lg"></i>',
          			'date_format' => '',
          			'tooltip' 		=> 'Go to previous portfolio item',
          			'in_same_tax' => $in_same_tax,
          			'before'			=> '<ul class="pager"><li class="previous">',
          			'after'				=> '</li></ul>'
          		));	
          		previous_post_link_plus( array(
          			'format' 			=> '%link',
          			'link' 				=> '<i class="icon-arrow-right-lg"></i>',
          			'date_format' => '',
          			'tooltip' 		=> 'Go to next portfolio item',
          			'in_same_tax' => $in_same_tax,
          			'before'			=> '<ul class="pager"><li class="next">',
          			'after'				=> '</li></ul>'
          		));	?>
            </div>
					</div>
				<?php }

				// Tekst
				if ( !is_front_page() ) : ?>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<div class="entry-content">
						<?php if ($movie_desc) { echo $movie_desc; } ?>
					</div>
					<footer>
						<div id="shareme" data-url="<?php echo get_permalink(); ?>" data-text="<?php the_title(); echo ' - '; bloginfo('title'); ?>" data-img="<?php echo $first_img_lg; ?>"></div>
					</footer>
				<?php endif; ?>
				
		  </article>
		<?php endwhile;
		wp_reset_query();
	endif;
}