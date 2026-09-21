<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class="qcformbuilder-backdrop qcformbuilder-forms-insert-modal" style="display: none;"></div>
<form id="qcformbuilderf_forms_shortcode_modal" class="qcformbuilder-modal-wrap qcformbuilder-forms-insert-modal" style="display: none; width: 700px; max-height: 500px; margin-left: -350px;">
	<div class="qcformbuilder-modal-title" id="qcformbuilderf_forms_shortcode_modalTitle" style="display: block;">
		<a href="#close" class="qcformbuilder-modal-closer" data-dismiss="modal" aria-hidden="true" id="qcformbuilderf_forms_shortcode_modalCloser">×</a>
		<h3 class="modal-label" id="qcformbuilderf_forms_shortcode_modalLable"><?php echo esc_html__('Insert Qcformbuilder Form', 'conversational-forms'); ?></h3>
	</div>
	<div class="qcformbuilder-modal-body none" id="qcformbuilderf_forms_shortcode_modalBody" style="width: 70%;">
		<div class="modal-body modal-forms-list">
		<?php

			$forms = Qcformbuilder_Forms_Forms::get_forms( true );
			if(!empty($forms)){
				foreach($forms as $form_id=>$form){

					echo '<div class="modal-list-item"><label><input name="insert_form_id" autocomplete="off" class="selected-form-shortcode" value="' . esc_attr( $form_id ) . '" type="radio">' . esc_html( $form['name'] );
					if(!empty($form['description'])){
						echo '<p style="margin-left: 20px;" class="description"> '. esc_html( $form['description'] ) .' </p>';
					}
					echo ' </label></div>';

				}
			}else{
				echo '<p>' . esc_html__('You don\'t have any forms to insert.', 'conversational-forms') .'</p>';
			}

		?>
		</div>
	</div>
	<div class="qcformbuilder-modal-body none" id="qcformbuilderf_forms_shortcode_modalBody_options" style="left: 70%;">
		<div class="modal-body modal-shortcode-options">
			<h4><?php esc_html_e('Options', 'conversational-forms'); ?></h4>
			<label><input type="checkbox" value="1" class="set_wfb_option set_wfb_modal"> <?php esc_html_e('Set as Modal', 'conversational-forms'); ?></label>
			<div class="modal-forms-setup" style="display:none;">
				<label><?php esc_html_e('Open Modal Trigger Type', 'conversational-forms'); ?></label>
				<select name="modal_button_type" class="modal_trigger_type" style="width: 100%;">
					<option value="link"><?php esc_html_e('Link', 'conversational-forms'); ?></option>
					<option value="button"><?php esc_html_e('Button', 'conversational-forms'); ?></option>					
				</select>
				<label><?php esc_html_e('Open Modal Text', 'conversational-forms'); ?></label>
				<input type="text" name="modal_button_text" class="modal_trigger" style="width: 100%;">
				<label><?php esc_html_e('Modal Width', 'conversational-forms'); ?></label>
				<input type="number" name="modal_width" class="modal_width" style="width: 60px;">px

			</div>

		</div>

	</div>
	<div class="qcformbuilder-modal-footer" id="qcformbuilderf_forms_shortcode_modalFooter" style="display: block;">
	<?php if(!empty($forms)){ ?>		
		<button class="button qcformbuilder-form-shortcode-insert" style="float:right;"><?php esc_html_e('Insert Form', 'conversational-forms'); ?></button>
	<?php }else{ ?>
		<button class="button qcformbuilder-modal-closer"><?php echo esc_html__('Close', 'conversational-forms'); ?></button>
	<?php } ?>
	</div>
</form>
