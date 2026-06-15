<?php
/**
 * Registers the `restaurant_tip` custom post type.
 *
 * Each submitted tip is stored as a draft so editors can review
 * it before it ever appears publicly.
 *
 * @package RestaurantTipSubmission
 */

defined( 'ABSPATH' ) || exit;

class RTS_CPT {

	public function __construct() {
		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'init', array( $this, 'register_meta' ) );
	}

	/**
	 * Register the CPT.
	 */
	public function register_cpt(): void {
		$labels = array(
			'name'               => _x( 'Restaurant Tips', 'post type general name', 'rts' ),
			'singular_name'      => _x( 'Restaurant Tip',  'post type singular name', 'rts' ),
			'menu_name'          => _x( 'Restaurant Tips', 'admin menu', 'rts' ),
			'add_new'            => __( 'Add New', 'rts' ),
			'add_new_item'       => __( 'Add New Tip', 'rts' ),
			'edit_item'          => __( 'Edit Tip', 'rts' ),
			'view_item'          => __( 'View Tip', 'rts' ),
			'all_items'          => __( 'All Tips', 'rts' ),
			'search_items'       => __( 'Search Tips', 'rts' ),
			'not_found'          => __( 'No tips found.', 'rts' ),
			'not_found_in_trash' => __( 'No tips in Trash.', 'rts' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => false,   // Tips are internal; editors review in WP Admin.
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,    // Gutenberg / REST access for editors.
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'custom-fields' ),
			'menu_icon'           => 'dashicons-food',
		);

		register_post_type( 'restaurant_tip', $args );
	}

	/**
	 * Register post meta fields so they are accessible via REST and properly sanitized.
	 */
	public function register_meta(): void {
		$shared = array(
			'object_subtype' => 'restaurant_tip',
			'single'         => true,
			'show_in_rest'   => true,
		);

		register_post_meta(
			'restaurant_tip',
			'_rts_submitter_name',
			array_merge( $shared, array(
				'type'              => 'string',
				'description'       => 'Name of the person who submitted the tip.',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => '__return_true',
			) )
		);

		register_post_meta(
			'restaurant_tip',
			'_rts_submitter_email',
			array_merge( $shared, array(
				'type'              => 'string',
				'description'       => 'E-mail address of the submitter.',
				'sanitize_callback' => 'sanitize_email',
				'auth_callback'     => '__return_true',
			) )
		);

		register_post_meta(
			'restaurant_tip',
			'_rts_restaurant_name',
			array_merge( $shared, array(
				'type'              => 'string',
				'description'       => 'Name of the restaurant being tipped.',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => '__return_true',
			) )
		);
	}
}
