<?php
/**
 * Template Part: Restaurant Tip Form
 *
 * Drop this file into your theme's directory and call it with:
 *
 *   <?php get_template_part( 'template-part', 'restaurant-tip-form' ); ?>
 *
 * Or copy it into your child-theme and customise the surrounding markup.
 * The form itself is always rendered via the plugin's RTS_Form class so
 * it stays in sync when the plugin updates.
 *
 * @package RestaurantTipSubmission
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'rts_render_tip_form' ) ) {
	// Plugin is inactive — fail silently so the template does not fatal.
	return;
}
?>

<main id="main-content" class="site-main" tabindex="-1">

	<!-- Skip link target (the link itself lives in header.php) -->

	<article class="page-content rts-page">
		<div class="rts-page__inner">

			<?php rts_render_tip_form(); ?>

		</div>
	</article>

</main>