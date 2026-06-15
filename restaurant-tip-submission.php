<?php

/**
 * Plugin Name: Restaurant Tip Submission
 * Plugin URI:  https://github.com/wpskilla/restaurant-tip-submission
 * Description:  A simple AJAX-powered form that lets visitors submit restaurant tips. Each submission is stored as a custom post type (restaurant_tip) in draft mode so editors can review it before publishing.
 * Version:      1.0.0
 * Requires at least: 6.0
 * Requires PHP:  8.0
 * Author:       Usama Shabir
 * License:      GPL-2.0-or-later
 * Text Domain:  rts
 */

defined('ABSPATH') || exit;

define('RTS_VERSION', '1.0.0');
define('RTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RTS_PLUGIN_URL', plugin_dir_url(__FILE__));

/*
 * Load plugin components
 * Each class is responsible for its own WordPress hooks.
 */
require_once RTS_PLUGIN_DIR . 'includes/class-rts-cpt.php';
require_once RTS_PLUGIN_DIR . 'includes/class-rts-form.php';
require_once RTS_PLUGIN_DIR . 'includes/class-rts-ajax.php';
require_once RTS_PLUGIN_DIR . 'includes/class-rts-assets.php';

/*
 * Initialize everything once WordPress is fully loaded
 */
add_action('plugins_loaded', function () {
    new RTS_CPT();
    new RTS_Form();
    new RTS_Ajax();
    new RTS_Assets();
});