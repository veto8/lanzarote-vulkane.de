<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package inbox
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<link rel="profile" href="http://gmpg.org/xfn/11">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>    
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="hfeed site">	
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'inbox' ); ?></a>  
<header id="masthead" class="site-header" role="banner">
<div class="container-fluid">
	<div class="row no-gutters">
	<div class="site-branding col-10 col-lg-2 col-md-10 col-xl-2">
		<?php 
			if ( function_exists( 'the_custom_logo' ) ) {
				the_custom_logo();
			}		
        ?>
        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
        <?php
            $description = get_bloginfo( 'description', 'display' );
            if ( $description || is_customize_preview() ) : ?>
            <p class="site-description"><?php echo esc_html( $description ); /* WPCS: xss ok. */ ?></p>
        <?php endif; ?>    
    </div>
	<div class="col-2 d-block d-xl-none d-lg-none">
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"><?php echo esc_html('&#9776;', 'inbox') ?></span>
          </button>           
    </div>    
	<div class="col-12 col-lg-6 col-md-6 col-xl-6">
        <nav class="navbar navbar-expand-lg">
                          <?php
                            wp_nav_menu( array(
                                'theme_location'  => 'primary-menu',
                                'depth'	          => 4, 
                                'container'       => 'div',
                                'container_class' => 'collapse navbar-collapse site-navigation',
                                'container_id'    => 'navbarSupportedContent',
                                'menu_class'      => 'navbar-nav mr-auto',
                                'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
                                'walker'          => new WP_Bootstrap_Navwalker(),
                            ) );														
                          ?>
        </nav>        
    </div>
    
	<div class="col-12 col-lg-4 col-md-12 col-xl-4 search-bar">
		<?php get_search_form(); ?>
    </div>        
</div>
</div>
</header>