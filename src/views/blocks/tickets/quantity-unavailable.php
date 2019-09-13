<?php
/**
 * Block: Tickets
 * Quantity Unavailable
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/quantity-unavailable.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link {INSERT_ARTICLE_LINK_HERE}
 *
 * @since 4.9.3
 * @version TBD
 *
 */

$ticket = $this->get( 'ticket' );
?>
<div
	class="tribe-tickets__item__quantity__unavailable"
>
	<?php echo esc_html_x( 'Sold Out', 'Tickets are sold out.', 'event-tickets' ); ?>
</div>
