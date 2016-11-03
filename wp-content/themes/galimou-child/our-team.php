<?php
/*
Template Name: Our Team
*/

//ini_set('display_errors','on');
//error_reporting(E_ALL);

global $wp_query,$url;

//die;
if(is_user_logged_in()){
//...only if logged in?...
$userdata = get_userdata(get_current_user_id());
}

get_header();
?>
<section id="content" class="container" style="margin-top:20px">
   <div class="portfolio_group">
		<div id="business_container" class="container-fluid article-page" style="">
		    <div class="row">
		
							<!--  <div class="col-12 col-sm-4 col-lg-3 sidebar_content" style=" margin-top: 45px;">
				
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
 
 
									  </div>
						
									  <br> 
									<div class="sidebar_search_by_id_container" >
										<h3 class="panel-title">
										  Find by ID
										</h3>
										<?php echo do_shortcode('[searchbyid addbutton=false]'); ?>	
									</div>
									  <br>
				
					  </div> -->

      <div  id="" class="col-12 col-sm-8 col-lg-9 searchlists_container">
			<h1 class="article-page-head"><?php echo get_the_title();?></h1>
					<div  id="" class="row profileDisplay">
										  
										  <?php

										  ////////////////////
										   echo get_the_content();
 
										  $json = x2apicall(array('_class'=>'Brokers/?_order=-c_position'));
										  $brokers =json_decode($json);

										  if($brokers){
											  echo "<div style='display:inline-block'>"; //this is so the page doesn't scroll endlessly.
										  //////////////////

										  $altcss = "#dddddd";
										  foreach ($brokers AS $broker){ //The l

											  if("Active" == $broker->c_status){
												  $phone_mobile = $broker->c_mobile;
												  $phone_office = $broker->c_office;
												  $broker_email = $broker->c_email;
												  $broker_description = $broker->description;
												  $broker_position = $broker->c_position;		
												  $altcss = ($altcss == "#dddddd")?"#dddddd":"#dddddd";
												  $altclass = ($altcss == "#dddddd")?"":"";
												  $butclass = ($altcss == "")?"altbrokerprofilebutton":"brokerprofilebutton";
										  ?>

                                     <div id="broker-<?php echo $broker->id;?>" class="brokeritem col-md-4" >

						  
											 <?php
												 if($broker->c_profilePicture){
													 $json = x2apicall(array('_class'=>'Media/by:fileName='.urlencode($broker->c_profilePicture).".json"));
													 $brokerimg =json_decode($json);
													 echo '<div style="display:inline-block;width:200px;height:auto;overflow:hidden;margin:22px 10px 10px 1px;"><img class="pImg" src="http://'.$apiserver.'/uploads/media/'.$brokerimg->uploadedBy.'/'.$brokerimg->fileName.'" style="width:100%" /></div>';	
												 }else{

												 //print_r($broker);

												 echo '<div style="display:inline-block; width:130px;height:auto;overflow:hidden;margin:22px 10px 10px 1px;"><img class="pImg"  src="http://'.$apiserver.'/uploads/media/marc/broker-'.$broker->c_gender.'.png" style="width:100%"  /></div>';
	
											 }
										 ?>

						                       <div class="property_detail"><h3><label><? _e('','bbcrm');?></label><?php echo $broker->name; ?></h3></div>
							                     </br>
							                    <div style=""> <? _e('','bbcrm');?> <?php echo $broker_position; ?></div> 
							        </div>
						
						
						
				</div><!--row-->
			</div><!--9er-->
	
						<!--portfolioitem-->
<?php 
		} //end if active
	}
}
?>
</div>
</div>
</div>

</section><!-- #primary .widget-area -->

<?php
get_footer();
?>
?>
