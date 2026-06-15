<?php
/**
 * Enqueues the plugin's front-end CSS and JS.
 *
 * The AJAX URL and nonce are passed to JS via wp_localize_script() so they
 * are never hardcoded in the template or the JS file itself.
 *
 * @package RestaurantTipSubmission
 */

defined( 'ABSPATH' ) || exit;

class RTS_Assets {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	public function enqueue(): void {
		/* CSS ----------------------------------------------------------- */
		wp_enqueue_style(
			'rts-styles',
			RTS_PLUGIN_URL . 'assets/css/rts-form.css',
			array(),
			RTS_VERSION
		);

		/* JS ------------------------------------------------------------ */
		wp_enqueue_script(
			'rts-form',
			RTS_PLUGIN_URL . 'assets/js/rts-form.js',
			array(),           // No jQuery dependency; we use the native fetch API.
			RTS_VERSION,
			array(
				'strategy'  => 'defer',  // Non-blocking; script runs after HTML parse.
				'in_footer' => true,
			)
		);

		/*
		 * Pass dynamic values to JS via wp_localize_script().
		 *
		 * WHY wp_localize_script AND NOT INLINE IN THE TEMPLATE?
		 * -------------------------------------------------------
		 * Hardcoding admin_url('admin-ajax.php') or the nonce directly into
		 * the template would:
		 *  – Couple the PHP template to the JS implementation.
		 *  – Make it impossible to use the form via a shortcode in different
		 *    page contexts without manually passing variables each time.
		 *  – Risk leaking the nonce in HTML source comments / page caches.
		 *
		 * wp_localize_script() attaches a <script> block that is output right
		 * before the enqueued script tag, so JS always has the correct values
		 * for the current page load.
		 *
		 * The nonce printed HERE in rtsData is for the JS fetch() call.
		 * The nonce inside the form <input> (printed by RTS_Form) provides a
		 * second layer: the server verifies whichever one is posted.
		 */
		wp_localize_script(
			'rts-form',
			'rtsData',    // Global JS object name.
			array(
				'ajaxUrl' => esc_url( admin_url( 'admin-ajax.php' ) ),
				'nonce'   => wp_create_nonce( 'rts_submit_tip' ),
				'action'  => RTS_Ajax::ACTION,
				'i18n'    => array(
					'sending'      => __( 'Sending…', 'rts' ),
					'send'         => __( 'Send Tip', 'rts' ),
					'networkError' => __( 'Network error. Please check your connection and try again.', 'rts' ),
				),
			)
		);
	}
}
