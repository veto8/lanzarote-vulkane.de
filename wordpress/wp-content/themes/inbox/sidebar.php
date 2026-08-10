<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package inbox
 */

if ( ! is_active_sidebar( 'sidebar-main' ) ) {
 	return;
}
?>

    <div class="col col-12 col-lg-2 order-3 order-lg-2 col-xl-2 order-md-3 order-xl-2 sidebar" data-simplebar style="padding:0px;">
	    <div id="secondary" class="widget-area" role="complementary">	                    
			 <?php dynamic_sidebar( 'sidebar-main' ); ?>                                                            
       </div>    
       
    </div>
       