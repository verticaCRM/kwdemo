<?php
/*
Template Name: Comm Management Lease
*/
session_start();
//ini_set('display_errors',true);
//error_reporting(E_ALL);

$postmeta = get_post_meta( get_the_ID() );

foreach($_REQUEST as $k=>$v){
	if( $v=='' ){
		unset($_REQUEST[$k]);
	}
}

// Grab our filters

$get_params = '_partial=1&_escape=0';

if(isset($_REQUEST["c_listing_franchise_c"]) && !empty($_REQUEST["c_listing_franchise_c"])){
	$franch = 'c_listing_franchise_c='.$_REQUEST["c_listing_franchise_c"];
	$get_params .= '&'.$franch;
}

if(isset($_REQUEST["c_listing_exclusive_c"]) && !empty($_REQUEST["c_listing_exclusive_c"])){
	$exclus = 'c_listing_exclusive_c='.$_REQUEST["c_listing_exclusive_c"];
	$get_params .= '&'.$exclus;
}

if(isset($_REQUEST["c_listing_homebusiness_c"]) && !empty($_REQUEST["c_listing_homebusiness_c"])){
	$home = 'c_listing_homebusiness_c='.$_REQUEST["c_listing_homebusiness_c"];
	$get_params .= '&'.$home;
}


if(isset($_REQUEST["id"]) && !empty($_REQUEST["id"])){
	$home = 'id='.$_REQUEST["id"];
	$get_params .= '&'.$home;
}

if(isset($_REQUEST["c_listing_region_c"]) && !empty($_REQUEST["c_listing_region_c"])){
	$home = 'c_listing_region_c='.$_REQUEST["c_listing_region_c"];
	$get_params .= '&'.$home;
}

if(isset($_REQUEST["c_listing_town_c"]) && !empty($_REQUEST["c_listing_town_c"])){
	$home = 'c_listing_town_c='.$_REQUEST["c_listing_town_c"];
	$get_params .= '&'.$home;
}

// if(isset($_REQUEST["c_Broker"]) && !empty($_REQUEST["c_Broker"])){
// 	$home = 'c_Broker='.$_REQUEST["c_Broker"];
// 	$get_params .= '&'.$home;
// }


/**
	LAST ADDED FIELDS
*/
if(isset($_REQUEST["c_keyword_c"]) && !empty($_REQUEST["c_keyword_c"])){
	$keyword = trim($_REQUEST["c_keyword_c"]);
}

if(isset($_REQUEST["c_minimum_investment_c"]) && !empty($_REQUEST["c_minimum_investment_c"])){
	$minimum_investment = $_REQUEST["c_minimum_investment_c"];
}

if(isset($_REQUEST["c_maximum_investment_c"]) && !empty($_REQUEST["c_maximum_investment_c"])){
	$maximum_investment = $_REQUEST["c_maximum_investment_c"];
}

if(isset($_REQUEST["c_adjusted_net_profit_c"]) && !empty($_REQUEST["c_adjusted_net_profit_c"])){
	$adjusted_net_profit = explode("|",$_REQUEST["c_adjusted_net_profit_c"]);
}

if(isset($_REQUEST["c_Broker"]) && !empty($_REQUEST["c_Broker"])){

	foreach($_REQUEST["c_Broker"] as $broker) {
		$brokers[] = $broker;		
	}
}
/**
*/



// echo '<pre>'; print_r($keyword); echo '</pre>';


if(isset($_REQUEST["c_listing_askingprice_c"]) && !empty($_REQUEST["c_listing_askingprice_c"])){
	$askingprice_params = explode("|",$_REQUEST["c_listing_askingprice_c"]);
}

if(isset($_REQUEST["c_ownerscashflow"]) && !empty($_REQUEST["c_ownerscashflow"])){
	$ownerscashflow_params = explode("|",$_REQUEST["c_ownerscashflow"]);
}

if(isset($_REQUEST["c_listing_downpayment_c"]) && !empty($_REQUEST["c_listing_downpayment_c"])){
	$listing_downpayment_params = explode("|",$_REQUEST["c_listing_downpayment_c"]);
}



// Define function for applying filters
// echo '<pre>'; print_r(filter_listings_obj($obj, $k, $v)); echo '</pre>';



// If we have Categories
if(isset($_REQUEST["c_businesscategories"]) && !empty($_REQUEST["c_businesscategories"])){

	foreach($_REQUEST["c_businesscategories"] as $k=>$v)
	{
		$business_categories = 'c_businesscategories=["'.trim($v).'"]';
		$cat = '&'.$business_categories;
		$json[] = x2apicall(array('_class'=>'Clistings?'.$get_params.$cat));
	}

	foreach($json as $k=>$v)
	{
		$decoded_json[] = json_decode($v);
	}

	// Assign Results
	foreach($decoded_json as $k=>$v)
	{
		foreach($v as $kk=>$vv)
		{
			$results[] = $vv;	
		}	
	}

	// Filter Results
	$results = filter_listings_obj($results);

}
else
{
	// If we don't have Categories
	$json = x2apicall(array('_class'=>'Clistings?'.$get_params));
	$decoded_json = json_decode($json);

	// Filter Results
	$decoded_json = filter_listings_obj($decoded_json);

	$results = $decoded_json;

}



get_header();

?>

<?php the_content(); ?>
<section id="content" data="property" style="min-height:1500px;"> 
	<div class="portfolio_group">
		<div class="container-fluid search_result">
			<div class="row searchpage_main_content_row">

				<div class="col-12 col-sm-4 col-lg-3 sidebar_content" style=" margin-top: 45px;">
					<?php get_sidebar('comercial'); ?>
				</div>	

				<div  id="business_container" class="col-12 col-sm-8 col-lg-9 searchlists_container">
                    <h1 style="text-align:left; padding-top: 22px;"> <?php echo get_the_title( $ID ); ?></h1>
                    <p>We can look after your commercial leasing requirements for a very competitive management fee.</p>
                    <p>Call Ken Allsop today to discuss <a href="tel:0411428888">0411 428 888</a>.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer();
