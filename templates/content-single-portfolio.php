<?php
// Get taxonomy term
$terms = get_the_terms($post->ID, 'portfolio-category');
$term = array_pop($terms);
// Get ID for highlighting
global $firstid;
$firstid = get_the_ID();
?>

<div class="newest">
  <?php if (have_posts()) :
		while (have_posts()) : the_post(); ?>
	    <article <?php post_class(); ?>>
				<?php if( have_rows('portfolio_image') ) {
						// Variables
						$rows 				  = get_field('portfolio_image');
						$first_row      = $rows[0];
						$orientation 		= $first_row['portrait_landscape_image'];
						// 2 Portrait Images
						if ($orientation == 'portrait') {
							$portrait_img_left  = $first_row['portrait_image_left'];
							$portrait_img_right = $first_row['portrait_image_right'];
							// Sizes
							$img_left_alt 	  = $portrait_img_left['alt'];
							$img_left_cap 	  = $portrait_img_left['caption'];
							$img_left_md_src 	= $portrait_img_left['sizes']['photo-portrait-md'];
							$img_left_lg_src 	= $portrait_img_left['sizes']['photo-portrait-lg'];
							$img_right_alt    = $portrait_img_right['alt'];
							$img_right_cap    = $portrait_img_right['caption'];
							$img_right_md_src = $portrait_img_right['sizes']['photo-portrait-md'];
							$img_right_lg_src = $portrait_img_right['sizes']['photo-portrait-lg'];
							$share_img        = $img_left_lg_src;
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
							$img_alt    = $landscape_img['alt'];
							$img_cap    = $landscape_img['caption'];
							$img_md_src = $landscape_img['sizes']['photo-landscape-md'];
							$img_lg_src = $landscape_img['sizes']['photo-landscape-lg'];
							$share_img  = $img_lg_src;
							// Retina Images
    					if (function_exists('wr2x_get_retina_from_url')) {
    						$img_md_2x_src 	= wr2x_get_retina_from_url($img_md_src);
    						$img_lg_2x_src 	= wr2x_get_retina_from_url($img_lg_src);
    					}
						}
						// Portrait
						if ($orientation == 'portrait') { ?>
						  <div class="entry-image row">
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
						  </div>
              <div class="image-captions row">
                <div class="caption col-xs-6"><?php echo $img_left_cap; ?></div>
                <div class="caption col-xs-6"><?php echo $img_right_cap; ?></div>
              </div>
						<?php
						// Landscape
						} elseif ($orientation == 'landscape') { ?>
						  <div class="entry-image row">
								<div class="img col-xs-12">
									<picture id="img-<?php echo $i ?>">
										<!--[if IE 9]><video style="display: none;"><![endif]-->
										<source srcset="<?php echo $img_lg_src; if($img_lg_2x_src){echo ', '.$img_lg_2x_src.' 2x'; } ?>" media="(min-width:510px)">
										<source srcset="<?php echo $img_md_src; if($img_md_2x_src){echo ', '.$img_md_2x_src.' 2x'; } ?>">
										<!--[if IE 9]></video><![endif]-->
										<img srcset="<?php echo $img_lg_src; if($img_lg_2x_src){echo ', '.$img_lg_2x_src.' 2x';} ?>" alt="<?php echo $img_alt; ?>" />
									</picture>
								</div>
						  </div>
              <div class="image-captions row">
                <div class="caption col-xs-12"><?php echo $img_cap; ?></div>
              </div>	
						<?php } ?>
						
        <?php
				// If it's a Video
				} elseif (has_term('film', 'portfolio-category')) {
					$movie 			  = get_field('portfolio_movie_url');
					$movie_url 		= $movie['url'];
					$movie_embed 	= $movie['embed'];
					$movie_tn 		= $movie['thumbnail'];
					$movie_desc   = get_field('portfolio_movie_desc');
					?>
					<div class="entry-video row">
					  <div class="vid col-xs-12"><?php echo $movie_embed; ?></div>  
					</div>
					<div class="entry-content row">
					  <div class="caption col-xs-12"><?php if ($movie_desc) { echo $movie_desc; } ?></div>  
					</div>
				<?php } ?>
				
				<!-- Prev / Next post link -->
				<div class="post-navigation hidden-xs">
        	<?php next_post_link_plus( array(
      			'format' 			=> '%link',
      			'link' 				=> '<i class="icon-arrow-left-lg"></i>',
      			'date_format' => '',
      			'tooltip' 		=> 'Go to previous portfolio item',
      			'in_same_tax' => true,
      			'before'			=> '<ul class="pager"><li class="previous">',
      			'after'				=> '</li></ul>'
      		));	
      		previous_post_link_plus( array(
      			'format' 			=> '%link',
      			'link' 				=> '<i class="icon-arrow-right-lg"></i>',
      			'date_format' => '',
      			'tooltip' 		=> 'Go to next portfolio item',
      			'in_same_tax' => true,
      			'before'			=> '<ul class="pager"><li class="next">',
      			'after'				=> '</li></ul>'
      		)); ?>
        </div>
				<footer class="row">
				  <div class="col-xs-12">
					  <div id="shareme" data-url="<?php echo get_permalink(); ?>" data-text="<?php the_title(); echo ' - '; bloginfo('title'); ?>" data-img="<?php echo $share_img; ?>"></div>
				  </div>  
				</footer>
		  </article>
		<?php endwhile;
		wp_reset_query();
	endif; ?>
</div>

<?php
// Get portfolio items
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$wp_query = new WP_Query( array(	
	'post_type' 			    => 'portfolio',
	'portfolio-category'	=> $term->slug,
	'posts_per_page'		  => 8,
	'paged' 					    => $paged
)); ?>
<div id="portfolio" class="grid row">
	<?php get_template_part('templates/loop', 'portfolio'); ?>
</div>
<div class="pagination row">
	<?php /* Navigation */
	if ($wp_query->max_num_pages > 1) : ?>
		<nav class="post-nav">
			<ul class="pager">
				<li class="next"><?php next_posts_link(__('<i class="icon-arrow-right-lg"></i>', 'dedato')); ?></li>
				<li class="previous"><?php previous_posts_link(__('<i class="icon-arrow-left-lg"></i>', 'dedato')); ?></li>
			</ul>
		</nav>
	<?php endif;
	wp_reset_query(); ?>
</div>