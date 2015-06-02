<?php
/*
Template Name: Homepage
*/
?>

<div class="newest row">
  <article <?php post_class('col-xs-12'); ?>>
		<?php // Slider
		if( have_rows('portfolio_image') ) { ?>
			<div id="slider" class="flexslider">
				<ul class="slides">
					<?php
					// Slide counter
					$i = 0;
					// First image
					$rows 								    = get_field('portfolio_image');
					$first_orientation 				= $rows[0]['portrait_landscape_image'];
					if ($first_orientation == 'portrait') {
						$first_portrait_img_left  	= $rows[0]['portrait_image_left'];
						$first_img_lg 					= $first_portrait_img_left['sizes']['photo-portrait-lg']; 
					} elseif ($first_orientation == 'landscape') {	
						$first_landscape_img 		= $rows[0]['landscape_image'];
						$first_img_lg 					= $first_landscape_img['sizes']['photo-landscape-lg'];
					}
					// Start Loop
					while( have_rows('portfolio_image') ): the_row();
						// Variables
						$rows 				  = get_field('portfolio_image');
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
							$img_alt    = $landscape_img['alt'];
							$img_md_src = $landscape_img['sizes']['photo-landscape-md'];
							$img_lg_src = $landscape_img['sizes']['photo-landscape-lg'];
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
		<?php } ?>
  </article>
</div>