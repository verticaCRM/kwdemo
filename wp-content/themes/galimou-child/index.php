<?php get_header();?>
<?php echo do_shortcode('[listmenu menu="Sub Home" menu_id="sub_home" menu_class="au_submenu"]');?>

<div class='content'>
		<div style="margin-top:36px;" class='container'>
		
		<div class="">

				<div class="col-12 col-sm-4 col-lg-3 sidebar_content" style=" margin-top: 45px;">
				
				<div class="panel-group" id="accordion">
						  <div class="panel panel-default">
							<div class="panel-heading">
							  <h4 style="line-height: 40px;" class="panel-title">
								<a style="color:#333; font-weight:100; font-size: 24px; " class="accordion-toggle in" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
								  Business Search
								</a>
							  </h4>
							</div>
							<div id="collapseOne" class="panel-collapse collapsed">
							  <div class="panel-body">
								 <?php echo do_shortcode('[featuredsearch]');?>
							  </div>
							</div>
						  </div>
                        
						<br> 
					  <div class="sidebar_search_by_id_container" >
						  <h3 class="panel-title">
							Find by ID
						  </h3>
						  <?php echo do_shortcode('[searchbyid addbutton=false]'); ?>	
					  </div>
						<br> 
						</div>  			
				
		</div>

				<div  id="business_container" class="col-12 col-sm-8 col-lg-9 searchlists_container">
                       <h1 style="text-align:left; padding-top: 22px;"> <?php echo wp_title( ); ?></h1>
	                 	<!-- <div style="margin:18px 0; color: #333;" ><strong> Published:</strong> <?php the_time('F jS, Y'); ?>  <strong>Author:</strong> <?php the_author_posts_link(); ?> <strong>Comments:</strong> <?php comments_template(); ?></div> -->
	             	<?php if (have_posts()): while (have_posts()): the_post(); ?>
<?php
echo get_the_post_thumbnail(get_the_ID(),array(252,252),array('align'=>'left','style'=>'padding-right:10px'));
?>
                                          <h2><a href="<?php echo the_permalink(); ?>"><?php echo the_title() ?></a></h2>
					<div style="margin:18px 0; color: #333;" ><strong> Published:</strong> <?php     the_time('F jS, Y'); ?>  <strong>Author:</strong> <?php the_author_posts_link(); ?></div>

                                        <div style="margin-bottom:36px;" > <p><?php the_excerpt(); ?></p>
					<hr style='height:1px;color:#ddd;width:95%' />
					</div>					 
					 <?php endwhile; else : ?>
					 <p><?php _e( 'Sorry No Pages Found.');?></p>
					 <?php endif; ?>
									 
      </div>
</div>
 
					 
      </div>
</div>

<?php get_footer();?>
