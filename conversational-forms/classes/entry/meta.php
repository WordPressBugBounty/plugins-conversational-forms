<?php

/**
 * Object representation of an entry meta - wfb_entry_meta
 *
 * @package Caldera_Forms Modified by QuantumCloud
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Qcformbuilder_Forms_Entry_Meta extends Qcformbuilder_Forms_Entry_Object {

	/** @var  string */
	protected $meta_id;

	/** @var  string */
	protected $entry_id;

	/** @var  string */
	protected $process_id;

	/** @var  string */
	protected $meta_key;

	/** @var  string|array */
	protected $meta_value;

	/**
	 * Apply deserialization/json_decoding if needed to meta_value column
	 *
	 * @since 1.4.0
	 *
	 * @param string $value Meta value
	 */
	protected function meta_value_set( $value ){
		if( is_array( $value ) ){
			$this->meta_value = $value;
		} elseif( is_serialized( $value  ) ){
			// Security: restrict deserialization to scalars and arrays only.
			// maybe_unserialize() passes no class whitelist, so a crafted
			// serialized object stored in meta_value would instantiate an
			// arbitrary class and invoke __wakeup / __destruct gadget chains.
			// With allowed_classes => false, object tokens produce a harmless
			// __PHP_Incomplete_Class and no constructor or magic method runs.
			$this->meta_value = unserialize( $value, array( 'allowed_classes' => false ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
		}elseif( 0 === strpos( $value, '{' ) && is_object( $_value = json_decode( $value ) ) ){
			$this->meta_value = (array) $_value;
		}else{
			$this->meta_value = $value;
		}

	}
}
