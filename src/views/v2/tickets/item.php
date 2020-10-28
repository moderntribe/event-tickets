<?php
/**
 * Block: Tickets
 * Single Ticket Item
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/tickets/item.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://m.tri.be/1amp Help article for RSVP & Ticket template files.
 *
 * @since   TBD
 *
 * @version TBD
 *
 * If RSVP:
 * @var Tribe__Tickets__Editor__Template   $this                Template object.
 * @var null|bool                          $is_modal            [Global] Whether the modal is enabled.
 * @var int                                $post_id             [Global] The current Post ID to which tickets are attached.
 * @var Tribe__Tickets__Tickets            $provider            [Global] The tickets provider class.
 * @var string                             $provider_id         [Global] The tickets provider class name.
 * @var string                             $cart_url            [Global] Link to Cart (could be empty).
 * @var Tribe__Tickets__Ticket_Object[]    $tickets             [Global] List of tickets.
 * @var Tribe__Tickets__Ticket_Object[]    $tickets_on_sale     [Global] List of tickets on sale.
 * @var bool                               $has_tickets_on_sale [Global] True if the event has any tickets on sale.
 * @var bool                               $is_sale_past        [Global] True if tickets' sale dates are all in the past.
 * @var bool                               $is_sale_future      [Global] True if no ticket sale dates have started yet.
 * @var Tribe__Tickets__Commerce__Currency $currency            [Global] Tribe Currency object.
 * @var Tribe__Tickets__Ticket_Object      $ticket              The ticket object.
 *
 * If Ticket, some of the above, plus:
 * @var int                                 $key                    The ticket key.
 * @var bool                                $is_mini                True if it's in mini cart context.
 * @var array                               $events                 [Global] Multidimensional array of post IDs with their ticket data.
 */

if (
	empty( $provider )
	|| $ticket->provider_class !== $provider->class_name
) {
	return false;
}

/* @var Tribe__Tickets__Privacy $privacy */
$privacy = tribe( 'tickets.privacy' );

/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
$tickets_handler = tribe( 'tickets.handler' );

$context = [
	'ticket'        => $ticket,
	'key'           => $this->get( 'key' ),
	'privacy'       => $privacy,
	'max_at_a_time' => $tickets_handler->get_ticket_max_purchase( $ticket->ID ),
];

$has_suffix = ! empty( $ticket->price_suffix );

$classes = [
	'tribe-tickets__tickets-item',
	'tribe-tickets__tickets-item--disabled'     => ! empty( $must_login ),
	'tribe-tickets__tickets-item--price-suffix' => $has_suffix,
	get_post_class( '', $ticket->ID ),
];

$ticket_item_id = 'tribe-';
$ticket_item_id .= ! empty( $is_modal ) ? 'modal' : 'block';
$ticket_item_id .= '-tickets-item-' . $ticket->ID;

// ET has this set from global context but ETP does not.
$has_shared_cap = isset( $has_shared_cap ) ? $has_shared_cap : $this->get( 'has_shared_cap' );
?>
<div
	id="<?php echo esc_attr( $ticket_item_id ); ?>"
	<?php tribe_classes( $classes ); ?>
	data-ticket-id="<?php echo esc_attr( $ticket->ID ); ?>"
	data-available="<?php echo esc_attr( $this->get( 'data_available' ) ); ?>"
	data-has-shared-cap="<?php echo esc_attr( $this->get( 'data_has_shared_cap' ) ); ?>"
	<?php if ( $has_shared_cap ) : ?>
		data-shared-cap="<?php echo esc_attr( get_post_meta( $post_id, $tickets_handler->key_capacity, true ) ); ?>"
	<?php endif; ?>
>

	<?php $this->template( 'v2/tickets/item/content', $context ); ?>

	<?php $this->template( 'v2/tickets/item/quantity', $context ); ?>

	<?php $this->template( 'v2/tickets/item/quantity-mini', $context ); ?>

	<?php $this->template( 'v2/tickets/item/opt-out', $context ); ?>

</div>
