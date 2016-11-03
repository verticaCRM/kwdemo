/*
Template Name: Blog Posts
*/

<php get_header(); ?>




<section id="" data="property" style="min-height:1500px;"> 
	<div class="portfolio_group">
		<div class="container-fluid search_result">
			<div class="row searchpage_main_content_row">

				<div class="col-md-3 sidebar_content">
				
				<div class="panel-group" id="accordion">
						  <div class="panel panel-default">
							<div class="panel-heading">
							  <h4 style="line-height: 40px;" class="panel-title">
								<a style="color:#333; font-weight:100; font-size: 24px; " class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
								  Business Search
								</a>
							  </h4>
							</div>
							<div id="collapseOne" class="panel-collapse collapse in">
							  <div class="panel-body">
								 <?php echo do_shortcode('[featuredsearch]');?>
							  </div>
							</div>
						  </div>
 
 
						</div>
						<br> 
				
		</div>

				<div  id="business_container" class="col-md-9 searchlists_container">
                       <h1 style="text-align:left; padding-top: 22px;"> <?php echo get_the_title( $ID ); ?></h1>
                        <?php the_content(); ?>
                        
                        
                        
                        
                        
                        
                        
	<div id="content">

        <?php query_posts('post_type=post&post_status=publish&posts_per_page=10&paged='. get_query_var('paged')); ?>

	<?php if( have_posts() ): ?>

        <?php while( have_posts() ): the_post(); ?>

	    <div id="post-<?php get_the_ID(); ?>" <?php post_class(); ?>>

        	<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( array(200,220) ); ?></a>

                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                <span class="meta"><?php author_profile_avatar_link(48); ?> <strong><?php the_time('F jS, Y'); ?></strong> / <strong><?php the_author_link(); ?></strong> / <span class="comments"><?php comments_popup_link(__('0 comments','example'),__('1 comment','example'),__('% comments','example')); ?></span></span>

		<?php the_excerpt(__('Continue reading »','example')); ?>

            </div><!-- /#post-<?php get_the_ID(); ?> -->

        <?php endwhile; ?>

		<div class="navigation">
			<span class="newer"><?php previous_posts_link(__('« Newer','example')) ?></span> <span class="older"><?php next_posts_link(__('Older »','example')) ?></span>
		</div><!-- /.navigation -->

	<?php else: ?>

		<div id="post-404" class="noposts">

		    <p><?php _e('None found.','example'); ?></p>

	    </div><!-- /#post-404 -->

	<?php endif; wp_reset_query(); ?>

	</div>

<!--	
<?php


global $wpdb;


$results_false_flag = 0;

foreach( $results as $result )
{
	if($result !== false) {
		$results_false_flag = 1;
	}
}


$html = '';


if(count((array)$results) > 0 && $results->status != "404"  &&  $results_false_flag){
	$listingids = array();

	foreach ($results as $searchlisting){

		if($searchlisting) {

			if(!in_array($searchlisting->id,$listingids)){
				$listingids[]= $searchlisting->id;

				if(!empty($searchlisting->c_businesscategories)){
				$categories = substr($searchlisting->c_businesscategories,1,-1);
				$categories = explode(',',str_replace('"', '', $categories));
				$cats = '';
				foreach($categories as $cat){
					$cats .='<a href="?find='.urlencode(stripslashes($cat)).'">'.stripslashes($cat).'</a> ';
				}
			}

			$images_results = $wpdb->get_results( 'SELECT gp.* FROM x2_gallery_photo gp RIGHT JOIN x2_gallery_to_model gm ON gm.galleryId = gp.gallery_id WHERE gm.modelName="Clistings" AND gm.modelId='.$searchlisting->id, OBJECT );

			$img_div = '';
			if( !empty($images_results[0]) && $images_results[0]->id > 0)
			{
				// echo '<pre>'; var_dump($images_results[0]); echo '</pre>';
				$img_div = "<div class='searchlisting_featured_image'><img src='/crm/uploads/gallery/_".$images_results[0]->id.".jpg' /></div>" ;
			}

			$html .= "<div class='listing_search_result searchresult'>";
			$html .= "	<div class='row'>";
			$html .= "		<div class='col-md-3 searchlisting_photo_box'>";		    
	        $html .= 			$img_div; 
	        $html .= "		</div>";
			$html .= "		<div class='col-md-9 searchlisting_content_box'>";
		//Marc - LINE BELOW USES A PLACEHOLDER:".$searchlisting->c_listing_id_c." Some functional code needs to go here in its place :) In addition to the only float on the page ( kind you used for the "more button" on home-featured php file )
        	$html .= "<div class='searchlisting_save_ca'>&nbsp;<span class='glyphicon glyphicon-ok-circle'></span>&nbsp;".__("Save/Request CA","bbcrm").$searchlisting->c_listing_id_c."</div>";		
		    $html .= "			<a class='searchlisting_name' href=\"/listing/". sanitize_title($searchlisting->c_name_generic_c) ."\" class=\"listing_link\" data-id=\"". $searchlisting->id ."\">".$searchlisting->c_name_generic_c."</a>";
		    $html .= "<br>";
		    $html .= "			<div class='searchlisting_region'>".__("","bbcrm").$searchlisting->c_listing_region_c."</div>";
		    $html .= "<div class='searchlisting_currency_id'>".__("",'bbcrm').$searchlisting->c_currency_id.number_format($searchlisting->c_listing_askingprice_c)."</div>";
			//$html .= "		<div>".__("Cash Flow: ",'bbcrm').$searchlisting->c_currency_id.number_format($searchlisting->c_ownerscashflow)."</div>";
		    $html .= "			<div class='searchlisting_description'>".$searchlisting->description."</div>";
			//$html .= "		<div>".__("Contact Seller",'bbcrm').$searchlisting->c_listing_businesscat_c."</div>";
			//$html .= "		<div>".__("More Info",'bbcrm').$searchlisting->c_listing_businesscat_c."</div>";
		    $html .= "			<div class='searchlisting_bottom_category'>".$cats."</div>";
		    
			//$html .= "		<div>".$searchlisting->c_listing_businesscat_c."</div>";
			
			 //Marc - LINE BELOW USES A PLACEHOLDER:".$searchlisting->c_listing_id_c." Some functional code needs to go here in its place :)
            $html .= "<div class='searchlisting_bottom_ref'>".__("Reference","bbcrm").$searchlisting->c_listing_id_c."</div>";
			$html .= "		</div>";
			
			
	        $html .= "	</div>";
	       
	        $html .= "</div>";
				
				
				

				
				
			if(is_user_logged_in() ){
				$html .= '<form action="/listing/'.sanitize_title($searchlisting->c_name_generic_c).'" method=post><input type=hidden name="action" value="add_to_portfolio" /><input type=hidden name="id" value="'. $searchlisting->id.'" /><input type=submit style="display:none; margin-bottom:18px;" value="'. __('Add to my portfolio','bbcrm').' &#10010;" class="portfolio_action_button portfolio-add"  /></form>';
				}
			}
		}
	}
}
else{
	$qy = (empty($qy))?"your search":'"'.$qy.'"';
	$html .= "<h2>No results were found for ".$qy."</h2>";
	$html .= "<p>Please check your spelling or try a search with different parameters.</p>";
	// $html .= do_shortcode('[featuredsearch]');
}


if(!empty($listingids)){
echo '<h2>'.get_the_title().'</h2>';
echo __("Your search ",'bbcrm');

if(is_array($qy)){
	echo join(",",$qy);
} else {
	echo $qy;
}
_e(" returned ",'bbcrm');
echo count((array)$listingids);
echo (count((array)$listingids)===1)?__(' result.','bbcrm'):__(' results.','bbcrm');
}


echo $html;

//get_template_part("home","search");
?>  

       
				</div>
			</div>      
		</div>
	</div>   -->
	
		 

		  
		  
		  
		  
		  
		  
<!-- /#content -->
	</section>

<?php get_footer(); ?>
