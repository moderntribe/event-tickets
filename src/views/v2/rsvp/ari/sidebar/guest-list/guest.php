<?php
/**
 * This template renders the RSVP ARI sidebar guest list item.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/ari/sidebar/guest-list/item.php
 *
 * @since TBD
 *
 * @version TBD
 */

?>
<li class="tribe-tickets__rsvp-ar-guest-list-item">
	<button
		class="tribe-tickets__rsvp-ar-guest-list-item-button"
		type="button"
		data-guest-number="1"
	>
		<?php $this->template( 'v2/components/icons/guest', [ 'classes' => [ 'tribe-tickets__rsvp-ar-guest-icon' ] ] ); ?>
		<span class="tribe-tickets__rsvp-ar-guest-list-item-title tribe-common-a11y-visual-hide">
			<?php
			echo esc_html(
				sprintf(
					/* Translators: %s Guest label for RSVP attendee registration sidebar. */
					__( 'Main %s', 'event-tickets' ),
					tribe_get_guest_label_singular( 'RSVP attendee registration sidebar guest button' )
				)
			);
			?>
		</span>
	</button>
</li>