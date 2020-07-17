<?php
/**
 * This template renders the Text or Textarea field.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/components/fields/text.php
 *
 * @since TBD
 *
 * @version TBD
 *
 * @see Tribe__Tickets_Plus__Meta__Field__Text
 */

$field_name = tribe_tickets_ar_field_name( $ticket->ID, $field->slug );
$field_id   = tribe_tickets_ar_field_id( $ticket->ID, $field->slug );
$required   = tribe_tickets_ar_field_is_required( $field );
$field      = (array) $field;
$multiline  = isset( $field['extra'] ) && isset( $field['extra']['multiline'] ) ? $field['extra']['multiline'] : '';
$disabled   = false;

$classes = [
	'tribe-common-b1',
	'tribe-tickets__form-field',
	'tribe-tickets__form-field--required' => $required,
];

?>
<div <?php tribe_classes( $classes ); ?>>
	<label
		class="tribe-common-b2--min-medium tribe-tickets__form-field-label"
		for="<?php echo esc_attr( $field_id ); ?>"
	><?php echo wp_kses_post( $field['label'] ); ?><?php tribe_required_label( $required ); ?></label>
	<?php if ( $multiline ) : ?>
		<textarea
			id="<?php echo esc_attr( $field_id ); ?>"
			class="tribe-common-form-control-text__input tribe-tickets__form-field-input"
			name="<?php echo esc_attr( $field_name ); ?>"
			<?php tribe_required( $required ); ?>
			<?php tribe_disabled( $disabled ); ?>
		><?php echo esc_textarea( $value ); ?></textarea>
	<?php else : ?>
		<input
			type="text"
			id="<?php echo esc_attr( $field_id ); ?>"
			class="tribe-common-form-control-text__input tribe-tickets__form-field-input"
			name="<?php echo esc_attr( $field_name ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			<?php tribe_required( $required ); ?>
			<?php tribe_disabled( $disabled ); ?>
		/>
	<?php endif; ?>
</div>
