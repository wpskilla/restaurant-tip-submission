<?php
/**
 * Registers the [restaurant_tip_form] shortcode.
 *
 * The shortcode renders the AJAX form.  It can also be called as a
 * template-part function: rts_render_tip_form().
 *
 * @package RestaurantTipSubmission
 */

defined( 'ABSPATH' ) || exit;

class RTS_Form {

	public function __construct() {
		add_shortcode( 'restaurant_tip_form', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Shortcode callback – always returns output (never echoes).
	 *
	 * @return string  HTML markup for the form.
	 */
	public function render_shortcode(): string {
		ob_start();
		$this->render();
		return ob_get_clean();
	}

	/**
	 * Echo the form markup.
	 * Call directly from a template part: ( new RTS_Form() )->render();
	 */
	public function render(): void {
		// The nonce is printed into the form as a hidden field so screen readers
		// and bots cannot see it in plain JavaScript variables.
		$nonce = wp_create_nonce( 'rts_submit_tip' );
		?>
		<section class="rts-form-wrap" aria-labelledby="rts-form-heading">
			<h2 id="rts-form-heading" class="rts-form-heading">
				<?php esc_html_e( 'Submit a Restaurant Tip', 'rts' ); ?>
			</h2>

			<p class="rts-form-intro">
				<?php esc_html_e( 'Know a great restaurant our readers should visit? Tell us about it — our editors review every tip.', 'rts' ); ?>
			</p>

			<!--
				aria-live region – JS writes success / error messages here so
				assistive technologies announce them without a page reload.
			-->
			<div id="rts-form-status" class="rts-form-status" role="status" aria-live="polite" aria-atomic="true"></div>

			<form id="rts-tip-form" class="rts-tip-form" novalidate>
				<!-- Hidden nonce – value printed server-side, never via JS -->
				<input type="hidden" name="rts_nonce" value="<?php echo esc_attr( $nonce ); ?>"/>

				<!-- ── Your name ───────────────────────────────────────── -->
				<div class="rts-field-group">
					<label for="rts-name" class="rts-label">
						<?php esc_html_e( 'Your Name', 'rts' ); ?>
						<span class="rts-required" aria-hidden="true">*</span>
					</label>
					<input type="text" id="rts-name" name="rts_name" class="rts-input" autocomplete="name" required aria-required="true" aria-describedby="rts-name-error"/>
					<span id="rts-name-error" class="rts-field-error" role="alert" aria-live="assertive"></span>
				</div>

				<!-- ── Email ────────────────────────────────────────────── -->
				<div class="rts-field-group">
					<label for="rts-email" class="rts-label">
						<?php esc_html_e( 'Your Email', 'rts' ); ?>
						<span class="rts-required" aria-hidden="true">*</span>
					</label>
					<input type="email" id="rts-email" name="rts_email" class="rts-input" autocomplete="email" required aria-required="true" aria-describedby="rts-email-error"/>
					<span id="rts-email-error" class="rts-field-error" role="alert" aria-live="assertive"></span>
				</div>

				<!-- ── Restaurant name ──────────────────────────────────── -->
				<div class="rts-field-group">
					<label for="rts-restaurant" class="rts-label">
						<?php esc_html_e( 'Restaurant Name', 'rts' ); ?>
						<span class="rts-required" aria-hidden="true">*</span>
					</label>
					<input type="text" id="rts-restaurant" name="rts_restaurant" class="rts-input" required aria-required="true" aria-describedby="rts-restaurant-error"/>
					<span id="rts-restaurant-error" class="rts-field-error" role="alert" aria-live="assertive"></span>
				</div>

				<!-- ── Message ──────────────────────────────────────────── -->
				<div class="rts-field-group">
					<label for="rts-message" class="rts-label">
						<?php esc_html_e( 'Your Message', 'rts' ); ?>
						<span class="rts-required" aria-hidden="true">*</span>
					</label>
					<textarea id="rts-message" name="rts_message" class="rts-input rts-textarea" rows="5" required aria-required="true" aria-describedby="rts-message-error"></textarea>
					<span id="rts-message-error" class="rts-field-error" role="alert" aria-live="assertive"></span>
				</div>

				<p class="rts-required-note">
					<span aria-hidden="true">*</span>
					<?php esc_html_e( 'Required fields', 'rts' ); ?>
				</p>

				<!-- ── Submit ───────────────────────────────────────────── -->
				<div class="rts-submit-wrap">
					<button type="submit" id="rts-submit" class="rts-btn rts-btn--primary">
						<span class="rts-btn-text">
							<?php esc_html_e( 'Send Tip', 'rts' ); ?>
						</span>
						<span class="rts-btn-spinner" aria-hidden="true"></span>
					</button>
				</div>

			</form>
		</section>
		<?php
	}
}

/**
 * Template-part helper.
 * Usage in a theme template: <?php rts_render_tip_form(); ?>
 */
function rts_render_tip_form(): void {
	( new RTS_Form() )->render();
}