<?php
// Get taxonomy term
$term =	$wp_query->queried_object;
?>

<div class="newest row">
	<?php 
	$wp_query = new WP_Query( array(
		'post_type' 					    => 'portfolio',
		'portfolio-category'			=> $term->slug,
		'posts_per_page'          => -1,
		'posts_per_archive_page'	=> -1,
    'nopaging'                => true
	));
	portfolio_slider($term);
	wp_reset_query(); ?>
</div>