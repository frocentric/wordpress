<style>

    <?php if( $title ): ?>
    /* <?php echo strtoupper( $title ); ?> */
    <?php endif; ?>

    <?php foreach( $styles as $selector => $rules ): ?>
    <?php echo $selector; ?> {
        <?php foreach( $rules as $rule => $value ): ?>
        <?php if( 'show_advanced_css' == $rule ) continue; ?>
        <?php if( 'advanced' != $rule ): ?>
            <?php echo apply_filters( 'ninja_forms_styles_output_rule_' . $rule, $rule ); ?>:<?php echo $value; ?>;
        <?php else: ?>
            <?php echo $value; ?>
        <?php endif; ?>
        <?php endforeach; ?>
    }
    <?php endforeach; ?>
</style>