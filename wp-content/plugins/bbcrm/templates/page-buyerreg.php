<?php
/*
Template Name: Buyer Registration
*/

//ini_set('display_errors','on');
//error_reporting(E_ALL);
session_start();
//print_r($_SESSION);

if(!empty($_POST)){
	$_POST["name"]=$_POST["firstName"]." ".$_POST["lastName"];
	$_POST["visibility"] = '0';
	$_POST["c_username"] = $_POST["email"];
	$result = x2apipost(array('_class'=>'Contacts/','_data'=>$_POST));
//print_r($result);
	if($result[0]=="201"){
		$newuser = json_decode($result[1]);
		//Create NDA Document
		$qy["data"] = http_build_query($_POST); 
		$userndadoc = json_decode(buyerreg_ajax_getnda($qy) );
		$usernda = x2apipost(array('_class'=>'Docs/','_data'=>array('name'=>$_POST["name"].' NDA Agreement','text'=>$userndadoc->text)));
		
//ADD: docs/buyer relationship
#$json = x2apipost( array('_class'=>'Contacts/'.$_GET["pid"].'/relationships/'.##BUYERID##.'.json','_data'=>$data ) );		
		
		$newuser = wp_create_user( $_POST["email"], $_POST["c_password"], $_POST["email"] );
	//	print_r($newuser);
//exit;

	$json = x2apicall(array('_class'=>'Contacts/by:email='.urlencode($_POST["email"]).".json"));
	$buyer =json_decode($json);
$json = x2apicall(array('_class'=>'Clistings/'.$_POST["listingid"].'.json'));
$listing = json_decode($json);
//print_r($buyer);
	$json = x2apicall(array('_class'=>'Brokers/by:nameId='.urlencode($buyer->c_broker).".json"));
	$buyerbroker =json_decode($json);	

if(isset($_POST['listingid']) && $_POST["listingid"]!=""){

	$data = array(
		'name'	=>	'Portfolio listing for '.$listing->name,
		'c_listing'	=>	$listing->name,
		'c_listing_id'	=>	$listing->id,
		'c_buyer'	=>	$buyer->name,
		'c_buyer_id'	=>	$buyer->id,
		'c_release_status'	=>	'Added',
		'assignedTo'	=>	$buyerbroker->assignedTo,
	);
	$json = x2apipost( array('_class'=>'Portfolio/','_data'=>$data ) );
}
		
		//putfile($filedata);
		wp_redirect("/welcome/");
		exit;
	}else{
		echo $result[0];
	}
	
}
wp_enqueue_script('jquery');
get_header();

	$listingoptions =array();

if(!empty($_SESSION["viewed_listings"])){	

	foreach($_SESSION["viewed_listings"] AS $k=>$v){
		//$listingoptions .= "<option value='$k'>".$v["listingname"]."</option>";
		$listingoptions[] = 	'"'.$v["listingname"].'":"'.$k.'"';
	}
}else{
		$listingoptions[] = 	'"":"No listings viewed"';

}

$json = x2apicall(array('_class'=>'dropdowns/1002.json'));
$states =json_decode($json);

if($states){
	$statesselect = array();
foreach ($states->options AS $k=>$v){
	$statesselect[] = 	'"'.$k.'":"'.$v.'"';
	}	
}	

//Get the brokers in the system
$json = x2apicall(array('_class'=>'Brokers/'));
$brokers =json_decode($json);

if($brokers){
	$brokerselect = array();
foreach ($brokers AS $broker){
$brokerselect[] = 	'"'.$broker->name.'":"'.$broker->nameId.'"';
	}
}		
?>
<script>
	jQuery(document).ready(function(){ 
		var newOptions = {<?php echo join(',',$statesselect);?>};
		var el = jQuery("#state");
			el.append(jQuery("<option></option>").attr("value", "").text("<?php __('Choose A State');?>"));
			jQuery.each(newOptions, function(key, value) {
				el.append(jQuery("<option></option>").attr("value", value).text(key));
			});

		var newListings = {<?php echo join(',',$listingoptions);?>};
		var el = jQuery("#listingid");
			el.append(jQuery("<option></option>").attr("value", "").text("<?php __('Choose A Listing');?>"));
			jQuery.each(newListings, function(key, value) {
				el.append(jQuery("<option></option>").attr("value", value).text(key));
			});

		var newBrokers = {<?php echo join(',',$brokerselect);?>};
		var el = jQuery("#c_broker");
		el.append(jQuery("<option></option>").attr("value", "House Broker_5").text("<?php __('Choose A Broker');?>"));			
			jQuery.each(newBrokers, function(key, value) {
				el.append(jQuery("<option></option>").attr("value", value).text(key));
			});
jQuery("#broker").change(function(){
	jQuery.getJSON('<?php echo plugin_dir_url().'/bbcrm/_auth.php'; ?>',
	{query:	'AJAX',action: 'x2apicall',_class:"Brokers/by:nameId="+encodeURI(jQuery(this).val())+".json"}, 
	function(response){
//		console.log(response)
		jQuery("#assignedTo").remove();
		jQuery("#form_buyerreg").append("<input type='hidden' id='assignedTo' name='assignedTo' value='"+response.assignedTo+"' />");
	});
})		

jQuery("#email").blur(function(){
	jQuery("#emailerr").remove();		
emailaddr = jQuery(this).val()
jQuery.get('<?php echo plugin_dir_url().'/bbcrm/_auth.php'; ?>',
{query:	'AJAX',action: 'isvalidemail',email:emailaddr}, 
	function(response){
		if(!response){
			jQuery("#emailerr").remove();
			jQuery("#email").parent().prepend("<div id='emailerr' class='formerr'>That is not a valid email</div>")
			jQuery("#email").select();								
		//return false;		
		}else{
			jQuery.getJSON('<?php echo plugin_dir_url().'/bbcrm/_auth.php'; ?>',
			{query:	'AJAX',action: 'x2apicall',_class:	'Contacts/by:email='+emailaddr+'.json'}, 
				function(response){
					if(!response.status){
						jQuery("#emailerr").remove();
						jQuery("#email").parent().prepend("<div id='emailerr' class='formerr'>That email is already in the system.</div>")
						jQuery("email").select();		
					}else{
						jQuery("#emailerr,#emailsuc").remove();
						jQuery("#email").parent().prepend("<div id='emailsuc' class='formsuc'>That email is available.</div>")
					}        				
 				});
		}
	});
});
					
			
			jQuery("#snap").click(function(event){
					event.preventDefault();
					//alert("caught");
			});			
			jQuery("#form-reg").click(function(event){
					//event.preventDefault();					
					if(jQuery("#c_password").val() != jQuery("#password2").val() ){
						jQuery("#c_password").parent().before("<div id='pwderr' class='formerr'>The passwords do not match.</div>");
						jQuery("#form1,#form2").toggle();
						return false;
					}else{
						jQuery("#pwderr").remove();					
					}
					if(jQuery("#accept-sig").val() != jQuery("#firstName").val()+" "+jQuery("#lastName").val() ){
						jQuery("#accept-sig").parent().before("<div id='accept-sigerr' class='formerr'>This field must match the first name and last name you entered. Please check your entries.</div>");
						return false;
					}else{
						jQuery("#accept-sigerr").remove();
					}
					if(!jQuery("#acceptance").prop("checked") ){
						jQuery("#acceptance").parent().prepend("<div id='acceptanceerr' class='formerr'>Please check this field in order to continue.</div>");
						return false;
					}
					else{
						jQuery("#acceptanceerr").remove();
						jQuery("#form-reg").submit();
					}
			});
						
			jQuery("#form-next,#form-back").click(function(event){
				event.preventDefault();
				jQuery(".formerr").remove()
				jQuery("#prefinal").show()
       		jQuery("#regfinal").hide()
fail=false;
fail_log = '';
jQuery('#form_buyerreg input, #form_buyerreg select' ).each(function(){
            if(typeof jQuery(this ).attr('required' ) == "undefined" || jQuery(this).prop('required') == false){
            } else {
                if (!jQuery(this ).val() ) {
                    fail = true;
                    name = jQuery(this ).attr('name' );                   
							jQuery(this ).prev().append("<span class='formerr'>This field is required.</span>");
                    fail_log += jQuery("label[for='"+name+"']").html() + " is required \n";
                }
            }
        });

        if (!fail ) {
//console.log(jQuery("#form_buyerreg").serialize())        	
			response = '';
				jQuery("#agreement").html("<h3>Please wait a moment while we generate your Confidentiality Agreement...</h3>")
				jQuery.getJSON(
					'<?php echo plugin_dir_url().'/bbcrm/_auth.php'; ?>',
					{
						query:	'AJAX',
						action: 'buyerreg_ajax_getnda',
						data:jQuery("#form_buyerreg").serialize()						
    				}, 
    				function(response){  				
						jQuery("#agreement").empty().html(response.text);//.css({'height':'350px','overflow':'scroll'});
					//	jQuery("#agreement").prepend('<div id="pb" style="padding-top:30px;"><a class="printbutton" href="javascript:window.print()">Print Registration Form</a></div>');
					//	jQuery("#agreement").append('<div  id="pb" style="padding-top:30px;"><a class="printbutton" href="javascript:window.print()">Print Registration Form</a></div>');
    				}
				);
				jQuery("#form1,#form2").toggle();
			

        } else {}
});
			});
</script>


 <section style="margin-top: 36px !important;"  id="content" class="container"> 
      
  <div stlye="max-width:100%; id="business_container">
	
	    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="portfolio_group">
		
			<div class="col-md-3">
			
				<div class="panel-group" id="accordion">
					<div class="panel panel-default">
						<div class="panel-heading">
						  <h4 style="line-height: 40px;" class="panel-title">
							<a style="color:#333; font-weight:100; font-size: 24px; " class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
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
				
				<br />
				<div class="sidebar_search_by_id_container" >
                    <h3 class="panel-title">
                        Find by ID
                    </h3>
					<?php echo do_shortcode('[searchbyid addbutton=false]'); ?>	
				</div>					 
			</div>
			</div>	

		    <div class="col-md-9">
               <h1 style="text-align:left;"> <?php echo get_the_title( $ID ); ?></h1>
		      <?php the_content('<p class="serif">Read the rest of this page &raquo;</p>');	?>
		    </div>
		
         </div>
		
           <div  class="col-md-3 sidebar">
 
         <?php dynamic_sidebar( "content-sidebar" ); ?>
  
         </div>



		<?php endwhile; endif;?>
		
   </div>	
 </section>
<script>


jQuery(document).ready(function(){

jQuery("#agreement").scroll(function() {
//console.log (jQuery(this).scrollTop())
//console.log (jQuery(this).height())
//console.log (jQuery(this)[0].scrollHeight)

   if(Math.ceil(jQuery(this).scrollTop()) + jQuery(this).height()  >= jQuery(this)[0].scrollHeight-20 ) {
       jQuery("#prefinal").slideUp();
       jQuery("#regfinal").slideDown();
   }
});

})
</script>

	
 
<?php /*get_sidebar();*/
get_footer();
?>
