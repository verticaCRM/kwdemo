<?php
/*
Template Name: Comm Search Results
*/
session_start();
//ini_set('display_errors',true);
//error_reporting(E_ALL);

$postmeta = get_post_meta( get_the_ID() );

foreach($_REQUEST as $k=>$v){
	if(''==$v){
		unset($_REQUEST[$k]);
	}
}


// Grab our type filters
if(isset($_REQUEST["c_listing_franchise_c"]) && !empty($_REQUEST["c_listing_franchise_c"])){
	$franch = 'c_listing_franchise_c='.$_REQUEST["c_listing_franchise_c"];
	$and_franch = '&'.$franch;
	$franch_semi = $franch.';';
}

if(isset($_REQUEST["c_listing_exclusive_c"]) && !empty($_REQUEST["c_listing_exclusive_c"])){
	$exclus = 'c_listing_exclusive_c='.$_REQUEST["c_listing_exclusive_c"];
	$and_exclus = '&'.$exclus;
	$exclus_semi = $exclus.';';
}

if(isset($_REQUEST["c_listing_homebusiness_c"]) && !empty($_REQUEST["c_listing_homebusiness_c"])){
	$home = 'c_listing_homebusiness_c='.$_REQUEST["c_listing_homebusiness_c"];
	$and_home = '&'.$home;
	$home_semi = $home.';';
}

if(isset($_REQUEST['c_name_generic_c'])&& is_numeric($_REQUEST["c_name_generic_c"]) || isset($_REQUEST["id"]) && !empty($_REQUEST["id"])){

	$qy = $_REQUEST["id"];
	$_SESSION["listingid"]=$qy;

if(is_numeric($_REQUEST["c_name_generic_c"])){
$qy = $_REQUEST["c_name_generic_c"];
}
$json = x2apicall(array('_class'=>'Clistings?_partial=1&_escape=0'.$and_franch.$and_exclus.$and_home.'&c_sales_stage=Active&id='.$qy));
$idresults = json_decode($json);

if(empty($idresults[0]->c_listing_frontend_url)){
header("Location:/listing/");
exit;
}else{
header("Location:".$idresults[0]->c_listing_frontend_url,false,307);
exit;
}



}//end of numeric only

$qy = $_REQUEST["c_name_generic_c"];
if(isset($_REQUEST["find"]) && !empty($_REQUEST["find"])){
	$qy = $_REQUEST["find"];
}
if(isset($_REQUEST["c_listing_region_c"]) && !empty($_REQUEST["c_listing_region_c"])){
	$qy = $_REQUEST["c_listing_region_c"];
}
if(isset($_REQUEST["c_listing_town_c"]) && !empty($_REQUEST["c_listing_town_c"])){
	$qy = $_REQUEST["c_listing_town_c"];
}
if(isset($_REQUEST["broker"]) && !empty($_REQUEST["broker"])){
	$qy = urlencode($_REQUEST["broker"]);
}
if(isset($_REQUEST["c_businesscategories"]) && !empty($_REQUEST["c_businesscategories"])){
	$qy = $_REQUEST["c_businesscategories"];
}

//echo $qy;
if(!empty($qy)){
$busca3results = null;
$buscat4results = null;
if(isset($_REQUEST["find"]) || isset($_REQUEST["c_businesscategories"])){
	$json = x2apicall(array('_class'=>'Clistings?_partial=1&_escape=0'.$and_franch.$and_exclus.$and_home.'&c_businesscategories=%25'.urlencode($qy).'%25'));
	$buscat3result = json_decode($json);
	foreach($buscat3result AS $idx=>$res){
		$buscat3results[] = $res;
	}

	$json = x2apicall(array('_class'=>'Clistings?_partial=1&_escape=0'.$and_franch.$and_exclus.$and_home.'&c_listing_businesscat_c=%25'.$qy.'%25'));
	$buscat4result = json_decode($json);
	foreach($buscat4result AS $idx=>$res){
		//$buscat4results[] = $res;
	}
}

$json = x2apicall(array('_class'=>'Clistings?_partial=1&_escape=0'.$and_franch.$and_exclus.$and_home.'&c_sales_stage=Active&c_name_generic_c=%25'.urlencode($qy).'%25'));
$genresults = json_decode($json);

//echo $json;

$json = x2apicall(array('_class'=>'Clistings?_partial=1&_escape=0'.$and_franch.$and_exclus.$and_home.'&c_sales_stage=Active&description=%25'.urlencode($qy).'%25'));
$descresults = json_decode($json);

//echo "1".$json;

$json = x2apicall(array('_class'=>'Clistings?_partial=1&_escape=0'.$and_franch.$and_exclus.$and_home.'&c_sales_stage=Active&c_listing_region_c=%25'.urlencode($qy).'%25'));
$regionresults = json_decode($json);

//echo "2".$json;

$json = x2apicall(array('_class'=>'Clistings?_partial=1&_escape=0'.$and_franch.$and_exclus.$and_home.'&c_sales_stage=Active&c_listing_town_c=%25'.urlencode($qy).'%25'));
$countyresults = json_decode($json);

//echo "3".$json;

$json = x2apicall(array('_class'=>'Clistings?_partial=1&_escape=0'.$and_franch.$and_exclus.$and_home.'&c_sales_stage=Active&c_assigned_user_id=%25'.$qy.'%25'));
$brokerresults = json_decode($json);

//echo "4".$json;

$json = x2apicall(array('_class'=>'Clistings?_partial=1&_escape=0'.$and_franch.$and_exclus.$and_home.'&c_sales_stage=Active&c_name_dba_c=%25'.urlencode($qy).'%25'));
$dbaresults = json_decode($json);

//echo $json;

$params=http_build_query(array("_tags"=>$qy,"_tagOr"=>"1"));
$json = x2apicall(array('_class'=>'Clistings?_partial=1&_escape=0'.$and_franch.$and_exclus.$and_home.'&c_sales_stage=Active&_limit=20&'.$params));
$tagresults = json_decode($json);

}

if(isset($_REQUEST["c_ownerscashflow"])){
	$params = explode("|",$_REQUEST["c_ownerscashflow"]);
	$json = x2apicall(array('_class'=>'Clistings/by:c_sales_stage=Active;'.$franch_semi.$exclus_semi.$home_semi.'c_ownerscashflow='.urlencode('>=').$params[0].';c_ownerscashflow='.urlencode('<=').$params[1].'.json'));

	$cashflowresults = array();
//echo 	'Clistings/by:c_sales_stage=Active;c_ownerscashflow='.urlencode('>=').$params[0].';c_ownerscashflow='.urlencode('<=').$params[1].'.json';

	$cashflowresult = json_decode($json);
//print_r($cashflowresult);
	if($cashflowresult->directUris){
		foreach($cashflowresult->directUris as $idx=>$uri){
				$json = x2apicall(array("_url"=>$uri));
				$cashflowresults[] = json_decode($json);
		}
	}

}

if(isset($_REQUEST["c_listing_askingprice_c"])){
	$params = explode("|",$_REQUEST["c_listing_askingprice_c"]);
	$json = x2apicall(array('_class'=>'Clistings/by:c_sales_stage=Active;'.$franch_semi.$exclus_semi.$home_semi.'c_listing_askingprice_c>='.$params[0].';c_listing_askingprice_c<'.$params[1].'.json'));
	$askingpriceresults = json_decode($json);
}

if(isset($_REQUEST["c_listing_downpayment_c"])){
	$params = explode("|",$_REQUEST["c_listing_downpayment_c"]);
	$json = x2apicall(array('_class'=>'Clistings/by:c_sales_stage=Active;'.$franch_semi.$exclus_semi.$home_semi.'c_listing_downpayment_c>='.$params[0].';c_listing_downpayment_c<'.$params[1].'.json'));
	$downpaymentresults = json_decode($json);
}

if(isset($_REQUEST["c_businesscategories"]) && !empty($_REQUEST["c_businesscategories"])){
$buscatresults = null;
$buscat2results = null;
	foreach ($_REQUEST["c_businesscategories"] AS $idx=>$cat){
		$json = x2apicall(array('_class'=>'Clistings?_partial=1&_escape=0'.$and_franch.$and_exclus.$and_home.'&c_sales_stage=Active&c_listing_businesscat_c=%25'.urlencode($cat).'%25'));
		$buscatresult = json_decode($json);
		foreach($buscatresult AS $idx=>$res){
			$buscatresults[] = $res;
		}

		$json = x2apicall(array('_class'=>'Clistings?_partial=1&_escape=0'.$and_franch.$and_exclus.$and_home.'&c_sales_stage=Active&c_businesscategories=%25'.urlencode($cat).'%25'));
		$buscat2result = json_decode($json);
		foreach($buscat2result AS $idx=>$res){
			$buscat2results[] = $res;
		}
	}
}
if( isSet($postmeta["search_key"]) ){
//print_r($postmeta);
//echo 'Clistings/by:c_sales_stage=Active;'.$postmeta['search_key'][0].urlencode($postmeta['search_operator'][0]).$postmeta['search_value'][0].'.json';
//	$json = x2apicall(array('_class'=>'Clistings/by:c_sales_stage=Active;'.$postmeta['search_key'][0].$postmeta['search_operator'][0].$postmeta['search_value'][0].'.json'));
//        $postmetaresults = json_decode($json);
//var_dump($postmetaresults);
global $wpdb;
$postmetaresults = $wpdb->get_results( 'SELECT * FROM x2_clistings WHERE '.$postmeta['search_key'][0].$postmeta['search_operator'][0].$postmeta['search_value'][0], OBJECT );
print_r($results);

}

$results = (object) array_merge((array) $idresults,(array) $genresults, (array) $tagresults, (array) $descresults, (array) $regionresults, (array) $countyresults, (array) $dbaresults, (array) $brokerresults, (array) $buscatresults, (array) $buscat2results,(array) $buscat3results,(array) $buscat4results,(array) $downpaymentresults,(array) $askingpriceresults,(array) $cashflowresults, (array) $postmetaresults);

//print_r($results);



//get_template_part('template','top');
get_header();

?>
<?php the_content(); ?>
<section id="content" class="container" data="property"> 
<div style="margin-top:36px;" class="portfolio_group">

<div class="container-fluid search_result">
<div class="row">

<div style="min-width:280px !important; max-width:285px; float:left; display: inline-block; clear: none; " class="col-md-3">
<!--comm sidebar here--->
<div class="panel panel-default" >
  <div class="panel-heading">
	<h2 class="panel-title">
	 <strong> Commercial Search </strong>
	</h2>
  </div>

 <div class="panel-body">
   <?php echo do_shortcode('[featuredsearch]');?>
 </div>
 </div>


<!--// comm sidebar --->

</div>



<div id="business_container" class="col-md-9 searchlists_container">

<h1><?php the_title ();?></h1>
	
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


$cjson = x2apicall(array('_class'=>'dropdowns/1086.json'));
$colorjson = json_decode($cjson);
$colors = (array) $colorjson->options;
//print_r($colors);

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
					$cat = stripslashes( preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {   return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');}, $cat));
					$cats .='<a href="?find='.urlencode($cat).'">'.$cat.'</a> ';
				}
			}

			$images_results = $wpdb->get_results( 'SELECT gp.* FROM x2_gallery_photo gp RIGHT JOIN x2_gallery_to_model gm ON gm.galleryId = gp.gallery_id WHERE gm.modelName="Clistings" AND gm.modelId='.$searchlisting->id, OBJECT );

			$img_div = '';
			if( !empty($images_results[0]) && $images_results[0]->id > 0)
			{
				// echo '<pre>'; var_dump($images_results[0]); echo '</pre>';
				$img_div = "<div class='searchlisting_featured_image'><img src='/crm/uploads/gallery/_".$images_results[0]->id.".jpg' /></div>" ;
			}

			$html .= "<div class='listing_search_result searchresult'>";
			$html .= "	<div class='row'>";
			$html .= "		<div class='col-md-3 searchlisting_photo_box'>";		    
	        $html .= 			$img_div; 
	        $html .= "		</div>";
			$html .= "		<div class='col-md-9 searchlisting_content_box'>";
				
		    $html .= "			<a class='searchlisting_name' href=\"/listing/". sanitize_title($searchlisting->c_name_generic_c) ."\" class=\"listing_link\" data-id=\"". $searchlisting->id ."\">".$searchlisting->c_name_generic_c."</a>";
		    $html .= "<br>";
		    $html .= "			<div class='searchlisting_region'>".__("","bbcrm").$searchlisting->c_listing_region_c."</div>";
		    $html .= "<div class='searchlisting_currency_id'>".__("",'bbcrm').$searchlisting->c_currency_id.number_format($searchlisting->c_listing_askingprice_c)."</div>";
			//$html .= "		<div>".__("Cash Flow: ",'bbcrm').$searchlisting->c_currency_id.number_format($searchlisting->c_ownerscashflow)."</div>";
		    $html .= "			<div class='searchlisting_description'>".$searchlisting->description."</div>";
			//$html .= "		<div>".__("Contact Seller",'bbcrm').$searchlisting->c_listing_businesscat_c."</div>";
			//$html .= "		<div>".__("More Info",'bbcrm').$searchlisting->c_listing_businesscat_c."</div>";
//print_r($colors);
		    $html .= "			<div class='searchlisting_bottom_category' style='background-color:".$colors[$cat]."'>".$cats."</div>";
		    
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
 </div>      
</section>

<?php get_footer(); ?>
