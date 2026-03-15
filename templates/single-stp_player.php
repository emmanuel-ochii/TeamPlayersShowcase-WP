<?php
/**
 * Single template for Players CPT.
 *
 * @package TeamPlayersShowcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="primary" class="site-main stp-template stp-template--single">
	<div class="stp-template__inner">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php echo do_shortcode( '[stp_player_card id="' . absint( get_the_ID() ) . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
