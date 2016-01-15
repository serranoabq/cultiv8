<?php
/**
 * Customizer options via Titan Framework
 */
  
add_action( 'tf_create_options', 'cultiv8_tf_customizer_options');
function cultiv8_tf_customizer_options() {
 
    // Initialize Titan with your theme name
    $titan = TitanFramework::getInstance( 'cultiv8' );
 
    // General options panel
    $cb_op_gen = $titan->createThemeCustomizerSection( array(
			'id'      => 'htf_op_gen',
			'name'    => 'General Options',
			'position'=> 30,
    ) );
		{
			// Site logo
			$cb_op_gen->createOption( array(
				'id'      => 'htf_sitelogo', 
				'type'    => 'upload', 
				'name'    => 'Site Logo',
				'desc'		=> 'Logo displayed in site header',
			) );
			
			// RSS logo
			$cb_op_gen->createOption( array(
				'id'      => 'htf_rsslogo', 
				'type'    => 'upload', 
				'name'    => 'RSS Feed Logo',
				'desc'    => 'Logo used in RSS feeds',
			) );
			
			// City
			$cb_op_gen->createOption( array(
				'id'      => 'htf_city', 
				'type'    => 'text', 
				'name'    => 'Church City',
				'default' => 'Albuquerque',
			) );
			
			// Title fonts (h1)
			$cb_op_gen->createOption( array(
				'id'                    => 'htf_titlefont', 
				'type'                  => 'font', 
				'name'                  => 'Title Font',
				'show_line_height'      => false,
				'show_letter_spacing'   => false,
				'show_font_variant'     => false,
				'show_text_shadow'      => false,
				'default' => array(
					'font-family' => 'Open Sans',
					'color'       => '#888888',
					'line-height' => '1em',
					'font-weight' => '700',
				),
				'css'                   => '.titlefont { value }'
			) );
			
			// Subtitle font (h2 and below)
			$cb_op_gen->createOption( array(
				'id'                    => 'htf_subtitlefont', 
				'type'                  => 'font', 
				'name'                  => 'Sub Title Font',
				'show_line_height'      => false,
				'show_letter_spacing'   => false,
				'show_font_variant'     => false,
				'show_text_shadow'      => false,
				'default' => array(
					'font-family' => 'Open Sans',
					'color'       => '#888888',
					'line-height' => '1em',
					'font-weight' => 'normal',
				),
				'css'                   => '.subtitlefont { value }'
			) );
			
			// Text font
			$cb_op_gen->createOption( array(
				'id'                    => 'htf_textfont', 
				'type'                  => 'font', 
				'name'                  => 'Text Font',
				'show_line_height'      => false,
				'show_letter_spacing'   => false,
				'show_font_variant'     => false,
				'show_text_shadow' => false,
				'default' => array(
					'font-family' => 'Open Sans',
					'color'       => '#888888',
					'line-height' => '1em',
					'font-weight' => 'normal',
				),
				'css'                   => '.titlefont { value }'
			) );
		}
		// **********
		
		// Home page section
		$cb_op_home = $titan->createThemeCustomizerSection( array(
			'id'      => 'htf_op_home',
			'name'    => 'General Display',
			'panel'   => 'Home Page Options',
			'position'=> 31,
    ) );
		{
			// Slider
			$cb_op_home->createOption( array(
				'id'      => 'htf_slider', 
				'type'    => 'text', 
				'name'    => 'Main Slider',
				'desc'    => 'Enter the shortcode to the slider (e.g., <code>[metaslider id="1"]</code>)',
				'default' => '',
			) );
			
			// The array below would be the child pages of the home page
			// Lay the groundwork
			$sermon = cultiv8_option( 'ctc-sermons' , __( 'Sermons', 'cultiv8' ) ) ;
			$sermon = explode( '/', $sermon );
			$sermons = ucfirst( array_shift( $sermon ) ); // plural
			$sermon = empty( $sermon ) ? $sermons : ucfirst( array_pop( $sermon ) ); // singular
			$sermon_default_title = 'Recent ' . $sermons; 
			
			$event = cultiv8_option( 'ctc-events' , __( 'Events', 'cultiv8' ) );
			$event = explode( '/', $event );
			$events = ucfirst( array_shift( $event ) );
			$event = empty( $event ) ? $events : ucfirst( array_pop( $event ) );
			$event_default_title = 'Upcoming ' . $events; 
			
			$person = cultiv8_option( 'ctc-people' , __( 'People', 'cultiv8' ) );
			$person = explode( '/', $person );
			$people = ucfirst( array_shift( $person ) );
			$person = empty( $person ) ? $people : ucfirst( array_pop( $person ) );
			$person_default_title = $people; 
			
			// Sections
			$cb_op_home->createOption( array(
				'id'      => 'htf_sections', 
				'type'    => 'sortable', 
				'name'    => 'Sections',
				'desc'    => 'Sort the sections to display. ',
				'options' => array(
					'slider' => 'Slider',
					'value1' => 'The first label',
					'value2' => 'The second label',
					'value3' => 'The third label',
					'value4' => 'The fourth label',
					'sermon' => $sermon_default_title,
					'event'  => $event_default_title,
					'person' => $person_default_title,
				),
			) );
		}
		
		// **********
		
		$cb_op_ctc = $titan->createThemeCustomizerSection( array(
			'id'      => 'htf_op_ctc',
			'name'    => 'Church Theme Content Sections',
      'panel'   => 'Home Page Options',
			'position'=> 31,
    ) );
		{
			// Sermon
			$cb_op_ctc->createOption( array(
				'type'    => 'heading', 
				'name'    => $sermons,
			) );
			{
				// Sermon title
				$cb_op_ctc->createOption( array(
					'id'      => 'htf_sermon', 
					'type'    => 'text', 
					'name'    => $sermon . ' Section Title',
					'default' => $sermon_default_title,
				) );
				// Sermon topic
				$terms = get_terms( 'ctc_sermon_topic', array( 'hide_empty' => 0 ) );
				$options[ 'all' ] = 'All';
				foreach ($terms as $option) {
					$options[ $option->slug ] = $option->name;
				}
				$topic = cultiv8_option( 'ctc-sermon-topic' , __( 'Topic', 'cultiv8' ) );
				$topic = explode( '/', $topic );
				$topics = ucfirst( array_pop( $topic ) );
				$topic = empty( $topic ) ? $topics : ucfirst( array_pop( $topic ) );
				$cb_op_ctc->createOption( array(
					'id'      => 'htf_sermon_topic', 
					'type'    => 'select', 
					'name'    => $topics,
					'desc'    => $topic . ' to display',
					'options' => $options,
				) );
				// Section style
				$cb_op_ctc->createOption( array(
					'id'   => 'htf_sermon_bgcolor', 
					'type' => 'color', 
					'name' => 'Background Color', 
					'default' => '#FFFFFF',
				) );			
				$cb_op_ctc->createOption( array(
					'id'   => 'htf_sermon_bgimage', 
					'type' => 'upload', 
					'name' => 'Background Image',
					'desc' => 'Section background image',
				) );
				$cb_op_ctc->createOption( array(
					'id'   => 'htf_sermon_bgtint', 
					'type' => 'checkbox', 
					'name' => 'Background Image Tint', 
					'desc' => 'Apply a darkening tint to the background image',
					'default' => true,
				) );
			}
			
			// Events
			$cb_op_ctc->createOption( array(
				'type'    => 'heading', 
				'name'    => $events,
			) );
			{
				// Event title
				$cb_op_ctc->createOption( array(
					'id'      => 'htf_event', 
					'type'    => 'text', 
					'name'    => $event . ' Section Title',
					'default' => $event_default_title,
				) );
				// Event count
				$cb_op_ctc->createOption( array(
					'id'      => 'htf_event_count', 
					'type'    => 'number', 
					'name'    => sprintf( __('Number of %s to display', 'cultiv8'), $events ),
					'default' => 4,
					'min'     => 1,
					'max'     => 5,
				) );				
				// Event category
				$terms = get_terms( 'ctc_event_category', array( 'hide_empty' => 0 ) );
				$options[ 'all' ] = 'All';
				foreach ($terms as $option) {
					$options[ $option->slug ] = $option->name;
				}
				$cb_op_ctc->createOption( array(
					'id'      => 'htf_event_category', 
					'type'    => 'select', 
					'name'    => $event . ' Category',
					'desc'    => $event . ' category to display',
					'options' => $options,
				) );				
				// Section style
				$cb_op_ctc->createOption( array(
					'id'   => 'htf_event_bgcolor', 
					'type' => 'color', 
					'name' => 'Background Color', 
					'default' => '#FFFFFF',
				) );			
				$cb_op_ctc->createOption( array(
					'id'   => 'htf_event_bgimage', 
					'type' => 'upload', 
					'name' => 'Background Image',
					'desc' => 'Section background image',
				) );
				$cb_op_ctc->createOption( array(
					'id'   => 'htf_event_bgtint', 
					'type' => 'checkbox', 
					'name' => 'Background Image Tint', 
					'desc' => 'Apply a darkening tint to the background image',
					'default' => true,
				) );
			}
				
			// People
			$cb_op_ctc->createOption( array(
				'type'    => 'heading', 
				'name'    => $people,
			) );
			{
				// People title
				$cb_op_ctc->createOption( array(
					'id'      => 'htf_person', 
					'type'    => 'text', 
					'name'    => $people . ' Section Title',
					'default' => $person_default_title,
				) );				
				// Section style
				$cb_op_ctc->createOption( array(
					'id'   => 'htf_people_bgcolor', 
					'type' => 'color', 
					'name' => 'Background Color', 
					'default' => '#FFFFFF',
				) );			
				$cb_op_ctc->createOption( array(
					'id'   => 'htf_people_bgimage', 
					'type' => 'upload', 
					'name' => 'Background Image',
					'desc' => 'Section background image',
				) );
				$cb_op_ctc->createOption( array(
					'id'   => 'htf_people_bgtint', 
					'type' => 'checkbox', 
					'name' => 'Background Image Tint', 
					'desc' => 'Apply a darkening tint to the background image',
					'default' => true,
				) );
			}
		}
		
		// **********
		// Social accounts
		$cb_op_social = $titan->createThemeCustomizerSection( array(
			'id'      => 'htf_social_acct',
			'name'    => 'Social Media Accounts',
      'desc'    => 'Enter the URL of your social media accounts',
			'position'=> 32,
    ) );
		{
			$cb_op_social->createOption( array(
				'id'      => 'htf_facebook', 
				'type'    => 'text', 
				'name'    => 'Facebook',
				'default' => '',
			) );
			$cb_op_social->createOption( array(
				'id'      => 'htf_twitter', 
				'type'    => 'text', 
				'name'    => 'Twitter',
				'default' => '',
			) );
			$cb_op_social->createOption( array(
				'id'      => 'htf_instagram', 
				'type'    => 'text', 
				'name'    => 'Instagram',
				'default' => '',
			) );
			$cb_op_social->createOption( array(
				'id'      => 'htf_Google', 
				'type'    => 'text', 
				'name'    => 'Google+',
				'default' => '',
			) );
			$cb_op_social->createOption( array(
				'id'      => 'htf_youtube', 
				'type'    => 'text', 
				'name'    => 'YouTube',
				'default' => '',
			) );
			$cb_op_social->createOption( array(
				'id'      => 'htf_vimeo', 
				'type'    => 'text', 
				'name'    => 'Vimeo',
				'default' => '',
			) );
		}
		// **********
		
		// Podcasting
		$cb_op_podcast = $titan->createThemeCustomizerSection( array(
			'id'      => 'htf_podcast',
			'name'    => 'Podcasting',
      'position'=> 33,
    ) );
		{
			// Description
			$cb_op_podcast->createOption( array(
				'id'      => 'htf_podcast_desc', 
				'type'    => 'textarea', 
				'name'    => 'Description',
				'default' => '',
			) );
			// Author
			$cb_op_podcast->createOption( array(
				'id'      => 'htf_podcast_author', 
				'type'    => 'text', 
				'name'    => 'Author',
				'default' => '',
			) );
			// Image
			$cb_op_podcast->createOption( array(
				'id'      => 'htf_podcast_logo', 
				'type'    => 'upload', 
				'name'    => 'Logo',
				'desc'    => 'Logo used in podcast feed. Needs to be 1400 x 1400 jpg or png.',
			) );
		}
		/*
	*/	
}