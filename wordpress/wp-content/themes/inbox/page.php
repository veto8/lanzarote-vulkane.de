<?php
/**
 * The template for displaying all single pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package inbox
 */

get_header(); 
?>
<div class="container-fluid" id="content">
	<div class="row">

    <!-- Post sidebar Starts -->
    <?php 
	do_action('inbox_social_link');
	get_sidebar(); 
	?>

    <!-- Post sidebar ends -->    
    
    <!-- Post Content Starts -->
    <div class="col col-12 col-lg-9 post-list order-2 order-lg-2 order-md-2 order-xl-3 post-content" data-simplebar id="single-home-container">    
    
			<?php while ( have_posts() ) : the_post(); //main loop ?>                        
				<div id="primary" class="content-area">
					<main id="main" class="site-main">


					<?php 
					get_template_part( 'template-parts/content', 'page' );                     
					                   
					if ( comments_open() || get_comments_number() ) :
                    comments_template();
                    endif; 
					?> 


					</main><!-- #main -->
				</div><!-- #primary -->
			<?php endwhile; // End of the loop. ?>  
    
    </div>
    <!-- Post Ends Starts -->    
    
    
    
    </div>
</div>

<?php  get_footer(); ?>