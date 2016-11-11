<?php

// View Template in Footer

add_theme_support( 'post-thumbnails' ); 
function show_template() {
     if( is_super_admin() ){
         global $template;
        print_r($template);
     } 
 }
add_action('wp_footer', 'show_template');



