<?php
/**
 * The template for displaying search results
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package inbox
 */

get_header(); 
?>
<div class="container-fluid">
	<div class="row">

    <!-- Post sidebar Starts -->
    <?php 
	do_action('inbox_social_link');
	get_sidebar(); 
	?>
    <!-- Post sidebar ends -->    
    
    <!-- Post List Starts -->
	<?php if( have_posts() ) : ?>
    <div class="col col-12 col-lg-3 post-list order-2 order-lg-3 order-md-3 order-xl-3" data-simplebar>
    
		<div class="search-heading">
		<?php /* translators: %s: search term */ ?>
		<h1 class="search-title"><?php printf( esc_html( 'Search Results for: %s', 'inbox' ), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?></h1>                    
		</div>                        
			<?php 
            while ( have_posts() ) : the_post();		
                get_template_part( 'template-parts/content' ); 
            endwhile;
			
			the_posts_pagination();     
			
            ?>    
    </div>
    <!-- Post List Ends -->    
    
    <!-- Post Content Starts -->
    <div class="col col-lg-6 post-content order-4 order-lg-4 order-md-4 order-xl-4 d-none d-lg-block d-md-block d-xl-block" id="single-home-container">
    <!-- content fetched from single post/page is shown here. -->
    </div>
    <!-- Post Content Ends -->      
    
		<?php
        else :
        get_template_part( 'template-parts/content', 'none' );
        endif; 
        ?>            
    
    
    </div>    
</div>

<?php  get_footer(); ?>