<?php
/**
 * Metabox options via Titan Framework.
 */
 
add_action( 'admin_enqueue_scripts', 'cultiv8_tf_metabox_scripts' );
add_action( 'tf_create_options', 'cultiv8_tf_metabox_options' );
 
function cultiv8_tf_metabox_options() {
	
    // Initialize Titan with your theme name.
    $titan = TitanFramework::getInstance( 'cultiv8' );
 
		// Create the metaboxes
    $mb_op = $titan->createContainer( array(
			'type'      => 'meta-box-extra',  // custom metabox
			'name'      => 'Section styles',  
			'post_type' => array( 'page' ),   
			'desc'			=> 'These options only apply to pages with the <code>Section</code> template.',
			'template'  => '',//'template-front-section.php',
    ) );
		
		
		$mb_op->createOption( array(
			'id'   => 'htf_bgcolor', 
			'type' => 'color', 
			'name' => 'Background Color', 
			'desc' => 'Section background color',
			'default' => '#FFFFFF',
		) );
		
		$mb_op->createOption( array(
			'id'   => 'htf_bgimage', 
			'type' => 'upload', 
			'name' => 'Background Image', 
			'desc' => 'Section background image',
		) );
		
		$mb_op->createOption( array(
			'id'   => 'htf_bgtint', 
			'type' => 'checkbox', 
			'name' => 'Background Image Tint', 
			'desc' => 'Apply a darkening tint to the background image',
			'default' => true,
		) );
		
 /* */
}

function cultiv8_tf_metabox_scripts( $hook ){
	if( $hook != 'edit.php' && $hook != 'post.php' && $hook != 'post-new.php' )  return;
	
	wp_enqueue_script( 'custom_metabox',  get_stylesheet_directory_uri() . '/admin/titan-fw/metabox-options.js' );
}

// Extend the MetaBox class with some custom options

class TitanFrameworkMetaBoxExtra extends TitanFrameworkMetaBox {
	private $defaultSettings = array(
		'name'               => '', 
		'id'                 => '', 
		'post_type'          => 'page', 
		'template'           => '', // specific template to apply metabox to
		'post_id'            => '', // specific post-ID to apply the metabox to
		'context'            => 'normal', 
		'hide_custom_fields' => true, 
		'priority'           => 'high', 
		'desc'               => '', 
	);
	public $settings;
	public $options = array();
	public $owner;
	public $postID; // Temporary holder for the current post ID being edited in the admin
	function __construct( $settings, $owner ) {
		$this->owner = $owner;
		if ( ! is_admin() ) {
			return;
		}
		$this->settings = array_merge( $this->defaultSettings, $settings );
		// $this->options = $options;
		if ( empty( $this->settings['name'] ) ) {
			$this->settings['name'] = __( 'More Options', TF_I18NDOMAIN );
		}
		if ( empty( $this->settings['id'] ) ) {
			$this->settings['id'] = str_replace( ' ', '-', trim( strtolower( $this->settings['name'] ) ) );
		}
		add_action( 'add_meta_boxes', array( $this, 'register' ) );
		add_action( 'save_post', array( $this, 'saveOptions' ), 10, 2 );
		// The action save_post isn't performed for attachments. edit_attachments
		// is a specific action only for attachments.
		add_action( 'edit_attachment', array( $this, 'saveOptions' ) );
	}
	
	public function register() {
		$postTypes = array();
		$post = get_post();
		
		// Skip if the current post is NOT in the post_ID list 
		if ( ! empty( $this->settings['post_ID'] ) ) {
			if ( is_array( $this->settings['post_ID'] ) ) {
				$postids = $this->settings['post_ID'];
			} else {
				$postids[] = $this->settings['post_ID'];
			}
			if ( ! in_array( $post -> ID, $postids ) ) return;
		}
		
		// accomodate multiple post types
		if ( is_array( $this->settings['post_type'] ) ) {
			$postTypes = $this->settings['post_type'];
		} else {
			$postTypes[] = $this->settings['post_type'];
		}
		
		
		foreach ( $postTypes as $postType ) {
			// Hide the custom fields
			if ( $this->settings['hide_custom_fields'] ) {
				remove_meta_box( 'postcustom' , $postType , 'normal' );
			}
			add_meta_box(
				$this->settings['id'],
				$this->settings['name'],
				array( $this, 'display' ),
				$postType,
				$this->settings['context'],
				$this->settings['priority']
			);
		}
	}
	
	public function display( $post ) {
		$this->postID = $post->ID;
		$templates = array();
		if ( ! empty( $this->settings['template'] ) ) {
			if ( is_array( $this->settings['template'] ) ) {
				$templates = $this->settings['template'];
			} else {
				$templates[] = $this->settings['template'];
			}
		}
		
		wp_nonce_field( $this->settings['id'], TF . '_' . $this->settings['id'] . '_nonce' );
		if ( ! empty( $this->settings['desc'] ) ) {
			?><p class='description'><?php echo $this->settings['desc'] ?></p><?php
		}
		?>
		<table class="form-table tf-form-table" data-template="<?php echo implode(',' , $templates); ?>">
		<tbody>
		<?php
		foreach ( $this->options as $option ) {
			$option->display();
		}
		?>
		</tbody>
		</table>
		
		<?php
	}
	
	private function verifySecurity( $postID, $post = null ) {
		// Verify edit submission
		if ( empty( $_POST ) ) {
			return false;
		}
		
		if ( empty( $_POST['post_type'] ) ) {
			return false;
		}
		
		// Don't save on revisions
		if ( wp_is_post_revision( $postID ) ) {
			return false;
		}
		
		// Don't save on autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}
		
		// Verify that we are editing the correct post type
		if ( is_array( $this->settings['post_type'] ) ) {
			if ( ! in_array( $_POST['post_type'], $this->settings['post_type'] ) ) {
				return false;
			}
			if ( null !== $post && ! in_array( $post->post_type, $this->settings['post_type'] ) ) {
				return false;
			}
		} else {
			if ( $_POST['post_type'] != $this->settings['post_type'] ) {
				return false;
			}
			if ( null !== $post && $post->post_type != $this->settings['post_type'] ) {
				return false;
			}
		}
		
		// Verify that we are editing the right post by id
		if ( is_array( $this->settings['post_id'] ) ) {
			if ( ! in_array( $_POST['post_ID'], $this->settings['post_ID'] ) ) {
				return false;
			}
			if ( null !== $post && ! in_array( $post->ID, $this->settings['post_id'] ) ) {
				return false;
			}
		} else {
			if ( $_POST['post_ID'] != $this->settings['post_id'] ) {
				return false;
			}
			if ( null !== $post && $post->ID != $this->settings['post_id'] ) {
				return false;
			}
		}
		
		// Verify that we are editing the right template
		$cur_template = get_page_template_slug( $post->ID );
		if ( is_array( $this->settings['template'] ) ) {
			if ( ! in_array( $_POST['page_template'], $this->settings['template'] ) ) {
				return false;
			}
			if ( null !== $post && ! in_array( $cur_template, $this->settings['template'] ) ) {
				return false;
			}
		} else {
			if ( $_POST['page_template'] != $this->settings['template'] ) {
				return false;
			}
			if ( null !== $post && $cur_template != $this->settings['template'] ) {
				return false;
			}
		}
		
		// Verify our nonce
		if ( ! check_admin_referer( $this->settings['id'], TF . '_' . $this->settings['id'] . '_nonce' ) ) {
			return false;
		}
		// Check permissions
		if ( is_array( $this->settings['post_type'] ) ) {
			if ( in_array( 'page', $this->settings['post_type'] ) ) {
				if ( ! current_user_can( 'edit_page', $postID ) ) {
					return false;
				}
			} else if ( ! current_user_can( 'edit_post', $postID ) ) {
				return false;
			}
		} else {
			if ( $this->settings['post_type'] == 'page' ) {
				if ( ! current_user_can( 'edit_page', $postID ) ) {
					return false;
				}
			} else if ( ! current_user_can( 'edit_post', $postID ) ) {
				return false;
			}
		}
		return true;
	}
	
	public function createOption( $settings ) {
		if ( ! apply_filters( 'tf_create_option_continue_' . $this->owner->optionNamespace, true, $settings ) ) {
			return null;
		}
		$obj = TitanFrameworkOption::factory( $settings, $this );
		$this->options[] = $obj;
		do_action( 'tf_create_option_' . $this->owner->optionNamespace, $obj );
		return $obj;
	}
	/**/
}