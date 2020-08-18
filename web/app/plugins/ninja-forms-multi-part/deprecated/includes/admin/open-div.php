<?php
function nf_mp_edit_field_open_div( $form_id ){
	
	$pages = nf_mp_get_pages( $form_id );

	$page_count = nf_mp_get_page_count( $form_id );
	if ( $page_count > 1 ) {
		$display_page_nav = '';
		$display_enable = 'display:none;';
	} else {
		$display_page_nav = 'display:none;';
		$display_enable = '';
	}

	?>
	<div id="ninja-forms-viewport">
		<input class="button-primary menu-save nf-save-admin-fields" id="ninja_forms_save_data_top" type="button" value="<?php _e( 'Save', 'ninja-forms-mp' ); ?>" />
		<a href="#" class="button-secondary nf-save-spinner" style="display:none;" disabled><span class="spinner nf-save-spinner" style="float:left;"></span></a>
		<input class="button-secondary" id="nf_mp_enable" type="button" value="<?php _e( 'Enable Multi-Part', 'ninja-forms-mp' ); ?>" style="<?php echo $display_enable; ?>">
		<ul id="ninja_forms_mp_pagination" style="<?php echo $display_page_nav; ?>">
			<?php
			nf_mp_admin_page_nav( $form_id );
			?>
		</ul>
		<span class="spinner mp-spinner" style="display:none;"></span>
<?php
}