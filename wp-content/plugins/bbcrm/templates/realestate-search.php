<h3 class="theme-background" style="display:none; color:#ccc;margin:0;margin-top:10px;width:40%"><?php _e($a['title'],'bbcrm');?></h3>

<div id="pagewidget-<?php echo str_replace(' ','_',$a['title']);?>" class="searchbox theme-color-border">

<?php
	// Get tab active class
	if( strpos($_SERVER['REQUEST_URI'], "/commercial-property/commercial-for-sale/") !== false )
	{
		$sale_act = 'active';
		$lease_act = '';
	}
	else {
		$sale_act = '';
		$lease_act = 'active';
	}


	// Get Real Estates Categories
	$json = x2apicall(array('_class'=>'dropdowns/1088.json'));
	$realestates_subctg = json_decode($json)->options;
?>



<ul class="sidebar_tabs">
	<li class="tab_container <?php echo $lease_act; ?>">
		<div toggle-id="for_lease">For Lease</div>
	</li>
	<li class="tab_container <?php echo $sale_act; ?>">
		<div toggle-id="for_sale">For Sale</div>
	</li>
</ul>


<div class="toggle_content <?php echo $lease_act; ?>" id="for_lease">

	<form method="get" action="/commercial-property/" id="searchform" class="sidebar_search_form">
		<input type="hidden" value="1" name="c_real_estate_lease_c">

		<div class="form_group">
			<label class="theme-color" for="generic"><?php _e('Keyword/s:','bbcrm');?></label><br>
			<input name="c_keyword_c" class="" type="search" id="generic">
		</div>
		<div class="sidebar_categories_base_container form_group">
			<label class="theme-color" for="c_real_estate_categories">
				<?php _e('Commercial Category','bbcrm');?>
			</label>
			<select multiple="multiple" name="c_real_estate_categories[]" id="commercial_category">
				<?php
				foreach( $realestates_subctg as $k=>$v )
				{
					echo '<option value="'.$v.'">'.$v.'</option>';
				}
				?>
			</select>
		</div>

		<div class="form_group">
			<label class="theme-color" for="c_minimum_rent_c"><?php _e("Minimum Rental (p/a):",'bbcrm')?></label><br>
			<select name="c_minimum_rent_c" id="minimum_rent">
				<option value="">ANY</option>
				<option value="10000">$10,000</option>
				<option value="25000">$25,000</option>
				<option value="50000">$50,000</option>
				<option value="75000">$75,000</option>
				<option value="100000">$100,000</option>
				<option value="150000">$150,000</option>
				<option value="200000">$200,000</option>
			</select>
		</div>
		<div class="form_group">
			<label class="theme-color" for="c_maximum_rent_c"><?php _e("Maximum Rental (p/a):",'bbcrm')?></label><br>
			<select name="c_maximum_rent_c" id="maximum_rent">
				<option value="">ANY</option>
				<option value="25000">$25,000</option>
				<option value="50000">$50,000</option>
				<option value="75000">$75,000</option>
				<option value="100000">$100,000</option>
				<option value="200000">$200,000</option>
				<option value="250000">$250,000</option>
				<option value="99999999999999999999">$250k plus</option>
			</select>
		</div>
		<div id="dd_state" class="dd_bystate form_group">
			<label id="regionlabel" class="theme-color" for="c_listing_region_c"><?php _e("Location/Region",'bbcrm')?></label><br />
			<select size="4" multiple="multiple" name="c_listing_region_c[]" id="listing_region" class="fs_select">
			<?php
			$json = x2apicall(array('_class'=>'dropdowns/1077.json'));
			$regions = json_decode($json);
			foreach ($regions->options as $k=>$v){
					echo "<option value='$v'>$k</option>";	
				}
			?>
			</select>
		</div>

		<?php

		//Get the brokers in the system
		$json = x2apicall(array('_class'=>'Brokers/'));
		$brokers =json_decode($json);

		if($brokers){
			$brokerselect = array();
			foreach ($brokers as $broker){
				$brokerselect[] = '"'.$broker->name.'":"'.$broker->nameId.'"';
			}
		}
		?>
		<script>
		brokerJSON = {<?php echo join(",",$brokerselect);?>};
		chooseABrokerTxt = "Anyone";
		authURI = "<?php echo plugin_dir_url(__FILE__).'../_auth.php'; ?>";
		pleaseWaitTxt = "<?php _e("Please wait a moment...",'bbcrm');?>";
		selectCountyTxt = "<?php _e("Please select a city",'bbcrm');?>";
		</script>

			<div id="sebu" class="form_group">
				<button type="submit" class="btn btn-default find_real_est_search_button">
					<span class="glyphicon glyphicon-search"></span><?php _e('Find Commercial','bbcrm');?>
				</button>
				<button onclick="javascript: clear_real_est_lease_search_fields();" type="button" class="btn btn-primary pull-right">
					<span class="glyphicon glyphicon-refresh"></span>
				</button>
			</div>
	</form>

</div>


<div class="toggle_content <?php echo $sale_act; ?>" id="for_sale">
	<form method="get" action="/commercial-property/commercial-for-sale/" id="searchform" class="sidebar_search_form">
		<input type="hidden" value="1" name="c_real_estate_sale_c">

		<div class="form_group">
			<label class="theme-color" for="generic"><?php _e('Keyword/s:','bbcrm');?></label><br>
			<input name="c_keyword_c" class="" type="search" id="generic">
		</div>
		<div class="sidebar_categories_base_container form_group">
			<label class="theme-color" for="c_real_estate_categories">
				<?php _e('Commercial Category','bbcrm');?>
			</label>
			<select multiple="multiple" name="c_real_estate_categories[]" id="commercial_category">
				<?php
				foreach( $realestates_subctg as $k=>$v )
				{
					// $option_value = 
					echo '<option value="'.$v.'">'.$v.'</option>';
				}
				?>
			</select>
		</div>

		<div class="form_group">
			<label class="theme-color" for="c_minimum_investment_c"><?php _e("Minimum Investment:",'bbcrm')?></label><br>
			<select name="c_minimum_investment_c" id="minimum_investment">
				<option value="">ANY</option>
				<option value="200000">$200,000</option>
				<option value="250000">$250,000</option>
				<option value="300000">$300,000</option>
				<option value="350000">$350,000</option>
				<option value="400000">$400,000</option>
				<option value="450000">$450,000</option>
				<option value="500000">$500,000</option>
				<option value="600000">$600,000</option>
				<option value="700000">$700,000</option>
				<option value="800000">$800,000</option>
				<option value="900000">$900,000</option>
				<option value="1000000">$1,000,000</option>
				<option value="1250000">$1,250,000</option>
				<option value="1500000">$1,500,000</option>
				<option value="1750000">$1,750,000</option>
				<option value="2000000">$2,000,000</option>
				<option value="2500000">$2,500,000</option>
				<option value="3000000">$3,000,000</option>
				<option value="4000000">$4,000,000</option>
				<option value="5000000">$5,000,000</option>
			</select>
		</div>
		<div class="form_group">
			<label class="theme-color" for="c_maximum_investment_c"><?php _e("Maximum Investment:",'bbcrm')?></label><br>
			<select name="c_maximum_investment_c" id="maximum_investment">
				<option value="">ANY</option>
				<option value="250000">$250,000</option>
				<option value="300000">$300,000</option>
				<option value="350000">$350,000</option>
				<option value="400000">$400,000</option>
				<option value="450000">$450,000</option>
				<option value="500000">$500,000</option>
				<option value="600000">$600,000</option>
				<option value="700000">$700,000</option>
				<option value="800000">$800,000</option>
				<option value="900000">$900,000</option>
				<option value="1000000">$1,000,000</option>
				<option value="1250000">$1,250,000</option>
				<option value="1500000">$1,500,000</option>
				<option value="1750000">$1,750,000</option>
				<option value="2000000">$2,000,000</option>
				<option value="2500000">$2,500,000</option>
				<option value="3000000">$3,000,000</option>
				<option value="4000000">$4,000,000</option>
				<option value="5000000">$5,000,000</option>
				<option value="99999999999999999999">$5mil plus</option>
			</select>
		</div>
		<div id="dd_state" class="dd_bystate form_group">
			<label id="regionlabel" class="theme-color" for="c_listing_region_c"><?php _e("Location/Region",'bbcrm')?></label><br />
			<select multiple="multiple" size="4" name="c_listing_region_c[]" id="listing_region" class="fs_select">
			<?php
			$json = x2apicall(array('_class'=>'dropdowns/1077.json'));
			$regions = json_decode($json);
			foreach ($regions->options as $k=>$v){
					echo "<option value='$v'>$k</option>";	
				}
			?>
			</select>
		</div>

		<?php

		//Get the brokers in the system
		$json = x2apicall(array('_class'=>'Brokers/'));
		$brokers =json_decode($json);

		if($brokers){
			$brokerselect = array();
			foreach ($brokers as $broker){
				$brokerselect[] = '"'.$broker->name.'":"'.$broker->nameId.'"';
			}
		}
		?>
		<script>
		brokerJSON = {<?php echo join(",",$brokerselect);?>};
		chooseABrokerTxt = "Anyone";
		authURI = "<?php echo plugin_dir_url(__FILE__).'../_auth.php'; ?>";
		pleaseWaitTxt = "<?php _e("Please wait a moment...",'bbcrm');?>";
		selectCountyTxt = "<?php _e("Please select a city",'bbcrm');?>";
		</script>

			<div id="sebu" class="form_group">
				<button type="submit" class="btn btn-default find_real_est_search_button">
					<span class="glyphicon glyphicon-search"></span><?php _e('Find Commercial','bbcrm');?>
				</button>
				<button onclick="javascript: clear_real_est_sale_search_fields();" type="button" class="btn btn-primary pull-right">
					<span class="glyphicon glyphicon-refresh"></span>
				</button>
			</div>
	</form>

</div>

</div>
