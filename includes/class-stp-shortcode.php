<?php
/**
 * Single player card shortcode renderer.
 *
 * @package TeamPlayersShowcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STP_Shortcode {

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_shortcode( 'stp_player_card', array( __CLASS__, 'render_player_card_shortcode' ) );

		// Multi-player shortcode.
		add_shortcode( 'stp_players', array( __CLASS__, 'render_players_shortcode' ) );
		add_shortcode( 'team_players', array( __CLASS__, 'render_players_shortcode' ) );
	}

	/**
	 * Render multiple player cards.
	 *
	 * @param array<string, mixed> $atts Shortcode attributes.
	 * @return string
	 */
	public static function render_players_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'ids'    => '',
				'number' => -1,
				'order'  => 'ASC',
				'layout' => 'rows',
				'columns' => 2,
				'class'  => '',
			),
			is_array( $atts ) ? $atts : array(),
			'stp_players'
		);

		$ids    = self::sanitize_ids( (string) $atts['ids'] );
		$number = (int) $atts['number'];
		$order  = in_array( strtoupper( (string) $atts['order'] ), array( 'ASC', 'DESC' ), true ) ? strtoupper( (string) $atts['order'] ) : 'ASC';
		$layout  = self::sanitize_layout( (string) $atts['layout'] );
		$columns = self::sanitize_columns( $atts['columns'] );

		$query_args = array(
			'post_type'           => STP_Post_Type::POST_TYPE,
			'post_status'         => 'publish',
			'posts_per_page'      => $number > 0 ? $number : -1,
			'orderby'             => 'menu_order',
			'order'               => $order,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		if ( ! empty( $ids ) ) {
			$query_args['post__in'] = $ids;
			$query_args['orderby']  = 'post__in';

			if ( $number <= 0 ) {
				$query_args['posts_per_page'] = count( $ids );
			}
		}

		$query = new WP_Query( $query_args );
		if ( ! $query->have_posts() ) {
			return '<p class="stp-player-empty">' . esc_html__( 'No players found.', 'team-players-showcase' ) . '</p>';
		}

		STP_Assets::enqueue_frontend();

		$wrapper_classes   = array( 'stp-player-cards' );
		$wrapper_classes[] = ( 'columns' === $layout ) ? 'stp-player-cards--columns' : 'stp-player-cards--rows';

		$wrapper_classes = array_merge( $wrapper_classes, self::sanitize_user_classes( (string) $atts['class'] ) );
		$wrapper_class   = implode( ' ', array_unique( $wrapper_classes ) );
		$wrapper_style   = ( 'columns' === $layout ) ? '--stp-grid-columns:' . $columns . ';' : '';

		ob_start();
		?>
		<section class="<?php echo esc_attr( $wrapper_class ); ?>"<?php echo '' !== $wrapper_style ? ' style="' . esc_attr( $wrapper_style ) . '"' : ''; ?>>
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				echo self::render_card( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			endwhile;
			?>
		</section>
		<?php

		wp_reset_postdata();
		return (string) ob_get_clean();
	}

	/**
	 * Render one player card.
	 *
	 * @param array<string, mixed> $atts Shortcode attributes.
	 * @return string
	 */
	public static function render_player_card_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'    => 0,
				'class' => '',
			),
			is_array( $atts ) ? $atts : array(),
			'stp_player_card'
		);

		$player_id = absint( $atts['id'] );

		if ( $player_id <= 0 && is_singular( STP_Post_Type::POST_TYPE ) ) {
			$player_id = absint( get_queried_object_id() );
		}

		if ( $player_id <= 0 ) {
			$player_id = self::get_latest_player_id();
		}

		if ( $player_id <= 0 || STP_Post_Type::POST_TYPE !== get_post_type( $player_id ) ) {
			return '<p class="stp-player-empty">' . esc_html__( 'No player found.', 'team-players-showcase' ) . '</p>';
		}

		STP_Assets::enqueue_frontend();

		$wrapper_classes = array( 'stp-player-card' );
		$wrapper_classes = array_merge( $wrapper_classes, self::sanitize_user_classes( (string) $atts['class'] ) );
		$wrapper_class   = implode( ' ', array_unique( $wrapper_classes ) );

		return '<section class="' . esc_attr( $wrapper_class ) . '">' . self::render_card( $player_id ) . '</section>';
	}

	/**
	 * Render card HTML.
	 *
	 * @param int $post_id Player post ID.
	 * @return string
	 */
	private static function render_card( $post_id ) {
		$player_name     = get_the_title( $post_id );
		$age             = absint( get_post_meta( $post_id, '_stp_age', true ) );
		$experience      = absint( get_post_meta( $post_id, '_stp_experience', true ) );
		$height_imperial = sanitize_text_field( (string) get_post_meta( $post_id, '_stp_height_imperial', true ) );
		$height_metric   = absint( get_post_meta( $post_id, '_stp_height_metric', true ) );
		$position        = sanitize_text_field( (string) get_post_meta( $post_id, '_stp_position', true ) );
		$jersey_number   = absint( get_post_meta( $post_id, '_stp_jersey_number', true ) );

		$image_html = get_the_post_thumbnail(
			$post_id,
			'large',
			array(
				'class'    => 'stp-player__img',
				'loading'  => 'lazy',
				'decoding' => 'async',
				'alt'      => $player_name,
			)
		);

		ob_start();
		?>
		<article class="stp-player">
			<div class="stp-player__stats">
				<?php echo self::render_stat( __( 'Age', 'team-players-showcase' ), $age ? (string) $age : '--' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo self::render_stat( __( 'Exp', 'team-players-showcase' ), self::format_experience( $experience ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo self::render_stat( __( 'Ht', 'team-players-showcase' ), '' !== $height_imperial ? $height_imperial : '--' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo self::render_stat( __( 'Ht', 'team-players-showcase' ), $height_metric ? (string) $height_metric : '--' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<div class="stp-player__main">
				<div class="stp-player__image-wrap">
					<?php if ( $image_html ) : ?>
						<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php else : ?>
						<div class="stp-player__placeholder" aria-hidden="true"></div>
					<?php endif; ?>
				</div>
				<div class="stp-player__footer">
					<h3 class="stp-player__name"><?php echo esc_html( $player_name ); ?></h3>
					<div class="stp-player__identity">
						<span class="stp-player__number"><?php echo esc_html( self::format_jersey_number( $jersey_number ) ); ?></span>
						<span class="stp-player__position"><?php echo esc_html( '' !== $position ? $position : __( 'N/A', 'team-players-showcase' ) ); ?></span>
					</div>
				</div>
			</div>
		</article>
		<?php

		return (string) ob_get_clean();
	}

	/**
	 * Sanitize comma-separated post IDs.
	 *
	 * @param string $ids Comma-separated IDs.
	 * @return array<int, int>
	 */
	private static function sanitize_ids( $ids ) {
		$ids_array = wp_parse_id_list( $ids );
		if ( empty( $ids_array ) ) {
			return array();
		}

		return array_values( array_unique( array_map( 'absint', $ids_array ) ) );
	}

	/**
	 * Sanitize multi-player layout option.
	 *
	 * @param string $layout Layout value.
	 * @return string
	 */
	private static function sanitize_layout( $layout ) {
		$value = strtolower( trim( $layout ) );

		if ( in_array( $value, array( 'columns', 'column', 'grid' ), true ) ) {
			return 'columns';
		}

		return 'rows';
	}

	/**
	 * Sanitize columns count for multi-player columns layout.
	 *
	 * @param mixed $columns Columns value.
	 * @return int
	 */
	private static function sanitize_columns( $columns ) {
		$columns_count = absint( $columns );

		if ( $columns_count < 2 ) {
			return 2;
		}

		if ( $columns_count > 4 ) {
			return 4;
		}

		return $columns_count;
	}

	/**
	 * Find latest published player ID.
	 *
	 * @return int
	 */
	private static function get_latest_player_id() {
		$query = new WP_Query(
			array(
				'post_type'      => STP_Post_Type::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'fields'         => 'ids',
			)
		);

		if ( empty( $query->posts ) ) {
			return 0;
		}

		return absint( $query->posts[0] );
	}

	/**
	 * Render one stat item.
	 *
	 * @param string $label Stat label.
	 * @param string $value Stat value.
	 * @return string
	 */
	private static function render_stat( $label, $value ) {
		return sprintf(
			'<div class="stp-stat"><span class="stp-stat__label">%1$s</span><span class="stp-stat__value">%2$s</span></div>',
			esc_html( $label ),
			esc_html( $value )
		);
	}

	/**
	 * Format jersey number.
	 *
	 * @param int $jersey Jersey number.
	 * @return string
	 */
	private static function format_jersey_number( $jersey ) {
		if ( $jersey <= 0 ) {
			return '--';
		}

		return str_pad( (string) $jersey, 2, '0', STR_PAD_LEFT );
	}

	/**
	 * Format experience string.
	 *
	 * @param int $experience Experience in years.
	 * @return string
	 */
	private static function format_experience( $experience ) {
		if ( $experience <= 0 ) {
			return '--';
		}

		/* translators: %d: years of experience. */
		return sprintf( _n( '%d YR', '%d YRS', $experience, 'team-players-showcase' ), $experience );
	}

	/**
	 * Sanitize extra classes from shortcode attrs.
	 *
	 * @param string $classes Space-separated class list.
	 * @return array<int, string>
	 */
	private static function sanitize_user_classes( $classes ) {
		$clean_classes = array();
		$parts         = preg_split( '/\s+/', trim( $classes ) );

		if ( empty( $parts ) ) {
			return $clean_classes;
		}

		foreach ( $parts as $part ) {
			$sanitized = sanitize_html_class( $part );
			if ( '' !== $sanitized ) {
				$clean_classes[] = $sanitized;
			}
		}

		return $clean_classes;
	}
}
