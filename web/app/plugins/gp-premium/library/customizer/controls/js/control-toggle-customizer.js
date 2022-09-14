jQuery( function( $ ) {
	$( '.generatepress-control-toggles' ).each( function() {
		$( this ).find( 'button' ).first().addClass( 'active' );
	} );

	$( document ).on( 'click', '.generatepress-control-toggles button', function( e ) {
		e.preventDefault();
		var button = $( this ),
			target = button.data( 'target' ),
			otherTargets = button.siblings();

		button.addClass( 'active' );
		button.siblings().removeClass( 'active' );

		$( 'li[data-control-section="' + target + '"]' ).css( {
			visibility: 'visible',
			height: '',
			width: '',
			margin: '',
			overflow: '',
		} );

		$.each( otherTargets, function() {
			var otherTarget = $( this ).data( 'target' );

			$( 'li[data-control-section="' + otherTarget + '"]' ).css( {
				visibility: 'hidden',
				height: '0',
				width: '0',
				margin: '0',
				overflow: 'hidden',
			} );
		} );
	} );
} );
