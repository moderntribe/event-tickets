<?php

/**
 * The ORM/Repository class for RSVP attendees.
 *
 * @since TBD
 */
class Tribe__Tickets__Repositories__Attendee__RSVP extends Tribe__Tickets__Attendee_Repository {

	/**
	 * {@inheritdoc}
	 */
	public function attendee_types() {
		$types = parent::attendee_types();

		$types = [
			'rsvp' => $types['rsvp'],
		];

		return $types;
	}

	/**
	 * {@inheritdoc}
	 */
	public function attendee_to_event_keys() {
		$keys = parent::attendee_to_event_keys();

		$keys = [
			'rsvp' => $keys['rsvp'],
		];

		return $keys;
	}

}
