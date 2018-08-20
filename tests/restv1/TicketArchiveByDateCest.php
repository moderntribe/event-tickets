<?php

namespace Tribe\Tickets\Test\REST\V1;

use Restv1Tester;
use Tribe\Tickets\Test\Commerce\Attendee_Maker;
use Tribe\Tickets\Test\Commerce\PayPal\Ticket_Maker as PayPal_Ticket_Maker;
use Tribe\Tickets\Test\Commerce\RSVP\Ticket_Maker as RSVP_Ticket_Maker;

class TicketArchiveByDateCest extends BaseRestCest {
	use RSVP_Ticket_Maker;
	use PayPal_Ticket_Maker;
	use Attendee_Maker;

	/**
	 * It should allow getting tickets by date
	 *
	 * @test
	 */
	public function should_allow_getting_tickets_by_date( Restv1Tester $I ) {
		// 5 posts, 2 tickets per post = 10 tickets
		$post_ids = $I->haveManyPostsInDatabase( 5 );
		$tickets  = array_reduce( $post_ids, function ( $acc, $post_id ) {
			$acc[] = $this->create_rsvp_ticket( $post_id );
			$acc[] = $this->create_paypal_ticket( $post_id, 3 );

			return $acc;
		}, [] );
		// space the tickets out by 1 day starting today; the last ticket will be
		// the most recent one
		$utc           = new \DateTimeZone( 'UTC' );
		$date          = new \DateTime( 'now', $utc );
		$four_days_ago = new \DateTime( '-4 days', $utc );
		$a_week_ago    = new \DateTime( '-1 week', $utc );
		$one_day       = new \DateInterval( 'P1D' );
		tribe_tickets()
			->in( array_reverse( $tickets ) )
			->order_by( 'post__in' )
			->set( 'post_date_gmt', function () use ( &$date, $one_day ) {
				$post_date_gmt = $date->format( 'Y-m-d H:i:s' );
				$date->sub( $one_day );

				return $post_date_gmt;
			} )
			->save();

		// all tickets
		$I->sendGET( $this->tickets_url );
		$I->seeResponseIsJson();
		$I->seeResponseCodeIs( 200 );
		$expected_tickets = tribe_tickets( 'restv1' )
			->in( $tickets )
			->all();
		$I->seeResponseContainsJson( [
			'rest_url'    => $this->tickets_url . '/',
			'total'       => 10,
			'total_pages' => 1,
			'tickets'     => $expected_tickets,
		] );

		$I->sendGET( $this->tickets_url, [ 'after' => $four_days_ago->format( 'Y-m-d H:i:s' ) ] );
		$I->seeResponseIsJson();
		$I->seeResponseCodeIs( 200 );
		$expected_tickets = tribe_tickets( 'restv1' )
			->in( \array_slice( $tickets, 5 ) )
			->all();
		$I->seeResponseContainsJson( [
			'rest_url'    => add_query_arg( [ 'after' => $four_days_ago->format( 'Y-m-d H:i:s' ) ], $this->tickets_url . '/' ),
			'total'       => 5,
			'total_pages' => 1,
			'tickets'     => $expected_tickets,
		] );

		$I->sendGET( $this->tickets_url, [ 'before' => $a_week_ago->format( 'Y-m-d H:i:s' ) ] );
		$I->seeResponseIsJson();
		$I->seeResponseCodeIs( 200 );
		$expected_tickets = tribe_tickets( 'restv1' )
			->in( \array_slice( $tickets, 0, 3 ) )
			->all();
		$I->seeResponseContainsJson( [
			'rest_url'    => add_query_arg( [ 'before' => $a_week_ago->format( 'Y-m-d H:i:s' ) ], $this->tickets_url . '/' ),
			'total'       => 3,
			'total_pages' => 1,
			'tickets'     => $expected_tickets,
		] );

		$I->sendGET( $this->tickets_url, [ 'after' => '-3 days' ] );
		$I->seeResponseIsJson();
		$I->seeResponseCodeIs( 200 );
		$expected_tickets = tribe_tickets( 'restv1' )
			->in( \array_slice( $tickets, 7 ) )
			->all();
		$I->seeResponseContainsJson( [
			'rest_url'    => add_query_arg( [ 'after' => '-3 days' ], $this->tickets_url . '/' ),
			'total'       => 3,
			'total_pages' => 1,
			'tickets'     => $expected_tickets,
		] );

		$I->sendGET( $this->tickets_url, [ 'before' => '-1 week' ] );
		$I->seeResponseIsJson();
		$I->seeResponseCodeIs( 200 );
		$expected_tickets = tribe_tickets( 'restv1' )
			->in( \array_slice( $tickets, 0, 3 ) )
			->all();
		$I->seeResponseContainsJson( [
			'rest_url'    => add_query_arg( [ 'before' => '-1 week' ], $this->tickets_url . '/' ),
			'total'       => 3,
			'total_pages' => 1,
			'tickets'     => $expected_tickets,
		] );
	}

	/**
	 * It should return 400 when trying to filter by bad date
	 *
	 * @test
	 */
	public function should_return_400_when_trying_to_filter_by_bad_date( Restv1Tester $I ) {
		$I->sendGET( $this->tickets_url, [ 'before' => 'floz' ] );
		$I->seeResponseIsJson();
		$I->seeResponseCodeIs( 400 );

		$I->sendGET( $this->tickets_url, [ 'after' => 'wroom' ] );
		$I->seeResponseIsJson();
		$I->seeResponseCodeIs( 400 );
	}

	/**
	 * It should allow getting tickets by date availability
	 *
	 * @test
	 */
	public function should_allow_getting_tickets_by_date_availability( Restv1Tester $I ) {
		$post_ids = $I->haveManyPostsInDatabase( 5 );
		$i        = 0;
		$dates    = [
			'no-start-no-end' => [ null, null ],
			'no-end'          => [ '-1 month', null ],
			'start-and-end-1' => [ '-1 week', '+1week' ],
			'start-and-end-2' => [ 'tomorrow', '+3 days' ],
			'no-start'        => [ null, '+2 weeks' ]
		];
		$tz       = new \DateTimeZone( 'UTC' );
		$tickets  = array_combine( array_keys( $dates ), array_reduce( $post_ids, function ( array $acc, $post_id ) use ( &$i, $dates, $tz ) {
			$start = array_values( $dates )[ $i ][0];
			$end   = array_values( $dates )[ $i ++ ][1];

			$overrides = [
				'meta_input' => [
					'_ticket_start_date' => $start ? ( new \DateTime( $start, $tz ) )->format( 'Y-m-d H:i:s' ) : null,
					'_ticket_end_date'   => $end ? ( new \DateTime( $end, $tz ) )->format( 'Y-m-d H:i:s' ) : null,
				]
			];
			$acc[]     = $this->create_rsvp_ticket( $post_id, $overrides );

			return $acc;
		}, [] ) );

		$I->sendGET( $this->tickets_url, [ 'available_from' => '-8 days' ] );
		$I->seeResponseIsJson();
		$I->seeResponseCodeIs( 200 );
		$expected_tickets = tribe_tickets( 'restv1' )
			->in( [
				$tickets['no-start-no-end'],
				$tickets['start-and-end-1'],
				$tickets['start-and-end-2'],
				$tickets['no-start'],
			] )
			->all();
		$I->seeResponseContainsJson( [
			'rest_url'    => add_query_arg( [ 'available_from' => '-8 days' ], $this->tickets_url . '/' ),
			'total'       => 4,
			'total_pages' => 1,
			'tickets'     => $expected_tickets,
		] );

		$until = strtotime( '+2 days' );
		$I->sendGET( $this->tickets_url, [ 'available_until' => $until ] );
		$I->seeResponseIsJson();
		$I->seeResponseCodeIs( 200 );
		$expected_tickets = tribe_tickets( 'restv1' )
			->in( [
				$tickets['no-start-no-end'],
				$tickets['no-end'],
			] )
			->all();
		$I->seeResponseContainsJson( [
			'rest_url'    => add_query_arg( [ 'available_until' => $until ], $this->tickets_url . '/' ),
			'total'       => 2,
			'total_pages' => 1,
			'tickets'     => $expected_tickets,
		] );

		$I->sendGET( $this->tickets_url, [ 'available_from' => '-5 days', 'available_until' => '+5 days' ] );
		$I->seeResponseIsJson();
		$I->seeResponseCodeIs( 200 );
		$expected_tickets = tribe_tickets( 'restv1' )
			->in( [
				$tickets['no-start-no-end'],
				$tickets['start-and-end-2'],
			] )
			->all();
		$I->seeResponseContainsJson( [
			'rest_url'    => add_query_arg( [
				'available_from'  => '-5 days',
				'available_until' => '+5 days'
			], $this->tickets_url . '/' ),
			'total'       => 2,
			'total_pages' => 1,
			'tickets'     => $expected_tickets,
		] );
	}
}
