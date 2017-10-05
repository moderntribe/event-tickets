<?php
/**
 * Class Tribe__Tickets__Commerce__PayPal__Endpoints__Template_Interface
 *
 * @since TBD
 */
interface Tribe__Tickets__Commerce__PayPal__Endpoints__Template_Interface {

	/**
	 * Registers the resources this template will need to correctly render.
	 */
	public function register_resources();

	/**
	 * Builds and returns the date needed by this template.
	 *
	 * @since TBD
	 *
	 * @param array $template_data
	 */
	public function get_template_data( array $template_data = array() );

	/**
	 * Enqueues the resources needed by this template to correctly render.
	 *
	 * @since TBD
	 */
	public function
	enqueue_resources();
}