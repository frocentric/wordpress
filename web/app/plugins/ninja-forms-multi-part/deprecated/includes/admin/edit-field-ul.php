<?php
/**
 * Remove the default admin field editing handlers and attach our own.
 * 
 * @since 1.3
 * @return void
 */
function nf_mp_register_edit_field_ul(){
	if( isset( $_REQUEST['form_id'] ) && ! empty( $_REQUEST['form_id'] ) ){
		$form_id = $_REQUEST['form_id'];

		remove_action( 'ninja_forms_edit_field_ul', 'ninja_forms_edit_field_output_ul' );
		add_action( 'ninja_forms_edit_field_ul', 'nf_mp_slide_container' );
		add_action( 'ninja_forms_edit_field_before_ul', 'nf_mp_edit_field_open_div' );
		add_action( 'ninja_forms_edit_field_after_ul', 'nf_mp_edit_field_close_div' );
	}
}

add_action( 'admin_init', 'nf_mp_register_edit_field_ul' );

/**
 * Output our slide wrapper and field UL
 * 
 * @since 1.3
 * @return void
 */
function nf_mp_slide_container( $form_id ) {
	?>
	<div id="ninja-forms-slide">
	<?php
		nf_mp_edit_field_output_all_uls( $form_id );
}


function nf_mp_edit_field_output_all_uls( $form_id ){
	$pages = nf_mp_get_pages( $form_id );

	if( is_array( $pages ) AND ! empty( $pages ) ){
		foreach( $pages as $page => $data ){
			nf_mp_edit_field_output_ul( $form_id, $page );
		}
	} else {
		nf_mp_edit_field_output_ul( $form_id, 1 );
	}
}

function nf_mp_edit_field_output_ul( $form_id, $page ) {
	$pages = nf_mp_get_pages( $form_id );
	$page_count = nf_mp_get_page_count( $form_id );
	$data = isset ( $pages[ $page ] ) ? $pages[ $page ] : array();
	?>
	<ul class="menu ninja-forms-field-list" id="ninja_forms_field_list_<?php echo $page;?>" data-page="<?php echo $page;?>">
  		<?php
			if( isset( $data['fields'] ) && is_array( $data['fields'] ) ) {
				foreach( $data['fields'] as $field_id ){
					if ( empty ( $field_id ) )
						continue;
					
					$field_type = Ninja_Forms()->form( $form_id )->fields[ $field_id ]['type'];
					if ( $field_type != '_page_divider' || ( $field_type == '_page_divider' && $page_count > 1 ) ) {
						ninja_forms_edit_field( $field_id );
					}
				}
			}
		?>
	</ul>
	<?php
}