<?php
/**
 * Roots initial setup and constants
 */
function roots_setup() {
  // Make theme available for translation
  load_theme_textdomain('roots', get_template_directory() . '/lang');

  // Register wp_nav_menu() menus (http://codex.wordpress.org/Function_Reference/register_nav_menus)
  register_nav_menus(array(
    'primary_navigation' => __('Primary Navigation', 'roots'),
  ));

  // Add post thumbnails (http://codex.wordpress.org/Post_Thumbnails)
  add_theme_support('post-thumbnails');
  //set_post_thumbnail_size(150, 150, false);
  
  //add_image_size('photo-portrait-lg', 560, 747, true); // For 2 portrait images full format
  add_image_size('photo-portrait-lg', 465, 620, true); // For 2 portrait images full format
  add_image_size('photo-portrait-md', 230, 307, true);
  //add_image_size('photo-portrait-sm', 131, 175, true);
  add_image_size('photo-portrait-sm', 109, 145, true);
  add_image_size('photo-portrait-xs', 66, 88, true);

  //add_image_size('photo-landscape-lg', 1140, 747, true); // For 1 landscape image full format
  add_image_size('photo-landscape-lg', 947, 620, true); // For 1 landscape image full format
  add_image_size('photo-landscape-md', 480, 315, true);
  //add_image_size('photo-landscape-sm', 268, 175, true);
  add_image_size('photo-landscape-sm', 222, 145, true);
  add_image_size('photo-landscape-xs', 134, 88, true);
  
  // Add post formats (http://codex.wordpress.org/Post_Formats)
  // add_theme_support('post-formats', array('aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat'));

  // Tell the TinyMCE editor to use a custom stylesheet
  add_editor_style('/assets/css/editor-style.css');
}
add_action('after_setup_theme', 'roots_setup');
