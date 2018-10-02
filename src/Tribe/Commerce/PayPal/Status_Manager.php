<?php


/**
 * Class Tribe__Tickets__Commerce__PayPal__Status_Manager
 *
 * @since TBD
 *
 */
class Tribe__Tickets__Commerce__PayPal__Status_Manager {

	public $status_names = array(
		'Complete',
		'Denied',
		'Not_Completed',
		'Pending',
		'Refunded',
	);

	public $statuses = array();

	public function __construct() {

		$this->initialize_status_classes();
	}

	/**
	 * Initialize Commerce Status Class and Get all Statuses
	 */
	public function initialize_status_classes() {

		foreach ( $this->status_names as $name ) {

			$class_name = 'Tribe__Tickets__Commerce__PayPal__Status__' . $name;

			$this->statuses[ $name ] = new $class_name();
		}
	}
}