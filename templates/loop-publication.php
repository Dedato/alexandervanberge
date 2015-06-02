<?php if (have_posts()) {
	while (have_posts()) : the_post(); ?>
		<article <?php post_class('col-xs-6 col-sm-3 col-md-3'); ?>>
			<?php
			// Variables
			$pub_date   = DateTime::createFromFormat('Ymd', get_field('publication_date'));
			$pub_source = get_field('publication_source');
			$pub_image 	= get_field('publication_image');
			$pub_file 	= get_field('publication_file');
			// Sizes
			$img_alt    = $pub_image['alt'];
			$img_sm_src = $pub_image['sizes']['photo-landscape-sm'];
			$img_md_src = $pub_image['sizes']['photo-landscape-md'];
			// Retina Images
			if (function_exists('wr2x_get_retina_from_url')) {
				$img_sm_2x_src 	= wr2x_get_retina_from_url($img_sm_src);
				$img_md_2x_src 	= wr2x_get_retina_from_url($img_md_src);
			}
			if($pub_image && $pub_file) { ?>
				<div class="effect">
					<div class="entry-image">
						<picture>
							<!--[if IE 9]><video style="display: none;"><![endif]-->
							<source srcset="<?php echo $img_sm_src; if($img_sm_2x_src){echo ', '.$img_sm_2x_src.' 2x';} ?>" media="(min-width:510px)">
							<source srcset="<?php echo $img_md_src; if($img_md_2x_src){echo ', '.$img_md_2x_src.' 2x';} ?>">
							<!--[if IE 9]></video><![endif]-->
							<img srcset="<?php echo $img_sm_src; if($img_sm_2x_src){echo ', '.$img_sm_2x_src.' 2x';} ?>" alt="<?php echo $img_alt; ?>" />
						</picture>
					</div>
					<div class="overlay">
						<a class="expand" href="<?php echo $pub_file['url']; ?>" title="<?php _e('View publication:','alexandervanberge'); echo ' '. $pub_file['title']; ?>" target="_blank"><i class="icon-eye"></i></a>
						<a class="close-overlay hidden">x</a>
					</div>
				</div>
			<?php } ?>
			<h5 class="entry-title"><?php the_title(); ?></h5>
			<!--<h6 class="pub-date"><?php echo $pub_date->format('d M Y'); ?></h6>-->
		</article>
	<?php $i++;
	endwhile; ?>
<?php } else { ?>
	<div class="alert alert-warning">
		<?php _e('Sorry, no items found', 'alexandervanberge'); ?>
	</div>
<?php } ?>