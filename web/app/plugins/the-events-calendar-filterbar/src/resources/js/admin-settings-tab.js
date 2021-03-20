/**
 * Makes sure we have all the required levels on the Tribe Object.
 *
 * @since 4.9.0
 *
 * @type   {PlainObject}
 */
tribe.events = tribe.events || {};
tribe.events.admin = tribe.events.admin || {};

/**
 * Configures Events Bar Object in the Global Tribe variable.
 *
 * @since 4.9.0
 *
 * @type   {PlainObject}
 */
tribe.events.admin.filterBarSettings = {};

/**
 * Initializes in a Strict env to manage the filterbar settings tab.
 *
 * @since 4.9.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.events.views.filterBar
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';
	var $body = null;

	/**
	 * Selectors used for configuration and setup.
	 *
	 * @since 4.9.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		layoutFieldset: '#tribe-field-events_filters_layout',
		defaultStateFieldset: '#tribe-field-events_filters_default_state',
	};

	/**
	 * Handles the possible toggle for default state based on layout options.
	 *
	 * @since 4.9.0
	 *
	 * @param {Event} event Document Event for the change value.
	 *
	 * @return {void}
	 */
	obj.toggleDefaultStateFieldset = function( event ) {
		var $layoutFieldset = $( obj.selectors.layoutFieldset );
		var $defaultStateFieldset = $( obj.selectors.defaultStateFieldset );
		var $layoutField = $layoutFieldset.find( 'input[name="events_filters_layout"]:checked' );
		var layoutValue = $layoutField.val();

		if ( ! $defaultStateFieldset.length ) {
			return;
		}

		if ( 'vertical' === layoutValue ) {
			$defaultStateFieldset.hide();
		} else {
			$defaultStateFieldset.show();
		}
	};

	/**
	 * Handles the initialization of filter bar when Document is ready.
	 *
	 * @since 4.9.0
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$body = $( 'body' );

		var $layoutFieldset = $( obj.selectors.layoutFieldset );
		var $defaultStateFieldset = $( obj.selectors.defaultStateFieldset );

		if ( ! $layoutFieldset.length ) {
			return;
		}

		if ( ! $defaultStateFieldset.length ) {
			return;
		}

		var $fields = $layoutFieldset.find( 'input[name="events_filters_layout"]' );

		// Attach to the field the change
		$fields.on( 'change', obj.toggleDefaultStateFieldset );

		// Also trigger this, to make sure we have the correct state on load.
		obj.toggleDefaultStateFieldset()
	};

	// Configure on document ready.
	$( obj.ready );
} )( jQuery, tribe.events.admin.filterBarSettings );
