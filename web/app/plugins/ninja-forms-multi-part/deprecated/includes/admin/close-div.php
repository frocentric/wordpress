<?php
function nf_mp_edit_field_close_div( $form_id ){
	?>
		</div> <!-- DIV 1 -->
		<input class="button-primary menu-save nf-save-admin-fields" id="ninja_forms_save_data_top" type="button" value="<?php _e('Save', 'ninja-forms'); ?>" />
		<a href="#" class="button-secondary nf-save-spinner" style="display:none;" disabled><span class="spinner nf-save-spinner" style="float:left;"></span></a>
	</div> <!-- DIV 2 -->

	<?php
}
