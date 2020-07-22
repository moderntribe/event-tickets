<?php
/**
 * Block: RSVP
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link  {INSERT_ARTICLE_LINK_HERE}
 *
 * @since TBD
 *
 * @version TBD
 *
 * @var Tribe__Tickets__Editor__Template $this
 */

$post_id          = $this->get( 'post_id' );
$rsvps            = $this->get( 'active_rsvps' );
$has_active_rsvps = $this->get( 'has_active_rsvps' );
$has_rsvps        = $this->get( 'has_rsvps' );

// We don't display anything if there is no RSVP.
if ( ! $has_rsvps ) {
	return false;
}

/**
 * A flag we can set via filter, e.g. at the end of this method, to ensure this template only shows once.
 *
 * @since 4.5.6
 *
 * @param boolean $already_rendered Whether the order link template has already been rendered.
 *
 * @see Tribe__Tickets__Tickets_View::inject_link_template()
 */
$already_rendered = apply_filters( 'tribe_tickets_order_link_template_already_rendered', false );

// Output order links / view link if we haven't already (for RSVPs).
// @todo @juanfra: componetize this.
if ( ! $already_rendered ) {
	$html = $this->template( 'blocks/attendees/order-links', [], false );

	if ( empty( $html ) ) {
		$html = $this->template( 'blocks/attendees/view-link', [], false );
	}

	echo $html;

	add_filter( 'tribe_tickets_order_link_template_already_rendered', '__return_true' );
}

?>
<?php if ( $has_active_rsvps ) : ?>
	<div class="tribe-common event-tickets">

		<?php foreach ( $rsvps as $rsvp ) : ?>

			<div
				class="tribe-tickets__rsvp-wrapper"
				data-rsvp-id="<?php echo esc_attr( $rsvp->ID ); ?>"
			>
				<?php $this->template( 'v2/components/loader/loader' ); ?>
				<?php $this->template( 'v2/rsvp/content', [ 'rsvp' => $rsvp ] ); ?>

			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
