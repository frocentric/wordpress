<div class="postbox">
    <h3 class="hndle">
        <span><?php echo $data[ 'label' ]; ?></span>
        <span class="nf-postbox-controls"></span>
    </h3>
    <div class="inside" style="">
        <table class="form-table">

            <?php
                foreach( $data[ 'settings' ] as $setting ) {
                    echo $view->get_part('postbox-content', $setting );
                }
            ?>

        </table>
    </div>
</div>