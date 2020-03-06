<?php


//Add the ajax listner for changing post types and retrieving new taxonomies.
add_action('wp_ajax_ninja_forms_post_return_tax', 'ninja_forms_post_return_tax');
function ninja_forms_post_return_tax(){
	$form_id = $_REQUEST['form_id'];
	$post_type = $_REQUEST['post_type'];
	$form_row = ninja_forms_get_form_by_id( $form_id );
	$form_data = $form_row['data'];
	$post_tax = $form_data['post_tax'];
	

	$taxonomies = get_object_taxonomies( $post_type );
	foreach($taxonomies as $key){
		if($key != 'post_tag' AND $key != 'post_format'){
			$val = get_taxonomies(array('name' => $key), 'objects');
			$val = $val[$key];
			?>
			<label>
				<input type="checkbox" id="" name="post_tax[]" class="ninja-forms-post-tax" value="<?php echo $key;?>"> <?php echo $val->label;?>
			</label>
			<?php
		}
		$terms = get_terms( $key, array( 'parent' => 0, 'hide_empty' => false ) );
		if( is_array( $post_tax ) AND in_array( $key, $post_tax ) ){
			$display = '';
		}else{
			$display = 'display:none;';
		}
		if( isset( $form_data[$key.'_terms'] ) ){
			$terms_array = $form_data[$key.'_terms'];
		}else{
			$terms_array = array();
		}
		?>
		<div id="post_tax_<?php echo $key;?>" class="" style="<?php echo $display;?>">
			<ul class="categorychecklist">
			<?php
			foreach($terms as $t){
				if($key != 'post_tag' AND $key != 'post_format'){	
					?>
					<li>
						<label>
							<input type="checkbox" id="post_term_<?php echo $t->term_id;?>" name="<?php echo $key;?>_terms[]" value="<?php echo $t->term_id;?>"> <?php echo $t->name;?>
						</label>
					<?php
					$child_terms = get_categories( array( 'child_of' => $t->term_id, 'hide_empty' => false ) );
					if( is_array( $child_terms ) AND !empty( $child_terms ) ){
						?>
						<ul class="children categorychecklist form-no-clear">
						<?php
						foreach( $child_terms as $child_term ){
						?>
						<li>
							<label>
								<input type="checkbox" id="post_term_<?php echo $child_term->term_id;?>" name="<?php echo $key;?>_terms[]" value="<?php echo $child_term->term_id;?>" <?php checked( in_array( $t->term_id, $terms_array ) );?>> <?php echo $child_term->name;?>
							</label>
						</li>
						<?php
						}
						?>
						</ul>
						<?php
					}
					?>
					</li>
					<?php
				}
			}
			?>
			</ul>
		</div>
		<?php
	}
	die();
}