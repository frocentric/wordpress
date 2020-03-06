<?php if( $view->get_var( 'tab' ) == $data[ 'name' ] ): ?>

    <span class="nav-tab nav-tab-active"><?php echo $data[ 'label' ] ?></span>

<?php else: ?>

    <a href="<?php echo add_query_arg( 'tab', $data[ 'name' ], $view->get_var( 'url' ) );?>" target="" class="nav-tab "><?php echo $data[ 'label' ] ?></a>

<?php endif; ?>