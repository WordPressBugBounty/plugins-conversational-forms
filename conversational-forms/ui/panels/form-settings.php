<?php
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
		<?php esc_html_e( 'Form Settings', 'qcformbuilder-forms' ); ?>
	</h3>

	<input type="hidden" name="config[wfb_version]" value="<?php echo esc_attr( WFBCORE_VER ); ?>">

	<div class="qcformbuilder-config-group">
		<label for="wfb-form-name">
			<?php esc_html_e( 'Form Name', 'qcformbuilder-forms' ); ?>
		</label>
		<div class="qcformbuilder-config-field">
			<input id="wfb-form-name"type="text" class="field-config required" name="config[name]" value="<?php esc_html_e( esc_attr($form[ 'name' ]), 'chatbot-forms' ); ?>" style="width:500px;" required="required">
		</div>
	</div>
	<div class="qcformbuilder-config-group">
		<p>This Form Name will be label of the button if you add it in WPBot start menu.</p>
		<p>If you update the Form Name and already added in start menu before, then please hit the Save Settings in WPBot Lite > Start Menu.</p>
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
