<?php

add_action( 'init', 'ninja_forms_register_style_layout_tab_mp_div' );
function ninja_forms_register_style_layout_tab_mp_div(){
	if( isset( $_REQUEST['form_id'] ) ){
		$form_id = $_REQUEST['form_id'];
	}else{
		$form_id = '';
	}

	$enabled = false;
	if ( function_exists( 'nf_mp_get_page_count' ) ) {
		if ( nf_mp_get_page_count( $form_id ) > 1 ) {
			$enabled = true;
		}
	} else if ( function_exists( 'ninja_forms_get_form_by_id' ) ) {
		$form_row = ninja_forms_get_form_by_id( $form_id );
		$form_data = $form_row['data'];
		if( isset( $form_data['multi_part'] ) AND $form_data['multi_part'] == 1 ){
			$enabled = true;
		}
	}

	if( $form_id != '' ){
		if ( $enabled ) {
			remove_action( 'ninja_forms_style_layout_tab_div', 'ninja_forms_style_layout_tab_div' );
			add_action( 'ninja_forms_style_layout_tab_div', 'ninja_forms_style_layout_tab_mp_div' );	
		}
	}
}

function ninja_forms_style_layout_tab_mp_div( $form_id ){
	global $ninja_forms_fields;

	if ( ! function_exists( 'ninja_forms_get_form_by_id' ) )
		return false;

	$form_row = ninja_forms_get_form_by_id( $form_id );
	$form_data = $form_row['data'];
	$all_fields = ninja_forms_get_fields_by_form_id( $form_id );
	$pages = array();
	if( is_array( $all_fields ) AND !empty( $all_fields ) ){
		$pages = array();
		$this_page = array();
		$x = 0;
		foreach( $all_fields as $field ){
			if ( ! isset ( $ninja_forms_fields[ $field['type'] ] ) )
				continue;
			if( $field['type'] == '_page_divider' ){
				$x++;
			}
			$pages[$x][] = $field['id'];
		}
	}
	$page_count = count($pages);
	if( isset( $_REQUEST['mp_page'] ) ){
		$current_page = $_REQUEST['mp_page'];
	}else{
		$current_page = 1;
	}

	if( $current_page > $page_count ){
		$current_page = $page_count;
	}

	$tmp = $current_page - 1;

	$offset = $tmp * -450;

?>
	<input type="hidden" id="mp_form" value="1">
	<input type="hidden" id="mp_page" name="mp_page" value="<?php echo $current_page;?>">
	<div id="ninja-forms-style-viewport">
		
		<ul id="ninja-forms-style-mp-pagination">
			<li class="style-mp-subtract">-</li>
			<span id="style-mp-page-list">
			<?php
			if( is_array( $pages ) AND !empty( $pages ) ){
				foreach( $pages as $page => $field_id ){
					if( $page == $current_page ){
						$active = 'active';
						?>
						<input type="hidden" id="_current_page" name="_current_page" value="<?php echo $current_page;?>">
						<?php
					}else{
						$active = '';
					}
					?>
					<li class="<?php echo $active;?> style-mp-page" title="<?php echo $page;?>" id="ninja_forms_style_mp_page_<?php echo $page;?>"><?php echo $page;?></li>
					<?php
				}
			}
			?>
			</span>
			<li class="style-mp-add">+</li>
		<span class="spinner" style="float:left;display:none;"></span>
		</ul>
		<div id="ninja-forms-slide" style="left: <?php echo $offset;?>px;">
		<?php
			if( is_array( $pages ) AND !empty( $pages ) ){
				foreach( $pages as $page => $fields ){
					if( isset( $form_data['style']['mp'][$page]['cols'] ) ){
						$cols = $form_data['style']['mp'][$page]['cols'];
					}else{
						$cols = 1;
					}

					?>
					<div id="ninja_forms_style_mp_<?php echo $page;?>" class="style-layout">
						<div>
							<?php _e( 'Columns', 'ninja-forms-style' ); ?>: 
							<select name="cols_<?php echo $page;?>" id="cols_<?php echo $page;?>" class="ninja-forms-style-col">
								<option value="1" <?php selected( $cols, 1 );?>>1</option>
								<option value="2" <?php selected( $cols, 2 );?>>2</option>
								<option value="3" <?php selected( $cols, 3 );?>>3</option>
								<option value="4" <?php selected( $cols, 4 );?>>4</option>
							</select>
						</div>
						<input type="hidden" name="order_<?php echo $page;?>" id="order_<?php echo $page;?>" value="" class="field-order">
						<div id="col_fields_<?php echo $page;?>">
							<?php
							for ($x=1; $x <= $cols; $x++) { 
								?>
								<input type="hidden" name="col_<?php echo $x;?>_<?php echo $page;?>" id="col_<?php echo $x;?>_<?php echo $page;?>" value="" class="col-fields">
								<?php
							}
							?>
						</div>

						<?php
							ninja_forms_style_output_layout_ul( $form_id, $cols, $fields, $page );
						?>

					</div>
				<?php
				}
			}
			?>
		</div>
	</div>
<?php
}