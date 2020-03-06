<?php
/*
 *
 * Function to add a dropdown of terms to the list field.
 *
 * @since 0.7
 * @returns void
 */
function ninja_forms_edit_field_list_term( $field_id ){

    $field_row = ninja_forms_get_field_by_id( $field_id );
    $field_type = $field_row['type'];
    $field_data = $field_row['data'];

    if( isset( $field_data['populate_term'] ) ){
        $populate_term = $field_data['populate_term'];
    }else{
        $populate_term = '';
    }

    $form_row = ninja_forms_get_form_by_field_id( $field_id );

    if( isset( $form_row['data']['post_type'] ) ){
        $post_type = $form_row['data']['post_type'];
    }else{
        $post_type = '';
    }

    if( $field_type == '_list' AND $post_type != '' ){
       ?>
        <div>
            <label>
                <?php _e( 'Populate this with the term: ', 'ninja-forms-pc' );?>
            <select name="ninja_forms_field_<?php echo $field_id;?>[populate_term]">
                <option value=""><?php _e( '- None', 'ninja-forms-pc' );?></option>
                <?php
                 // Get a list of terms registered to the post type set above and loop through them.
                foreach ( get_object_taxonomies( $post_type ) as $tax_name ) {
                    if( $tax_name != 'post_tag' AND $tax_name != 'post_status' ){
                        $tax = get_taxonomy( $tax_name );
                        ?>
                        <option value="<?php echo $tax_name;?>" <?php selected( $populate_term, $tax_name );?>><?php echo $tax->labels->name;?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <?php
    }
}

add_action('ninja_forms_edit_field_after_registered', 'ninja_forms_edit_field_list_term', 9);