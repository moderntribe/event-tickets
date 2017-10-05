<?php

class Tribe__Tickets__Commerce__PayPal__Handler__PDT implements Tribe__Tickets__Commerce__PayPal__Handler__Interface {

	/**
	 * Set up hooks for PDT transaction handling
	 *
	 * @since TBD
	 */
	public function hook() {
		add_action( 'template_redirect', array( $this, 'check_response' ) );
	}

	/**
	 * Checks the request to see if payment data was communicated
	 *
	 * @since TBD
	 */
	public function check_response() {
		if ( ! isset( $_GET['tx'] ) ) {
			return;
		}

		$paypal  = tribe( 'tickets.commerce.paypal' );
		$gateway = tribe( 'tickets.commerce.paypal.gateway' );

		$results = $this->validate_transaction( $_GET['tx'] );

		if ( false === $results ) {
			return false;
		}

		$results = $gateway->parse_transaction( $results );
		$gateway->set_transaction_data( $results );

		$paypal->generate_tickets();

		// since the purchase has completed, reset the invoice number
		$gateway->reset_invoice_number();
	}

	/**
	 * Validates a PayPal transaction ensuring that it is authentic
	 *
	 * @since TBD
	 *
	 * @param string $transaction
	 *
	 * @return array|bool
	 */
	public function validate_transaction( $transaction = null ) {
		$gateway = tribe( 'tickets.commerce.paypal.gateway' );

		$args = array(
			'httpversion' => '1.1',
			'timeout'     => 60,
			'user-agent'  => 'EventTickets/' . Tribe__Tickets__Main::VERSION,
			'body'        => array(
				'cmd' => '_notify-synch',
				'tx'  => $transaction,
				'at'  => $gateway->identity_token,
			),
		);

		$response = wp_safe_remote_post( $gateway->get_cart_url(), $args );

		if (
			is_wp_error( $response )
			|| ! ( 0 === strpos( $response['body'], 'SUCCESS' ) )
		) {
			return false;
		}

		return $this->parse_transaction_body( $response['body'] );
	}

	/**
	 * Parses flat transaction text
	 *
	 * @since TBD
	 *
	 * @param string $transaction
	 *
	 * @return array
	 */
	public function parse_transaction_body( $transaction ) {
		$results = array();

		$body    = explode( "\n", $transaction );

		foreach ( $body as $line ) {
			if ( ! trim( $line ) ) {
				continue;
			}

			$line            = explode( '=', $line );
			$var             = array_shift( $line );
			$results[ $var ] = urldecode( implode( '=', $line ) );
		}

		return $results;
	}
}