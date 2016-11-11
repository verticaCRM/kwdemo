<?php
/*
  Plugin Name: Business Brokers CRM Integration for WordPress
  Plugin URI: http://businessbrokerscrm.com
  Description: integration plugin for the BusinessBrokersCRM platform
  Version: 1.0
  Author: BusinessBrokersCRM
  Author URI: http://businessbrokerscrm.com
  Text Domain: bbcrm
*/
//ini_set('display_errors',1);
//error_reporting(E_ALL);

global $wp_query;
include_once ("_auth.php");
include_once ("functions-bbcrm_wp.php");
include_once ("functions-bbcrm_api.php");
include_once ("class.plugintemplates.php");
include_once ("options-bbcrm.php");

show_admin_bar(false);
$bbcrm_option = get_option( 'bbcrm_settings' );

global $bbcrm_option;


function bbcrm_load_textdomain() {
  load_plugin_textdomain( 'bbcrm', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'bbcrm_load_textdomain' );

function bbcrm_set_wp_title(){
global $pagetitle;
return $pagetitle;
}
add_filter('wp_head','bbcrm_set_wp_title');

function bbcrm_enqueue_scripts(){
	//wp_enqueue_script( 'ajaxform', get_stylesheet_directory_uri() . '/js/ajaxform.js', array(), '1.0.0', true );
	wp_enqueue_script('my_script',plugin_dir_url(__FILE__)."js/lib.js", array('jquery'), '1.0.0');
	wp_enqueue_script('web-tracker',get_bloginfo('url').'/crm/webTracker.php');
	wp_enqueue_style('bbcrm',plugin_dir_url(__FILE__)."css/style.css");
	wp_enqueue_style('bbcrm',plugin_dir_url(__FILE__)."css/wp_properties.css");
 	wp_register_script( 'jquery-form', '/wp-includes/js/jquery/jquery.form.js', array('jquery') );
}
add_action( 'wp_enqueue_scripts', 'bbcrm_enqueue_scripts' );


function bbcrm_set_listing_meta(){
   global $wp_query,$listing,$listingtags;

   $is_listing = get_query_var('listing');
    if($is_listing){
      $html = '<meta name="Keywords" content="'.join(',',$listingtags).'" />'.
     '<meta name="Description" content="'.$listing->description.'" />';

      $title = $listing->c_name_generic_c;
     }
}

function bbcrm_get_loginbar(){
bbcrm_load_textdomain();
	$inctemp = plugin_dir_path(__FILE__)."templates/loginbar.php";
	ob_start();
   include($inctemp);
   return ob_get_clean();
}
add_shortcode('bbcrm_loginbar','bbcrm_get_loginbar');


function get_featured_search( $atts ){
  $a = shortcode_atts( array(
	'num'=>'4',    
	'title' => 'Business for Sale',
    'type' => 'all',
    'broker'=>'',
    'featured'=>1,
    'franchise'=>false,
    'sold'=>false,
    'collapsed'=>false
    ), $atts );
  $search = plugin_dir_path(__FILE__)."templates/home-search.php";
  ob_start();
        include($search);
        return ob_get_clean();
}
add_shortcode('featuredsearch','get_featured_search');


/**
  *Developer: Theo@BioeliteVert
  *Shortcode to get Real Estates
  *for the Commercial Page (for Sale; for Lease)
*/

function get_featured_real_estates( $atts ){

  $a = shortcode_atts( array(
    'num'=>'4',    
    'title' => 'Comercial Search',
    'type' => 'all',
    'broker'=>'',
    'featured'=>1,
    'franchise'=>false
    ), $atts );

  $search = plugin_dir_path(__FILE__)."templates/realestate-search.php";
  ob_start();
    include($search);
  return ob_get_clean();
}
add_shortcode('featuredsearch_realestate','get_featured_real_estates');




function get_featured_listings($atts){
global $a;

$a = shortcode_atts( array(
'num'=>'4',    
'franchise'=>0,
    'broker'=>'',
    'featured'=>1,
    ), $atts );

	$search = plugin_dir_path(__FILE__)."templates/home-featured.php";
	ob_start();
        include($search);
        return ob_get_clean();

}
add_shortcode('featuredlistings','get_featured_listings');


function show_visitorcontact($atts){
global $a;

$a = shortcode_atts( array(
'num'=>'4',    
'franchise'=>0,
    'broker'=>'',
    'featured'=>1,
    ), $atts );

	$search = plugin_dir_path(__FILE__)."templates/sidebar-visitor.php";
	ob_start();
        include($search);
        return ob_get_clean();

}
add_shortcode('visitorcontact','show_visitorcontact');



function get_id_search($atts){
global $a;

$a = shortcode_atts( array(
	'num'=>'4',    
	'franchise'=>0,
	'broker'=>'',
	'featured'=>1,
	'addbutton'=>true,
    ), $atts );

	$search = plugin_dir_path(__FILE__)."templates/portfolio-search.php";
	ob_start();
        include($search);
        return ob_get_clean();

}
add_shortcode('searchbyid','get_id_search');



add_filter( 'no_texturize_shortcodes', 'shortcodes_to_exempt_from_wptexturize' );
function shortcodes_to_exempt_from_wptexturize( $shortcodes ) {
    $shortcodes[] = 'featuredlistings';
	$shortcodes[] = 'featuredsearch';
    return $shortcodes;
}


add_action( 'wp_ajax_contact_to_crm', 'contact_to_crm' );
add_action( 'wp_ajax_nopriv_contact_to_crm', 'contact_to_crm' );

wp_enqueue_script('jquery'); // I assume you registered it somewhere else
wp_localize_script('jquery', 'ajax_custom', array(
   'ajaxurl' => admin_url('admin-ajax.php')
));

//////////
function contact_to_crm(){

parse_str($_REQUEST["data"],$params);

$model = ($_REQUEST["model"])?$_REQUEST["model"]:"Contacts";

if(isset($params["_wpnonce"])){

$res = x2apipost(array("_class"=>$model."/","_data"=>$params));
print_r($res);
exit;
}
}
//////////


/**
  *Developer: Theo@BioeliteVert
  *Shortcode to get Categories
  *for the 'For Sale By Industry' Page
*/
function get_for_sale_by_industry($atts){

  { // Creating a new array to get the parent categories for the child categories
    // Get all categories (kids and parents)
    $json = x2apicall(array('_class'=>'dropdowns/1000.json'));
    $buscats = json_decode($json);
    // Get parent Categories
    $json = x2apicall(array('_class'=>'dropdowns/1080.json'));
    $buscats_par = json_decode($json);
    // Get Clistings Number
    $business_categories = 'c_businesscategories=["'.trim($v).'"]';
    $json_for_clistings = x2apicall(array('_class'=>'Clistings'));
    $decoded_clistings = json_decode($json_for_clistings);

    // echo '<pre>'; print_r($decoded_clistings); echo '</pre>';

    $parent_cat = '';
    $result = '';
    $new_array = array();

    foreach ($buscats->options as $k=>$v)
    {
      foreach ($buscats_par->options as $kk=>$vv)
      {
        if( strtolower($v) == strtolower($vv) )
        {
          $parent_cat = $vv;
          $class_cat = str_replace( array('/', '&', ' '), array('_', '_', ''), strtolower(stripslashes($parent_cat)) );
        }
      }

      $i_listings = 0;
      foreach($decoded_clistings as $dec_k=>$dec_v)
      {
        if( strpos( $dec_v->c_businesscategories, '"'.$v.'"' ) !== false )
        {
          $i_listings++;
        }
      }
    // echo '<pre>'; print_r($parent_cat); echo '</pre>';


      if( strtolower($parent_cat) == strtolower($v) )
      {
        $result .= '<div class="for_sale_by_industry_parent">'.$v.' - Business For Sale'.'</div>';
      }
      else
      {
        $result .= '<a class="for_sale_by_industry_category '.$class_cat.'" data-cat="'.$v.'" href="/search/?c_businesscategories[]='.$v.'">'.$v.'('.$i_listings.')</a>';          
      }

      $new_val = $parent_cat;
      $new_array[$v] = $new_val;
    }
  }

  return $result;

}
add_shortcode('for_sale_by_industry_cats','get_for_sale_by_industry');


// Define function for applying filters

function filter_listings_obj($obj) {

	global $_REQUEST, $askingprice_params, $ownerscashflow_params, $listing_downpayment_params, $keyword, $minimum_investment, $maximum_investment, $adjusted_net_profit, $brokers, $sold_selection,
			$real_est_categories, $listing_regions, $minimum_rent, $maximum_rent,
			 $businesscategories, $franchise
			;
	


	foreach($obj as $k=>$v)
	{
		//we need to show only Sold Business 
		if ($sold_selection)
		{
			if( $v->c_sales_stage != 'Sold' )
			{
				$obj[$k] = false;
			}
		}
		else
		{
			//we need to show Business that are not Sold
			if( $v->c_sales_stage != 'Active' && $v->c_sales_stage != 'Needs Refresh' )
			{
				$obj[$k] = false;
			}
		}
		if(isset($_REQUEST["c_listing_askingprice_c"]) && !empty($_REQUEST["c_listing_askingprice_c"])){
			if( !($v->c_listing_askingprice_c >= $askingprice_params[0]) || !($v->c_listing_askingprice_c < $askingprice_params[1]) )
			{
				$obj[$k] = false;
			}
		}
		
		if(isset($_REQUEST["c_ownerscashflow"]) && !empty($_REQUEST["c_ownerscashflow"])){
			if( !($v->c_ownerscashflow >= $ownerscashflow_params[0]) || !($v->c_ownerscashflow < $ownerscashflow_params[1]) )
			{
				$obj[$k] = false;
			}
		}
		
		if(isset($_REQUEST["c_listing_downpayment_c"]) && !empty($_REQUEST["c_listing_downpayment_c"])){
			if( !($v->c_listing_downpayment_c >= $listing_downpayment_params[0]) || !($v->c_listing_downpayment_c < $listing_downpayment_params[1]) )
			{
				$obj[$k] = false;
			}
		}
		
		if(isset($_REQUEST["c_keyword_c"]) && !empty($_REQUEST["c_keyword_c"])){
			if( (string) strpos( strtolower($v->c_name_generic_c) , strtolower($keyword) ) == '' )
			{
				$obj[$k] = false;
			}
		}
		
		if(isset($_REQUEST["c_minimum_investment_c"]) && !empty($_REQUEST["c_minimum_investment_c"])){
			if( $v->c_listing_askingprice_c < $minimum_investment )
			{
				$obj[$k] = false;
			}
		}
		
		if(isset($_REQUEST["c_maximum_investment_c"]) && !empty($_REQUEST["c_maximum_investment_c"])){
			if( $v->c_listing_askingprice_c > $maximum_investment )
			{
				$obj[$k] = false;
			}
		}
		if(isset($_REQUEST["c_minimum_rent_c"]) && !empty($_REQUEST["c_minimum_rent_c"])){
			if( $v->c_real_estate_rent < $minimum_rent )
			{
				$obj[$k] = false;
			}
		}
		
		if(isset($_REQUEST["c_maximum_rent_c"]) && !empty($_REQUEST["c_maximum_rent_c"])){
			if( $v->c_real_estate_rent > $maximum_rent )
			{
				$obj[$k] = false;
			}
		}	
		
		if(isset($_REQUEST["c_adjusted_net_profit_c"]) && !empty($_REQUEST["c_adjusted_net_profit_c"])){
			if( !($v->c_financial_net_profit_c >= $adjusted_net_profit[0]) || !($v->c_financial_net_profit_c < $adjusted_net_profit[1]) )
			{
				$obj[$k] = false;
			}
		}
		if( isset($_REQUEST["c_franchise_c"]) && !empty($_REQUEST["c_franchise_c"])){
			if( trim($v->c_listing_franchise_c) !=  trim($franchise) )
			{
				$obj[$k] = false;
			}
		}
		
		if(isset($_REQUEST["c_Broker"]) && !empty($_REQUEST["c_Broker"])){

			$broker_flag = 0;
			foreach($brokers as $broker)
			{
				if( $broker == $v->assignedTo )
				{
					$broker_flag = 1;
				}
			}

			if( !$broker_flag )
			{
				$obj[$k] = false;
			}
		}	
		// If we have Categories
		if(isset($_REQUEST["c_businesscategories"]) && !empty($_REQUEST["c_businesscategories"])){

			$businesscategories_flag = 0;
			foreach($businesscategories as $businesscategory)
			{
				$businesscategory = '"'.$businesscategory.'"';
				if( strpos( $v->c_businesscategories, $businesscategory ) !== false )
				{
					$businesscategories_flag = 1;
				}
			}

			if( !$businesscategories_flag )
			{
				$obj[$k] = false;
			}
		}
		// If we have Region selected
		if(isset($_REQUEST["c_listing_region_c"]) && !empty($_REQUEST["c_listing_region_c"])){

			$listing_region_flag = 0;
			foreach($listing_regions as $listing_region)
			{
				if( trim($listing_region) == trim($v->c_listing_region_c) )
				{				
					$listing_region_flag = 1;
				}
			}

			if( !$listing_region_flag )
			{
				$obj[$k] = false;
			}
		}

		// If we have Categories
		if(isset($_REQUEST["c_real_estate_categories"]) && !empty($_REQUEST["c_real_estate_categories"])){
			
			$db_real_est_categories = explode( ',', rtrim( ltrim( $v->c_real_estate_categories, '[' ), ']' ) );

			$real_est_cat_flag = 0;
			foreach($real_est_categories as $real_est_category)
			{
				$real_est_category = strtolower(str_replace( array(' ', '/'), array('_', '_'), $real_est_category ));

				foreach($db_real_est_categories as $db_real_est_category)
				{
					$db_real_est_category = rtrim( ltrim( strtolower( str_replace( array(' ', '/'), array('_', '_'), stripslashes( $db_real_est_category ) ) ), '"' ), '"' );

					if( $real_est_category == $db_real_est_category )
					{					
						$real_est_cat_flag = 1;
					}
				}
			}

			if( !$real_est_cat_flag )
			{
				$obj[$k] = false;
			}
		}
	}

	return $obj;

}

define('MAX_LISTING_PER_PAGE', 10);
function pagination($maxPages,$p,$lpm1,$prev,$next, $max, $totalposts){
    $adjacents = 3;
    if($maxPages > 1)
    {
	   
	    $limits_start = (int)($p - 1) * $max;
	    if ($limits_start == 0)
	    {
		    $limits_start = 1;
	    }
	    $limits_end = $limits_start + $max - 1;
	    if ($limits_end > $totalposts)
	    {
		    $limits_end = $totalposts; 
	    }
	     
	    $get_query = http_build_query($_GET);
		$get_query = preg_replace('/page_no=\d*/i', '', $get_query);
		
		$pagination .="<small class='hidden-phone pageClass'>Showing <strong>$limits_start</strong> - <strong>$limits_end</strong> of <strong>$totalposts</strong></small>";
        $pagination .= "<ul class='pagination pagination-sm'>";
        //previous button
        if ($p > 1)
        {
			$pagination.= "<li><a href=\"?$get_query&page_no=$prev\"><small style='padding: 3px 0px;' class='glyphicon glyphicon-chevron-left'></small></a></li>";       
        }
        else
        {
	         //$pagination.= "<li><span class=\"disabled\"><small class='glyphicon glyphicon-chevron-left'></small></span></li>";
        } 
        if ($maxPages < 7 + ($adjacents * 2)){
            for ($counter = 1; $counter <= $maxPages; $counter++){
                if ($counter == $p)
                $pagination.= "<li class='active'><span class=\"current\">$counter</span></li>";
                else
                $pagination.= "<li><a href=\"?$get_query&page_no=$counter\">$counter</a></li> ";}
        }elseif($maxPages > 5 + ($adjacents * 2)){
            if($p < 1 + ($adjacents * 2)){
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
                    if ($counter == $p)
                    $pagination.= "<li><span class=\"current\">$counter</span></li> ";
                    else
                    $pagination.= "<li><a href=\"?$get_query&page_no=$counter\">$counter</a></li> ";
                }
                $pagination.= "<li><span>...</span></li>";
                $pagination.= "<li><a href=\"?$get_query&page_no=$lpm1\">$lpm1</a> ";
                $pagination.= "<li><a href=\"?$get_query&page_no=$maxPages\">$maxPages</a></li> ";
            }
            //in middle; hide some front and some back
            elseif($maxPages - ($adjacents * 2) > $p && $p > ($adjacents * 2)){
                $pagination.= "<li><a href=\"?$get_query&page_no=1\">1</a></li> ";
                $pagination.= "<li><a href=\"?$get_query&page_no=2\">2</a></li> ";
                $pagination.= "<li><span>...</span></li>";
                for ($counter = $p - $adjacents; $counter <= $p + $adjacents; $counter++){
                    if ($counter == $p)
                    $pagination.= "<li class='active'><span class=\"current\">$counter</span></li> ";
                    else
                    $pagination.= "<li><a href=\"?$get_query&page_no=$counter\">$counter</a></li> ";
                }
                $pagination.= "<li><span>...</span></li>";
                $pagination.= "<li><a href=\"?$get_query&page_no=$lpm1\">$lpm1</a></li> ";
                $pagination.= "<li><a href=\"?$get_query&page_no=$maxPages\">$maxPages</a></li> ";
            }else{
                $pagination.= "<li><a href=\"?$get_query&page_no=1\">1</a></li> ";
                $pagination.= "<li><a href=\"?$get_query&page_no=2\">2</a></li> ";
                $pagination.= "<li><span>...</span></li>";
                for ($counter = $maxPages - (2 + ($adjacents * 2)); $counter <= $maxPages; $counter++){
                    if ($counter == $p)
                    $pagination.= "<li class='active'><span class=\"current\">$counter</span></li> ";
                    else
                    $pagination.= "<li><a href=\"?$get_query&page_no=$counter\">$counter</a></li> ";
                }
            }
        }
        if ($p < $counter - 1)
        {
			$pagination.= "<li><a href=\"?$get_query&page_no=$next\"><small style='padding: 3px 0px;' class='glyphicon glyphicon-chevron-right'></small></a></li>";
        }
        else
        {
	       //$pagination.= "<li><span class=\"disabled\"><small class='glyphicon glyphicon-chevron-right'></small></span></li>";  
        }
        $pagination.= "</ul>\n";
    }
    return $pagination;
}

