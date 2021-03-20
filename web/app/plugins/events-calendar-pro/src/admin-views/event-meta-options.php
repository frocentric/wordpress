<div id="modern-tribe-info">

	<h2><?php esc_html_e( 'Additional Fields', 'tribe-events-calendar-pro' ); ?></h2>

	<p><?php printf( esc_html__( 'Add additional fields to your event admin pages by configuring the fields and options here. Your custom additional fields then display on the event admin so that anyone creating events can add unique event information such as food options, recommended age groups, or parking details. %s', 'tribe-events-calendar-pro' ), '<a href="https://theeventscalendar.com/knowledgebase/pro-additional-fields/?utm_campaign=in-app&utm_medium=plugin-ecp&utm_source=settings" target="_blank">' . esc_html__( 'Read more', 'tribe-events-calendar-pro' ) . '</a>' ); ?></p>
</div>

<div id="tribe-additional-fields" class="tribe-additional-fields">

	<table id="additional-field-table" class="wp-list-table widefat <?php echo ( count ( $custom_fields ) > 1 ) ? 'has-fields' : 'no-fields'; ?>">
		<tbody>
		<?php
		foreach ( $custom_fields as $field ) : // Track our progress through the list of custom fields
			// Reuse the existing index (and maintain an underscore prefix - to differentiate
			// between existing fields and newly created ones (so we can maintain the relationship
			// between keys and values)
			if ( isset( $field['name'] ) && 0 === strpos( $field['name'], '_ecp_custom' ) ) {
				$index = esc_attr( substr( $field['name'], 11 ) );
			} // In all other cases, we'll leave things open for a new index to be applied
			else {
				$index = '';
			}
			?>
			<tr>
				<td>
					<div class="tribe-field-heading">
						<h3>
							<span class="tribe-field-type">
								<?php echo isset( $field['type'] ) ? esc_html( stripslashes( $field['type'] ) ) : esc_html_e( 'Add field', 'tribe-events-calendar-pro' ) ?>
							</span>
							<span class="tribe-field-label">
								<?php echo isset( $field['label'] ) ? '&mdash; ' . esc_html( stripslashes( $field['label'] ) ) : '' ?>
							</span>
							<span class="tribe-toggle">
								<i class="dashicons dashicons-arrow-up-alt2"></i>
							</span>
						</h3>
					</div>

					<div class="tribe-field-content">
						<?php
						/**
						 * Allow for additional rendering of elements inside at the end of the custom
						 * fields markup, before any other custom field markup
						 *
						 * @param array $field Field rendered on this action
						 * @param int $index Index of the field usually used to identify the field name
						 * @param int $count Total count of the current field useful to identify copies amount of same field
						 * @param array $custom_fields An array with all the custom fields
						 *
						 * @since 4.4.34
						 */
						do_action( 'tribe_events_pro_before_custom_field_content', $field, $index, $count, $custom_fields );
						?>
						<div class="tribe-field-row tribe-field-type">
							<label><?php esc_html_e( 'Field Type', 'tribe-events-calendar-pro' ); ?></label>
							<select
								class="tribe-dropdown tribe-custom-field-type"
								name="custom-field-type[<?php echo esc_attr( $index ); ?>]"
								data-name-template="custom-field-type"
								data-count='<?php echo esc_attr( $count ); ?>'
								data-prevent-clear
							>
								<option value="text" <?php selected( isset( $field['type'] ) && $field['type'] == 'text' ) ?>><?php esc_html_e( 'Text', 'tribe-events-calendar-pro' ) ?></option>
								<option value="textarea" <?php selected( isset( $field['type'] ) && $field['type'] == 'textarea' ) ?>><?php esc_html_e( 'Text Area', 'tribe-events-calendar-pro' ) ?></option>
								<option value="url" <?php selected( isset( $field['type'] ) && $field['type'] == 'url' ) ?>><?php esc_html_e( 'URL', 'tribe-events-calendar-pro' ) ?></option>
								<option value="radio" <?php selected( isset( $field['type'] ) && $field['type'] == 'radio' ) ?>><?php esc_html_e( 'Radio', 'tribe-events-calendar-pro' ) ?></option>
								<option value="checkbox" <?php selected( isset( $field['type'] ) && $field['type'] == 'checkbox' ) ?>><?php esc_html_e( 'Checkbox', 'tribe-events-calendar-pro' ) ?></option>
								<option value="dropdown" <?php selected( isset( $field['type'] ) && $field['type'] == 'dropdown' ) ?>><?php esc_html_e( 'Dropdown', 'tribe-events-calendar-pro' ) ?></option>
							</select>
						</div>

						<div class="tribe-field-row tribe-field-label">
							<label><?php esc_html_e( 'Field Label', 'tribe-events-calendar-pro' ); ?></label>
							<input
								type="text"
								name="custom-field[<?php echo esc_attr( $index ); ?>]"
								data-persisted="<?php echo $count != count( $field ) ? 'yes' : 'no' ?>"
								data-name-template="custom-field"
								data-count="<?php echo esc_attr( $count ) ?>"
								value="<?php echo isset( $field['label'] ) ? esc_attr( stripslashes( $field['label'] ) ) : '' ?>"
								placeholder="<?php esc_attr_e( 'Enter field label', 'tribe-events-calendar-pro' ) ?>"
							/>
						</div>

						<div class="tribe-field-row tribe-field-options" style='display: <?php echo ( isset( $field['type'] ) && ( $field['type'] == 'radio' || $field['type'] == 'checkbox' || $field['type'] == 'dropdown' ) ) ? 'block' : 'none' ?>;'>
							<label><?php esc_html_e( 'Options (one per line)', 'tribe-events-calendar-pro' ); ?></label>
							<textarea
								name="custom-field-options[<?php echo esc_attr( $index ); ?>]"
								data-name-template="custom-field-options"
								placeholder="<?php esc_attr_e( 'One per line', 'tribe-events-calendar-pro' ); ?>"
								data-count="<?php echo esc_attr( $count ); ?>" rows="3"><?php echo stripslashes( esc_textarea( isset( $field['values'] ) ? $field['values'] : '' ) ) ?></textarea>
						</div>
						<span class="add-remove-actions"></span>
						<?php
						/**
						 * Allow for additional rendering of elements inside at the end of the custom
						 * fields markup.
						 *
						 * @param array $field Field rendered on this action
						 * @param int $index Index of the field usually used to identify the field name
						 * @param int $count Total count of the current field useful to identify copies amount of same field
						 * @param array $custom_fields An array with all the custom fields
						 *
						 * @since 4.4.34
						 */
						do_action( 'tribe_events_pro_after_custom_field_content', $field, $index, $count, $custom_fields );
						?>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
		<tfoot>
			<tr>
				<td>
					<a name="add-field" class="add-another-field tribe-add-post button">
						<i class="dashicons dashicons-plus"></i> <?php echo esc_html( $add_another ) ?>
					</a>
				</td>
			</tr>
		</tfoot>
		</tbody>
	</table>
</div>

<script type="text/javascript">

	jQuery( function ( $ ) {
		var fields_tbl  = $( '#additional-field-table' );
		var tbl_body    = fields_tbl.find( 'tbody' );
		var add_new_tpl = '';
		var remove_tpl  = "<a name='remove-field' title='<?php echo esc_js( $remove_field ) ?>' class='remove-field dashicons dashicons-trash tribe-delete-this'><span class='screen-reader-text'><?php echo esc_js( $remove_field ) ?></span></a>";

		var textFieldLabel      = '<?php echo esc_js( __( 'Text', 'tribe-events-calendar-pro' ) ) ?>';
		var textareaFieldLabel  = '<?php echo esc_js( __( 'Text Area', 'tribe-events-calendar-pro' ) ) ?>';
		var urlFieldLabel       = '<?php echo esc_js( __( 'URL', 'tribe-events-calendar-pro' ) ) ?>';
		var dropdownFieldLabel  = '<?php echo esc_js( __( 'Dropdown', 'tribe-events-calendar-pro' ) ) ?>';
		var radioFieldLabel     = '<?php echo esc_js( __( 'Radio', 'tribe-events-calendar-pro' ) ) ?>';
		var checkboxFieldLabel  = '<?php echo esc_js( __( 'Checkbox', 'tribe-events-calendar-pro' ) ) ?>';

		/**
		 * Ensures the correct action link is present for each row in the table.
		 */
		function refresh_add_remove_links() {
			var rows     = tbl_body.find( 'tr' );
			var num_rows = rows.length;
			var count    = 0;

			// Insert the remove link for every row but the final one (which should contain the add new link)
			$.each( rows, function( index, object ) {
				if ( ++count == num_rows ) $( object ).find( '.add-remove-actions' ).html( add_new_tpl );
				else $( object ).find( '.add-remove-actions' ).html( remove_tpl );
			} );
		}

		/**
		 * Add toggling for each field
		 */
		$( '#tribe-additional-fields' ).on( 'click', '.tribe-field-heading', function () {
			$( this ).toggleClass( 'closed' );
			$( this ).next().slideToggle();
		} );

		// Set up the add/remove links as soon as the page is ready
		refresh_add_remove_links();

		if ( fields_tbl.length > 0 ) {

			fields_tbl.on( 'click', '.remove-field', function () {
				var row = $( this ).closest( 'tr' ), firstInput = row.find( 'td:first input' ), data = {
					action: 'remove_option',
					field : firstInput.data( 'count' )
				}, persisted = firstInput.data( 'persisted' )
				if ( confirm( '<?php echo esc_js( __( 'Are you sure you wish to remove this field and its data from all events?', 'tribe-events-calendar-pro' ) ); ?>' ) ) {
					if ( 'yes' === persisted ) {
						jQuery.post( ajaxurl, data, function ( response ) {
							row.fadeOut( 'slow', function () {
								$( this ).remove();
							});
						});
					} else {
						row.fadeOut( 'slow', function () {
							$( this ).remove();
						});
					}
				}
			});

			$( '#tribe-additional-fields' ).on( 'click', '.add-another-field', function () {
				var lastRow = tbl_body.find( 'tr:last' ),
					newRow = lastRow.clone();

				newRow.find( 'input, select, textarea' ).each( function () {
					var $input = $( this ),
						number = parseInt( $input.data( 'count' ), 10 ) + 1;

					$input
						.attr( 'name', $input.data( 'name-template' ) + '[]' )
						.val( '' )
						.attr( 'data-count', number );
				} );

				newRow.find( '.tribe-field-heading .tribe-field-type' ).html( textFieldLabel );
				newRow.find( '.tribe-field-heading .tribe-field-label' ).html( '' );
				newRow.find( '.tribe-field-options' ).hide();

				newRow.find( '.tribe-custom-field-type' ).val( 'text' );

				newRow
					.find( '.tribe-dropdown' ).show()
					.filter( '.select2-container' ).remove()
					.end().tribe_dropdowns();

				tbl_body.append( newRow );
				refresh_add_remove_links()
			} );

			/**
			 * Update the field label on the heading while they change the text
			 */
			$( '#additional-field-table' ).on( 'keyup', 'input', function () {

				$( this ).closest( 'tr' ).find( '.tribe-field-heading .tribe-field-label' ).html( ' &mdash; ' + $( this ).val() );

			});

			/**
			 * Hide/show the textarea options for radio, dropdown and checkboxes
			 * Also, update the field label according to the type of field the user selected
			 */
			$( '#additional-field-table' ).on( 'change', 'select', function () {
				var fieldType  = $( this ).find( 'option:selected' ).val();
				var fieldLabel = eval( fieldType + 'FieldLabel' );

				$( this ).closest( 'tr' ).find( '.tribe-field-heading .tribe-field-type' ).html( fieldLabel );

				if (
					'radio' == fieldType
					|| 'dropdown' == fieldType
					|| 'checkbox' == fieldType
				) {
					$( this ).closest( 'tr' ).find( '.tribe-field-options' ).show();
				} else {
					$( this ).closest( 'tr' ).find( '.tribe-field-options' ).hide();
				}
			});
		}
	});
</script>
