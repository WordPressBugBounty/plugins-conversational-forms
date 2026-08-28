<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$elementType = $field['config']['element'];
if(empty($elementType)){
	$elementType = 'div';
}

if(!empty($field['config']['before'])){
	$field['config']['before'] = Qcformbuilder_Forms::do_magic_tags($field['config']['before']);
}
if(!empty($field['config']['after'])){
	$field['config']['after'] = Qcformbuilder_Forms::do_magic_tags($field['config']['after']);
}


if( !isset( $field['config']['thousand_separator'] ) ){
	$field['config']['thousand_separator'] = ',';
}

if( !isset( $field['config']['decimal_separator'] ) ){
	$field['config']['decimal_separator'] = '.';
}


$target_id = Qcformbuilder_Forms_Field_Util::get_base_id( $field, $current_form_count, $form );

$value_field_id = $target_id . '-value';
$attrs = array(
	'type'            => 'hidden',
	'name'            => $field_name,
	'value'           => 0,
	'data-field'      => $target_id,
	'data-calc-field' => $field[ 'ID' ],
	'data-type'       => 'calculation',
	'id'              => $value_field_id
);
$attr_string =  qcformbuilder_forms_field_attributes( $attrs, $field, $form );
$syncer = Qcformbuilder_Forms_Sync_Factory::get_object( $form, $field, Qcformbuilder_Forms_Field_Util::get_base_id( $field, null, $form ) );


?>

<?php echo wp_kses_post( $wrapper_before ); ?>

	<?php echo wp_kses_post( $field_label ); ?>

	<?php echo wp_kses_post( $field_before ); ?>

		<<?php echo tag_escape( $elementType ) . $field_structure['aria']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- aria attributes built from escaped parts ?> class="<?php echo esc_attr( $field['config']['classes'] ); ?>"><?php echo wp_kses_post( $field['config']['before'] ); ?>
			<span id="<?php echo esc_attr( $field_id ); ?>" data-calc-display="<?php echo esc_attr( $value_field_id ); ?>"><?php echo esc_html( $field_value ); ?></span><?php echo wp_kses_post( $field['config']['after'] ); ?></<?php echo tag_escape( $elementType ); ?>>
				<input type="hidden" <?php echo $attr_string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts ?> >

		<?php echo wp_kses_post( $field_caption ); ?>

	<?php echo wp_kses_post( $field_after ); ?>

<?php echo wp_kses_post( $wrapper_after ); ?>

<?php

Qcformbuilder_Forms_Render_Util::add_inline_script( $syncer->get_formula( true ), $form );