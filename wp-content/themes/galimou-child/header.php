<!doctype html>
<html lang="en">
  
<head>
  		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta charset="utf-8">
<?php
global $pagetitle,$bbcrm_option;
$pagetitle = (isset($pagetitle)&& !empty($pagetitle))?$pagetitle:get_the_title();
if( is_home() ){
$pagetitle = "Blog";
}
?>
		<title><?php echo $pagetitle." | ";?><?php echo bloginfo('site_name');?></title>
<?php 
global $bbcrm_option;
wp_head();?>
</head>

<body class='sec-1 pg-1'>            
	<header class="theme-bordertop1-dk" style="border-top-width:4px !important">
		<div class='container'>
            <div class="header_main">
               	<div class="subHeadPanel">
					<div class="mimicRow">
						<div id="logo" title="Our Business Sales"><img src="<?php echo $bbcrm_option["bbcrm_design_logo"]; ?>" style="max-width:100%;max-height:100%">
							<h1><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>
						</div>
						<nav id="topNav">
							<div class="topNavButton"><span class="glyphicon glyphicon-th-list"></span> Show Menu</div>
							<div class="topNavButtonClose noshow"><span class="glyphicon glyphicon-remove"></span> Close Menu</div>
							<?php
                            $defaults = array(
                            'container'=> false,
                            'theme_location' => 'primary-menu',
                            'menu_class' => 'listReset topnav');
                            
                            wp_nav_menu( $defaults );
                            
                            ?>
                            
                           </nav>
					</div>
				</div>
				

<?php 
if( is_home() || is_front_page() ) {
	layerslider(6);
}else{
//	echo '<div style="width:100%;overflow:hidden"><img src="/wp-content/uploads/2016/09/main-bg-hm4.jpg"></div>';
}

 ?>




                <div class="clearfix subNavRow theme-background1-dk">
					<div class="mimicRow">
						<div id="breadWrap">
							<ul id="breadCrumbs" style="display: block;">
<?php if(!is_front_page()):?>
								<li id="bc-1"><span><a href="<?php bloginfo('url');?>">Home</a></span></li>
								<li id="bc-1"><span> / </span></li>
								<li id="bc-1"><span id="bcs-1"><a href="<?php the_permalink();?>"></a><?php the_title(); ?></span></li>
<?php endif; ?>
							</ul>
						</div>		
		<div style="display:inline-block!important;float:right;vertical-align:top;padding-top:7px;">
							

<?php echo do_shortcode('[bbcrm_loginbar]'); ?> 
						</div>
					</div>
				</div>

			</div>
        </div>
    </header>
