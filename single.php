<?php
/* Single Portfolio */
if ( get_post_type() == 'portfolio' ) {
	get_template_part('templates/content', 'single-portfolio');
/* Single Library */
} elseif ( get_post_type() == 'library' ) {
	get_template_part('templates/content', 'single-library');
/* Everything Else */
} else {
	get_template_part('templates/content', 'single');
} ?>