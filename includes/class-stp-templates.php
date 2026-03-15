<?php
/**
 * Template loader for single player pages.
 *
 * @package TeamPlayersShowcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STP_Templates {

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'template_include', array( __CLASS__, 'template_include' ) );
	}

	/**
	 * Supply plugin single template when theme does not provide one.
	 *
	 * @param string $template Template path.
	 * @return string
	 */
	public static function template_include( $template ) {
		if ( is_admin() || is_embed() || ! is_singular( STP_Post_Type::POST_TYPE ) ) {
			return $template;
		}

		$theme_template = locate_template( array( 'single-' . STP_Post_Type::POST_TYPE . '.php' ) );
		if ( $theme_template ) {
			return $theme_template;
		}

		$plugin_template = STP_PLUGIN_DIR . 'templates/single-' . STP_Post_Type::POST_TYPE . '.php';
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}

		return $template;
	}
}
