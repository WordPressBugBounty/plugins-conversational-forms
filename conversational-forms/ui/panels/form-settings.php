<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Form Settings panel
 *
 * @package Caldera_Forms Modified by QuantumCloud
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
?>
<div style="display: none;" class="qcformbuilder-editor-body qcformbuilder-config-editor-panel " id="settings-panel">
	<h3>
		<?php esc_html_e( 'Form Settings', 'conversational-forms'); ?>
	</h3>

	<input type="hidden" name="config[wfb_version]" value="<?php echo esc_attr( WFBCORE_VER ); ?>">

	<div class="wfb-form-settings-layout">
		<div class="qcformbuilder-config-group wfb-form-settings-name">
			<label for="wfb-form-name">
				<?php esc_html_e( 'Form Name', 'conversational-forms'); ?>
			</label>
			<div class="qcformbuilder-config-field">
				<input id="wfb-form-name" type="text" class="field-config required" name="config[name]" value="<?php echo esc_attr( $form['name'] ); ?>" required="required">
			</div>
		</div>
		<div class="qcformbuilder-config-group wfb-form-settings-help">
			<p><?php esc_html_e( 'This Form Name will be the label of the button if you add it in the WPBot start menu.', 'conversational-forms'); ?></p>
			<p><?php esc_html_e( 'If you update the Form Name and it was already added in the start menu, please hit Save Settings in WPBot Lite > Start Menu.', 'conversational-forms'); ?></p>
		</div>
	</div>

	<?php

	/**
	 * Runs at the bottom of the general settings panel
	 *
	 * @since unknown
	 *
	 * @param array $element Form config
	 */
	//do_action( 'qcformbuilder_forms_general_settings_panel', $element );
	?>
</div>
