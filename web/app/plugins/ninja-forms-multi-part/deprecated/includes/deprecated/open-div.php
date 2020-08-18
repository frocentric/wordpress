<?php
function ninja_forms_edit_field_open_div( $form_id ){
	$all_fields = ninja_forms_get_fields_by_form_id( $form_id );
	$pages = array();
	if( is_array( $all_fields ) AND !empty( $all_fields ) ){
		$pages = array();
		$this_page = array();
		$x = 0;
		foreach( $all_fields as $field ){
			if( $field['type'] == '_page_divider' ){
				$x++;
			}
			$pages[$x][] = $field['id'];
		}
	}
	$page_count = count($pages);
	if( isset( $_REQUEST['_current_page'] ) ){
		$current_page = $_REQUEST['_current_page'];
	}else{
		$current_page = 1;
	}

	if( $current_page > $page_count ){
		$current_page = $page_count;
	}

	$current_page = 1;

	$tmp = $current_page - 1;

	$offset = $tmp * -599.8182067871094;
?>
	<div id="ninja-forms-viewport">
		<input class="button-primary menu-save ninja-forms-save-data" id="ninja_forms_save_data_top" type="submit" value="<?php _e( 'Save Field Settings', 'ninja-forms-mp' ); ?>" />
		<ul id="ninja-forms-mp-pagination">
			<li class="mp-subtract">-</li>
			<span id="mp-page-list">
			<?php
			if( is_array( $pages ) AND !empty( $pages ) ){
				foreach( $pages as $page => $field_id ){
					if( $page == $current_page ){
						$active = 'active';
					}else{
						$active = '';
					}
					?>
					<li class="<?php echo $active;?> mp-page" title="<?php echo $page;?>" id="ninja_forms_mp_page_<?php echo $page;?>"><?php echo $page;?></li>
					<?php
				}
			}
			?>
			</span>
			<li class="mp-add">+</li>
		<span class="spinner" style="float:left;display:none;"></span>
		</ul>
		
		<div id="ninja-forms-slide" style="left: <?php echo $offset;?>px;">

<?php
}