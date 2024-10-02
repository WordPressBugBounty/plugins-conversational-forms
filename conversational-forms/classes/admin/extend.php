<?php

/**
 * Manage extend sub-menu
 *
 * @package Caldera_Forms Modified by QuantumCloud
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Qcformbuilder_Forms_Admin_Extend {


	/**
	 * Enqueue scripts for the admin extend sub menu
	 *
	 * @uses "admin_enqueue_scripts" action
	 *
	 * @since 1.4.2
	 */
	public static function scripts(){
		Qcformbuilder_Forms_Render_Assets::register();
		Qcformbuilder_Forms_Render_Assets::enqueue_script( 'handlebars' );
		Qcformbuilder_Forms_Admin_Assets::enqueue_style( 'admin' );
	}
}