<div class="newest row">
	<?php
	$wp_query = new WP_Query( array(
		'post_type' 					    => 'library',
		'posts_per_archive_page'	=> 1
	));
	$firstid = $wp_query->posts[0]->ID;
	library_slider(); 
	wp_reset_query(); ?>
</div>		

<?php
// Get library items
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$wp_query = new WP_Query( array(	
	'post_type' 					=> 'library',
	'posts_per_archive_page'	=> 8, // should be the same in library_posts_per_page() in: lib/cpt-houses.php
	'paged' 							=> $paged
)); ?>
	
<div id="library" class="grid row">
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