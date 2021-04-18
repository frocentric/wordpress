<div class="wrap">

	<h1><?php _e( 'File Uploads', 'ninja-forms-uploads' ); ?></h1>

	<h2 class="nav-tab-wrapper">
		<?php foreach ( $tabs as $tab => $name ): ?>
			<?php if ( $tab === $active_tab ): ?>
				<span class="nav-tab nav-tab-active"><?php echo $name ?></span>
			<?php else: ?>
				<a href="<?php echo add_query_arg( 'tab', $tab ); ?>" target="" class="nav-tab "><?php echo $name ?></a>
			<?php endif; ?>
		<?php endforeach; ?>
	</h2>

	<div id="poststuff">
		<?php NF_File_Uploads()->template( 'admin-menu-uploads-' . $active_tab, compact( 'save_button_text', 'table' ) ); ?>
	</div>

	<script>
		jQuery( document ).ready( function( $ ) {
			// close postboxes that should be closed
			jQuery( '.if-js-closed' ).removeClass( 'if-js-closed' ).addClass( 'closed' );
		} );
	</script>

</div>