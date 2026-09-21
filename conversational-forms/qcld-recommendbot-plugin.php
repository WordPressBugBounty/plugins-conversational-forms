<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
remove_all_filters( 'plugins_api' );

$qcld_chatplugintags = array(
	'a' => array(
		'href'   => array(),
		'title'  => array(),
		'target' => array(),
	),
);

$qcld_plugininstal = array();
$argus             = array(
	'slug'   => 'chatbot',
	'fields' => array(
		'short_description' => true,
		'icons'             => true,
		'reviews'           => false,
	),
);

$data = plugins_api( 'plugin_information', $argus );
if ( $data && ! is_wp_error( $data ) ) {
	$qcld_plugininstal['convers-form']        = $data;
	$qcld_plugininstal['convers-form']->name = 'ChatBot';
}
?>
<div class="wrap wfb-wpbot-page">
	<div class="wfb-wpbot-page__header">
		<h2><?php esc_html_e( 'ChatBot WPBot', 'conversational-forms'); ?></h2>
		<p><?php esc_html_e( 'Connect Conversational Forms with WPBot ChatBot.', 'conversational-forms'); ?></p>
	</div>

	<div class="wfb-wpbot-page__card wfb-wpbot-page__info">
		<p>
			<strong><?php esc_html_e( 'Conversational Form', 'conversational-forms'); ?></strong>
			<?php esc_html_e( 'Builder works with', 'conversational-forms'); ?>
			<strong><?php esc_html_e( 'WPBot ChatBot.', 'conversational-forms'); ?></strong>
		</p>
		<p>
			<?php esc_html_e( 'After creating a', 'conversational-forms'); ?>
			<strong><?php esc_html_e( 'Conversational Form,', 'conversational-forms'); ?></strong>
			<?php esc_html_e( 'you can add it to the', 'conversational-forms'); ?>
			<strong><?php esc_html_e( "ChatBot's Start Menu", 'conversational-forms'); ?></strong>
			<?php esc_html_e( 'from the', 'conversational-forms'); ?>
			<strong><?php esc_html_e( 'ChatBot Settings → Start Menu', 'conversational-forms'); ?></strong>
		</p>
		<p>
			<?php esc_html_e( 'You can use conversational forms for', 'conversational-forms'); ?>
			<strong><?php esc_html_e( 'Lead Generation,', 'conversational-forms'); ?></strong>
			<?php esc_html_e( 'to collect information from users, and to', 'conversational-forms'); ?>
			<strong><?php esc_html_e( 'create Button (menu)', 'conversational-forms'); ?></strong>
			<?php esc_html_e( 'driven conversations.', 'conversational-forms'); ?>
		</p>
	</div>

	<?php
	foreach ( (array) $qcld_plugininstal as $plugin ) {
		if ( is_object( $plugin ) ) {
			$plugin = (array) $plugin;
		}

		$title   = wp_kses( $plugin['name'], $qcld_chatplugintags );
		$version = wp_kses( $plugin['version'], $qcld_chatplugintags );
		$name    = wp_strip_all_tags( $title . ' ' . $version );

		$requires_php   = isset( $plugin['requires_php'] ) ? $plugin['requires_php'] : null;
		$requires_wp    = isset( $plugin['requires'] ) ? $plugin['requires'] : null;
		$compatible_php = is_php_version_compatible( $requires_php );
		$compatible_wp  = is_wp_version_compatible( $requires_wp );

		$action_links = array();

		if ( current_user_can( 'install_plugins' ) || current_user_can( 'update_plugins' ) ) {
			$status = install_plugin_install_status( $plugin );

			switch ( $status['status'] ) {
				case 'install':
					if ( $status['url'] ) {
						if ( $compatible_php && $compatible_wp ) {
							/* translators: %s: Plugin name */
							$aria_label = sprintf( _x( 'Install %s now',  'plugin' , 'conversational-forms'), $name );
							$action_links[] = sprintf(
								'<a class="install-now button" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
								esc_attr( $plugin['slug'] ),
								esc_url( $status['url'] ),
								esc_attr( $aria_label ),
								esc_attr( $name ),
								__( 'Install Now' , 'conversational-forms')
							);
						} else {
							$action_links[] = sprintf(
								'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
								_x( 'Cannot Install',  'plugin' , 'conversational-forms')
							);
						}
					}
					break;

				case 'update_available':
					if ( $status['url'] ) {
						if ( $compatible_php && $compatible_wp ) {
							/* translators: %s: Plugin name */
							$aria_label = sprintf( _x( 'Update %s now',  'plugin' , 'conversational-forms'), $name );
							$action_links[] = sprintf(
								'<a class="update-now button aria-button-if-js" data-plugin="%s" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
								esc_attr( $status['file'] ),
								esc_attr( $plugin['slug'] ),
								esc_url( $status['url'] ),
								esc_attr( $aria_label ),
								esc_attr( $name ),
								__( 'Update Now' , 'conversational-forms')
							);
						} else {
							$action_links[] = sprintf(
								'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
								_x( 'Cannot Update',  'plugin' , 'conversational-forms')
							);
						}
					}
					break;

				case 'latest_installed':
				case 'newer_installed':
					if ( is_plugin_active( $status['file'] ) ) {
						$action_links[] = sprintf(
							'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
							_x( 'Active',  'plugin' , 'conversational-forms')
						);
					} elseif ( current_user_can( 'activate_plugin', $status['file'] ) ) {
						$button_text  = __( 'Activate' , 'conversational-forms');
						/* translators: %s: Plugin name */
						$button_label = _x( 'Activate %s',  'plugin' , 'conversational-forms');
						$activate_url = add_query_arg(
							array(
								'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $status['file'] ),
								'action'   => 'activate',
								'plugin'   => $status['file'],
							),
							network_admin_url( 'plugins.php' )
						);

						if ( is_network_admin() ) {
							$button_text  = __( 'Network Activate' , 'conversational-forms');
							/* translators: %s: Plugin name */
							$button_label = _x( 'Network Activate %s',  'plugin' , 'conversational-forms');
							$activate_url = add_query_arg( array( 'networkwide' => 1 ), $activate_url );
						}

						$action_links[] = sprintf(
							'<a href="%1$s" class="button activate-now" aria-label="%2$s">%3$s</a>',
							esc_url( $activate_url ),
							esc_attr( sprintf( $button_label, $plugin['name'] ) ),
							$button_text
						);
					} else {
						$action_links[] = sprintf(
							'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
							_x( 'Installed',  'plugin' , 'conversational-forms')
						);
					}
					break;
			}
		}

		$details_link = self_admin_url(
			'plugin-install.php?tab=plugin-information&plugin=' . rawurlencode( $plugin['slug'] ) . '&width=700&height=550'
		);

		if ( ! empty( $plugin['icons']['svg'] ) ) {
			$plugin_icon_url = $plugin['icons']['svg'];
		} elseif ( ! empty( $plugin['icons']['2x'] ) ) {
			$plugin_icon_url = $plugin['icons']['2x'];
		} elseif ( ! empty( $plugin['icons']['1x'] ) ) {
			$plugin_icon_url = $plugin['icons']['1x'];
		} else {
			$plugin_icon_url = isset( $plugin['icons']['default'] ) ? $plugin['icons']['default'] : '';
		}

		$action_links = apply_filters( 'plugin_install_action_links', $action_links, $plugin );
		?>
		<div class="wfb-wpbot-page__card wfb-wpbot-plugin-card plugin-card plugin-card-<?php echo sanitize_html_class( $plugin['slug'] ); ?>">
			<?php if ( ! $compatible_php || ! $compatible_wp ) : ?>
				<div class="notice inline notice-error notice-alt">
					<p>
						<?php
						if ( ! $compatible_php && ! $compatible_wp ) {
							esc_html_e( 'This plugin doesn&#8217;t work with your versions of WordPress and PHP.' , 'conversational-forms');
							if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
								printf(
									' <a href="%1$s">%2$s</a>, %3$s <a href="%4$s">%5$s</a>.',
									esc_url( self_admin_url( 'update-core.php' ) ),
									esc_html__( 'Please update WordPress' , 'conversational-forms'),
									esc_html__( 'and then' , 'conversational-forms'),
									esc_url( wp_get_update_php_url() ),
									esc_html__( 'learn more about updating PHP' , 'conversational-forms')
								);
								wp_update_php_annotation( '</p><p><em>', '</em>' );
							} elseif ( current_user_can( 'update_core' ) ) {
								printf(
									' <a href="%s">%s</a>.',
									esc_url( self_admin_url( 'update-core.php' ) ),
									esc_html__( 'Please update WordPress' , 'conversational-forms')
								);
							} elseif ( current_user_can( 'update_php' ) ) {
								printf(
									' <a href="%s">%s</a>.',
									esc_url( wp_get_update_php_url() ),
									esc_html__( 'Learn more about updating PHP' , 'conversational-forms')
								);
								wp_update_php_annotation( '</p><p><em>', '</em>' );
							}
						} elseif ( ! $compatible_wp ) {
							esc_html_e( 'This plugin doesn&#8217;t work with your version of WordPress.' , 'conversational-forms');
							if ( current_user_can( 'update_core' ) ) {
								printf(
									' <a href="%s">%s</a>.',
									esc_url( self_admin_url( 'update-core.php' ) ),
									esc_html__( 'Please update WordPress' , 'conversational-forms')
								);
							}
						} elseif ( ! $compatible_php ) {
							esc_html_e( 'This plugin doesn&#8217;t work with your version of PHP.' , 'conversational-forms');
							if ( current_user_can( 'update_php' ) ) {
								printf(
									' <a href="%s">%s</a>.',
									esc_url( wp_get_update_php_url() ),
									esc_html__( 'Learn more about updating PHP' , 'conversational-forms')
								);
								wp_update_php_annotation( '</p><p><em>', '</em>' );
							}
						}
						?>
					</p>
				</div>
			<?php endif; ?>

			<div class="wfb-wpbot-plugin-card__main">
				<div class="wfb-wpbot-plugin-card__icon">
					<?php if ( $plugin_icon_url ) : ?>
						<img src="<?php echo esc_url( $plugin_icon_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
					<?php endif; ?>
				</div>
				<h3 class="wfb-wpbot-plugin-card__title">
					<a href="<?php echo esc_url( $details_link ); ?>" class="wfb-wpbot-more-details"><?php echo esc_html( $title ); ?></a>
				</h3>
			</div>

			<div class="wfb-wpbot-plugin-card__actions">
				<?php if ( ! empty( $action_links ) ) : ?>
					<ul>
						<?php foreach ( $action_links as $action_link ) : ?>
							<li><?php echo $action_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<a class="wfb-wpbot-more-details wpbot_installModal" href="#"><?php esc_html_e( 'More Details', 'conversational-forms'); ?></a>
			</div>

			<div class="wfb-wpbot-modal" id="wfb-wpbot-plugin-modal" role="dialog" aria-labelledby="wfb-wpbot-plugin-modal-title" aria-hidden="true">
				<div class="wfb-wpbot-modal__dialog">
					<button type="button" class="wfb-wpbot-modal__close" aria-label="<?php esc_attr_e( 'Close', 'conversational-forms'); ?>">&times;</button>
					<div class="wfb-wpbot-modal__body">
						<iframe title="<?php echo esc_attr( $title ); ?>" src="<?php echo esc_url( $details_link ); ?>"></iframe>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	?>
</div>
<script>
jQuery(function ($) {
	$('.wpbot_installModal').on('click', function (e) {
		e.preventDefault();
		$('#wfb-wpbot-plugin-modal').addClass('is-open').attr('aria-hidden', 'false');
	});
	$('.wfb-wpbot-modal__close, .wfb-wpbot-modal').on('click', function (e) {
		if (e.target !== this && !$(e.target).hasClass('wfb-wpbot-modal__close')) {
			return;
		}
		$('#wfb-wpbot-plugin-modal').removeClass('is-open').attr('aria-hidden', 'true');
	});
});
</script>
