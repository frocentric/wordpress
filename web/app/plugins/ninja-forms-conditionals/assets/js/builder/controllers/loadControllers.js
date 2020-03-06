/**
 * Loads all of our custom controllers.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [
	'controllers/templateHelpers',
	'controllers/returnChildView',
	'models/conditionCollection',
	'views/drawerHeader',
	'controllers/newCondition',
	'controllers/updateSettings',
	'controllers/clickControls',
	'controllers/undo',
	// 'controllers/maybeModifyElse',
	'controllers/coreValues',
	'controllers/coreComparators',
	'controllers/coreTriggers',
	'controllers/getDrawerHeader',
	'controllers/trackKeyChanges',
	'controllers/maybeConvertConditions',
	'controllers/filters'

	], function(

	TemplateHelpers,
	ReturnChildView,
	ConditionCollection,
	DrawerHeaderView,
	NewCondition,
	UpdateSettings,
	ClickControls,
	Undo,
	// MaybeModifyElse,
	CoreValues,
	CoreComparators,
	CoreTriggers,
	GetDrawerHeader,
	TrackKeyChanges,
	MaybeConvertConditions,
	Filters
	) {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			new TemplateHelpers();
			new ReturnChildView();
			new NewCondition();
			new UpdateSettings();
			new ClickControls();
			new Undo();
			// new MaybeModifyElse();
			new CoreValues();
			new CoreComparators();
			new CoreTriggers();
			new GetDrawerHeader();
			new TrackKeyChanges();
			new MaybeConvertConditions();
			new Filters();
		}
	});

	return controller;
} );
