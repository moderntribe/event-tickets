<?php

namespace Tribe\Tickets\Partials\RSVP;

use Codeception\TestCase\WPTestCase;
use Spatie\Snapshots\MatchesSnapshots;
use tad\WP\Snapshots\WPHtmlOutputDriver;
use Tribe\Test\PHPUnit\Traits\With_Post_Remapping;
use Tribe\Tickets\Test\Commerce\Attendee_Maker as Attendee_Maker;
use Tribe\Tickets\Test\Commerce\RSVP\Ticket_Maker as RSVP_Ticket_Maker;

class Form extends WPTestCase {

	use MatchesSnapshots;
	use With_Post_Remapping;

	use RSVP_Ticket_Maker;
	use Attendee_Maker;

	protected $partial_path = 'blocks/rsvp/form';

	/**
	 * @test
	 */
	public function test_should_render_form() {
		/** @var \Tribe__Tickets__Editor__Template $template */
		$template = tribe( 'tickets.editor.template' );

		/** @var \Tribe__Tickets__RSVP $rsvp_instance */
		$rsvp_instance = tribe( 'tickets.rsvp' );

		$event     = $this->get_mock_event( 'events/single/1.json' );
		$ticket_id = $this->create_rsvp_ticket( $event->ID );

		$ticket = $rsvp_instance->get_ticket( $event->ID, $ticket_id );

		$args = [
			'ticket'  => $ticket,
			'post_id' => $event->ID,
			'going'   => true,
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
