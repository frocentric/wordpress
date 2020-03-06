<?php

function ninja_forms_register_post_settings_metabox(){

	//Get an array of post types for our post type option.
	$post_types = get_post_types();

	//Remove the built-in post types that we aren't using.
	unset( $post_types['nav_menu_item'] );
	unset( $post_types['mediapage'] );
	unset( $post_types['attachment'] );
	unset( $post_types['revision'] );

	//Loop through the remaining post types and put the array in ['name'] and ['value'] format.
	$tmp_array = array();
	$x = 0;
	foreach( $post_types as $type ){
		$type_obj = get_post_type_object( $type );
		$tmp_array[$x]['name'] = $type_obj->labels->singular_name;
		$tmp_array[$x]['value'] = $type_obj->name;
		$x++;
	}

	$post_types = $tmp_array;

	$args = array(
		'page' => 'ninja-forms',
		'tab' => 'form_settings',
		'slug' => 'create_post',
		'title' => __( 'Post creation settings', 'ninja-forms-pc' ),
		'display_function' => '',
		'state' => 'closed',
		'settings' => array(
			array(
				'name' => 'create_post',
				'type' => 'checkbox',
				'desc' => '',
				'label' => __('Create Post From Input?', 'ninja-forms-pc'),
				'display_function' => '',
				'help' => __('If this box is checked, Ninja Forms will create a post from user input.', 'ninja-forms-pc'),
				'default' => 0,
			),
			array(
				'name' => 'post_as',
				'type' => '',
				'desc' => '',
				'label' => __('Users must be logged in to create post?', 'ninja-forms-pc'),
				'display_function' => 'ninja_forms_metabox_post_as',
				'help' =>'',
			),
			array(
				'name' => 'post_status',
				'type' => 'select',
				'options' => array(
					array('name' => __( 'Draft', 'ninja-forms-pc' ), 'value' => 'draft'),
					array('name' => __( 'Pending', 'ninja-forms-pc' ), 'value' => 'pending'),
					array('name' => __( 'Publish', 'ninja-forms-pc' ), 'value' => 'publish'),
				),
				'desc' => '',
				'label' => __( 'Select a post status', 'ninja-forms-pc'),
				'display_function' => '',
				'help' =>'',
			),
			array(
				'name' => 'post_type',
				'type' => 'select',
				'desc' => '',
				'options' => $post_types,
				'label' => __( 'Select a post type', 'ninja-forms-pc' ),
				'display_function' => '',
				'help' =>'',
				'class' => 'ninja-forms-post-type',
			),
			array(
				'name' => 'post_terms',
				'type' => '',
				'desc' => '',
				'label' => __( 'Default post terms', 'ninja-forms-pc' ),
				'display_function' => 'ninja_forms_metabox_post_terms',
				'help' =>'',
			),
			array(
				'name' => 'post_tags',
				'type' => 'text',
				'label' => __( 'Default post tags', 'ninja-forms-pc' ),
				'display_function' => '',
				'help' =>'',
				'desc' => __( 'Comma separated list', 'ninja-forms-pc' ),
			),
			array(
				'name' => 'post_title',
				'type' => 'text',
				'label' => __( 'Default post title', 'ninja-forms-pc' ),
			),
			array(
				'name' => 'post_content',
				'type' => 'rte',
				'label' => __( 'Default Post Content', 'ninja-forms-pc' ),
				'display_function' => '',
			),
			array(
				'name' => 'post_content_location',
				'type' => 'radio',
				'label' => __( 'Where should the default content be placed?', 'ninja-forms-pc' ),
				'options' => array(
					array( 'name' => 'Before user submitted content', 'value' => 'prepend' ),
					array( 'name' => 'After user submitted content', 'value' => 'append' ),
				),
				'desc' => __( 'If you do not have a "Post Content" field in your form, the default content will be used instead of the main content.', 'ninja-forms-pc' ),
			),
			array(
				'name' => 'post_excerpt',
				'type' => 'rte',
				'label' => __( 'Default Post Excerpt', 'ninja-forms-pc' ),
				'display_function' => '',
			),
		),
	);
	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}
}

add_action( 'admin_init', 'ninja_forms_register_post_settings_metabox', 11 );

function ninja_forms_metabox_post_as($form_id, $data){
	if(isset($data['post_as'])){
		$post_as = $data['post_as'];
	}else{
		$post_as = '';
	}
	?>
	<tr>
		<th>
			<?php _e('Users post as', 'ninja-forms-pc'); ?>
		</th>
		<td>
			<?php if( apply_filters( 'nf_post_creation_user_dropdown', TRUE ) ):?>
			<?php wp_dropdown_users( array('name' => 'post_as', 'id' => 'post_as', 'show_option_all' => __( '- Themselves', 'ninja-forms-pc' ), 'selected' => $post_as) ); ?>
			<?php else: ?>
			<input type="text" name="post_as" id="post_as">
			<p class="description">User ID. The User Select Dropdown has been disabled.</p>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}

function ninja_forms_metabox_post_terms($form_id, $data){

	if( isset( $data['post_type'] ) ){
		$post_type = $data['post_type'];
	}else{
		$post_type = 'post';
	}

	if( isset( $data['post_tax'] ) ){
		$post_tax = $data['post_tax'];
		if( $post_tax == '' ){
			$post_tax = array();
		}
	}else{
		$post_tax = array('category');
	}

	$taxonomies = get_object_taxonomies( $post_type );

	if(is_array($taxonomies) AND !empty($taxonomies)){

		?>
		<tr>
			<th>
				<?php
				_e( 'Default Terms', 'ninja-forms-pc' );
				?>
			</th>
			<td>
				<div>
					<img src="<?php echo NINJA_FORMS_URL;?>/images/ajax-loader.gif" id="post_tax_loader" style="display:none;">
				</div>
				
				<div id="post_tax_div">
					<input type="hidden" name="post_tax" value="">
					<?php
					foreach($taxonomies as $key){
						if($key != 'post_tag' AND $key != 'post_format'){
							$val = get_taxonomies(array('name' => $key), 'objects');
							$val = $val[$key];
							?>
							<label>
								<input type="checkbox" id="" name="post_tax[]" class="ninja-forms-post-tax" value="<?php echo $key;?>" <?php checked( in_array( $key, $post_tax ) );?>> <?php echo $val->label;?>
							</label>
							<?php
						}
						$terms = get_terms( $key, array( 'parent' => 0, 'hide_empty' => false ) );
						if( in_array( $key, $post_tax ) ){
							$display = '';
						}else{
							$display = 'display:none;';
						}
						if( isset( $data[$key.'_terms'] ) ){
							$terms_array = $data[$key.'_terms'];
							if( $terms_array == '' ){
								$terms_array = array();
							}
						}else{
							$terms_array = array();
						}
						?>
						<div id="post_tax_<?php echo $key;?>" class="" style="<?php echo $display;?>">
							<ul class="categorychecklist">
								<input type="hidden" name="<?php echo $key;?>_terms" value="">
							<?php
							foreach($terms as $t){
								if($key != 'post_tag' AND $key != 'post_format'){
									?>
									<li>
										<label>
											<input type="checkbox" id="post_term_<?php echo $t->term_id;?>" name="<?php echo $key;?>_terms[]" value="<?php echo $t->term_id;?>" <?php checked( in_array( $t->term_id, $terms_array ) );?>> <?php echo $t->name;?>
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
												<input type="checkbox" id="post_term_<?php echo $child_term->term_id;?>" name="<?php echo $key;?>_terms[]" value="<?php echo $child_term->term_id;?>" <?php checked( in_array( $child_term->term_id, $terms_array ) );?>> <?php echo $child_term->name;?>
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
					?>
				</div>
			</td>
		</tr>
	<?php
	}
}

function ninja_forms_metabox_post_content($form_id, $data){
	if(isset($data['post_content'])){
		$post_content = $data['post_content'];
	}else{
		$post_content = '';
	}
	if($post_content == ''){
		$display = 'display:none;';
	}else{
		$display = '';
	}
	if(isset($data['post_content_location'])){
		$post_content_location = $data['post_content_location'];
	}else{
		$post_content_location = 'append';
	}
	?>
	<br />
	<div id="post_content_link">
		<a href="#" id="ninja_forms_toggle_post_content"><?php _e( 'View Advanced Post Content Options', 'ninja-forms-pc' );?></a>
	</div>
	<div id="post_content_div" style="<?php echo $display;?>">

		<label for="post_content">
			<?php _e( 'Advanced Post Content', 'ninja-forms-pc' );?>
		</label>
		<p>
			<!-- <textarea id="post_content" name="post_content" class="post-content" rows="10"><?php echo $post_content;?></textarea> -->
			<?php wp_editor( $post_content, 'post_content' ); ?>
		</p>
		<span class="howto">
			<?php _e('This feature can be used to create post content from other Ninja Forms fields. In the textarea above, enter the labels of the fields you wish to include in the content, surrounded by braces.', 'ninja-forms-pc' );?>
			<?php _e('For example, if you wanted to add the values submitted by the user for the First Name and Last Name fields, you would enter:', 'ninja-forms-pc' );?>
			<br />
			<br />
			<?php _e('First Name: [First Name] Last Name: [Last Name].', 'ninja-forms-pc' );?>
			<br />
			<br />
			<?php _e('This would add those values to the post content before it was inserted.', 'ninja-forms-pc' );?>
			<br />
			<br />
			<?php _e('If you do not add a "Post Content" field, this value will be used as the sole Post Content', 'ninja-forms-pc' );?>
		</span>
		<p class="radio">
			<label for="post_content_location_prepend"><input type="radio" id="post_content_location_prepend" name="post_content_location" value="prepend" <?php checked($post_content_location, 'prepend');?>> <?php _e('Prepend to Post Content', 'ninja-forms-pc' );?></label>
		</p>
		<p class="radio">
			<label for="post_content_location_append"><input type="radio" id="post_content_location_append" name="post_content_location" value="append" <?php checked($post_content_location, 'append');?>> <?php _e('Append to Post Content', 'ninja-forms-pc' );?></label>
		</p>
	</div>

	<?php
}