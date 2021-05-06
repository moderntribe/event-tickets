<?php

namespace Tribe\Tickets\Commerce\Tickets_Commerce\Gateways\PayPal_Commerce\Webhooks;

use Exception;
use Tribe\Tickets\Commerce\Tickets_Commerce\Gateways\PayPal_Commerce\REST;
use Tribe\Tickets\Commerce\Tickets_Commerce\Gateways\PayPal_Commerce\SDK_Interface\Repositories\MerchantDetails;
use Tribe\Tickets\Commerce\Tickets_Commerce\Gateways\PayPal_Commerce\SDK_Interface\Repositories\Webhooks;
use Tribe\Tickets\Commerce\Tickets_Commerce\Gateways\PayPal_Commerce\SDK\DataTransferObjects\PayPalWebhookHeaders;
use Tribe\Tickets\Commerce\Tickets_Commerce\Gateways\PayPal_Commerce\Webhooks\WebhookRegister;
use Tribe\Tickets\REST\V1\Endpoints\PayPal_Commerce\Webhook;

class WebhooksRoute {
	/**
	 * @since TBD
	 *
	 * @var MerchantDetails
	 */
	private $merchantRepository;

	/**
	 * @since TBD
	 *
	 * @var Webhooks
	 */
	private $webhookRepository;

	/**
	 * @since TBD
	 *
	 * @var WebhookRegister
	 */
	private $webhookRegister;

	/**
	 * WebhooksRoute constructor.
	 *
	 * @since TBD
	 *
	 * @param MerchantDetails $merchantRepository
	 * @param WebhookRegister $register
	 * @param Webhooks        $webhookRepository
	 */
	public function __construct( MerchantDetails $merchantRepository, WebhookRegister $register, Webhooks $webhookRepository ) {
		$this->merchantRepository = $merchantRepository;
		$this->webhookRegister    = $register;
		$this->webhookRepository  = $webhookRepository;
	}

	/**
	 * Get the REST API route URL.
	 *
	 * @since TBD
	 *
	 * @return string The REST API route URL.
	 */
	public function getRouteUrl() {
		/** @var REST $rest */
		$rest = tribe( REST::class );

		/** @var Webhook $endpoint */
		$endpoint = tribe( Webhook::class );

		return rest_url( '/' . $rest->namespace . $endpoint->path, 'https' );
	}

	/**
	 * Handles all webhook event requests. First it verifies that authenticity of the event with
	 * PayPal, and then it passes the event along to the appropriate listener to finish.
	 *
	 * @since TBD
	 *
	 * @param string|object $event   The PayPal payment event object.
	 * @param array         $headers The list of HTTP headers for the request.
	 *
	 * @return bool Whether the event was processed.
	 *
	 * @throws Exception
	 */
	public function handle( $event, $headers = [] ) {
		if ( ! $this->merchantRepository->accountIsConnected() ) {
			return false;
		}

		$merchantDetails = $this->merchantRepository->getDetails();

		// Try to decode the event.
		if ( ! is_object( $event ) ) {
			$event = @json_decode( $event );

			// The event is not valid.
			if ( ! $event ) {
				return false;
			}
		}

		// If we receive an event that we're not expecting, just ignore it
		if ( ! $this->webhookRegister->hasEventRegistered( $event->event_type ) ) {
			tribe( 'logger' )->log_debug(
				sprintf(
					// Translators: %s: The event type.
					__( 'PayPal webhook event type not registered or supported: %s', 'event-tickets' ),
					$event->event_type
				),
				'tickets-commerce-paypal-commerce'
			);

			return false;
		}

		tribe( 'logger' )->log_debug(
			sprintf(
				// Translators: %s: The event type.
				__( 'Received PayPal webhook event for type: %s', 'event-tickets' ),
				$event->event_type
			),
			'tickets-commerce-paypal-commerce'
		);

		$payPalHeaders = PayPalWebhookHeaders::fromHeaders( $headers );

		if ( ! $this->webhookRepository->verifyEventSignature( $merchantDetails->accessToken, $event, $payPalHeaders ) ) {
			tribe( 'logger' )->log_error( __( 'Failed PayPal webhook event verification', 'event-tickets' ), 'tickets-commerce-paypal-commerce' );

			throw new Exception( 'Failed event verification' );
		}

		try {
			return $this->webhookRegister
				->getEventHandler( $event->event_type )
				->processEvent( $event );
		} catch ( Exception $exception ) {
			$eventType = empty( $event->event_type ) ? 'Unknown' : $event->event_type;

			tribe( 'logger' )->log_error( sprintf(
				// Translators: %s: The event type.
				__( 'Error processing webhook: %s', 'event-tickets' ),
				$eventType
			), 'tickets-commerce-paypal-commerce' );

			throw $exception;
		}
	}
}
