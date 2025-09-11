<?php
/*
 * Plugin Name: ChatBot Conversational Forms for Lead Generation
 * Plugin URI: https://wordpress.org/plugins/conversational-forms
 * Description: ChatBot Conversational Forms for Lead Generation
 * Author: ChatBot for WordPress - WPBot
 * @author    QuantumCloud
 * Version: 1.4.5
 * Donate link: https://www.wpbot.pro/
 * @author    QuantumCloud
 * Author URI: https://www.wpbot.pro/
 * Requires at least: 4.6
 * Tested up to: 6.8
 * Text Domain: conversational-form-builder
*/



// If this file is called directly, abort.
if ( !defined('WPINC') ) {
	die;
}

add_action( 'init', function(){
    //hack to make code splitting work.
    if( false !== strpos( $_SERVER['REQUEST_URI'], 'wp-admin/clients/') ){
        wfb_redirect(plugin_dir_url(__FILE__).str_replace( '/wp-admin/', '',$_SERVER['REQUEST_URI']));exit;
    }

});

global $wp_version;
if ( !version_compare(PHP_VERSION, '5.6.0', '>=') ) {
	function qcformbuilder_forms_php_version_nag()
	{
		?>
        <div class="notice notice-error">
            <p>
				<?php esc_html_e('Your version of PHP is incompatible with Qcformbuilder Forms and can not be used.',
					'qcformbuilder-forms'); ?>
				</p>
        </div>
		<?php
	}

	add_shortcode('qcformbuilder_form', 'qcformbuilder_forms_fallback_shortcode');
	add_shortcode('qcformbuilder_form_modal', 'qcformbuilder_forms_fallback_shortcode');
	add_action('admin_notices', 'qcformbuilder_forms_php_version_nag');
} elseif ( !version_compare($wp_version, '4.7.0', '>=') ) {
	function qcformbuilder_forms_wp_version_nag()
	{
		?>
        <div class="notice notice-error">
            <p>
				<?php esc_html_e('Your version of WordPress is incompatible with Qcformbuilder Forms and can not be used.',
					'qcformbuilder-forms'); ?>
            </p>
        </div>
		<?php
	}

	add_shortcode('qcformbuilder_form', 'qcformbuilder_forms_fallback_shortcode');
	add_shortcode('qcformbuilder_form_modal', 'qcformbuilder_forms_fallback_shortcode');
	add_action('admin_notices', 'qcformbuilder_forms_wp_version_nag');
} else {
	if ( ! defined('WFBCORE_PATH') ) {
		define('WFBCORE_PATH', plugin_dir_path(__FILE__));
	}
	if ( ! defined('WFBCORE_URL') ) {
		define('WFBCORE_URL', plugin_dir_url(__FILE__));
	}
	if ( ! defined('WFBCORE_VER') ) {
		define( 'WFBCORE_VER', '1.4.5' );
	}
	if ( ! defined('WFBCORE_EXTEND_URL') ) {
		define('WFBCORE_EXTEND_URL', 'https://api.qcformbuilderforms.com/1.0/');
	}
	if ( ! defined('WFBCORE_BASENAME') ) {
		define('WFBCORE_BASENAME', plugin_basename(__FILE__));
	}
	/**
	 * Qcformbuilder Forms DB version
	 *
	 * @since 1.3.4
	 *
	 * PLEASE keep this an integer
	 */
	if ( ! defined('WFB_DB') ) {
		define('WFB_DB', 8);
	}

	// init internals of CF
	include_once WFBCORE_PATH . 'classes/core.php';

	add_action('init', [ 'Qcformbuilder_Forms', 'init_wfb_internal' ]);
	// table builder
	register_activation_hook(__FILE__, [ 'Qcformbuilder_Forms', 'activate_qcformbuilder_forms' ]);


	// load system
	add_action('plugins_loaded', 'qcformbuilder_forms_load', 0);
	if( !function_exists('qcformbuilder_forms_load')){
	function qcformbuilder_forms_load()
	{
		include_once WFBCORE_PATH . 'classes/autoloader.php';
		include_once WFBCORE_PATH . 'classes/widget.php';
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_DB', WFBCORE_PATH . 'classes/db');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_Entry', WFBCORE_PATH . 'classes/entry');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_Email', WFBCORE_PATH . 'classes/email');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_Admin', WFBCORE_PATH . 'classes/admin');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_Render', WFBCORE_PATH . 'classes/render');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_Sync', WFBCORE_PATH . 'classes/sync');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_CSV', WFBCORE_PATH . 'classes/csv');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_Processor_Interface', WFBCORE_PATH . 'processors/classes/interfaces');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_API', WFBCORE_PATH . 'classes/api');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_Field', WFBCORE_PATH . 'classes/field');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_Magic', WFBCORE_PATH . 'classes/magic');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_Processor', WFBCORE_PATH . 'processors/classes');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_Shortcode', WFBCORE_PATH . 'classes/shortcode');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_CDN', WFBCORE_PATH . 'classes/cdn');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_Settings', WFBCORE_PATH . 'classes/settings');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_Import', WFBCORE_PATH . 'classes/import');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms_Query', WFBCORE_PATH . 'classes/query');
		Qcformbuilder_Forms_Autoloader::add_root('Qcformbuilder_Forms', WFBCORE_PATH . 'classes');
		Qcformbuilder_Forms_Autoloader::register();


		// includes
		include_once WFBCORE_PATH . 'includes/ajax.php';
		include_once WFBCORE_PATH . 'includes/field_processors.php';
		include_once WFBCORE_PATH . 'includes/custom_field_class.php';
		include_once WFBCORE_PATH . 'includes/filter_addon_plugins.php';
		include_once WFBCORE_PATH . 'includes/compat.php';
		include_once WFBCORE_PATH . 'processors/functions.php';
		include_once WFBCORE_PATH . 'includes/functions.php';
		include_once WFBCORE_PATH . 'ui/blocks/init.php';
		include_once WFBCORE_PATH . 'vendor/autoload.php';
		include_once WFBCORE_PATH . 'includes/wfb-pro-client/wfb-pro-init.php';
		require_once("class-qc-free-plugin-upgrade-notice.php");

		/**
		 * Runs after all of the includes and autoload setup is done in Qcformbuilder Forms core
		 *
		 * @since 1.3.5.3
		 */
		do_action('qcformbuilder_forms_includes_complete');

		/**
		 * Start cf2 system
		 *
		 * @since 1.8.0
		 */
		add_action('qcformbuilder_forms_v2_init', 'qcformbuilder_forms_v2_container_setup' );
		qcformbuilder_forms_get_v2_container();
	}
	}


	add_action('plugins_loaded', [ 'Qcformbuilder_Forms', 'get_instance' ]);


	// Admin & Admin Ajax stuff.
	if ( is_admin() || defined('DOING_AJAX') ) {
		add_action('plugins_loaded', [ 'Qcformbuilder_Forms_Admin', 'get_instance' ]);
		add_action('plugins_loaded', [ 'Qcformbuilder_Forms_Support', 'get_instance' ]);
		include_once WFBCORE_PATH . 'includes/plugin-page-banner.php';
	}


	//@see https://github.com/QcformbuilderWP/Qcformbuilder-Forms/issues/2855
	add_filter( 'qcformbuilder_forms_pro_log_mode', '__return_false' );
	add_filter( 'qcformbuilder_forms_pro_mail_debug', '__return_false' );


}

/**
 * Shortcode handler to be used when Qcformbuilder Forms can not be loaded
 *
 * @since 1.7.0
 *
 * @return string
 */
if( ! function_exists('qcformbuilder_forms_fallback_shortcode') ){
	function qcformbuilder_forms_fallback_shortcode()
	{
		if ( current_user_can('edit_posts') ) {
			return esc_html__('Your version of WordPress or PHP is incompatible with Qcformbuilder Forms.', 'qcformbuilder-forms');
		}

		return esc_html__('Form could not be loaded. Contact the site administrator.', 'qcformbuilder-forms');

	}
}


if(!function_exists('qc_get_first_field')){
	function qc_get_first_field($array){
		foreach($array as $k=>$v){
			return $k;
		}
	}
}

if(!function_exists('qcld_condition_check')){
	function qcld_condition_check($form, $condition){

		global $wpdb;
		$group = $condition['group'];
		$result = false;
		foreach($group as $key=>$value){
			foreach($value as $k=>$v){

				$fieldid = $v['field'];
				$compare = $v['compare'];
				$val = $v['value'];
				$fields = $form['fields'];
				$table = $wpdb->prefix.'wfb_form_entry_values';

				$row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table WHERE 1 and `field_id` = %d order by id desc limit 1",$fieldid) ); //db call ok, no-caching ok
				
				if(isset($fields[$fieldid]['type']) && $fields[$fieldid]['type']=='dropdown'){
					
					foreach($fields[$fieldid]['config']['option'] as $ok=>$ov){
						if($ok==$val && $ov['value']==$row->value){						
							$result = true;
						}
					}

				}else{

					if($compare=='is'){
						if($row->value==$val){
							$result = true;
						}
					}
					if($compare=='contains'){
						if (strpos($row->value, $val) !== false) {
							$result = true;
						}
					}

				}

			}
			
		}
		return $result;
	}
}
if( ( !empty($_GET['page']) && $_GET['page'] == 'qcformbuilder-form-wpbot') ){
//	add_action( 'admin_notices', 'qcld_condition_promotion_notice');
}
function qcld_condition_promotion_notice(){
    $promotion_img =  WFBCORE_URL . "assets/images/newyear24-wpbot.jpg";
    ?>
    <div data-dismiss-type="qcbot-feedback-notice" class="notice is-dismissible qcbot-feedback" style="background: #e80607">
        <div class="">
            <div class="qc-review-text" >
            	<a href="https://www.wpbot.pro/pricing/" target="_blank">
                	<img src="<?php echo esc_url($promotion_img); ?>" alt="promotion_img">
				</a>
            </div>
        </div>
    </div>
    <?php
}


if(!function_exists('qc_get_next_field')){
	function qc_get_next_field($form, $fieldid){
		global $wpdb;

		$conditions = array();
		if(isset($form['conditional_groups']['conditions'])){
			$conditions = $form['conditional_groups']['conditions'];
		}
		$fields = $form['fields'];
		$trigger = 0;
		foreach($form['layout_grid']['fields'] as $k=>$v){
			if($trigger==1){
				
				if(trim($fields[$k]['conditions']['type'])!=''){

					$condition = trim($fields[$k]['conditions']['type']);
					if(qcld_condition_check($form, $conditions[$condition])==true){
						return $k;
					}else{					
						return qc_get_next_field($form, $k);					
					}

				}else{
					return $k;
				}

			}
			if($k==$fieldid){
				$trigger = 1;
			}

		}
		if($trigger==0){
			return 'none';
		}
	}
}

add_action( 'wp_ajax_wpbot_get_form',        'wpbot_get_form' );
add_action( 'wp_ajax_nopriv_wpbot_get_form', 'wpbot_get_form' );
if(!function_exists('wpbot_get_form')){
	function wpbot_get_form(){
		global $wpdb;

		$formid = sanitize_text_field($_POST['formid']);
		$table = $wpdb->prefix.'wfb_forms';

		$result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table WHERE form_id = %s and type='primary'",$formid) ); //db call ok, no-caching ok

		$form = unserialize($result->config);
		$fields = $form['fields'];
		if(isset($form['layout_grid']['fields']) && !empty($form['layout_grid']['fields'])){
			
			$firstfield = qc_get_first_field($form['layout_grid']['fields']);
			$field = $fields[$firstfield];
			echo json_encode($field);
		}
		
		die();
	}
}

add_action( 'wp_ajax_wpbot_capture_form_value',        'wpbot_capture_form_value' );
add_action( 'wp_ajax_nopriv_wpbot_capture_form_value', 'wpbot_capture_form_value' );
if(!function_exists('wpbot_capture_form_value')){
	function wpbot_capture_form_value(){
		global $wpdb;

		$formid = sanitize_text_field($_POST['formid']);
		$fieldid = sanitize_text_field($_POST['fieldid']);
		$answer = $_POST['answer'];
		$entry = sanitize_text_field($_POST['entry']);
		$name = sanitize_text_field($_POST['name']);
	
		if($entry==0){

			$wpdb->insert(
				$wpdb->prefix."wfb_form_entries",
				array(
					'datestamp'  => current_time( 'mysql' ),
					'user_id'   => 0,
					'form_id'	=> $formid,
					'status'	=> 'active'
				)
			); //db call ok

			$entry = $wpdb->insert_id;

		}
		if($answer!=''){

			$wpdb->insert(
				$wpdb->prefix."wfb_form_entry_values",
				array(
					'entry_id'  => $entry,
					'field_id'   => $fieldid,
					'slug'	=> '',
					'value'	=> $answer
				)
			);  //db call ok

		}
		$table = $wpdb->prefix.'wfb_forms';

		$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE form_id = %s and type='primary'",$formid)); //db call ok, no-caching ok

		$form = unserialize($result->config);
		
		$fields = $form['fields'];
		$mailer = $form['mailer'];
		
		$conditions = array();
		if(isset($form['conditional_groups']['conditions'])){
			$conditions = $form['conditional_groups']['conditions'];
		}


		//print_r($form['layout_grid']['fields']);exit;
		
		if(isset($form['layout_grid']['fields']) && !empty($form['layout_grid']['fields'])){
		
			$nextfield = qc_get_next_field($form, $fieldid);
			

			if($nextfield!='none' && !empty($fields[$nextfield])){

				$field = $fields[$nextfield];
				$field['entry'] = $entry;
				
				$field['status'] = 'incomplete';
				echo json_encode($field);

			}else{
				if(isset($mailer['on_insert']) && $mailer['on_insert']==1){
					$answers = qc_form_answer($fields, $entry);
					qcld_wb_chatbot_send_form_query($answers, $name, $mailer);
				}
				echo json_encode(array('status'=>'complete'));
			}
			
		}else{
			if(isset($mailer['on_insert']) && $mailer['on_insert']==1){
				$answers = qc_form_answer($fields, $entry);
				qcld_wb_chatbot_send_form_query($answers, $name, $mailer);
			}
			echo json_encode(array('status'=>'complete'));
		}
		
		die();
	}
}

if(!function_exists('qc_form_answer')){
	function qc_form_answer($fields, $entry){

		global $wpdb;
		$data = array();
		
		foreach($fields as $key=>$field){

			$fieldid = $field['ID'];
			$question = $field['label'];
			$table = $wpdb->prefix.'wfb_form_entry_values';
			
			$answer = '';
			
			if(!empty($fieldid)){
				$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table  where entry_id = %s and field_id = %s",$entry,$fieldid)); //db call ok, no-caching ok
				
				$answer = $result->value;
			}
			if($answer!=''){
				$data[] = array(
					'question'=>$question,
					'answer' => $answer
				);
			}
		}

		return $data;

	}
}

if(!function_exists('qcld_wb_chatbot_send_form_query')){
	function qcld_wb_chatbot_send_form_query($datas, $name, $mailer) {


		$subject = (isset($mailer['email_subject']) && $mailer['email_subject']!=''?$mailer['email_subject']:'Conversational form data from Chatbot');
		//Extract Domain
		$url = get_site_url();
		$url = parse_url($url);
		$domain = $url['host'];
		
		$admin_email = get_option('admin_email');
		$toEmail = get_option('qlcd_wp_chatbot_admin_email') != '' ? get_option('qlcd_wp_chatbot_admin_email') : $admin_email;
		$fromEmail = "wordpress@" . $domain;
		if(isset($mailer['sender_email']) && $mailer['sender_email']!=''){
			$fromEmail = $mailer['sender_email'];
		}
		if(isset($mailer['sender_name']) && $mailer['sender_name']!=''){
			$name = $mailer['sender_name'];
		}
		$conversation_details_text = get_option('qlcd_wp_chatbot_conversation_details') ? get_option('qlcd_wp_chatbot_conversation_details') : esc_html__('Conversation Details', 'wpchatbot');
		$bodyContent = "";
		$bodyContent .= '<p><strong>' . $conversation_details_text . ':</strong></p><hr>';
		
		foreach($datas as $data){
			
			if(isset($data['question'])){
				$bodyContent .= '<p>'.esc_html($data['question']).': ' . esc_html($data['answer']) . '</p>';
			}

		}
			
		$bodyContent .= '<p>' . esc_html__('Mail Generated on', 'wpchatbot') . ': ' . the_time('F j, Y g:i a') . '</p>';
		$body = $bodyContent;
		
		if(isset($mailer['recipients']) && $mailer['recipients']!=''){
			
			$recipients = explode(',', $mailer['recipients']);
			foreach($recipients as $toEmail){
				
			//build email body


				$headers = array();
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$headers[] = 'From: ' . esc_html($name) . ' <' . esc_html($fromEmail) . '>';
				@wp_mail(trim($toEmail), $subject, $body, $headers);			
				
			}
			
		}else{
			$headers = array();
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			$headers[] = 'From: ' . esc_html($name) . ' <' . esc_html($fromEmail) . '>';
			@wp_mail(trim($toEmail), $subject, $body, $headers);		
		}

		
	}
}
if(!function_exists('add_menu_wpbot_adding_page')){
	add_action( 'admin_menu',  'add_menu_wpbot_adding_page'  );
	function add_menu_wpbot_adding_page(){
		add_submenu_page(
			'qcformbuilder-forms',
			__( 'ChatBot WPBot', 'qcformbuilder-forms' ),
			__( 'ChatBot WPBot', 'qcformbuilder-forms' ),
			'manage_options',
			'qcformbuilder-form-wpbot',
			'set_wpbot_page'

		);
	}
	function set_wpbot_page(){
		require_once("qcld-recommendbot-plugin.php");
	}
}

