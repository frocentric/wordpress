<select name="form_id" id="form_id">
	<option value="0"><?php echo __( '- Select a form', 'ninja-forms-uploads' ); ?></option>
	<?php foreach ( $form_options as $id => $title ): ?>
		<option value="<?php echo $id; ?>" <?php if ( $id == $form_selected ) {
			echo 'selected';
		} ?>>
			<?php echo $title; ?>
		</option>
	<?php endforeach; ?>
</select>

<input type="text" name="begin_date" class="datepicker" placeholder="<?php echo __( 'Being Date', 'ninja-forms-uploads' ); ?>" value="<?php echo $begin_date; ?>">

<input type="text" name="end_date" class="datepicker" placeholder="<?php echo __( 'End Date', 'ninja-forms-uploads' ); ?>" value="<?php echo $end_date; ?>">
<input type="submit" class="button" name="filter_action" value="<?php _e( 'Filter', 'ninja-forms-uploads' ); ?>">
<script>
	jQuery( document).ready( function($) {

		$( '.datepicker').datepicker();
	});
</script>
