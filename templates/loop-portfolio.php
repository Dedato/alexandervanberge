<?php if (have_posts()) {
	// Get ID from newest post
	global $firstid;
		while (have_posts()) : the_post();
			// Add active class
			if($firstid == $post->ID) {
				$activeclass = ' active';
			} else {
				$activeclass = '';
			}
			/* Add term class
			$terms = get_the_terms($post->ID, 'portfolio-category');
			$terms = wp_list_pluck($terms, 'slug');*/
			?>
			<article <?php post_class('col-xs-6 col-sm-3 col-md-3' . ' ' . $activeclass); ?>>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
					<?php if( have_rows('portfolio_image') ) { ?>
						<div class="entry-image row">
							<?php
							$rows 			= get_field('portfolio_image');
							$first_row 		= $rows[0]; // get the first row
							$orientation 	= $first_row['portrait_landscape_image'];
							// 2 Portrait Images
							if ($orientation == 'portrait'){						
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
      					} ?>
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
							<?php
							// 1 Landscape Image	
							} elseif ($orientation == 'landscape'){
								$landscape_img 		= $first_row['landscape_image'];
								// Sizes
								$img_alt    = $landscape_img['alt'];
								$img_sm_src = $landscape_img['sizes']['photo-landscape-sm'];
								$img_md_src = $landscape_img['sizes']['photo-landscape-md']; 
								// Get Retina images
								if (function_exists('wr2x_get_retina_from_url')) {		
  								$img_sm_2x_src 	= wr2x_get_retina_from_url($img_sm_src);
      						$img_md_2x_src 	= wr2x_get_retina_from_url($img_md_src);
								} ?>
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
					<?php } elseif (has_term('film', 'portfolio-category')) {
						$movie 			= get_field('portfolio_movie_url');
						$movie_url 		= $movie['url'];
						$movie_embed 	= $movie['embed'];
						$movie_tn 		= $movie['thumbnail'];
						?>
						<div class="entry-image row">
							<div class="col-xs-12">
								<picture>
									<!--[if IE 9]><video style="display: none;"><![endif]-->
									<source srcset="<?php echo $movie_tn; ?>" media="(min-width:768px)">
									<source srcset="<?php echo $movie_tn; ?>">
									<!--[if IE 9]></video><![endif]-->
									<img srcset="<?php echo $movie_tn; ?>" alt="<?php echo $movie_url; ?>" />
								</picture>
							</div>	
						</div>	
					<?php } ?>
					<?php if (is_post_type_archive('library') || is_singular('library') ) { ?>
						<h5 class="entry-title"><?php the_title(); ?></h5>
					<?php } ?>
				</a>
			</article>
		<?php endwhile; ?>
<?php } else { ?>
	<div class="alert alert-warning">
		<?php _e('Sorry, no items found', 'alexandervanberge'); ?>
	</div>
<?php } ?>