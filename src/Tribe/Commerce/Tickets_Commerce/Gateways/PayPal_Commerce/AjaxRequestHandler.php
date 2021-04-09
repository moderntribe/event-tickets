<?php

namespace Tribe\Tickets\Commerce\Tickets_Commerce\Gateways\PayPal_Commerce;

use Exception;
use TEC\ConnectClient\ConnectClient;
use Tribe\Tickets\Commerce\Tickets_Commerce\Gateways\PayPal_Commerce\SDK\Models\MerchantDetail;
use Tribe\Tickets\Commerce\Tickets_Commerce\Gateways\PayPal_Commerce\Repositories\MerchantDetails;
use Tribe\Tickets\Commerce\Tickets_Commerce\Gateways\PayPal_Commerce\Repositories\PayPalAuth;
use Tribe\Tickets\Commerce\Tickets_Commerce\Gateways\PayPal_Commerce\Repositories\SDK\PayPalOrder;
use Tribe\Tickets\Commerce\Tickets_Commerce\Gateways\PayPal_Commerce\Repositories\Settings;
use Tribe\Tickets\Commerce\Tickets_Commerce\Gateways\PayPal_Commerce\Repositories\Webhooks;

/**
 * Class AjaxRequestHandler
 *
 * @package Tribe\Tickets\Commerce\Tickets_Commerce\Gateways\PayPal_Commerce
 *
 * @since TBD
 */
class AjaxRequestHandler {

	/**
	 * @since TBD
	 *
	 * @var Webhooks
	 */
	private $webhooksRepository;

	/**
	 * @since TBD
	 *
	 * @var MerchantDetail
	 */
	private $merchantDetails;

	/**
	 * @since TBD
	 *
	 * @var PayPalAuth
	 */
	private $payPalAuth;

	/**
	 * @since TBD
	 *
	 * @var MerchantDetails
	 */
	private $merchantRepository;

	/**
	 * @since TBD
	 *
	 * @var ConnectClient
	 */
	private $refreshToken;

	/**
	 * @since TBD
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * AjaxRequestHandler constructor.
	 *
	 * @since TBD
	 *
	 * @param Webhooks        $webhooksRepository
	 * @param MerchantDetail  $merchantDetails
	 * @param MerchantDetails $merchantRepository
	 * @param RefreshToken    $refreshToken
	 * @param Settings        $settings
	 * @param PayPalAuth      $payPalAuth
	 */
	public function __construct(
		Webhooks $webhooksRepository,
		MerchantDetail $merchantDetails,
		MerchantDetails $merchantRepository,
		RefreshToken $refreshToken,
		Settings $settings,
		PayPalAuth $payPalAuth
	) {
		$this->webhooksRepository = $webhooksRepository;
		$this->merchantDetails    = $merchantDetails;
		$this->merchantRepository = $merchantRepository;
		$this->refreshToken       = $refreshToken;
		$this->settings           = $settings;
		$this->payPalAuth         = $payPalAuth;
	}

	/**
	 *  give_paypal_commerce_user_onboarded ajax action handler
	 *
	 * @since TBD
	 */
	public function onBoardedUserAjaxRequestHandler() {
		$this->validateAdminRequest();

		$partnerLinkInfo = $this->settings->getPartnerLinkDetails();

		$payPalResponse = $this->payPalAuth->getTokenFromAuthorizationCode(
			give_clean( $_GET['authCode'] ),
			give_clean( $_GET['sharedId'] ),
			$partnerLinkInfo['nonce']
		);

		if ( ! $payPalResponse || array_key_exists( 'error', $payPalResponse ) ) {
			wp_send_json_error();
		}

		$this->settings->updateAccessToken( $payPalResponse );

		tribe( RefreshToken::class )->registerCronJobToRefreshToken( $payPalResponse['expiresIn'] );

		wp_send_json_success();
	}

	/**
	 * give_paypal_commerce_get_partner_url action handler
	 *
	 * @since TBD
	 */
	public function onGetPartnerUrlAjaxRequestHandler() {
		$this->validateAdminRequest();

		if ( empty( $country = $_GET['countryCode'] ) || ! isset( give_get_country_list()[ $country ] ) ) {
			wp_send_json_error( 'Must include valid 2-character country code' );
		}

		$data = $this->payPalAuth->getSellerPartnerLink(
			admin_url( 'edit.php?post_type=give_forms&page=give-settings&tab=gateways&section=paypal&group=paypal-commerce' ),
			$country
		);

		if ( ! $data ) {
			wp_send_json_error();
		}

		$this->settings->updateAccountCountry( $country );
		$this->settings->updatePartnerLinkDetails( $data );

		wp_send_json_success( $data );
	}

	/**
	 * give_paypal_commerce_disconnect_account ajax request handler.
	 *
	 * @since TBD
	 */
	public function removePayPalAccount() {
		$this->validateAdminRequest();

		// Remove the webhook from PayPal if there is one
		if ( $webhookConfig = $this->webhooksRepository->getWebhookConfig() ) {
			$this->webhooksRepository->deleteWebhook( $this->merchantDetails->accessToken, $webhookConfig->id );
			$this->webhooksRepository->deleteWebhookConfig();
		}

		$this->merchantRepository->delete();
		$this->merchantRepository->deleteAccountErrors();
		$this->merchantRepository->deleteClientToken();
		$this->refreshToken->deleteRefreshTokenCronJob();

		wp_send_json_success();
	}

	/**
	 * Create order.
	 *
	 * @since TBD
	 * @todo  : handle payment create error on frontend.
	 *
	 */
	public function createOrder() {
		$this->validateFrontendRequest();

		// @todo Set up the order with our own custom code.

		$postData = give_clean( $_POST );
		$formId   = absint( tribe_get_request_var( 'give-form-id' ) );

		$data = [
			'formId'              => $formId,
			'formTitle'           => give_payment_gateway_item_title( [ 'post_data' => $postData ], 127 ),
			'donationAmount'      => isset( $postData['give-amount'] ) ? (float) apply_filters( 'give_donation_total', give_maybe_sanitize_amount( $postData['give-amount'], [ 'currency' => give_get_currency( $formId ) ] ) ) : '0.00',
			'payer'               => [
				'firstName' => $postData['give_first'],
				'lastName'  => $postData['give_last'],
				'email'     => $postData['give_email'],
			],
			'application_context' => [
				'shipping_preference' => 'NO_SHIPPING',
			],
		];

		try {
			$result = tribe( PayPalOrder::class )->createOrder( $data );

			wp_send_json_success(
				[
					'id' => $result,
				]
			);
		} catch ( \Exception $ex ) {
			wp_send_json_error(
				[
					'error' => json_decode( $ex->getMessage(), true ),
				]
			);
		}
	}

	/**
	 * Approve order.
	 *
	 * @since TBD
	 * @todo  : handle payment capture error on frontend.
	 *
	 */
	public function approveOrder() {
		$this->validateFrontendRequest();

		$orderId = absint( tribe_get_request_var( 'order' ) );

		// @todo Handle our own order approval process.

		try {
			$result = tribe( PayPalOrder::class )->approveOrder( $orderId );

			wp_send_json_success(
				[
					'order' => $result,
				]
			);
		} catch ( \Exception $ex ) {
			wp_send_json_error(
				[
					'error' => json_decode( $ex->getMessage(), true ),
				]
			);
		}
	}

	/**
	 * Return on boarding trouble notice.
	 *
	 * @since TBD
	 */
	public function onBoardingTroubleNotice() {
		if ( ! current_user_can( 'manage_give_settings' ) ) {
			wp_die();
		}

		/* @var AdminSettingFields $adminSettingFields */
		$adminSettingFields = tribe( AdminSettingFields::class );

		$actionList = sprintf(
			'<ol><li>%1$s</li><li>%2$s</li><li>%3$s %4$s</li></ol>',
			esc_html__( 'Make sure to complete the entire PayPal process. Do not close the window you have finished the process.', 'event-tickets' ),
			esc_html__( 'The last screen of the PayPal connect process includes a button to be sent back to your site. It is important you click this and do not close the window yourself.', 'event-tickets' ),
			esc_html__( 'If you’re still having problems connecting:', 'event-tickets' ),
			$adminSettingFields->getAdminGuidanceNotice( false )
		);

		$standardError = sprintf(
			'<div id="give-paypal-onboarding-trouble-notice" class="give-hidden"><p class="error-message">%1$s</p><p>%2$s</p></div>',
			esc_html__( 'Having trouble connecting to PayPal?', 'event-tickets' ),
			$actionList
		);

		wp_send_json_success( $standardError );
	}

	/**
	 * Validate admin ajax request.
	 *
	 * @since TBD
	 */
	private function validateAdminRequest() {
		// @todo Add our own capacity check.
		if ( ! current_user_can( 'manage_give_settings' ) ) {
			wp_die();
		}
	}

	/**
	 * Validate frontend ajax request.
	 *
	 * @since TBD
	 */
	private function validateFrontendRequest() {
		$formId = absint( $_POST['give-form-id'] );

		if ( ! $formId || ! give_verify_donation_form_nonce( give_clean( $_POST['give-form-hash'] ), $formId ) ) {
			wp_die();
		}
	}
}
