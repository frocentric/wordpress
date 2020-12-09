<?php

function ninja_forms_mp_nav( $form_id ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$form_data = $ninja_forms_loading->get_all_form_settings();
		$all_fields = $ninja_forms_loading->get_all_fields();
		$pages = $ninja_forms_loading->get_form_setting( 'mp_pages' );
	} else {
		$form_data = $ninja_forms_processing->get_all_form_settings();
		$all_fields = $ninja_forms_processing->get_all_fields();
		$pages = $ninja_forms_processing->get_form_setting( 'mp_pages' );
	}

	$js_transition = 1;

	if( count( $pages ) > 1 ){

		if( isset( $_REQUEST['_current_page'] ) ){
			$current_page = $_REQUEST['_current_page'];
		}else{
			$current_page = 1;
		}

		if( is_object( $ninja_forms_processing ) AND $ninja_forms_processing->get_extra_value( '_current_page' ) AND $form_id == $ninja_forms_processing->get_form_ID() ){
			$current_page = $ninja_forms_processing->get_extra_value( '_current_page' );
		}

		if( is_object( $ninja_forms_processing ) AND $ninja_forms_processing->get_form_setting( 'sub_id' ) && $form_id == $ninja_forms_processing->get_form_ID() ){
			$sub_id = $ninja_forms_processing->get_form_setting( 'sub_id' );
		}else{
			$sub_id = '';
		}

		if ( is_object( $ninja_forms_processing ) AND $ninja_forms_processing->get_error( 'confirm-submit' ) !== false ) {
			$confirm = true;
		} else {
			$confirm = false;
		}

		$page_count = count($pages);

		$x = $current_page + 1;

		if ( function_exists( 'ninja_forms_conditionals_field_filter' ) ) {
			$show_next = false;
			while( $x <= $page_count ){
				if( ninja_forms_mp_check_page_conditional( $form_id, $x ) ){
					$show_next = true;
					break;
				}
				$x++;
			}
		} else {
			$show_next = true;
		}

		if ( !$confirm ) {
			$style = '';
		} else {
			$style = 'style="display:none;"';
		}

		?>
		
		<input type="hidden" name="_current_page" value="<?php echo $current_page;?>">
		<div id="ninja_forms_form_<?php echo $form_id;?>_mp_nav_wrap" class="ninja-forms-mp-nav-wrap" <?php echo $style;?>>
			<?php
			if( $js_transition == 1 ){

				if( $current_page == 1 AND $current_page < $page_count ){
					$prev_style = 'display:none;';
					$next_style = '';
					if( !$show_next ){
						$next_style = 'display:none;';
					}
				}else if( $current_page > 1 AND $current_page < $page_count ){
					$prev_style = '';
					$next_style = '';
					if( !$show_next ){
						$next_style = 'display:none;';
					}
				}else if( $current_page == $page_count ){
					$prev_style = '';
					$next_style = 'display:none;';
				}

				$plugin_settings = nf_get_settings();

				$next_label = apply_filters( 'nf_multi_part_next_label', $plugin_settings['mp_next'] );
				$prev_label = apply_filters( 'nf_multi_part_previous_label', $plugin_settings['mp_previous'] );
				?>
				<input type="submit" name="_prev" class="ninja-forms-mp-nav ninja-forms-mp-prev" id="ninja_forms_form_<?php echo $form_id;?>_mp_prev" value="<?php echo $prev_label;?>" style="<?php echo $prev_style;?>">
				<input type="submit" name="_next" class="ninja-forms-mp-nav ninja-forms-mp-next" id="ninja_forms_form_<?php echo $form_id;?>_mp_next" value="<?php echo $next_label;?>" style="<?php echo $next_style;?>">	
				<?php
			}else{
				if( $current_page != 1 AND $next_style ){
					?>
					<input type="submit" name="_prev" class="ninja-forms-mp-nav ninja-forms-mp-prev" id="ninja_forms_form_<?php echo $form_id;?>_mp_prev" value="<?php echo $prev_label;?>">
					<?php
				}
				if( $current_page < $page_count ){
					?>
					<input type="submit" name="_next" class="ninja-forms-mp-nav ninja-forms-mp-next" id="ninja_forms_form_<?php echo $form_id;?>_mp_next" value="<?php echo $next_label;?>">
					<?php
				}				
			}
			?>
		</div>
		<?php
	}
}

add_action( 'ninja_forms_display_after_fields', 'ninja_forms_mp_nav', 20 );