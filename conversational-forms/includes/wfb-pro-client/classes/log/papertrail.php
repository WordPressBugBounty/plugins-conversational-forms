<?php


namespace qcformbuilderwp\qcformbuilderforms\pro\log;


/**
 * Class papertrail
 *
 * This class is based on https://github.com/sc0ttkclark/papertrail Thanks Scott!
 *
 *
 * @package qcformbuilderwp\qcformbuilderforms\pro\log
 */
class papertrail
{
	/**
	 * Socket resource for reuse
	 *
	 * @var resource
	 */
	protected static $socket;

	/**
	 * @var string
	 */
	protected static $destination = 'logs6.papertrailapp.com:10966';

	/**
	 * An array of error codes and their equivalent string value
	 *
	 * @var array
	 */
	protected static $codes = [
		E_ERROR => 'E_ERROR',
		E_WARNING => 'E_WARNING',
		E_PARSE => 'E_PARSE',
		E_NOTICE => 'E_NOTICE',
		E_CORE_ERROR => 'E_CORE_ERROR',
		E_CORE_WARNING => 'E_CORE_WARNING',
		E_COMPILE_ERROR => 'E_COMPILE_ERROR',
		E_COMPILE_WARNING => 'E_COMPILE_WARNING',
		E_USER_ERROR => 'E_USER_ERROR',
		E_USER_WARNING => 'E_USER_WARNING',
		E_USER_NOTICE => 'E_USER_NOTICE',
		E_STRICT => 'E_STRICT',
		E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
		E_DEPRECATED => 'E_DEPRECATED',
		E_USER_DEPRECATED => 'E_USER_DEPRECATED',
	];

	/**
	 * Log data to Papertrail.
	 *
	 * @author Troy Davis from the Gist located here: https://gist.github.com/troy/2220679
	 *
	 * @param string|array|object $data Data to log to Papertrail.
	 * @param string $component Component name to identify log in Papertrail.
	 *
	 * @return bool|\WP_Error True if successful or an WP_Error object with the problem.
	 */
	public static function log($data, $component = '')
	{


		$destination = array_combine([ 'hostname', 'port' ], explode(':', self::$destination));
		$program = wp_parse_url(is_multisite() ? network_site_url() : site_url(), PHP_URL_HOST);
		$json = json_encode($data);

		if ( empty($destination) || 2 != count($destination) || empty($destination[ 'hostname' ]) ) {
			/* translators: 1: full destination, 2: hostname, 3: port */
			$error_message = sprintf(__('Invalid Papertrail destination (%1$s >> %2$s:%3$s).', 'conversational-forms'), self::$destination, $destination[ 'hostname' ], $destination[ 'port' ]);
			return new WP_Error('papertrail-invalid-destination', $error_message);
		}

		$syslog_message = '<22>' . date_i18n('M d H:i:s');

		if ( $program ) {
			$syslog_message .= ' ' . trim($program);
		}

		if ( $component ) {
			$syslog_message .= ' ' . trim($component);
		}

		$syslog_message .= ' ' . $json;

		if ( !self::$socket ) {
			self::$socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

			@socket_connect(self::$socket, $destination[ 'hostname' ], $destination[ 'port' ]);
		}

		$result = socket_send(self::$socket, $syslog_message, strlen($syslog_message), 0);

		//socket_close( self::$socket );

		$success = false;

		if ( false !== $result ) {
			$success = true;
		}

		return $success;

	}

	/**
	 * Get page info
	 *
	 * @param array $page_info
	 *
	 * @return array
	 */
	public static function get_page_info($page_info = [])
	{

		// Setup URL
		$page_info[ 'url' ] = 'http://';

		if ( is_ssl() ) {
			$page_info[ 'url' ] = 'https://';
		}

		$http_host   = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

		$page_info[ 'url' ] .= $http_host . $request_uri;

		$page_info[ 'url' ] = explode('?', $page_info[ 'url' ]);
		$page_info[ 'url' ] = $page_info[ 'url' ][ 0 ];
		$page_info[ 'url' ] = explode('#', $page_info[ 'url' ]);
		$page_info[ 'url' ] = $page_info[ 'url' ][ 0 ];

		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		$page_info[ '$_GET' ] = $_GET;
		$page_info[ '$_POST' ] = $_POST;
		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing

		$page_info[ 'DOING_AJAX' ] = (defined('DOING_AJAX') && DOING_AJAX);
		$page_info[ 'DOING_CRON' ] = (defined('DOING_CRON') && DOING_CRON);

		// Remove potentially sensitive information from page info
		if ( isset($page_info[ '$_GET' ][ 'password' ]) ) {
			unset($page_info[ '$_GET' ][ 'password' ]);
		}

		if ( isset($page_info[ '$_GET' ][ 'pwd' ]) ) {
			unset($page_info[ '$_GET' ][ 'pwd' ]);
		}

		if ( isset($page_info[ '$_POST' ][ 'password' ]) ) {
			unset($page_info[ '$_POST' ][ 'password' ]);
		}

		if ( isset($page_info[ '$_POST' ][ 'pwd' ]) ) {
			unset($page_info[ '$_POST' ][ 'pwd' ]);
		}

		return $page_info;

	}


}
