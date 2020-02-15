jQuery( document ).ready( function($) {
	$( '.generatepress-control-toggles' ).each( function() {
		$( this ).find( 'button' ).first().addClass( 'active' );
	} );

	$( document ).on( 'click', '.generatepress-control-toggles button', function( e ) {
		e.preventDefault();
		var button = $( this ),
			target = button.data( 'target' ),
			other_targets = button.siblings();

		button.addClass( 'active' );
		button.siblings().removeClass( 'active' );

		$( 'li[data-control-section="' + target + '"]' ).css( {
			visibility: 'visible',
			height: '',
			width: '',
			margin: ''
		} );

		$.each( other_targets, function( index, value ) {
			var other_target = $( this ).data( 'target' );
			$( 'li[data-control-section="' + other_target + '"]' ).css( {
				visibility: 'hidden',
				height: '0',
				width: '0',
				margin: '0'
			} );
		} );
	} );
});
