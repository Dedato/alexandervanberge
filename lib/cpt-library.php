<?php
/* ==========================================================================
   Post Type
   ========================================================================== */
   
function library_init() {
	register_post_type('library', array(
		'hierarchical'        => false,
		'public'              => true,
		'show_in_nav_menus'   => true,
		'show_ui'             => true,
		'menu_position'		    => 6,
		'supports'            => array('title', 'editor', 'excerpt'),
		'has_archive'         => 'library',
		'query_var'           => true,
		'rewrite'             => array('slug' => 'library/%library-category%'),
		'labels'              => array(
			'name'                => __( 'Library', 'alexandervanberge'),
			'singular_name'       => __( 'Library item' ),
			'add_new'             => __( 'Voeg library item toe' ),
			'all_items'           => __( 'Library', 'alexandervanberge'),
			'add_new_item'        => __( 'Voeg library item toe' ),
			'edit_item'           => __( 'Bewerk library item' ),
			'new_item'            => __( 'Nieuw library item' ),
			'view_item'           => __( 'Bekijk library item' ),
			'search_items'        => __( 'Zoek library item' ),
			'not_found'           => __( 'Geen library items gevonden' ),
			'not_found_in_trash'  => __( 'Geen library items gevonden in prullenbak' ),
			'parent_item_colon'   => __( 'Hoofd library item' ),
			'menu_name'           => __( 'Library', 'alexandervanberge'),
		),
	));
}
/* Messages */
function library_updated_messages( $messages ) {
	global $post;
	$permalink = get_permalink( $post );
	$messages['library'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Library item bijgewerkt. <a target="_blank" href="%s">Bekijk library item</a>'), esc_url( $permalink ) ),
		2 => __('Aangepast veld bijgewerkt.'),
		3 => __('Aangepast veld verwijderd.'),
		4 => __('Library item bijgewerkt.'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Library item hersteld tot revisie van %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Library item gepubliceerd. <a href="%s">Bekijk library item</a>'), esc_url( $permalink ) ),
		7 => __('Library item bewaard.'),
		8 => sprintf( __('Library item ingediend. <a target="_blank" href="%s">Voorvertoning library item</a>'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9 => sprintf( __('Library item gepland voor: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Voorvertoning library item</a>'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		10 => sprintf( __('Library item concept bijgewerkt. <a target="_blank" href="%s">Voorvertoning library item</a>'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);
	return $messages;
}
add_action( 'init', 'library_init' );
add_filter( 'post_updated_messages', 'library_updated_messages' );



/* ==========================================================================
   Taxonomies
   ========================================================================== */

/* Library category taxonomy */
function library_category_taxonomy() {
  $labels = array(
    'name'              => __('Library categori&euml;n'),
    'singular_name'     => __('Library categorie'),
    'search_items'      => __('Zoek library categori&euml;n'),
    'all_items'         => __('Alle library categori&euml;n'),
    'parent_item'       => __('Hoofdcategorie'),
    'parent_item_colon' => __('Hoofdcategorie:'),
    'edit_item'         => __('Bewerk categorie'),
    'update_item'       => __('Werk categorie bij'),
    'add_new_item'      => __('Voeg nieuwe categorie toe'),
    'new_item_name'     => __('Nieuwe categorie naam'),
    'menu_name'         => __('Library categori&euml;n')
  );
  $args = array(
    'labels'       		   => $labels,
    'public'             => true,
    'show_ui'      		   => true,
    'show_in_nav_menus'	 => true,
    'show_admin_column'	 => true,
    'hierarchical' 		   => true,
    'query_var'    		   => true,
    'rewrite'      		   => array('slug' => 'library') // Should be same as post type slug
  );
  register_taxonomy('library-category', 'library', $args);
}
add_action( 'init', 'library_category_taxonomy' );


/* ==========================================================================
   Rewrite Rules
   ========================================================================== */

function library_permalink($permalink, $post_id, $leavename) {
	// Add %category% taxonomy term to library post type slug
    if (strpos($permalink, '%library-category%') === FALSE) return $permalink;
        // Get post
        $post = get_post($post_id);
        if (!$post) return $permalink;

        // Get taxonomy terms
        $terms = wp_get_object_terms($post->ID, 'library-category');
        if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0]))
        	$taxonomy_slug = $terms[0]->slug;
        else $taxonomy_slug = 'no-category';

    return str_replace('%library-category%', $taxonomy_slug, $permalink);
}
add_filter('post_link', 'library_permalink', 1, 3);
add_filter('post_type_link', 'library_permalink', 1, 3);


add_filter('rewrite_rules_array', 'library_rewrite_rules');
function library_rewrite_rules($rules) {
    $newRules  = array();
    $newRules['library/page/([0-9]{1,})/?$'] 						= 'index.php?post_type=library&paged=$matches[1]';
    $newRules['library/([^/]+)/([^/]+)/?$'] 						= 'index.php?library=$matches[2]';
    $newRules['library/([^/]+)/([^/]+)/page/([0-9]{1,})/?$'] = 'index.php?library=$matches[2]&paged=$matches[3]';
    $newRules['library/([^/]+)/?$']          						= 'index.php?library-category=$matches[1]';
    return array_merge($newRules, $rules);
}


/* ==========================================================================
   Pagination on Single post
   ========================================================================== */
   
add_filter('redirect_canonical','library_disable_redirect_canonical');
function library_disable_redirect_canonical( $redirect_url ) {
    if ( is_singular('library') )
    $redirect_url = false;
    return $redirect_url;
}


/* ==========================================================================
   Set 'posts_per_page' for library archive
   ========================================================================== */
   
/* Posts per page based on CPT */   
function library_posts_per_page( $query ) {
	if( is_post_type_archive('library') ):
   	if ( $query->query_vars['post_type'] == 'library' ) $query->query_vars['posts_per_page'] = 8;
		return $query;
	endif;	
}
if ( !is_admin() ) add_filter('pre_get_posts', 'library_posts_per_page');




/* ==========================================================================
   Show most recent library item
   ========================================================================== */

function library_slider() { ?>
  <article <?php post_class('col-xs-12'); ?>>
		<?php if( have_rows('portfolio_image') ) : ?>
			<div id="slider" class="flexslider">
				<ul class="slides">
					<?php
					// Slide counter
					$i = 0;
					// Start Loop
					while( have_rows('portfolio_image') ): the_row();								
						// Variables
						$orientation 		= get_sub_field('portrait_landscape_image');
						// 2 Portrait Images
						if ($orientation == 'portrait') {
							$portrait_img_left  = get_sub_field('portrait_image_left');
							$portrait_img_right = get_sub_field('portrait_image_right');
							// Sizes
							$img_left_alt 	  = $portrait_img_left['alt'];
							$img_left_md_src 	= $portrait_img_left['sizes']['photo-portrait-md'];
							$img_left_lg_src 	= $portrait_img_left['sizes']['photo-portrait-lg'];
							$img_lg_h 		    = $portrait_img_left['sizes']['photo-portrait-lg-height'];
							$img_right_alt    = $portrait_img_right['alt'];
							$img_right_md_src = $portrait_img_right['sizes']['photo-portrait-md'];
							$img_right_lg_src = $portrait_img_right['sizes']['photo-portrait-lg'];
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
							$landscape_img 		= get_sub_field('landscape_image');
							// Sizes
							$img_alt 	  = $landscape_img['alt'];
							$img_md_src = $landscape_img['sizes']['photo-landscape-md'];
							$img_lg_src = $landscape_img['sizes']['photo-landscape-lg'];
							$img_lg_h 	= $landscape_img['sizes']['photo-landscape-lg-height'];
							if ($i == 0) {$share_img = $img_lg_src;}
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
			</div>

			<?php 
			// Thumbnails
			$rows = get_field('portfolio_image');
			$total = count($rows);					
			if( $total > 1 ) : ?>
				<div id="thumbs" class="flexslider hidden-xs">
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
								$img_left_alt 	    = $portrait_img_left['alt'];
								$img_left_xs_src 	  = $portrait_img_left['sizes']['photo-portrait-xs'];
								$img_left_sm_src 	  = $portrait_img_left['sizes']['photo-portrait-sm'];
								$img_right_alt      = $portrait_img_right['alt'];
								$img_right_xs_src 	= $portrait_img_right['sizes']['photo-portrait-xs'];
								$img_right_sm_src 	= $portrait_img_right['sizes']['photo-portrait-sm'];
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
								$img_alt      = $landscape_img['alt'];
								$img_xs_src 	= $landscape_img['sizes']['photo-landscape-xs']; 
								$img_sm_src	  = $landscape_img['sizes']['photo-landscape-sm'];
  							// Retina Images
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
			<?php endif; ?>	
		<?php endif; ?>
		
		<?php if ( !is_front_page() ) : ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<div class="entry-content row">
				<div class="col-xs-12 col-sm-9"><?php the_content(); ?></div>
			</div>
			<footer class="row">
			  <div class="col-xs-6">
  				<a class="more-info btn btn-primary btn-sm" title="<?php echo sprintf(__('Would you like to receive more information about &quot;%1$s&quot;?', 'alexandervanberge'), get_the_title() ); ?>">
  					<i class="icon-envelope"></i><?php _e('More info','alexandervanberge'); ?>
  				</a>
			  </div>
			  <div class="col-xs-6">
				  <div id="shareme" data-url="<?php echo get_permalink(); ?>" data-text="<?php the_title(); echo ' - '; bloginfo('title'); ?>" data-img="<?php echo $share_img; ?>"></div>
			  </div>  
			</footer>
			<!-- More information form -->
			<div class="form">
				<?php $message = sprintf(__('Would you like to receive more information about &quot;%1$s&quot;?', 'alexandervanberge'), get_the_title() ); ?>	
				<h4><?php echo $message; ?></h4>
				<?php gravity_form(1, false, false, false, array('library_title' => get_the_title() ), true); ?>
			</div>
		<?php endif; ?>
  </article>
  
<?php }
