<script>
	jQuery( function ( $ ) {
		var checkboxes = $( '#all_filters' ).find( 'li > label > input:checkbox' );
		checkboxes.not( ':checked' ).each( function () {
			var slug = $( this ).attr( 'id' );
			$( '#tribe_events_active_filter_' + slug ).hide();
		} );
		$( checkboxes ).change( function () {
			var slug = $( this ).attr( 'id' );
			var filter_settings = $( '#tribe_events_active_filter_' + slug );
			if ( this.checked ) {
				filter_settings.show();
			} else {
				filter_settings.hide();
			}
		} );
		$( "#active_filters" ).sortable( {
			cursor: 'move',
			handle: '.tribe-arrangeable-item-top',
			update: function () {
				var index = 1;
				$( '#active_filters' ).find( 'input.tribe-filter-priority' ).each( function () {
					$( this ).val( index );
					index++;
				} );
			}
		} );
		$( "body" ).on( 'click', '.tribe-arrangeable-action', function () {
			$( this ).toggleClass( 'open' );
			$( this ).parents( "li" ).children( ".tribe-arrangeable-child" ).slideToggle( "fast" );
			return false;
		} );
	} );
</script>
