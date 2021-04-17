<?php

add_action('admin_init', 'ninja_forms_register_tab_browse_uploads');
function ninja_forms_register_tab_browse_uploads(){
	$args = array(
		'name' => __( 'Browse Uploads', 'ninja-forms-uploads' ),
		'page' => 'ninja-forms-uploads',
		'display_function' => 'ninja_forms_tab_browse_uploads',
		'save_function' => 'ninja_forms_save_browse_uploads',
		'show_save' => false,
		'tab_reload' => false,
	);
	if( function_exists( 'ninja_forms_register_tab' ) ){
		ninja_forms_register_tab('browse_uploads', $args);
	}
}

function ninja_forms_tab_browse_uploads(){
	global $wpdb;
	$plugin_settings = get_option("ninja_forms_settings");

	//Check for filters
	$args = array();
	if(isset($_POST['form_id']) AND $_POST['form_id'] == 'all'){
		unset( $_SESSION['ninja_forms_form_id'] );
	}else if(isset($_POST['form_id']) AND $_POST['form_id'] != 'all'){
		$args['form_id'] = $_POST['form_id'];
		$_SESSION['ninja_forms_form_id'] = $_POST['form_id'];
	}else if(isset($_SESSION['ninja_forms_form_id']) AND $_SESSION['ninja_forms_form_id'] != 'all'){
		$args['form_id'] = $_SESSION['ninja_forms_form_id'];
	}

	if(isset($_POST['begin_date']) AND !empty($_POST['begin_date'])){
		$args['begin_date'] = $_POST['begin_date'];
	}else if(isset($_SESSION['ninja_forms_begin_date']) AND !empty($_SESSION['ninja_forms_begin_date'])){
		$args['begin_date'] = $_SESSION['ninja_forms_begin_date'];
	}

	if(isset($_POST['end_date']) AND !empty($_POST['end_date'])){
		$args['end_date'] = $_POST['end_date'];
	}else if(isset($_SESSION['ninja_forms_end_date']) AND !empty($_SESSION['ninja_forms_end_date'])){
		$args['end_date'] = $_SESSION['ninja_forms_end_date'];
	}

	if(isset($_POST['upload_types']) AND !empty($_POST['upload_types'])){
		$args['upload_types'] = $_POST['upload_types'];
	}else if(isset($_SESSION['ninja_forms_upload_types']) AND !empty($_SESSION['ninja_forms_upload_types'])){
		$args['upload_types'] = $_SESSION['ninja_forms_upload_types'];
	}

	if(isset($_POST['upload_name']) AND !empty($_POST['upload_name'])){
		$args['upload_name'] = $_POST['upload_name'];
	}else if(isset($_SESSION['ninja_forms_upload_name']) AND !empty($_SESSION['ninja_forms_upload_name'])){
		$args['upload_name'] = $_SESSION['ninja_forms_upload_name'];
	}	

	if(isset($_POST['upload_user']) AND !empty($_POST['upload_user'])){
		$args['upload_user'] = $_POST['upload_user'];
	}else if(isset($_SESSION['ninja_forms_upload_user']) AND !empty($_SESSION['ninja_forms_upload_user'])){
		$args['upload_user'] = $_SESSION['ninja_forms_upload_user'];
	}

	if(isset($_POST['order_by']) AND !empty($_POST['order_by'])){
		$args['order_by'] = $_POST['order_by'];
	}else if(isset($_SESSION['ninja_forms_upload_order_by']) AND !empty($_SESSION['ninja_forms_upload_order_by'])){
		$args['order_by'] = $_SESSION['ninja_forms_upload_order_by'];
	}else{
		$args['order_by'] = 'date_updated';
	}

	$all_files = ninja_forms_get_uploads($args);
	$upload_count = count($all_files);

	if(isset($_POST['limit'])){
		$saved_limit = $_POST['limit'];
		$limit = $_POST['limit'];
	}else{
		$saved_limit = 20;
		$limit = 20;
	}

	if($upload_count < $limit){
		$limit = $upload_count;
	}

	if(isset($_REQUEST['paged']) AND !empty($_REQUEST['paged'])){
		$current_page = $_REQUEST['paged'];
	}else{
		$current_page = 1;
	}

	if($upload_count > $limit){
		$page_count = ceil($upload_count / $limit);
	}else{
		$page_count = 1;
	}

	if($current_page > 1){
		$start = (($current_page - 1) * $limit);
		if($upload_count < $limit){
			$end = $upload_count;
		}else{
			$end = $current_page * $limit;
		}

		if($end > $upload_count){
			$end = $upload_count;
		}
	}else{
		$start = 0;
		$end = $limit;
	}

	?>
		<div id="" class="tablenav top">
			<div class="alignleft actions">
				<select id="" class="" name="bulk_action">
					<option value=""><?php _e('Bulk Actions', 'ninja-forms-uploads');?></option>
					<option value="delete"><?php _e('Delete', 'ninja-forms-uploads');?></option>
				</select>
				<input type="submit" name="submit" value="<?php _e( 'Apply', 'ninja-forms-uploads' ); ?>" class="button-secondary">
			</div>
			<div class="alignleft actions">
				<select id="" name="limit">
					<option value="20" <?php selected($saved_limit, 20);?>>20</option>
					<option value="50" <?php selected($saved_limit, 50);?>>50</option>
					<option value="100" <?php selected($saved_limit, 100);?>>100</option>
				</select>
				<?php _e('Uploads Per Page', 'ninja-forms-uploads');?>
				<input type="submit" name="submit" value="<?php _e( 'Go', 'ninja-forms-uploads' ); ?>" class="button-secondary">
			</div>
			<div id="" class="alignright navtable-pages">
				<?php
				if($upload_count != 0 AND $current_page <= $page_count){
				?>
				<span class="displaying-num"><?php if($start == 0){ echo 1; }else{ echo $start; }?> - <?php echo $end;?> of <?php echo $upload_count;?> <?php if($upload_count == 1){ _e('Upload', 'ninja-forms-uploads'); }else{ _e('Uploads', 'ninja-forms-uploads');}?></span>
				<?php
				}
					if($page_count > 1){

						$first_page = add_query_arg( array( 'paged' => 1 ) );
						$last_page = add_query_arg( array( 'paged' => $page_count ) );

						if( $current_page > 1 ){
							$prev_page = $current_page - 1;
							$prev_page = add_query_arg( array('paged' => $prev_page ) );
						}else{
							$prev_page = $first_page;
						}
						if( $current_page != $page_count ){
							$next_page = $current_page + 1;
							$next_page = add_query_arg( array( 'paged' => $next_page ) );
						}else{
							$next_page = $last_page;
						}

				?>
				<span class="pagination-links">
					<a class="first-page disabled" title="<?php _e( 'Go to the first page', 'ninja-forms-uploads' ); ?>" href="<?php echo $first_page;?>">«</a>
					<a class="prev-page disabled" title="<?php _e( 'Go to the previous page', 'ninja-forms-uploads' ); ?>" href="<?php echo $prev_page;?>">‹</a>
					<span class="paging-input"><input class="current-page" title="<?php _e( 'Current page', 'ninja-forms-uploads' ); ?>" type="text" name="paged" value="<?php echo $current_page;?>" size="2"> of <span class="total-pages"><?php echo $page_count;?></span></span>
					<a class="next-page" title="Go to the next page" href="<?php echo $next_page;?>">›</a>
					<a class="last-page" title="Go to the last page" href="<?php echo $last_page;?>">»</a>
				</span>
				<?php
					}
				?>
			</div>
		</div>
		<br />
		<table border="1px" class="wp-list-table widefat fixed posts">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" id="ninja_forms_select_all" class="ninja-forms-uploads-bulk-action"></th>
					<th><?php _e('File Name', 'ninja-forms-uploads');?></th>
					<th><?php _e('Original User Filename', 'ninja-forms-uploads');?></th>
					<th><?php _e('Date', 'ninja-forms-uploads');?></th>
					<th><?php _e('Form Name', 'ninja-forms-uploads');?></th>
					<th><?php _e('User Name', 'ninja-forms-uploads');?></th>
					<th><?php _e('File Location', 'ninja-forms-uploads');?></th>
				</tr>
			</thead>
			<tbody id="ninja_forms_uploads_tbody">
		<?php
		if(is_array($all_files) AND !empty($all_files) AND $current_page <= $page_count){
			for ($i = $start; $i < $end; $i++) {
				$field_id = $all_files[$i]['field_id'];
				$upload_id = $all_files[$i]['id'];
				$date_updated = strtotime($all_files[$i]['date_updated']);
				$date_updated = date('m/d/Y', $date_updated);
				$data = $all_files[$i]['data'];
				$form_row = ninja_forms_get_form_by_field_id($field_id);
				$form_data = $form_row['data'];
				$form_title = isset ( $form_data['form_title'] ) ? $form_data['form_title'] : '';
				if ( isset( $data['user_file_name'] ) ) {
					$user_file_name = stripslashes($data['user_file_name']);
				} else {
					$user_file_name = '';
				}
				
				if(isset($all_files[$i]['user_id']) AND !empty($all_files[$i]['user_id'])){
					$user_data = get_userdata($all_files[$i]['user_id']);
					$user_nicename = $user_data->user_nicename;
				}else{
					$user_nicename = '';
				}

				if ( isset( $data['file_url'] ) ) {
					$file_url = ninja_forms_upload_file_url( $data );
				} else {
					$file_url = '';
				}				

				if ( isset( $data['file_name'] ) ) {
					$file_name = $data['file_name'];
				} else {
					$file_name = '';
				}

				if ( isset( $data['upload_location'] ) ) {
					$upload_location = $data['upload_location'];
				} else {
					$upload_location = 'Server';
				}

				?>
				<tr id="ninja_forms_upload_<?php echo $upload_id;?>_tr">
					<th scope="row" class="check-column">
						<input type="checkbox" id="" name="ninja_forms_uploads[]" value="<?php echo $upload_id;?>" class="ninja-forms-uploads-bulk-action">
					</th>
					<td>
						<a href="<?php echo $file_url;?>" target="_blank"><?php echo $file_name;?></a>
						<div class="row-actions">
							<span class="view"><a class="view" title="<?php _e( 'View this file', 'ninja-forms-uploads' ); ?>" href="<?php echo $file_url;?>" target="_blank" id=""><?php _e( 'View', 'ninja-forms-uploads' ); ?></a> | </span>
							<span class="trash"><a class="trash ninja-forms-delete-upload" title="<?php _e( 'Delete this file', 'ninja-forms-uploads' ); ?>" href="#" id="delete_upload_<?php echo $upload_id;?>"><?php _e( 'Delete', 'ninja-forms-uploads' ); ?></a></span>
						</div>
					</td>
					<td>
						<?php echo $user_file_name;?>
					</td>
					<td>
						<?php echo $date_updated;?>
					</td>
					<td>
						<?php echo $form_title;?>
					</td>
					<td>
						<?php echo $user_nicename;?>
					</td>
					<td>
						<?php echo ucwords( $upload_location );?>
					</td>
				</tr>
			<?php
			}
		}else{
		?>
		<tr id="ninja_forms_files_empty" style="">
			<td colspan="5">
				<?php _e( 'No files found', 'ninja-forms-uploads');?>
			</td>
		</tr>
		<?php
		}
		?>

			</tbody>
			<tfoot>
				<tr>
					<th class="check-column"><input type="checkbox" id="ninja_forms_select_all" class="ninja-forms-uploads-bulk-action"></th>
					<th><?php _e('File Name', 'ninja-forms-uploads');?></th>
					<th><?php _e('Original User Filename', 'ninja-forms-uploads');?></th>
					<th><?php _e('Date', 'ninja-forms-uploads');?></th>
					<th><?php _e('Form Name', 'ninja-forms-uploads');?></th>
					<th><?php _e('User Name', 'ninja-forms-uploads');?></th>
					<th><?php _e('File Location', 'ninja-forms-uploads');?></th>
				</tr>
			</tfoot>
		</table>

	<?php
}

function ninja_forms_save_browse_uploads( $data ){
	if(isset($data['bulk_action']) AND $data['bulk_action'] == 'delete'){
		if(isset($data['ninja_forms_uploads'])){
			if(is_array($data['ninja_forms_uploads']) AND !empty($data['ninja_forms_uploads'])){
				foreach($data['ninja_forms_uploads'] as $upload){
					ninja_forms_delete_upload($upload);
				}
				$update_msg = count( $_POST['ninja_forms_uploads'] ).' ';
				if( count( $_POST['ninja_forms_uploads'] ) > 1 ){
					$update_msg .= __( 'Uploads Deleted', 'ninja-forms-uploads' );

				}else{
					$update_msg .= __( 'Upload Deleted', 'ninja-forms-uploads' );
				}
			}
			return $update_msg;
		}
	}

}