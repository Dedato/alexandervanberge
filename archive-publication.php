<?php
// Get publications
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$wp_query = new WP_Query( array(	
	'post_type' 					    => 'publication',
	//'meta_key' 					    => 'publication_date',
	//'orderby' 					    => 'meta_value_num',
	'order' 							    => 'DESC',
	'posts_per_archive_page'	=> 12, // should be the same in publication_posts_per_page() in: lib/cpt-publication.php
	'paged' 							    => $paged
)); ?>
	
<div id="publications" class="grid row">
	<?php get_template_part('templates/loop', 'publication'); ?>
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