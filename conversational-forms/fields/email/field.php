<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(!empty($field['config']['placeholder'])){
	$placeholder = Qcformbuilder_Forms::do_magic_tags( $field['config']['placeholder'] );
	$field_placeholder = 'placeholder="'. esc_attr( $placeholder ).'"';
}

?><?php echo wp_kses_post( $wrapper_before ); ?>
	<?php echo wp_kses_post( $field_label ); ?>
	<?php echo wp_kses_post( $field_before ); ?>
		<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes built from esc_attr() calls ?>
		<input <?php echo $field_placeholder; ?> type="email" data-field="<?php echo esc_attr( $field_base_id ); ?>" class="<?php echo esc_attr( $field_class ); ?>" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_value ); ?>" <?php echo $field_required; ?> <?php echo $field_structure['aria']; ?>>
		<?php echo wp_kses_post( $field_caption ); ?>
	<?php echo wp_kses_post( $field_after ); ?>
<?php echo wp_kses_post( $wrapper_after ); ?>