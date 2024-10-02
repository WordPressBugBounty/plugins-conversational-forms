<?php
if (defined('ABSPATH') === false) {
    exit;
}

?>
<?php
require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
remove_all_filters('plugins_api');
$qcld_chatplugintags = array(
    'a'       => array(
        'href'   => array(),
        'title'  => array(),
        'target' => array(),
    ),
);

$qcld_plugininstal = array();

/* Conversational Forms Plugins */
    $argus = [
        'slug' => 'chatbot',
        'fields' => [
            'short_description' => true,
            'icons' => true,
            'reviews'  => false, // excludes all reviews
        ],
    ];

    $data = plugins_api( 'plugin_information', $argus );
    if ( $data && ! is_wp_error( $data ) ) {
        $qcld_plugininstal['convers-form'] = $data;
        $qcld_plugininstal['convers-form']->name = 'ChatBot';
    }
?>

<div class="wrap recommended-plugins">
    <div class="wp-list-table widefat plugin-install">
        <div class="the-list">
            <?php
            foreach ( (array) $qcld_plugininstal as $plugin ) {
                if ( is_object( $plugin ) ) {
                    $plugin = (array) $plugin;
                }

                // Display the group heading if there is one.
                if ( isset( $plugin['group'] ) && $plugin['group'] != $group ) {
                    if ( isset( $this->groups[ $plugin['group'] ] ) ) {
                        $group_name = $this->groups[ $plugin['group'] ];
                        if ( isset( $plugins_group_titles[ $group_name ] ) ) {
                            $group_name = $plugins_group_titles[ $group_name ];
                        }
                    } else {
                        $group_name = $plugin['group'];
                    }

                    // Starting a new group, close off the divs of the last one.
                    if ( ! empty( $group ) ) {
                        echo '</div></div>';
                    }

                    echo '<div class="plugin-group"><h3>' . esc_html( $group_name ) . '</h3>';
                    // Needs an extra wrapping div for nth-child selectors to work.
                    echo '<div class="plugin-items">';

                    $group = $plugin['group'];
                }
                $title = wp_kses( $plugin['name'], $qcld_chatplugintags );

                // Remove any HTML from the description.
                $description = wp_strip_all_tags( $plugin['short_description'] );
                $version     = wp_kses( $plugin['version'], $qcld_chatplugintags );

                $name = wp_strip_all_tags( $title . ' ' . $version );

                $author = wp_kses( $plugin['author'], $qcld_chatplugintags );
                if ( ! empty( $author ) ) {
                    /* translators: %s: Plugin author. */
                    $author = ' <cite>' . sprintf( __( 'By %s' ), $author ) . '</cite>';
                }

                $requires_php = isset( $plugin['requires_php'] ) ? $plugin['requires_php'] : null;
                $requires_wp  = isset( $plugin['requires'] ) ? $plugin['requires'] : null;

                $compatible_php = is_php_version_compatible( $requires_php );
                $compatible_wp  = is_wp_version_compatible( $requires_wp );
                $tested_wp      = ( empty( $plugin['tested'] ) || version_compare( get_bloginfo( 'version' ), $plugin['tested'], '<=' ) );

                $action_links = array();

                if ( current_user_can( 'install_plugins' ) || current_user_can( 'update_plugins' ) ) {
                    $status = install_plugin_install_status( $plugin );

                    switch ( $status['status'] ) {
                        case 'install':
                            if ( $status['url'] ) {
                                if ( $compatible_php && $compatible_wp ) {
                                    $action_links[] = sprintf(
                                        '<a class="install-now button" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
                                        esc_attr( $plugin['slug'] ),
                                        esc_url( $status['url'] ),
                                        /* translators: %s: Plugin name and version. */
                                        esc_attr( sprintf( _x( 'Install %s now', 'plugin' ), $name ) ),
                                        esc_attr( $name ),
                                        __( 'Install Now' )
                                    );
                                } else {
                                    $action_links[] = sprintf(
                                        '<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
                                        _x( 'Cannot Install', 'plugin' )
                                    );
                                }
                            }
                            break;

                        case 'update_available':
                            if ( $status['url'] ) {
                                if ( $compatible_php && $compatible_wp ) {
                                    $action_links[] = sprintf(
                                        '<a class="update-now button aria-button-if-js" data-plugin="%s" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
                                        esc_attr( $status['file'] ),
                                        esc_attr( $plugin['slug'] ),
                                        esc_url( $status['url'] ),
                                        /* translators: %s: Plugin name and version. */
                                        esc_attr( sprintf( _x( 'Update %s now', 'plugin' ), $name ) ),
                                        esc_attr( $name ),
                                        __( 'Update Now' )
                                    );
                                } else {
                                    $action_links[] = sprintf(
                                        '<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
                                        _x( 'Cannot Update', 'plugin' )
                                    );
                                }
                            }
                            break;

                        case 'latest_installed':
                        case 'newer_installed':
                            if ( is_plugin_active( $status['file'] ) ) {
                                $action_links[] = sprintf(
                                    '<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
                                    _x( 'Active', 'plugin' )
                                );
                            } elseif ( current_user_can( 'activate_plugin', $status['file'] ) ) {
                                $button_text = __( 'Activate' );
                                /* translators: %s: Plugin name. */
                                $button_label = _x( 'Activate %s', 'plugin' );
                                $activate_url = add_query_arg(
                                    array(
                                        '_wpnonce' => wp_create_nonce( 'activate-plugin_' . $status['file'] ),
                                        'action'   => 'activate',
                                        'plugin'   => $status['file'],
                                    ),
                                    network_admin_url( 'plugins.php' )
                                );

                                if ( is_network_admin() ) {
                                    $button_text = __( 'Network Activate' );
                                    /* translators: %s: Plugin name. */
                                    $button_label = _x( 'Network Activate %s', 'plugin' );
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
                                    _x( 'Installed', 'plugin' )
                                );
                            }
                            break;
                    }
                }

                $details_link = self_admin_url(
                    'plugin-install.php?tab=plugin-information&amp;plugin=' . $plugin['slug'] .
                    '&amp;width=700&amp;height=550'
                );
                $action_links[] = sprintf( '%s','<a data-toggle="modal" class="wpbot_installModal" data-target="">More Details</a><div class="modal fade baldrick-modal-wrap" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"><iframe width="100%" height="550" src="'.$details_link.'"></iframe></div></div></div></div>');
                /*===show icon ==*/
                if ( ! empty( $plugin['icons']['svg'] ) ) {
                    $plugin_icon_url = $plugin['icons']['svg'];
                } elseif ( ! empty( $plugin['icons']['2x'] ) ) {
                    $plugin_icon_url = $plugin['icons']['2x'];
                } elseif ( ! empty( $plugin['icons']['1x'] ) ) {
                    $plugin_icon_url = $plugin['icons']['1x'];
                } else {
                    $plugin_icon_url = $plugin['icons']['default'];
                }
                /*
                 * $action_links An array of plugin action links. Defaults are links to Details and Install Now.
                 * $plugin The plugin currently being listed.
                 */
                $action_links = apply_filters( 'plugin_install_action_links', $action_links, $plugin );

                $last_updated_timestamp = strtotime( $plugin['last_updated'] );
                ?>
                <div class="cxsc-settings-blocks-addon">
                <strong><?php esc_html_e('Conversational Form '); ?></strong><?php esc_html_e('Builder works with'); ?><strong><?php esc_html_e(' WPBot ChatBot.'); ?></strong></br>
                <?php esc_html_e('After creating a '); ?><strong><?php esc_html_e('Conversational Form,'); ?></strong><?php esc_html_e(' you can add it to the '); ?><strong><?php esc_html_e('ChatBot`s Start Menu '); ?></strong><?php esc_html_e('from the '); ?><strong><?php esc_html_e('ChatBot Settings->Start Menu'); ?></strong></br>
                <?php esc_html_e('You can use conversational forms for '); ?><strong><?php esc_html_e('Lead Generation, '); ?></strong><?php esc_html_e('to Collect Information from the users, and to '); ?><strong><?php esc_html_e(' create Button (menu) '); ?></strong><?php esc_html_e('Driven Conversations.'); ?>
              
                </div>
                <div class="plugin-card plugin-card-<?php echo sanitize_html_class( $plugin['slug'] ); ?>">
                    <?php
                    if ( ! $compatible_php || ! $compatible_wp ) {
                        echo '<div class="notice inline notice-error notice-alt"><p>';
                        if ( ! $compatible_php && ! $compatible_wp ) {
                            esc_html_e( 'This plugin doesn&#8217;t work with your versions of WordPress and PHP.' );
                            if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
                                printf(
                                /* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
                                    '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.',
                                    esc_url( self_admin_url( 'update-core.php' ) ),
                                    esc_url( wp_get_update_php_url() )
                                );
                                wp_update_php_annotation( '</p><p><em>', '</em>' );
                            } elseif ( current_user_can( 'update_core' ) ) {
                                printf(
                                /* translators: %s: URL to WordPress Updates screen. */
                                    '<a href="%s">Please update WordPress</a>.',
                                    esc_url( self_admin_url( 'update-core.php' ) )
                                );
                            } elseif ( current_user_can( 'update_php' ) ) {
                                printf(
                                /* translators: %s: URL to Update PHP page. */
                                    '<a href="%s">Learn more about updating PHP</a>.',
                                    esc_url( wp_get_update_php_url() )
                                );
                                wp_update_php_annotation( '</p><p><em>', '</em>' );
                            }
                        } elseif ( ! $compatible_wp ) {
                            esc_html_e( 'This plugin doesn&#8217;t work with your version of WordPress.' );
                            if ( current_user_can( 'update_core' ) ) {
                                printf(
                                /* translators: %s: URL to WordPress Updates screen. */
                                    '<a href="%s">Please update WordPress</a>.',
                                    esc_url( self_admin_url( 'update-core.php' ) )
                                );
                            }
                        } elseif ( ! $compatible_php ) {
                            esc_html_e( 'This plugin doesn&#8217;t work with your version of PHP.' );
                            if ( current_user_can( 'update_php' ) ) {
                                printf(
                                /* translators: %s: URL to Update PHP page. */
                                    '<a href="%s">Learn more about updating PHP</a>.',
                                    esc_url( wp_get_update_php_url() )
                                );
                                wp_update_php_annotation( '</p><p><em>', '</em>' );
                            }
                        }
                        echo '</p></div>';
                    }
                    ?>
                    <div class="plugin-card-top">
                        <div class="name column-name">
                            <h3>
                                <a href="<?php echo esc_url( $details_link ); ?>" class="thickbox open-plugin-details-modal">
                                    <?php echo esc_attr($title); ?>
                                    <img src="<?php echo esc_attr( $plugin_icon_url ); ?>" class="plugin-icon" alt="" />
                                </a>
                            </h3>
                        </div>
                        <div class="action-links">
                            <?php
                            if ( $action_links ) {
                                // phpcs:ignore
                                echo '<ul class="plugin-action-buttons"><li>' . implode( '</li><li>', $action_links ) . '</li></ul>';
                            }
                            ?>
                        </div>

                    </div>
                </div>
                <?php
            } ?>
        </div>
    </div>

</div>
<style>
.baldrick-modal-wrap{
    display: none;
}
.plugin-card .baldrick-modal-wrap {
    position: absolute;
    top: 20px;
    right: auto;
    width: 800px;
    left: auto;
    margin: 0 auto;
}
.baldrick-modal-wrap ul .plugin-action-buttons {
    width: 50%;
    margin: 0 auto;
    text-align: center;
}
div#myModal.baldrick-modal-wrap {
    position: fixed;
    top: 20px;
    right: 0;
    width: 100%;
    left: 0;
    margin: 0 auto;
    background: #0000008a;
    height: 100%;
}
.baldrick-modal-wrap .modal-content {
    margin: 0 auto;
    text-align: CENTER;
    left: 0;
    right: 0;
    position: fixed;
    max-width: 950px;
}
.baldrick-modal-wrap .modal-dialog {
    height: 100%;
    display: flex;
    align-items: center;
}
.baldrick-modal-wrap button.close {
    background: #232323;
    border: none;
    color: #fff;
    font-size: 20px;
    padding: 10px 12px;
    line-height: 10px;
}
.baldrick-modal-wrap .modal-header {
    text-align: right;
    position: absolute;
    right: 0;
}
.cxsc-settings-blocks-addon {
  box-shadow: rgb(0 0 0 / 24%) 0px 3px 8px;
  padding: 12px;
  margin: 0 10px 20px 10px;
  border-radius: 6px;
  border: 1px solid #8c8f949c;
  font-size: 15px;
  line-height: 25px;
  width: 75%;
  max-width: 570px;
}
.baldrick-modal-wrap .plugin-icon {
    position: absolute;
    top: 35px;
    left: 20px;
    width: 92px;
    height: 92px;
    margin: 0;
    bottom: 0;
}

.baldrick-modal-wrap .plugin-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.baldrick-modal-wrap .plugin-card .name {
    margin-left: 120px;
}
.baldrick-modal-wrap ul.plugin-action-buttons {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.baldrick-modal-wrap .plugin-card .action-links {
    position: relative;
    top: 0;
    right: 0;
    width: auto;
}
.plugin-card-chatbot{
  max-width: 600px;
  width: 600px;
}
</style>
<script>
jQuery( ".wpbot_installModal" ).on( "click", function() {
		jQuery('.baldrick-modal-wrap').show();
})
jQuery( ".baldrick-modal-wrap" ).on( "click",'.close', function() {
		jQuery('.baldrick-modal-wrap').hide();
})
</script>