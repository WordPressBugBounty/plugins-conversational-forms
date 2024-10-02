		<?php
			$entry_perpage = get_option( '_qcformbuilder_forms_entry_perpage', 20 );
		?>
		<div class="qcformbuilder-entry-exporter" style="display:none;">
			<?php wp_nonce_field( 'wfb_toolbar', 'wfb_toolbar_actions' ); ?>
			<span class="wfb-tools-row">
							
				<span class="toggle_option_preview">

					<button type="button" class="status_toggles button button-primary ajax-trigger" style="margin-top: 1px;"
						data-before="wfb_set_limits"
						data-action="browse_entries"
						data-target="#form-entries-viewer"
						data-form=""
						data-template="#forms-list-alt-tmpl"
						data-load-class="spinner"
						data-active-class="button-primary"
						data-group="status_nav"
						data-callback="setup_pagination"
						data-page="1"
					    data-nonce="<?php echo esc_attr( wp_create_nonce( 'view_entries' ) ); ?>"
						data-status="active"
					    data-perpage="<?php echo esc_attr( Qcformbuilder_Forms_Entry_Viewer::entries_per_page() ); ?>"
					><?php esc_html_e('Active', 'qcformbuilder-forms'); ?> <span class="current-status-count"></span></button>
					<button type="button" class="status_toggles button ajax-trigger" style="margin-top: 1px; margin-right: 10px;"
						data-before="wfb_set_limits"
						data-action="browse_entries"
						data-target="#form-entries-viewer"
						data-form=""
						data-template="#forms-list-alt-tmpl"
						data-load-class="spinner"
						data-active-class="button-primary"
						data-group="status_nav"
						data-callback="setup_pagination"
						data-page="1"
						data-perpage="<?php echo esc_attr( Qcformbuilder_Forms_Entry_Viewer::entries_per_page() ); ?>"
						data-nonce="<?php echo esc_attr( wp_create_nonce( 'view_entries' ) ); ?>"
				        data-status="trash"
					><?php echo esc_html_x( 'Trash', 'Status: In trash', 'qcformbuilder-forms' ); ?> <span class="current-status-count"></span></button>
				</span>
				<!--
				<a href="" role="button" class="button qcformbuilder-forms-entry-exporter">
					<?php esc_html_e( 'Export All', 'qcformbuilder-forms' ); ?>
				</a>
				-->
				
			</span>
			<span class="wfb-tools-row wfb-tools-row-second">
				<label for="wfb_bulk_action" class="screen-reader-text">
					<?php esc_html_e( 'Bulk Action For Entries', 'qcformbuilder-forms' ); ?>
				</label>
				<select id="wfb_bulk_action" name="action" style="vertical-align: top;">
					<option selected="selected" value="">
						<?php esc_html_e( 'Bulk Actions', 'qcformbuilder-forms' ); ?>
					</option>
					
					<option value="trash">
						<?php esc_html_e( 'Move to Trash', 'qcformbuilder-forms' ); ?>
					</option>
				</select>
				<button type="button" class="button wfb-bulk-action">
					<?php esc_html_e( 'Apply', 'qcformbuilder-forms' ); ?>
				</button>
			</span>

		</div>

		<?php
		/**
		 * Runs at the end of the entries toolbar
		 *
		 * @since unknown
		 */
		do_action('qcformbuilder_forms_entries_toolbar');
		?>

