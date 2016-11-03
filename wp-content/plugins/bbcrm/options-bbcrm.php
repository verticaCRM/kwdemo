<?php
class options_page {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'init_fields') );
	
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
}

	function admin_menu() {
		add_menu_page(
			'BBCRM Settings',
			'BBCRM Settings',
			'manage_options',
			'bbcrm-options',
			array(
				$this,
				'settings_page'
			)
		);
		
}

function settings_page() {
	echo '<h2>'.__('BBCRM Settings','bbcrm').'</h2>';
?>
<script type="text/javascript">
jQuery(document).ready(function() {
 
jQuery('#upload_image_button').click(function() {
 formfield = jQuery('#upload_image').attr('name');
 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
 return false;
});
 
window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 jQuery('#upload_image').val(imgurl);
 tb_remove();
}
 
});
</script>
	<form action='options.php' method='post'>		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
	</form>
	<?php
}

public function init_fields(){
	register_setting( 'pluginPage', 'bbcrm_settings' );

	add_settings_section(
		'bbcrm_loginbar_section', 
		__( 'Login Bar Fields', 'bbcrm' ), 
		array($this,'bbcrm_settings_section_callback'), 
		'pluginPage'
	);

	add_settings_field( 
		'bbcrm_loginbar_phone', 
		__( 'Phone Number', 'bbcrm' ), 
		array($this,'bbcrm_text_render'), 
		'pluginPage', 
		'bbcrm_loginbar_section',
		array('option-name'=>'bbcrm_loginbar_phone')
	);

	add_settings_field( 
		'bbcrm_loginbar_fax', 
		__( 'Fax Number', 'bbcrm' ), 
		array($this,'bbcrm_text_render'), 
		'pluginPage', 
		'bbcrm_loginbar_section',
		array('option-name'=>'bbcrm_loginbar_fax')
	);
	
	add_settings_section(
		'bbcrm_pages_section',
		__('Default Pages','bbcrm'),
		array($this,'bbcrm_settings_section_callback'),
		'pluginPage'
	);
	add_settings_field( 
		'bbcrm_loginbar_contactus', 
		__( 'Contact Us Page', 'bbcrm' ), 
		array($this,'bbcrm_selectpage_render'), 
		'pluginPage', 
		'bbcrm_pages_section',
		array('option-name'=>'bbcrm_loginbar_contactus')
	);
	add_settings_field( 
		'bbcrm_pageselect_broker', 
		__( 'Broker Detail Page', 'bbcrm' ), 
		array($this,'bbcrm_selectpage_render'), 
		'pluginPage', 
		'bbcrm_pages_section',
		array('option-name'=>'bbcrm_pageselect_broker')
	);

	add_settings_field( 
		'bbcrm_loginbar_dataroom', 
		__( 'Data Room Page', 'bbcrm' ), 
		array($this,'bbcrm_selectpage_render'), 
		'pluginPage', 
		'bbcrm_pages_section',
		array('option-name'=>'bbcrm_loginbar_dataroom')
	);

	add_settings_section(
		'bbcrm_design_section', 
		__( 'Design Elements', 'bbcrm' ), 
		array($this,'bbcrm_settings_section_callback'), 
		'pluginPage'
	);
	
	add_settings_field( 
		'bbcrm_design_logo', 
		__( 'Logo', 'bbcrm' ), 
		array($this,'bbcrm_media_logo_render'), 
		'pluginPage', 
		'bbcrm_design_section',
		array('option-name'=>'bbcrm_design_logo')
	);

	add_settings_section(
		'bbcrm_crm_section', 
		__( 'CRM Fields', 'bbcrm' ), 
		array($this,'bbcrm_settings_section_callback'), 
		'pluginPage'
	);
	
	add_settings_field( 
		'bbcrm_crm_assignedTo', 
		__( 'Default Assigned To User', 'bbcrm' ), 
		array($this,'bbcrm_select_assignedTo_render'), 
		'pluginPage', 
		'bbcrm_crm_section',
		array('option-name'=>'bbcrm_crm_assignedTo')
	);

	}



function bbcrm_media_logo_render($args ) { 
	$options = get_option( 'bbcrm_settings' );
	?>
<input id="upload_image" type="text" size="36" name="bbcrm_settings[<?php echo $args['option-name'];?>]" value="<?php echo $options[$args['option-name']];?>" />
<input id="upload_image_button" type="button" value="Upload Image" />
<br />Enter an URL or upload an image for the banner.
	<?php

}
function bbcrm_text_render($args ) { 
	$options = get_option( 'bbcrm_settings' );
	?>
	<input type='text' name='bbcrm_settings[<?php echo $args['option-name'];?>]' value='<?php echo $options[$args['option-name']];?>'>
	<?php

}

function bbcrm_selectpage_render( $args ) {
	$options = get_option( 'bbcrm_settings' );
?>
	<?php wp_dropdown_pages( array('selected'=>$options[$args['option-name']],'name'=>"bbcrm_settings[".$args['option-name']."]",'echo'=>1) );?>

<?php
}

function bbcrm_select_assignedTo_render(  ) { 

$options = get_option( 'bbcrm_settings' );
$useroptions='';
$json = x2apicall((array('_class'=>'users')));
$userar = json_decode($json);
foreach($userar as $bbcrmuser){
$useroptions .="<option value='".$bbcrmuser->username."' ".selected($options['bbcrm_default_assignedTo'],$bbcrmuser->username,0).">".$bbcrmuser->firstName." ".$bbcrmuser->lastName."</option>";
}
	?>
	<select name='bbcrm_settings[bbcrm_default_assignedTo]'>
	<?php echo $useroptions;?></select>
<?php
}

function bbcrm_settings_section_callback($args ) { 
//	var_dump($args);
//	_e($args['callback'][2]);
//	 _e( 'This section description', 'bbcrm' );
}

}

new options_page;
?>
