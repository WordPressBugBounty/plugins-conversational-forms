<?php
/**
 * Support page -- main view
 *
 * @package Caldera_Forms Modified by QuantumCloud
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

?>
<div class="qcformbuilder-editor-header">
	<ul class="qcformbuilder-editor-header-nav">
		<li class="qcformbuilder-editor-logo">
			<span class="qcformbuilder-forms-name">
				<?php esc_html_e( 'Qcformbuilder Forms: Support', 'qcformbuilder-forms' ); ?>
			</span>

		</li>
		<li class="qcformbuilder-forms-toolbar-link" id="support-nav-info">
			<a href="#info">
				<?php esc_html_e( 'How To Get Support', 'qcformbuilder-forms' ); ?>
			</a>
		</li>
		<li class="qcformbuilder-forms-toolbar-link" id="support-nav-debug">
			<a href="#debug">
				<?php esc_html_e( 'Debug Information', 'qcformbuilder-forms' ); ?>
			</a>
		</li>
		<li class="qcformbuilder-forms-toolbar-link" id="support-nav-beta">
			<a href="#beta">
				<?php esc_html_e( 'Get Latest Beta', 'qcformbuilder-forms' ); ?>
			</a>
		</li>

	</ul>
</div>
<div class="support-admin-page-wrap" style="margin-top: 75px;">
	<div class="support-panel-wrap" id="panel-support-info" style="visibility: visible" aria-hidden="false">
		<?php include WFBCORE_PATH  . 'ui/support/panels/support.php'; ?>
	</div>
	<div class="support-panel-wrap" id="panel-support-debug" style="visibility: hidden" aria-hidden="true">
		<?php include WFBCORE_PATH  . 'ui/support/panels/debug.php'; ?>
	</div>
	<div class="support-panel-wrap" id="panel-support-beta" style="visibility: hidden" aria-hidden="true">
		<?php include WFBCORE_PATH  . 'ui/support/panels/beta.php'; ?>
	</div>
</div>

