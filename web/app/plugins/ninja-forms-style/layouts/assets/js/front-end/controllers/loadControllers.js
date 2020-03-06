define( 
	[
		'controllers/formContentFilters',
	], 
	function
	(
		FormContentFilters
	)
	{
	var controller = Marionette.Object.extend( {
		initialize: function() {
			new FormContentFilters();
		}

	});

	return controller;
} );