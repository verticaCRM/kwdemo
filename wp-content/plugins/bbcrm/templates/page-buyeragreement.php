<?php
//ini_set('display_errors','on');
//error_reporting(E_ALL);
/*
Template Name: Buyer Agreement
*/

if(!is_user_logged_in()){
	wp_redirect('/register/');
	exit;
}
//global $sugarsession_id, $url;
//include('_sugarconnect.php');

$userdata = get_userdata(get_current_user_id());

    $get_buyer_parameters = array(
         'session' => $sugarsession_id ,
         'module_name' => "Leads",
         'query' =>   "leads.id IN (SELECT bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (eabr.email_address_id = ea.id) WHERE bean_module = 'Leads' AND ea.email_address = '".trim($userdata->user_email)."' AND eabr.deleted=0)",
         'max_results' => '1',
         'deleted' => 0,
         'Favorites' => false,
);

//Get CRM Buyer Info
    $get_buyer_obj= call("get_entry_list", $get_buyer_parameters, $url);
    $buyer_obj = $get_buyer_obj->entry_list[0];
	$crmid = $buyer_obj ->id;
	$userstatus = $buyer_obj ->name_value_list->status->value;
	$userfname = $buyer_obj ->name_value_list->first_name->value;
	$userlname = $buyer_obj ->name_value_list->last_name->value;	
	$userbrokerid = $buyer_obj ->name_value_list->assigned_user_id->value;	

//print_r($buyer_obj ->name_value_list);
//Registered Pending
if(isset($_POST['action'])):
	//print_r($_POST);
	# POST id = LISTING ID (OPPORTUNITY)
	# POST uid = BUYER/USER ID (LEAD)
	if("" ==$_POST['action']){
	}
endif;

get_header();

   //retrieve records ----------------------------------------

?>
<section id="content">
	<div class="portfolio_group">
		<div id="business_container">
<h2><?php the_title();?></h2>
<br>
<?php

	echo "<div style='height:400px;overflow:auto;'>"; //this is so the page doesn't scroll endlessly.
//////////////////


if("Registered Pending" == $userstatus){
////////////////////
	 $get_agreement_parameters = array(
			'session' => $sugarsession_id,
			'module_name' => "Documents",
			'id' => "258428a3-d3e6-4989-2091-54abf20c20a4",
			'select_fields' => ''
		    );

    $agreement_obj = call("get_entry", $get_agreement_parameters , $url);
	$agreement_data= $agreement_obj->entry_list;		
	$agreementdata= $agreement_data[0]->name_value_list;

$ch = curl_init();
$source = "http://crm.mgcsdev.com/upload/".$agreementdata->document_revision_id->value;
curl_setopt($ch, CURLOPT_URL, $source);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$agreement = curl_exec($ch);
curl_close ($ch);

foreach($buyer_obj ->name_value_list AS $buyerparam){
	$agreement = str_replace("{".$buyerparam->name."}",$buyerparam->value,$agreement);
}

$agreement = str_replace("{date}",date('l, F j, Y',strtotime('now')),$agreement);
//echo $agreementdata->document_revision_id->value;
echo nl2br($agreement);

	} //if is active
else{
?>
you already agreed. Why are you here?
<?php
}
?>
</div>

</div>
<!-- #bc-->
<aside id="sidebar" class="<?php wpp_css('property::primary', "widget-area wpp_sidebar_{$post->property_type}"); ?>" role="complementary">
	<ul>
<?php 

	    $get_broker_parameters = array(
			'session' => $sugarsession_id,
			'module_name' => "Users",
			'id' => $userbrokerid,
//'select_fields'=>array(),
			'select_fields' => array('first_name','last_name','phone_home','phone_mobile','phone_work','phone_other','email1','broker_frontend_photo_url_c'),
    );

    $get_broker_result = call("get_entry", $get_broker_parameters , $url);
	$brokerdata = $get_broker_result ->entry_list;		
	$brokerdata = $brokerdata[0]->name_value_list;

//print_r($brokerdata);
	$brokerfirstname= $brokerdata ->first_name->value;
	$brokerlastname= $brokerdata ->last_name->value;
	$brokermobile = $brokerdata ->phone_mobile->value;
	$brokeroffice = $brokerdata ->phone_work->value;
	$brokeremail = $brokerdata ->email1->value;		
	$brokerimg = $brokerdata ->broker_frontend_photo_url_c->value;		
		echo "<h4>Your Broker: ".$brokerfirstname." ".$brokerlastname.'</h4>';
			echo '<img src="'.$brokerimg.'" width=160>';
		echo 	$brokermobile .'<br>';
		echo 	$brokeroffice .'<br>';
		echo 	$brokeremail.'<br>';
?>
		<?php dynamic_sidebar( "portfolio" ); ?>
	</ul>
</aside>

</div>
</section><!-- #primary .widget-area -->

<?php get_footer(); ?>
