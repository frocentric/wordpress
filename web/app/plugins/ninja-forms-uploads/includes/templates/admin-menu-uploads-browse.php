<form method="get">
	<input type="hidden" name="page" value="ninja-forms-uploads">
	<input type="hidden" name="_total" value="<?php echo esc_attr( $table->get_pagination_arg( 'total_items' ) ); ?>" />
	<input type="hidden" name="_per_page" value="<?php echo esc_attr( $table->get_pagination_arg( 'per_page' ) ); ?>" />
	<input type="hidden" name="_page" value="<?php echo esc_attr( $table->get_pagination_arg( 'page' ) ); ?>" />

	<?php if ( isset( $_REQUEST['paged'] ) ) { ?>
		<input type="hidden" name="paged" value="<?php echo esc_attr( absint( $_REQUEST['paged'] ) ); ?>" />
	<?php } ?>
	<?php
	$table->search_box( __( 'Search Uploads' , 'ninja-forms-uploads' ), 'ninja-forms-uploads' );
	$table->display(); ?>
</form>
