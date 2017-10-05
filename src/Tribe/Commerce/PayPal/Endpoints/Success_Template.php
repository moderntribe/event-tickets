<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Endpoints__Success_Template
 *
 * @since TBD
 */
class Tribe__Tickets__Commerce__PayPal__Endpoints__Success_Template implements Tribe__Tickets__Commerce__PayPal__Endpoints__Template_Interface {

	/**
	 * Registers the resources this template will need to correctly render.
	 */
	public function register_resources() {
		// no-op
	}

	/**
	 * Builds and returns the date needed by this template.
	 *
	 * @since TBD
	 *
	 * @param array $template_data
	 *
	 * @return array
	 */
	public function get_template_data( array $template_data = array() ) {
		/** @var \Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal                          = tribe( 'tickets.commerce.paypal' );
		$template_data['order_is_valid'] = true;
		$order_number                    = $template_data['order_number'];
		$attendees                       = $paypal->get_attendees_by_order( $order_number );
		if ( empty( $attendees ) ) {
			// weird...
			$template_data['order_is_valid'] = false;

			return;
		}

		$template_data['post_id'] = Tribe__Utils__Array::get( $template_data, 'post_id', $paypal->get_post_id_from_order( $order_number ) );

		// the purchaser details will be the same for all the attendees, so we fetch it from the first
		$first                            = reset( $attendees );
		$template_data['purchaser_name']  = get_post_meta( $first->ID, $paypal->full_name, true );
		$template_data['purchaser_email'] = get_post_meta( $first->ID, $paypal->email, true );

		$order_quantity = $order_total = 0;
		$tickets        = array();

		foreach ( $attendees as $attendee ) {
			$order_quantity ++;
			$ticket_id    = get_post_meta( $attendee->ID, $paypal->attendee_product_key, true );
			$ticket_price = (int) get_post_meta( $ticket_id, '_price', true );
			$order_total  += $ticket_price;
			if ( array_key_exists( $ticket_id, $tickets ) ) {
				$tickets[ $ticket_id ]['quantity'] += 1;
				$tickets[ $ticket_id ]['subtotal'] = $tickets[ $ticket_id ]['quantity'] * $ticket_price;
			} else {
				$tickets[ $ticket_id ] = array(
					'name'     => get_the_title( $ticket_id ),
					'price'    => $ticket_price,
					'quantity' => 1,
					'subtotal' => $ticket_price,
				);
			}
		}

		$template_data['order']    = array( 'quantity' => $order_quantity, 'total' => $order_total );
		$template_data['tickets']  = $tickets;
		$template_data['is_event'] = function_exists( 'tribe_is_event' ) && tribe_is_event( $template_data['post_id'] );

		return $template_data;
	}

	/**
	 * Enqueues the resources needed by this template to correctly render.
	 *
	 * @since TBD
	 */
	public function enqueue_resources() {
		Tribe__Tickets__RSVP::get_instance()->enqueue_resources();
	}
}