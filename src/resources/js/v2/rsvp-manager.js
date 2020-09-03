/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since5.0.0
 *
 * @type {PlainObject}
 */
tribe.tickets = tribe.tickets || {};
tribe.tickets.rsvp = tribe.tickets.rsvp || {};

/**
 * Configures RSVP manager Object in the Global Tribe variable
 *
 * @since 5.0.0
 *
 * @type {PlainObject}
 */
tribe.tickets.rsvp.manager = {};

/**
 * Initializes in a Strict env the code that manages the Event Views
 *
 * @since 5.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} _   Underscore.js
 * @param  {PlainObject} obj tribe.tickets.rsvp.manager
 *
 * @return {void}
 */
( function( $, _, obj ) {
	'use strict';
	var $document = $( document );
	var $window = $( window );

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since5.0.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		container: '.tribe-tickets__rsvp-wrapper',
		loader: '.tribe-common-c-loader',
		hiddenElement: '.tribe-common-a11y-hidden',
		messageError: '.tribe-tickets__rsvp-message--error',
	};

	/**
	 * Stores the current ajax request been handled by the manager.
	 *
	 * @since5.0.0
	 *
	 * @type {jqXHR|null}
	 */
	obj.currentAjaxRequest = null;

	/**
	 * Containers on the current page that were initialized.
	 *
	 * @since5.0.0
	 *
	 * @type {jQuery}
	 */
	obj.$containers = $();

	/**
	 * Saves all the containers in the page into the object.
	 *
	 * @since 5.0.0
	 *
	 * @return {void}
	 */
	obj.selectContainers = function() {
		obj.$containers = $( obj.selectors.container );
	};

	/**
	 * Clean up the container and event listeners
	 *
	 * @since5.0.0
	 *
	 * @param  {jQuery} container Which element we are going to clean up
	 *
	 * @return {void}
	 */
	obj.cleanup = function( container ) {
		var $container = $( container );

		$container.trigger( 'beforeCleanup.tribeTicketsRsvp', [ $container ] );

		$container.trigger( 'afterCleanup.tribeTicketsRsvp', [ $container ] );
	};

	/**
	 * Setup the container for RSVP management
	 *
	 * @since5.0.0
	 *
	 * @todo  Requirement to setup other JS modules after hijacking Click and Submit
	 *
	 * @param  {integer}        index     jQuery.each index param
	 * @param  {Element|jQuery} container Which element we are going to setup
	 *
	 * @return {void}
	 */
	obj.setup = function( index, container ) {
		var $container = $( container );

		$container.trigger( 'beforeSetup.tribeTicketsRsvp', [ index, $container ] );

		$container.trigger( 'afterSetup.tribeTicketsRsvp', [ index, $container ] );
	};

	/**
	 * Performs an AJAX request.
	 *
	 * @since5.0.0
	 *
	 * @param  {object}         data       DOM Event related to the Click action
	 * @param  {Element|jQuery} $container Which container we are dealing with
	 *
	 * @return {void}
	 */
	obj.request = function( data, $container ) {
		var settings = obj.getAjaxSettings( $container );

		// Pass the data received to the $.ajax settings
		settings.data = data;

		obj.currentAjaxRequest = $.ajax( settings );
	};

	/**
	 * Gets the jQuery.ajax() settings provided a views container
	 *
	 * @since5.0.0
	 *
	 * @param  {Element|jQuery} $container Which container we are dealing with.
	 *
	 * @return {Object} ajaxSettings
	 */
	obj.getAjaxSettings = function( $container ) {

		var ajaxSettings = {
			url: TribeRsvp.ajaxurl,
			method: 'POST',
			beforeSend: obj.ajaxBeforeSend,
			complete: obj.ajaxComplete,
			success: obj.ajaxSuccess,
			error: obj.ajaxError,
			context: $container,
		};

		return ajaxSettings;
	};

	/**
	 * Triggered on jQuery.ajax() beforeSend action, which we hook into to
	 * setup a Loading Lock, as well as trigger a before and after hook, so
	 * third-party developers can always extend all requests
	 *
	 * Context with the RSVP container used to fire this AJAX call
	 *
	 * @since5.0.0
	 *
	 * @param  {jqXHR}       jqXHR    Request object
	 * @param  {PlainObject} settings Settings that this request will be made with
	 *
	 * @return {void}
	 */
	obj.ajaxBeforeSend = function( jqXHR, settings ) {
		var $container = this;
		var $loader = $container.find( obj.selectors.loader );

		$container.trigger( 'beforeAjaxBeforeSend.tribeTicketsRsvp', [ jqXHR, settings ] );

		if ( $loader.length ) {
			$loader.removeClass( obj.selectors.hiddenElement.className() );
		}

		$container.trigger( 'afterAjaxBeforeSend.tribeTicketsRsvp', [ jqXHR, settings ] );
	};

	/**
	 * Triggered on jQuery.ajax() complete action, which we hook into to
	 * removal of Loading Lock, as well as trigger a before and after hook,
	 * so third-party developers can always extend all requests
	 *
	 * Context with the RSVP container used to fire this AJAX call
	 *
	 * @since5.0.0
	 *
	 * @param  {jqXHR}  jqXHR      Request object
	 * @param  {String} textStatus Status for the request
	 *
	 * @return {void}
	 */
	obj.ajaxComplete = function( jqXHR, textStatus ) {
		var $container = this;
		var $loader = $container.find( obj.selectors.loader );

		$container.trigger( 'beforeAjaxComplete.tribeTicketsRsvp', [ jqXHR, textStatus ] );

		if ( $loader.length ) {
			$loader.addClass( obj.selectors.hiddenElement.className() );
		}

		$container.trigger( 'afterAjaxComplete.tribeTicketsRsvp', [ jqXHR, textStatus ] );

		// Reset the current AJAX request on the manager object.
		obj.currentAjaxRequest = null;
	};

	/**
	 * Triggered on jQuery.ajax() success action, which we hook into to
	 * replace the contents of the container which is the base behavior
	 * for the RSVP manager, as well as trigger a before and after hook,
	 * so third-party developers can always extend all requests
	 *
	 * Context with the RSVP container used to fire this AJAX call
	 *
	 * @since5.0.0
	 *
	 * @param  {Object} response   Response sent from the AJAX response.
	 * @param  {String} textStatus Status for the request
	 * @param  {jqXHR}  jqXHR      Request object
	 *
	 * @return {void}
	 */
	obj.ajaxSuccess = function( response, textStatus, jqXHR ) {
		const $container = this;
		const $html = response.data.html;

		// If the request is not successful, prepend the error.
		if ( ! response.success ) {
			// Prepend the error only once.
			if ( ! $container.find( obj.selectors.messageError ).length ) {
				$container.prepend( $html );
			}

			return;
		}

		$container.trigger( 'beforeAjaxSuccess.tribeTicketsRsvp', [ response, textStatus, jqXHR ] );

		// Clean up the container and event listeners.
		obj.cleanup( $container );

		// Replace the current container with the new Data.
		$container.html( $html );

		// Setup the container with the data received.
		obj.setup( 0, $container );

		// Update the global set of containers with all of the manager object.
		obj.selectContainers();

		$container.trigger( 'afterAjaxSuccess.tribeTicketsRsvp', [ response, textStatus, jqXHR ] );
	};

	/**
	 * Triggered on jQuery.ajax() error action, which we hook into to
	 * display error and keep the user on the same "page", as well as
	 * trigger a before and after hook, so third-party developers can
	 * always extend all requests
	 *
	 * Context with the RSVP container used to fire this AJAX call
	 *
	 * @since5.0.0
	 *
	 * @param  {jqXHR}       jqXHR    Request object
	 * @param  {PlainObject} settings Settings that this request was made with
	 *
	 * @return {void}
	 */
	obj.ajaxError = function( jqXHR, settings ) {
		var $container = this;

		$container.trigger( 'beforeAjaxError.tribeTicketsRsvp', [ jqXHR, settings ] );

		$container.trigger( 'afterAjaxError.tribeTicketsRsvp', [ jqXHR, settings ] );
	};

	/**
	 * Handles the initialization of the manager when Document is ready.
	 *
	 * @since 5.0.0
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		obj.selectContainers();
		obj.$containers.each( obj.setup );
	};

	// Configure on document ready.
	$document.ready( obj.ready );

} )( jQuery, window.underscore || window._, tribe.tickets.rsvp.manager );
