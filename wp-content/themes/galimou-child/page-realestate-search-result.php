<?php
/*
Template Name: Real Estate Results
*/
session_start();
// ini_set('display_errors',true);
// error_reporting(E_ALL);

$postmeta = get_post_meta( get_the_ID() );

foreach($_REQUEST as $k=>$v){
	if( $v=='' ){
		unset($_REQUEST[$k]);
	}
}

// echo '<pre>'; print_r($_REQUEST); echo '</pre>';


// Grab our filters

$get_params = '_partial=1&_escape=0&c_is_real_estate=1';


if(isset($_REQUEST["c_real_estate_sale_c"]) && !empty($_REQUEST["c_real_estate_sale_c"])){
	if( $_REQUEST["c_real_estate_sale_c"] == 1 )
	{
		$real_estate_sale = 'c_real_estate_sale_c='.$_REQUEST["c_real_estate_sale_c"];
		$get_params .= '&'.$real_estate_sale;		
	}
}


if(isset($_REQUEST["c_real_estate_lease_c"]) && !empty($_REQUEST["c_real_estate_lease_c"])){
	if( $_REQUEST["c_real_estate_lease_c"] == 1 )
	{
		$real_estate_lease = 'c_real_estate_lease_c='.$_REQUEST["c_real_estate_lease_c"];
		$get_params .= '&'.$real_estate_lease;		
	}
}

if(isset($_REQUEST["c_keyword_c"]) && !empty($_REQUEST["c_keyword_c"])){
	$keyword = trim($_REQUEST["c_keyword_c"]);
}

if(isset($_REQUEST["c_real_estate_categories"]) && !empty($_REQUEST["c_real_estate_categories"])){
	foreach($_REQUEST["c_real_estate_categories"] as $real_est_category) {
		$real_est_categories[] = $real_est_category;		
	}
}

if(isset($_REQUEST["c_minimum_investment_c"]) && !empty($_REQUEST["c_minimum_investment_c"])){
	$minimum_investment = $_REQUEST["c_minimum_investment_c"];
}

if(isset($_REQUEST["c_maximum_investment_c"]) && !empty($_REQUEST["c_maximum_investment_c"])){
	$maximum_investment = $_REQUEST["c_maximum_investment_c"];
}

if(isset($_REQUEST["c_minimum_rent_c"]) && !empty($_REQUEST["c_minimum_rent_c"])){
	$minimum_rent = $_REQUEST["c_minimum_rent_c"];
}

if(isset($_REQUEST["c_maximum_rent_c"]) && !empty($_REQUEST["c_maximum_rent_c"])){
	$maximum_rent = $_REQUEST["c_maximum_rent_c"];
}


if(isset($_REQUEST["c_listing_region_c"]) && !empty($_REQUEST["c_listing_region_c"])){
	foreach($_REQUEST["c_listing_region_c"] as $listing_region) {
		$listing_regions[] = $listing_region;		
	}
}



// echo '<pre>'; print_r($businesscategories); echo '</pre>';

// Define function for applying filters

function filter_listings_obj_old($obj) {

	global $_REQUEST, $keyword, $real_est_categories, $minimum_investment, $maximum_investment, $listing_regions, $minimum_rent, $maximum_rent;

	foreach($obj as $k=>$v)
	{

		// echo '<pre>'; print_r( stripslashes( explode( ',', rtrim(ltrim($v->c_real_estate_categories, '['), ']') ) ) ); echo '</pre>';

		if(isset($_REQUEST["c_keyword_c"]) && !empty($_REQUEST["c_keyword_c"])){
			if( (string) strpos( strtolower($v->c_name_generic_c) , strtolower($keyword) ) == '' )
			{
				$obj[$k] = false;
			}
		}
		
		if(isset($_REQUEST["c_minimum_investment_c"]) && !empty($_REQUEST["c_minimum_investment_c"])){
			if( $v->c_real_estate_investment < $minimum_investment )
			{
				$obj[$k] = false;
			}
		}
		
		if(isset($_REQUEST["c_maximum_investment_c"]) && !empty($_REQUEST["c_maximum_investment_c"])){
			if( $v->c_real_estate_investment > $maximum_investment )
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

// echo '<pre>'; print_r(filter_listings_obj($obj, $k, $v)); echo '</pre>';



/**
	*Make the apicall
	*and filter the the decoded json object
*/
$json = x2apicall(array('_class'=>'Clistings?'.$get_params));
$decoded_json = json_decode($json);

// echo '<pre>'; print_r( $decoded_json ); echo '</pre>';

// Filter Results
$decoded_json = filter_listings_obj($decoded_json);

$results = $decoded_json;




get_header();

the_content();


?>
<section id="content" data="property"> 
	<div class="portfolio_group">
		<div class="container-fluid search_result">
			<div class="row searchpage_main_content_row">

				<div class="col-12 col-sm-4 col-lg-3 sidebar_content" style=" margin-top: 45px;">
					<?php get_sidebar('comercial'); ?>
				</div>

				<div id="business_container" class="col-12 col-sm-8 col-lg-9 searchlists_container">
					<h1 style="text-align:left; padding-top: 22px;"> <?php the_title(); ?></h1>

<?php
global $wpdb;


$results_false_flag = 0;

foreach( $results as $result )
{
	if($result !== false) {
		$results_false_flag = 1;
	}
}


$html = '';

$results_no = 0;

if($results_false_flag)
{

	// echo '<pre>'; print_r($new_array); echo '</pre>';

	foreach ($results as $searchlisting){

		if($searchlisting) {

			// Get number of items found
			$results_no++;

			//Get Real Estate Categories
			$cats = '';
			$db_real_est_categories = explode( ',', rtrim( ltrim( $searchlisting->c_real_estate_categories, '[' ), ']' ) );

			foreach($db_real_est_categories as $db_real_est_category)
			{
				$cats .= '<div class="real_est_cat_tag">'.rtrim( ltrim( stripslashes( $db_real_est_category ), '"' ), '"' ).'</div>';
			}

			// Get Real Est Featured Image
			$images_results = $wpdb->get_results( 'SELECT gp.* FROM x2_gallery_photo gp RIGHT JOIN x2_gallery_to_model gm ON gm.galleryId = gp.gallery_id WHERE gm.modelName="Clistings" AND gm.modelId='.$searchlisting->id, OBJECT );

			$img_div = '';

			// echo '<pre>'; print_r($images_results); echo '</pre>';
			if( !empty($images_results[0]) && $images_results[0]->id > 0)
			{
				// echo '<pre>'; var_dump($images_results[0]); echo '</pre>';
				$img_div = "<div class='searchlisting_featured_image'><img src='/crm/uploads/gallery/_".$images_results[0]->id.".jpg' /></div>" ;
			}

			$html .= "<div class='listing_search_result searchresult real_est_searchresult'>";
			$html .= "	<div class='row'>";
			$html .= "		<div class='col-md-3'>";		    
	        $html .= 			$img_div; 
	        $html .= "		</div>";
			$html .= "		<div class='col-md-9'>";		
		    $html .= "			<a class='ream_est_name' href=\"/listing/". sanitize_title($searchlisting->c_name_generic_c) ."\" class=\"listing_link\" data-id=\"". $searchlisting->id ."\">".$searchlisting->c_name_generic_c."</a>";

			if(is_user_logged_in() ){
			$html .= '<form action="/listing/'.sanitize_title($searchlisting->c_name_generic_c).'" method="post" class="listing_add_to_port_buttton">'.
						'<input type=hidden name="action" value="add_to_portfolio" />'.
						'<input type=hidden name="id" value="'. $searchlisting->id.'" />'.
						'<input type=submit value="'. __('Add to my portfolio','bbcrm').' &#10010;" class="portfolio_action_button portfolio-add"  />'.
					'</form>';
			}

		    $html .= "			<div class='searchlisting_region'>".__("","bbcrm").$searchlisting->c_listing_region_c;
		    $html .= "          	<span class='searchlisting_currency_id'>".__("",'bbcrm').$searchlisting->c_currency_id.number_format($searchlisting->c_listing_askingprice_c)."</span>";
		    $html .= "			</div>";
			//$html .= "		<div>".__("Cash Flow: ",'bbcrm').$searchlisting->c_currency_id.number_format($searchlisting->c_ownerscashflow)."</div>";
		    $html .= "			<a href=\"/listing/". sanitize_title($searchlisting->c_name_generic_c) ."\" class='real_est_description'>".$searchlisting->description."</a>";
			//$html .= "		<div>".__("Contact Seller",'bbcrm').$searchlisting->c_listing_businesscat_c."</div>";
			//$html .= "		<div>".__("More Info",'bbcrm').$searchlisting->c_listing_businesscat_c."</div>";
		    $html .= "			<div class='real_est_bottom_categories'>".$cats."</div>";
			//$html .= "		<div>".$searchlisting->c_listing_businesscat_c."</div>";
			$html .= "		</div>";
	        $html .= "	</div>";

	        $html .= "</div>";
				
		
		}
	}
}
else{
	$html .= "<h2>No results were found for your search.</h2>";
	$html .= "<p>Please check your spelling or try a search with different parameters.</p>";
}


if($results_no)
{
	echo __("Your search ",'bbcrm');

	_e(" returned ",'bbcrm');
	echo $results_no;
	echo ($results_no===1)?__(' result.','bbcrm'):__(' results.','bbcrm');
}


echo $html;

//get_template_part("home","search");
?>  

       
				</div>
			</div>      
		</div>
	</div>      
</section>

<?php get_footer(); ?>
