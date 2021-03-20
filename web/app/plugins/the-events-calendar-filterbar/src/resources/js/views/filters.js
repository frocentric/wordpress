/* global tribe */
/* eslint-disable no-var, strict */

/**
 * Makes sure we have all the required levels on the Tribe Object.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar = tribe.filterBar || {};

/**
 * Configures Filters Object in the Global Tribe variable.
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.filterBar.filters = {};

/**
 * Initializes in a Strict env the code that manages the filters.
 *
 * @since 5.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.filterBar.filters
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';
	var $document = $( document );

	/**
	 * Selectors used for configuration and setup.
	 *
	 * @since 5.0.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		filter: '.tribe-filter-bar-c-filter',
		filterOpen: '.tribe-filter-bar-c-filter--open',
		filterBar: '[data-js~="tribe-filter-bar"]',
		filterBarOpen: '.tribe-filter-bar--open',
		filterBarVertical: '.tribe-events--filter-bar-vertical',
	};

	/**
	 * Remove one set of square brackets and numbers inside from string from the end.
	 *
	 * @since  5.0.0
	 *
	 * @param  {string} string String to remove square brackets from.
	 *
	 * @return {string}
	 */
	obj.removeSquareBracketsFromEnd = function( string ) {
		// Replace any square brackets and contents inside square brackets with empty string.
		return string.replace( /(\[[0-9]*\])$/, '' );
	};

	/**
	 * Checks whether a string has square brackets at the end or not.
	 *
	 * @since  5.0.0
	 *
	 * @param  {string} string String to check for square brackets.
	 *
	 * @return {boolean}
	 */
	obj.hasSquareBracketsAtEnd = function( string ) {
		return string.search( /\[[0-9]*\]$/ ) !== -1;
	};

	/**
	 * Removes value from provided query string pieces.
	 *
	 * @since  5.0.0
	 *
	 * @param  {array}  baseKeyQueryStringPieces Array of query string pieces in the form of 'key[index]=value'.
	 * @param  {string} value                    Query value to be removed.
	 *
	 * @return {array}
	 */
	obj.removeValueFromBaseKeyQueryStringPieces = function( baseKeyQueryStringPieces, value ) {
		return baseKeyQueryStringPieces
			// Filter out value from query string pieces.
			.filter( function( queryStringPiece ) {
				var queryVal = queryStringPiece.split( '=' )[ 1 ];
				return queryVal !== value;
			} )
			// Rebuild array numbering for query key.
			.map( function( queryStringPiece, index ) {
				var [ queryKey, queryVal ] = queryStringPiece.split( '=' );
				var baseQueryKey = obj.removeSquareBracketsFromEnd( decodeURIComponent( queryKey ) );
				var newQueryKey = encodeURIComponent( baseQueryKey + '[' + index + ']' );
				return [ newQueryKey, queryVal ].join( '=' );
			} );
	};

	/**
	 * Remove key value pair from provided query string pieces.
	 * If passed value is `true`, all instances of `key` will be removed.
	 *
	 * @since  5.0.0
	 *
	 * @param  {array}  queryStringPieces Array of query string pieces in the form of 'key=value'.
	 * @param  {string} key               Query key to be removed.
	 * @param  {string} value             Query value to be removed.
	 *
	 * @return {array}
	 */
	obj.removeKeyValueFromQueryStringPieces = function( queryStringPieces, key, value ) {
		// Get base key, without square brackets.
		var baseKey = key;
		var keyIsArray = obj.hasSquareBracketsAtEnd( key );
		if ( keyIsArray ) {
			baseKey = obj.removeSquareBracketsFromEnd( baseKey );
		}

		// Filter out key and value from query string.
		var modifiedQueryStringPieces = [];
		var baseKeyQueryStringPieces = [];
		for ( var i = 0; i < queryStringPieces.length; i++ ) {
			var [ queryKey, queryVal ] = queryStringPieces[ i ].split( '=' );
			var baseQueryKey;

			try {
				baseQueryKey = obj.removeSquareBracketsFromEnd( decodeURIComponent( queryKey ) );
			} catch ( error ) {
				// Skip if queryKey cannot be decoded properly.
				continue;
			}

			// If baseQueryKey is the baseKey and value is true, remove.
			if ( baseQueryKey === baseKey && value === true ) {
				continue;
			}

			// Keep if baseQueryKey is not the baseKey we want, or baseQueryKey is the baseKey but queryVal is not the value.
			if (
				baseQueryKey !== baseKey ||
					( ! keyIsArray && queryVal !== value )
			) {
				modifiedQueryStringPieces.push( queryStringPieces[ i ] );

			// Hold query string piece if key is array and value is not true.
			} else if ( keyIsArray && value !== true ) {
				baseKeyQueryStringPieces.push( queryStringPieces[ i ] );
			}
		}

		// If we're dealing with an array key, do below.
		if ( keyIsArray ) {
			// Filter out query string piece with value.
			baseKeyQueryStringPieces = obj.removeValueFromBaseKeyQueryStringPieces( baseKeyQueryStringPieces, value );
		}

		// Add back base key query string pieces to modified query string pieces.
		baseKeyQueryStringPieces.forEach( function( queryStringPiece ) {
			modifiedQueryStringPieces.push( queryStringPiece );
		} );

		return modifiedQueryStringPieces;
	};

	/**
	 * Remove key value pair from current url query string.
	 * If passed value is `true`, all instances of `key` will be removed.
	 *
	 * @since  5.0.0
	 *
	 * @param  {PlainObject} location Location object containing url data.
	 * @param  {string}      key      Query key to be removed.
	 * @param  {string}      value    Query value to be removed.
	 *
	 * @return {PlainObject}
	 */
	obj.removeKeyValueFromQuery = function( location, key, value ) {
		// If no query string, nothing to remove, return original url.
		if ( ! location.search ) {
			return location;
		}

		// Split query string into queries.
		var queryStringPieces = location.search.slice( 1 ).split( '&' );

		// Remove key value from query string pieces.
		var modifiedQueryStringPieces = obj.removeKeyValueFromQueryStringPieces( queryStringPieces, key, value );

		// Build back query string if query string pieces exist.
		var modifiedQueryString = '';
		if ( modifiedQueryStringPieces.length ) {
			modifiedQueryString = '?' + modifiedQueryStringPieces.join( '&' );
		}

		return {
			origin: location.origin,
			pathname: location.pathname,
			search: modifiedQueryString,
			hash: location.hash,
			href: [ location.origin, location.pathname, modifiedQueryString, location.hash ].join( '' ),
		};
	};

	/**
	 * Get query to add to query string from key and value provided.
	 *
	 * @since  5.0.0
	 *
	 * @param  {array}  queryStringPieces Array of query string pieces in the form of 'key=value'.
	 * @param  {string} key               Query key to be added.
	 * @param  {string} value             Query value to be added.
	 *
	 * @return {array}
	 */
	obj.getQueryToAdd = function( queryStringPieces, key, value ) {
		// Get base key, without square brackets.
		var baseKey = key;
		var keyIsArray = obj.hasSquareBracketsAtEnd( key );

		if ( keyIsArray ) {
			baseKey = obj.removeSquareBracketsFromEnd( baseKey );
		}

		var keyArrayIndex = 0;

		// Loop through query string pieces.
		for ( var i = 0; i < queryStringPieces.length; i++ ) {
			var [ queryKey, queryVal ] = queryStringPieces[ i ].split( '=' );
			var baseQueryKey;

			try {
				baseQueryKey = obj.removeSquareBracketsFromEnd( decodeURIComponent( queryKey ) );
			} catch ( error ) {
				// Skip if queryKey cannot be decoded properly.
				continue;
			}

			// If key value pair already exists, return blank string.
			if ( ( baseQueryKey === baseKey ) && ( queryVal === value ) ) {
				return '';
			}

			// Increase key array index if base key exists.
			if ( keyIsArray && ( baseQueryKey === baseKey ) ) {
				keyArrayIndex++;
			}
		}

		// Add key value pair to query string.
		var keyToAdd = baseKey;
		if ( keyIsArray ) {
			keyToAdd += '[' + keyArrayIndex + ']';
		}

		return [ encodeURIComponent( keyToAdd ), value ].join( '=' );
	};

	/**
	 * Add key value pair to current url query string.
	 *
	 * @since  5.0.0
	 *
	 * @param  {PlainObject} location Location object containing url data.
	 * @param  {string}      key      Query key.
	 * @param  {string}      value    Query value.
	 *
	 * @return {PlainObject}
	 */
	obj.addKeyValueToQuery = function( location, key, value ) {
		// Split query string into queries.
		var queryStringPieces = [];
		var queryString = location.search;
		if ( queryString ) {
			queryStringPieces = queryString.slice( 1 ).split( '&' );
		}

		// Get query to add to query string.
		var queryToAdd = obj.getQueryToAdd( queryStringPieces, key, value );

		// Build back query string, adding new query if it exists (it could be an empty string).
		var modifiedQueryString = queryString;
		if ( queryToAdd ) {
			modifiedQueryString = queryString ? [ queryString, queryToAdd ].join( '&' ) : '?' + queryToAdd;
		}

		return {
			origin: location.origin,
			pathname: location.pathname,
			search: modifiedQueryString,
			hash: location.hash,
			href: [ location.origin, location.pathname, modifiedQueryString, location.hash ].join( '' ),
		};
	};

	/**
	 * Get filters state by converting binary to base 10.
	 * Open state is 1, closed state is 0.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {boolean|number}
	 */
	obj.getFiltersState = function( $container ) {
		var containerState = $container.data( 'tribeEventsState' );

		// Return early if state is not mobile and not vertical.
		if ( ! containerState.isMobile && ! $container.is( obj.selectors.filterBarVertical ) ) {
			return false;
		}

		var $filters = $container.find( obj.selectors.filter );

		// Return early if no filters are found.
		if ( ! $filters.length ) {
			return false;
		}

		// Calculate filter state by converting binary to base 10.
		var filtersState = $filters
			.toArray()
			.reduce( function( state, filter, index ) {
				// Return original state if filter is not open.
				if ( ! $( filter ).is( obj.selectors.filterOpen ) ) {
					return state;
				}

				return state + Math.pow( 2, index );
			}, 0 );

		return filtersState;
	};

	/**
	 * Set flag for filter bar request.
	 *
	 * @since  5.0.2
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.setTribeFilterBarRequest = function( $container ) {
		var requestData = $container.data( 'tribeRequestData' );
		var data = {
			tribe_filter_bar_request: 1,
		};

		if ( $.isPlainObject( requestData ) ) {
			data = $.extend( requestData, data );
		}

		$container.data( 'tribeRequestData', data );
	};

	/**
	 * Submit request using manager JS.
	 *
	 * @since  5.0.0
	 *
	 * @param  {jQuery} $container jQuery object of view container.
	 * @param  {string} url        The url to submit the request to.
	 *
	 * @return {void}
	 */
	obj.submitRequest = function( $container, url ) {
		$container.trigger( 'beforeFilterBarSubmitRequest.tribeEvents' );

		var currentUrl = window.location.href;
		var nonce = $container.data( 'view-rest-nonce' );
		var shouldManageUrl = tribe.events.views.manager.shouldManageUrl( $container );

		var data = {
			prev_url: encodeURI( decodeURI( currentUrl ) ),
			url: encodeURI( decodeURI( url ) ),
			should_manage_url: shouldManageUrl,
			_wpnonce: nonce,
		};

		obj.setTribeFilterBarRequest( $container );

		tribe.events.views.manager.request( data, $container );

		$container.trigger( 'afterFilterBarSubmitRequest.tribeEvents' );
	};

	/**
	 * Add filter bar data to container data.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object for 'beforeRequest.tribeEvents' event.
	 *
	 * @return {void}
	 */
	obj.addFilterBarData = function( event ) {
		var $container = event.data.container;
		var containerState = $container.data( 'tribeEventsState' );
		var $filterBar = $container.find( obj.selectors.filterBar );
		var filtersState = obj.getFiltersState( $container );
		var requestData = $container.data( 'tribeRequestData' );
		var data = {};

		// Filter bar is open if making a filter bar request on mobile with filter bar open or on desktop and filter bar is open.
		var isFilterBarOpen = $filterBar.is( obj.selectors.filterBarOpen ) && (
			(
				containerState.isMobile &&
					$.isPlainObject( requestData ) &&
					requestData.tribe_filter_bar_request
			) ||
			! containerState.isMobile
		);

		data.tribe_filter_bar_state = isFilterBarOpen ? 1 : 0;

		// Add filter state to data if filterState is not false.
		if ( false !== filtersState ) {
			data.tribe_filters_state = filtersState;
		}

		if ( $.isPlainObject( requestData ) ) {
			data = $.extend( requestData, data );
		}

		$container.data( 'tribeRequestData', data );
	};

	/**
	 * Deinitialize filters JS.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event} event event object for 'beforeAjaxSuccess.tribeEvents' event.
	 *
	 * @return {void}
	 */
	obj.deinit = function( event ) {
		var $container = event.data.container;
		$container.off( 'beforeRequest.tribeEvents', obj.addFilterBarData );
		$container.off( 'beforeAjaxSuccess.tribeEvents', obj.deinit );
	};

	/**
	 * Initialize filters JS.
	 *
	 * @since  5.0.0
	 *
	 * @param  {Event}   event      event object for 'afterSetup.tribeEvents' event.
	 * @param  {integer} index      jQuery.each index param from 'afterSetup.tribeEvents' event.
	 * @param  {jQuery}  $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.init = function( event, index, $container ) {
		$container.on( 'beforeRequest.tribeEvents', { container: $container }, obj.addFilterBarData );
		$container.on( 'beforeAjaxSuccess.tribeEvents', { container: $container }, obj.deinit );
	};

	/**
	 * Handles the initialization of filters when Document is ready.
	 *
	 * @since 5.0.0
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$document.on( 'afterSetup.tribeEvents', tribe.events.views.manager.selectors.container, obj.init );
	};

	// Configure on document ready.
	$( obj.ready );
} )( jQuery, tribe.filterBar.filters );
