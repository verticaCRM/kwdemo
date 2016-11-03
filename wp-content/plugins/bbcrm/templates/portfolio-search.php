<div id="pagewidget" class="searchbox theme-color-border">
  
	<form method="post" action="/search/" class="font-montserrat" style="display:inline">
		<div class="row search_by_id_form">
			<div class="col-xs-6 col-sm-12 col-md-7 col-lg-7">
				<input name=id id="generic" class="id_search_input" placeholder="ID Search">
			</div>
			<div class="col-xs-5 col-sm-12 col-md-5 col-lg-4">
				<button onclick="javascript: clear_business_shearch_fields();" type="submit" class="btn btn-default id_search_submit">
					<span class="glyphicon glyphicon-search"></span><?php _e('Find','bbcrm');?>
				</button>
			</div>
		</div>
	<?php
	if($a['addbutton']===true){ 
	?>
	<input type=submit name="add_to_portfolio" value="Add" class="theme-background" style="margin-left:25px; padding:4px 8px;border-radius:8px;border:0;" />
	<?php } ?>
	</form>


</div>
<div style="height:10px;"></div>
