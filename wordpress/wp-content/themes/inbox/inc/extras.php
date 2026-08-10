<?php
/**
 * Extra Theme Functions
 *
 * @package inbox
 */
 
// change color lumiance
// @link https://stackoverflow.com/questions/3512311/how-to-generate-lighter-darker-color-with-php
function inbox_adjustBrightness($hex, $steps) {
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        $color   = max(0,min(255,$color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}

/**
 * Calculate whether black or white is best for readability based upon the brightness of specified colour
 *
 * @param type $hex
 * @link https://www.binarymoon.co.uk/2015/04/simple-php-colour-manipulation-functions/
 */
function inbox_readabletext( $hex ) {

	$hex = str_replace( '#', '', $hex );
	if ( strlen( $hex ) == 3 ) {
		$hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1), 2 );
	}

	$color_parts = str_split( $hex, 2 );

	$brightness = ( hexdec( $color_parts[0] ) * 0.299 ) + ( hexdec( $color_parts[1] ) * 0.587 ) + ( hexdec( $color_parts[2] ) * 0.114 );

	if ( $brightness > 128 ) {
		return '#000';
	} else {
		return '#fff';
	}

} 
 
if ( ! function_exists( 'inbox_customize_colors' ) ) {
	function inbox_customize_colors() {
	
		$inbox_header_title_color = get_theme_mod('header_textcolor', '#ffffff');
		$inbox_accent_color = get_theme_mod('inbox_accent_color', '#061932');
		$inbox_secondary_color = get_theme_mod('inbox_secondary_color', '#F13446');		
?>

    <style type='text/css' media='all'> 		
		.site-header .site-branding p,
		.site-header .site-branding a, visited {
			color: #<?php echo esc_attr($inbox_header_title_color); ?>;		
		}
		.site-header {
			background-color: <?php echo esc_attr($inbox_accent_color); ?>;
		}
		.social-networks {
			background-color: <?php echo esc_attr($inbox_accent_color); ?>;		
		}
		.sidebar {
			background-color: <?php echo esc_attr( inbox_adjustBrightness($inbox_accent_color, '10')); ?>;
		}
		.widget-area .widget,
		.widget-area .widget .textwidget,
		.widget-area .widget p,		
		.widget-area .widget a, visited {
			color: <?php echo esc_attr( inbox_readabletext($inbox_accent_color) ); ?>;										
		}						
		.widget-area .widget .widget-title	{
			background-color: <?php echo esc_attr( inbox_adjustBrightness($inbox_accent_color, '30')); ?>;
		}
		.widget-area .widget_search .search-form input.search-field,
		.widget-area .widget select, 
		.widget-area .widget.widget_categories select, 
		.widget-area .widget.widget_archive select,
		.search-form input.search-field,
		.search-bar .search-form input.search-field {
			background-color: <?php echo esc_attr( inbox_adjustBrightness($inbox_accent_color, '30')); ?>;
		}
		.widget.widget_calendar table caption {
			background-color: <?php echo esc_attr( inbox_adjustBrightness($inbox_accent_color, '-50')); ?>;		
		}		
		.widget.widget_calendar table td#today,
		.widget.widget_calendar table th {
			background-color: <?php echo esc_attr( inbox_adjustBrightness($inbox_accent_color, '-80')); ?>;				
		}
		.widget.widget_calendar table td {
			color: <?php echo esc_attr( inbox_readabletext($inbox_accent_color) ); ?>;												
		}
		.widget.widget_calendar table td a {
			border-color: <?php echo esc_attr( inbox_adjustBrightness($inbox_accent_color, '-80')); ?>;						
		}
		.post-list .archive-title {
			background-color: <?php echo esc_attr( inbox_adjustBrightness($inbox_accent_color, '10')); ?>;		
		}
		
		.navigation.pagination .nav-links .current,
		.widget-area .widget_search .search-form input.search-submit,
		.search-form input.search-submit,
		.social-menu li a:hover,
		.post-content .entry-content .read-more .button,
		.search-bar .search-form input.search-submit {
			background-color: <?php echo esc_attr($inbox_secondary_color); ?>;				
		}
		 a,visited,
		 a:hover,
		 .loading-text,
		.entry-content p a,visited,
		.post-list .single-post-list .text-holder .post-category a, visited {
			color: <?php echo esc_attr($inbox_secondary_color); ?>;						
		}
		
	</style>		

<?php		
	}
}	
add_action( 'wp_head', 'inbox_customize_colors');


if ( ! function_exists( 'inbox_social_menu' ) ) {
	function inbox_social_menu() { 
?>
        <div class="col col-12 col-md-12 col-lg-12 col-xl-1 order-1 order-lg-1 order-md-1 order-xl-1 social-networks"  data-simplebar>        
                      <?php
                        wp_nav_menu( array(
                            'theme_location' 	=> 'social-menu',
                            'depth'				=>	0,
                            'menu_class'		=>	'social-menu',
                            'container'		 	=>	'ul',
                            'fallback_cb'		=>	false,
                        ) );
                      ?>                      
        </div>    
<?php
	}
add_action( 'inbox_social_link', 'inbox_social_menu' );	
}

function inbox_header_title() {
	/*
	 * If header text is set to display, let's bail.
	 * hattip:  https://themetry.com/custom-header-text-display-option/
	 */
	if ( display_header_text() ) {
		return;
	}
	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
		.site-title,
		.site-description {
			position: absolute;
			clip: rect(1px, 1px, 1px, 1px);
		}
	</style>
	<?php	
}

/**
 * Footer Credits
*/
if ( ! function_exists( 'inbox_footer_credit' ) ) :

function inbox_footer_credit(){

    $text  = '<div class="site-info">';
	$text .= '<p>';
    $text .=  esc_html__( 'Copyright &copy; ', 'inbox' ) . date_i18n( 'Y' );
    $text .= ' <a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>. ';
    $text .= '</p><p>';
    $text .= '<a href="' . esc_url( 'https://metricthemes.com/theme/inbox/' ) .'" rel="author" target="_blank">' . esc_html__( 'Inbox by MetricThemes', 'inbox' ) .'</a>. '; /* translators: %s: wordpress.org URL */ 
    $text .= sprintf( esc_html__( 'Powered by %s', 'inbox' ), '<a href="'. esc_url( __( 'https://wordpress.org/', 'inbox' ) ) .'" target="_blank">WordPress</a>.' );
    $text .= '</p></div>';

    echo apply_filters( 'inbox_footer_text', $text ); // WPCS: xss ok
}

add_action( 'inbox_footer', 'inbox_footer_credit' );

endif;