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
	$trailing = (substr($_SERVER["REQUEST_URI"],-1)=="/")?"":"/";
	$json = x2apicall(array('_class'=>'Clistings/by:c_listing_frontend_url='.$_SERVER["REQUEST_URI"].$trailing.'.json'));
}
$listing = json_decode($json);

//print_r($listing);
$json = x2apicall(array('_class'=>'Clistings/'.$listing->id.'/tags'));
$tags = json_decode($json);

$listingtags = array();
foreach ($tags as $idx=>$tag){
	$listingtags[] = urldecode(substr($tag, 1));
}

/* Failsafe. Need to move to create flow */
if(empty($listing->c_listing_frontend_url)){
$json = x2apipost( array('_method'=>'PUT','_class'=>'Clistings/'.$listing->id.'.json','_data'=>array('c_listing_frontend_url'=>'/listing/'.sanitize_title($listing->c_name_generic_c)."/") ) );
}

$json = x2apicall(array('_class'=>'Brokers/by:nameId='.urlencode($listing->c_assigned_user_id).".json"));
$listingbroker =json_decode($json);

if(!$listingbroker->nameId){
$json = x2apicall(array('_class'=>'Brokers/by:nameId=House%20Broker_5.json'));
$listingbroker =json_decode($json);
}

if(is_user_logged_in() ){	
	$json = x2apicall(array('_class'=>'Contacts/by:email='.urlencode($userdata->user_email).".json"));
	$buyer =json_decode($json);

$isuserregistered = ($buyer->c_buyer_status=="Registered")?true:false;
	$json = x2apicall(array('_class'=>'Brokers/by:nameId='.urlencode($buyer->c_broker).".json"));
	$buyerbroker =json_decode($json);	

if(isset($_POST["add_to_portfolio"]) || isset($_POST['action']) && $_POST["action"]=="add_to_portfolio"){

	$json = x2apicall(array('_class'=>'Portfolio/by:c_listing_id='.$listing->id.";c_buyer=".urlencode($buyer->nameId).".json"));
	$prevlisting =json_decode($json);	

	if(!$prevlisting->status || $prevlisting->status=="404"){
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

		$status =$listing->c_sales_stage;
		$listing_id =$listing->id;
		$listing_dateapproved = $listing->c_listing_date_approved_c;
		$generic_name =$listing->c_name_generic_c;
		$description =$listing->description;
		$region=$listing->c_listing_region_c;
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

get_header();
?>
<div class="container-fluid">
<section id="content" class="portfolio_group" data="property">
	<div class="row" style="padding:12px; margin:0px">
	<div style="min-width:300px !important; display: inline-block; clear: none; margin-top: 45px;" class="col-md-3">

<?php get_sidebar('page'); ?>
</div>



<div style="clear:none; width:65%" class="col-md-9">
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
?>

<div style="position: relative;" id="business_container" role="main" >
	<div class="">
	           
	  <div class="pull-left" style="display:inline; width:60%;">
			    <div class="al-title property-title entry-title "> <?php echo $cssclass;?><?php echo $generic_name; ?></div>	
			<br><div class="al-price property_detail"><label><?php _e("", 'bbcrm');?></label><?php echo $currency_symbol." ".$amount;?> + SAV</div>
			<br><div class="al-cat property_detail"><label><?php _e("", 'bbcrm');?></label><?php echo $categories; ?></div>
		</div>	
		 <div class="pull-right" style="display:inline; width:40%;">
                <div class="al-id property_detail" id="property_listing_id" data-id="<?php echo $listing_id;?>"><label><?php _e("ID Ref:", 'bbcrm');?></label>#<?php echo preg_replace("/[^0-9,.]/","",$listing->c_name_generic_c); ?></div>
             <br><div class="al-region property_detail"><label><?php _e("County:", 'bbcrm');?></label><?php echo $suberb;?></div>	
             <br><div class="al-region property_detail"><label><?php _e("State:", 'bbcrm');?></label><?php echo $region;?></div>
             <br><div class="al-status property_detail"><label><?php _e("Status:", 'bbcrm');?></label><?php echo $status; ?></div>
<?php
if(is_user_logged_in() && !$inportfolio){
?>
                                                <form method=post>
                                                        <input type=submit style="color:#fff; font-weight:600; font-size:1.0em; background-color:#333; width:auto;text-align:center; padding:7px 8px 3px 8px; float:right; clear:right; height:auto; border-radius:4px; vertical-align:bottom; position:absolute: bottom:0; margin-bottom: 4px;" value="<?php _e('SAVE','bbcrm');?>" class=""  />
                                                        <input type=hidden name="action" value="add_to_portfolio" />
                                                        <input type=hidden name="id" value="<?echo $listing->id;?>" />
                                                </form>
<?php 
}else{
	echo '<div style="color:#fff; font-weight:600; font-size:1.0em; background-color:#333; width:auto;text-align:center; padding:7px 8px 3px 8px; float:right; clear:right; height:auto; border-radius:4px; vertical-align:bottom; position:absolute: bottom:0; margin-bottom: 4px;" ><span style="color:#fff;" class="glyphicon glyphicon-ok-circle"></span> <a style="color:#fff;" href="/registration/">REQUEST CA</a></div>';
}
?>     </div>

 </div>
</div>

</div>
				
				
				<br clear=all><br>
				<div class="entry-content" style="background-color:#ffffff; border:1px solid #ddd; padding:13px;">
								
<?php
global $wpdb;
$results = $wpdb->get_results( 'SELECT gp.* FROM x2_gallery_photo gp RIGHT JOIN x2_gallery_to_model gm ON gm.galleryId = gp.gallery_id WHERE gm.modelName="Clistings" AND gm.modelId='.$listing->id, OBJECT );

if(!empty($results[0]->id)):
?>
						<h3 class="detailheader theme-color" style="cursor:pointer;width:100%;background-color:#ddd" onclick='jQuery("#propertygallery").slideToggle()'>Gallery <div style="display:inline;float:right;font-size:.6em;margin:auto 6px;">(click to hide/view)</div></h3>
						
<div id=propertygallery style="height:460px;display:block">
<?php
foreach ($results as $image){
//echo $image->file_name;
//echo substr($image->file_name,-3);
echo "<div style='display:inline-block;padding:4px;width:200px;height:200px;overflow:hidden;vertical-align:middle;margin-right:2px;'><img style='width:100%' src='/crm/uploads/gallery/_".$image->id.".jpg' /></div>";
}

endif; ?>					


<div class="row" style="">
<div class="col-md-8">
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
                         <?php if(is_user_logged_in()){ ?>

						<!--<form><input type="button" id="contactbuyerbroker" class="contactbroker" data-buyerid="<?php echo $buyer->id;?>" data-listingid="<?php _e($listing->id);?>" data-portfolioid="<?php echo $portfoliolisting->id ;?>" name="contact" value="Contact Me"></form>
<?php } ?>						
<?php echo wp_get_attachment_image( 5575, 'full', 0, array('class'=>'contactbroker','data-buyerid'=>$buyer->id,'data-listingid'=>$listing->id,'data-portfolioid'=>$portfoliolisting->id) ); ?>	-->
						
						
<div class="property_detail"><label style="border-bottom: 1px solid #b2b4b5; font-size: 25px; line-height:40px; font-weight:300; width:100% ; margin-top:36px; margin-bottom:20px;font-family: 'Roboto',Tahoma,Verdana,Segoe,sans-serif;  " >Business Description</label><BR><?php echo nl2br($description); ?></div>		
							
							
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
					<!-- <?php elseif($isuserregistered): 
						_e('For more details on this listing, please add it to your portfolio.','bbcrm');				
					else: 
						_e("In order to see more details about this listing, please contact your broker to become registered.",'bbcrm');
					endif; ?>-->
					<br clear=all><br>
							<div class="property_detail"><label style="font-weight:100">Broker:</label> <?php echo $listingbroker->name ;?><label></label> <?php echo $listingbroker->c_mobile;?> / <?php echo $listingbroker->c_email;?></div>
						    <div class="property_detail"><label style="font-weight:100">Head Office: <?php echo $bbcrm_option['bbcrm_loginbar_phone'] ?></label> / </div>
							<br><div style=" display:inline-block; text-align:left; width:auto; height:auto;margin:10px 0; " class="property_detail"><label style="font-weight:normal !important;"><?php _e("Price for Sale ", 'bbcrm');?></label> <?php echo $currency_symbol." ".$amount;?> + SAV</div>
							</div>
							
<div style="margin-top:36px;"  >

    <label style="border-bottom: 1px solid #b2b4b5; font-size: 25px; line-height:40px; font-weight:200; width:100% ;" >Business Features / Snapshot</label></div>
    <div style="border-bottom: 1px dashed #b2b4b5;padding-top: 10px;  min-height:33px;"> 
     <label style="font-weight:400; text-align:left;display:inline-block;width: 120px; " >Price:</label>
  <div style="font-weight:800;display:inline; width: auto; text-align:right;float:right; "><label ><?php _e("", 'bbcrm');?></label> <?php echo $currency_symbol." ".$amount;?> + SAV</div>
    </div>		
    
     <div style="border-bottom: 1px dashed #b2b4b5;padding-top: 10px; min-height:33px; "> 
     <label style="font-weight:400; float:left; " >Trial:</label>
  <div style="font-weight:400; float:right; "></div>
    </div>		
    
    
</div>

<div class="col-md-4" style="background-color: #fff !important; margin-top:78px;">


			<div class="panel panel-default">
				<div style="background-color: #fff !important;" class="panel-heading">
					<h3 class="panel-title">
						Your Business Broker
					</h3>
				</div>
			<div class="panel-body">
				  <div class="pull-left" style="display:inline-block; width: 40%;" >
					  <div class="al-agent-image"><?php
                      if($brokerimg->fileName){
                          ?>							
                       <img src="<?php echo "http://".$apiserver."/uploads/media/".$brokerimg->uploadedBy."/".$brokerimg->fileName;?>" height=170 align=right />
                        <?php } ?>
                      </div>
                  </div>
                         <div class="pull-right" style="display:inline-block; width: 60%; padding-left:5px;">
                         
							<div style="display:inline-block; width: auto; font-weight:bold; font-size:17px;" class="property_detail"><label></label><?php echo $listingbroker->name ;?></div>
							<div style="display:inline-block; width: auto;"class="property_detail"><label style="font-size:12px; font-weight: 200;">Phone:</label><?php echo $listingbroker->c_office;?></div>
							<div style="display:inline-block; width: auto;"class="property_detail"><label style="font-size:12px; font-weight: 200;">Mobile:</label><?php echo $listingbroker->c_mobile;?></div>
				            <div style="color:#fff; width:auto;text-align:center; font-weight:600; font-size:.9em; background-color:#333; auto; padding:7px 8px 3px 8px; clear:right; height:auto; border-radius:4px;" >
				            <span style="color:#fff;" class="glyphicon glyphicon-ok-circle"></span> <a style="color:#fff;" href="/registration/">REGISTER</a></div>
				         </div>


				</div>
			</div>
			
			<div class="panel panel-default">
				<div style="background-color: #fff !important;" class="panel-heading">
					<h3 class="panel-title">
						Business Links / Tools
					</h3>
				</div>
				<div class="panel-body">
					<ul class="listReset listingLinks">

	<li class="icon-links"><a href="/pdf/re-pdf-1-hd.php?id=1429&amp;ut=1470051416" class="printPage" target="_blank"><span class="glyphicon glyphicon-print"></span> Print PDF</a></li>
	<li class="icon-links"><a rel="prettyPhotoIFRAME" title="Email this listing to a friend" href="/re-email-friend.php?iframe=true&amp;width=800&amp;height=600&amp;lid=1429&amp;t=1470051416"><span class="glyphicon glyphicon-envelope"></span> Email to a Friend</a></li>
	<li class="icon-links"><a href="" title="Superior Fruit and Vegetable Business for Sale – Ref: 2963" class="jQueryBookmark"><span class="glyphicon glyphicon-book"></span> Bookmark Page</a></li>
	<li class="icon-links"><a href="http://maps.google.com/maps?daddr=Brisbane, Queensland 4001" target="_blank"><span class="glyphicon glyphicon-map-marker"></span> Map directions</a></li>
</ul>

				</div>
			</div>
			
			<div class="panel panel-default">
				<div style="background-color: #fff !important;" class="panel-heading">
					<h3 class="panel-title">
						Business Location
					</h3>
				</div>
				<div class="panel-body">
					GoogleMap Here
				</div>
			</div>


</div>
						</div><!-- .entry-content -->

					</div><!-- #post-## -->
<br clear=all><br>




</div>
</div>

<div id="sidebar">
					
<?php
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
?>
