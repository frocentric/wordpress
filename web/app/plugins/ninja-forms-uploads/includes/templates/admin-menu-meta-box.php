<div id="ninja_forms_uploads_metabox_<?php echo $group; ?>_settings" class="postbox">
	<h3 class="hndle"><span><?php echo $group_label; ?></span></h3>

	<div class="inside" style="">
		<table class="form-table">
			<tbody>
			<?php

			foreach ( $settings as $key => $setting ) :
				$default = isset( $setting['default'] ) ? $setting['default'] : '';
				$defined = NF_File_Uploads()->controllers->settings->is_defined( $key );
				$value = NF_File_Uploads()->controllers->settings->get_setting( $key, $default );
				?>

				<?php if ( 'prompt' == $setting['type'] ) {
				continue;
			} ?>

				<tr id="row_<?php echo $setting['id']; ?>" style="<?php echo isset( $setting['type'] ) && 'hidden' === $setting['type'] ? 'display: none;' : ''; ?>">
					<th scope="row">
						<?php if ( isset( $setting['label'] ) ) : ?>
							<label for="<?php echo $setting['id']; ?>"><?php echo $setting['label']; ?></label>
						<?php endif; ?>
					</th>
					<td>
						<?php
						switch ( $setting['type'] ) {
							case 'html':
								echo $setting['html'];
								break;
							case 'desc' :
								echo $setting['default'];
								if ( isset( $setting['desc'] ) ) {
									echo "<p class='description'>" . $setting['desc'] . "</p>";
								}
								break;
							case 'textbox' :
								if ( $defined ) {
									echo "<input disabled='disabled' type='text' class='code widefat' name='{$setting['id']}' id='{$setting['id']}' placeholder='Defined in wp-config.php'>";
								} else {
									echo "<input type='text' class='code widefat' name='{$setting['id']}' id='{$setting['id']}' value='{$value}'>";
								}
								if ( isset( $setting['desc'] ) ) {
									echo "<p class='description'>" . $setting['desc'] . "</p>";
								}
								break;
							case 'number' :
								$max = isset( $setting['max'] ) ? 'max="' .$setting['max'] . '"' : '';
								echo '<input type="number" class="code widefat" name="'. $setting['id'] . '" id="' . $setting['id'] . '" value="' . $value . '"' . $max . '>';
								if ( isset( $setting['desc'] ) ) {
									echo "<p class='description'>" . $setting['desc'] . "</p>";
								}
								break;
							case 'checkbox' :
								$checked = ( $value ) ? 'checked' : '';
								echo "<input type='hidden' name='{$setting['id']}' value='0'>";
								echo "<input type='checkbox' name='{$setting['id']}' value='1' id='{$setting['id']}' class='widefat' $checked>";
								echo "<p class='description'>" . $setting['desc'] . "</p>";
								break;
							case 'callback' :
								if ( isset( $setting['display_function'] ) ) {
									call_user_func( $setting['display_function'] );
								}
								break;
						}
						?>
						<?php
						if ( isset( $setting['errors'] ) ) {
							foreach ( $setting['errors'] as $error_id => $error ) {
								echo "<div id='$error_id' class='error'><p>$error</p></div>";
							}
						}
						?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>