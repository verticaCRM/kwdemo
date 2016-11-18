<?php
session_start();

$inportfolio = false;
$crmid = 0;

$bbcrm_option = get_option( 'bbcrm_settings' );

if(isset($_POST["id"])){
	$crmid = $_POST["id"];
}elseif(isset($_SESSION["listingid"]) ){
	$crmid = $_SESSION["listingid"];
}else{}

if($crmid>0){
	$json = x2apicall(array('_class'=>'Clistings/'.$crmid.'.json'));
}else{
	//we need to get the listing by ID
	$listingParams = explode('--',$_SERVER["REQUEST_URI"]);
	if (count($listingParams) > 1)
	{
		//we have a link that contains id
		$listingID = str_replace('/', '', $listingParams[1]);
		$json = x2apicall(array('_class'=>'Clistings/'.$listingID.'.json'));
	}
	else
	{
		$trailing = (substr($_SERVER["REQUEST_URI"],-1)=="/")?"":"/";
		$json = x2apicall(array('_class'=>'Clistings/by:c_listing_frontend_url='.$_SERVER["REQUEST_URI"].$trailing.'.json'));
	}
	
}
$listing = json_decode($json);

// echo '<pre>'; print_r($listing); echo '</pre>';
$json = x2apicall(array('_class'=>'Clistings/'.$listing->id.'/tags'));
$tags = json_decode($json);

$listingtags = array();
foreach ($tags as $idx=>$tag){
	$listingtags[] = urldecode(substr($tag, 1));
}

/* Failsafe. Need to move to create flow */
if(empty($listing->c_listing_frontend_url) || $listing->c_listing_frontend_url != $listing->c_name_generic_c){
$json = x2apipost( array('_method'=>'PUT','_class'=>'Clistings/'.$listing->id.'.json','_data'=>array('c_listing_frontend_url'=>'/listing/'.sanitize_title($listing->c_name_generic_c)."/") ) );
}



//Get The Broker
if ($listing->c_assigned_user_id != '')
{
	$json = x2apicall(array('_class'=>'Brokers/by:nameId='.urlencode($listing->c_assigned_user_id).".json"));
	$listingbroker =json_decode($json);
}
elseif ($listing->assignedTo != '')
{
	$results = $wpdb->get_results( "SELECT * FROM x2_users WHERE userAlias='".$listing->assignedTo."'", OBJECT );
	$broker_nameId = $results[0]->firstName.' '.$results[0]->lastName.'_'.$results[0]->id;
	$broker_name = $results[0]->firstName.' '.$results[0]->lastName;
	$broker_nameID = $results[0]->nameId;
	
	// $json = x2apicall(array('_class'=>'Brokers/by:name='.urlencode($broker_name).".json"));
	$json = x2apicall(array('_class'=>'Brokers/by:nameId='.urlencode($broker_nameID).".json"));
	$listingbroker =json_decode($json);
	
}



if(!$listingbroker->nameId){
	$json = x2apicall(array('_class'=>'Brokers/by:nameId=House%20Broker_5.json'));
	$listingbroker =json_decode($json);
}
if(!is_user_logged_in() ){	
	
	$json = x2apicall(array('_class'=>'Brokers/by:nameId='.urlencode($listingbroker->nameId).".json"));
	$buyerbroker =json_decode($json);	

	$json = x2apicall(array('_class'=>'Media/by:fileName='.$buyerbroker->c_profilePicture.".json"));
	$brokerimg =json_decode($json);
	
}
if(is_user_logged_in() ){	
	$json = x2apicall(array('_class'=>'Contacts/by:email='.urlencode($userdata->user_email).".json"));
	$buyer =json_decode($json);

	$isuserregistered = ($buyer->c_buyer_status=="Registered")?true:false;
	if ($listingbroker->name == '')
	{
		$json = x2apicall(array('_class'=>'Brokers/by:nameId='.urlencode($buyer->c_broker).".json"));
		$buyerbroker =json_decode($json);
	}
	else
	{
		$buyerbroker = $listingbroker;
	}
		
	
	$json = x2apicall(array('_class'=>'Media/by:fileName='.$buyerbroker->c_profilePicture.".json"));
	$brokerimg =json_decode($json);
	
	
	//get all files that are allowed to see them
	$data = array(
			'listing_id'	=>	$listing->id,
			'buyer_id'	=>	$buyer->id,		
		);
	$json = x2apipost( array('_method'=>'GET','_class'=>'PortfolioMedia/','_data'=>$data ) );
	$buyerListingFiles =json_decode($json[1]);
	$currentBuyerListingFiles = array();
	if (!empty($buyerListingFiles))
	{
		foreach ($buyerListingFiles as $indexFile => $portfolioMediaFile)
		{
			if ($portfolioMediaFile->listing_id == $listing->id && $portfolioMediaFile-> buyer_id == $buyer->id)
			{
				$currentBuyerListingFiles[$portfolioMediaFile->media_id] = $portfolioMediaFile;
			}
		}
	}
	
	//get all filed for the listing
	$data = array(
			'associationId'	=>	$listing->id,
			'associationType' => "clistings",		
		);	
	$json = x2apipost( array('_method'=>'GET','_class'=>'Media/','_data'=>$data ) );
	$fileslisting =json_decode($json[1]);
	
	function get_file_icon ($mediaFile)
	{
		
		$mediaIcon = 'unset';
		
		$map = array(
			'image' 		=> '<i class="fa fa-file-picture-o"></i>', 
			'text' 			=> '<i class="fa fa-file-text-o"></i>', 
			'word' 			=> '<i class="fa fa-file-word-o"></i>', 
			'excel' 		=> '<i class="fa fa-file-excel-o"></i>', 
			'sheet' 		=> '<i class="fa fa-file-excel-o"></i>', 
			'powerpoint' 	=> '<i class="fa fa-file-powerpoint-o"></i>', 
			'presentation' 	=> '<i class="fa fa-file-powerpoint-o"></i>', 
			'pdf' 			=> '<i class="fa fa-file-pdf-o"></i>',
			'audio' 		=> '<i class="fa fa-file-audio-o"></i>', 
			'video' 		=> '<i class="fa fa-file-video-o"></i>', 
			'zip' 			=> '<i class="fa fa-file-zip-o"></i>', 
			'rar' 			=> '<i class="fa fa-file-zip-o"></i>',
		);
		
		foreach ($map as $fileType => $fileIcon) {
			if (strpos($mediaFile -> mimetype, $fileType) !== false) {
			    $mediaIcon = $fileIcon;
		        break;
		    }
		}
		if ($mediaIcon == 'unset')
		{
			$mediaIcon = '<i class="fa fa-file-o"></i>';
		}
		return $mediaIcon;
	}
	//print_r('<pre>');print_r('buyerListingFiles');print_r('</pre>');
	//print_r('<pre>');print_r($fileslisting);print_r('</pre>');
	$currentListingFiles = array();
	if (!empty($fileslisting))
	{
		foreach ($fileslisting as $indexFile => $mediaFile)
		{
			if ($mediaFile->associationType == 'clistings' && $mediaFile->private == 0)
			{
				//check if this buyer has permission to see this file
				if (array_key_exists($mediaFile->id, $currentBuyerListingFiles))
				{
					$buyerFile = $currentBuyerListingFiles[$mediaFile->id];
					if ($buyerFile -> private == 1)
					{
						//check if the date is still available
						if ($buyerFile -> private_end_date == '0000-00-00') {
							$mediaFile -> mediaIcon = get_file_icon($mediaFile);
							$currentListingFiles[$mediaFile->id] = $mediaFile; 
						}
						else
						{
							if (strtotime(date('Y-m-d')) <= strtotime($buyerFile -> private_end_date))
							{
								$mediaFile -> mediaIcon = get_file_icon($mediaFile);
								$currentListingFiles[$mediaFile->id] = $mediaFile; 
								// check what icon need to list based on myme-type
							}
	
						}
					}
					
				}
				
			}
		}
	}
	
	if(isset($_POST["add_to_portfolio"]) || isset($_POST['action']) && $_POST["action"]=="add_to_portfolio"){
		
		$json = x2apicall(array('_class'=>'Portfolio/by:c_listing_id='.$listing->id.";c_buyer=".urlencode($buyer->nameId).".json"));
		$prevlisting =json_decode($json);	

		// echo '<pre>'; print_r($prevlisting); echo '</pre>';
	
		if($prevlisting->status=="404"){
			$data = array(
				'name'	=>	'Portfolio listing for '.$listing->name,
				'c_listing'	=>	$listing->name,
				'c_listing_id'	=>	$listing->id,
				'c_buyer'	=>	$buyer->nameId,
				'c_buyer_id'	=>	$buyer->id,
				'c_release_status'	=>	'Added',
				'assignedTo'	=>	$buyerbroker->assignedTo,
			);
		
			$json = x2apipost( array('_class'=>'Portfolio/','_data'=>$data ) );
			$portfoliolisting =json_decode($json[1]);
		
			$json = x2apicall(array('_class'=>'Portfolio/'.$portfoliolisting->id.'.json'));
			$portfoliorelationships =json_decode($json);
			
			$json = x2apicall( array('_class'=>'Portfolio/'.$portfoliorelationships->id."/relationships?secondType=Contacts" ) );
			$rel = json_decode($json);
		
			$json = x2apipost( array('_method'=>'PUT','_class'=>'Portfolio/'.$portfoliolisting->id.'/relationships/'.$rel[0]->id.'.json','_data'=>$data ) );
	
		}
	}
//Is this listing in the user's portfolio?	
	$json = x2apicall(array('_class'=>'Portfolio/by:c_listing_id='.$listing->id.';c_buyer='.urlencode($buyer->nameId).'.json'));
	
	$portfoliolisting =json_decode($json);	
	
	if($portfoliolisting->id){
		$inportfolio=true;		
	}
}
//////////////////
//print_r($listing); 

		$status =($listing->c_sales_stage=="Active")?"For Sale":$listing->c_sales_stage;
		$listing_id =$listing->id;
		$listing_dateapproved = $listing->c_listing_date_approved_c;
		$generic_name =$listing->c_name_generic_c;
		$description =$listing->description;
		$region=$listing->c_listing_region_c;
		$town=$listing->c_listing_town_c;
		$idref=$listing->c_listing_frontend_id_c;
		$terms=$listing->c_listing_terms_c;
		$currency_symbol=$listing->c_currency_id;
		$grossrevenue=number_format($listing->c_financial_grossrevenue_c);
		$amount=number_format($listing->c_listing_askingprice_c);
		$downpayment=number_format($listing->c_listing_downpayment_c);
		$ownercashflow=number_format($listing->c_ownerscashflow);
		$brokername = $listing->assignedTo;
		$brokerid = substr($listing->c_assigned_user_id, strpos($listing->c_assigned_user_id, "_") + 1);;
		$categories = 	join(",",json_decode($listing->c_businesscategories)); //

$_SESSION["viewed_listings"][$listing_id] = array("brokerid"=>$listingbroker->name,"listingname"=>$generic_name);

	$cssclass = '';

if( is_user_logged_in() ){
if($portfoliolisting->c_release_status== "Released"){
	$isaddressreleased = true;
	$cssclass = 'nareq_released';
	$generic_name = $listing->name_dba_c.' "'.$generic_name.'" ';
	$address = $listing->listing_address_c."<br>";
	$city = $listing->listing_city_c." ";
	$postal = $listing->listing_postal_c."<br>";
}

}
global $pagetitle;
$pagetitle = "Listing: ".$listing->c_name_generic_c. " | ".get_bloginfo('name');

wp_enqueue_script('galleria',get_stylesheet_directory_uri().'/js/galleria-1.4.2.min.js',array('jquery'),'1.4.2');
wp_enqueue_script('galleriatheme',get_stylesheet_directory_uri().'/themes/classic/galleria.classic.min.js',array('jquery'));
wp_enqueue_style('galleriacss',get_stylesheet_directory_uri().'/themes/classic/galleria.classic.css');


get_header();
?>
<div class="container-fluid">
<section id="content" class="portfolio_group" data="property">
<div class="row searchpage_main_content_row">
	<div style=" margin-top: 45px;" class="col-12 col-sm-4 col-lg-3">

<?php get_sidebar('page'); ?>
</div>



<div  class="col-12 col-sm-8 col-lg-9">
<h2><?php the_title(); ?></h2>
<div id="top-horizontal-grey-div"> 
	

<?php 
if( is_user_logged_in() ){
if($portfoliolisting->c_release_status== "Deleted"){
	echo '<div class="portfoliostatus deleted">&#10006; ' .	__("This propery was removed from your portfolio",'bbcrm') . "</div>";
}elseif($isaddressreleased){
echo '<div class="portfoliostatus released"> &#9733; ' .	__("The address of this business is available to you",'bbcrm') . "</div>";
}elseif($inportfolio){
echo '<div class="portfoliostatus added">&#10003; ' .	__("This propery is in your portfolio",'bbcrm') . "</div>";
}
}
// echo '<pre>';print_r($listing); echo '</pre>';
?>

<div style="position: relative;" id="business_container" role="main" >
	<div class="">
	           
	  <div class="pull-left" style="display:inline; width:60%;">
			    <div class="al-title property-title entry-title <?php echo $cssclass;?>"><?php echo $generic_name; ?></div>	
			<br><div class="al-price property_detail"><label><?php _e("", 'bbcrm');?></label><?php echo $listing->c_currency_id.$listing->c_listing_askingprice_c.""; ?></div>
			<?php if ($categories != '') { ?><br><div class="al-cat property_detail"><label><?php _e("", 'bbcrm');?></label><?php echo $categories; ?></div><?php } ?>
		</div>	
		 <div class="pull-right" style="display:inline; width:40%;">
                <div class="al-id property_detail" id="property_listing_id" data-id="<?php echo $listing_id;?>"><label><?php _e("ID Ref:", 'bbcrm');?></label>#<?php echo preg_replace("/[^0-9,.]/","",$listing->c_name_generic_c); ?></div>
             <br><div class="al-region property_detail"><label><?php _e("City:", 'bbcrm');?></label><?php echo $suberb;?></div>	
             <br><div class="al-region property_detail"><label><?php _e("State:", 'bbcrm');?></label><?php echo $region;?></div>
             <br><div class="al-status property_detail"><label><?php _e("Status:", 'bbcrm');?></label><?php echo $status; ?></div>
<?php
if(is_user_logged_in() && !$inportfolio){
?>
                                                <form method=post>
                                                        <input type=submit style="color:#fff!important; font-weight:600; font-size:1.0em; background-color:#333; width:auto;text-align:center; padding:7px 8px 3px 8px; float:right; clear:right; height:auto; border-radius:4px; vertical-align:bottom; position:absolute: bottom:0; margin-bottom: 4px;" value="<?php _e('SAVE','bbcrm');?>" class=""   />
                                                        <input type=hidden name="action" value="add_to_portfolio" />
                                                        <input type=hidden name="id" value="<?php echo $listing->id;?>" />
                                                </form>
<?php 
}else{
	echo '<div style="color:#fff; font-weight:600; font-size:1.0em; background-color:#333; width:auto;text-align:center; padding:7px 8px 3px 8px; float:right; clear:right; height:auto; border-radius:4px; vertical-align:bottom; position:absolute: bottom:0; margin-bottom: 4px;" ><span style="color:#fff;" class="glyphicon glyphicon-ok-circle"></span> <a style="color:#fff;" href="/registration/">REQUEST CA</a></div>';
}
?>     </div>

 </div>
 <?php if (!empty($currentListingFiles)) { ?>
			           		<div id="confidential_files">
					   		<h3 class=detailheader>Confidential Files</h3>
					        <?php   foreach ($currentListingFiles as $mediaFile) { ?>
						           <div class="property_detail" style="margin-bottom:3px;"><a target="_blank" href="/crm/uploads/media/<?php echo $mediaFile->uploadedBy; ?>/<?php echo $mediaFile->fileName; ?>"><?php echo $mediaFile->mediaIcon; ?> &nbsp;<?php echo $mediaFile->fileName; ?></a></div>	           		
					        <?php   } ?>
					        </div>
			           
			           <?php } ?> 
</div>

</div>
				
				
				<br clear=all><br>
				
								
<?php
global $wpdb;
$results = $wpdb->get_results( 'SELECT gp.* FROM x2_gallery_photo gp RIGHT JOIN x2_gallery_to_model gm ON gm.id = gp.gallery_id WHERE gm.modelName="Clistings" AND gm.modelId='.$listing->id.' ORDER BY gp.rank', OBJECT );
if(!empty($results[0]->id)):
?>
                                                <h3 class="detailheader theme-color" style="cursor:pointer;width:100%;background-color:#ddd" onclick='jQuery("#propertygallery").slideToggle()'>Gallery <div class="clicktoggle">(click to hide/view)</div></h3>

<div class="galleria" style="max-width:45%;margin:0 auto">
<?php
foreach ($results as $image){
echo "<img src='/crm/uploads/gallery/_".$image->id.".jpg' />";
}
?>
</div>
<script>
    //Galleria.loadTheme('/wp-content/');
    Galleria.run('.galleria', {
		imageCrop:true,
		height: .75,
		//height: 1.5,
		debug:false
		/*extend: function(options) {
		
		       // Galleria.log(this) // the gallery instance
		       // Galleria.log(options) // the gallery options
		
		        // listen to when an image is shown
		        this.bind('image', function(e) {
		
		          //  Galleria.log(e) // the event object may contain custom objects, in this case the main image
		          //  Galleria.log(e.imageTarget) // the current image
		
		            // lets make galleria open a lightbox when clicking the main image:
		            $(e.imageTarget).click(this.proxy(function() {
		               this.openLightbox({
						  height: 600
						});
		            }));
		        });
		
	}*/
});

</script>
<?php
endif;
//print_r($listing);
 ?>		


<div class="row" style="">
<div class="col-12 col-lg-7 col-md-6 col-sm-12">
<?php

	$detailsheader = __("Business Details", 'bbcrm');
if($isuserregistered && $inportfolio){
	$detailsheader = __("Complete Business Profile", 'bbcrm');
}
 ?>
					<!--<h3 class="detailheader theme-color" onclick='jQuery("#propertydetails ").slideToggle()'><?php echo $detailsheader;?></h3>-->


							<div id=propertydetails class="property_details_div">
		
						  <div class="property_details">
							<!--<div class="property_detail"><label><?php _e("Listed on:", 'bbcrm');?></label> <?php echo date('F j, Y',$listing_dateapproved); ?></div>
							
							<div class="property_detail"><label><?php _e("Gross Revenue:", 'bbcrm');?></label> <?php echo $currency_symbol." ".$grossrevenue;?></div>
							<div class="property_detail"><label><?php _e("Down Payment:", 'bbcrm');?></label> <?php echo $currency_symbol." ".$downpayment;?></div>
							<div class="property_detail"><label><?php _e("Terms:", 'bbcrm');?></label> <?php echo $terms;?></div>
							<div class="property_detail"><label><?php _e("Owner's Cash Flow:", 'bbcrm');?></label> <?php echo $currency_symbol." ".$ownercashflow;?></div>-->
							
							
						<!-- 	<div class="property_detail  theme-color" style="cursor:pointer;width:100%;" onclick='jQuery("#brokerdetails ").slideToggle()'><?php _e('Listing Broker','bbcrm');?>  -->
						<div id=brokerdetails class="">
			
<!-- <?php if(is_user_logged_in()){ 
//print_r($buyer);
?>
							<form><input type="button" id="contactlistingbroker" class="contactbroker" data-buyerid="<?php _e($buyer->id);?>" data-listingid="<?php _e($listing->id);?>" data-portfolioid="<?php _e($portfoliolisting->id);?>" name="contact" value="Contact Me"></form>
							<br clear=all><br>
<?php } ?> -->
						</div>
                           </div>
						
						
						<!--<?php if(is_user_logged_in()){ 

$json = x2apicall(array('_class'=>'Media/by:fileName='.$buyerbroker->c_profilePicture.".json"));
$brokerimg =json_decode($json);
?>
						<div class="property_detail"><label><?php _e("Your Broker",'bbcrm');?></label></div>
<?php
if($brokerimg->fileName){
?>	
						<img src="<?php echo "http://".$apiserver."/uploads/media/".$brokerimg->uploadedBy."/".$brokerimg->fileName;?>" height=170 align=right />
<?php } ?>
						<div class="property_detail"><label>Buyer Broker: </label>&nbsp;<?php echo $buyerbroker->name;?></div>
						<div class="property_detail"><label>Cell phone: </label>&nbsp;<?php echo $buyerbroker->c_mobile;?></div>
						<div class="property_detail"><label>Office phone: </label>&nbsp;<?php echo $buyerbroker->c_office;?></div>-->
                       

						<!--<form><input type="button" id="contactbuyerbroker" class="contactbroker" data-buyerid="<?php echo $buyer->id;?>" data-listingid="<?php _e($listing->id);?>" data-portfolioid="<?php echo $portfoliolisting->id ;?>" name="contact" value="Contact Me"></form>
<?php } ?>						
<?php echo wp_get_attachment_image( 5575, 'full', 0, array('class'=>'contactbroker','data-buyerid'=>$buyer->id,'data-listingid'=>$listing->id,'data-portfolioid'=>$portfoliolisting->id) ); ?>	-->
						
						
<div class="property_detail">
	<label style="border-bottom: 1px solid #b2b4b5; font-size: 25px; line-height:40px; font-weight:300; width:100% ; margin-top:36px; margin-bottom:20px;font-family: 'Roboto',Tahoma,Verdana,Segoe,sans-serif;  " >Business Description</label><BR><?php echo nl2br($description); ?></div>		
					<?php if(is_user_logged_in()){ ?>	
							
					<?php if( $inportfolio ): 
					//print_r($listing);
					if($isaddressreleased){
					?>
                    <br />
					<h4 class="detailheader theme-color"><?php _e("Location", 'bbcrm');?></h4>
					<div class="property_detail"><label><?php _e("Address:", 'bbcrm');?></label> <?php echo $listing->c_listing_address_c;?></div>
					<div class="property_detail"><label><?php _e("City:", 'bbcrm');?></label> <?php echo $listing->c_listing_city_c;?></div>
					<div class="property_detail"><label><?php _e("State:", 'bbcrm');?></label> <?php echo $listing->c_listing_region_c;?></div>					
					<div class="property_detail"><label><?php _e("Zip/Postal:", 'bbcrm');?></label> <?php echo $listing->c_listing_postal_c;?></div>
							
                    <h4 class="detailheader theme-color">Additional Information</h4>
					<div class="property_detail"><label>Reason for Selling:</label> <?php echo $listing->c_listing_reasonforselling_c;?></div>
                    <div class="property_detail"><label>Hours of Operation:</label> <?php echo $listing->c_listing_hours_c;?></div>
                    <div class="property_detail"><label>Lease Terms:</label> <?php echo $listing->c_Leaseterms;?></div>
                    <div class="property_detail"><label>Lease Contract Date Start:</label> <?php echo date_format($listing->c_Contractdatestart);?></div>
                    <div class="property_detail"><label>Lease Contract Date End:</label> <?php echo date_format($listing->c_Contractdateend);?></div>
                    <div class="property_detail"><label>Lease Improvements:</label> <?php echo $listing->c_financial_leaseimpr_c;?></div>
                    <div class="property_detail"><label>Lease Copy Available?</label> <?php echo $listing->c_listing_leasecopy_c;?></div>
                    <div class="property_detail"><label>Security:</label> <?php echo $listing->c_listing_security_c;?></div>
                    <div class="property_detail"><label>Rental Increase:</label> <?php echo $currency_symbol . number_format($listing->c_financial_rentincrease_c);?></div>
					<?php } ?>
                    
<h3 class="detailheader theme-color">Business Information</h3>
				<table id="listingtable">
					<tr>
						<td class="listingtablelabel">Franchise?</td>
						<td class="listingtabledata"><?php echo $listing->c_listing_franchise_c;?></td>
						<td class="listingtablelabel">New Franchise?</td>
						<td class="listingtabledata"><?php echo $listing->c_listing_newfranchise_c;?></td>
					</tr>
					<tr>
						<td height="22" class="listingtablelabel">Relocatable?</td>
						<td class="listingtabledata"><?php echo $listing->c_listing_relocatable_c;?></td>
						<td class="listingtablelabel">Home-Based Business?</td>
						<td class="listingtabledata"><?php echo $listing->c_listing_homebusiness_c;?></td>
					</tr>						
					<tr>
						<td class="listingtablelabel">Currently Operating?</td>
						<td class="listingtabledata"><?php echo $listing->c_listing_currently_operating_c;?></td>
						<td class="listingtablelabel">Support/Training?</td>
						<td class="listingtabledata"><?php echo $listing->c_listing_support_training_c;?></td>
					</tr>
                    <tr>
					  <td class="listingtablelabel">Real Estate Available?</td>
					  <td class="listingtabledata"><?php echo $listing->c_listing_reavail_c;?></td>
					  <td class="listingtablelabel">Store Size (Sq.m.):</td>
					  <td class="listingtabledata"><?php echo number_format($listing->c_listing_area_c);?></td>
				 	</tr>
					<tr>
					 <td class="listingtablelabel">Parking Spaces:</td>
					  <td class="listingtabledata"><?php echo $listing->c_listing_pkgspace_c;?></td>
					  <td class="listingtablelabel">Inventory Value:</td>
					  <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_inventoryval_c);?></td>
					</tr>
                    <tr>
					  <td class="listingtablelabel">FT Employees:</td>
					  <td class="listingtabledata"><?php echo number_format($listing->c_listing_emp_ft_c);?></td>
					  <td class="listingtablelabel">PT Employees:</td>
					  <td class="listingtabledata"><?php echo number_format($listing->c_listing_emp_pt_c);?></td>
				  </tr>
					<tr>
					  <td class="listingtablelabel">FF&E:</td>
					  <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_ffae);?></td>
					  <td class="listingtablelabel">Rent up to Date?</td>
					  <td class="listingtabledata"><?php echo $listing->c_listing_rentutd_c;?></td>
				  </tr>
					</table>
           <div style="height:10px;"></div>    
           <div class="property_detail"><label>Inventory/Stock Included in Price?</label> <?php echo $listing->c_listing_inventory_incl_c;?></div>   
           <div class="property_detail"><label>Recent Leasehold Improvements:</label> <?php echo $currency_symbol . number_format($listing->c_recentleaseholdimprovements);?></div>
           <p>&nbsp;</p>
           <h3 class="detailheader theme-color" >Financial Information</h3>
                    <h4 class="detailheader theme-color">Income</h4>
                    <table id="listingtable">
						<tr>
						  <td class="listingtablelabel">Gross Sales:</td>
							<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_sales_c);?></td>
							
							<td class="listingtablelabel">Monthly Gross Sales:</td>
							<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_monthly_sales_c);?></td>
						</tr>
												
						<tr>
						  <td class="listingtablelabel">Gross Revenue:</td>
							<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_grossrevenue_c);?></td>
							
							<td class="listingtablelabel">Monthly Gross Revenue:</td>
							<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_monthly_revenue_c);?></td>
						</tr>					
						<tr>
						  <td class="listingtablelabel">Less Sales Tax (-):</td>
							<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_lesssalestax);?></td>
						<td class="listingtablelabel">Monthly Gross Profit:</td>
							<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_monthly_profit_c);?></td>
					  </tr>
						<tr>
						 <td class="listingtablelabel">Cost of Goods Sold (%):</td>
							<td class="listingtabledata"><?php echo number_format($listing->c_financial_cgs_c) . "%";?></td>
							 <td>&nbsp;</td>
						  <td>&nbsp;</td>
						</tr>
						<tr>
						  <td class="listingtablelabel">Cost of Goods Sold:</td>
							<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_cgstotal_c);?></td>
							 <td>&nbsp;</td>
						  <td>&nbsp;</td>
						</tr>
						<tr>
						  <td class="listingtablelabel">Other Income:</td>
						  <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_other_income_c);?></td>
						  <td>&nbsp;</td>
						  <td>&nbsp;</td>
			  </tr>
						<tr>
						<td class="listingtablelabel">Gross Profit:</td>
						  <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_grossprofit_c);?></td>
						  <td>&nbsp;</td>
						  <td>&nbsp;</td>
			  </tr>
</table>

<!-- Gail's added tables start here -->	

<h4 class="detailheader theme-color">Occupancy Expenses</h4>
				<table id="listingtable">
					<tr>
						<td class="listingtablelabel">Rent:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_rent_c);?></td>
						<td class="listingtablelabel">Utilities:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_utilities_c);?></td>
					</tr>
					<tr>
						<td class="listingtablelabel">CAM:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_cam);?></td>
						<td class="listingtablelabel">Financial Insurance:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format(intval($listing->c_financial_ins_c));?></td>
					</tr>						
					<tr>
						<td class="listingtablelabel">Repairs/Maintenance:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_repairsmaint_c);?></td>
						<td class="listingtablelabel">Rubbish Removal:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_rubbish_c);?></td>
					</tr>					
				</table>

                <h4 class="detailheader theme-color">Operating Expenses</h4>
			<table id="listingtable">
					<tr>
						<td class="listingtablelabel">Advertising:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_advertising_c);?></td>
                        <td class="listingtablelabel">Credit Card Fees:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_ccfees_c);?></td>
					</tr>
					<tr>
						<td class="listingtablelabel">Business Loans:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_businessloans_c);?></td>
						<td class="listingtablelabel">Telephone:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_telephone_c);?></td>
					</tr>						
					<tr>
						<td class="listingtablelabel">Cell Phones:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_cellphones_c);?></td>
						<td class="listingtablelabel">Supplies:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_supplies_c);?></td>
					</tr>
					<tr>
						<td class="listingtablelabel">Interest (eg. Line of Credit):</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_addback_interest_c);?></td>
						<td class="listingtablelabel">Leased Vehicles:</td>
					    <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_vehicles_c);?></td>
			 		</tr>
					<tr>
						<td class="listingtablelabel">Leased Equipment:</td>
					    <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_leasedequip_c);?></td>
						<td class="listingtablelabel">Postage:</td>
					    <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_postage_c);?></td>
			 		</tr>
					<tr>
						<td class="listingtablelabel">Legal/Accounting:</td>
					    <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_legal_acct_c);?></td>
						<td class="listingtablelabel">Travel &amp; Entertainment:</td>
					    <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_te_c);?></td>
			 		</tr>
					<tr>
						<td class="listingtablelabel">Fuel and Vehicle Expense:</td>
						 <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_fuelvehicle_c);?></td>
					     <td>&nbsp;</td>
						 <td>&nbsp;</td>
			  		</tr>					
			</table>
            <h4 class="detailheader theme-color">Payroll Expenses</h4>
				<table id="listingtable">
					<tr>
						<td class="listingtablelabel">Officer Salary:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_officersalary_c);?></td>
						<td class="listingtablelabel">Payroll:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_payroll_c);?></td>
					</tr>
					<tr>
						<td class="listingtablelabel">Payroll Taxes:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_payrolltaxes_c);?></td>
						<td class="listingtablelabel">Employee Health Insurance:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_employeehealthinsurance);?></td>
					</tr>						
					<tr>
						<td class="listingtablelabel">Owner's Health Insurance:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_healthins_owner_c);?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>

                <h4 class="detailheader theme-color">Miscellaneous Expenses</h4>

				<table id="listingtable">
					<tr>
						<td class="listingtablelabel">Miscellaneous 1:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_misc1);?></td>
						<td class="listingtablelabel">Miscellaneous 2:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_misc2);?></td>
				  </tr>
					<tr>
						<td class="listingtablelabel">Miscellaneous 3:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_misc3);?></td>
						<td class="listingtablelabel">Miscellaneous 4:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_misc4);?></td>
					</tr>						
					<tr>
						<td class="listingtablelabel">Miscellaneous 5:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_misc5);?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
                    
                <h4 class="detailheader theme-color">Add-Backs/Adjustments</h4>

<table id="listingtable">
					<tr>
						<td class="listingtablelabel">Officers' Salaries:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_officersalaries_c);?></td>
                        <td class="listingtablelabel">Owner's Health Insurance:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_ownerhealthins_c);?></td>
					</tr>
					<tr>
						<td class="listingtablelabel">Loans:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_loans_c);?></td>
						<td class="listingtablelabel">Interest:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_interest_c);?></td>
					</tr>						
					<tr>
						<td class="listingtablelabel">Owner's Credit Card:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_ownercc_c);?></td>
						<td class="listingtablelabel">Owner Car Lease Payments:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_ownerlease_c);?></td>
					</tr>
					<tr>
						<td class="listingtablelabel">Owner's Cell Phone:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_ownercell_c);?></td>
						<td class="listingtablelabel">Owner's Fuel Expense:</td>
					    <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_ownerfuel_c);?></td>
			 		</tr>
					<tr>
						<td class="listingtablelabel">Miscellaneous 1:</td>
					    <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_misc6);?></td>
						<td class="listingtablelabel">Miscellaneous 2:</td>
					    <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_misc7);?></td>
			 		</tr>
					<tr>
						<td class="listingtablelabel">Miscellaneous 3:</td>
					    <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_misc8);?></td>
						<td class="listingtablelabel">Miscellaneous 4:</td>
					    <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_misc9);?></td>
			 		</tr>
					<tr>
						<td class="listingtablelabel">Miscellaneous 5:</td>
						 <td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_misc10);?></td>
					     <td>&nbsp;</td>
						 <td>&nbsp;</td>
			  		</tr>					
			</table>
            
            <h3 class="detailheader theme-color">Totals:</h3>
				<table id="listingtable">
					<tr>
						<td class="listingtablelabel">Total Expenses:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_total_expenses_c);?></td>
						<td class="listingtablelabel">Monthly Expenses:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_monthly_expense_c);?></td>
					 </tr>
					<tr>
						<td class="listingtablelabel">Yearly Expenses:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_yearlyexpense);?></td>
						<td class="listingtablelabel">Net Profit:</td>
						<td class="listingtabledata"><?php echo $currency_symbol . number_format($listing->c_financial_net_profit_c);?></td>
					</tr>						
					</table>


<!-- Gail's added tables end here 	-->
					<?php elseif($isuserregistered): 
						//_e('For more details on this listing, please add it to your portfolio.','bbcrm');				
					else: 
						//_e("In order to see more details about this listing, please contact your broker to become registered.",'bbcrm');
					endif; ?>
					<?php } ?>
					<br clear=all><br>
<!--							<div class="property_detail"><label style="font-weight:100">Broker:</label> <?php echo $listingbroker->name ;?><label></label> <?php echo $listingbroker->c_mobile;?> / <?php echo $listingbroker->c_email;?></div> -->
						    <div class="property_detail"><label style="font-weight:100">Head Office: <?php echo $bbcrm_option['bbcrm_loginbar_phone'];?></label> / <a href="mailto:kdwilliams@ccbsolutions.com"></a>kdwilliams@ccbsolutions.com</div>
							<br><div style=" display:inline-block; text-align:left; width:auto; height:auto;margin:10px 0; " class="property_detail"><label style="font-weight:normal !important;"><?php _e("Price for Sale ", 'bbcrm');?></label> <?php echo $currency_symbol." ".$amount;?> </div>
							</div>
							
<div style="margin-top:36px;"  >

    <label style="border-bottom: 1px solid #b2b4b5; font-size: 25px; line-height:40px; font-weight:200; width:100% ;font-family: 'Roboto',Tahoma,Verdana,Segoe,sans-serif;" >Business Features / Snapshot</label></div>
    <div style="border-bottom: 1px dashed #b2b4b5;padding-top: 10px;  min-height:33px;"> 
     <label style="font-weight:400; text-align:left;display:inline-block;width: 120px; " >Price:</label>
  <div style="font-weight:800;display:inline; width: auto; text-align:right;float:right; "><label ><?php _e("", 'bbcrm');?></label> <?php echo $currency_symbol." ".$amount;?></div>
    </div>		
    
     <div style="border-bottom: 1px dashed #b2b4b5;padding-top: 10px; min-height:33px; "> 
     <label style="font-weight:400; float:left; " >Trial:</label>
  <div style="font-weight:400; float:right; "></div>
    </div>		
    
    
</div>

<div class="col-12 col-lg-5 col-md-6 col-sm-12" style="background-color: #fff !important; margin-top:25px; ">

		<?php
			// echo '<pre>'; print_r($buyerbroker); echo '</pre>';

		?>

			<?php if ($buyerbroker->name != '') { ?>
			<div class="panel panel-default">
				<div style="background-color: #fff !important;" class="panel-heading">
					<h3 class="panel-title">
						<?php if(is_user_logged_in()){ echo 'Your Business Broker'; } else { echo 'Listing Broker'; } ?>
					</h3>
				</div>
				<div class="panel-body">
						<div class="al-agent-image"><?php
	                      if($brokerimg->fileName){
	                          ?>							
	                       <img src="<?php echo "http://".$apiserver."/uploads/media/".$brokerimg->uploadedBy."/".$brokerimg->fileName;?>" style="width:95px; height: auto;" align=right />
	                        <?php } ?>
	                    </div>
	                    <form method=POST id="broker_frm_<?php echo $buyerbroker->id; ?>" action="<?php echo get_permalink($bbcrm_option["bbcrm_pageselect_broker"]);?>">
		                    <input type=hidden name=eid value="<?php echo $buyerbroker->nameId; ?>">
	                    </form>
						<ul class="agentData">
							<li><h4><?php echo $buyerbroker->name ;?>&nbsp;</h4></li>
							<li>Phone: <strong><?php echo $buyerbroker->c_office;?></strong></li>
							<li>Mobile: <strong><?php echo $buyerbroker->c_mobile;?></strong></li>
							<li>Profile: <a href="javascript: document.getElementById('broker_frm_'+<?php echo $buyerbroker->id; ?>).submit();"><strong>view profile</strong></a></li>
							<li class="icon-links savelisting notsaved">
							<?php
if(is_user_logged_in() && !$inportfolio){
?>
                                                <form method=post>
                                                        <input type=submit style="color:#ffffff !important; font-weight:600; font-size:1.0em; background-color:#333; width:auto;text-align:center; padding:7px 8px 3px 8px; float:right; clear:right; height:auto; border-radius:4px; border:0; vertical-align:bottom; position:absolute: bottom:0; margin-bottom: 4px;" value="SAVE"  />
                                                        <input type=hidden name="action" value="add_to_portfolio" />
                                                        <input type=hidden name="id" value="<?echo $listing->id;?>" />
                                                </form>
<?php 
}else{
	echo '<div class="btn btn-primary" style="color:#fff; width:auto;text-align:center; font-weight:600; font-size:.9em; background-color:#333; auto; padding:7px 8px 3px 8px; clear:right; height:auto; border-radius:4px;" > <span style="color:#fff;" class="glyphicon glyphicon-ok-circle"></span> <a style="color:#fff;" href="/registration/">REQUEST CA</a></div>';
}
?>
							</li>
							<!--<li class="icon-links savelisting notsaved"><div class="btn btn-primary" style="color:#fff; width:auto;text-align:center; font-weight:600; font-size:.9em; background-color:#333; auto; padding:7px 8px 3px 8px; clear:right; height:auto; border-radius:4px;" > <span style="color:#fff;" class="glyphicon glyphicon-ok-circle"></span> <a style="color:#fff;" href="/registration/">SAVE / REQUEST CA</a></div></li>-->
							
						</ul>
				</div>
			</div>
			<?php } ?>
			
			
			<div id="businesslinks" class="panel panel-default">
				<div style="background-color: #fff !important;" class="panel-heading">
					<h3 class="panel-title">
						Business Links / Tools
					</h3>
				</div>
				<div class="panel-body">
					<?php
					$hasMap = false;
					$hasCoordinates = false;
					$mapsLink = '';
					if($inportfolio && $isaddressreleased )
					{
						if ($listing -> c_listing_longitude_c != '' && $listing -> c_listing_latitude_c != '')
						{
							$hasMap = true;
							$hasCoordinates = true;
							//$mapsLink = '//www.google.com/maps/place/'.$listing -> c_listing_latitude_c.','.$listing -> c_listing_longitude_c;
							$mapsLink = '//maps.google.com/maps?q='.$listing -> c_listing_latitude_c.','.$listing -> c_listing_longitude_c;
						}
						elseif ($listing -> c_listing_postal_c != '' && $listing -> c_listing_city_c != '' && $listing -> c_listing_address_c != '')
						{
							$hasMap = true;
							//$mapsLink = '//www.google.com/maps/place/'.$listing -> c_listing_latitude_c.','.$listing -> c_listing_longitude_c;
							$mapsLink = '//maps.google.com/maps?daddr='.urlencode($listing -> c_listing_postal_c.' '.$listing -> c_listing_city_c.' '.$listing -> c_listing_address_c);
							$mapsAddress = urlencode($listing -> c_listing_postal_c.' '.$listing -> c_listing_city_c.' '.$listing -> c_listing_address_c);
						}
						elseif ($listing -> c_listing_town_c != '' && $listing -> c_listing_city_c != '')
						{
							$hasMap = true;
							$mapsLink = '//maps.google.com/maps?daddr='.urlencode($listing -> c_listing_town_c.' '.$listing -> c_listing_city_c.' '.$listing-> c_listing_address_c);
							$mapsAddress = urlencode($listing -> c_listing_town_c.' '.$listing -> c_listing_city_c.' '.$listing-> c_listing_address_c);					
							
						}
					} 
					else
					{
						if ($listing -> c_listing_town_c != '' && $listing -> c_listing_city_c != '')
						{
							$hasMap = true;
							$mapsLink = '//maps.google.com/maps?daddr='.urlencode($listing -> c_listing_town_c.' '.$listing -> c_listing_city_c);
							$mapsAddress = urlencode($listing -> c_listing_town_c.' '.$listing -> c_listing_city_c);
							
							
						}
						
					}
					
				?>
					<ul class="listReset listingLinks">

	<li class="icon-links"><a href="javascript:print();" class="printPage"><span class="glyphicon glyphicon-print"></span> Print Page</a></li>
	<li class="icon-links"><a rel="prettyPhotoIFRAME" title="Email this listing to a friend" href="mailto:?subject=Listing: <?php echo $generic_name; ?>&body=Check out <?php echo $generic_name; ?> at <?php echo get_site_url();?>/listing/<?php echo sanitize_title($generic_name)."--".$listing->id;?>"><span class="glyphicon glyphicon-envelope"></span> Email to a Friend</a></li>
	<li class="icon-links"><a href="" title="Superior Fruit and Vegetable Business for Sale – Ref: 2963" class="jQueryBookmark"><span class="glyphicon glyphicon-book"></span> Bookmark Page</a></li>
	<?php if ($mapsLink) { ?>
	<li class="icon-links" ><a href="<?php echo $mapsLink;?>" target="_blank"><span class="glyphicon glyphicon-map-marker"></span> Map directions</a></li>
	<?php } ?>
</ul>

				</div>
			</div>
			<?php if ($hasMap) { ?>
			<div class="panel panel-default" >
				<div style="background-color: #fff !important;" class="panel-heading">
					<h3 class="panel-title">
						Business Location
					</h3>
				</div>
				<div class="panel-body" id="business_map" style="height: 350px;">
					<?php if ($hasCoordinates) { ?>
					<script>
						//function initMap() {
					        var myLatLng = {lat: <?php echo $listing -> c_listing_latitude_c; ?>, lng: <?php echo $listing -> c_listing_longitude_c; ?>};
					
					        var map = new google.maps.Map(document.getElementById('business_map'), {
					          zoom: 16,
					          center: myLatLng
					        });
					
					        var marker = new google.maps.Marker({
					          position: myLatLng,
					          map: map,
					          title: '<?php echo $generic_name; ?>'
					        });
					     // }
				    </script>
				    <!--<script src="https://maps.googleapis.com/maps/api/js?callback=initMap" async defer></script>-->
					<?php } else { ?>
					<script>
						var geocoder = new google.maps.Geocoder();
				            geocoder.geocode({ 'address': '<?php echo $mapsAddress; ?>' }, function (results, status) {
					            if (status == google.maps.GeocoderStatus.OK) {
				                    var latitude = results[0].geometry.location.lat();
				                    var longitude = results[0].geometry.location.lng();
				                    
				                     var myLatLng = {lat: latitude, lng: longitude};
									    var map = new google.maps.Map(document.getElementById('business_map'), {
								          zoom: 10,
								          center: myLatLng
								        });
								
								        var marker = new google.maps.Marker({
								          position: myLatLng,
								          map: map,
								          title: '<?php echo $generic_name; ?>'
								        });
								        
				                } else {
				                   
				                }
						    });
						
					</script>		
					<?php } ?>
				</div>
			</div>
			<?php } ?>

</div>
						</div><!-- .entry-content -->

					</div><!-- #post-## -->
<br clear=all><br>




</div>
</div>

<div id="sidebar" style="padding:12px; margin:0px;" class="row">
					
	<?php if(is_user_logged_in()){ 
	        dynamic_sidebar( "property-registered" ); 
		}else{
			echo do_shortcode('[visitorcontact]');
			dynamic_sidebar( "property-unregistered" ); 
		}        
        ?>
    			</div><!-- #sidebar -->
	</div>
</section>	
</div>
<?php
get_footer();

