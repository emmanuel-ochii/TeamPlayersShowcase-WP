<?php
/**
 * Assets manager.
 *
 * @package TeamPlayersShowcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STP_Assets {

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_frontend_assets' ) );
	}

	/**
	 * Register frontend assets.
	 *
	 * @return void
	 */
	public static function register_frontend_assets() {
		wp_register_style(
			'stp-player-card',
			STP_PLUGIN_URL . 'assets/css/stp-players.css',
			array(),
			STP_PLUGIN_VERSION
		);
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public static function enqueue_frontend() {
		if ( ! wp_style_is( 'stp-player-card', 'registered' ) ) {
			self::register_frontend_assets();
		}

		wp_enqueue_style( 'stp-player-card' );
	}
}
