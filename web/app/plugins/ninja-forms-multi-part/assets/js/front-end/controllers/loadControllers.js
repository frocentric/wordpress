define( 
	[
		'controllers/conditionalLogic',
		'controllers/renderRecaptcha',
		'controllers/renderHelpText'
	],
	function
	(
		ConditionalLogic,
		RenderRecaptcha,
		RenderHelpText
	)
	{
	var controller = Marionette.Object.extend( {
		initialize: function() {
			new ConditionalLogic();
			new RenderRecaptcha();
			new RenderHelpText();
		}

	});

	return controller;
} );