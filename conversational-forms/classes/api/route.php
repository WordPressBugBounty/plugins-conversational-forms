<?php

/**
 * Interface all REST API routes should use
 *
 * @package Caldera_Forms Modified by QuantumCloud
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
interface Qcformbuilder_Forms_API_Route {

	/**
	 * Add the routes for this set of endpoints
	 *
	 * @since 1.4.4
	 *
	 * @param string $namespace API namespace
	 *
	 * @return void
	 */
	public function add_routes( $namespace );

}