<?php
/**
 * Plugin Name: Team Players Showcase
 * Plugin URI: https://studiox.ng/
 * Description: Manage and display screenshot-style player cards (single or multiple).
 * Version: 1.0.3
 * Author: StudioX
 * Text Domain: team-players-showcase
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STP_PLUGIN_VERSION', '1.0.3' );
define( 'STP_PLUGIN_FILE', __FILE__ );
define( 'STP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'STP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once STP_PLUGIN_DIR . 'includes/class-stp-plugin.php';
require_once STP_PLUGIN_DIR . 'includes/class-stp-post-type.php';
require_once STP_PLUGIN_DIR . 'includes/class-stp-meta-boxes.php';
require_once STP_PLUGIN_DIR . 'includes/class-stp-assets.php';
require_once STP_PLUGIN_DIR . 'includes/class-stp-shortcode.php';
require_once STP_PLUGIN_DIR . 'includes/class-stp-templates.php';
require_once STP_PLUGIN_DIR . 'includes/class-stp-settings.php';

register_activation_hook( __FILE__, array( 'STP_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'STP_Plugin', 'deactivate' ) );

STP_Plugin::instance();
