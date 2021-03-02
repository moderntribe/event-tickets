<?php

namespace Tribe\Tickets\Partials\RSVP;

use Codeception\TestCase\WPTestCase;
use Spatie\Snapshots\MatchesSnapshots;
use tad\WP\Snapshots\WPHtmlOutputDriver;
use Tribe\Test\PHPUnit\Traits\With_Post_Remapping;
use Tribe\Tickets\Test\Commerce\Attendee_Maker as Attendee_Maker;
use Tribe\Tickets\Test\Commerce\RSVP\Ticket_Maker as RSVP_Ticket_Maker;

class Content extends WPTestCase {

	use MatchesSnapshots;
	use With_Post_Remapping;

	use RSVP_Ticket_Maker;
	use Attendee_Maker;

	protected $partial_path = 'blocks/rsvp/content';

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();
		$_GET['going'] = 'yes';
	}

	/**
	 * @test
	 */
	public function test_should_render_content() {
		$template  = tribe( 'tickets.editor.template' );
		$event     = $this->get_mock_event( 'events/single/1.json' );
		$ticket_id = $this->create_rsvp_ticket( $event->ID );

		/** @var \Tribe__Tickets__RSVP $rsvp */
		$rsvp = tribe( 'tickets.rsvp' );

		$ticket = $rsvp->get_ticket( $event->ID, $ticket_id );

		$args = [
			'ticket'     => $ticket,
			'post_id'    => $event->ID,
			'must_login' => false,
			'going'      => tribe_get_request_var( 'going', '' ),
		];

		$html   = $template->template( $this->partial_path, $args, false );
		$driver = new WPHtmlOutputDriver( home_url(), TRIBE_TESTS_HOME_URL );

		$driver->setTolerableDifferences( [ $ticket_id, $event->ID ] );
		$driver->setTolerableDifferencesPrefixes(
			[
				'quantity_',
			]
		);

		$driver->setTimeDependentAttributes(
			[
				'data-rsvp-id',
				'data-product-id',
			]
		);

		// Remove pesky SVG.
		$html = preg_replace( '/<svg.*<\/svg>/Ums', '', $html );

		$this->assertMatchesSnapshot( $html, $driver );
	}

	/**
	 * @test
	 */
	public function test_should_render_out_of_stock() {
		$template  = tribe( 'tickets.editor.template' );
		$event     = $this->get_mock_event( 'events/single/1.json' );
		$ticket_id = $this->create_rsvp_ticket(
			$event->ID, [
				'meta_input' => [
					'_capacity' => 3,
				],
			]
		);

		$this->create_many_attendees_for_ticket( 5, $ticket_id, $event->ID );

		/** @var \Tribe__Tickets__RSVP $rsvp */
		$rsvp = tribe( 'tickets.rsvp' );

		$ticket = $rsvp->get_ticket( $event->ID, $ticket_id );

		$args = [
			'ticket'  => $ticket,
			'post_id' => $event->ID,
		];

		$html   = $template->template( $this->partial_path, $args, false );
		$driver = new WPHtmlOutputDriver( home_url(), TRIBE_TESTS_HOME_URL );

		$driver->setTolerableDifferences( [ $ticket_id, $event->ID ] );
		$driver->setTolerableDifferencesPrefixes(
			[
				'quantity_',
			]
		);

		$driver->setTimeDependentAttributes(
			[
				'data-rsvp-id',
				'data-product-id',
			]
		);

		// Remove pesky SVG.
		$html = preg_replace( '/<svg.*<\/svg>/Ums', '', $html );

		$this->assertMatchesSnapshot( $html, $driver );
	}

	/**
	 * @test
	 */
	public function test_should_render_unlimited() {
		$template = tribe( 'tickets.editor.template' );

		$event = $this->get_mock_event( 'events/single/1.json' );

		$ticket_id = $this->create_rsvp_ticket(
			$event->ID, [
				'meta_input' => [
					'_capacity' => - 1,
				],
			]
		);

		/** @var \Tribe__Tickets__RSVP $rsvp */
		$rsvp = tribe( 'tickets.rsvp' );

		$ticket = $rsvp->get_ticket( $event->ID, $ticket_id );
		add_filter( 'tribe_rsvp_block_show_unlimited_availability', '__return_true' );

		$args = [
			'ticket'     => $ticket,
			'post_id'    => $event->ID,
			'must_login' => false,
			'going'      => tribe_get_request_var( 'going', '' ),
		];

		$html = $template->template( $this->partial_path, $args, false );

		$driver = new WPHtmlOutputDriver( home_url(), TRIBE_TESTS_HOME_URL );

		$driver->setTolerableDifferences( [ $ticket_id, $event->ID ] );
		$driver->setTolerableDifferencesPrefixes(
			[
				'quantity_',
			]
		);

		$driver->setTimeDependentAttributes(
			[
				'data-rsvp-id',
				'data-product-id',
			]
		);

		// Remove pesky SVG.
		$html = preg_replace( '/<svg.*<\/svg>/Ums', '', $html );

		$this->assertMatchesSnapshot( $html, $driver );
	}
}
