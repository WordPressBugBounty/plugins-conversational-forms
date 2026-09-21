<?php


/**
 * Class Qcformbuilder_Forms_Admin_Resend
 */
class Qcformbuilder_Forms_Admin_Resend {

	/**
	 * Nonce action for resend links
	 *
	 * @since 1.5.2
	 *
	 * @var string
	 */
	protected static $nonce_action = '_wfb_resend';

	/**
	 * Generate nonce for resend links
	 *
	 * @since 1.5.2
	 *
	 * @return string
	 */
	public static function resend_nonce(){
		return wp_create_nonce( self::$nonce_action );
	}

	/**
	 * Verify the resend nonce
	 *
	 * @since 1.5.2
	 *
	 * @param string $nonce Nonce to check
	 *
	 * @return false|int
	 */
	public static function verify_nonce( $nonce ){
		return wp_verify_nonce( $nonce, self::$nonce_action );

	}

	/**
	 * Watch for resend requests and process if so and authorized, etc.
	 *
	 * @uses "init" action
	 *
	 * @since 1.5.2
	 *
	 */
	public static function watch_for_resend(){
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if( isset( $_GET[ '_wfb_resend' ], $_GET[ 'e' ], $_GET[ 'f' ] ) ){
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$nonce = sanitize_text_field( wp_unslash( $_GET[ '_wfb_resend' ] ) );
			if ( ! self::verify_nonce( $nonce ) ) {
				return;
			}

			if ( ! current_user_can( Qcformbuilder_Forms::get_manage_cap( 'resend-email' ) ) ) {
				return;
			}
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$form = Qcformbuilder_Forms_Forms::get_form( sanitize_text_field( wp_unslash( $_GET[ 'f' ] ) ) );
			if( is_array( $form ) ){
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$resender = new Qcformbuilder_Forms_Email_Resend( absint( wp_unslash( $_GET[ 'e' ] ) ), $form  );

				$resender->resend();
				wfb_redirect( add_query_arg( 'message_resent', 'true', Qcformbuilder_Forms_Admin::main_admin_page_url()  ) );
				exit;

			}


		}

 	}


}