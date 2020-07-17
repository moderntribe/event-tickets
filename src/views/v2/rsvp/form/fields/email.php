<?php
/**
 * Block: RSVP
 * Form Email
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/form/fields/email.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link {INSERT_ARTICLE_LINK_HERE}
 *
 * @since TBD
 * @version TBD
 */

/**
 * Set the default value for the email on the RSVP form.
 *
 * @param string
 * @param Tribe__Events_Gutenberg__Template $this
 *
 * @since 4.9
 */
$email = apply_filters( 'tribe_tickets_rsvp_form_email', '', $this );
?>
<div class="tribe-common-b1 tribe-tickets__form-field tribe-tickets__form-field--required">
	<label
		class="tribe-common-b2--min-medium tribe-tickets__form-field-label"
		for="tribe-tickets-rsvp-email"
	>
		<?php esc_html_e( 'Email', 'event-tickets' ); ?><span class="screen-reader-text"><?php esc_html_e( 'required', 'event-tickets' ); ?></span>
		<span class="tribe-required" aria-hidden="true" role="presentation">*</span>
	</label>
	<input
		type="email"
		id="tribe-tickets-rsvp-email"
		class="tribe-common-form-control-text__input tribe-tickets__form-field-input"
		name="attendee[email]"
		value="<?php echo esc_attr( $email ); ?>"
		required
		placeholder="<?php esc_attr_e( 'your@email.com', 'event-tickets' ); ?>"
	>
</div>
