<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $field_config_panels;
?>
<div id="field_config_panels"><?php

if(!empty($field_config_panels)){
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from qcformbuilder_forms_field_wrapper_template() using esc_* helpers.
	echo implode("\r\n", $field_config_panels );
}

?></div>
<?php do_action( 'qcformbuilder_forms_layout_sidebar', $element ); ?>