
<footer style="display: inline-block;width: 100%;>
		<div class='container'>
            <div class="footer_main">
				<div id="footer-sidebar" class="secondary">
<div id="footer-sidebar1" class="col-xs-12 col-sm-6">
<?php
if(is_active_sidebar('footer-sidebar-1')){
dynamic_sidebar('footer-sidebar-1');
}
?>
<a href="https://cabb.org/brokers/342/Kourt-Williams"><img style="margin-right: 30px;" class="alignleft wp-image-1803" src="/wp-content/uploads/2016/07/cabb-300x286.jpg" alt="cabb" width="55" height="48" /></a>

<a href="http://mortgage.nationwidelicensingsystem.org/"><img style="margin-right: 30px;" class="wp-image-1806 alignleft" src="/wp-content/uploads/2016/07/nmls.jpg" alt="nmls" width="50" height="50" /></a>

<a href="https://kingdomadvisors.com/choose-a-professional/advisors/15084/90501"><img class="wp-image-1807 alignleft" style="margin-right: 30px;" src="/wp-content/uploads/2016/07/cka.png" alt="cka" width="116" height="32" /></a>

<a href="https://www.ethics.net/members/search/bg_checked=0/first_name=Kourt/last_name=Williams"><img style="margin-right: 30px;" class="wp-image-1805 alignleft" src="/wp-content/uploads/2016/07/nea.png" alt="nea" width="51" height="50" /></a>

<a href="https://www.ibba.org/broker-profile/ca/torrance/kourt-williams"><img class="wp-image-1804 alignleft" src="/wp-content/uploads/2016/07/ibba-300x67.jpg" alt="ibba" width="118" height="27" /></a>
</div>
<div id="footer-sidebar2" class="col-xs-12 col-sm-3">
<?php
if(is_active_sidebar('footer-sidebar-2')){
dynamic_sidebar('footer-sidebar-2');
}
?>
</div>
<div id="footer-sidebar3" class="col-xs-12 col-sm-3">
<?php
if(is_active_sidebar('footer-sidebar-3')){
dynamic_sidebar('footer-sidebar-3');
}
?>
</div>
</div>					</div>
				</div>
			<div class='container'>
				<div class="row footerBottom">
					<div class="col-12">
						<p class="align-centre">
							<small><em>Every precaution has been taken to establish accuracy of the above information but does not constitute any representation by the vendor or agent.  Some photos are used as a representation of the services or location of the listing, and are therefore not necessarily photos of that actual business.</em></small>
						</p>
					</div>
				</div>
				<div class="row copyright">
					<div class="col-12">
						<p class="pull-left">
							<small>Copyright © <?php echo date('Y'); ?> <?php echo get_bloginfo('site_name');?>. All rights reserved. Powered by <a href="http://businessbrokerscrm.com">Business Brokers CRM.</a></small>
						</p>
						<p class="pull-right">
							
						</p>
					</div>
				</div>
			</div>
				 <!-- Modal -->
				  <div class="modal fade" id="saveAlert" tabindex="-1" role="dialog" aria-labelledby="saveAlertLabel" aria-hidden="true">
				    <div class="modal-dialog">
				      <div class="modal-content">
				        <div class="modal-header">
				          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				          <h4 class="modal-title">Save Property</h4>
				        </div>
				        <div class="modal-body">
				          <p>This property has been added to your save list now.</p>
				        </div>
				        <div class="modal-footer">
				          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				        </div>
				      </div><!-- /.modal-content -->
				    </div><!-- /.modal-dialog -->
				  </div><!-- /.modal -->
  
				  <div class="modal fade" id="loginActive" tabindex="-1" role="dialog" aria-labelledby="loginActive" aria-hidden="true">
				    <div class="modal-dialog">
				      <div class="modal-content">
				        <div class="modal-header">
				          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				          <h4 class="modal-title">Login to members area</h4>
				        </div>
				        <div class="modal-body">

							<div class="registerForm">
								<form class="form-horizontal" role="form" data-validate="parsley" method="post" id="loginNow" name="loginNow">
							
									<div class="form-group">
										<label class="col-lg-2 control-label" for="inputUsername">Username</label>
										<div class="col-lg-8">
											<input class="form-control" id="inputUsername" name="inputUsername" type="text" placeholder="Username"  data-error-message="A username with a minimum 3 characters, and maximum of 30 characters is required, typically your firstname.lastname" data-rangelength="[3,30]" data-required="true" data-trigger="change focusin focusout" >
										</div>
									</div>
							
									<div class="form-group">
										<label class="col-lg-2 control-label" for="inputPassword">Password</label>
										<div class="col-lg-8">
											<input class="form-control" id="inputPassword" name="inputPassword" type="password" placeholder="Password" data-rangelength="[5,30]" data-error-message="Password of 5 characters or more is required" data-required="true" data-trigger="change focusin focusout" >
										</div>
									</div>
							
									<div class="form-group">
										<div class="col-lg-offset-2 col-lg-8" id="buttonHere"> </div>
									</div>
							
									<div class="alert alert-danger main-errorLogin noshow">
										<button type="button" class="close" data-dismiss="alert">
											&times;
										</button>
										<span class="alertContent">Fail</span>
									</div>
									<input type="hidden" name="addlisting" id="addlisting" />
									<input class="noshow" id="inputCheck" name="inputCheck" type="text" value="" />
								</form>
								<p>Don't have registration yet ? <a href="register.html" class="normalLink">"Create Account"</a></p>
								<p><small><em>If you have lost / forgotten your password click: <a href="lost-password.html" class="normalLink">"Lost Password"</a></em></small></p>
							</div>

				        </div>
				        <div class="modal-footer">
				          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				        </div>
				      </div><!-- /.modal-content -->
				    </div><!-- /.modal-dialog -->
				  </div><!-- /.modal -->
			</div>
        </div>
    </footer>
	
<?php wp_footer(); ?>
</body>
</html>
