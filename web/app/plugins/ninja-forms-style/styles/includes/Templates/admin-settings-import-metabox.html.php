<div class="wrap">

    <form action="" method="post" enctype="multipart/form-data">

        <table class="form-table">
            <tbody>
            <tr id="row_nf_import_styles">
                <th scope="row">
                    <label for="nf_import_styles"><?php echo __( 'Select a file', 'ninja-forms-layout-styles' ); ?></label>
                </th>
                <td>
                    <input type="file" name="nf_import_style" id="nf_import_style" class="widefat">
                </td>
            </tr>
            <tr id="row_nf_import_form_submit">
                <th scope="row">
                </th>
                <td>
                    <input type="submit" name="nf_import_style_submit" id="nf_import_style_submit" class="button-secondary" value="<?php _e( 'Import Default Styles', 'ninja-forms-layout-styles' ) ;?>">
                </td>
            </tr>
            </tbody>
        </table>

    </form>

</div>