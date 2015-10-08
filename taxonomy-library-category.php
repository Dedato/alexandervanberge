<?php
// Get taxonomy term
$terms = get_the_terms($post->ID, 'library-category');
$term  = array_pop($terms);
if (term_description()): ?>
  <div class="row">
    <div class="page col-xs-10 col-xs-offset-1">
      <?php echo term_description(); ?>
    </div>
  </div>
<?php endif; ?>  
		
<div class="newest row">
	<?php 
	$items = new WP_Query( array(
		'post_type' 					    => 'library',
		'library-category'			  => $term->slug,
		'posts_per_archive_page'	=> 1
	));
	$firstid = $items->posts[0]->ID;
	if ($items->have_posts()) :
		while ($items->have_posts()) : $items->the_post();
		  library_slider();
		endwhile;
		wp_reset_postdata();
	endif; ?>
</div>

<?php
// Get library items
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$wp_query = new WP_Query( array(	
	'post_type' 					    => 'library',
	'library-category'        => $term->slug,
	'posts_per_archive_page'	=> 8, // should be the same in library_posts_per_page() in: lib/cpt-library.php
	'paged' 							    => $paged
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