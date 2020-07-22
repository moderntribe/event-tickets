<?php

namespace Tribe\Tickets\Partials\V2\RSVP\Messages;

use Codeception\TestCase\WPTestCase;
use Spatie\Snapshots\MatchesSnapshots;
use tad\WP\Snapshots\WPHtmlOutputDriver;

class SuccessTest extends WPTestCase {

	use MatchesSnapshots;

	protected $partial_path = 'v2/rsvp/messages/success';

	/**
	 * @test
	 */
	public function test_should_render_success_message() {
		$template     = tribe( 'tickets.editor.template' );
		$_GET['step'] = 'success';

		$html   = $template->template( $this->partial_path, [], false );
		$driver = new WPHtmlOutputDriver( home_url(), 'http://test.tribe.dev' );

		$this->assertMatchesSnapshot( $html, $driver );
	}

	/**
	 * @test
	 */
	public function test_should_render_empty_if_not_param_set() {
		$template = tribe( 'tickets.editor.template' );

		$html   = $template->template( $this->partial_path, [], false );
		$driver = new WPHtmlOutputDriver( home_url(), 'http://test.tribe.dev' );

		$this->assertMatchesSnapshot( $html, $driver );
	}
}
