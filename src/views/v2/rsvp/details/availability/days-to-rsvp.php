<?php
/**
 * Block: RSVP
 * Details Availability - Days to RSVP
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/details/availability/days-to-rsvp.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link    https://m.tri.be/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.12.3
 *
 * @version 4.12.3
 *
 * @var Tribe__Tickets__Editor__Template $this                Template object.
 * @var int                              $post_id             [Global] The current Post ID to which RSVPs are attached.
 * @var array                            $attributes          [Global] RSVP attributes (could be empty).
 * @var Tribe__Tickets__Ticket_Object[]  $active_rsvps        [Global] List of RSVPs.
 * @var bool                             $all_past            [Global] True if RSVPs availability dates are all in the past.
 * @var bool                             $has_rsvps           [Global] True if the event has any RSVPs.
 * @var bool                             $has_active_rsvps    [Global] True if the event has any RSVPs available.
 * @var bool                             $must_login          [Global] True if only logged-in users may obtain RSVPs.
 * @var string                           $login_url           [Global] The site's login URL.
 * @var int                              $threshold           [Global] The count at which "number of tickets left" message appears.
 * @var null|string                      $step                [Global] The point we're at in the loading process.
 * @var bool                             $opt_in_checked      [Global] Whether appearing in Attendee List was checked.
 * @var string                           $opt_in_attendee_ids [Global] The list of attendee IDs to send in the form submission.
 * @var string                           $opt_in_nonce        [Global] The nonce for opt-in AJAX requests.
 * @var bool                             $doing_shortcode     [Global] True if detected within context of shortcode output.
 * @var bool                             $block_html_id       [Global] The RSVP block HTML ID. $doing_shortcode may alter it.
 * @var Tribe__Tickets__Ticket_Object    $rsvp                The rsvp ticket object.
 * @var false|float                      $days_to_rsvp        The number of days before the RSVP is no longer available.
 */

if ( 0 < $days_to_rsvp ) {
	$text = sprintf(
		// Translators: 1: opening span. 2: the number of remaining days to RSVP. 3: Closing span. 4: The RSVP label.
		_nx(
			'%1$s %2$s %3$s day left to %4$s',
			'%1$s %2$s %3$s days left to %4$s',
			$days_to_rsvp,
			'Days to RSVP',
			'event-tickets'
		),
		'<span class="tribe-tickets__rsvp-availability-days-left tribe-common-b2--bold">',
		number_format_i18n( $days_to_rsvp ),
		'</span>',
		tribe_get_rsvp_label_singular( 'Days to RSVP' )
	);
} else {
	$text = sprintf(
		// Translators: %s: The RSVP label.
		_x(
			'Last day to %s',
			'Last day to RSVP',
			'event-tickets'
		),
		tribe_get_rsvp_label_singular( 'Last day to RSVP' )
	);
}
?>

<span class="tribe-tickets__rsvp-availability-days-to-rsvp">
	<?php echo wp_kses_post( $text ); ?>
</span>
