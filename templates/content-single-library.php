<?php
// Get ID for highlighting
global $firstid;
$firstid = get_the_ID();
// Get taxonomy term
$terms = get_the_terms($post->ID, 'library-category');
// Get first term
$term = array_pop($terms);
// Get term description
$desc = term_description($term->term_id, 'library-category');
if ($desc): ?>
  <div class="row">
    <div class="page col-xs-10 col-xs-offset-1">
      <?php echo $desc; ?>
    </div>
  </div>
<?php endif; ?>

<div class="newest row">
	<?php library_slider(); ?>
</div>

<?php
// Get portfolio items
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$wp_query = new WP_Query( array(	
	'post_type' 			=> 'library',
  'library-category'=> $term->slug,
	'posts_per_page'	=> 8,
	'paged' 					=> $paged
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