<?php
/**
 * Player post type and metadata registration.
 *
 * @package TeamPlayersShowcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class STP_Post_Type {

	/**
	 * Custom post type slug.
	 */
	const POST_TYPE = 'stp_player';

	/**
	 * Meta keys used for player details.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function meta_fields() {
		return array(
			'_stp_age'             => array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			),
			'_stp_experience'      => array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			),
			'_stp_height_imperial' => array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'_stp_height_metric'   => array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			),
			'_stp_position'        => array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'_stp_jersey_number'   => array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			),
		);
	}

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
	}

	/**
	 * Register CPT + meta.
	 *
	 * @return void
	 */
	public static function register() {
		$labels = array(
			'name'               => __( 'Players', 'team-players-showcase' ),
			'singular_name'      => __( 'Player', 'team-players-showcase' ),
			'add_new'            => __( 'Add New', 'team-players-showcase' ),
			'add_new_item'       => __( 'Add New Player', 'team-players-showcase' ),
			'edit_item'          => __( 'Edit Player', 'team-players-showcase' ),
			'new_item'           => __( 'New Player', 'team-players-showcase' ),
			'view_item'          => __( 'View Player', 'team-players-showcase' ),
			'search_items'       => __( 'Search Players', 'team-players-showcase' ),
			'not_found'          => __( 'No players found', 'team-players-showcase' ),
			'not_found_in_trash' => __( 'No players found in trash', 'team-players-showcase' ),
			'all_items'          => __( 'All Players', 'team-players-showcase' ),
			'menu_name'          => __( 'Players', 'team-players-showcase' ),
			'name_admin_bar'     => __( 'Player', 'team-players-showcase' ),
		);

		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => $labels,
				'public'              => true,
				'show_in_rest'        => true,
				'has_archive'         => false,
				'rewrite'             => array(
					'slug' => 'players',
				),
				'menu_icon'           => 'dashicons-groups',
				'supports'            => array( 'title', 'thumbnail', 'page-attributes' ),
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
			)
		);

		self::register_meta();
	}

	/**
	 * Register post meta fields.
	 *
	 * @return void
	 */
	private static function register_meta() {
		foreach ( self::meta_fields() as $meta_key => $args ) {
			register_post_meta(
				self::POST_TYPE,
				$meta_key,
				array(
					'type'              => $args['type'],
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => $args['sanitize_callback'],
					'auth_callback'     => static function() {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
}
