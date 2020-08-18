<?php
add_action( 'ninja_forms_edit_field_after_ul', 'ninja_forms_edit_field_close_div' );
function ninja_forms_edit_field_close_div( $form_id ){
	?>
		</div> <!-- DIV 1 -->
	</div> <!-- DIV 2 -->

	<?php
}