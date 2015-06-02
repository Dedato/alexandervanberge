<div class="newest">
  <?php
  $recent = new WP_Query( array(
  	'post_type' 					    => 'portfolio',
  	'posts_per_archive_page'	=> 1
  ));
  $firstid = $recent->posts[0]->ID;
  get_recent_item($firstid); ?>
</div>

<?php
// Get portfolio items
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$wp_query = new WP_Query( array(	
	'post_type' 					    => 'portfolio',
	'posts_per_archive_page'	=> 8, // should be the same in portfolio_posts_per_page() in: lib/cpt-portfolio.php
	'paged' 							    => $paged
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