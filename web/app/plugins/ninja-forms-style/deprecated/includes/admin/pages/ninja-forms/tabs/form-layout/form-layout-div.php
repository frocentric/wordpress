<?php

add_action( 'init', 'ninja_forms_register_style_layout_tab_div' );
function ninja_forms_register_style_layout_tab_div(){
	add_action( 'ninja_forms_style_layout_tab_div', 'ninja_forms_style_layout_tab_div' );
}

function ninja_forms_style_layout_tab_div( $form_id ){
	if( $form_id != '' ){
		
		$form_row = ninja_forms_get_form_by_id( $form_id );
		$form_data = $form_row['data'];

		if( isset( $form_data['style']['cols'] ) ){
			$cols = $form_data['style']['cols'];
		}else{
			$cols = 1;
		}

		if ( !is_numeric( $cols ) OR $cols == 0 ) {
			$cols = 1;
		}

		$all_fields = ninja_forms_get_fields_by_form_id( $form_id );
		?>
		<br />
		<div>
			<?php _e( 'Columns', 'ninja-forms-style' ); ?>: 
			<select name="cols" id="cols">
				<option value="1" <?php selected( $cols, 1 );?>>1</option>
				<option value="2" <?php selected( $cols, 2 );?>>2</option>
				<option value="3" <?php selected( $cols, 3 );?>>3</option>
				<option value="4" <?php selected( $cols, 4 );?>>4</option>
			</select>
		</div>
		<input type="hidden" name="order" id="order" value="">
		<div id="col_fields">
			<?php

			for ($x=1; $x <= $cols; $x++) { 
				?>
				<input type="hidden" name="col_<?php echo $x;?>" id="col_<?php echo $x;?>" value="">
				<?php
			}

			?>
		</div>
		<div class="layout">
			<?php 
				ninja_forms_style_output_layout_ul( $form_id, $cols );
			?>
		</div><!-- End demo -->

	<?php
	}
}