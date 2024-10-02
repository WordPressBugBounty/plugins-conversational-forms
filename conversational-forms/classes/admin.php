<?php
/**
 * Qcformbuilder Forms.
 *
 * @package Caldera_Forms Modified by QuantumCloud
 * @author    David <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer
 */
/**
 * Qcformbuilder_Forms Plugin class.
 * @package Caldera_Forms Modified by QuantumCloud
 * @author  David Cramer <david@digilab.co.za>
 */

class Qcformbuilder_Forms_Admin {

	/**
	 * @var     string
	 */
	const VERSION = WFBCORE_VER;

	/**
	 * GET var for from ID to edit
	 *
	 * @since 1.5.3
	 *
	 * @var string
	 */
	const EDIT_KEY = 'edit';

	/**
	 * GET var for revision ID to edit
	 *
	 * @since 1.5.3
	 *
	 * @var string
	 */
	const REVISION_KEY = 'wfb_revision';

	/**
	 * GET var for form ID when doing a preview
	 *
	 * @since 1.5.3
	 *
	 * @var string
	 */
	const PREVIEW_KEY = 'wfb_preview';

	/**
	 * GET var for what to order forms by
	 *
	 * @since 1.5.6
	 *
	 * @var string
	 */
	const ORDERBY_KEY = 'wfb_orderby';


	/**
	 * @var      string
	 */
	protected $plugin_slug = 'qcformbuilder-forms';


	/**
	 * @var      string
	 */
	protected $screen_prefix = array();

	/**
	 * @var      string
	 */
	protected $sub_prefix = null;

	/**
	 * @var      string
	 */
	protected $addons = array();

	/**
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Holds admin notices
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	private static $admin_notices;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 */
	private function __construct() {

		add_filter( 'all_plugins', array( $this, 'prepare_filter_addons' ) );

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add Admin menu page
		add_action( 'admin_menu', array( $this, 'register_admin_page' ), 9 );

		// Add admin scritps and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_stylescripts' ), 1 );

		// add element & fields filters
		add_filter('qcformbuilder_forms_get_panel_extensions', array( $this, 'get_panel_extensions'), 1);
		add_filter('qcformbuilder_forms_entry_viewer_buttons', array( $this, 'set_viewer_buttons'),10, 4);
		add_filter('qcformbuilder_forms_entry_editor_buttons', array( $this, 'set_editor_buttons'),10, 4);

		// action

		add_action('qcformbuilder_forms_admin_templates', array( $this, 'get_admin_templates'),1);
		add_action('qcformbuilder_forms_entry_meta_templates', array( $this, 'get_admin_meta_templates'),1);

		add_action( 'init', array( $this, 'save_form') );
		//add_action( 'media_buttons', array($this, 'shortcode_insert_button' ), 11 );
		add_filter( 'wp_fullscreen_buttons', array($this, 'shortcode_insert_button_fs' ), 11 );

		// filter for adding presets
		add_filter( 'qcformbuilder_forms_field_option_presets', array($this, 'load_option_presets' ) );

		if( current_user_can( Qcformbuilder_Forms::get_manage_cap( 'create' ) ) ){
			// create forms
			add_action("wp_ajax_wfb_create_form", array( $this, 'create_form') );
		}

		if( current_user_can( Qcformbuilder_Forms::get_manage_cap( 'admin' ) ) ) {
			add_action( "wp_ajax_toggle_form_state", array( $this, 'toggle_form_state' ) );
			add_action( "wp_ajax_save_wfb_setting", array( $this, 'save_wfb_setting' ) );
			add_action( "wp_ajax_wfb_dismiss_pointer", array( $this, 'update_pointer' ) );
			add_action( "wp_ajax_wfb_bulk_action", array( $this, 'bulk_action' ) );
		}
		add_action("wp_ajax_wfb_get_form_preview", array( $this, 'get_form_preview') );
        add_action( 'admin_footer', array( $this, 'add_shortcode_inserter'));

		$this->addons = apply_filters( 'qcformbuilder_forms_get_active_addons', array() );

		add_action('admin_footer-edit.php', array( $this, 'render_editor_template')); // Fired on the page with the posts table
		add_action('admin_footer-post.php', array( $this, 'render_editor_template')); // Fired on post edit page
		add_action('admin_footer-post-new.php', array( $this, 'render_editor_template')); // Fired on add new post page

		add_action( 'qcformbuilder_forms_new_form_template_end', array( $this, 'load_new_form_templates') );

		add_action( 'qcformbuilder_forms_prerender_edit', array( __CLASS__, 'easy_pods_auto_populate' ) );

		add_action( 'init', array( 'Qcformbuilder_Forms_Admin_Resend', 'watch_for_resend' ) );

        add_action( 'qcformbuilder_forms_admin_footer', array( 'Qcformbuilder_Forms_Email_Settings', 'ui' ) );

		add_action(  'qcformbuilder_forms_admin_init', array( __CLASS__ , 'init_privacy_settings' ) );

        add_action( 'admin_init', array( __CLASS__, 'add_suggested_privacy_content' ), 35 );

		/**
		 * Runs after Qcformbuilder Forms admin is initialized
		 *
		 * @since 1.3.5.3
		 */
		do_action( 'qcformbuilder_forms_admin_init' );

		/** Adding anything to this constructor after qcformbuilder_forms_admin_init action is a violation of intergalactic law */
	}

	public function render_editor_template(){
		?>
		<script type="text/html" id="tmpl-editor-qcformbuilder-forms">
			<# if ( data.html ) { #>
				{{{ data.html }}}
				<# } else { #>
					<div class="wpview-error">
						<div class="dashicons dashicons-wfb-logo"></div><p style="font-size: 13px;"><?php esc_html_e( 'Invalid Form.', 'qcformbuilder-forms' ); ?></p>
					</div>
					<# } #>
		</script>
		<?php

	}

	/**
	 * Returns the array of option presets for option based fields.
	 *
	 * @since 1.4.0
	 * @param array $presets current array of presets
	 *
	 * @return    array array of presets
	 */
	 public function load_option_presets( $presets ){

	 	$internal = array_merge( $presets, array(
	 		'countries_iso_alpha_2' => array(
	 			'name' => __( 'Countries (ISO Alpha-2)', 'qcformbuilder-forms'),
	 			'data' => file_get_contents( WFBCORE_PATH . 'includes/presets/countries_iso_alpha_2.txt' ),
	 		),
			'countries_names' => array(
				'name' => __( 'Countries (Names Only)', 'qcformbuilder-forms'),
				'data' => file_get_contents( WFBCORE_PATH . 'includes/presets/countries_names.txt' ),
			),
			'continents' => array(
				'name' => __( 'Continents', 'qcformbuilder-forms'),
				'data' => array(
					"Africa",
					"Antarctica",
					"Asia",
					"Australia",
					"Europe",
					"North America",
					"South America",
				),
			),
			'canadian_provinces_territories' => array(
				'name' => __( 'Canadian Provinces & Territories', 'qcformbuilder-forms'),
				'data' => array(
					"Alberta",
					"British Columbia",
					"Manitoba",
					"New Brunswick",
					"Newfoundland and Labrador",
					"Northwest Territories",
					"Nova Scotia",
					"Nunavut",
					"Ontario",
					"Prince Edward Island",
					"Quebec",
					"Saskatchewan",
					"Yukon",
				),
			),
		    'us_states' => array(
			    'name' => __( 'US States', 'qcformbuilder-forms'),
			    'data' => file_get_contents( WFBCORE_PATH . 'includes/presets/us_states.txt' ),
		    ),
	    ));

	 	return $internal;
	 }

	/**
	 * Returns the array of form templates.
	 *
	 * @since 1.2.3
	 *
	 * @return    array The form templates
	 */
	public static function internal_form_templates(){

		$internal_templates = array(
		
		'simple-feedback-example' => array(
			'name'     => esc_html__( 'Simple Feedback', 'qcformbuilder-forms' ),
			'template' => include WFBCORE_PATH . 'includes/templates/simple-feedback-example.php'
		),
		'simple-survey-example' => array(
			'name'     => esc_html__( 'Simple Survey', 'qcformbuilder-forms' ),
			'template' => include WFBCORE_PATH . 'includes/templates/simple-survey-example.php'
		),
		
		'simple_booking_form_example' => array(
			'name'     => esc_html__( 'Simple Booking', 'qcformbuilder-forms' ),
			'template' => include WFBCORE_PATH . 'includes/templates/simple-booking-form-example.php'
		),
		
		

		);

		/**
		 * Filter form templates
		 *
		 * @since 1.2.3
		 *
		 * @param array $internal_templates Form templates
		 */
		return apply_filters( 'qcformbuilder_forms_get_form_templates', $internal_templates );

	}

	public function load_new_form_templates(){

		$form_templates = self::internal_form_templates();

		?>
		<div class="wfb-templates-wrapper">
			<?php
			$selected_field = '';//' checked="checked"';
			$selected_template = '';//' selected';

			foreach( $form_templates as $template_slug => $template ){
				if( !empty( $template['template'] ) && !empty( $template['name'] ) ){

					echo '<label class="qcformbuilder-grid wfb-form-template' . esc_html( $selected_template ) . '">';
						echo '<small>' . esc_html( $template['name'] ) . '</small>';

						echo '<input type="radio" name="template" value="' . esc_html( $template_slug ) . '" class="wfb-template-select"' . esc_html( $selected_field ) . '>';


						// check a layout exists
						if( !empty( $template['preview'] ) ){
							echo '<img src="' . esc_url( $template['preview'] ) . '"></label>';
							continue;
						}
						if( empty( $template['template']['layout_grid'] ) || empty( $template['template']['layout_grid']['structure'] ) || empty( $template['template']['layout_grid']['fields'] ) ){
							echo '<p class="description" style="padding: 50px 0px; text-align: center;">' . esc_html__( 'Preview not available', 'qcformbuilder-forms' ) . '</p></label>';
							continue;
						}

						$struct = explode('|', $template['template']['layout_grid']['structure'] );

						foreach ($struct as $row_num=>$row) {

							$columns = explode( ':', $row );
							echo '<div class="row" style="margin: 6px 0px;">';
								foreach ($columns as $column_num=>$column) {
									//var_dump( $template['template']['layout_grid']['fields'][ ( $row_num+1) . ':' . ( $column_num+1) ] );
									$fields = array_keys( $template['template']['layout_grid']['fields'], ( $row_num+1) . ':' . ( $column_num+1) );
									echo '<div class="col-sm-' . esc_html( $column ) . '" style="padding: 0px 3px;">';
									echo '<div class="wfb-template-column">';
									foreach( $fields as $field ){
										if( isset( $template['template']['fields'][ $field ] ) ){
											if( $template['template']['fields'][ $field ]['type'] == 'button'){
												echo '<small class="wfb-preview-field wfb-preview-button">' . esc_html( $template['template']['fields'][ $field ]['label'] ) .'</small>';
											}elseif( $template['template']['fields'][ $field ]['type'] == 'html'){
												echo '<small class="wfb-preview-field wfb-preview-field-html"></small>';
											}elseif( $template['template']['fields'][ $field ]['type'] == 'paragraph'){
												echo '<small class="wfb-preview-field" style="height:50px;">' . esc_html( $template['template']['fields'][ $field ]['label'] ) .'</small>';
											}elseif( $template['template']['fields'][ $field ]['type'] == 'hidden'){
												// nope- nothing
											}else{
												echo '<small class="wfb-preview-field">' . esc_html( $template['template']['fields'][ $field ]['label'] ) .'</small>';
											}
										}
									}
									echo '</div>';
									echo '</div>';
								}

							echo '</div>';
						}

					echo '</label>';
					// unset selection
					$selected_field = null;
					$selected_template = null;
				}
			}

			?>
			<label class="qcformbuilder-grid wfb-form-template">
				<small><?php echo esc_html__( 'Blank Form', 'qcformbuilder-forms' ); ?></small>
				<input type="radio" name="template" value="" class="wfb-template-select">
			</label>
			<div class="qcformbuilder-grid wfb-form-create" style="display:none; visibility: hidden;" aria-hidden="true">
				<div class="wfb-template-title"></div>

				<div class="qcformbuilder-config-field">
					<input type="text" class="new-form-name block-input field-config" name="name" value="" required="required" autofocus="true" autocomplete="off" placeholder="<?php echo esc_html__('Form Name', 'qcformbuilder-forms' ); ?>">
				</div>

				<button type="button" class="wfb-change-template-button"><span class="dashicons dashicons-arrow-left-alt"></span> <?php echo esc_html__( 'Change Template', 'qcformbuilder-forms' ); ?></button>
				<button type="button" class="wfb-create-form-button ajax-trigger"
				 data-action="wfb_create_form"
				 data-active-class="disabled"
				 data-load-class="disabled"
				 data-callback="new_form_redirect"
				 data-before="serialize_modal_form"
				 data-modal-autoclose="new_form"
				 data-nonce=<?php echo esc_attr( wp_create_nonce( 'wfb_create_form' ) ); ?>
				><?php echo esc_html__( 'Create Form', 'qcformbuilder-forms' ); ?> <span class="dashicons dashicons-yes"></span><span class="spinner"></span></button>

			</div>

		</div>
		<?php
		/**
		 * Runs at the bottom of the new form modal
		 *
		 * Use to add extra buttons, etc.
		 *
		 * @since 1.4.2
		 */
		do_action( 'qcformbuilder_forms_new_form_modal_bottom' );
	}

	public function get_form_preview(){
		global $post;
		add_filter('qcformbuilder_forms_render_form_element', array( $this, 'set_preview_form_element') );
		$post = get_post( (int) $_POST['post_id'] );
		if( isset($_POST['atts']['named']['id']) ){
			$form = $_POST['atts']['named']['id'];
		}elseif( isset($_POST['atts']['named']['name']) ){
			$form = $_POST['atts']['named']['name'];
		}

		add_filter('qcformbuilder_forms_get_form-' . $form, array( $this, 'set_preview_get_form'),100 );

		$atts = $_POST['atts']['named'];
		$atts['preview'] = true;

		if( !empty( $form ) ){
			ob_start();
			wp_head();
			echo esc_html( Qcformbuilder_Forms::render_form( $form ) );
			wp_print_footer_scripts();
			$html = ob_get_clean();
		}
		$out = array();
		if( !empty( $html ) ){
			$out['html'] = $html;
		}

		wp_send_json_success( $out );
	}
	public function set_preview_get_form( $form ){
		$form['form_ajax'] = false;
		$form['settings']['responsive']['break_point'] = 'xs';
		return $form;
	}
	public function set_preview_form_element($element){
		return 'div';
	}

	public function prepare_filter_addons($plugins){
		global $wp_list_table, $status;

		if( !empty( $this->addons ) ){
			$addons = array();
			foreach( $this->addons as $addon ){
				$plugin_slug = basename( dirname( $addon['file'] ) ) .'/'.basename( $addon['file'] );
				if( isset( $plugins[$plugin_slug] ) ){
					if( isset( $addon['slug'] ) ){
						$plugins[$plugin_slug]['slug'] = $addon['slug'];
					}
				}
			}
		}
		if( isset( $_REQUEST['plugin_status'] ) && $_REQUEST['plugin_status'] === 'qcformbuilder_forms' ){
			$status = 'qcformbuilder_forms';
		}

		return $plugins;
	}

	public function bulk_action(){

		// first validate
		self::verify_ajax_action();


		if(empty($_POST['do'])){
			die;
		}

		$do_action = strtolower( $_POST['do'] );

		switch ( $do_action ) {
			case 'active':
			case 'trash':
			case 'delete':
				global $wpdb;

				$result = false;
				$items = array();
				$selectors = array();
				foreach ( (array) $_POST[ 'items' ] as $item_id ) {
					$items[]     = (int) $item_id;
					$selectors[] = '#entry_row_' . (int) $item_id;
				}

				switch ( $do_action ) {
					case 'delete':
						if( current_user_can( Qcformbuilder_Forms::get_manage_cap( 'delete-entry' ) ) ){
							$result = Qcformbuilder_Forms_Entry_Bulk::delete_entries( $items );
						}
						$out['status'] = 'reload';
						wp_send_json( $out );
						break;

					default:
						if( current_user_can( Qcformbuilder_Forms::get_manage_cap( 'edit-entry' ) ) ){
							$result = Qcformbuilder_Forms_Entry_Bulk::change_status( $items, $do_action  );
						}
						break;
				}

				if( $result ){
					$out[ 'status' ]    = $do_action;
					$out[ 'undo' ]      = ( $do_action === 'trash' ? 'active' : esc_html_x( 'Trash', 'Verb: Action of moving to trash', 'qcformbuilder-forms' ) );
					$out[ 'undo_text' ] = ( $do_action === 'trash' ? esc_html__( 'Restore', 'qcformbuilder-forms' ) : esc_html_x( 'Trash', 'Verb: Action of moving to trash', 'qcformbuilder-forms' ) );

					$form             = strip_tags( $_POST[ 'form' ] );
					$out[ 'entries' ] = implode( ',', $selectors );
					$out[ 'total' ]   = Qcformbuilder_Forms_Entry_Bulk::count( $form, false );
					$out[ 'trash' ]   = Qcformbuilder_Forms_Entry_Bulk::count( $form, 'trash' );
					wp_send_json( $out );
				}
				exit();

				break;
			case 'export':

				$transientid = uniqid('cfe');
				Qcformbuilder_Forms_Transient::set_transient(  $transientid, $_POST['items'], 180 );
				$out['url'] = "admin.php?page=qcformbuilder-forms&export=" . $_POST['form'] . "&tid=" . $transientid;
				wp_send_json( $out );
				exit();
				break;
			default:
				# code...
				break;
		}
		exit();
	}

	/**
	 * Dismiss admin pointer
	 *
	 * @since unknown
	 *
	 * @uses "wp_ajax_wfb_dismiss_pointer" action
	 */
	public static function update_pointer(){
		if( ! isset( $_POST[ 'nonce' ] ) || ! wp_verify_nonce( $_POST[ 'nonce' ], 'wfb_dismiss_pointer' ) ){
			status_header( 500 );
			exit;
		}

		if ( ! empty( $_POST[ 'pointer' ] ) ) {
			add_user_meta( get_current_user_id(), 'wfb_pointer_' . $_POST[ 'pointer' ], array( 0 => NULL ) );
		}
		exit;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( $this->plugin_slug, FALSE, basename( WFBCORE_PATH ) . '/languages');
	}



	public static function add_shortcode_inserter(){

		$screen = get_current_screen();

		if($screen->base === 'post'){
			include WFBCORE_PATH . 'ui/insert_shortcode.php';
		}
	}

	public static function get_admin_templates(){
		include WFBCORE_PATH . 'ui/news_templates.php';
		include WFBCORE_PATH . 'ui/admin_templates.php';
	}
	public static function get_admin_meta_templates(){

		$processors = $processors = Qcformbuilder_Forms_Processor_Load::get_instance()->get_processors();
		if(!empty($processors)){
			foreach($processors as $processor_type=>$processor_config){
				if( isset( $processor_config['meta_template'] ) && file_exists( $processor_config['meta_template'] ) ){
					echo "{{#if " . esc_attr( $processor_type ) . "_template}}\r\n";
					echo "{{#each data}}\r\n";
					echo "{{#if title}}\r\n";
					echo "<h4>{{title}}</h4>\r\n";
					echo "{{/if}}\r\n";
					echo "{{#each entry}}\r\n";
					include $processor_config['meta_template'];
					echo "{{/each}}\r\n";
					echo "{{/each}}\r\n";
					echo "{{/if}}\r\n";
				}
			}
		}
	}

	/**
	 * Get the entry editor's buttons
	 *
	 * @since unknown
	 *
	 * @deprecated
	 */
	public static function get_entry_actions(){
		_deprecated_function( __FUNCTION__, 'Qcformbuilder_Forms_Entry_UI::get_entry_actions()', '1.5.0' );
		Qcformbuilder_Forms_Entry_UI::get_entry_actions();
	}

	/**
	 * Set buttons for entry viewer
	 *
	 * @since 1.4.0
	 *
	 * @uses "qcformbuilder_forms_entry_viewer_buttons" filter
	 *
	 * @param array $buttons
	 *
	 * @return array
	 */
	public static function set_viewer_buttons($buttons){

		$buttons[ 'close_panel' ] = array(
			'label'  => esc_html__( 'Close', 'qcformbuilder-forms' ),
			'config' => 'dismiss',
			'class'  => 'right'
		);

		if ( current_user_can( Qcformbuilder_Forms::get_manage_cap( 'edit-entry' ) ) ) {
			$buttons[ 'edit_entry' ] = array(
				'label'  => esc_html__( 'Edit Entry', 'qcformbuilder-forms' ),
				'config' => array(
					'data-trigger' => '#edit-entry-{{_entry_id}}'
				),
				'class'  => 'button-primary'
			);

		}


		return $buttons;
	}


	public static function set_editor_buttons($buttons){

		$buttons['submit_form'] = array(
			'label'		=>	esc_html__( 'Save Changes', 'qcformbuilder-forms' ),
			'config'	=>	array(
				"data-for" => "#view_entry_baldrickModalBody .qcformbuilder_forms_form"
			),
			'class'		=>	'right button-primary'
		);
		$buttons['view_entry'] = array(
			'label'		=>	esc_html__( 'View Entry', 'qcformbuilder-forms' ),
			'config'	=>	array(
				"data-for" => ".view-entry-btn.current-view"
			),
			'class'		=>	''
		);


		return $buttons;
	}

	/**
	 * Handles saving general settings
	 *
	 * @since unknown
	 *
	 * @uses "wp_ajax_save_wfb_setting" action
	 */
	public static function save_wfb_setting(){
		self::verify_ajax_action();
		if(empty($_POST['set'])){
			exit;
		}
		$style_includes = get_option( '_qcformbuilder_forms_styleincludes' );


		if( 'cdn_enable' == $_POST[ 'set' ] ){
			Qcformbuilder_Forms::settings()->get_cdn()->toggle_cdn_enable();

		}else{
			if(empty($style_includes[$_POST['set']])){
				$style_includes[$_POST['set']] = true;
			}else{
				$style_includes[$_POST['set']] = false;
			}
			update_option( '_qcformbuilder_forms_styleincludes', $style_includes);

		}

		$return_data = array_merge( $style_includes, array(
			'cdn_enable' => Qcformbuilder_Forms::settings()->get_cdn()->enabled()
		) );

		wp_send_json( $return_data );
		exit;
	}

	/**
	 * Insert shortcode media button
	 *
	 * Add shortcode insert button in classic editor
	 *
	 * Function is intentionally not static
	 *
	 * @since unknown
	 */
	public function shortcode_insert_button(){
		if( Qcformbuilder_Forms_Admin_Assets::is_woocommerce_page() ){
			return;
		}
		global $post;
		if(!empty($post)){
			echo "<a id=\"qcformbuilder-forms-form-insert\" title=\"". esc_attr__( 'Add Form to Page', 'qcformbuilder-forms' ) . "\" class=\"button qcformbuilder-forms-insert-button\" href=\"#inst\">\n";
			echo "	<img src=\"". esc_html( WFBCORE_URL ) . "assets/images/qcformbuilder-globe-logo-sm.png\" alt=\"". esc_attr__( 'Insert Form Shortcode' , 'qcformbuilder-forms') . "\" style=\"padding: 0px 2px 0px 0px; width: 16px; margin: -2px 0px 0px;\" /> ".esc_html__('Qcformbuilder Form', 'qcformbuilder-forms' )."\n";
			echo "</a>\n";
		}
	}


	function shortcode_insert_button_fs($buttons){

		$buttons['qcformbuilder-forms'] = array(
			"title"		=>	__( 'Add Form to Page', 'qcformbuilder-forms' ),
			"both"		=> true
		);
		return $buttons;
	}

	/**
	 * Change form's state
	 *
	 * @uses "wp_ajax_toggle_form_state" action
	 *
	 * @since unknown
	 */
	public static function toggle_form_state(){
		if( ! isset( $_POST[ 'nonce' ] ) || !wp_verify_nonce( $_POST[ 'nonce' ], 'toggle_form_state' ) ){
			wp_send_json_error( $_POST );
		}

		$forms = Qcformbuilder_Forms_Forms::get_forms( true );
		$form = sanitize_text_field( $_POST['form'] );
		$form = Qcformbuilder_Forms_Forms::get_form( $form );
		if( empty( $form ) || empty( $form['ID'] ) || empty( $forms[ $form['ID'] ]) ){
			wp_send_json_error( );
		}

		add_filter( 'qcformbuilder_forms_save_revision', '__return_false' );

		if ( ! empty( $form[ 'form_draft' ] ) ) {
			unset( $form['form_draft'] );
			unset( $forms[ $form['ID'] ]['form_draft'] );
			Qcformbuilder_Forms_Forms::form_state( $form );
			$state = 'active-form';
			$label = esc_html__( 'Disable', 'qcformbuilder-forms' );
		}else{
			Qcformbuilder_Forms_Forms::form_state( $form , false );
			$state = 'draft-form';
			$label = esc_html__( 'Enable', 'qcformbuilder-forms' );
		}

		add_filter( 'qcformbuilder_forms_save_revision', '__return_true' );


		wp_send_json_success( array( 'ID' => $form['ID'], 'state' => $state, 'label' => $label ) );
	}

	/**
	 * nonce verifier for ajax actions
	 *
	 * @since 1.3.2.1
	 */
	private static function verify_ajax_action(){
		if ( ! isset( $_POST['wfb_toolbar_actions'] ) || ! wp_verify_nonce( $_POST['wfb_toolbar_actions'], 'wfb_toolbar' ) || !check_admin_referer( 'wfb_toolbar', 'wfb_toolbar_actions' ) ) {
			wp_send_json_error( $_POST );
		}
	}

	/**
	 * Show entries in admin
	 *
	 * @deprecated 1.4.0 Use Qcformbuilder_Forms_Entry_UI::view_entries()
	 *
	 * @since unknown
	 */
	public static function browse_entries(){
		_deprecated_function( __FUNCTION__, '1.4.0', 'Qcformbuilder_Forms_Entry_UI::view_entries' );
		self::verify_ajax_action();
		if ( isset( $_POST[ 'page' ] ) && 0 < $_POST[ 'page' ] ) {
			$page = absint( $_POST[ 'page' ] );
		}else{
			$page = 1;
		}
		$entry_perpage = get_option( '_qcformbuilder_forms_entry_perpage', 20 );
		if ( isset( $_POST[ 'perpage' ] ) && 0 < $_POST[ 'perpage' ] ) {
			$perpage = absint( (int) $_POST[ 'perpage' ] );
			if( $entry_perpage != $perpage ){
				update_option( '_qcformbuilder_forms_entry_perpage', $perpage );
			}
		}else{
			$perpage = $entry_perpage;
		}

		if ( isset( $_POST[ 'status' ] ) ) {
			$status = strip_tags( $_POST[ 'status' ] );
		}else{
			$status = 'active';
		}

		$form = Qcformbuilder_Forms_Forms::get_form( $_POST['form'] );

		$data = self::get_entries( $form, $page, $perpage, $status );

		// set status output
		$data['is_' . $status ] = true;

		wp_send_json( $data );
		exit;


	}

	/**
	 * Get entries from a form
	 *
	 * @since 1.2.1
	 *
	 * @param string|array $form_or_id Form ID or form config.
	 * @param int $page Optional. Page of entries to get per page. Default is 1.
	 * @param int $perpage Optional. Number of entries per page. Default is 20.
	 * @param string $status Optional. Form status. Default is active.
	 *
	 * @return array
	 */
	public static function get_entries( $form_or_id, $page = 1, $perpage = 20, $status = 'active' ) {

		if ( is_string( $form_or_id ) ) {
			$form_or_id = Qcformbuilder_Forms_Forms::get_form( $form_or_id );
		}

		if ( isset( $form_or_id[ 'ID' ])) {
			$form_id = $form_or_id[ 'ID' ];
		}else{
			return;
		}

        global $form;
		$form = $form_or_id;

		global $wpdb;

		$field_labels = array();
		$backup_labels = array();
		$selects = array();


		$fields = array();
		if ( ! empty( $form[ 'fields' ] ) ) {
			foreach ( $form[ 'fields' ] as $fid => $field ) {
				$fields[ $field[ 'slug' ] ] = $field;

				if ( ! empty( $field[ 'entry_list' ] ) ) {
					$selects[] = "'" . $field[ 'slug' ] . "'";
					$field_labels[ $field[ 'slug' ] ] = $field[ 'label' ];
				}
				$has_vars = array();
				if ( ! empty( $form[ 'variables' ][ 'types' ] ) ) {
					$has_vars = $form[ 'variables' ][ 'types' ];
				}
				if ( ( count( $backup_labels ) < 4 && ! in_array( 'entryitem', $has_vars ) ) && in_array( $field[ 'type' ], array(
						'text',
						'email',
						'date',
						'name'
					) )
				) {
					// backup only first 4 fields
					$backup_labels[ $field[ 'slug' ] ] = $field[ 'label' ];
				}
			}
		}

		if ( empty( $field_labels ) ) {
			$field_labels = $backup_labels;
		}

		$entries = new Qcformbuilder_Forms_Entry_Entries( $form, $perpage );

		$data = array();

		$filter = null;

		$data[ 'trash' ]  = $entries->get_total( 'trash' );
		$data[ 'active' ] = $entries->get_total( 'active' );

		// set current total
		if ( ! empty( $status ) && isset( $data[ $status ] ) ) {
			$data[ 'total' ] = $entries->get_total( $status );
		} else {
			$data[ 'total' ] = $data[ 'active' ];
		}


		$data[ 'pages' ] = ceil( $data[ 'total' ] / $perpage );

		if ( ! empty( $page ) ) {
			$page = abs( $page );
			if ( $page > $data[ 'pages' ] ) {
				$page = $data[ 'pages' ];
			}
		}

		$data['current_page'] = $page;

		if($data['total'] > 0){

			$data[ 'form' ] = $form_id;

			$data[ 'fields' ] = $field_labels;


			$the_entries = $entries->get_page( $page, $status );

			if ( ! empty( $the_entries ) ) {

				$ids               = array();
				$data[ 'entries' ] = array();


				/** @var Qcformbuilder_Forms_Entry $an_entry */
				foreach ( $the_entries as $an_entry ) {
					$ids[] = $an_entry->get_entry_id();
				}
				// init field types to initialize view rendering in entry lists
				Qcformbuilder_Forms_Fields::get_all();

				foreach ( $ids as $entry_id ) {
					$rows = $entries->get_rows( $page, (int) $entry_id, $status );
					foreach ( $rows as $row ) {
						$e = 'E' . $row->entry_id;
						if ( ! empty( $row->_user_id ) ) {
							$user = get_userdata( $row->_user_id );
							if ( ! empty( $user ) ) {
								$data[ 'entries' ][ $e ][ 'user' ][ 'ID' ]     = $user->ID;
								$data[ 'entries' ][ $e ][ 'user' ][ 'name' ]   = $user->data->display_name;
								$data[ 'entries' ][ $e ][ 'user' ][ 'email' ]  = $user->data->user_email;
								$data[ 'entries' ][ $e ][ 'user' ][ 'avatar' ] = get_avatar( $user->ID, 64 );
							}
						}

						$data[ 'entries' ][ $e ][ '_entry_id' ] = $row->entry_id;

						$submitted = $row->_datestamp;


						$data[ 'entries' ][ $e ][ '_date' ] = Qcformbuilder_Forms::localize_time( $submitted );

						// setup default data array
						if ( ! isset( $data[ 'entries' ][ $e ][ 'data' ] ) ) {
							if ( isset( $field_labels ) ) {
								foreach ( $field_labels as $slug => $label ) {
									// setup labels ordering
									$data[ 'entries' ][ $e ][ 'data' ][ $slug ] = null;
								}
							}
						}

						if ( ! empty( $field_labels[ $row->slug ] ) ) {

							$label = $field_labels[ $row->slug ];

							// check view handler
							$field = Qcformbuilder_Forms_Field_Util::get_field(  $row->slug, $form, true );

                                                        // maybe json?
                                                        if ( is_string($row->value) ) {
                                                            $is_json = json_decode( $row->value, ARRAY_A );
                                                        } else if ( is_array($row->value) ) { //Process all checkbox values
                                                            $is_json = $row->value;
                                                        }

							if ( ! empty( $is_json ) ) {
								$row->value = $is_json;
							}else  {
								$row->value = maybe_unserialize( $row->value );
							}

							if( is_array( $row->value )  ) {
								$row->value = implode( ',' , $row->value );
							}

							if( is_string( $row->value ) ){
								$row->value = esc_html( stripslashes_deep( $row->value ) );
							}else{
								$row->value = stripslashes_deep( Qcformbuilder_Forms_Sanitize::sanitize( $row->value ) );
							}

							$row->value = apply_filters( 'qcformbuilder_forms_view_field_' . $field[ 'type' ], $row->value, $field, $form );

							if ( isset( $data[ 'entries' ][ $e ][ 'data' ][ $row->slug ] ) ) {
								// array based - add another entry
								if ( ! is_array( $data[ 'entries' ][ $e ][ 'data' ][ $row->slug ] ) ) {
									$tmp                                             = $data[ 'entries' ][ $e ][ 'data' ][ $row->slug ];
									$data[ 'entries' ][ $e ][ 'data' ][ $row->slug ] = array( $tmp );
								}
								$data[ 'entries' ][ $e ][ 'data' ][ $row->slug ][] = $row->value;
							} else {
								$data[ 'entries' ][ $e ][ 'data' ][ $row->slug ] = $row->value;
							}
						}

						if ( ! empty( $form[ 'variables' ][ 'types' ] ) ) {
							foreach ( $form[ 'variables' ][ 'types' ] as $var_key => $var_type ) {
								if ( $var_type == 'entryitem' ) {
									$data[ 'fields' ][ $form[ 'variables' ][ 'keys' ][ $var_key ] ]                  = ucwords( str_replace( '_', ' ', $form[ 'variables' ][ 'keys' ][ $var_key ] ) );
									$data[ 'entries' ][ $e ][ 'data' ][ $form[ 'variables' ][ 'keys' ][ $var_key ] ] = Qcformbuilder_Forms::do_magic_tags( $form[ 'variables' ][ 'values' ][ $var_key ], $row->_entryid );
								}
							}
						}


					}
				}
			}
		}


		return $data;

	}


	/**
	 * Return an instance of this class.
	 *
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Registers the admin page
	 *
	 */
	public function register_admin_page(){
		global $menu, $submenu;

		$forms = Qcformbuilder_Forms_Forms::get_forms( true );

		
		// get current user
		if( current_user_can( Qcformbuilder_Forms::get_manage_cap() ) ){

			$this->screen_prefix[] = add_menu_page(
				__('Bot Form Builder', 'qcformbuilder-forms' ),
				__('Bot Form Builder', 'qcformbuilder-forms' ),
				Qcformbuilder_Forms::get_manage_cap(),
				$this->plugin_slug, array( $this, 'render_admin' ),
				'dashicons-format-aside',
				'9'
			);
			
			add_submenu_page(
				$this->plugin_slug,
				__('Qcformbuilder Forms Admin', 'qcformbuilder-forms' ),
				__('Bot Form Builder', 'qcformbuilder-forms' ),
				Qcformbuilder_Forms::get_manage_cap(),
				$this->plugin_slug, array( $this, 'render_admin' ) );

			if( ! empty( $forms ) ){
				foreach($forms as $form_id=>$form){
					if(!empty($form['pinned'])){

						$this->screen_prefix[] 	 = add_submenu_page(
							$this->plugin_slug,
							__('Qcformbuilder Forms', 'qcformbuilder-forms' ).' - ' . $form['name'], '- '.$form['name'],
							Qcformbuilder_Forms::get_manage_cap(), $this->plugin_slug . '-pin-' . $form_id, array( $this, 'render_admin' )
						);
					}
				}
			}

		}else{
			// not an admin - pin for user
			if( ! empty( $forms ) ){
				$user = wp_get_current_user();
				if(empty($user->roles)){
					// no role - bye bye.
					return;
				}

				foreach($forms as $form_id=>$form){
					$capability = null;
					if(!empty($form['pinned']) && !empty( $form['pin_roles'] ) ){
						if( !empty( $form['pin_roles']['all_roles'] ) ){
							$user = wp_get_current_user();
							if( empty( $user ) || empty( $user->roles ) ){
								continue;
							}
							$capabilities = array_keys( $user->allcaps );
							if( empty( $capabilities ) ){
								continue;
							}
							$capability = $capabilities[0];
						}elseif( !empty( $form['pin_roles']['access_role'] ) ){
							foreach ($form['pin_roles']['access_role'] as $role => $enabled) {
								if( in_array( $role, $user->roles ) ){
									$role_details = get_role( $role );
									if(empty($role_details->capabilities)){
										continue;
									}
									$capabilities = array_keys( $role_details->capabilities );
									$capability = $capabilities[0];
									break;
								}
							}
						}
						if( empty($capability)){
							// not this one.
							continue;
						}

						if( empty( $this->screen_prefix ) ){
							// make top menu
							$main_slug = $this->plugin_slug . '-pin-' . $form_id;
							$this->screen_prefix[] = add_menu_page( __('Bot Form Builder', 'qcformbuilder-forms' ), __('Bot Form Builder ', 'qcformbuilder-forms' ), $capability, $main_slug, array( $this, 'render_admin' ), 'dashicons-format-aside', 52.999 );

						}

						$this->screen_prefix[] 	 = add_submenu_page( $main_slug, __('Bot Form Builder', 'qcformbuilder-forms' ).' - ' . $form['name'], $form['name'], $capability, $this->plugin_slug . '-pin-' . $form_id, array( $this, 'render_admin' ) );

					}
				}
			}
		}


	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @return    null
	 */
	public function enqueue_admin_stylescripts() {

		$screen = get_current_screen();
		Qcformbuilder_Forms_Render_Assets::register();
		Qcformbuilder_Forms_Admin_Assets::register_scripts();
		Qcformbuilder_Forms_Admin_Assets::register_styles();

		wp_enqueue_style( $this->plugin_slug . '-admin-icon-styles', WFBCORE_URL . 'assets/css/dashicon.css', array(), self::VERSION );

        add_action( 'qcformbuilder_forms_admin_enqueue_post_editor', ['Qcformbuilder_Forms_Admin_Assets', 'post_editor' ]);
        add_action( 'qcformbuilder_forms_admin_main_enqueue', ['Qcformbuilder_Forms_Admin_Assets', 'admin_common' ],1);
        add_action( 'qcformbuilder_forms_admin_enqueue_form_editor', ['Qcformbuilder_Forms_Admin_Assets', 'form_editor' ]);

        /**
         * Runs directly before assets MIGHT be enqueued in the WordPress admin
		 *
		 * @since 1.7.3
         */
        do_action( 'qcformbuilder_forms_admin_pre_enqueue' );
		/**
		 * Control if Qcformbuilder Forms assets run in post editor
         *
         * @since 1.5.7
         *
         * @param bool $use Return false to disable.
         * @param string $post_type Current post type
		 */
		if ( $screen->base === 'post' && apply_filters( 'qcformbuilder_forms_insert_button_include', true, get_post_type() ) ) {
            /**
             * This action causes the assets Qcformbuilder Forms loads in the post editor to be enqueued
			 *
			 * @since 1.7.3
             */
			do_action( 'qcformbuilder_forms_admin_enqueue_post_editor' );
		}

		if ( ! in_array( $screen->base, $this->screen_prefix ) ) {
			return;
		}

		add_action( 'admin_head', array( __CLASS__, 'remove_notice_actions' ) );
		if( self::is_page( 'qcformbuilder-forms-extend' ) ){
			add_action( 'admin_enqueue_scripts', array( 'Qcformbuilder_Forms_Admin_Extend', 'scripts' ), 55 );
			return;
		}


        /**
         * This action causes the assets Qcformbuilder Forms loads in the main admin page to be enqueued
         *
         * @since 1.7.3
         */
        do_action( 'qcformbuilder_forms_admin_main_enqueue' );

		if ( Qcformbuilder_Forms_Admin::is_edit() ) {

            /**
             * This action causes the assets Qcformbuilder Forms loads in the form editor to be enqueued
             *
             * @since 1.7.3
             */
            do_action( 'qcformbuilder_forms_admin_enqueue_form_editor' );

		} else {
			Qcformbuilder_Forms_Render_Assets::enqueue_all_fields();
			if ( ! empty( $_GET[ 'edit-entry' ] ) ) {
                Qcformbuilder_Forms_Render_Assets::enqueue_style('grid');
            }
		}

		Qcformbuilder_Forms_Admin_Assets::panels();

	}

	/**
	 * Renders the admin pages
	 *
	 */
	public function render_admin(){

		echo "	<div class=\"wrap\">\r\n";
		if(!empty($_GET['edit'])){
			echo "<form method=\"POST\" action=\"admin.php?page=" . esc_html( $this->plugin_slug ) . "\" data-load-element=\"#save_indicator\" data-sender=\"ajax\" class=\"qcformbuilder-forms-options-form edit-update-trigger\">\r\n";
			include WFBCORE_PATH . 'ui/edit.php';
			echo "</form>\r\n";
		}elseif(!empty($_GET['page']) && $_GET['page'] == 'qcformbuilder-forms-extend'){
			include WFBCORE_PATH . 'ui/extend.php';
		}elseif(!empty($_GET['page']) && false !== strpos($_GET['page'], 'qcformbuilder-forms-pin-')){
			$formID = substr($_GET['page'], 18);
			$form = Qcformbuilder_Forms_Forms::get_form( $formID );
			include WFBCORE_PATH . 'ui/entries.php';

		}else{
			add_action( 'qcformbuilder_forms_admin_footer', array( 'Qcformbuilder_Forms_Entry_Viewer', 'print_scripts' ) );
			include WFBCORE_PATH . 'ui/admin.php';

		}
		echo "	</div>\r\n";



	}

	/***
	 * Handles form updating, deleting, exporting and importing
	 *
	 * @uses "init" action
	 */
	static function save_form(){
		if( ! isset( $_GET[ 'page' ] ) || 'qcformbuilder-forms' != $_GET[ 'page' ] ){
			return;
		}

		/// check for form delete
		if(!empty($_GET['delete']) && !empty($_GET['cal_del']) && current_user_can( Qcformbuilder_Forms::get_manage_cap( 'save' ), strip_tags( $_GET[ 'delete' ] ) ) ){

			if ( ! wp_verify_nonce( $_GET['cal_del'], 'wfb_del_frm' ) ) {
				// This nonce is not valid.
				wp_die( esc_html__('Sorry, please try again', 'qcformbuilder-forms' ), esc_html__('Form Delete Error', 'qcformbuilder-forms' ) );
			}else{
				$deleted = Qcformbuilder_Forms_Forms::delete_form( strip_tags( $_GET['delete'] ) );
				if ( $deleted ) {
					wp_redirect( 'admin.php?page=qcformbuilder-forms' );
					exit;
				} else {
					wp_die( esc_html__('Form could not be deleted.', 'qcformbuilder-forms' ) );
				}

			}

		}

		/** IMPORT */
		if( isset($_POST['cfimporter']) && current_user_can( Qcformbuilder_Forms::get_manage_cap( 'import' )  ) ){

			if ( check_admin_referer( 'wfb-import', 'cfimporter' ) ) {
				if ( isset( $_FILES[ 'import_file' ] ) && ! empty( $_FILES[ 'import_file' ][ 'size' ] ) ) {
					$loc = wp_upload_dir();
                    $temp_name = $_FILES[ 'import_file' ][ 'tmp_name' ];
                    $name = $_FILES[ 'import_file' ][ 'name' ];
					$type_check = wp_check_filetype_and_ext($temp_name,$name, [
					        'json' => 'application/json',
                    ]);
					if( ! $type_check['type'] ){
						$type_check = wp_check_filetype_and_ext($temp_name,$name, [
							'json' => 'text/plain',
						]);
                    }

					if( ! 'json' === $type_check[ 'ext'] ){
						wp_die( esc_html__( 'Form could not be imported. File type must be JSON.', 'qcformbuilder-forms' ) );
					}

					$location = $loc[ 'path' ] . '/wfb-form-import.json';
					if ( move_uploaded_file( $temp_name, $location ) ) {
						$data = json_decode( file_get_contents($location ), true  );
						unlink($location);
						if( ! is_array( $data ) ){
							wp_die( esc_html__( 'File is not a valid Qcformbuilder Form Import', 'qcformbuilder-forms' ) );
						}
						if( ! isset( $_POST[ 'name' ] ) ){
							wp_die( esc_html__( 'Form must have a name.', 'qcformbuilder-forms' ) );
						}

						$data[ 'name' ] = strip_tags( $_POST[ 'name' ] );
                        $trusted = isset( $_POST[ 'import_trusted' ] ) ? boolval( $_POST[ 'import_trusted' ] ) : false;
						$new_form_id = Qcformbuilder_Forms_Forms::import_form( $data, $trusted );
						if( is_string( $new_form_id )  ){

							wfb_redirect( add_query_arg(array(
								'page' => 'qcformbuilder-forms',
								'edit' => $new_form_id,
                                't' => $trusted
							), admin_url( 'admin.php' ) ), 302 );
							exit;

						}else{
							wp_die( esc_html__( 'Form could not be imported.', 'qcformbuilder-forms' ) );
						}




					}
				} else {
					wp_die( esc_html__( 'Sorry, File not uploaded.', 'qcformbuilder-forms' ), esc_html__( 'Form Import Error', 'qcformbuilder-forms' ) );
				}

			} else {

				wp_die( esc_html__( 'Sorry, please try again', 'qcformbuilder-forms' ), esc_html__( 'Form Import Error', 'qcformbuilder-forms' ) );
			}

		}

		if(!empty($_GET['export-form']) && current_user_can( Qcformbuilder_Forms::get_manage_cap( 'export', strip_tags( $_GET[ 'export-form' ] ) ) )){

			$form = Qcformbuilder_Forms_Forms::get_form( $_GET['export-form'] );

			if(empty($form)){
				wp_die( esc_html__('Form does not exist.', 'qcformbuilder-forms' ) );
			}

			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			if( empty( $_GET['format'] ) || $_GET['format'] != 'php' ){
				header("Content-Type: application/json");
				header("Content-Disposition: attachment; filename=\"" . sanitize_file_name( strtolower( $form['name'] ) ) . "-export.json\";" );
				echo json_encode($form);
			}else{

				$form_id = sanitize_key( $_GET['form_id'] );
				$form['_external_form'] = 1;
				if( !empty( $_GET['pin_menu'] ) ){
					$form['pinned'] = 1;
				}
				header("Content-Type: application/php");
				header("Content-Disposition: attachment; filename=\"" . sanitize_file_name( strtolower( $form_id ) ) . "-include.php\";" );
				echo '<?php' . "\r\n";
				echo "/**\r\n * Qcformbuilder Forms - PHP Export \r\n * " . esc_attr( $form['name'] ) . " \r\n * @see https://quantumcloud.com/doc/exporting-qcformbuilder-forms/ \r\n * @version    " . esc_attr( WFBCORE_VER ) . "\r\n * @license   GPL-2.0+\r\n * \r\n */\r\n\r\n\r\n";

				$callback_function = 'slug_register_qcformbuilder_forms_' . preg_replace("/[^A-Za-z0-9 ]/", '', $form_id);

				$structure = sprintf( '
                    /**
                     * Hooks to load form.
                     * Remove "qcformbuilder_forms_admin_forms" if you do not want this form to show in admin entry viewer
                     */
                    add_filter( "qcformbuilder_forms_get_forms", "%s" );
                    add_filter( "qcformbuilder_forms_admin_forms", "%s" );
                    /**
                     * Add form to front-end and admin
                     *
                     * @param array $forms All registered forms
                     *
                     * @return array
                     */
                    function %s( $forms ) {
                        $forms["%s"] = apply_filters( "qcformbuilder_forms_get_form-%s", array() );
                        return $forms;
                    };',
                    $callback_function,
                    $callback_function,
                    $callback_function,
                    $form_id,
                    $form_id
                );
				$structure = ltrim($structure) . "\r\n\r\n";

				$structure .= "/**\r\n * Filter form request to include form structure to be rendered\r\n *\r\n * @since 1.3.1\r\n *\r\n * @param \$form array form structure\r\n */\r\n";
				$structure .= "add_filter( 'qcformbuilder_forms_get_form-{$form_id}', function( \$form ){\r\n return " . var_export( $form, true ) . ";\r\n" . '} );' . "\r\n";
				// cleanups because I'm me
				$structure = str_replace( 'array (', 'array(', $structure );
				$structure = str_replace( $form['ID'], $form_id, $structure );
				// switch field IDs
				if( !empty( $_GET['convert_slugs'] ) ){
					if ( !empty( $form['fields'] ) ){
						foreach( $form['fields'] as $field_id=>$field ){
							$structure = str_replace( $field_id, $field['slug'], $structure );
						}
					}
				}

				echo esc_html( $structure );
			}
			exit;

		}

		if(!empty($_GET['export']) && current_user_can( Qcformbuilder_Forms::get_manage_cap( 'export', strip_tags( $_GET[ 'export' ] ) ) ) ){

			$form = Qcformbuilder_Forms_Forms::get_form( $_GET['export'] );

			global $wpdb;

			//build labels
			$labels = array();
			$structure = array();
			$field_types = Qcformbuilder_Forms_Fields::get_all();
			$headers = array();
			if(!empty($form['fields'])){
				$headers['date_submitted'] = 'Submitted';
				foreach( Qcformbuilder_Forms_Forms::get_fields( $form, true ) as $field_id => $field ){
					if(isset($field_types[$field['type']]['capture']) &&  false === $field_types[$field['type']]['capture']){
						continue;
					}
					$headers[$field['slug']] = $field['label'];
					$structure[$field['slug']] = $field_id;
				}
			}
			$filter = null;
			// export set - transient
			if(!empty($_GET['tid'])){
				$items = Qcformbuilder_Forms_Transient::get_transient( $_GET[ 'tid' ] );

				if(!empty($items)){
					Qcformbuilder_Forms_Transient::delete_transient( $_GET[ 'tid' ] );
					$filter = ' AND `entry`.`id` IN (' . implode(',', $items) . ') ';
				}else{
					wp_die( esc_html__('Export selection has expired', 'qcformbuilder-forms' ) , esc_html__('Export Expired', 'qcformbuilder-forms' ) );
				}
			}

			//Table Name with DB Prefix
			$tableName = $wpdb->prefix ."wfb_form_entries";

			//Prepared Query
			$preparedSQLQuery = $wpdb->prepare("
			SELECT
				`entry`.`id` as `_entryid`,
				`entry`.`form_id` AS `_form_id`,
				`entry`.`datestamp` AS `_date_submitted`,
				`entry`.`user_id` AS `_user_id`

			FROM %s AS `entry`

			WHERE `entry`.`form_id` = %s
			%s
			AND `entry`.`status` = 'active'
			ORDER BY `entry`.`datestamp` DESC;", $tableName, Qcformbuilder_Forms_Sanitize::sanitize($_GET['export']), $filter);

			//Execution of Prepared Query, i.e. SQL execution.
			$rawdata = $wpdb->get_results( $preparedSQLQuery );  //db call ok; no-cache ok

			$data = array();

			$localize_time = Qcformbuilder_Forms_CSV_Util::should_localize_time( $form );
			foreach( $rawdata as $entry){
				$submission = Qcformbuilder_Forms::get_entry( $entry->_entryid, $form);
				if( $localize_time ){
					$data[$entry->_entryid]['date_submitted'] = Qcformbuilder_Forms::localize_time( $entry->_date_submitted, true );
				}else{
					$data[$entry->_entryid]['date_submitted'] = $entry->_date_submitted;
				}

				foreach ($structure as $slug => $field_id) {
					$data[$entry->_entryid][$slug] = (
					        isset( $submission['data'][$field_id]['view'] ) ? $submission['data'][$field_id]['view']
                                : ( isset( $submission['data'][$field_id]['value'] ) ? $submission['data'][$field_id]['value'] : null )
                    );
				}

			}

			if( empty( $headers ) ){
				wp_die( esc_html__( 'Could not process export. This is most likely due to a problem with the form configuration.', 'qcformbuilder-forms' ) );
			}
			$encoding = Qcformbuilder_Forms_CSV_Util::character_encoding( $form );

			$file_type = Qcformbuilder_Forms_CSV_Util::file_type( $form );

			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: text/csv charset=$encoding;");
			header("Content-Disposition: attachment; filename=\"" . sanitize_file_name( $form['name'] ) . ".$file_type\";" );
			header("Content-Transfer-Encoding: binary");
			$df = fopen("php://output", 'w');

			if ( 'tsv' == $file_type ) {
				$delimiter = chr(9);
			} else {
				$delimiter = ',';
			}
			$csv_data = apply_filters( 'qcformbuilder_forms_admin_csv', array(
				'headers' => $headers,
				'data' => $data
			), $form );
			$data = $csv_data[ 'data' ];
			$headers = $csv_data[ 'headers' ];

			fputcsv($df, $headers, $delimiter);
			foreach($data as $row){
				$csvrow = array();
				foreach($headers as $key=>$label){
					if(!isset($row[$key])){
						$row[$key] = null;
					}else{
						if( is_array( $row[$key] ) && isset( $row[$key]['label'] ) ){
							$row[$key] = $row[$key]['value'];
						}elseif( is_array( $row[$key] ) ){
							$subs = array();
							foreach( $row[$key] as $row_part ){
								if( is_array( $row_part ) && isset( $row_part['label'] ) ){
									$subs[] = $row_part['value'];
								}else{
									if( is_string( $row_part ) && '{"opt' == substr( $row_part, 0, 5 ) ){
									    continue;
									}
									$subs[] = $row_part;
								}
							}
							$row[$key] = implode(', ', $subs );
						}
					}

					$csvrow[] = $row[$key];
				}
				fputcsv($df, $row, $delimiter);
			}
			fclose($df);
			exit;
		}

		if( isset($_POST['config']) && isset( $_POST['wfb_edit_nonce'] ) && current_user_can( Qcformbuilder_Forms::get_manage_cap( 'manage' ) ) ){

			// if this fails, check_admin_referer() will automatically print a "failed" page and die.
			if ( check_admin_referer( 'wfb_edit_element', 'wfb_edit_nonce' ) ) {

				// strip slashes
				$data = json_decode( stripslashes_deep($_POST['config']) , ARRAY_A );
				self::save_a_form( $data );

				if(!empty($_POST['sender'])){
					exit;
				}

				wp_redirect('admin.php?page=qcformbuilder-forms');
				die;

			}
			return;
		}

		/** Resotre revisions */
		if( isset( $_POST[ 'wfb_edit_nonce' ], $_POST[ self::REVISION_KEY ], $_POST[ 'form' ], $_POST[ 'restore' ] ) ){
			if( ! current_user_can( Qcformbuilder_Forms::get_manage_cap( 'manage' ) ) || ! wp_verify_nonce( $_POST[ 'wfb_edit_nonce' ], 'wfb_edit_element' ) ){
				wp_send_json_error();

			}
			$restored = Qcformbuilder_Forms_Forms::restore_revision( absint( $_POST[ self::REVISION_KEY ] ));
			if( $restored ){
				wp_send_json_success();
			}else{
				wp_send_json_error();
			}

			exit;
		}
	}

	/**
	 * Save a form
	 *
	 * @since 1.3.4
	 *
	 * @param array $data
	 */
	public static function save_a_form( $data ){
		$saved_form = Qcformbuilder_Forms_Forms::get_form( $data['ID'] );
		if( ! empty( $saved_form ) && isset( $saved_form[ 'fields' ] ) ){
			$extra_fields = self::get_editor_extra_fields($saved_form );
			$form = new Qcformbuilder_Forms_API_Privacy($data);
			if( isset( $extra_fields['email_identifying_fields'] ) ){
				$form->set_email_identifying_fields( $extra_fields['email_identifying_fields'] );
			}
			if( isset( $extra_fields['personally_identifying_fields'] ) ){
				$form->set_pii_fields( $extra_fields['personally_identifying_fields'] );
			}

			$data = $form->get_form();
			if( isset( $extra_fields['is_privacy_export_enabled'] ) ){
				$data = Qcformbuilder_Forms_Forms::update_privacy_export_enabled( $data, boolval($extra_fields['is_privacy_export_enabled']));
			}
		}

		Qcformbuilder_Forms_Forms::save_form( $data );

	}


    /**
     * Get the additional fields of form that are not used in the editor
	 *
	 * @since 1.7.1
	 *
	 * @param array $form Form config to get saved field values from
     * @return array
     */
	public static function get_editor_extra_fields(array $form )
	{
        return array_merge(
            [
                'email_identifying_fields' => Qcformbuilder_Forms_Forms::email_identifying_fields($form, true ),
                'personally_identifying_fields' => Qcformbuilder_Forms_Forms::personally_identifying_fields($form,true),
                'is_privacy_export_enabled' => Qcformbuilder_Forms_Forms::is_privacy_export_enabled($form),
            ],
            /**
             * Add additional fields to the non-editor fields
             *
             * These values will be saved with the form, unedited.
             *
             * @since 1.7.0
             *
             * @param array $field Extra fields.
             */
            apply_filters( 'qcformbuilder_forms_editor_extra_fields', [] )

        );
	}

	/**
	 * AJAX callback for new form creation
	 *
	 * @since unknown
	 *
	 * @uses "wp_ajax_create_form" action
	 */
	public static function create_form(){
		$nonce_validated = false;
		if(  isset( $_POST[ 'nonce' ] ) &&  wp_verify_nonce( $_POST[ 'nonce'], 'wfb_create_form' ) ){
			$nonce_validated = true;
		}

		parse_str( $_POST['data'], $newform );


		if( ! $nonce_validated ){
			if( isset( $newform, $newform[ 'nonce' ] ) ) {
				if( wp_verify_nonce( $newform[ 'nonce' ], 'wfb_create_form' ) ){
					$nonce_validated = true;
				}
			}

		}

		if( ! $nonce_validated ){
			status_header(500);
			wp_send_json_error( );
		}
		$newform = Qcformbuilder_Forms_Forms::create_form( $newform );
		echo esc_attr( $newform['ID'] );
		exit;


	}


	/**
	 * Set panels to be used in Qcformbuilder Forms form editor
	 *
	 * @since unknown
	 *
	 * @uses "qcformbuilder_forms_get_panel_extensions" fitler
	 *
	 * @param array $panels
	 *
	 * @return array
	 */
	public function get_panel_extensions($panels){

		$path = WFBCORE_PATH . "ui/panels/";

		$internal_panels = array(
			'form_layout' => array(
				"name"			=>	__( 'Layout', 'qcformbuilder-forms' ),
				"setup"		=>	array(
					"scripts"	=>	array(
						'jquery-ui-sortable',
						'jquery-ui-draggable',
						'jquery-ui-droppable',
					),
					"styles"	=>	array(
						WFBCORE_URL . "assets/css/editor-grid.css",
						WFBCORE_URL . "assets/css/processors-edit.css"
					),
				),
				"tabs"		=>	array(
					"layout" => array(
						"name" => __( 'Layout', 'qcformbuilder-forms' ),
						"location" => "lower",
						"label" => __( 'Layout', 'qcformbuilder-forms' ),
						"active" => true,
						"actions" => array(
							$path . "layout_toolbar.php"
						),
						"repeat" => 0,
						"canvas" => $path . "layout.php",
						"side_panel" => $path . "layout_side.php",
						
					),
					/*
					"pages" => array(
						"name" => __( 'Pages', 'qcformbuilder-forms' ),
						"location" => "lower",
						"label" => __( 'Form Pages', 'qcformbuilder-forms' ),
						"canvas" => $path . "pages.php",
						'tip' => array(
							'link' => 'https://quantumcloud.com/doc/using-multi-page-forms/?utm_source=wp-admin&utm_medium=form-editor&utm_term=tabs',
							'text' => __( 'Using multi-page forms.', 'qcformbuilder-forms' ),
						)
					),
					*/
					"mailer" => array(
						"name" => __( 'Email', 'qcformbuilder-forms' ),
						"location" => "lower",
						"label" => __( 'Email Notification Settings', 'qcformbuilder-forms' ),
						"canvas" => $path . "emailer.php",
						'tip' => array()
					),
					"processors" => array(
						"name" => __( 'Processors', 'qcformbuilder-forms' ),
						"location" => "lower",
						"label" => __( 'Form Processors', 'qcformbuilder-forms' ),
						"canvas" => $path . "processors.php",
						'tip' => array()
					),
					/*
                    "antispam" => array(
                        "name" => __( 'Anti-Spam', 'qcformbuilder-forms' ),
                        "location" => "lower",
                        "label" => __( 'Anti Spam', 'qcformbuilder-forms' ),
                        "canvas" => $path . "anti-spam.php",
                        'tip' => array(
                            'link' => 'https://quantumcloud.com/doc/protect-form-spam-qcformbuilder-forms/?utm_source=wp-admin&utm_medium=form-editor&utm_term=tabs',
                            'text' => __( 'Anti-spam documentation', 'qcformbuilder-forms' )
                        )
                    ),
					*/
					"conditions" => array(
						"name" => __( 'Conditions', 'qcformbuilder-forms' ),
						"location" => "lower",
						"label" => __( 'Conditions', 'qcformbuilder-forms' ),
						"canvas" => $path . "conditions.php",
						'tip' => array()
					),
					
					"variables" => array(
						"name" => __( 'Variables', 'qcformbuilder-forms' ),
						"location" => "lower",
						"label" => __( 'Variables', 'qcformbuilder-forms' ),
						"canvas" => $path . "variables.php",
						"actions" => array(
							$path . "variable_add.php"
						),
						'tip' => array()
					),
					"revisions" => array(
						"name" => __( 'Revisions', 'qcformbuilder-forms' ),
						"location" => "lower",
						"label" => __( 'Revisions', 'qcformbuilder-forms' ),
						"canvas" => $path . "revisions.php",
						
					),
					/*
					"responsive" => array(
						"name" => __( 'Responsive', 'qcformbuilder-forms' ),
						"location" => "lower",
						"label" => __( 'Responsive Settings', 'qcformbuilder-forms' ),
						"repeat" => 0,
						"fields" => array(
							"break_point" => array(
								"label" => __( 'Grid Collapse', 'qcformbuilder-forms' ),
								"slug" => "break_point",
								"caption" => __( 'Set the smallest screen size at which to collapse the grid. (based on Bootstrap 3.0)', 'qcformbuilder-forms' ),
								"type" => "radio",
								"config" => array(
									"default" => "sm",
									"option"	=> array(
										"xs"	=> array(
											'value'	=> 'xs',
											'label'	=> __('Maintain grid always', 'qcformbuilder-forms' ),
										),
										"sm"	=> array(
											'value'	=> 'sm',
											'label'	=> '< 767px'
										),
										"md"	=> array(
											'value'	=> 'md',
											'label'	=> '< 991px'
										),
										"lg"	=> array(
											'value'	=> 'lg',
											'label'	=> '< 1199px'
										)
									)
								),
							)
						),
						'tip' => array(
							'link' => 'https://quantumcloud.com/doc/configure-responsive-settings/?utm_source=wp-admin&utm_medium=form-editor&utm_term=tabs',
							'text' => __( 'Responsive settings getting started guide', 'qcformbuilder-forms' )
						)
						
					),
					*/
				),
			),
		);


		if( self::is_revision_edit() ){
			unset( $internal_panels[ 'revisions' ] );
		}



		return array_merge( $panels, $internal_panels );

	}

	/**
	 * Add to the admin notices
	 *
	 * @since 1.3.0
	 *
	 * @param string|array $notice The notice or array of notices to add.
	 */
	public static function add_admin_notice( $notice ) {
		if ( is_string( $notice ) ) {
			self::$admin_notices[] = $notice;
		}

		if ( is_array( $notice ) ) {
			foreach( $notice as $n) {
				self::add_admin_notice( $n );
			}

		}

	}

	/**
	 * Get the admin messages
	 *
	 * @since 1.3
	 *
	 * @param bool $as_string Optional. To return as string, the default, or as an array
	 * @param string $seperator Optional. What to break notices with, when returning as string. Default is "\n"
	 *
	 * @return string|array|void
	 */
	public static  function get_admin_notices( $as_string = true, $seperator = "\n" ) {
		if ( ! empty( self::$admin_notices ) ) {
			if ( $as_string ) {
				return implode( $seperator, self::$admin_notices  );

			}else{
				return self::$admin_notices;

			}

		}

	}

	/**
	 * Create an admin notice
	 *
	 * @since 1.3.4
	 *
	 * @param $title
	 * @param $content
	 */
	public static function create_admin_notice( $title, $content, $sanitize = true  ){
		if( $sanitize ) {
			$content = wp_kses( $content, wp_kses_allowed_html( 'post' ) );
		}
		?>
		<div
			class="ajax-trigger"
			data-modal="wfb-admin-notice"
			data-modal-title="<?php echo esc_attr( $title ); ?>"
			data-template="#<?php echo esc_attr( sanitize_key( 'admin-modal' .  $title ) ); ?>"
			data-modal-height="300"
			data-modal-width="650"
			data-autoload="true"
		>
		</div>
		<script type="text/html" id="<?php echo esc_attr( sanitize_key('admin-modal' . $title ) ); ?>">
			<?php echo esc_attr( $content ); ?>
		</script>
		<?php
	}

	/**
	 * Add Easy Pods as an auto-populate option in admin
	 *
	 * @since 1.4.3
	 *
	 * @uses "qcformbuilder_forms_prerender_edit" action
	 */
	public static function  easy_pods_auto_populate(){
		if( version_compare( phpversion(), '5.3.0', '>=' ) ){
			if( class_exists( 'Qcformbuilder_Easy_Pods' ) ){
				new Qcformbuilder_Forms_Admin_APEasyPods;
			}

			if( defined( 'CAEQ_PATH' ) ){
				new Qcformbuilder_Forms_Admin_APEasyQueries;
			}
		}

	}

    /**
     * Remove hooks for admin notices while in Qcformbuilder Forms admin
     *
     * Qcformbuilder Forms admin does not play nice with admin notices, so we use a series of steps to remove most of them, sadly can not beat them all.
     *
     * @since 1.4.7
     * @uses "admin_head" action
     */
	public static function remove_notice_actions(){
        remove_all_actions( 'admin_notices' );
        remove_all_actions( 'network_admin_notices' );
        remove_all_actions( 'user_admin_notices' );
        remove_all_actions( 'all_admin_notices' );
    }

	/**
	 * Check if is a Qcformbuilder Forms page
	 *
	 * @since 1.5.0.9
	 *
	 * @param null|string $page Optional. Pass page name (get var) for sub page
	 *
	 * @return bool
	 */
	public static function is_page( $page = null ){
		if( is_admin() && isset( $_GET[ 'page' ] )  ){
			if( is_null( $page ) ){
				return  Qcformbuilder_Forms::PLUGIN_SLUG == $_GET[ 'page' ];
			}elseif ( is_string( $page ) ){
				return  $page == $_GET[ 'page' ];
			}
		}

		return false;
	}

	/**
	 * Check if is form editor page
	 *
	 * @since 1.5.0.9
	 *
	 * @return bool
	 */
	public static function is_edit(){
		return Qcformbuilder_Forms_Admin::is_page() && isset( $_GET[ self::EDIT_KEY ] );

	}

	/**
	 * Check if is form revision edit page
	 *
	 * @since 1.5.0.9
	 *
	 * @return bool
	 */
	public static function is_revision_edit(){
		return  self::is_edit() && isset( $_GET[ self::REVISION_KEY ] ) && is_numeric( $_GET[ self::REVISION_KEY ] );
	}

	/**
	 * Check if is main admin page
	 *
	 * @since 1.5.0.9
	 *
	 * @return bool
	 */
	public static function is_main_page(){
		return Qcformbuilder_Forms_Admin::is_page() && ! isset( $_GET[ self::EDIT_KEY ] );

	}

	/**
	 * Get URL for main admin page
	 *
	 * @since 1.5.2
	 *
	 * @param string|bool $orderby Optional. If valid string ("name") then that is appended as orderby. Default is false, which does nothing, default link.
	 *
	 * @return string
	 */
	public static function main_admin_page_url( $orderby = false ){
		$url =  add_query_arg( 'page', Qcformbuilder_Forms::PLUGIN_SLUG, admin_url( 'admin.php' ) );
		if( 'name' === $orderby ){
			$url = add_query_arg( self::ORDERBY_KEY, 'name', $url );
		}

		return $url;
	}

	/**
	 * Get link for form editor
	 *
	 * @since 1.5.3
	 *
	 * @param string $form_id ID of form to edit
	 * @param int $revision_id Optional The ID of the revision to edit if editing a revision
	 *
	 * @return  string
	 */
	public static function form_edit_link( $form_id, $revision_id = false ){
		$args = array(
			self::EDIT_KEY => $form_id,
			'page' => Qcformbuilder_Forms::PLUGIN_SLUG
		);

		if( $revision_id ){
			$args[ self::REVISION_KEY ] = $revision_id;
		}

		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	/**
	 * @param $form_id
	 * @param bool $revision_id
	 *
	 * @return string
	 */
	public static function preview_link( $form_id, $revision_id = false ){
		$args =  array(
			self::PREVIEW_KEY => $form_id
		);
		if( $revision_id ){
			$args[ self::REVISION_KEY ] = $revision_id;
		}

		return  add_query_arg( $args, get_home_url() );
	}

	/**
	 * Prevent re-sending email on form edit
	 *
	 * @uses "qcformbuilder_forms_send_email" filter
	 *
	 * @param bool $send
	 *
	 * @return bool
	 */
	public static function block_email_on_edit( $send ){
		if( isset( $_POST, $_POST[ '_wfb_frm_edt' ] ) && 0 < absint( $_POST[ '_wfb_frm_edt' ] ) ){
			return false;
		}

		return $send;
	}

	/**
	 * Setup privacy settings
     *
     * @since 1.6.1
     *
     * @uses "qcformbuilder_forms_admin_init" action
	 */
    public static function init_privacy_settings()
	{
        /*Qcformbuilder_Forms_Admin_Factory::menu_page('privacy', __('Privacy Settings', 'qcformbuilder-forms'), '<div id="qcformbuilder-forms-privacy-settings"></div>', [
            'scripts' => [
                'privacy',
                'admin',

            ],
            'styles' => [
                'editor-grid'
            ]
        ]);*/

    }


    /**
     * Return the default suggested privacy policy content.
     *
     * @since 1.7.0
     *
     * @return string The default policy content.
     */
    protected static function core_privacy_content() {
        return
            '<h2>' . esc_html__( 'Qcformbuilder Forms: Data Collection', 'qcformbuilder-forms') . '</h2>' .
            '<em>' . esc_html__( 'Suggested Text (if you have entry tracking enabled) -', 'qcformbuilder-forms' ) . '</em>' .
            '<p>' . esc_html__( 'Qcformbuilder Forms stores a record of all form submissions. Your data may be deleted by the site administrator. You may request a report of saved data related to your email address.', 'qcformbuilder-forms' ) . '</p>' .
            '<em>' . esc_html__( 'Suggested Text (if you do not have entry tracking enabled) -', 'qcformbuilder-forms' ) . '</em>' .
            '<p>' . esc_html__( 'Qcformbuilder Forms stores data only briefly for each submission. Uploaded media files may remain on the server' ) . '</p>' .
            '<em>' . esc_html__( 'Suggested Text (if you use add-ons that interact with third party services) -', 'qcformbuilder-forms' ) . '</em>' .
            '<p>' . esc_html__( 'Some data may be shared with other services including [list services such as MailChimp, PayPal]', 'qcformbuilder-forms' ) . '</p>';
    }

    /**
     * Return the default suggested privacy policy content for Qcformbuilder Forms Pro.
     *
     * @since 1.7.0
     *
     * @return string The default policy content.
     */
    protected static function pro_privacy_content() {
        return
            '<h2>' . esc_html__( 'Qcformbuilder Forms Pro: Data Collection', 'qcformbuilder-forms') . '</h2>' .
            '<p>' . esc_html__( 'This site uses a third-party service to deliver some emails.', 'qcformbuilder-forms' ) . '</p>' .
            '<p>' . esc_html__( 'This data will be stored on servers controlled by CalderaWP LLC, the makers of Qcformbuilder Forms, and operated by Linode and Amazon Web Services. Meta data about emails will be stored by SendGrid and Amazon Web Services', 'qcformbuilder-forms' ) . '</p>';
            '<p>' . esc_html__( 'If you request that personal data shared with this site is deleted, the corresponding data shared with QcformbuilderWP will also be deleted when the request is fulfilled.', 'qcformbuilder-forms' ) . '</p>';
    }



    /**
     * Add the suggested privacy policy text to the policy postbox.
     *
     * @uses "qcformbuilder_forms_admin_init" action
     *
     * @since 1.7.0
     */
    public static function add_suggested_privacy_content() {
        if( function_exists( 'wp_add_privacy_policy_content' ) ){
            wp_add_privacy_policy_content( esc_html__( 'Qcformbuilder Forms', 'qcformbuilder-forms' ), self::core_privacy_content() );
            if ( qcformbuilder_forms_pro_is_active() ) {
                wp_add_privacy_policy_content(esc_html__('Qcformbuilder Forms Pro', 'qcformbuilder-forms'), self::pro_privacy_content());
            }
        }
    }



}


