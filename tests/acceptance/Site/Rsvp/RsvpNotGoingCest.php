<?php
namespace Site\Rsvp;

use AcceptanceTester;

class RsvpNotGoingCest {

	public function _before( AcceptanceTester $I ) {

		// Log in as an admin.
		$I->loginAsAdmin();

		// Activate required plugins.
		$I->amOnAdminPage( '/plugins.php' );
		$I->activatePlugin(
			[
				'the-events-calendar',
				'event-tickets',
			]
		);

		// Set site options.
		$I->haveOptionInDatabase( 'permalink_structure', '/%postname%/' );
		$I->haveOptionInDatabase( 'showEventsInMainLoop', 'yes' );

		// Set tribe options.
		$I->setTribeOption( 'toggle_blocks_editor', 1 );
		$I->setTribeOption( 'tickets_rsvp_use_new_views', 1 );
	}

	/*
	 * @test
	 */
	public function should_confirm_rsvp_not_going_flow( AcceptanceTester $I ) {
		// Go to the Event page.
		$I->amOnPage( '/event/rsvp-test/' );

		// Confirm that we see the RSVP for the Event.
		$I->waitForText( 'Job & Career Fair' );
		$I->seeElement( ".tribe-tickets__rsvp-wrapper" );

		// Click on "Not Going".
		$I->click( ".tribe-tickets__rsvp-actions-button-not-going" );

		// Confirm that we see the "Not going Form".
		$I->waitForText( 'Please submit your information even if you are unable to attend.' );
		$I->seeElement( "form[name='tribe-tickets-rsvp-form']" );

		// Let's check if the validation works.
		$I->clearField( '.tribe-tickets__rsvp-form-field-name' );

		// Try to submit the form.
		$I->click( "form[name='tribe-tickets-rsvp-form'] button[type='submit']" );

		// Not seeing the loader will mean that it didn't submit.
		$I->dontSeeElement( '.tribe-common-c-loader' );

		// Re-fill the RSVP name field.
		$I->fillField( '.tribe-tickets__rsvp-form-field-name', 'Juan Doe' );

		// Submit the form.
		$I->click( "form[name='tribe-tickets-rsvp-form'] button[type='submit']" );

		// Check that the RSVP was confirmed.
		$I->waitForText( 'Thank you for confirming' );
		$I->seeElement( '.tribe-tickets__rsvp-message--success' );

		// Reload the event.
		$I->reloadPage();

		// Check that the view RSVP links is there on reload.
		$I->waitForText( 'You have 1 RSVP for this Event' );
		$I->seeElement( '.tribe-link-view-attendee' );

	}
}
