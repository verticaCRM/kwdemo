<?php
$bbcrm_option = get_option( 'bbcrm_settings' );
//print_r($bbcrm_option);
?>
  <div class="top-bar">
      <ul class="left-bar-side">&nbsp;&nbsp;


		<li id="selectphone"><i class="fa  fa-phone"></i>&nbsp;<div id="ddpho" style='display:inline;color:#FFF !important;'><?php echo $bbcrm_option['bbcrm_loginbar_phone'];?></div><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span></li> 	
		<li id="selectphone"><i class="fa  fa-fax"></i>&nbsp;<div id="ddpho" style='display:inline;color:#FFF !important;'><?php echo $bbcrm_option['bbcrm_loginbar_fax'];?></div><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span></li> 	
		<li id="contactusli"><a href="<?php echo get_permalink($bbcrm_option['bbcrm_loginbar_contactus']);?>"><i class="fa fa-envelope"></i> <?php _e('Contact Us','bbcrm');?> </a><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span></li> 	
		<?php if(!is_user_logged_in() ): ?>
		<li id="loginli"><a href="#" class="loginlink"><i class="fa fa-lock"></i> <?php _e('Buyer Login','bbcrm');?> </a><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span></li>
<div id="logindiv"><?php 
$args = array(
	'form_id' => 'headerloginform',
	'remember' => false,
	 'value_remember' => false,
	 'redirect' => get_permalink($bbcrm_option['bbcrm_loginbar_dataroom']),
'label_username' => __( 'Username' , 'bbcrm' ),
	'label_password' => __( 'Password' , 'bbcrm' ),
	'label_remember' => __( 'Remember Me' , 'bbcrm' ),
	'label_log_in'   => __( 'Log In' , 'bbcrm'),
	);
wp_login_form($args); ?>
 </div>

<!--<li><a href="<?php echo wp_lostpassword_url(); ?>"><i class="fa  fa-question"></i> <?php _e('Forgot Password', 'bbcrm');?> </a><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span></li>-->
<li><a href="/registration/"><i class="fa  fa-plus-circle"></i> <?php _e('Member Registration','bbcrm');?> </a><!-- <span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>--></li>
	 	<?php else: ?>
	 	<li><a href="<?php echo get_permalink($bbcrm_option['bbcrm_loginbar_dataroom']);?>"><i class="fa fa-building-o"></i> <?php echo __('Your','bbcrm')." ".get_the_title($bbcrm_option['bbcrm_loginbar_dataroom']);?> </a><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span></li>
	 	<li><a href="/buyer-profile/"><i class="fa fa-user"></i> <?php _e('Your Profile','bbcrm');?> </a><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span></li>
	 	<li><a href="<?php echo wp_logout_url('/'); ?>"><i class="fa fa-arrow-circle-o-right"></i> <?php _e('Log Out','bbcrm');?> </a></li>	
		<?php endif; ?>		
      </ul> 
</div>
     <script>
     jQuery(document).ready(function(){
jQuery(".loginlink").click(function(){
	event.preventDefault();
	jQuery("#logindiv").css('display','inline');jQuery("#loginli").hide();})

});     
     </script>
