<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Generic HTML input field
 *
 * @package Caldera_Forms Modified by QuantumCloud
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
?>
<?php echo wp_kses_post( $wrapper_before ); ?>
	<?php echo wp_kses_post( $field_label ); ?>
	<?php echo wp_kses_post( $field_before ); ?>
		<?php echo Qcformbuilder_Forms_Field_Input::html( $field, $field_structure, $form ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output is built from escaped parts ?>
		<?php echo wp_kses_post( $field_caption ); ?>
	<?php echo wp_kses_post( $field_after ); ?>
<?php echo wp_kses_post( $wrapper_after ); ?>