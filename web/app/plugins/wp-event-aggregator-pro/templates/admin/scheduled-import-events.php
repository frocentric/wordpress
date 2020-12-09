<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;
global $importevents;

if( isset( $_GET['edit'] ) && $_GET['edit'] != '' ){
	$scheduled_id = absint( $_GET['edit'] );
	$scheduled_import = get_post( $scheduled_id );
	if( !empty( $scheduled_import ) ){
		$import_eventdata = get_post_meta( $scheduled_import->ID, 'import_eventdata', true );
		?>
		<div class="wpea_container">
    	<div class="wpea_row">
    	<div class="wpea-column wpea_well">
            <h3><?php esc_attr_e( 'Edit scheduled import', 'wp-event-aggregator-pro' ); ?></h3>
            <form method="post" id="wpea_edit_scheduled_import">
           	
               	<table class="form-table">
		            <tbody>
		                <tr>
					        <th scope="row">
					        	<?php esc_attr_e( 'Import name','wp-event-aggregator-pro' ); ?> :
					        </th>
					        <td>
					        	<input type="text" name="scheduled_import_name" required="required" value="<?php echo $scheduled_import->post_title; ?>">
					        </td>
					    </tr>
					    <tr class="import_type_wrapper">
					    	<th scope="row">
					    		<?php esc_attr_e( 'Import frequency','wp-event-aggregator-pro' ); ?> : 
					    	</th>
					    	<td>
						    	<?php $importevents->common->render_import_frequency( $import_eventdata['import_frequency']); ?>
					    	</td>
					    </tr>

					    <?php 
					    $taxonomy_terms = array( 'cats' => array(), 'tags' => array() );
					    if( isset( $import_eventdata['event_cats'] ) ){
					    	$taxonomy_terms['cats'] = $import_eventdata['event_cats'];
					    }
					    if( isset( $import_eventdata['event_tags'] ) ){
					    	$taxonomy_terms['tags'] = $import_eventdata['event_tags'];
					    }
					    $importevents->common->render_import_into_and_taxonomy( $import_eventdata['import_into'], $taxonomy_terms );
					    $importevents->common->render_eventstatus_input( $import_eventdata['event_status'] );
					    ?>
					</tbody>
		        </table>
                
                <div class="wpea_element">
                	<input type="hidden" name="scheduled_id" value="<?php echo $scheduled_import->ID;?>" />
                	<input type="hidden" name="wpea_action" value="wpea_save_scheduled_import" />
                    <?php wp_nonce_field( 'wpea_scheduled_import_nonce_action', 'wpea_scheduled_import_nonce' ); ?>
                    <input type="submit" class="button-primary wpea_submit_button" style=""  value="<?php esc_attr_e( 'Save scheduled import', 'wp-event-aggregator-pro' ); ?>" />
                </div>
            </form>
        </div>
    </div>
</div>
		<?php
	}else{

	}
	
}else{
?>
<div class="wpea_container">
    <div class="wpea_row">
        <div class="">
			<form id="scheduled-import" method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<input type="hidden" name="tab" value="<?php echo $tab = isset($_REQUEST['tab'])? $_REQUEST['tab'] : 'scheduled' ?>" />
				<input type="hidden" name="ntab" value="" />
				<?php
				$listtable = new WP_Event_Aggregator_List_Table();
				$listtable->prepare_items();
				$listtable->display();
        		?>
			</form>
        </div>
    </div>
</div>
<?php
}