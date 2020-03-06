<p>
    <select name="" id="ninja-forms-styles-field-type-selector">
        <option value=""><?php echo __( 'Select a Field Type', 'ninja-forms-layout-styles' ); ?></option>
        <?php foreach( Ninja_Forms()->fields as $field ): ?>
            <?php if( in_array( $field->get_type(), array( 'hidden', 'note', 'creditcard', 'unknown' ) ) ) continue; ?>
            <option value="<?php echo $field->get_name(); ?>"><?php echo $field->get_nicename(); ?></option>
        <?php endforeach; ?>
    </select>
</p>