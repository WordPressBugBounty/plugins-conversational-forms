<?php
/**
 * Access to field definitions
 *
 * @package Caldera_Forms Modified by QuantumCloud
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Qcformbuilder_Forms_Fields {

	/**
	 * Get all field definitions
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public static function get_all() {

		/**
		 * Register or remove field types
		 *
		 * @since unknown
		 *
		 * @param array $field_types Field types
		 */
		$field_types = apply_filters( 'qcformbuilder_forms_get_field_types', self::internal_types() );


		if ( ! empty( $field_types ) ) {
			foreach ( $field_types as $fieldType => $fieldConfig ) {
				// check for a viewer
				if ( isset( $fieldConfig[ 'viewer' ] ) ) {
					add_filter( 'qcformbuilder_forms_view_field_' . $fieldType, $fieldConfig[ 'viewer' ], 10, 3 );
				}
			}
		}

		return $field_types;

	}

	/**
	 * Get definition of one field
	 *
	 * @since 1.5.0
	 *
	 * @param string $type Field type
	 *
	 * @return array
	 */
	public static function definition( $type ){
		$fields = self::get_all();
		if( array_key_exists( $type, $fields ) ){
			return $fields[ $type ];
		}

		return array();

	}

	/**
	 * Check if a field definition has defined a specific "not support" argument
	 *
	 * Use to check if field of $type does $not_support
	 *
	 * @since 1.5.0
	 *
	 * @param string $type The field type
	 * @param string $not_support The not support argument, for example "entry_list"
	 *
	 * @return bool|null True if not supported, false if not not supported. Null if invalid field type
	 */
	public static function not_support( $type, $not_support ){
		$field = self::definition( $type );
		if( ! empty( $field ) ){
			if( ! isset( $field[ 'setup' ], $field[ 'setup' ][ 'not_supported' ] )  ){
				return false;
			}
			if( ! empty(  $field[ 'setup' ][ 'not_supported' ] ) &&  in_array( $not_support, $field[ 'setup' ][ 'not_supported' ] )  ){
				return true;
			}

			return false;
		}

		return null;

	}

	/**
	 * Get internal field types without filter
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public static function internal_types() {
		$deprecated = __( 'Discontinued', 'qcformbuilder-forms' );
		$internal_fields = array(
			//basic
			'text'             => array(
				"field"       => __( 'Simple Text', 'qcformbuilder-forms' ),
				"description" => __( 'Simple Text Input Field', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/generic-input.php",
				"category"    => __( 'Basic', 'qcformbuilder-forms' ),
				"setup"       => array(
					"template" => WFBCORE_PATH . "fields/text/config.php",
					"preview"  => WFBCORE_PATH . "fields/text/preview.php"
				),

			),
			/*
			'hidden'           => array(
				"field"       => __( 'Hidden', 'qcformbuilder-forms' ),
				"description" => __( 'Hidden', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/hidden/field.php",
				"category"    => __( 'Basic', 'qcformbuilder-forms' ),
				"static"      => true,
				"setup"       => array(
					"preview"       => WFBCORE_PATH . "fields/hidden/preview.php",
					"template"      => WFBCORE_PATH . "fields/hidden/setup.php",
					"not_supported" => array(
						'hide_label',
						'caption',
						'required',
					)
				)
			),
			
			'email'            => array(
				"field"       => __( 'Email Address', 'qcformbuilder-forms' ),
				"description" => __( 'Email Address', 'qcformbuilder-forms' ),
				'icon'          => WFBCORE_URL . 'assets/build/images/envelope-o.svg',
				"file"        => WFBCORE_PATH . "fields/generic-input.php",
				"category"    => __( 'Basic', 'qcformbuilder-forms' ),
				"setup"       => array(
					"preview"  => WFBCORE_PATH . "fields/email/preview.php",
					"template" => WFBCORE_PATH . "fields/email/config.php"
				)
			),
			
			'button'           => array(
				"field"       => __( 'Button', 'qcformbuilder-forms' ),
				"description" => __( 'Button, Submit and Reset types', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/button/field.php",
				"category"    => __( 'Basic', 'qcformbuilder-forms' ),
				"capture"     => false,
				"setup"       => array(
					"template"      => WFBCORE_PATH . "fields/button/config_template.php",
					"preview"       => WFBCORE_PATH . "fields/button/preview.php",
					"default"       => array(
						'class' => 'btn btn-default',
						'type'  => 'submit'
					),
					"not_supported" => array(
						'hide_label',
						'caption',
						'required',
						'entry_list'
					)
				)
			),
			'phone_better'     => array(
				"field"       => __( 'Phone Number (Better)', 'qcformbuilder-forms' ),
				"description" => __( 'Phone number with advanced options and international formatting', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/generic-input.php",
				"category"    => __( 'Basic', 'qcformbuilder-forms' ),
				'icon'          => WFBCORE_URL . 'assets/build/images/mobile.svg',
				"setup"       => array(
					"template" => WFBCORE_PATH . "fields/phone_better/config.php",
					"preview"  => WFBCORE_PATH . "fields/phone_better/preview.php",
					"default"  => array(
						'default' => '',

					)
				),
				"scripts"     => array(
					WFBCORE_URL . 'fields/phone_better/assets/js/intlTelInput.min.js',
				),
				"styles"      => array(
					WFBCORE_URL . 'fields/phone_better/assets/css/intlTelInput.css'
				),
			),
			
			'number'            => array(
				"field"       => __( 'Number', 'qcformbuilder-forms' ),
				"description" => __( 'Number', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/generic-input.php",
				"category"    => __( 'Basic', 'qcformbuilder-forms' ),
				"setup"       => array(
					"preview"  => WFBCORE_PATH . "fields/number/preview.php",
					"template" => WFBCORE_PATH . "fields/number/config.php"
				)
			),
			'phone'            => array(
				"field"       => __( 'Phone Number (Basic)', 'qcformbuilder-forms' ),
				"description" => __( 'Phone number with masking', 'qcformbuilder-forms' ),
				'icon'          => WFBCORE_URL . 'assets/build/images/volume-control-phone.svg',
				"file"        => WFBCORE_PATH . "fields/generic-input.php",
				"category"    => __( 'Basic', 'qcformbuilder-forms' ),
				"setup"       => array(
					"template" => WFBCORE_PATH . "fields/phone/config.php",
					"preview"  => WFBCORE_PATH . "fields/phone/preview.php",
					"default"  => array(
						'default' => '',
						'type'    => 'local',
						'custom'  => '(999)999-9999'
					)
				)
			),
			/*
			'paragraph'        => array(
				"field"       => __( 'Paragraph Textarea', 'qcformbuilder-forms' ),
				"description" => __( 'Paragraph Textarea', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/paragraph/field.php",
				"category"    => __( 'Basic', 'qcformbuilder-forms' ),
				"setup"       => array(
					"template" => WFBCORE_PATH . "fields/paragraph/config_template.php",
					"preview"  => WFBCORE_PATH . "fields/paragraph/preview.php",
					"default"  => array(
						'rows' => '4'
					),
				)
			),
			'wysiwyg'          => array(
				"field"       => __( 'Rich Editor', 'qcformbuilder-forms' ),
				"description" => __( 'TinyMCE WYSIWYG editor', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/wysiwyg/field.php",
				'icon'          => WFBCORE_URL . 'assets/build/images/align-justify.svg',
				"category"    => __( 'Basic', 'qcformbuilder-forms' ),
				"setup"       => array(
					"template" => WFBCORE_PATH . "fields/wysiwyg/config_template.php",
					"preview"  => WFBCORE_PATH . "fields/wysiwyg/preview.php",
				),
				"scripts"     => array(
					WFBCORE_URL . 'fields/wysiwyg/wysiwyg.js'
				),
				"styles"      => array(
					WFBCORE_URL . "fields/wysiwyg/wysiwyg.min.css",
				),
			),
			
			'url'            => array(
				"field"       => __( 'URL', 'qcformbuilder-forms' ),
				'icon'          => WFBCORE_URL . 'assets/build/images/chain.svg',
				"description" => __( 'URL input for website addresses', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/generic-input.php",
				"category"    => __( 'Basic', 'qcformbuilder-forms' ),
				"setup"       => array(
					"preview"  => WFBCORE_PATH . "fields/url/preview.php",
					"template" => WFBCORE_PATH . "fields/url/config.php"
				)
			),
			
			//eCommerce
			'credit_card_number' => array(
				"field"       => __( 'Credit Card Number', 'qcformbuilder-forms' ),
				"description" => __( 'Credit Card Number With Validation', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/generic-input.php",
				"category"    => __( 'eCommerce', 'qcformbuilder-forms' ),
				'icon'        => WFBCORE_URL . 'assets/build/images/credit-card.svg',
				"setup"       => array(
					"template" => WFBCORE_PATH . "fields/credit_card_number/config.php",
					"preview"  => WFBCORE_PATH . "fields/credit_card_number/preview.php"
				),
				"scripts" => array(
					WFBCORE_URL . 'fields/credit_card_number/credit-card.js'
				)
			),
			'credit_card_exp' => array(
				"field"       => __( 'Credit Card Expiration Date', 'qcformbuilder-forms' ),
				"description" => __( 'Credit Card Expiration Date With Validation', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/generic-input.php",
				'icon'        => WFBCORE_URL . 'assets/build/images/credit-card.svg',
				"category"    => __( 'eCommerce', 'qcformbuilder-forms' ),
				"setup"       => array(
					"template" => WFBCORE_PATH . "fields/credit_card_exp/config.php",
					"preview"  => WFBCORE_PATH . "fields/credit_card_exp/preview.php"
				),
				"scripts" => array(
					WFBCORE_URL . 'fields/credit_card_number/credit-card.js'
				)
			),
			'credit_card_cvc' => array(
				"field"       => __( 'Credit Card CVC', 'qcformbuilder-forms' ),
				"description" => __( 'Credit Card CVC With Validation', 'qcformbuilder-forms' ),
				'icon'        => WFBCORE_URL . 'assets/build/images/credit-card.svg',
				"file"        => WFBCORE_PATH . "fields/generic-input.php",
				"category"    => __( 'eCommerce', 'qcformbuilder-forms' ),
				"setup"       => array(
					"template" => WFBCORE_PATH . "fields/credit_card_cvc/config.php",
					"preview"  => WFBCORE_PATH . "fields/credit_card_cvc/preview.php"
				),
				"scripts" => array(
					WFBCORE_URL . 'fields/credit_card_number/credit-card.js'
				)
			),


			//special
			'calculation'      => array(
				"field"       => __( 'Calculation', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/calculation/field.php",
				"handler"     => array( Qcformbuilder_Forms::get_instance(), "run_calculation" ),
				'icon'          => WFBCORE_URL . 'assets/build/images/calculator.svg',
				"category"    => __( 'Special', 'qcformbuilder-forms' ),
				"description" => __( 'Calculate values', 'qcformbuilder-forms' ),
				"setup"       => array(
					"template" => WFBCORE_PATH . "fields/calculation/config.php",
					"preview"  => WFBCORE_PATH . "fields/calculation/preview.php",
					"default"  => array(
						'element' => 'h3',
						'classes' => 'total-line',
						'before'  => __( 'Total', 'qcformbuilder-forms' ) . ':',
						'after'   => ''
					),

				),
			),
			'range_slider'     => array(
				"field"       => __( 'Range Slider', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/range_slider/field.php",
				"category"    => __( 'Special', 'qcformbuilder-forms' ),
				"description" => __( 'Range Slider input field', 'qcformbuilder-forms' ),
				"setup"       => array(
					"template" => WFBCORE_PATH . "fields/range_slider/config.php",
					"preview"  => WFBCORE_PATH . "fields/range_slider/preview.php",
					"default"  => array(
						'default'      => 1,
						'step'         => 1,
						'min'          => 0,
						'max'          => 100,
						'showval'      => 1,
						'suffix'       => '',
						'prefix'       => '',
						'color'        => '#00ff00',
						'handle'       => '#ffffff',
						'handleborder' => '#cccccc',
						'trackcolor'   => '#e6e6e6'
					),
				),
				"styles"      => array(
					WFBCORE_URL . "fields/range_slider/rangeslider.min.css",
				),
				"scripts"      => array(
					WFBCORE_URL . "fields/range_slider/rangeslider.min.js",
				),
			),
			'star_rating'      => array(
				"field"       => __( 'Star Rating', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/star-rate/field.php",
				"category"    => __( 'Special', 'qcformbuilder-forms' ),
				'icon'          => WFBCORE_URL . 'assets/build/images/star.svg',
				"description" => __( 'Star rating input for feedback', 'qcformbuilder-forms' ),
				"viewer"      => array( Qcformbuilder_Forms::get_instance(), 'star_rating_viewer' ),
				"setup"       => array(
					"template" => WFBCORE_PATH . "fields/star-rate/config.php",
					"preview"  => WFBCORE_PATH . "fields/star-rate/preview.php",
					"default"  => array(
						'number'      => 5,
						'space'       => 3,
						'size'        => 13,
						'color'       => '#FFAA00',
						'track_color' => '#AFAFAF',
						'type'        => 'star',
					),
				),
				"scripts"     => array(
					WFBCORE_URL . "fields/star-rate/jquery.raty.js",
				),
				"styles"      => array(
					WFBCORE_URL . "fields/star-rate/wfb-raty.css",
				),
			),
			'utm' => array(
				'field'       => __( 'UTM', 'qcformbuilder-forms' ),
				'file'        => WFBCORE_PATH . 'fields/utm/field.php',
				'category'    => __( 'Special', 'qcformbuilder-forms' ),
				'description' => __( 'Capture all UTM tags', 'qcformbuilder-forms' ),
				'setup'       => array(
					'template'      => WFBCORE_PATH . 'fields/utm/config.php',
					'preview'       => WFBCORE_PATH . 'fields/utm/preview.php',
					'not_supported' => array(
						'hide_label',
						'caption',
						'required',
					)
				),
				'handler'     => array( 'Qcformbuilder_Forms_Field_Utm', 'handler' )
			),
            'gdpr' => array(
                "field"       => __( 'Consent Field', 'qcformbuilder-forms' ),
                "description" => __( 'Record consent to collect personally identifying information (PII).', 'qcformbuilder-forms' ),
                "file"        => WFBCORE_PATH . "fields/gdpr/field.php",
                "category"    => __( 'Special', 'qcformbuilder-forms' ),
                "setup"       => array(
                    "template" => WFBCORE_PATH . "fields/gdpr/config_template.php",
                    "preview"  => WFBCORE_PATH . "fields/gdpr/preview.php",
                    "not_supported" => array(
                        'caption',
                        'required',
                    )
                ),

            ),
			//file
			'file'             => array(
				"field"       => __( 'File', 'qcformbuilder-forms' ),
				"description" => __( 'Basic HTML5 File Uploader', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/file/field.php",
				'icon'          => WFBCORE_URL . 'assets/build/images/cloud-upload.svg',
				"viewer"      => array( Qcformbuilder_Forms::get_instance(), 'handle_file_view' ),
				"category"    => __( 'File', 'qcformbuilder-forms' ),
				"setup"       => array(
					"preview"  => WFBCORE_PATH . "fields/file/preview.php",
					"template" => WFBCORE_PATH . "fields/file/config_template.php"
				)
			),
			'advanced_file'    => array(
				"field"       => __( 'Advanced File Uploader (1.0)', 'qcformbuilder-forms' ),
				'icon'          => WFBCORE_URL . 'assets/build/images/cloud-upload.svg',
				"description" => __( 'File upload field with more features than standard HTML5 input.', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/advanced_file/field.php",
				"viewer"      => array( Qcformbuilder_Forms::get_instance(), 'handle_file_view' ),
				"category"    => $deprecated,
				"setup"       => array(
					"preview"  => WFBCORE_PATH . "fields/advanced_file/preview.php",
					"template" => WFBCORE_PATH . "fields/advanced_file/config_template.php"
				),
				"scripts"     => array(
					WFBCORE_URL . 'fields/advanced_file/uploader.min.js'
				),

			),
			*/
			//content
			'html'             => array(
				"field"       => __( 'HTML', 'qcformbuilder-forms' ),
				"description" => __( 'Add text/html content', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/html/field.php",
				"category"    => __( 'Content', 'qcformbuilder-forms' ),
				"icon"        => WFBCORE_URL . "fields/html/icon.png",
				"capture"     => false,
				"setup"       => array(
					"preview"       => WFBCORE_PATH . "fields/html/preview.php",
					"template"      => WFBCORE_PATH . "fields/html/config_template.php",
					"not_supported" => array(
						'hide_label',
						'caption',
						'required',
						'entry_list'
					)
				)
			),
			/*
			'summary'             => array(
				"field"       => __( 'Summary', 'qcformbuilder-forms' ),
				"description" => __( 'Live updating summary of submission', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/summary/field.php",
				"category"    => __( 'Content', 'qcformbuilder-forms' ),
				'icon'          => WFBCORE_URL . 'assets/build/images/list.svg',
				"capture"     => false,
				"setup"       => array(
					"preview"       => WFBCORE_PATH . "fields/summary/preview.php",
					"template"      => WFBCORE_PATH . "fields/summary/config.php",
					"not_supported" => array(
						'required',
						'entry_list'
					)
				)
			),
			'section_break'    => array(
				"field"       => __( 'Section Break', 'qcformbuilder-forms' ),
				"description" => __( 'An HR tag to separate sections of your form.', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/section-break/section-break.php",
				"category"    => __( 'Content', 'qcformbuilder-forms' ),
				"capture"     => false,
				"setup"       => array(
					"template"      => WFBCORE_PATH . "fields/section-break/config.php",
					"not_supported" => array(
						'hide_label',
						'caption',
						'required',
						'entry_list'
					)
				)
			),
			*/
			//select
			'dropdown'         => array(
				"field"       => __( 'Select Option', 'qcformbuilder-forms' ),
				"description" => __( 'Display Options as button', 'qcformbuilder-forms' ),
				'icon'          => WFBCORE_URL . 'assets/build/images/plus.svg',
				"file"        => WFBCORE_PATH . "fields/dropdown/field.php",
				"category"    => __( 'Select', 'qcformbuilder-forms' ),
				"options"     => "single",
				"static"      => true,
				"viewer"      => array( Qcformbuilder_Forms::get_instance(), 'filter_options_calculator' ),
				"setup"       => array(
					"template" => WFBCORE_PATH . "fields/dropdown/config_template.php",
					"preview"  => WFBCORE_PATH . "fields/dropdown/preview.php",
					"default"  => array(),
				)
			),
			'checkbox'         => array(
				"field"       => __( 'Checkbox', 'qcformbuilder-forms' ),
				"description" => __( 'Checkbox', 'qcformbuilder-forms' ),
				'icon'          => WFBCORE_URL . 'assets/build/images/plus.svg',
				"file"        => WFBCORE_PATH . "fields/checkbox/field.php",
				"category"    => __( 'Select', 'qcformbuilder-forms' ),
				"options"     => "multiple",
				"static"      => true,
				"viewer"      => array( Qcformbuilder_Forms::get_instance(), 'filter_options_calculator' ),
				"setup"       => array(
					"preview"  => WFBCORE_PATH . "fields/checkbox/preview.php",
					"template" => WFBCORE_PATH . "fields/checkbox/config_template.php",

				),
			),
			/*
			
			'radio'            => array(
				"field"       => __( 'Radio', 'qcformbuilder-forms' ),
				"description" => __( 'Radio', 'qcformbuilder-forms' ),
				'icon'          => WFBCORE_URL . 'assets/build/images/plus.svg',
				"file"        => WFBCORE_PATH . "fields/radio/field.php",
				"category"    => __( 'Select', 'qcformbuilder-forms' ),
				"options"     => true,
				"static"      => true,
				"viewer"      => array( Qcformbuilder_Forms::get_instance(), 'filter_options_calculator' ),
				"setup"       => array(
					"preview"  => WFBCORE_PATH . "fields/radio/preview.php",
					"template" => WFBCORE_PATH . "fields/radio/config_template.php",
				)
			),
			'filtered_select2' => array(
				"field"       => __( 'Autocomplete', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/select2/field/field.php",
				'icon'          => WFBCORE_URL . 'assets/build/images/plus.svg',
				"category"    => __( 'Select', 'qcformbuilder-forms' ),
				"description" => 'Select2 dropdown',
				"options"     => "multiple",
				"static"      => true,
				"setup"       => array(
					"template" => WFBCORE_PATH . "fields/select2/field/config.php",
					"preview"  => WFBCORE_PATH . "fields/select2/field/preview.php",
				),
				"scripts"     => array(
					WFBCORE_URL . "fields/select2/js/select2.min.js",
				),
				"styles"      => array(
					WFBCORE_URL . "fields/select2/css/select2.css",
				)
			),
			'date_picker'      => array(
				"field"       => __( 'Date Picker', 'qcformbuilder-forms' ),
				"description" => __( 'Date Picker', 'qcformbuilder-forms' ),
				'icon'          => WFBCORE_URL . 'assets/build/images/plus.svg',
				"file"        => WFBCORE_PATH . "fields/date_picker/datepicker.php",
				"category"    => __( 'Select', 'qcformbuilder-forms' ),
				"setup"       => array(
					"preview"  => WFBCORE_PATH . "fields/date_picker/preview.php",
					"template" => WFBCORE_PATH . "fields/date_picker/setup.php",
					"default"  => array(
						'format' => 'yyyy-mm-dd'
					),
				),
				"styles"     => array(
					WFBCORE_URL . "fields/date_picker/css/datepicker.css",
				),
				"scripts"      => array(
					WFBCORE_URL . "fields/date_picker/wfb-datepicker.js",
				)
			),
			'toggle_switch'    => array(
				"field"       => __( 'Toggle Switch', 'qcformbuilder-forms' ),
				"description" => __( 'Toggle Switch', 'qcformbuilder-forms' ),
				"category"    => __( 'Select', 'qcformbuilder-forms' ),
				'icon'          => WFBCORE_URL . 'assets/build/images/plus.svg',
				"file"        => WFBCORE_PATH . "fields/toggle_switch/field.php",
				"viewer"      => array( Qcformbuilder_Forms::get_instance(), 'filter_options_calculator' ),
				"options"     => "single",
				"static"      => true,
				"setup"       => array(
					"template" => WFBCORE_PATH . "fields/toggle_switch/config_template.php",
					"preview"  => WFBCORE_PATH . "fields/toggle_switch/preview.php",
				),
			),
			'color_picker'     => array(
				"field"       => __( 'Color Picker', 'qcformbuilder-forms' ),
				"description" => __( 'Color Picker', 'qcformbuilder-forms' ),
				'icon'          => WFBCORE_URL . 'assets/build/images/paint-brush.svg',
				"category"    => __( 'Select', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/generic-input.php",
				"setup"       => array(
					"preview"  => WFBCORE_PATH . "fields/color_picker/preview.php",
					"template" => WFBCORE_PATH . "fields/color_picker/setup.php",
					"default"  => array(
						'default' => '#FFFFFF'
					),
				),
				'styles' => array(
					WFBCORE_URL . 'fields/color_picker/minicolors.min.css'
				),
				'scripts' => array(
					WFBCORE_URL . 'fields/color_picker/minicolors.js'
				)
			),
			'states'           => array(
				"field"       => __( 'State/ Province Select', 'qcformbuilder-forms' ),
				'icon'          => WFBCORE_URL . 'assets/build/images/plus.svg',
				"description" => __( 'Dropdown select for US states and Canadian provinces.', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/states/field.php",
				"category"    => __( 'Select', 'qcformbuilder-forms' ),
				"placeholder" => false,
				"setup"       => array(
					"template" => WFBCORE_PATH . "fields/states/config_template.php",
					"preview"  => WFBCORE_PATH . "fields/states/preview.php",
					"default"  => array(),
				)
			),


			//discontinued
			'recaptcha'        => array(
				"field"       => __( 'reCAPTCHA', 'qcformbuilder-forms' ),
				"description" => __( 'reCAPTCHA anti-spam field', 'qcformbuilder-forms' ),
				"file"        => WFBCORE_PATH . "fields/recaptcha/field.php",
				"category"    => $deprecated,
				"handler"     => array( Qcformbuilder_Forms::get_instance(), 'captcha_check' ),
				"capture"     => false,
				"setup"       => array(
					"template"      => WFBCORE_PATH . "fields/recaptcha/config.php",
					"preview"       => WFBCORE_PATH . "fields/recaptcha/preview.php",
					"not_supported" => array(
						'caption',
						'required'
					),
				)
			),
			*/

		);

		return $internal_fields;
	}

}
