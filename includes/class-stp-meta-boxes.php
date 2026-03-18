<?php
/**
 * Player meta boxes.
 *
 * @package TeamPlayersShowcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STP_Meta_Boxes {

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_box' ) );
		add_action( 'save_post', array( __CLASS__, 'save' ), 10, 2 );
	}

	/**
	 * Register details box.
	 *
	 * @return void
	 */
	public static function register_box() {
		add_meta_box(
			'stp-player-details',
			__( 'Player Details', 'team-players-showcase' ),
			array( __CLASS__, 'render_box' ),
			STP_Post_Type::POST_TYPE,
			'normal',
			'default'
		);
	}

	/**
	 * Render player details fields.
	 *
	 * @param WP_Post $post Post object.
	 * @return void
	 */
	public static function render_box( $post ) {
		wp_nonce_field( 'stp_save_player_details', 'stp_player_nonce' );

		$age             = get_post_meta( $post->ID, '_stp_age', true );
		$experience      = get_post_meta( $post->ID, '_stp_experience', true );
		$height_imperial = get_post_meta( $post->ID, '_stp_height_imperial', true );
		$height_metric   = get_post_meta( $post->ID, '_stp_height_metric', true );
		$position        = get_post_meta( $post->ID, '_stp_position', true );
		$jersey_number   = get_post_meta( $post->ID, '_stp_jersey_number', true );
		$player_link     = get_post_meta( $post->ID, '_stp_player_link', true );
		?>
		<p>
			<label for="stp_jersey_number"><strong><?php esc_html_e( 'Jersey Number', 'team-players-showcase' ); ?></strong></label><br>
			<input type="number" min="0" step="1" class="regular-text" id="stp_jersey_number" name="stp_jersey_number" value="<?php echo esc_attr( $jersey_number ); ?>">
		</p>
		<p>
			<label for="stp_position"><strong><?php esc_html_e( 'Position', 'team-players-showcase' ); ?></strong></label><br>
			<input type="text" class="regular-text" id="stp_position" name="stp_position" value="<?php echo esc_attr( $position ); ?>" placeholder="<?php esc_attr_e( 'Forward, Guard, Center...', 'team-players-showcase' ); ?>">
		</p>
		<p>
			<label for="stp_player_link"><strong><?php esc_html_e( 'Player Link', 'team-players-showcase' ); ?></strong></label><br>
			<input type="url" class="regular-text" id="stp_player_link" name="stp_player_link" value="<?php echo esc_attr( $player_link ); ?>" placeholder="<?php esc_attr_e( 'https://example.com/player-profile', 'team-players-showcase' ); ?>">
		</p>
		<p>
			<label for="stp_age"><strong><?php esc_html_e( 'Age', 'team-players-showcase' ); ?></strong></label><br>
			<input type="number" min="0" step="1" class="regular-text" id="stp_age" name="stp_age" value="<?php echo esc_attr( $age ); ?>">
		</p>
		<p>
			<label for="stp_experience"><strong><?php esc_html_e( 'Experience (Years)', 'team-players-showcase' ); ?></strong></label><br>
			<input type="number" min="0" step="1" class="regular-text" id="stp_experience" name="stp_experience" value="<?php echo esc_attr( $experience ); ?>">
		</p>
		<p>
			<label for="stp_height_imperial"><strong><?php esc_html_e( 'Height (Imperial)', 'team-players-showcase' ); ?></strong></label><br>
			<input type="text" class="regular-text" id="stp_height_imperial" name="stp_height_imperial" value="<?php echo esc_attr( $height_imperial ); ?>" placeholder="<?php esc_attr_e( 'e.g. 6\'2"', 'team-players-showcase' ); ?>">
		</p>
		<p>
			<label for="stp_height_metric"><strong><?php esc_html_e( 'Height (Metric, cm)', 'team-players-showcase' ); ?></strong></label><br>
			<input type="number" min="0" step="1" class="regular-text" id="stp_height_metric" name="stp_height_metric" value="<?php echo esc_attr( $height_metric ); ?>">
		</p>
		<?php
	}

	/**
	 * Save details.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return void
	 */
	public static function save( $post_id, $post ) {
		if ( STP_Post_Type::POST_TYPE !== $post->post_type ) {
			return;
		}

		if ( ! isset( $_POST['stp_player_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['stp_player_nonce'] ) ), 'stp_save_player_details' ) ) {
			return;
		}

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$meta_map = array(
			'_stp_age'             => isset( $_POST['stp_age'] ) ? absint( wp_unslash( $_POST['stp_age'] ) ) : '',
			'_stp_experience'      => isset( $_POST['stp_experience'] ) ? absint( wp_unslash( $_POST['stp_experience'] ) ) : '',
			'_stp_height_imperial' => isset( $_POST['stp_height_imperial'] ) ? sanitize_text_field( wp_unslash( $_POST['stp_height_imperial'] ) ) : '',
			'_stp_height_metric'   => isset( $_POST['stp_height_metric'] ) ? absint( wp_unslash( $_POST['stp_height_metric'] ) ) : '',
			'_stp_position'        => isset( $_POST['stp_position'] ) ? sanitize_text_field( wp_unslash( $_POST['stp_position'] ) ) : '',
			'_stp_jersey_number'   => isset( $_POST['stp_jersey_number'] ) ? absint( wp_unslash( $_POST['stp_jersey_number'] ) ) : '',
			'_stp_player_link'     => isset( $_POST['stp_player_link'] ) ? esc_url_raw( trim( (string) wp_unslash( $_POST['stp_player_link'] ) ) ) : '',
		);

		foreach ( $meta_map as $meta_key => $meta_value ) {
			if ( '' === $meta_value ) {
				delete_post_meta( $post_id, $meta_key );
				continue;
			}

			update_post_meta( $post_id, $meta_key, $meta_value );
		}
	}
}
