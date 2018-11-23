<?php

/**
 * Class Tribe__Tickets__Editor
 *
 * @since TBD
 */
class Tribe__Tickets__Editor extends Tribe__Editor {

	/**
	 * Hooks actions from the editor into the correct places
	 *
	 * @since TBD
	 *
	 * @return bool
	 */
	public function hook() {
		// Add Rest API support
		add_filter( 'tribe_tickets_register_ticket_post_type_args', array( $this, 'add_rest_support' ) );

		// Update Post content to use correct child blocks for tickets
		add_filter( 'tribe_blocks_editor_update_classic_content', array( $this, 'update_tickets_block_with_childs' ), 10, 3 );

		// Make data available to the current ticket
		add_filter( 'tribe_editor_js_config', array( $this, 'add_tickets_js_config' ) );

		// Add RSVP and tickets blocks
		add_action( 'admin_init', array( $this, 'add_tickets_block_in_editor' ) );

		add_filter( 'tribe_events_editor_default_classic_template', array( $this, 'filter_default_template_classic_blocks' ), 15 );

		add_action( 'tribe_events_tickets_post_capacity', tribe_callback( 'tickets.admin.views', 'template', 'editor/button-view-orders' ) );
		add_action( 'tribe_events_tickets_metabox_edit_main', array( $this, 'filter_get_price_fields' ), 10, 2 );
		add_action( 'tribe_events_tickets_capacity', tribe_callback( 'tickets.admin.views', 'template', 'editor/total-capacity' ) );
		add_action( 'tribe_events_tickets_ticket_table_add_header_column', tribe_callback( 'tickets.admin.views', 'template', 'editor/column-head-price' ) );
		add_action( 'tribe_events_tickets_ticket_table_add_tbody_column', array( $this, 'add_column_content_price' ), 10, 2 );
	}

	/**
	 * Adds the ticket block into the editor
	 *
	 * @since TBD
	 *
	 * @param array $template Array of all the templates used by default
	 * @param string $post_type The current post type
	 *
	 * @return array
	 */
	public function add_tickets_block_in_editor() {
		foreach ( $this->get_enabled_post_types() as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );

			if ( ! $post_type_object ) {
				continue;
			}

			$template = isset( $post_type_object->template )
				? (array) $post_type_object->template
				: array();

			$template[] = array( 'tribe/tickets' );
			$template[] = array( 'tribe/rsvp' );
			$template[] = array( 'tribe/attendees' );

			$post_type_object->template = $template;
		}
	}

	/**
	 * Filters and adds the ticket block into the default classic blocks
	 *
	 * @since TBD
	 *
	 * @param  array $template
	 *
	 * @return array
	 */
	public function filter_default_template_classic_blocks( $template = array() ) {
		$template[] = array( 'tribe/tickets' );
		$template[] = array( 'tribe/rsvp' );
		$template[] = array( 'tribe/attendees' );
		return $template;
	}

	/**
	 * Check if current admin page is post type `tribe_events`
	 *
	 * @since TBD
	 *
	 * @param  mixed $post_type
	 *
	 * @return bool
	 */
	public function current_type_support_tickets( $post_type = null ) {
		$post_types = $this->get_enabled_post_types();

		if ( ! is_null( $post_type ) ) {
			return in_array( $post_type, $post_types, true );
		}

		$is_valid_type = false;
		foreach ( $this->get_enabled_post_types() as $post_type ) {
			$is_valid_type = Tribe__Admin__Helpers::instance()->is_post_type_screen( $post_type );
			// Don't operate on following types as current type is valid
			if ( $is_valid_type ) {
				return $is_valid_type;
			}
		}
		return $is_valid_type;
	}

	/**
	 * Making sure we have correct post content for tickets blocks after going into Gutenberg
	 *
	 * @since TBD
	 *
	 * @param  string  $content Content that will be updated
	 * @param  WP_Post $post    Which post we will migrate
	 * @param  array   $blocks  Which blocks we are updating with
	 *
	 * @return bool
	 */
	public function update_tickets_block_with_childs( $content, $post, $blocks ) {
		$search = '<!-- wp:tribe/tickets  /-->';

		// Do we haave a tickets blocks already setup? (we should)
		if ( false === strpos( $content, $search ) ) {
			return $content;
		}

		$tickets = Tribe__Tickets__Tickets::get_all_event_tickets( $post->ID );

		$replace[] = '<!-- wp:tribe/tickets --><div class="wp-block-tribe-tickets">';

		foreach ( $tickets as $key => $ticket ) {
			// Skip RSVP items
			if ( 'Tribe__Tickets__RSVP' === $ticket->provider_class ) {
				continue;
			}

			// Insert into the replace a single Child ticket
			$replace[] = '<!-- wp:tribe/tickets-item {"hasBeenCreated":true,"ticketId":' . $ticket->ID . '} --><div class="wp-block-tribe-tickets-item"></div><!-- /wp:tribe/tickets-item -->';
		}

		$replace[] = '</div><!-- /wp:tribe/tickets -->';

		// Do the actual replace for tickets blocks
		$content = str_replace( $search, implode( "\n\r", $replace ), $content );

		return $content;
	}

	/**
	 * Return the supported post types for tickets
	 *
	 * @return array
	 */
	public function get_enabled_post_types() {
		return (array) tribe_get_option( 'ticket-enabled-post-types', array() );
	}

	/**
	 * Add the event tickets category into the block categories
	 *
	 * @since TBD
	 *
	 * @param $categories
	 * @param $post
	 * @return array
	 */
	public function block_categories( $categories ) {
		if ( ! $this->current_type_support_tickets() ) {
			return $categories;
		}

		return array_merge(
			$categories,
			array(
				array(
					'slug' => 'tribe-tickets',
					'title' => __( 'Tickets Blocks', 'event-tickets' ),
				),
			)
		);
	}

	/**
	 * Add data associated with the tickets into the variable "tribe_js_config"
	 *
	 * @since TBD
	 *
	 * @param array $editor_js_config
	 *
	 * @return array An array with data to be passed to the FE.
	 */
	public function add_tickets_js_config( $editor_js_config ) {
		$modules = Tribe__Tickets__Tickets::modules();
		$class_names = array_keys( $modules );
		$providers = array();
		$default_currency_symbol = tribe_get_option( 'defaultCurrencySymbol', '$' );

		foreach ( $class_names as $class ) {
			if ( 'RSVP' === $modules[ $class ] ) {
				continue;
			}

			$currency = tribe( 'tickets.commerce.currency' );

			// Backwards to avoid fatals
			$currency_symbol = $default_currency_symbol;
			if ( is_callable( array( $currency, 'get_provider_symbol' ) ) ) {
				$currency_symbol = $currency->get_provider_symbol( $class, null );
			}

			$currency_position = 'prefix';
			if ( is_callable( array( $currency, 'get_provider_symbol_position' ) ) ) {
				$currency_position = $currency->get_provider_symbol_position( $class, null );
			}

			$providers[] = array(
				'name' => $modules[ $class ],
				'class' => $class,
				'currency' => html_entity_decode( $currency_symbol ),
				'currency_position' => $currency_position,
			);
		}

		$tickets = empty( $editor_js_config['tickets'] ) ? array() : $editor_js_config['tickets'];
		$editor_js_config['tickets'] = array_merge(
			(array) $tickets,
			array(
				'providers'        => $providers,
				'default_provider' => Tribe__Tickets__Tickets::get_default_module(),
				'default_currency' => tribe_get_option( 'defaultCurrencySymbol', '$' ),
			)
		);

		return $editor_js_config;
	}

	/**
	 * Prints and returns the Price fields
	 *
	 * @since TBD
	 *
	 * @param  int  $post_id    Post ID
	 * @param  int  $ticket_id  Ticket ID
	 *
	 * @return string
	 */
	public function filter_get_price_fields( $post_id, $ticket_id ) {
		$context = array(
			'post_id'   => $post_id,
			'ticket_id' => $ticket_id,
		);

		return tribe( 'tickets.admin.views' )->template( 'editor/fieldset/price', $context );
	}

	/**
	 * Prints and returns the Body for the Price Column
	 *
	 * @since TBD
	 *
	 * @param  Tribe__Tickets__Ticket_Object $ticket        Ticket object
	 * @param  mixed                         $provider_obj  The ticket provider object
	 *
	 * @return string
	 */
	public function add_column_content_price( $ticket, $provider_obj ) {
		$context = array(
			'ticket'       => $ticket,
			'provider_obj' => $provider_obj,
		);

		return tribe( 'tickets.admin.views' )->template( 'editor/column-body-price', $context );
	}
}
