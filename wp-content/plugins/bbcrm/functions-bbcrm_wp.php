<?php

//ini_set('display_errors','on');
//error_reporting(E_ALL);

include_once("functions-bbcrm_api.php");
//if(!is_admin() || !current_user_can('manage_options')){
//	show_admin_bar( false );
//}

if ( function_exists('register_sidebar') ){
    register_sidebar(array(
		'id'	=>'property-registered',
		'name'=>'Property Sidebar for Registered Users',
        'before_widget' => '<div class="sidewidget">',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));
    register_sidebar(array(
		'id'	=>'property-unregistered',
		'name'=>'Property Sidebar for Unregistered Users',
        'before_widget' => '<div class="sidewidget">',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));
    register_sidebar(array(
		'id'	=>'portfolio',
		'name'=>'Portfolio Sidebar',
        'before_widget' => '<div class="sidewidget">',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));    
    register_sidebar(array(
		'id'	=>'page',
		'name'=>'Page Sidebar',
        'before_widget' => '<div class="pagewidget">',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));        

    register_sidebar(array(
		'id'	=>'home-bl',
		'name'=>'Home Bottom Left',
        'before_widget' => '<div class="pagewidget">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));     
    register_sidebar(array(
		'id'	=>'home-bm',
		'name'=>'Home Bottom Middle',
        'before_widget' => '<div class="pagewidget">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));     
    register_sidebar(array(
		'id'	=>'home-br',
		'name'=>'Home Bottom Right',
        'before_widget' => '<div class="pagewidget">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));         
}


if ( function_exists('register_nav_menus') )
	register_nav_menus(array(
			'main_nav' => 'Main Navigation',
			'footer_nav' => 'Footer Site Map',
			'sec_nav' => 'Secondary Navigation',

		));    
?>
<?php
function widget_mytheme_search() {
?>
<h2>Search</h2>
<form id="searchform" method="get" action="<?php bloginfo('home'); ?>/"> <input type="text" value="type, hit enter" onfocus="if (this.value == 'type, hit enter') {this.value = '';}" onblur="if (this.value == '') {this.value = 'type, hit enter';}" size="18" maxlength="50" name="s" id="s" /> </form> 
<?php
}
if ( function_exists('register_sidebar_widget') )
    register_sidebar_widget(__('Search'), 'widget_mytheme_search');
?>
<?php
add_filter('comments_template', 'legacy_comments');
function legacy_comments($file) {
	if(!function_exists('wp_list_comments')) : // WP 2.7-only check
		$file = TEMPLATEPATH . '/legacy.comments.php';
	endif;
	return $file;
}


//WP-PROPERTY OVERWRITES

  function property_overview_image($args = '') {
    global $wpp_query, $property;
    $thumbnail_size = $wpp_query['thumbnail_size'];

    $defaults = array(
      'return' => 'false',
      'image_type' => $thumbnail_size,
    );
    $args = wp_parse_args( $args, $defaults );

    /* Make sure that a feature image URL exists prior to committing to fancybox */
    if($wpp_query['fancybox_preview'] == 'true' && !empty($property['featured_image_url'])) {
      $thumbnail_link = $property['featured_image_url'];
      $link_class = "fancybox_image";
    } else {
      $thumbnail_link = $property['permalink'];
    }

    $image = wpp_get_image_link($property['featured_image'], $thumbnail_size, array('return'=>'array'));

    if(!empty($image)) {
      ob_start();
      ?>
      <div class="property_image" data="functions"><a href="<?php echo $thumbnail_link; ?>" class="property_overview_thumb property_overview_thumb_<?php echo $thumbnail_size; ?> <?php echo $link_class; ?> thumbnail" rel="properties" ><img src="<?php echo $image['link']; ?>" style="width:<?php echo $image['width']; ?>px;height:<?php echo $image['height']; ?>px;" /></a></div>
      <?php
      $html = ob_get_contents();
      ob_end_clean();
    } else {
      $html = '';
    }
    if($args['return'] == 'true') {
      return $html;
    } else {
      echo $html;
    }
  }



if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}


/* 
XML-RPC endpoint to add an array of terms to a taxonomy
*/
function ibb_addCategories( $args ) {
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape( $args );

    $blog_id  = $args[0];	
    $username = $args[1];
    $password = $args[2];
    $data = $args[3];

  if ( ! $user = $wp_xmlrpc_server->login( $username, $password ) ){
		write_log($wp_xmlrpc_server->error);
        return $wp_xmlrpc_server->error;
	}

	foreach ($data["categories"] as $category){
		if(!term_exists($category,$data["taxonomy"])){
			wp_insert_term($category,$data["taxonomy"]);
		}
		$termrel = wp_set_post_terms( $data["post_id"], $category, $data["taxonomy"],TRUE);
		write_log($termrel);
	}
}

function ibb_addNewUser( $args ){
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape( $args );

    $blog_id  = $args[0];	
    $username = $args[1];
    $password = $args[2];
    $data = $args[3];

  if ( ! $user = $wp_xmlrpc_server->login( $username, $password ) ){
		write_log($wp_xmlrpc_server->error);
        return $wp_xmlrpc_server->error;
	}
	$user_id = username_exists( $user_name );
	if ( !$user_id and email_exists($user_email) == false ) {
		
		$user_data = array(
		                'ID' => '',
		                'user_login' => $data["email"],
		                'user_pass'=>$data["password"],
		                'display_name' => sanitize_text_field($data["first_name"])." ".sanitize_text_field($data["last_name"]),
		                'user_nicename'=>sanitize_text_field($data["first_name"])." ".sanitize_text_field($data["last_name"]),
		                'first_name' => sanitize_text_field($data["first_name"]),
		                'last_name' => sanitize_text_field($data["last_name"]),
		                'user_email' => $data["email"],
		                'role' => 'buyer'
		            );      
		$user_id = wp_insert_user( $user_data );
		
		return $user_id;
	}
	
	
	
}



function ibb_new_xmlrpc_methods( $methods ) {
    $methods['ibb.addCategories'] = 'ibb_addCategories';
    $methods['ibb.addNewUser'] = 'ibb_addNewUser';
    return $methods;   
}
add_filter( 'xmlrpc_methods', 'ibb_new_xmlrpc_methods');


// Register Theme Features
function custom_theme_features()  {

	// Add theme support for Translation
	load_theme_textdomain( 'brokernet', get_template_directory() . '/language' );	
}

// Hook into the 'after_setup_theme' action
add_action( 'after_setup_theme', 'custom_theme_features' );
include_once('functions-bbcrm_template.php');

function decode_slug($title) {
    return urldecode($title);
}
add_filter('sanitize_title', 'decode_slug');

function cfp($atts, $content = null) {
    extract(shortcode_atts(array( "id" => "", "title" => "", "pwd" => "","html_id"=>"" ), $atts));

    if(empty($id) || empty($title)) return "";

    $cf7 = do_shortcode('[contact-form-7 id="' . $id . '" title="' . $title . '" html_id="' . $html_id . '"]');

    $pwd = explode(',', $pwd);
    foreach($pwd as $p) {
        $p = trim($p);

        $cf7 = preg_replace('/<input type="text" name="' . $p . '"/usi', '<input type="password" name="' . $p . '"', $cf7);
    }

    return $cf7;
}
add_shortcode('cfp', 'cfp');

?>
<?php 
add_action( 'init', 'add_listing_type' );

function add_listing_type(){

	$labels = array(
		'name'               => _x( 'Listings', 'post type general name', 'bbcrm' ),
		'singular_name'      => _x( 'Listing', 'post type singular name', 'bbcrm' ),
		'menu_name'          => _x( 'Listings', 'admin menu', 'bbcrm' ),
		'name_admin_bar'     => _x( 'Listing', 'add new on admin bar', 'bbcrm' ),
		'add_new'            => _x( 'Add New', 'listing', 'bbcrm' ),
		'add_new_item'       => __( 'Add New Listing', 'bbcrm' ),
		'new_item'           => __( 'New Listing', 'bbcrm' ),
		'edit_item'          => __( 'Edit Listing', 'bbcrm' ),
		'view_item'          => __( 'View Listing', 'bbcrm' ),
		'all_items'          => __( 'All Listings', 'bbcrm' ),
		'search_items'       => __( 'Search Lsitings', 'bbcrm' ),
		'parent_item_colon'  => __( 'Parent Listings:', 'bbcrm' ),
		'not_found'          => __( 'No listings found.', 'bbcrm' ),
		'not_found_in_trash' => __( 'No listings found in Trash.', 'bbcrm' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'listing' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'slug', 'excerpt', 'comments' )
	);
	
	register_post_type( 'listing', $args ); 
}

function wpd_listing_404_template( $template = '' ){
    global $wp_query;
    if( isset($wp_query->query['listing']) || $wp_query->_post_type=='listing'        ){
		$template = plugin_dir_path(__FILE__).'templates/page-listing.php';
    }
    return $template;
}
add_filter( '404_template', 'wpd_listing_404_template' );

add_shortcode('search-listings', 'fnSearchListings');   
function fnSearchListings($attr, $content)
{        
    ob_start();  
    get_template_part('home', 'search');  
    $ret = ob_get_contents();  
    ob_end_clean();  
    return $ret;    
}
function show_sidebar( $atts ) {
	   ob_start();  
    get_sidebar($atts['name']); 
    $ret = ob_get_contents();  
    ob_end_clean();  
    return $ret;    
	
}
add_shortcode( 'showsidebar', 'show_sidebar' );


function kill_theme_wpse_188906($themes) {
//print_r($themes);
foreach($themes as $name=>$data){
//print_r($name);
if(!strpos($name,'-child')){
  unset($themes[$name]);
}}
  return $themes;
}
add_filter('wp_prepare_themes_for_js','kill_theme_wpse_188906');

?>
