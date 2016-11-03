<?php
/*
Template Name: Left Sidebar 
*/
?>
<?php get_header();?>
<div class="container-fluid">
<div class="row">
<div class="col-md-3">

   <?php get_sidebar('page');?>

</div>

<div style="float:right;" class="col-md-9">
<?php if (have_posts()): while (have_posts()): the_post(); ?>
<p><?php the_content(); ?></p>
<?php endwhile; else : ?>
<p><?php _e( 'Sorry No Pages Found.');?></p>
<?php endif; ?>             
</div>                        

</div>
</div>
<?php get_footer(); ?>