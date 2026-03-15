<?php
/**
 * Plugin settings.
 *
 * @package TeamPlayersShowcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STP_Settings {

	/**
	 * Option name for custom CSS.
	 */
	const OPTION_CUSTOM_CSS = 'stp_custom_css';

	/**
	 * Settings group.
	 */
	const SETTINGS_GROUP = 'stp_settings_group';

	/**
	 * Settings page slug.
	 */
	const SETTINGS_PAGE = 'stp-showcase-settings';

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_admin_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Register settings page under Players menu.
	 *
	 * @return void
	 */
	public static function register_admin_page() {
		add_submenu_page(
			'edit.php?post_type=' . STP_Post_Type::POST_TYPE,
			__( 'Showcase Settings', 'team-players-showcase' ),
			__( 'Showcase Settings', 'team-players-showcase' ),
			'manage_options',
			self::SETTINGS_PAGE,
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public static function register_settings() {
		register_setting(
			self::SETTINGS_GROUP,
			self::OPTION_CUSTOM_CSS,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( __CLASS__, 'sanitize_css' ),
				'default'           => '',
			)
		);

		add_settings_section(
			'stp_showcase_settings_main',
			__( 'Styling', 'team-players-showcase' ),
			array( __CLASS__, 'render_section_description' ),
			self::SETTINGS_PAGE
		);

		add_settings_field(
			self::OPTION_CUSTOM_CSS,
			__( 'Custom CSS', 'team-players-showcase' ),
			array( __CLASS__, 'render_custom_css_field' ),
			self::SETTINGS_PAGE,
			'stp_showcase_settings_main'
		);
	}

	/**
	 * Sanitize custom CSS value.
	 *
	 * @param string $css Raw CSS.
	 * @return string
	 */
	public static function sanitize_css( $css ) {
		$css = is_string( $css ) ? $css : '';
		$css = preg_replace( '#</?style[^>]*>#i', '', $css );
		$css = is_string( $css ) ? $css : '';
		$css = wp_strip_all_tags( $css, false );
		$css = trim( $css );

		$max_length = 50000;
		if ( strlen( $css ) > $max_length ) {
			$css = function_exists( 'mb_substr' ) ? mb_substr( $css, 0, $max_length ) : substr( $css, 0, $max_length );
		}

		return $css;
	}

	/**
	 * Get custom CSS.
	 *
	 * @return string
	 */
	public static function get_custom_css() {
		$css = get_option( self::OPTION_CUSTOM_CSS, '' );

		if ( ! is_string( $css ) ) {
			return '';
		}

		return trim( $css );
	}

	/**
	 * Render section helper text.
	 *
	 * @return void
	 */
	public static function render_section_description() {
		echo '<p>' . esc_html__( 'Write CSS overrides for all cards or specific player cards by ID class.', 'team-players-showcase' ) . '</p>';
	}

	/**
	 * Render custom CSS field.
	 *
	 * @return void
	 */
	public static function render_custom_css_field() {
		$css = self::get_custom_css();
		?>
		<textarea id="<?php echo esc_attr( self::OPTION_CUSTOM_CSS ); ?>" name="<?php echo esc_attr( self::OPTION_CUSTOM_CSS ); ?>" rows="16" class="large-text code"><?php echo esc_textarea( $css ); ?></textarea>
		<p class="description">
			<?php esc_html_e( 'Examples: .stp-player { ... } or .stp-player--id-123 { ... }', 'team-players-showcase' ); ?>
		</p>
		<?php
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Player Showcase Settings', 'team-players-showcase' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( self::SETTINGS_GROUP );
				do_settings_sections( self::SETTINGS_PAGE );
				submit_button( __( 'Save Settings', 'team-players-showcase' ) );
				?>
			</form>
		</div>
		<?php
	}
}
