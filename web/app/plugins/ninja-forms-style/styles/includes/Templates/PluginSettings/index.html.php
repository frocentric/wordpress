<div class="wrap">

    <h1><?php _e( 'Style Settings', 'ninja-forms-styles' ); ?></h1>

    <h2 class="nav-tab-wrapper">
        <?php foreach( $view->get_var( 'groups' ) as $group ): ?>
            <?php $view->get_part( 'tab', $group ); ?>
        <?php endforeach; ?>
    </h2>

    <div id="poststuff">
        <form action="" method="POST">

            <?php
            if( 'field_type' == $view->get_var( 'tab' ) ){
                $view->get_part( 'field-type-selector' );
            }
            ?>

            <?php
            foreach( $view->get_var( 'sections' ) as $section ) {
                $view->get_part( 'postbox', $section );
            }
            ?>

            <p>
                <label>
                    <input type="checkbox" name="advanced" id="advanced_css" value="1"> Show Advanced CSS Properties
                </label>
            </p>

            <input type="hidden" name="update_ninja_forms_style_settings">
            <input type="submit" class="button button-primary" value="Save Settings">

            <div style="float:right;">
                <input
                        onClick="return confirm('This will clear ALL styles. Are you sure?');"
                        type="submit" class="button button-default" name="nuke_styles" value="Clear All Styles">
            </div>

        </form>
    </div>

</div>

