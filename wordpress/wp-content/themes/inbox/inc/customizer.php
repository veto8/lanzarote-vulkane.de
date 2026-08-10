<?php
/**
 * Inbox Theme Customizer.
 *
 * @package inbox
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 */

function inbox_customize_register( $wp_customize ) {

    $wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	
			         
    /** Accent Color */
    $wp_customize->add_setting(
        'inbox_accent_color',
        array(
            'default' => '#061932',
			'sanitize_callback' => 'sanitize_hex_color',
        )
    );    
	
	$wp_customize->add_control( 
		new WP_Customize_Color_Control( 
		$wp_customize, 
		'inbox_accent_color', 
		array(
			'label'      => esc_attr__( 'Accent Color', 'inbox' ),
			'section'    => 'colors',
		) ) 
	);		
	
    /** Secondary Color */
    $wp_customize->add_setting(
        'inbox_secondary_color',
        array(
            'default' => '#F13446',
			'sanitize_callback' => 'sanitize_hex_color',
        )
    );    
	
	$wp_customize->add_control( 
		new WP_Customize_Color_Control( 
		$wp_customize, 
		'inbox_secondary_color', 
		array(
			'label'      => esc_attr__( 'Secondary Color', 'inbox' ),
			'section'    => 'colors',
		) ) 
	);		         
         
}
add_action( 'customize_register', 'inbox_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function inbox_customize_preview_js() {
	wp_enqueue_script( 'inbox-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), true );
}
add_action( 'customize_preview_init', 'inbox_customize_preview_js' );