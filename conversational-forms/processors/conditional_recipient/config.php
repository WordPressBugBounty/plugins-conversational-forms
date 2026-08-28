<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	echo wp_kses_post( Qcformbuilder_Forms_Processor_UI::config_fields( qcformbuilder_forms_conditional_recipients_fields() ) );
?>
