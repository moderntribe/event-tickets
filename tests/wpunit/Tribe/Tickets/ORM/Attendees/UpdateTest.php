<?php

namespace Tribe\Tickets\ORM\Attendees;

use Tribe\Tickets\Test\Commerce\RSVP\Ticket_Maker as RSVP_Ticket_Maker;
use Tribe\Tickets\Test\Commerce\PayPal\Ticket_Maker as PayPal_Ticket_Maker;
use Tribe\Tickets\Test\Commerce\Attendee_Maker;
use Tribe__Tickets__Attendee_Repository as Attendee_Repository;
use Tribe__Tickets__Data_API as Data_API;

/**
 * Class UpdateTest
 *
 * @package Tribe\Tickets\ORM\Attendees
 * @group orm-create-update
 */
class UpdateTest extends \Codeception\TestCase\WPTestCase {

	use RSVP_Ticket_Maker;
	use PayPal_Ticket_Maker;
	use Attendee_Maker;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		// Enable post as ticket type.
		add_filter( 'tribe_tickets_post_types', function () {
			return [ 'post' ];
		} );

		// Enable Tribe Commerce.
		add_filter( 'tribe_tickets_commerce_paypal_is_active', '__return_true' );
		add_filter( 'tribe_tickets_get_modules', function ( $modules ) {
			$modules['Tribe__Tickets__Commerce__PayPal__Main'] = tribe( 'tickets.commerce.paypal' )->plugin_name;

			return $modules;
		} );

		// Reset Data_API object so it sees Tribe Commerce.
		tribe_singleton( 'tickets.data_api', new Data_API );
	}

	/**
	 * It should allow updating ticket attendees.
	 *
	 * @test
	 */
	public function should_allow_updating_attendees() {
		/** @var Attendee_Repository $attendees */
		$attendees = tribe_attendees();

		$post_id = $this->factory->post->create();

		$paypal_ticket_id = $this->create_paypal_ticket_basic( $post_id, 1 );
		$rsvp_ticket_id   = $this->create_rsvp_ticket( $post_id );

		$paypal_attendee_ids = $this->create_many_attendees_for_ticket( 5, $paypal_ticket_id, $post_id );
		$rsvp_attendee_ids   = $this->create_many_attendees_for_ticket( 5, $rsvp_ticket_id, $post_id );

		$saved_ids = $attendees->set_args( [ 'post_content' => 'Cool.' ] )->save();

		$this->assertEqualSets( array_merge( $paypal_attendee_ids, $rsvp_attendee_ids ), $saved_ids );

		$saved_content = wp_list_pluck( $attendees->all(), 'post_content' );

		$this->assertEqualSets( array_fill( 0, count( $saved_ids ), 'Cool.' ), $saved_content );
	}

	/**
	 * It should allow updating ticket attendees from the rsvp context.
	 *
	 * @test
	 */
	public function should_allow_updating_attendees_from_rsvp_context() {
		/** @var Attendee_Repository $attendees */
		$attendees = tribe_attendees( 'rsvp' );

		$post_id = $this->factory->post->create();

		$paypal_ticket_id = $this->create_paypal_ticket_basic( $post_id, 1 );
		$rsvp_ticket_id   = $this->create_rsvp_ticket( $post_id );

		$paypal_attendee_ids = $this->create_many_attendees_for_ticket( 5, $paypal_ticket_id, $post_id );
		$rsvp_attendee_ids   = $this->create_many_attendees_for_ticket( 5, $rsvp_ticket_id, $post_id );

		$saved_ids = $attendees->set_args( [ 'post_content' => 'Cool.' ] )->save();

		$this->assertEqualSets( $rsvp_attendee_ids, $saved_ids );

		$saved_content = wp_list_pluck( $attendees->all(), 'post_content' );

		$this->assertEqualSets( array_fill( 0, count( $saved_ids ), 'Cool.' ), $saved_content );
	}

	/**
	 * It should allow updating ticket attendees from the tribe-commerce context.
	 *
	 * @test
	 */
	public function should_allow_updating_attendees_from_tribe_commerce_context() {
		/** @var Attendee_Repository $attendees */
		$attendees = tribe_attendees( 'tribe-commerce' );

		$post_id = $this->factory->post->create();

		$paypal_ticket_id = $this->create_paypal_ticket_basic( $post_id, 1 );
		$rsvp_ticket_id   = $this->create_rsvp_ticket( $post_id );

		$paypal_attendee_ids = $this->create_many_attendees_for_ticket( 5, $paypal_ticket_id, $post_id );
		$rsvp_attendee_ids   = $this->create_many_attendees_for_ticket( 5, $rsvp_ticket_id, $post_id );

		$saved_ids = $attendees->set_args( [ 'post_content' => 'Cool.' ] )->save();

		$this->assertEqualSets( $paypal_attendee_ids, $saved_ids );

		$saved_content = wp_list_pluck( $attendees->all(), 'post_content' );

		$this->assertEqualSets( array_fill( 0, count( $saved_ids ), 'Cool.' ), $saved_content );
	}

}
