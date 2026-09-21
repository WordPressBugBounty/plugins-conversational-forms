<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div id="newfield-tool" class="button button-primary button-small layout-new-form-field" title="<?php esc_attr_e( 'Drag this box onto the layout grid where you want the field to appear', 'conversational-forms'); ?>">
	<i class="icon-edit" style="display:none;"></i>
	<i class="dashicons dashicons-admin-page" style="display:none;"></i>
	<i class="dashicons dashicons-menu" style="display:none;"></i>
	<span id="new-form-element" class="layout_field_name"><span class="dashicons dashicons-move" style="margin: 1px 0px 0px -5px;"></span>
		<?php esc_html_e( 'Add Field', 'conversational-forms'); ?>
	</span>
	<div class="drag-handle">
		<div class="field_preview"></div>
	</div><input value="" type="hidden" class="field-location">	
</div>

<?php
/*
<button class="button button-small compact-mode" style="margin-top:-3px; margin-left:10px;" type="button"><?php _e('Compact', 'qcformbuilder-forms'); ?></button>
*/
?>
<span id="dismiss-add-element" class="ajax-trigger" data-action="wfb_dismiss_pointer" data-pointer="add_element" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wfb_dismiss_pointer' ) ); ?>"></span>
<?php
$haspointer = get_user_meta( get_current_user_id() , 'wfb_pointer_add_element' );
if(empty($haspointer)){ ?>
<script>
	
	jQuery(document).ready( function($) {
		$( '#newfield-tool' ).pointer( {
			content: <?php echo wp_json_encode(
				'<h3>' . esc_html__( 'Form Elements & Fields', 'conversational-forms') . '</h3>'
				. '<p>' . esc_html__( 'Drag the Add Field button onto the layout canvas to add a new conversation step.', 'conversational-forms') . '</p>'
				. '<p class="wfb-pointer-gif"><img src="' . esc_url( WFBCORE_URL . 'assets/images/howto.gif' ) . '" alt=""></p>'
			); ?>,
			pointerClass: 'wp-pointer wfb-add-field-pointer',
			pointerWidth: 420,
			position: {
				edge: 'top',
				align: 'center'
			},
			close: function() {
				$('#dismiss-add-element').trigger('click');
			}
		} ).pointer( 'open' );
	});

</script>
<?php } ?>
<script>
	jQuery(document).on('click','.compact-mode', function(){

		var form = jQuery('.qcformbuilder-forms-options-form'),
			clicked = jQuery(this);

		if(form.hasClass('mini-mode')){
			form.removeClass('mini-mode');
			clicked.removeClass('button-primary');
		}else{
			form.addClass('mini-mode');
			clicked.addClass('button-primary');
		}

	});
</script>