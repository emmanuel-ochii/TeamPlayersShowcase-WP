<?php
/**
 * Core plugin bootstrap.
 *
 * @package TeamPlayersShowcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class STP_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var STP_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return STP_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		STP_Post_Type::init();
		STP_Meta_Boxes::init();
		STP_Assets::init();
		STP_Shortcode::init();
		STP_Templates::init();
	}

	/**
	 * Load textdomain.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'team-players-showcase', false, dirname( plugin_basename( STP_PLUGIN_FILE ) ) . '/languages' );
	}

	/**
	 * Run on activation.
	 *
	 * @return void
	 */
	public static function activate() {
		STP_Post_Type::register();
		flush_rewrite_rules();
	}

	/**
	 * Run on deactivation.
	 *
	 * @return void
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
