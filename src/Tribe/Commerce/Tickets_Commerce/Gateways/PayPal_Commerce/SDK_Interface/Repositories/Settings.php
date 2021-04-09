<?php

namespace Tribe\Tickets\Commerce\Tickets_Commerce\Gateways\PayPal_Commerce\SDK_Interface\Repositories;

class Settings {

	/**
	 * wp_options key for the account country
	 *
	 * @since TBD
	 */
	const COUNTRY_KEY = 'paypal_commerce_account_country';

	/**
	 * wp_options key for the access token
	 *
	 * @since TBD
	 */
	const ACCESS_TOKEN_KEY = 'temp_tickets_paypal_commerce_seller_access_token';

	/**
	 * wp_options key for the partner link details
	 *
	 * @since TBD
	 */
	const PARTNER_LINK_DETAIL_KEY = 'temp_tickets_paypal_commerce_partner_link';

	/**
	 * Returns the country for the account
	 *
	 * @since TBD
	 *
	 * @return string|null
	 */
	public function getAccountCountry() {
		// @todo Replace this with a constant default value or a filtered value for setting the default country.
		return get_option( self::COUNTRY_KEY, give_get_country() );
	}

	/**
	 * Returns the account access token
	 *
	 * @since TBD
	 *
	 * @return array|null
	 */
	public function getAccessToken() {
		return get_option( self::ACCESS_TOKEN_KEY, null );
	}

	/**
	 * Updates the country account
	 *
	 * @param string $country
	 *
	 * @return bool
	 */
	public function updateAccountCountry( $country ) {
		return update_option( self::COUNTRY_KEY, $country );
	}

	/**
	 * Updates the account access token
	 *
	 * @param $token
	 *
	 * @return bool
	 */
	public function updateAccessToken( $token ) {
		return update_option( self::ACCESS_TOKEN_KEY, $token );
	}

	/**
	 * Deletes the account access token
	 *
	 * @return bool
	 */
	public function deleteAccessToken() {
		return delete_option( self::ACCESS_TOKEN_KEY );
	}

	/**
	 * Returns the partner link details
	 *
	 * @since TBD
	 *
	 * @return string|null
	 */
	public function getPartnerLinkDetails() {
		return get_option( self::PARTNER_LINK_DETAIL_KEY, null );
	}

	/**
	 * Updates the partner link details
	 *
	 * @param $linkDetails
	 *
	 * @return bool
	 */
	public function updatePartnerLinkDetails( $linkDetails ) {
		return update_option( self::PARTNER_LINK_DETAIL_KEY, $linkDetails );
	}

	/**
	 * Deletes the partner link details
	 *
	 * @return bool
	 */
	public function deletePartnerLinkDetails() {
		return delete_option( self::PARTNER_LINK_DETAIL_KEY );
	}
}
