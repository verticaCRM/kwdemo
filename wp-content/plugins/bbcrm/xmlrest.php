<?php

/**

**/
include_once ("_auth.php");
ob_start();
global $business;

$json = x2apicall(array('_class'=>'Brokers/13.json'));
$defaultBroker = json_decode($json);

$json = x2apicall(array('_class'=>'users?emailAddress='.$defaultBroker->c_email));
$defaultUser = json_decode($json);

$myXMLFilePath = '/home/absbus/public_html/xml/';
$dir = new DirectoryIterator($myXMLFilePath);

$isCommercial = false;


foreach ($dir as $fileinfo) {     
	if(!$fileinfo->isDot()){ 
		$files[$fileinfo->getMTime()][] = $fileinfo->getFilename();
		  //$files[] = $fileinfo->getFilename();
	}
}


ksort($files);

foreach($files as $time=>$fileAr){

        $getfile = file_get_contents($myXMLFilePath.$fileAr[0]);
	echo "\n<h3 style='color:purple'>".$myXMLFilePath.$fileAr[0]."</h3>";

	$xmlobj = simplexml_load_string($getfile, 'SimpleXMLElement', LIBXML_NOCDATA);

	$buslistings = array();

	if(is_object($xmlobj->business) && !empty($xmlobj->business) && count($xmlobj->business)==1 ){
		$buslistings[0] = $xmlobj->business;
		echo "#count (single):";
	}
	if(is_object($xmlobj->business) && count($xmlobj->business)>1){
		foreach($xmlobj->business as $k=>$v){
			$buslistings[] = $v;
		}
		echo "#count (array):";
	}
	if(is_object($xmlobj->commercial) && !empty($xmlobj->commercial) ){
		foreach($xmlobj->commercial as $k=>$v){
			$buslistings[] = $v;
		}
		echo "#count (commercial):";
		$isCommercial = true;
	}

	echo count($buslistings);

	foreach($buslistings AS $idx=>$business){
	echo "\n<h4>Searching CRM for XML Unique ID:".$business->uniqueID."</h4>";
	$json = x2apicall(array('_class'=>'Clistings/by:c_uniqueID='.$business->uniqueID.';.json'));
	$listing = json_decode($json);

	if($listing->status =="404"){ //it does not exist; create.
		echo "<h4 style='color:blue'>New Listing</h4>";
		$data = setData($business);
		//print_r($data);
		$json = x2apipost( array('_class'=>'Clistings/','_data'=>$data ) );

	}else{ 
		echo "<h4 style='color:orange'>Exists in database</h4>";
		$data = setData($business);
		//print_r($data);
		$json = x2apipost( array('_method'=>'PUT','_class'=>'Clistings/'.$listing->id.'.json','_data'=>$data ) );
	}
print_r($json);
ob_flush();

	}//end business for loop

// When we're absolutely sure it's working properly...
//unlink($myXMLFilePath.$fileAr[0]);

}//end foreach $dir

function setData($businessobj,$id=''){
global $isCommercial;
$business = (array)$businessobj;

$json = x2apicall(array('_class'=>'users?emailAddress='.$businessobj->listingAgent->email->__toString() ) );
$user = json_decode($json);
$assignedTo = $user[0]->username;
$brokerName = $user[0]->fullName;
if(empty($assignedTo)){
	$assignedTo = $defaultUser[0]->username;
	$brokerName = $defaultUser[0]->fullName;
}

//Set the data array
$towncodeAr = array(
	'qld'=>"Queensland",
);

$salesStage = "Active";
$salesStage = ($business["underOffer"]["@attributes"]["value"]=="yes")?"LETTER OF INTENT":$salesStage;
$salesStage = ($business["@attributes"]["status"]=="withdrawn")?"Withdrawn":$salesStage;
$salesStage = ($business["@attributes"]["status"]=="sold")?"Sold":$salesStage;


$buscats = array();
$busname = ((string)$businessobj->busExtras->bus_name!='')?(string)$businessobj->busExtras->bus_name:"BUSINESS NAME REQUIRED";
$busdba = ((string)$businessobj->busExtras->bus_trading!='')?(string)$businessobj->busExtras->bus_trading:"";
foreach($business["businessCategory"] AS $idx=>$buscat){
	$buscats[] = '"'.(string)$buscat->businessSubCategory->name.'"';
}

$latitude = $businessobj->address->latitude->__toString();
$longitude = $businessobj->address->longitude->__toString();
$address = (string)$businessobj->address->streetNumber." ".(string)$businessobj->address->street;
$cltype = $businessobj->commercialListingType->attributes()->value->__toString();
$exclusive = ((string)$businessobj->exclusivity->attributes()->value == "exclusive")?"Exclusive Listing":"Open";
$data = array(
	'c_uniqueID'=>$businessobj->uniqueID->__toString(),
	'c_listing_frontend_id_c'=>$businessobj->officeID->__toString(),
	'name'=>$busname,
	'c_name_dba_c'=>$busdba,
	'assignedTo'=>$assignedTo,
	'c_assigned_user_id'=>$assignedTo,
	'c_currency_id'=>'$',
	'c_listing_askingprice_c'=>$businessobj->price->__toString(),
	'c_listing_exclusive_c'=>($business["exclusivity"]["@attributes"]["value"]=="exclusive")?"Exclusive Listing":"Open Listing",
	'c_listing_franchise_c'=>($business["franchise"]["@attributes"]["value"]=="no")?"No":"Yes",
	'description'=>$business["description"],
	'c_name_generic_c'=>$business["headline"],
	'c_listing_address_c'=>$address,
	'c_listing_latitude_c'=>$latitude,
	'c_listing_longitude_c'=>$longitude,
	'c_listing_city_c'=>$businessobj->address->suburb->__toString(),
	'c_listing_town_c'=>$towncodeAr[(string)$businessobj->address->state],
	'c_listing_postal_c'=>$businessobj->address->postcode->__toString(),
	'c_businesscategories'=>"[".join(",",$buscats)."]",
	'c_sales_stage'=>$salesStage,
	'c_date_modified'=>strtotime(substr_replace($business["@attributes"]["modTime"],' ',10,1)),
	'c_commercialListingType'=>ucfirst($cltype),
	'c_listing_exclusive_c'=>$exclusive,
	'c_priceView'=>$businessobj->priceView->__toString(),
	'c_ExpiryDateOfAppointment'=>'',
	'c_ListingPrice'=>$businessobj->price->__toString()
);

if($isCommercial){
	$data["c_isCommercial"] = '1';
}

return $data;

}

?>
