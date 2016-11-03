<!doctype html>
<html lang="en">
  
<head>
  		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<title><?php wp_title('|',true,'right'); ?></title>
		<meta charset="utf-8">
<?php wp_head(); ?>
</head>

<body class='sec-1 pg-1'>            
	<header>
		<div class='container'>
            <div class="header_main">
               	<div class="subHeadPanel">
					<div class="mimicRow">
						<div id="logo" title="Our Business Sales">
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
					<div class="mainImagery">
					<div class="mimicRow">
						<h2>We sell businesses <small>That's what we do</small></h2>
					</div>
				</div>

                <div class="clearfix subNavRow">
					<div class="mimicRow">
						<div id="breadWrap">
							<ul id="breadCrumbs" style="display: block;">
								<li id="bc-1"><span><a href="<?php bloginfo('url');?>">Home</a></span></li>
								<li id="bc-1"><span id="bcs-1"><a href="<?php the_permalink();?>"><?php wp_title(); ?></a></span></li>
							</ul>
						</div>		
						<div class="membersPanel">
							<a href="register.html" rel="tooltip" data-placement="bottom">Register</a> | 
							<a href="member-login5bc4.html" id="topLogin" rel="tooltip">Login</a>
						</div>
					</div>
				</div>

			</div>
        </div>
    </header>
