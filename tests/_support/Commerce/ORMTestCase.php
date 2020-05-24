<?php

namespace Tribe\Tickets\Test\Commerce;

use Tribe\Events\Test\Factories\Event;
use Tribe\Tickets\Test\Commerce\PayPal\Ticket_Maker as PayPal_Ticket_Maker;
use Tribe\Tickets\Test\Commerce\RSVP\Ticket_Maker as RSVP_Ticket_Maker;
use Tribe__Tickets__Data_API as Data_API;

/**
 * Class ORMTestCase
 *
 * @package Tribe\Tickets\Test\Commerce
 */
class ORMTestCase extends Test_Case {

	use RSVP_Ticket_Maker;
	use PayPal_Ticket_Maker;
	use Attendee_Maker;

	/**
	 * The array of generated data.
	 *
	 * @see setup_test_data()
	 *
	 * @var array
	 */
	protected $test_data = [];

	public function setUp() {
		parent::setUp();

		$this->factory()->event = new Event();

		// Enable post as ticket type.
		add_filter( 'tribe_tickets_post_types', function () {
			return [ 'post' ];
		} );

		// Enable Tribe Commerce.
		add_filter( 'tribe_tickets_commerce_paypal_is_active', '__return_true' );
		add_filter( 'tribe_tickets_get_modules', function ( $modules ) {
			/** @var \Tribe__Tickets__Commerce__PayPal__Main $paypal */
			$paypal = tribe( 'tickets.commerce.paypal' );

			$modules['Tribe__Tickets__Commerce__PayPal__Main'] = $paypal->plugin_name;

			return $modules;
		} );

		// Reset Data_API object so it sees Tribe Commerce.
		tribe_singleton( 'tickets.data_api', new Data_API );

		// Setup test data here.
		$this->setup_test_data();
	}

	/**
	 * Get test matrix with all the assertions filled out.
	 *
	 * Method naming:
	 * "Match" means the filter finds what we expect it to with the created data.
	 * "Mismatch" is for filtering ones we expect to match an empty array (in most cases), such as matching the
	 * attendees for an RSVP ticket without any. It is to confirm we don't get results when we shouldn't.
	 *
	 * @see \Tribe__Tickets__Attendee_Repository::__construct() These tests are in the schema's order added
	 *                                                          so we know we got them all.
	 */
	public function get_attendee_test_matrix() {
		// Event
		yield 'event match single' => [ 'get_test_matrix_single_event_match' ];
		yield 'event match multi' => [ 'get_test_matrix_multi_event_match' ];
		yield 'event mismatch single' => [ 'get_test_matrix_single_event_mismatch' ];
		yield 'event mismatch multi' => [ 'get_test_matrix_multi_event_mismatch' ];
		// Event Not In
		yield 'event not in match single' => [ 'get_test_matrix_single_event_not_in_match' ];
		yield 'event not in match multi' => [ 'get_test_matrix_multi_event_not_in_match' ];
		yield 'event not in mismatch single' => [ 'get_test_matrix_single_event_not_in_mismatch' ];
		yield 'event not in mismatch multi' => [ 'get_test_matrix_multi_event_not_in_mismatch' ];

		// Ticket
		yield 'ticket match single' => [ 'get_test_matrix_single_ticket_match' ];
		yield 'ticket match multi' => [ 'get_test_matrix_multi_ticket_match' ];
		yield 'ticket mismatch single' => [ 'get_test_matrix_single_ticket_mismatch' ];
		yield 'ticket mismatch multi' => [ 'get_test_matrix_multi_ticket_mismatch' ];
		// Ticket Not In
		yield 'ticket not in match single' => [ 'get_test_matrix_single_ticket_not_in_match' ];
		yield 'ticket not in match multi' => [ 'get_test_matrix_multi_ticket_not_in_match' ];
		yield 'ticket not in mismatch single' => [ 'get_test_matrix_single_ticket_not_in_mismatch' ];
		yield 'ticket not in mismatch multi' => [ 'get_test_matrix_multi_ticket_not_in_mismatch' ];

		// Order
		yield 'order match single' => [ 'get_test_matrix_single_order_match' ];
		yield 'order match multi' => [ 'get_test_matrix_multi_order_match' ];
		yield 'order mismatch single' => [ 'get_test_matrix_single_order_mismatch' ];
		yield 'order mismatch multi' => [ 'get_test_matrix_multi_order_mismatch' ];
		// Order Not In
		yield 'order not in match single' => [ 'get_test_matrix_single_order_not_in_match' ];
		yield 'order not in match multi' => [ 'get_test_matrix_multi_order_not_in_match' ];
		yield 'order not in mismatch single' => [ 'get_test_matrix_single_order_not_in_mismatch' ];
		yield 'order not in mismatch multi' => [ 'get_test_matrix_multi_order_not_in_mismatch' ];

		// Purchaser Name
		yield 'purchaser_name match single' => [ 'get_test_matrix_single_purchaser_name_match' ];
		yield 'purchaser_name match multi' => [ 'get_test_matrix_multi_purchaser_name_match' ];
		yield 'purchaser_name mismatch single' => [ 'get_test_matrix_single_purchaser_name_mismatch' ];
		yield 'purchaser_name mismatch multi' => [ 'get_test_matrix_multi_purchaser_name_mismatch' ];
		// Purchaser Name Not In
		yield 'purchaser_name not in match single' => [ 'get_test_matrix_single_purchaser_name_not_in_match' ];
		yield 'purchaser_name not in match multi' => [ 'get_test_matrix_multi_purchaser_name_not_in_match' ];
		yield 'purchaser_name not in mismatch single' => [ 'get_test_matrix_single_purchaser_name_not_in_mismatch' ];
		yield 'purchaser_name not in mismatch multi' => [ 'get_test_matrix_multi_purchaser_name_not_in_mismatch' ];
		// Purchaser Name Like (does not support Multi)
		yield 'purchaser_name like match single' => [ 'get_test_matrix_single_purchaser_name_like_match' ];
		yield 'purchaser_name like mismatch single' => [ 'get_test_matrix_single_purchaser_name_like_mismatch' ];

		// RSVP
		yield 'rsvp match single' => [ 'get_test_matrix_single_rsvp_match' ];
		yield 'rsvp match multi' => [ 'get_test_matrix_multi_rsvp_match' ];
		yield 'rsvp mismatch single' => [ 'get_test_matrix_single_rsvp_mismatch' ];
		yield 'rsvp mismatch multi' => [ 'get_test_matrix_multi_rsvp_mismatch' ];
		// RSVP Not In
		yield 'rsvp not in match single' => [ 'get_test_matrix_single_rsvp_not_in_match' ];
		yield 'rsvp not in match multi' => [ 'get_test_matrix_multi_rsvp_not_in_match' ];
		yield 'rsvp not in mismatch single' => [ 'get_test_matrix_single_rsvp_not_in_mismatch' ];
		yield 'rsvp not in mismatch multi' => [ 'get_test_matrix_multi_rsvp_not_in_mismatch' ];

		// Tribe Commerce PayPal
		yield 'paypal match single' => [ 'get_test_matrix_single_paypal_match' ];
		yield 'paypal match multi' => [ 'get_test_matrix_multi_paypal_match' ];
		yield 'paypal mismatch single' => [ 'get_test_matrix_single_paypal_mismatch' ];
		yield 'paypal mismatch multi' => [ 'get_test_matrix_multi_paypal_mismatch' ];
		// Tribe Commerce PayPal Not In
		yield 'paypal not in match single' => [ 'get_test_matrix_single_paypal_not_in_match' ];
		yield 'paypal not in match multi' => [ 'get_test_matrix_multi_paypal_not_in_match' ];
		yield 'paypal not in mismatch single' => [ 'get_test_matrix_single_paypal_not_in_mismatch' ];
		yield 'paypal not in mismatch multi' => [ 'get_test_matrix_multi_paypal_not_in_mismatch' ];

		// User
		yield 'user match single' => [ 'get_test_matrix_single_user_match' ];
		yield 'user match multi' => [ 'get_test_matrix_multi_user_match' ];
		yield 'user mismatch single' => [ 'get_test_matrix_single_user_mismatch' ];
		yield 'user mismatch multi' => [ 'get_test_matrix_multi_user_mismatch' ];
		// User Not In
		yield 'user not in match single' => [ 'get_test_matrix_single_user_not_in_match' ];
		yield 'user not in match multi' => [ 'get_test_matrix_multi_user_not_in_match' ];
		yield 'user not in mismatch single' => [ 'get_test_matrix_single_user_not_in_mismatch' ];
		yield 'user not in mismatch multi' => [ 'get_test_matrix_multi_user_not_in_mismatch' ];

		// Price Paid, Paid Min, and Paid Max
		yield 'price match single' => [ 'get_test_matrix_single_price_match' ];
		yield 'price mismatch single' => [ 'get_test_matrix_single_price_mismatch' ];
		// @todo ORM broken: yield 'price minimum match single' => [ 'get_test_matrix_single_price_min_match' ];
		// @todo ORM broken: yield 'price minimum mismatch single' => [ 'get_test_matrix_single_price_min_mismatch' ];
		// @todo ORM broken: yield 'price maximum match single' => [ 'get_test_matrix_single_price_max_match' ];
		// @todo ORM broken: yield 'price maximum mismatch single' => [ 'get_test_matrix_single_price_max_mismatch' ];
		// Price Paid, Paid Min, and Paid Max Not In
		////yield 'price not in match single' => [ 'get_test_matrix_single_price_not_in_match' ];
		////yield 'price not in mismatch single' => [ 'get_test_matrix_single_price_not_in_mismatch' ];
		////yield 'price not in minimum match single' => [ 'get_test_matrix_single_price_min_not_in_match' ];
		////yield 'price not in minimum mismatch single' => [ 'get_test_matrix_single_price_min_not_in_mismatch' ];
		////yield 'price not in maximum match single' => [ 'get_test_matrix_single_price_max_not_in_match' ];
		////yield 'price not in maximum mismatch single' => [ 'get_test_matrix_single_price_max_not_in_mismatch' ];
	}

	/**
	 * EVENTS
	 */

	/**
	 * Get test matrix for Event match.
	 */
	public function get_test_matrix_single_event_match() {
		return [
			// Repository
			'default',
			// Filter name.
			'event',
			// Filter arguments to use.
			[
				[
					$this->get_event_id( 0 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_event_1'] ),
		];
	}

	/**
	 * Get test matrix for multiple Event match.
	 */
	public function get_test_matrix_multi_event_match() {
		return [
			// Repository
			'default',
			// Filter name.
			'event',
			// Filter arguments to use.
			[
				[
					$this->get_event_id( 0 ),
					$this->get_event_id( 1 ),
					$this->get_event_id( 2 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_all'] ),
		];
	}

	/**
	 * Get test matrix for Event mismatch.
	 */
	public function get_test_matrix_single_event_mismatch() {
		return [
			// Repository
			'default',
			// Filter name.
			'event',
			// Filter arguments to use.
			[
				[
					$this->get_event_id( 1 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for multiple Events mismatch.
	 */
	public function get_test_matrix_multi_event_mismatch() {
		return [
			// Repository
			'default',
			// Filter name.
			'event',
			// Filter arguments to use.
			[
				[
					$this->get_event_id( 1 ),
					$this->get_event_id( 3 ),
				]
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for Event Not In match.
	 */
	public function get_test_matrix_single_event_not_in_match() {
		return [
			// Repository
			'default',
			// Filter name.
			'event__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_event_id( 1 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_all'] ),
		];
	}

	/**
	 * Get test matrix for multiple Events Not In match.
	 */
	public function get_test_matrix_multi_event_not_in_match() {
		return [
			// Repository
			'default',
			// Filter name.
			'event__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_event_id( 1 ),
					$this->get_event_id( 4 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_all'] ),
		];
	}

	/**
	 * Get test matrix for Event Not In mismatch.
	 */
	public function get_test_matrix_single_event_not_in_mismatch() {
		return [
			// Repository
			'default',
			// Filter name.
			'event__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_event_id( 0 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_event_3'] ),
		];
	}

	/**
	 * Get test matrix for multiple Events Not In mismatch.
	 */
	public function get_test_matrix_multi_event_not_in_mismatch() {
		return [
			// Repository
			'default',
			// Filter name.
			'event__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_event_id( 0 ),
					$this->get_event_id( 2 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * TICKETS
	 */

	/**
	 * Get test matrix for Ticket match.
	 */
	public function get_test_matrix_single_ticket_match() {
		return [
			// Repository
			'default',
			// Filter name.
			'ticket',
			// Filter arguments to use.
			[
				[
					$this->test_data['tickets_products_rsvp'][0]
				]
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_rsvp_1'] ),
		];
	}

	/**
	 * Get test matrix for multiple Ticket match.
	 */
	public function get_test_matrix_multi_ticket_match() {
		return [
			// Repository
			'default',
			// Filter name.
			'ticket',
			// Filter arguments to use.
			[
				$this->test_data['tickets_products_rsvp']
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_rsvp'] ),
		];
	}

	/**
	 * Get test matrix for Ticket mismatch.
	 */
	public function get_test_matrix_single_ticket_mismatch() {
		return [
			// Repository
			'default',
			// Filter name.
			'ticket',
			// Filter arguments to use.
			[
				$this->get_fake_ids( 0 ),
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for multiple Tickets mismatch.
	 */
	public function get_test_matrix_multi_ticket_mismatch() {
		return [
			// Repository
			'default',
			// Filter name.
			'ticket',
			// Filter arguments to use.
			[
				$this->get_fake_ids(),
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for Ticket Not In match.
	 */
	public function get_test_matrix_single_ticket_not_in_match() {
		$expected = array_merge(
			$this->test_data['attendees_rsvp'],
			$this->test_data['attendees_paypal_5']
		);

		return [
			// Repository
			'default',
			// Filter name.
			'ticket__not_in',
			// Filter arguments to use.
			[
				[
					$this->test_data['tickets_products_paypal'][0],
				]
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Get test matrix for multiple Tickets Not In match.
	 */
	public function get_test_matrix_multi_ticket_not_in_match() {
		$expected = array_merge(
			$this->test_data['attendees_rsvp'],
			$this->test_data['attendees_paypal']
		);

		return [
			// Repository
			'default',
			// Filter name.
			'ticket__not_in',
			// Filter arguments to use.
			[
				$this->get_fake_ids(),
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Get test matrix for Ticket Not In mismatch.
	 */
	public function get_test_matrix_single_ticket_not_in_mismatch() {
		$expected = array_merge(
			$this->test_data['attendees_rsvp'],
			$this->test_data['attendees_paypal']
		);

		return [
			// Repository
			'default',
			// Filter name.
			'ticket__not_in',
			// Filter arguments to use.
			[
				$this->get_fake_ids( 0 ),
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Get test matrix for multiple Tickets Not In mismatch.
	 */
	public function get_test_matrix_multi_ticket_not_in_mismatch() {
		$filter = array_merge(
			$this->test_data['tickets_products_rsvp'],
			$this->test_data['tickets_products_paypal']
		);

		return [
			// Repository
			'default',
			// Filter name.
			'ticket__not_in',
			// Filter arguments to use.
			[
				$filter,
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * ORDERS
	 */

	/**
	 * Get test matrix for Order match.
	 */
	public function get_test_matrix_single_order_match() {
		return [
			// Repository
			'default',
			// Filter name.
			'order',
			// Filter arguments to use.
			[
				[
					$this->test_data['tickets_orders_rsvp'][0]
				]
			],
			// Assertions to make.
			$this->get_assertions_array( (array) $this->test_data['attendees_rsvp'][0] ),
		];
	}

	/**
	 * Get test matrix for multiple Order match.
	 */
	public function get_test_matrix_multi_order_match() {
		return [
			// Repository
			'default',
			// Filter name.
			'order',
			// Filter arguments to use.
			[
				$this->test_data['tickets_orders_rsvp']
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_rsvp'] ),
		];
	}

	/**
	 * Get test matrix for Order mismatch.
	 */
	public function get_test_matrix_single_order_mismatch() {
		return [
			// Repository
			'default',
			// Filter name.
			'order',
			// Filter arguments to use.
			[
				$this->get_fake_ids( 0 ),
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for multiple Orders mismatch.
	 */
	public function get_test_matrix_multi_order_mismatch() {
		return [
			// Repository
			'default',
			// Filter name.
			'order',
			// Filter arguments to use.
			[
				$this->get_fake_ids(),
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for Order Not In match.
	 */
	public function get_test_matrix_single_order_not_in_match() {
		$expected = array_merge(
			$this->test_data['attendees_paypal'],
			$this->test_data['attendees_rsvp']
		);

		array_shift( $expected );

		return [
			// Repository
			'default',
			// Filter name.
			'order__not_in',
			// Filter arguments to use.
			[
				[
					$this->test_data['tickets_orders_paypal'][0],
				]
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Get test matrix for multiple Orders Not In match.
	 */
	public function get_test_matrix_multi_order_not_in_match() {
		$expected = array_merge(
			$this->test_data['attendees_rsvp'],
			$this->test_data['attendees_paypal']
		);

		return [
			// Repository
			'default',
			// Filter name.
			'order__not_in',
			// Filter arguments to use.
			[
				$this->get_fake_ids(),
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Get test matrix for Order Not In mismatch.
	 */
	public function get_test_matrix_single_order_not_in_mismatch() {
		$expected = array_merge(
			$this->test_data['attendees_paypal'],
			$this->test_data['attendees_rsvp']
		);

		return [
			// Repository
			'default',
			// Filter name.
			'order__not_in',
			// Filter arguments to use.
			[
				$this->get_fake_ids( 0 ),
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Get test matrix for multiple Orders Not In mismatch.
	 */
	public function get_test_matrix_multi_order_not_in_mismatch() {
		$filter = array_merge(
			$this->test_data['tickets_orders_rsvp'],
			$this->test_data['tickets_orders_paypal']
		);

		return [
			// Repository
			'default',
			// Filter name.
			'order__not_in',
			// Filter arguments to use.
			[
				$filter,
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * PURCHASER NAMES
	 */

	/**
	 * Get test matrix for Purchaser Name match.
	 */
	public function get_test_matrix_single_purchaser_name_match() {
		$expected = [
			$this->get_attendee_id( 0 ), // User2 on Event1
			$this->get_attendee_id( 8 ), // User2 on Event1
			$this->get_attendee_id( 9 ), // User2 on Event3
		];

		return [
			// Repository
			'default',
			// Filter name.
			'purchaser_name',
			// Filter arguments to use.
			[
				[
					$this->test_data['tickets_purchaser_names_rsvp'][0],
				]
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Get test matrix for multiple Purchaser Name match.
	 */
	public function get_test_matrix_multi_purchaser_name_match() {
		$expected = [
			$this->get_attendee_id( 0 ), // User2 on Event1
			$this->get_attendee_id( 5 ), // User4
			$this->get_attendee_id( 8 ), // User2 on Event1
			$this->get_attendee_id( 9 ), // User2 on Event3
		];

		return [
			// Repository
			'default',
			// Filter name.
			'purchaser_name',
			// Filter arguments to use.
			[
				[
					$this->test_data['user_2_details']['first_name']
					. ' '
					. $this->test_data['user_2_details']['last_name'],

					$this->test_data['user_4_details']['first_name']
					. ' '
					. $this->test_data['user_4_details']['last_name'],
				]
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Get test matrix for Purchaser Name mismatch.
	 */
	public function get_test_matrix_single_purchaser_name_mismatch() {
		return [
			// Repository
			'default',
			// Filter name.
			'purchaser_name',
			// Filter arguments to use.
			[
				$this->get_fake_names( 0 ),
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for multiple Purchaser Names mismatch.
	 */
	public function get_test_matrix_multi_purchaser_name_mismatch() {
		return [
			// Repository
			'default',
			// Filter name.
			'purchaser_name',
			// Filter arguments to use.
			[
				$this->get_fake_names(),
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for Purchaser Name Not In match.
	 */
	public function get_test_matrix_single_purchaser_name_not_in_match() {
		$expected = [
			$this->get_attendee_id( 1 ), // User3
			$this->get_attendee_id( 2 ), // Guest
			$this->get_attendee_id( 3 ), // Guest
			$this->get_attendee_id( 4 ), // User3
			$this->get_attendee_id( 5 ), // User4
			$this->get_attendee_id( 6 ), // Guest
			$this->get_attendee_id( 7 ), // Guest
		];

		return [
			// Repository
			'default',
			// Filter name.
			'purchaser_name__not_in',
			// Filter arguments to use.
			[
				[
					$this->test_data['user_2_details']['first_name']
					. ' '
					. $this->test_data['user_2_details']['last_name'],
				]
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Get test matrix for multiple Purchaser Names Not In match.
	 */
	public function get_test_matrix_multi_purchaser_name_not_in_match() {
		$expected = array_merge(
			$this->test_data['attendees_rsvp'],
			$this->test_data['attendees_paypal']
		);

		return [
			// Repository
			'default',
			// Filter name.
			'purchaser_name__not_in',
			// Filter arguments to use.
			[
				$this->get_fake_names(),
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Get test matrix for Purchaser Name Not In mismatch.
	 */
	public function get_test_matrix_single_purchaser_name_not_in_mismatch() {
		$expected = array_merge(
			$this->test_data['attendees_paypal'],
			$this->test_data['attendees_rsvp']
		);

		return [
			// Repository
			'default',
			// Filter name.
			'purchaser_name__not_in',
			// Filter arguments to use.
			[
				$this->get_fake_names( 0 ),
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Get test matrix for multiple Purchaser Names Not In mismatch.
	 */
	public function get_test_matrix_multi_purchaser_name_not_in_mismatch() {
		$filter = array_merge(
			$this->test_data['tickets_purchaser_names_rsvp'],
			$this->test_data['tickets_purchaser_names_paypal']
		);

		return [
			// Repository
			'default',
			// Filter name.
			'purchaser_name__not_in',
			// Filter arguments to use.
			[
				$filter,
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for Purchaser Name Like match.
	 */
	public function get_test_matrix_single_purchaser_name_like_match() {
		$expected = [
			$this->get_attendee_id( 0 ), // User2 on Event1
			$this->get_attendee_id( 8 ), // User2 on Event1
			$this->get_attendee_id( 9 ), // User2 on Event3
		];

		return [
			// Repository
			'default',
			// Filter name.
			'purchaser_name__like',
			// Filter arguments to use.
			[
				$this->test_data['user_2_details']['first_name'] . '%',
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Get test matrix for Purchaser Name Like mismatch.
	 */
	public function get_test_matrix_single_purchaser_name_like_mismatch() {
		$name = $this->get_fake_names( 0 );

		return [
			// Repository
			'default',
			// Filter name.
			'purchaser_name__like',
			// Filter arguments to use.
			[
				$name[0] . '%',
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * RSVPS
	 */

	/**
	 * Get test matrix for RSVP match.
	 */
	public function get_test_matrix_single_rsvp_match() {
		return [
			// Repository
			'rsvp',
			// Filter name.
			'ticket',
			// Filter arguments to use.
			[
				[
					$this->get_rsvp_ticket_id( 0 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_rsvp_1'] ),
		];
	}

	/**
	 * Get test matrix for multiple RSVPs match.
	 */
	public function get_test_matrix_multi_rsvp_match() {
		return [
			// Repository
			'rsvp',
			// Filter name.
			'ticket',
			// Filter arguments to use.
			[
				[
					$this->get_rsvp_ticket_id( 0 ),
					$this->get_rsvp_ticket_id( 4 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_rsvp'] ),
		];
	}

	/**
	 * Get test matrix for RSVP mismatch.
	 */
	public function get_test_matrix_single_rsvp_mismatch() {
		return [
			// Repository
			'rsvp',
			// Filter name.
			'ticket',
			// Filter arguments to use.
			[
				[
					$this->get_rsvp_ticket_id( 1 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for multiple RSVPs mismatch.
	 */
	public function get_test_matrix_multi_rsvp_mismatch() {
		return [
			// Repository
			'rsvp',
			// Filter name.
			'ticket',
			// Filter arguments to use.
			[
				[
					$this->get_rsvp_ticket_id( 1 ),
					$this->get_rsvp_ticket_id( 2 ),
					$this->get_rsvp_ticket_id( 3 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for RSVP Not In match.
	 */
	public function get_test_matrix_single_rsvp_not_in_match() {
		return [
			// Repository
			'rsvp',
			// Filter name.
			'ticket__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_rsvp_ticket_id( 0 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_event_3'] ),
		];
	}

	/**
	 * Get test matrix for multiple RSVPs Not In match.
	 */
	public function get_test_matrix_multi_rsvp_not_in_match() {
		return [
			// Repository
			'rsvp',
			// Filter name.
			'ticket__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_rsvp_ticket_id( 0 ),
					$this->get_rsvp_ticket_id( 4 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for RSVP Not In mismatch.
	 */
	public function get_test_matrix_single_rsvp_not_in_mismatch() {
		return [
			// Repository
			'rsvp',
			// Filter name.
			'ticket__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_rsvp_ticket_id( 0 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_event_3'] ),
		];
	}

	/**
	 * Get test matrix for multiple RSVPs Not In mismatch.
	 */
	public function get_test_matrix_multi_rsvp_not_in_mismatch() {
		return [
			// Repository
			'rsvp',
			// Filter name.
			'ticket__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_rsvp_ticket_id( 0 ),
					$this->get_rsvp_ticket_id( 4 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Tribe Commerce
	 */

	/**
	 * Get test matrix for Tribe Commerce PayPal match.
	 */
	public function get_test_matrix_single_paypal_match() {
		return [
			// Repository
			'tribe-commerce',
			// Filter name.
			'ticket',
			// Filter arguments to use.
			[
				[
					$this->get_paypal_tickets_id( 0 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_paypal_1'] ),
		];
	}

	/**
	 * Get test matrix for multiple Tribe Commerce PayPal match.
	 */
	public function get_test_matrix_multi_paypal_match() {
		return [
			// Repository
			'tribe-commerce',
			// Filter name.
			'ticket',
			// Filter arguments to use.
			[
				[
					$this->get_paypal_tickets_id( 0 ),
					$this->get_paypal_tickets_id( 4 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_paypal'] ),
		];
	}

	/**
	 * Get test matrix for Tribe Commerce PayPal mismatch.
	 */
	public function get_test_matrix_single_paypal_mismatch() {
		return [
			// Repository
			'tribe-commerce',
			// Filter name.
			'ticket',
			// Filter arguments to use.
			[
				[
					$this->get_paypal_tickets_id( 1 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for multiple Tribe Commerce PayPal mismatch.
	 */
	public function get_test_matrix_multi_paypal_mismatch() {
		return [
			// Repository
			'tribe-commerce',
			// Filter name.
			'ticket',
			// Filter arguments to use.
			[
				[
					$this->get_paypal_tickets_id( 1 ),
					$this->get_paypal_tickets_id( 2 ),
					$this->get_paypal_tickets_id( 3 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for Tribe Commerce PayPal Not In match.
	 */
	public function get_test_matrix_single_paypal_not_in_match() {
		return [
			// Repository
			'tribe-commerce',
			// Filter name.
			'ticket__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_paypal_tickets_id( 1 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_paypal'] ),
		];
	}

	/**
	 * Get test matrix for multiple Tribe Commerce PayPal Not In match.
	 */
	public function get_test_matrix_multi_paypal_not_in_match() {
		return [
			// Repository
			'tribe-commerce',
			// Filter name.
			'ticket__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_paypal_tickets_id( 1 ),
					$this->get_paypal_tickets_id( 2 ),
					$this->get_paypal_tickets_id( 3 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_paypal'] ),
		];
	}

	/**
	 * Get test matrix for Tribe Commerce PayPal Not In mismatch.
	 */
	public function get_test_matrix_single_paypal_not_in_mismatch() {
		return [
			// Repository
			'tribe-commerce',
			// Filter name.
			'ticket__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_paypal_tickets_id( 0 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_paypal_5'] ),
		];
	}

	/**
	 * Get test matrix for multiple Tribe Commerce PayPal Not In mismatch.
	 */
	public function get_test_matrix_multi_paypal_not_in_mismatch() {
		return [
			// Repository
			'tribe-commerce',
			// Filter name.
			'ticket__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_paypal_tickets_id( 1 ),
					$this->get_paypal_tickets_id( 2 ),
					$this->get_paypal_tickets_id( 3 ),
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_paypal'] ),
		];
	}

	/**
	 * USERS
	 */

	/**
	 * Get test matrix for User match. 2nd user is first attendee.
	 */
	public function get_test_matrix_single_user_match() {
		$expected = [
			$this->get_attendee_id( 0 ), // User2 on Event1
			$this->get_attendee_id( 8 ), // User2 on Event1
			$this->get_attendee_id( 9 ), // User2 on Event3
		];

		return [
			// Repository
			'default',
			// Filter name.
			'user',
			// Filter arguments to use.
			[
				[
					$this->get_user_id( 1 ), // User2
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Get test matrix for multiple User match.
	 *
	 * 2nd user is first attendee. 3rd user is 2nd and 3rd attendee. 4th user is 4th attendee.
	 */
	public function get_test_matrix_multi_user_match() {
		$expected = [
			$this->get_attendee_id( 0 ), // User2
			$this->get_attendee_id( 1 ), // User3
			$this->get_attendee_id( 4 ), // User3
			$this->get_attendee_id( 5 ), // User4
			$this->get_attendee_id( 8 ), // User2 on Event1
			$this->get_attendee_id( 9 ), // User2 on Event3
		];

		return [
			// Repository
			'default',
			// Filter name.
			'user',
			// Filter arguments to use.
			[
				[
					$this->get_user_id( 1 ), // User2
					$this->get_user_id( 2 ), // User3
					$this->get_user_id( 3 ), // User4
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Get test matrix for User mismatch.
	 */
	public function get_test_matrix_single_user_mismatch() {
		return [
			// Repository
			'default',
			// Filter name.
			'user',
			// Filter arguments to use.
			[
				[
					$this->get_user_id( 0 ), // User1
				],
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for multiple Users mismatch.
	 */
	public function get_test_matrix_multi_user_mismatch() {
		return [
			// Repository
			'default',
			// Filter name.
			'user',
			// Filter arguments to use.
			[
				[
					$this->get_user_id( 0 ), // User1
					$this->get_user_id( 4 ), // User5
				],
			],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for User Not In match.
	 */
	public function get_test_matrix_single_user_not_in_match() {
		return [
			// Repository
			'default',
			// Filter name.
			'user__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_user_id( 0 ), // User1
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_all'] ),
		];
	}

	/**
	 * Get test matrix for multiple Users Not In match.
	 */
	public function get_test_matrix_multi_user_not_in_match() {
		return [
			// Repository
			'default',
			// Filter name.
			'user__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_user_id( 0 ), // User1
					$this->get_user_id( 4 ), // User5
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_all'] ),
		];
	}

	/**
	 * Get test matrix for User Not In mismatch.
	 *
	 * Get all the attendees that weren't purchased by User2.
	 */
	public function get_test_matrix_single_user_not_in_mismatch() {
		$expected = [
			$this->get_attendee_id( 1 ), // User3
			$this->get_attendee_id( 2 ), // Guest
			$this->get_attendee_id( 3 ), // Guest
			$this->get_attendee_id( 4 ), // User3
			$this->get_attendee_id( 5 ), // User4
			$this->get_attendee_id( 6 ), // Guest
			$this->get_attendee_id( 7 ), // Guest
		];

		return [
			// Repository
			'default',
			// Filter name.
			'user__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_user_id( 1 ), // User2
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Get test matrix for multiple Users Not In mismatch.
	 *
	 * Get all the attendees that weren't purchased by User2 nor User4.
	 */
	public function get_test_matrix_multi_user_not_in_mismatch() {
		$expected = [
			$this->get_attendee_id( 1 ), // User3
			$this->get_attendee_id( 2 ), // Guest
			$this->get_attendee_id( 3 ), // Guest
			$this->get_attendee_id( 4 ), // User3
			$this->get_attendee_id( 6 ), // Guest
			$this->get_attendee_id( 7 ), // Guest
		];

		return [
			// Repository
			'default',
			// Filter name.
			'user__not_in',
			// Filter arguments to use.
			[
				[
					$this->get_user_id( 1 ), // User2
					$this->get_user_id( 3 ), // User4
				],
			],
			// Assertions to make.
			$this->get_assertions_array( $expected ),
		];
	}

	/**
	 * Price Paid
	 */

	/**
	 * Get test matrix for Price Paid match.
	 */
	public function get_test_matrix_single_price_match() {
		return [
			// Repository
			'tribe-commerce',
			// Filter name.
			'price',
			// Filter arguments to use.
			[ 5 ],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_paypal_1'] ),
		];
	}

	/**
	 * Get test matrix for Price Paid match.
	 */
	public function get_test_matrix_single_price_mismatch() {
		return [
			// Repository
			'tribe-commerce',
			// Filter name.
			'price',
			// Filter arguments to use.
			[ 15 ],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for minimum Price Paid match.
	 */
	public function get_test_matrix_single_price_min_match() {
		return [
			// Repository
			'tribe-commerce',
			// Filter name.
			'price_min',
			// Filter arguments to use.
			[ 6 ],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_paypal_5'] ),
		];
	}

	/**
	 * Get test matrix for minimum Price Paid mismatch.
	 */
	public function get_test_matrix_single_price_min_mismatch() {
		return [
			// Repository
			'tribe-commerce',
			// Filter name.
			'price_min',
			// Filter arguments to use.
			[ 15 ],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Get test matrix for maximum Price Paid match.
	 */
	public function get_test_matrix_single_price_max_match() {
		return [
			// Repository
			'tribe-commerce',
			// Filter name.
			'price_max',
			// Filter arguments to use.
			[ 6 ],
			// Assertions to make.
			$this->get_assertions_array( $this->test_data['attendees_paypal_1'] ),
		];
	}

	/**
	 * Get test matrix for maximum Price Paid mismatch.
	 */
	public function get_test_matrix_single_price_max_mismatch() {
		return [
			// Repository
			'tribe-commerce',
			// Filter name.
			'price_max',
			// Filter arguments to use.
			[ 2 ],
			// Assertions to make.
			$this->get_assertions_array( [] ),
		];
	}

	/**
	 * Helpers
	 */

	/**
	 * Given an array index key, get its value from the array of Events.
	 *
	 * @param int $index
	 *
	 * @return int
	 */
	protected function get_event_id( $index ) {
		if ( isset( $this->test_data['events'][ $index ] ) ) {
			return $this->test_data['events'][ $index ];
		}

		return 0;
	}

	/**
	 * Given an array index key, get its value from the array of Attendees.
	 *
	 * @param int $index
	 *
	 * @return int
	 */
	protected function get_attendee_id( $index ) {
		if ( isset( $this->test_data['attendees_all'][ $index ] ) ) {
			return $this->test_data['attendees_all'][ $index ];
		}

		return 0;
	}

	/**
	 * Given an array index key, get its value from the array of Users.
	 *
	 * @param int $index
	 *
	 * @return int
	 */
	protected function get_user_id( $index ) {
		if ( isset( $this->test_data['users'][ $index ] ) ) {
			return $this->test_data['users'][ $index ];
		}

		return 0;
	}

	/**
	 * Given an array index key, get its value from the array of RSVP Tickets.
	 *
	 * @param int $index
	 *
	 * @return int
	 */
	protected function get_rsvp_ticket_id( $index ) {
		if ( isset( $this->test_data['rsvp_tickets'][ $index ] ) ) {
			return $this->test_data['rsvp_tickets'][ $index ];
		}

		return 0;
	}

	/**
	 * Given an array index key, get its value from the array of PayPal Tickets.
	 *
	 * @param int $index
	 *
	 * @return int
	 */
	protected function get_paypal_tickets_id( $index ) {
		if ( isset( $this->test_data['paypal_tickets'][ $index ] ) ) {
			return $this->test_data['paypal_tickets'][ $index ];
		}

		return 0;
	}

	/**
	 * Setup list of test data.
	 *
	 * We create 4 events, 1st and 3rd having same author, and various types of tickets that have attendees,
	 * and 2nd and 4th having neither an author nor tickets (and therefore no attendees).
	 * Some ticket purchases are by valid users and others are by non-users (site guests as attendees).
	 * Event 1 has:
	 * - User1 is author
	 * - User2 is RSVP attendee
	 * - User3 is RSVP attendee and PayPal attendee
	 * - User4 is PayPal attendee
	 * - So 1 RSVP ticket having 4 attendees (2 guests) and 1 PayPal ticket having 4 attendees (2 guests)
	 *   for a total of 8 attendees
	 * - And 3 RSVP tickets and 3 PayPal tickets, each having zero attendees
	 * Event 2 has: no author, no tickets (therefore no attendees)
	 * Event 3 has:
	 * - User1 is author
	 * - User2 is RSVP attendee
	 * Event 4 has: User5 as author, no tickets (therefore no attendees)
	 * Note that guest purchasers will still have User ID# zero saved to `_tribe_tickets_attendee_user_id` meta field.
	 */
	protected function setup_test_data() {
		/** @var \Tribe__Tickets__RSVP $rsvp */
		$rsvp = tribe( 'tickets.rsvp' );

		/** @var \Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );

		$test_data = [
			// 5 total: 1&5 = Event author, not Attendee; 2 = only RSVP attendee; 3 = RSVP & PayPal attendee; 4 = only PayPal attendee
			'events'                         => [],
			// '_tribe_rsvp_product' values
			'tickets_products_rsvp'          => [],
			// '_tribe_tpp_product' values
			'tickets_products_paypal'        => [],
			// '_tribe_rsvp_order' values
			'tickets_orders_rsvp'            => [],
			// '_tribe_tpp_order' values
			'tickets_orders_paypal'          => [],
			// '_tribe_rsvp_full_name' values
			'tickets_purchaser_names_rsvp'   => [],
			// '_tribe_tpp_full_name' values
			'tickets_purchaser_names_paypal' => [],
			// 4 total: 1&3 = Event author, 2&4 = Attendees
			'users'                          => [],
			'user_2_details'                 => [
				'first_name' => 'Female',
				'last_name'  => 'Blue',
				'email'      => 'user2@tri.be',
			],
			'user_4_details'                 => [
				'first_name' => 'Male',
				'last_name'  => 'Brown',
				'email'      => 'user4@tri.be',
			],
			// 4 total: 1&3 = has Author, Tickets, and Attendees; 2&4 = Author ID of zero and no Tickets (so no Attendees)
			'rsvp_tickets'                   => [],
			// 4 total: 1 = 4 Attendees (users 2 & 3 + 2 guests); 2, 3, & 4 = no Attendees
			'paypal_tickets'                 => [],
			// 4 total: 1 = 4 Attendees (users 3 & 4 + 2 guests); 2, 3, & 4 = no Attendees
			'attendees_all'                  => [],
			// 9 total (5 by logged in): 1 & 2 = RSVP by logged in; 3 & 4 = RSVP by logged out; 5 & 6 = PayPal by logged in; 7 & 8: PayPal by logged out; 9 by User2 on Event3
			'attendees_event_1'              => [], // Event1's
			'attendees_event_3'              => [], // Event3's
			'attendees_rsvp'                 => [], // All RSVP Ticket attendees
			'attendees_rsvp_1'               => [], // All RSVP Ticket ID 1's attendees: 1,2,3,4
			'attendees_rsvp_5'               => [], // All RSVP Ticket ID 5's attendees: 10
			'attendees_paypal'               => [], // All PayPal Ticket attendees
			'attendees_paypal_1'             => [], // All PayPal Ticket ID 1's attendees: 5,6,7,8
			'attendees_paypal_5'             => [], // All PayPal Ticket ID 5's attendees: 9
		];

		// Create User1, author of Event1.
		$test_data['users'][] = $user_id_one = $this->factory()->user->create( [ 'role' => 'author' ] );

		// Create test users 2, 3, and 4 as Attendees
		$test_data['users'][] = $user_id_two = $this->factory()->user->create( $test_data['user_2_details'] );
		$test_data['users'][] = $user_id_three = $this->factory()->user->create();
		$test_data['users'][] = $user_id_four = $this->factory()->user->create( $test_data['user_4_details'] );

		//
		// Event1: RSVP and PayPal by User2, User3, and guests
		//
		$test_data['events'][] =
		$event_id_one = $this->factory()->event->create( [
			'post_title'  => 'Test event 1',
			'post_author' => $user_id_one,
		] );

		// Create RSVP1 ticket on Event1
		$rsvp_id_one = $this->create_rsvp_ticket( $event_id_one );

		// Add User2 (Attendee1) and User3 (Attendee2) as RSVP1 attendees on Event1
		$test_data['attendees_event_1'][] =
		$test_data['attendees_rsvp'][] =
		$test_data['attendees_rsvp_1'][] =
		$attendee_id_1 =
			$this->create_attendee_for_ticket( $rsvp_id_one, $event_id_one, [ 'user_id' => $user_id_two ] );

		$test_data['attendees_event_1'][] =
		$test_data['attendees_rsvp'][] =
		$test_data['attendees_rsvp_1'][] =
		$attendee_id_2 =
			$this->create_attendee_for_ticket( $rsvp_id_one, $event_id_one, [ 'user_id' => $user_id_three ] );

		// Add 2 guest purchasers (Attendees 3 & 4) to RSVP1 Ticket already having other Attendees
		$test_data['attendees_event_1'][] =
		$test_data['attendees_rsvp'][] =
		$test_data['attendees_rsvp_1'][] =
		$attendee_id_3 =
			$this->create_attendee_for_ticket( $rsvp_id_one, $event_id_one );

		$test_data['attendees_event_1'][] =
		$test_data['attendees_rsvp'][] =
		$test_data['attendees_rsvp_1'][] =
		$attendee_id_4 =
			$this->create_attendee_for_ticket( $rsvp_id_one, $event_id_one );

		// Create 3 more RSVP tickets that will never have any attendees
		$test_data['rsvp_tickets'] = array_merge( [ $rsvp_id_one ], $this->create_many_rsvp_tickets( 3, $event_id_one ) );

		// Create test PayPal1 ticket
		$paypal_id_one = $this->create_paypal_ticket_basic( $event_id_one, 5 );

		// Add User3 (Attendee5) and User4 (Attendee6) as Tribe Commerce PayPal Ticket attendees

		$test_data['attendees_event_1'][] =
		$test_data['attendees_paypal'][] =
		$test_data['attendees_paypal_1'][] =
		$attendee_id_5 =
			$this->create_attendee_for_ticket( $paypal_id_one, $event_id_one, [ 'user_id' => $user_id_three ] );

		$test_data['attendees_event_1'][] =
		$test_data['attendees_paypal'][] =
		$test_data['attendees_paypal_1'][] =
		$attendee_id_6 =
			$this->create_attendee_for_ticket( $paypal_id_one, $event_id_one, [ 'user_id' => $user_id_four ] );

		// Add 2 guest purchasers (Attendees 7 & 8) to the PayPal Ticket already having other Attendees

		$test_data['attendees_event_1'][] =
		$test_data['attendees_paypal'][] =
		$test_data['attendees_paypal_1'][] =
		$attendee_id_7 =
			$this->create_attendee_for_ticket( $paypal_id_one, $event_id_one );

		$test_data['attendees_event_1'][] =
		$test_data['attendees_paypal'][] =
		$test_data['attendees_paypal_1'][] =
		$attendee_id_8 =
			$this->create_attendee_for_ticket( $paypal_id_one, $event_id_one );

		// Create 3 more PayPal tickets that will never have any attendees
		$test_data['paypal_tickets'] = array_merge( [ $paypal_id_one ], $this->create_many_paypal_tickets_basic( 3, $event_id_one ) );

		// Create test PayPal5 ticket
		$test_data['paypal_tickets'][] =
		$paypal_id_five =
			$this->create_paypal_ticket_basic( $event_id_one, 12 );

		// Add User2 (Attendee9) as Tribe Commerce PayPal Ticket attendee
		$test_data['attendees_event_1'][] =
		$test_data['attendees_paypal'][] =
		$test_data['attendees_paypal_5'][] =
		$attendee_id_9 =
			$this->create_attendee_for_ticket( $paypal_id_five, $event_id_one, [ 'user_id' => $user_id_two ] );

		//
		// Event2: No author nor tickets (and therefore no attendees)
		//
		$test_data['events'][] =
		$event_id_two = $this->factory()->event->create( [
			'post_title'  => 'Test event 2',
			'post_author' => 0,
		] );

		//
		// Event3: User2 as Attendee10 (RSVP5) for User2
		//

		// Create Event3, having tickets
		$test_data['events'][] =
		$event_id_three = $this->factory()->event->create( [
			'post_title'  => 'Test event 3',
			'post_author' => $user_id_one,
		] );

		$test_data['rsvp_tickets'][] =
		$rsvp_id_five =
			$this->create_rsvp_ticket( $event_id_three );

		// Add User2 (Attendee10) on RSVP5 ticket on Event3
		$test_data['attendees_event_3'][] =
		$test_data['attendees_rsvp'][] =
		$test_data['attendees_rsvp_5'][] =
		$attendee_id_10 =
			$this->create_attendee_for_ticket( $rsvp_id_five, $event_id_three, [ 'user_id' => $user_id_two ] );

		// Create User5, author of Event3.
		$test_data['users'][] = $user_id_five = $this->factory()->user->create( [ 'role' => 'author' ] );

		//
		// Event4: No author nor tickets (and therefore no attendees)
		//
		$test_data['events'][] = $this->factory()->event->create( [
			'post_title'  => 'Test event 4',
			'post_author' => $user_id_five,
		] );

		// Merge all attendees
		$test_data['attendees_all'] = array_unique(
			array_merge(
				$test_data['attendees_event_1'], // Event1
				$test_data['attendees_event_3'] // Event3
			)
		);

		// Get the post_meta so we can filter by it
		foreach ( $test_data['attendees_all'] as $attendee_id ) {
			$meta = get_post_meta( $attendee_id );

			foreach ( $meta as $k => $v ) {
				// Tickets
				if (
					$rsvp::ATTENDEE_PRODUCT_KEY === $k
					&& ! empty( $meta[ $k ][0] )
				) {
					$test_data['tickets_products_rsvp'][] = $meta[ $k ][0];
				} elseif (
					$paypal::ATTENDEE_PRODUCT_KEY === $k
					&& ! empty( $meta[ $k ][0] )
				) {
					$test_data['tickets_products_paypal'][] = $meta[ $k ][0];
				} // Orders
				elseif (
					$rsvp->order_key === $k
					&& ! empty( $meta[ $k ][0] )
				) {
					$test_data['tickets_orders_rsvp'][] = $meta[ $k ][0];
				} elseif (
					$paypal::ATTENDEE_ORDER_KEY === $k
					&& ! empty( $meta[ $k ][0] )
				) {
					$test_data['tickets_orders_paypal'][] = $meta[ $k ][0];
				} // Purchaser Names
				elseif (
					$rsvp->full_name === $k
					&& ! empty( $meta[ $k ][0] )
				) {
					$test_data['tickets_purchaser_names_rsvp'][] = $meta[ $k ][0];
				} elseif (
					$paypal->full_name === $k
					&& ! empty( $meta[ $k ][0] )
				) {
					$test_data['tickets_purchaser_names_paypal'][] = $meta[ $k ][0];
				}
			}
		}

		// Save test data to class property after running each through array_unique()
		foreach ( $test_data as $key => $value ) {
			$this->test_data[ $key ] = array_unique( (array) $value );
		}

		// Debugging (only works for failing tests)
		$debug = $this->get_attendee_data( 807 );

		global $wpdb;
		$all_metas = $wpdb->get_col(
			$wpdb->prepare(
				"
			SELECT pm.meta_value FROM {$wpdb->postmeta} pm
			LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			WHERE pm.meta_key = %s 
			AND p.post_type = %s
			",
				'_tribe_rsvp_full_name',
				'tribe_rsvp_attendees'
			)
		);

		if ( ! empty( $debug ) ) {
			codecept_debug( $debug );
		}
	}

	/**
	 * Given an Attendee ID, such as from Codeception saying one was missing, get the info about that Attendee to help
	 * point you in the right direction to get the test running correctly.
	 *
	 * @param int $id
	 *
	 * @return array|false
	 */
	protected function get_attendee_data( int $id = 0 ) {
		$result = [];

		$post = get_post( $id, ARRAY_A );

		if (
			empty( $id )
			|| empty( $post )
		) {
			return false;
		}

		$result['post'] = $post;
		$result['meta'] = get_post_meta( $id );

		return $result;
	}

	/**
	 * Get an array of IDs that would never match for any Attendees.
	 *
	 * @param int $key Optionally get just 1 value from the array (still returns an array).
	 *
	 * @return array
	 */
	protected function get_fake_ids( int $key = -1 ) {
		$array = [
			-1,
			888888,
			999999,
			PHP_INT_MAX,
		];

		shuffle( $array );

		if ( array_key_exists( $key, $array ) ) {
			return (array) $array[ $key ];
		}

		return $array;
	}

	/**
	 * Get an array of names that would never match for any Attendees.
	 *
	 * @param int $key Optionally get just 1 value from the array (still returns an array).
	 *
	 * @return array
	 */
	protected function get_fake_names( int $key = - 1 ) {
		$array = [
			'aaaaaaaaa',
			'bbbbbbbbb',
			'CCCCCCCCC',
			'DDDDDDDDD',
		];

		shuffle( $array );

		if ( array_key_exists( $key, $array ) ) {
			return (array) $array[ $key ];
		}

		return $array;
	}

	/**
	 * Given an array of post IDs, get the assertions array that flows through to the test.
	 *
	 * @param int|array $post_ids
	 *
	 * @return array
	 */
	protected function get_assertions_array( $post_ids ) {
		if ( ! is_array( $post_ids ) ) {
			$post_ids = (array) $post_ids;
		}

		// ORM will return sorted results, but we may not enter them that way
		sort( $post_ids );

		// Assume 'count' and 'found' will always be the same, since ORM defaults to unlimited (-1) results.
		$total = count( $post_ids );

		return [
			'get_ids' => $post_ids,
			'all'     => array_map( 'get_post', $post_ids ),
			'count'   => $total,
			'found'   => $total,
		];
	}
}