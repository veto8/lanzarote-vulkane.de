<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
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
    
    <!-- Post List Starts -->
	<?php if( have_posts() ) : ?>    
    <div class="col col-12 col-lg-3 post-list order-2 order-lg-2 order-md-2 order-xl-3" data-simplebar>
    
		<?php 
		while ( have_posts() ) : the_post();		
			get_template_part( 'template-parts/content' ); 
		endwhile;
		
		the_posts_pagination();        		
		
		?>
    
    </div>
    <!-- Post List Ends -->    
    
    <!-- Post Content Starts -->
    <div class="col col-lg-6 col-xl-6 post-content order-4 order-lg-4 order-md-4 order-xl-4 d-none d-lg-block d-xl-block" id="single-home-container">
    </div>
    <!-- Post Content Ends -->      

	<?php
    else :
    get_template_part( 'template-parts/content', 'none' );
    endif; 
    ?>         
    
    
    </div>    
</div><!-- #content -->
<?php  get_footer(); ?>