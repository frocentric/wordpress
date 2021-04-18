<?php
add_action( 'init', 'ninja_forms_register_uploads_help' );
function ninja_forms_register_uploads_help(){
	$args = array(
		'page' => 'ninja-forms',
		'tab' => 'builder',
		'title' => 'File Renaming',
		'display_function' => 'ninja_forms_help_uploads',
	);
	if( function_exists( 'ninja_forms_register_help_screen_tab' ) ){
		ninja_forms_register_help_screen_tab('upload_help', $args);			
	}
}

function ninja_forms_help_uploads(){
	?>
	<p><?php _e('If you leave the advanced rename box empty, the uploaded file will retain the original user\'s filename. (With any special characters removed.)', 'ninja-forms-uploads');?></p>
	</p><?php _e('If you want to rename the file, however, you can. These are the conventions that Ninja Forms understands, and their effect.', 'ninja-forms-uploads');?>
		<ul>
			<li><span class="code">%filename%</span> - <?php _e('The file\'s original filename, with any special characters removed.', 'ninja-forms-uploads');?></li>
			<li><span class="code">%formtitle%</span> - <?php _e('The title of the current form, with any special characters removed.', 'ninja-forms-uploads');?></li>
			<li><span class="code">%username%</span> - <?php _e('The WordPress username for the user, if they are logged in.', 'ninja-forms-uploads');?></li>
			<li><span class="code">%userid%</span> - <?php _e('The WordPress ID (int) for the user, if they are logged in.', 'ninja-forms-uploads');?></li>
			<li><span class="code">%displayname%</span> - <?php _e('The WordPress displayname for the user, if they are logged in.', 'ninja-forms-uploads');?></li>
			<li><span class="code">%lastname%</span> - <?php _e('The WordPress lastname for the user, if they are logged in.', 'ninja-forms-uploads');?></li>
			<li><span class="code">%firstname%</span> - <?php _e('The WordPress firstname for the user, if they are logged in.', 'ninja-forms-uploads');?></li>
			<li><span class="code">%date%</span> - <?php _e('Today\'s date in yyyy-mm-dd format.', 'ninja-forms-uploads');?></li>
			<li><span class="code">%month%</span> - <?php _e('Today\'s month in mm format.', 'ninja-forms-uploads');?></li>
			<li><span class="code">%day%</span> - <?php _e('Today\'s day in dd format.', 'ninja-forms-uploads');?></li>
			<li><span class="code">%year%</span> - <?php _e('Today\'s year in yyyy format.', 'ninja-forms-uploads');?></li>
			<li><span class="code">%field_x%</span> - <?php _e('Another field in your form, where x is the field id.', 'ninja-forms-uploads');?></li>
		</ul>
	</p>
	<p>
		<?php _e('Any characters other than letters, numbers, dashes (-) and those on the list above will be removed. This includes spaces.', 'ninja-forms-uploads');?>
	</p>
	<p>
		<?php _e('An Example', 'ninja-forms-uploads');?>: <span class="code">%date%-%filename%</span>
	</p>
	<p>
		<?php _e('Would Yield', 'ninja-forms-uploads');?>: <span class="code">2011-07-09-myflowers.jpg</span>
	<p>
	<?php
}