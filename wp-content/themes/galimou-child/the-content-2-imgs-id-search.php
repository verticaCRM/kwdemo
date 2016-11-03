<?php
/*
Template Name: page content +2 images + ID search
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



<?php echo do_shortcode('[listmenu menu="Sub Commercial" menu_id="sub_commercial" menu_class="au_submenu"]');?>


<section id="content" data="property" style="min-height:1500px;"> 
	<div class="portfolio_group">
		<div class="container-fluid search_result">
			<div class="row searchpage_main_content_row">

				<div class="col-12 col-sm-4 col-lg-3 sidebar_content" style=" margin-top: 45px;">
				
				<div class="panel-group" id="accordion">
						  <div class="panel panel-default">
							<div class="panel-heading">
							  <h4 style="line-height: 40px;" class="panel-title">
								<a style="color:#333; font-weight:100; font-size: 24px; " class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
								  Business Search
								</a>
							  </h4>
							</div>
							<div id="collapseOne" class="panel-collapse collapse in">
							  <div class="panel-body">
								 <?php echo do_shortcode('[featuredsearch]');?>
							  </div>
							</div>
						  </div>
 
 
						</div>
						<br> 
				
		</div>

				<div  id="business_container" class="col-12 col-sm-8 col-lg-9  searchlists_container">
                       <h1 style="text-align:left; padding-top: 22px;"> <?php echo get_the_title( $ID ); ?></h1>
                        <?php the_content(); ?>

<!--	
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


if(count((array)$results) > 0 && $results->status != "404"  &&  $results_false_flag){
	$listingids = array();

	foreach ($results as $searchlisting){

		if($searchlisting) {

			if(!in_array($searchlisting->id,$listingids)){
				$listingids[]= $searchlisting->id;

				if(!empty($searchlisting->c_businesscategories)){
				$categories = substr($searchlisting->c_businesscategories,1,-1);
				$categories = explode(',',str_replace('"', '', $categories));
				$cats = '';
				foreach($categories as $cat){
					$cats .='<a href="?find='.urlencode(stripslashes($cat)).'">'.stripslashes($cat).'</a> ';
				}
			}

//
$json = x2apicall(array('_class'=>'Media/by:description=thumbnail;associationId='.$searchlisting->id.'.json'));
$thumbnail = json_decode($json);
$img_div = "<div class='searchlisting_featured_image'>";
if(!$thumbnail->fileName){
                        $img_div .= '';//<a href="/listing/'.sanitize_title($listing->c_name_generic_c).'" class="listing_link" data-id="'.$listing->id.'"><img src="'.plugin_dir_url(__DIR__).'images/noimage.png"></a>';
                }else{
                        $img_div .= '<a href="/listing/'.sanitize_title($listing->c_name_generic_c).'" class="listing_link" data-id="'.$listing->id.'"><img src="'.get_bloginfo('url').'/crm/uploads/media/'.$thumbnail->uploadedBy.'/'.$thumbnail->fileName.'" style="width:100%" /></a>';

                }
$img_div .= "</div>";

                        $html .= "<div class='listing_search_result searchresult'>";
                        $html .= "      <div class='row'>";
                        $html .= "              <div class='col-md-3 searchlisting_photo_box'>";
                $html .=                        $img_div;
//
	        $html .= "		</div>";
			$html .= "		<div class='col-md-9 searchlisting_content_box'>";
		//Marc - LINE BELOW USES A PLACEHOLDER:".$searchlisting->c_listing_id_c." Some functional code needs to go here in its place :) In addition to the only float on the page ( kind you used for the "more button" on home-featured php file )
        	$html .= "<div class='searchlisting_save_ca'>&nbsp;<span class='glyphicon glyphicon-ok-circle'></span>&nbsp;".__("Save/Request CA","bbcrm").$searchlisting->c_listing_id_c."</div>";		
		    $html .= "			<a class='searchlisting_name' href=\"/listing/". sanitize_title($searchlisting->c_name_generic_c) ."\" class=\"listing_link\" data-id=\"". $searchlisting->id ."\">".$searchlisting->c_name_generic_c."</a>";
		    $html .= "<br>";
		    $html .= "			<div class='searchlisting_region'>".__("","bbcrm").$searchlisting->c_listing_region_c."</div>";
		    $html .= "<div class='searchlisting_currency_id'>".__("",'bbcrm').$searchlisting->c_currency_id.number_format($searchlisting->c_listing_askingprice_c)."</div>";
			//$html .= "		<div>".__("Cash Flow: ",'bbcrm').$searchlisting->c_currency_id.number_format($searchlisting->c_ownerscashflow)."</div>";
		    $html .= "			<div class='searchlisting_description'>".$searchlisting->description."</div>";
			//$html .= "		<div>".__("Contact Seller",'bbcrm').$searchlisting->c_listing_businesscat_c."</div>";
			//$html .= "		<div>".__("More Info",'bbcrm').$searchlisting->c_listing_businesscat_c."</div>";
		    $html .= "			<div class='searchlisting_bottom_category'>".$cats."</div>";
		    
			//$html .= "		<div>".$searchlisting->c_listing_businesscat_c."</div>";
			
			 //Marc - LINE BELOW USES A PLACEHOLDER:".$searchlisting->c_listing_id_c." Some functional code needs to go here in its place :)
            $html .= "<div class='searchlisting_bottom_ref'>".__("Reference","bbcrm").$searchlisting->c_listing_id_c."</div>";
			$html .= "		</div>";
			
			
	        $html .= "	</div>";
	       
	        $html .= "</div>";
				
				
				

				
				
			if(is_user_logged_in() ){
				$html .= '<form action="/listing/'.sanitize_title($searchlisting->c_name_generic_c).'" method=post><input type=hidden name="action" value="add_to_portfolio" /><input type=hidden name="id" value="'. $searchlisting->id.'" /><input type=submit style="display:none; margin-bottom:18px;" value="'. __('Add to my portfolio','bbcrm').' &#10010;" class="portfolio_action_button portfolio-add"  /></form>';
				}
			}
		}
	}
}
else{
	$qy = (empty($qy))?"your search":'"'.$qy.'"';
	$html .= "<h2>No results were found for ".$qy."</h2>";
	$html .= "<p>Please check your spelling or try a search with different parameters.</p>";
	// $html .= do_shortcode('[featuredsearch]');
}


if(!empty($listingids)){
echo '<h2>'.get_the_title().'</h2>';
echo __("Your search ",'bbcrm');

if(is_array($qy)){
	echo join(",",$qy);
} else {
	echo $qy;
}
_e(" returned ",'bbcrm');
echo count((array)$listingids);
echo (count((array)$listingids)===1)?__(' result.','bbcrm'):__(' results.','bbcrm');
}


echo $html;

//get_template_part("home","search");
?>  

       
				</div>
			</div>      
		</div>
	</div>   -->
	
		 
</section>

<?php get_footer(); ?>
