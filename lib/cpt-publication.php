<?php
/* ==========================================================================
   Post Type
   ========================================================================== */
   
function publication_init() {
	register_post_type('publication', array(
		'hierarchical'        => false,
		'public'              => true,
		'show_in_nav_menus'   => true,
		'show_ui'             => true,
		'menu_position'		 => 7,
		'supports'            => array('title', 'editor', 'excerpt'),
		'has_archive'         => 'publications',
		'query_var'           => true,
		'rewrite'             => array('slug' => 'publications'),
		'labels'              => array(
			'name'                => __( 'Publicatie' ),
			'singular_name'       => __( 'Publicaties' ),
			'add_new'             => __( 'Voeg publicatie toe' ),
			'all_items'           => __( 'Publicaties' ),
			'add_new_item'        => __( 'Voeg publicatie toe' ),
			'edit_item'           => __( 'Bewerk publicatie' ),
			'new_item'            => __( 'Nieuwe publicatie' ),
			'view_item'           => __( 'Bekijk publicatie' ),
			'search_items'        => __( 'Zoek publicaties' ),
			'not_found'           => __( 'Geen publicaties gevonden' ),
			'not_found_in_trash'  => __( 'Geen publicaties gevonden in prullenbak' ),
			'parent_item_colon'   => __( 'Hoofd publicatie' ),
			'menu_name'           => __( 'Publicaties' ),
		),
	));
}
/* Messages */
function publication_updated_messages( $messages ) {
	global $post;
	$permalink = get_permalink( $post );
	$messages['publicatie'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Publicatie bijgewerkt. <a target="_blank" href="%s">Bekijk publicatie</a>'), esc_url( $permalink ) ),
		2 => __('Aangepast veld bijgewerkt.'),
		3 => __('Aangepast veld verwijderd.'),
		4 => __('Publicatie bijgewerkt.'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Publicatie hersteld tot revisie van %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Publicatie gepubliceerd. <a href="%s">Bekijk publicatie</a>'), esc_url( $permalink ) ),
		7 => __('Publicatie bewaard.'),
		8 => sprintf( __('Publicatie ingediend. <a target="_blank" href="%s">Voorvertoning publicatie</a>'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9 => sprintf( __('Publicatie gepland voor: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Voorvertoning publicatie</a>'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		10 => sprintf( __('Publicatie concept bijgewerkt. <a target="_blank" href="%s">Voorvertoning publicatie</a>'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);
	return $messages;
}
add_action( 'init', 'publication_init' );
add_filter( 'post_updated_messages', 'publication_updated_messages' );



/* ==========================================================================
   Set 'posts_per_page' for publication archive
   ========================================================================== */
   
/* Posts per page based on CPT */   
function publication_posts_per_page( $query ) {
	if( is_post_type_archive('publication') ):
   	if ( $query->query_vars['post_type'] == 'publication' ) $query->query_vars['posts_per_page'] = 12;
		return $query;
	endif;	
}
if ( !is_admin() ) add_filter('pre_get_posts', 'publication_posts_per_page');