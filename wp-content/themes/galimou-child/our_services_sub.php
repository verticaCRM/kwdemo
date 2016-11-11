<?php
/*
Template Name: Our Services Sub
*/
?>
<?php get_header();?>
<?php echo do_shortcode('[listmenu menu="Sub Our Services" menu_id="sub_our_services" menu_class="au_submenu"]');?>

<div class='content'>
		<div class='container'>
		
		<div class="">

				<div class="col-md-3 sidebar_content">
					<?php get_sidebar('page'); ?>
				
		</div>

				<div  id="business_container" class="col-md-9 searchlists_container">
                       <h1 style="text-align:left; padding-top: 22px;"> <?php echo get_the_title( $ID ); ?></h1>
		
		
		
		
		
		
		

					 <?php if (have_posts()): while (have_posts()): the_post(); ?>
					 <p><?php the_content(); ?></p>
					 <?php endwhile; else : ?>
					 <p><?php _e( 'Sorry No Pages Found.');?></p>
					 <?php endif; ?>
									 
      </div>
</div>
 
			
					 
      </div>
</div>

<?php get_footer();?>