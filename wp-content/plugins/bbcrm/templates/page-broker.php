<?php
/*
Template Name: Broker Profile
*/


//ini_set('display_errors', true);
//error_reporting(E_ALL);
//print_r($wp_query);
//exit();



if(!isset($_POST['eid'])):
wp_redirect('/find-a-broker/');
endif;


//print_r($wp_query);


$json = x2apicall(array('_class'=>'Brokers/by:nameId='.urlencode($_POST["eid"]).".json"));
$broker =json_decode($json);



//global $pagetitle;

get_header();

   //retrieve records ----------------------------------------

?>



<?php echo do_shortcode('[listmenu menu="Sub Home" menu_id="sub_home" menu_class="au_submenu"]');?>


<section id="content" data="property" style="min-height:500px;"> 
	<div class="portfolio_group">
		<div class="container-fluid search_result">
			<div class="">

				<div class="col-md-3 sidebar_content">
				
				<div class="panel-group" id="accordion">
						  <div class="panel panel-default">
							<div class="panel-heading">
							  <h4 style="line-height: 40px;" class="panel-title">
								<a style="color:#333; font-weight:100; font-size: 24px; " class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
								  Business Search
								</a>
							  </h4>
							</div>
							<div id="collapseOne" class="panel-collapse collapse">
							  <div class="panel-body">
								 <?php echo do_shortcode('[featuredsearch]');?>
							  </div>
							</div>
						  </div>
 
 
						
						
						<br> 
					  <div class="sidebar_search_by_id_container" >
						  <h3 class="panel-title">
							Find by ID
						  </h3>
						  <?php echo do_shortcode('[searchbyid addbutton=false]'); ?>	
					  </div>
                        <br>
				</div>
		</div>

				<div  id="business_container" class="col-md-9 searchlists_container" style="margin-top:48px;">
                      
                       

<?php

////////////////////
 
//$json = x2apicall(array('_class'=>'Brokers/by:nameId='.urlencode($_POST["eid"]).".json"));
//$broker =json_decode($json);
?>
	<h1 class='article-page-head'><?php echo __("",'bbcrm')." <span class='theme-color1'>". $broker->name;?></span></h1>
	<div style="font-size:24px; font-weight: 200; color: #999;" class="property_detail"><?php echo $broker->c_position;?></div>	
<div>
<?php the_content();?>
<div id="broker-<?php echo $broker->nameId;?>" class="brokeritem theme-background" style="padding:10px;min-height:90px">
<?php
        if($broker->c_profilePicture){
                $json = x2apicall(array('_class'=>'Media/by:fileName='.$broker->c_profilePicture.".json"));
                $brokerimg =json_decode($json);
                echo '<div style="float:right;width:250px;height:250px;overflow:hidden;"><img src="http://'.$apiserver.'/uploads/media/'.$brokerimg->uploadedBy.'/'.$brokerimg->fileName.'" style="width:100%"  style="clear:both" /></div>';
        }else{

//print_r($broker);

                echo '<div style="float:right;display:inline"><img src="http://'.$apiserver.'/uploads/media/marc/broker-'.$broker->c_gender.'.png" height=170 /></div>';

        }
?>

		
				<br><br>	
				
		<div class="property_detail"><label><i class="fa fa-phone"></i>&nbsp;<?php _e('','bbcrm');?></label> <?php echo $broker->c_office; ?></div>	
		<div class="property_detail"><label><i class="fa fa-mobile-phone"></i>&nbsp;<?php _e('','bbcrm');?></label> <?php echo $broker->c_mobile; ?></div>
	 <div class="property_detail"><label><a style="color:steelblue;" href="mailto:<?php echo $broker->c_email;?>"><i style="color:steelblue;padding-right:7px;" class="fa fa-envelope-o"></i>Send Email</a> </div>	
     

			  



				
	<br><br>
		<div class="property_detail"><?php echo $broker->description; ?></div>		
<br clear=all><br>


<!--portfolioitem-->
</div>
</div>
<!-- start listing-->



<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="tabbable" id="tabs-181305">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#panel-703440" data-toggle="tab">Current Listings </a>
					</li>
					<li>
						<a href="#panel-21458" data-toggle="tab">Recently Sold</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="panel-703440">
						<br>
						<div style='margin-top:25px;'> 
						<br><p>
                       Current Listings
                       </p>
<?php echo do_shortcode('[featuredlistings num=-1 featured=0 broker="'.urlencode($_POST["eid"]).'"]');?>
</div>
					</div>
					<div class="tab-pane" id="panel-21458">
						<br><p>
							Recently Sold Businesses
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>





</div >
</div >
<!-- end listing-->
<!-- #bc
				 <aside id="sidebar" class="sidebar" role="complementary">
					 <ul>
				 <?php 
				 if(is_user_logged_in()){
				 $userdata = get_userdata(get_current_user_id());
				 $json = x2apicall(array('_class'=>'Contacts/by:email='.urlencode($userdata->user_email).".json"));
				 $buyer =json_decode($json);
				 $json = x2apicall(array('_class'=>'Brokers/by:nameId='.urlencode($buyer->c_broker).".json"));
				 $buyerbroker =json_decode($json);
				 $json = x2apicall(array('_class'=>'Media/by:fileName='.$buyerbroker->c_profilePicture.".json"));
				 $brokerimg =json_decode($json);
				 //print_r($buyer);

				 ?>
										 <h3 class="widget-title"><?php _e("Your Broker");?></h3>
				 <div class="textwidget">
				 <?php
				 if($brokerimg->fileName){
				 ?>						
										 <img src="<?php echo "http://".$apiserver."/uploads/media/".$brokerimg->uploadedBy."/".$brokerimg->fileName;?>" height=170 />
				 <?php } ?>
										 <h3><?php echo $buyerbroker->name;?></h3>
				 <i class="fa fa-phone"></i> <?php _e("Cell phone",'bbcrm');?>:<a href="tel:<?php echo $buyerbroker->c_mobile;?>"><?php echo $buyerbroker->c_mobile;?></a><br>
				 <i class="fa fa-phone"></i> <?php _e("Office phone",'bbcrm');?>:<a href="tel:<?php echo $buyerbroker->c_office;?>"><?php echo $buyerbroker->c_office;?></a><br>
				 <i class="fa fa-at"></i> <?php _e("Contact Agent",'bbcrm');?>:<a href="mailto:<?php echo $buyerbroker->c_email;?>"><?php echo $buyerbroker->c_email;?></a><br>

				 </div>
				 <?php 
				 }
				 dynamic_sidebar( "content-sidebar" );
				  ?>
					 </ul>
				 </aside>-->
				 
</div></div>
</section><!-- #primary .widget-area -->

<?php get_footer(); ?>
