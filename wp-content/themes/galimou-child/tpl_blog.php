<?php 
/*
Template Name: Blog Posts
*/
?>


<?php get_header();?>

<?php echo do_shortcode('[listmenu menu="Sub Blog" menu_id="sub_blog" menu_class="au_submenu"]');?>

<section id="" data="property"> 
	<div class="portfolio_group">
		<div class="container-fluid search_result">
			<div class="row searchpage_main_content_row">

			<div class="az col-12 col-sm-4 col-lg-3 sidebar_content" style=" margin-top: 45px;">
				
							   <div class="panel-group" id="accordion">
										 <div class="panel panel-default">
										   <div class="panel-heading">
											 <h4 style="line-height: 40px;" class="panel-title">
											   <a style="color:#333; font-weight:100; font-size: 24px; " class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
												 Business Search
											   </a>
											 </h4>
										   </div>
										   <div id="collapseOne" class="panel-collapse collapse">
											 <div class="panel-body">
												<?php echo do_shortcode('[featuredsearch]');?>
											 </div>
										   </div>
										   <br>
										   <div class="sidebar_search_by_id_container" >
												<h3 class="panel-title">
												Find by ID
											</h3>
				                            <?php echo do_shortcode('[searchbyid addbutton=false]'); ?>	
			                                </div>
                                          </div>
 
 
								</div>
									   <br> 
									   	
				
		</div>
		
		
		
		
		
			  <div class="col-12 col-sm-8 col-lg-9" style="float: none;">
		   <div id="content" >
		    <h1 style="text-align:left; padding-top: 22px;"> <?php echo get_the_title( $ID ); ?></h1>
                        <?php the_content(); ?>
										

									   <?php query_posts('post_type=post&post_status=publish&posts_per_page=10&paged='. get_query_var('paged')); ?>

								   <?php if( have_posts() ): ?>

									   <?php while( have_posts() ): the_post(); ?>
									   
									    <div style="border-bottom: 1px solid #ddd;padding-bottom:9px;" id="post-<?php get_the_ID(); ?>" <?php post_class(); ?>

													<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                                                       <h2><a style="color:#333 !important;" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                                   <div style="margin:18px 0; color: #333;" ><strong> Published:</strong> <?php the_time('F jS, Y'); ?>  <strong>Author:</strong> <?php the_author_posts_link(); ?> </div>
												  <?php the_excerpt(__('Continue reading »','example')); ?>
                                                    
                                                    <a href="<?php the_permalink(); ?>"><div class="blog-button">Read More</div></a>
                                                    
										   </div>

									   <?php endwhile; ?>

									   <div style="padding:36px 0;" class="navigation">
									   
									   <?php wpbeginner_numeric_posts_nav(); ?>

									 </div><!-- /.navigation -->




								   <?php else: ?>

									   <div id="post-404" class="noposts">

										   <p><?php _e('None found.','example'); ?></p>

	    </div><!-- /#post-404 -->

<?php endif; wp_reset_query(); ?>

	</div><!-- /#content -->
        
        
           </div><!-- /#content -->
         </div><!-- /col-md-9 -->
		

        <!-- /#content -->
     
				 	
</div></div>
</section> 
  <?php get_footer(); ?>