<?php


namespace qcformbuilderwp\qcformbuilderforms\pro\admin;

use qcformbuilderwp\qcformbuilderforms\pro\container;


/**
 * Class scripts
 * @package qcformbuilderwp\qcformbuilderforms\pro\admin
 */
class scripts
{

	/** @var string */
	protected $assets_url;

	/** @var string */
	protected $slug;

	/** @var string */
	protected $version;

	/**
	 * scripts constructor.
	 *
	 * @param string $assets_url Url for  assets dir
	 * @param string $slug Slug for script/css
	 * @param string $version Current version
	 */
	public function __construct($assets_url, $slug, $version)
	{
		$this->assets_url = $assets_url;
		$this->slug = $slug;
		$this->version = $version;

	}

	public function get_assets_url()
	{
		return $this->assets_url;
	}

	/**
	 * @param string $view_dir @deprecated
	 * @param null $context Default is for admin page. Pass "tab" for use in CF admin.
	 * @param bool $enqueue_admin
	 *
	 * @return string
	 */
	public function webpack($view_dir, $context = null, $enqueue_admin = true)
	{
		\Qcformbuilder_Forms_Render_Assets::maybe_register();
		if ( $enqueue_admin ) {
			wp_enqueue_style(
				\Qcformbuilder_Forms_Admin_Assets::slug('admin', false),
				\Qcformbuilder_Forms_Render_Assets::make_url('admin', false),
				array(),
				$this->version
			);
			\Qcformbuilder_Forms_Admin_Assets::set_wfb_admin(\Qcformbuilder_Forms_Render_Assets::make_slug('pro'));
		}
		\Qcformbuilder_Forms_Render_Assets::enqueue_style('pro');
		\Qcformbuilder_Forms_Render_Assets::enqueue_script('pro');
		wp_localize_script(\Qcformbuilder_Forms_Render_Assets::make_slug('pro'), 'CF_PRO_ADMIN', $this->data());
		if ( 'tab' === $context ) {
			$id = 'wfb-pro-app-tab';
		} else {
			$id = 'wfb-pro-app';
		}
		return sprintf('<div id="%s"></div>', $id);
	}

	/**
	 * Data to localize
	 *
	 * @return array
	 */
	public function data()
	{
		$pro_url = admin_url('admin.php?page=wfb-pro');

		$data = [
			'strings' => [
				'saved' => esc_html__('Settings Saved', 'conversational-forms'),
				'notSaved' => esc_html__('Settings could not be saved', 'conversational-forms'),
				'apiKeysViewText' => esc_html__('You must add your API keys to use Qcformbuilder Forms Pro', 'conversational-forms'),
				'apiKeysViewLink' => esc_url($pro_url),
				'minLogLevelTitle' => esc_html__('Minimum Log Level', 'conversational-forms'),
				'minLogLevelInfo' => esc_html__('Setting a higher level than notice may affect performance, and should only be used when instructed by support.', 'conversational-forms'),
				'whatIsCFPro' => [
					'firstParagraph' => esc_html__("Qcformbuilder Forms Pro is an app + plugin that makes forms easy.", 'conversational-forms'),
					'hTitle' => esc_html__('Benefits', 'conversational-forms'),
					'firstLi' => esc_html__('Enhanced Email Delivery', 'conversational-forms'),
					'secondLi' => esc_html__('Form To PDF', 'conversational-forms'),
					'thirdLi' => esc_html__('Priority Support.', 'conversational-forms'),
					'fourthLi' => esc_html__('Add-ons Included in Yearly Plans', 'conversational-forms'),
				],
				'freeTrial' => [
					'firstParagraph' => esc_html__('Ready to try Qcformbuilder Forms Pro? Plans start at just $14.99/ month with a 7 day free trial.', 'conversational-forms'),
					'buttonLeft' => esc_html__('View Documentation', 'conversational-forms'),
					'buttonRight' => esc_html__('Start Free Trial', 'conversational-forms'),
				],
				'notConnected' => esc_html__('Not Connected', 'conversational-forms'),
				'connected' => esc_html__('Connected', 'conversational-forms'),
				'tabNames' => [
					'account' => esc_html__('Account', 'conversational-forms'),
					'formSettings' => esc_html__('Form Settings', 'conversational-forms'),
					'settings' => esc_html__('Settings', 'conversational-forms'),
					'whatIsCFPro' => esc_html__('What is Qcformbuilder Forms Pro ?', 'conversational-forms'),
					'freeTrial' => esc_html__('Free Trial', 'conversational-forms'),
				],
			],
			'api' => [
				'cf' => [
					'url' => esc_url_raw(\Qcformbuilder_Forms_API_Util::url('settings/pro')),
					'nonce' => wp_create_nonce('wp_rest'),
				],
				'cfPro' => [
					'url' => esc_url_raw(qcformbuilder_forms_pro_app_url()),
					'auth' => [],
				],
			],
			'settings' => container::get_instance()->get_settings()->toArray(),
			'logLevels' => container::get_instance()->get_settings()->log_levels(),
		];

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$data[ 'formScreen' ] = ( \Qcformbuilder_Forms_Admin::is_edit() && isset( $_GET[ \Qcformbuilder_Forms_Admin::EDIT_KEY ] ) ) ? sanitize_text_field( wp_unslash( $_GET[ \Qcformbuilder_Forms_Admin::EDIT_KEY ] ) ) : false;

		return $data;
	}
}
