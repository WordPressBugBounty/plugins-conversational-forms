<?php

/**
 * Entry viewer shortcode
 *
 * @package Caldera_Forms Modified by QuantumCloud
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Qcformbuilder_Forms_Entry_Shortcode {

	/**
	 * Name of shortcode
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	protected static $name = 'qcformbuilder_forms_entry_viewer';

	/**
	 * Get shorcode name
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public static function get_shortcode_name(){
		return self::$name;
	}

	/**
	 * Callback for shortcode
	 *
	 * @since 1.5.0
	 *
	 * @param array $atts Shortcode atts
	 *
	 * @return string
	 */
	public static function shortcode_callback( $atts ){
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$wfb_id = isset( $_GET['wfb_id'] ) ? sanitize_text_field( wp_unslash( $_GET['wfb_id'] ) ) : null;
		$atts = shortcode_atts( array(
			'form_id' => $wfb_id,
			'id' => $wfb_id,
			'ID' => $wfb_id,
			'type' => '2',
			'with_toolbar' => false,
			'role' => 'admin'
		), $atts, self::$name );
		if( isset( $atts[ 'id' ] ) ){
			$id = $atts[ 'id' ];
		}elseif ( isset( $atts[ 'ID' ] ) ){
			$id = $atts[ 'ID' ];
		}elseif( isset( $atts[ 'form_id' ] ) ){
			$id = $atts[ 'form_id' ];
		}else{
			$id = null;
		}


		if ( ! is_null( $id ) ) {
			$form = Qcformbuilder_Forms_Forms::get_form( $id );
			if ( ! empty( $form ) ) {
				if ( 'classic' == $atts[ 'type' ] ) {
					return Qcformbuilder_Forms_Entry_Viewer::form_entry_viewer_1( $id, wp_validate_boolean( $atts[ 'with_toolbar' ] ) );

				} else {
					return Qcformbuilder_Forms_Entry_Viewer::form_entry_viewer_2( $form, array( 'token' => Qcformbuilder_Forms_API_Token::make_token( $atts[ 'role' ], $id ) ) );

				}

			}
		}

		return '';

	}

}