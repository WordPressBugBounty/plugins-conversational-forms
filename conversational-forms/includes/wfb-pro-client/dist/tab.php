<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo wp_kses_post( \qcformbuilderwp\qcformbuilderforms\pro\container::get_instance()->get_tab_html() );