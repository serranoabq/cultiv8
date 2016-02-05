 <?php
/**
* Cultiv8 Theme Customizer
*
* @package Cultiv8
*/
function cultiv8_customize_register( $wp_customize ) {
	
	// Site Logo functionality if Jetpack is not installed
	if( ! function_exists( 'jetpack_the_site_logo' ) ) {
		$wp_customize->get_section('title_tagline')->title = __( 'Site Title, Tagline, and Logo', 'cultiv8' );

		$wp_customize->add_setting( 'cultiv8_site_logo', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'postMessage',
		) );
		
		$wp_customize->add_control( new WP_Customize_Image_Control(
			$wp_customize,
			'cultiv8_site_logo',
			array(
				'label'      => esc_html__( 'Site Logo', 'cultiv8' ),
				'section'    => 'title_tagline',
				'settings'   => 'cultiv8_site_logo',
				'description'=> sprintf( __( 'The Site Logo is displayed in the header. Uncheck <code>%s</code> to display only the logo. ', 'cultiv8' ), __( 'Display Header Text' ) ), // Translators: %s is the label for the Display Header Text option provided by Custom Headers 
			)
		) );
	}
	
	// Podcasting options
	cultiv8_customize_createSection( $wp_customize, array(
		'id' => 'podcast',
		'title' => _x( 'Podcasting', 'Customizer section title', 'cultiv8' ),
		'description' => _x( 'Settings for audio podcast', 'Customizer section description', 'cultiv8' ),
	) );
	cultiv8_customize_createSetting( $wp_customize, array(
		'id' => 'cultiv8_podcast_desc',
		'label' => _x( 'Podcast Description', 'Customizer setting', 'cutiv8' ),
		'type' => 'textarea',
		'default' => get_bloginfo( 'description' ),
		'section' => 'podcast',
	) );
	cultiv8_customize_createSetting( $wp_customize, array(
		'id' => 'cultiv8_podcast_author',
		'label' => _x( 'Podcast Author', 'Customizer setting', 'cultiv8' ),
		'type' => 'text',
		'default' => get_bloginfo( 'name' ),
		'section' => 'podcast',
	) );
	cultiv8_customize_createSetting( $wp_customize, array(
		'id' => 'cultiv8_podcast_logo',
		'label' => _x( 'Podcast Logo', 'Customizer setting', 'cultiv8' ),
		'type' => 'image',
		'default' => '',
		'section' => 'podcast',
		'description' => _x( 'Logo used in podcast feed. Must be 1400 x 1400 jpg or png.', 'Podcast logo option description', 'cultiv8' ),
	) );
	
	// RSS
	cultiv8_customize_createSection( $wp_customize, array(
		'id' => 'rss',
		'title' => _x( 'RSS Options', 'Customizer section title', 'cultiv8' ),
		'description' => _x( 'Settings for RSS feed', 'Customizer section description', 'cultiv8' ),
	) );
	cultiv8_customize_createSetting( $wp_customize, array(
		'id' => 'cultiv8_feed_logo',
		'label' => _x( 'RSS feed Logo', 'Customizer setting', 'cultiv8' ),
		'type' => 'image',
		'default' => '',
		'section' => 'rss',
		'description' => _x( 'Logo used in RSS feed. Sometimes a white + transparent logo does not show well in RSS readers. Use this to display a different logo than your site logo.', 'RSS feed logo option description', 'cultiv8' ),
	) );
	
	// Panel options
	for($i = 1; $i <= 8; $i++ ){
		cultiv8_customize_createSetting( $wp_customize, array(
			'id' => 'cultiv8_panel'. $i .'_hidetitle',
			'label' => _x( 'Hide Title', 'Customizer setting', 'cultiv8' ),
			'type' => 'checkbox',
			'default' => false,
			'section' => 'pique_panel' . $i,
			'transport' => 'postMessage',
			'description' => _x( 'Check to hide the title in this section', 'cultiv8' ),
		) );
		cultiv8_customize_createSetting( $wp_customize, array(
			'id' => 'cultiv8_panel' . $i. '_height',
			'label' => _x( 'Auto height', 'Customizer setting', 'cultiv8' ),
			'type' => 'checkbox',
			'default' => false,
			'section' => 'pique_panel' . $i,
			'transport' => 'postMessage',
			'description' => _x( 'Check to adjust panel height to content', 'cultiv8' ),
		) );
	}
}
add_action( 'customize_register', 'cultiv8_customize_register', 11 );
 


function cultiv8_customize_preview_js() {
	wp_enqueue_script( 'cultiv8_customizer', get_stylesheet_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), '20160111', true );
}
add_action( 'customize_preview_init', 'cultiv8_customize_preview_js' );

// Some Customizer shortcuts
function cultiv8_customize_createSetting( $wp_customize, $args ) {
	$default_args = array(
		'id' 	              => '', // required
		'type'              => 'text', // required. This refers to the control type. 
																	 // All settings are theme_mod and accessible via get_theme_mod.  
																	 // Other types include: 'number', 'checkbox', 'textarea', 'radio',
																	 // 'select', 'dropdown-pages', 'email', 'url', 'date', 'hidden',
																	 // 'image', 'color'
		'label'             => '', // required
		'default'           => '', // required
		'section'           => '', // required
		'sanitize_callback' => '', // optional
		'transport'         => '', // optional
		'description'       => '', // optional
		'priority'          => '', // optional
		'choices'           => '', // optional
		'panel'             => '', // optional
	);
	
	// Available types and arguments
	$available_types = array( 'text', 'number', 'checkbox', 'textarea', 'radio', 'select', 'dropdown-pages', 'email', 'url', 'date', 'hidden', 'image', 'color' );
	$setting_def_args = array( 'default'=> '', 'sanitize_callback'=>'', 'transport'=>'' );
	$control_def_args = array( 'type'=>'', 'label'=>'', 'description'=>'', 'priority'=>'', 'choices'=>'', 'section'=>'' );
	// Check for required inputs
	if( ! ( isset( $args[ 'id' ] ) AND 
					isset( $args[ 'default' ] ) AND 
					isset( $args[ 'section' ] ) AND 
					isset( $args[ 'type' ] ) ) )
		return;
	// Check for non-empty inputs, too
	if( empty( $args[ 'id' ] ) ||  
			empty( $args[ 'section' ] ) ||  
			empty( $args[ 'type' ] ) )
		return;
		
	// Check for a right type
	if( ! in_array( $args[ 'type' ], $available_types ) ) $args[ 'type' ] = 'text';
	
	$id = $args[ 'id' ];
	unset( $args[ 'id' ] );
	
	// Split setting arguments and control arguments
	$setting_args = array_intersect_key( $args, $setting_def_args );
	$control_args = array_intersect_key( $args, $control_def_args );
	
	$wp_customize->add_setting( $id, $setting_args );
	
	if( 'image' == $args[ 'type' ] ) {
		$wp_customize->add_control( new WP_Customize_Image_Control(
			$wp_customize,
			$id,
			array(
				'label'      => $args[ 'label' ] ? $args[ 'label' ] : '',
				'section'    => $args[ 'section' ],
				'settings'   => $id,
				'description'=> $args[ 'description' ] ? $args[ 'description' ] : ''
			)
		) );
	} elseif( 'color' == $args[ 'type' ] ) {
		$wp_customize->add_control( new WP_Customize_Image_Control(
			$wp_customize,
			$id,
			array(
				'label'      => $args[ 'label' ] ? $args[ 'label' ] : '',
				'section'    => $args[ 'section' ],
				'settings'   => $id,
				'description'=> $args[ 'description' ] ? $args[ 'description' ] : ''
			)
		) );
	} else {
		$wp_customize->add_control( $id, $control_args );
	}
}

function cultiv8_customize_createSection( $wp_customize, $args ) {
	$default_args = array(
		'id' 	            => '', // required
		'title'           => '', // required
		'priority'        => '', // optional
		'description'     => '', // optional
		'active_callback' => '', // optional
		'panel'           => '', // optional
	);
	
	// Check for required inputs
	if( ! ( isset( $args[ 'id' ] ) AND isset( $args[ 'title' ] ) ) ) return;
	if( empty( $args[ 'id' ] ) ||  empty( $args[ 'title' ] ) ) return;
	
	$id = $args[ 'id' ];
	unset( $args[ 'id' ] );
	$wp_customize->add_section( $id, $args );
}

function cultiv8_customize_createPanel( $wp_customize, $args ) {
	$default_args = array(
		'id'              => '', // required
		'title' 	        => '', // required
		'priority'        => '', // optional
		'description'     => '', // optional
		'active_callback' => '', // optional
	);
	
	if( '' == $args[ 'id' ] ||  '' == $args[ 'title' ] ) return;
	
	$id = $args[ 'id' ];
	unset( $args[ 'id' ] );
	$wp_customize->add_panel( $id, $args );
}
