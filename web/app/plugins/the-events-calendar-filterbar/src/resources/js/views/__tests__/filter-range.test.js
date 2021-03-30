describe( 'Filter Checkboxes', () => {
	beforeAll( () => {
		String.prototype.className = function() {
			if (
				(
					'string' !== typeof this &&
					! this instanceof String /* eslint-disable-line no-unsafe-negation */
				) ||
				'function' !== typeof this.replace
			) {
				return this;
			}

			return this.replace( '.', '' );
		};

		global.tribe = {};
		require( '../filter-range' );
		global.tribe.filterBar.filters = {};
		global.tribe_events_filter_bar_js_config = {
			events: {
				currency_symbol: '$',
				reverse_currency_position: false,
			},
			l10n: {
				cost_range_currency_symbol_after: '<%- cost_low %><%- currency_symbol %> - <%- cost_high %><%- currency_symbol %>',
				cost_range_currency_symbol_before: '<%- currency_symbol %><%- cost_low %> - <%- currency_symbol %><%- cost_high %>',
			},
		};
	} );

	afterAll( () => {
		delete String.prototype.className;
		delete global.tribe;
	} );

	describe( 'Selectors', () => {
		test( 'Should match snapshot', () => {
			const selectors = JSON.stringify( tribe.filterBar.filterRange.selectors );
			expect( selectors ).toMatchSnapshot();
		} );
	} );

	describe( 'Handle range slide change', () => {
		let $rangeSlider;
		let $rangeInput;
		let windowHold;

		beforeEach( () => {
			windowHold = global.window;
			delete global.window.location;
			global.window = Object.create( window );
			// url = 'https://test.tri.be/events/month/?range=0-50'
			global.window.location = {
				href: 'https://test.tri.be/events/month/?range=0-50',
				origin: 'https://test.tri.be',
				pathname: '/events/month/',
				search: '?range=0-50',
				hash: '',
			};
			global.tribe.filterBar.filters = {
				addKeyValueToQuery: jest.fn(),
				removeKeyValueFromQuery: jest.fn(),
				submitRequest: jest.fn(),
			};
			$rangeSlider = $( '<div data-min="0" data-max="100"></div>' );
			$rangeInput = $( '<input name="range" />' );
			$rangeSlider.siblings = () => $rangeInput;
		} );

		afterEach( () => {
			global.window = windowHold;
		} );

		test( 'Should return early if no name attribute', () => {
			// Setup test.
			$rangeInput.removeAttr( 'name' );
			const event = {
				data: {
					target: $rangeSlider,
				},
			};
			const ui = {};

			// Test.
			tribe.filterBar.filterRange.handleRangeSlideChange( event, ui );

			// Confirm final states.
			expect( tribe.filterBar.filters.removeKeyValueFromQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.addKeyValueToQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.submitRequest.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should return early if data-min attribute is not a number', () => {
			// Setup test.
			$rangeSlider.data( 'min', 'string' );
			const event = {
				data: {
					target: $rangeSlider,
				},
			};
			const ui = {};

			// Test.
			tribe.filterBar.filterRange.handleRangeSlideChange( event, ui );

			// Confirm final states.
			expect( tribe.filterBar.filters.removeKeyValueFromQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.addKeyValueToQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.submitRequest.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should return early if data-max attribute is not a number', () => {
			// Setup test.
			$rangeSlider.data( 'max', 'string' );
			const event = {
				data: {
					target: $rangeSlider,
				},
			};
			const ui = {};

			// Test.
			tribe.filterBar.filterRange.handleRangeSlideChange( event, ui );

			// Confirm final states.
			expect( tribe.filterBar.filters.removeKeyValueFromQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.addKeyValueToQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.submitRequest.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should remove key and value from query', () => {
			// Setup test.
			global.tribe.filterBar.filters.removeKeyValueFromQuery = jest.fn().mockImplementation( () => ( {
				href: 'https://test.tri.be/events/month/',
				origin: 'https://test.tri.be',
				pathname: '/events/month/',
				search: '',
				hash: '',
			} ) );
			const event = {
				data: {
					target: $rangeSlider,
				},
			};
			const ui = {
				values: [ 0, 100 ],
			};

			// Test.
			tribe.filterBar.filterRange.handleRangeSlideChange( event, ui );

			// Confirm final states.
			expect( tribe.filterBar.filters.removeKeyValueFromQuery.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filters.addKeyValueToQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.submitRequest.mock.calls.length ).toBe( 1 );
		} );

		test( 'Should add key and value to query', () => {
			// Setup test.
			global.tribe.filterBar.filters.addKeyValueToQuery = jest.fn().mockImplementation( () => ( {
				href: 'https://test.tri.be/events/month/?range=0-75',
				origin: 'https://test.tri.be',
				pathname: '/events/month/',
				search: '?range=0-75',
				hash: '',
			} ) );
			const event = {
				data: {
					target: $rangeSlider,
				},
			};
			const ui = {
				values: [ 0, 75 ],
			};

			// Test.
			tribe.filterBar.filterRange.handleRangeSlideChange( event, ui );

			// Confirm final states.
			expect( tribe.filterBar.filters.removeKeyValueFromQuery.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filters.addKeyValueToQuery.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filters.submitRequest.mock.calls.length ).toBe( 1 );
		} );
	} );

	describe( 'Handle range slide', () => {
		let $rangeSlider;
		let $rangeLabel;
		let event;
		let ui;

		beforeEach( () => {
			$rangeSlider = $( '<div data-min="0" data-max="100"></div>' );
			$rangeLabel = $( '<label>$0 - $100</label>' );
			$rangeSlider.siblings = () => $rangeLabel;
			event = {
				data: {
					target: $rangeSlider,
				},
			};
			ui = {
				values: [ 10, 75 ],
			};
		} );

		test( 'Should update range label with currency symbol before value', () => {
			// Test.
			tribe.filterBar.filterRange.handleRangeSlide( event, ui );

			// Confirm final state.
			expect( $rangeLabel.text() ).toMatchSnapshot();
		} );

		test( 'Should update range label with currency symbol before value', () => {
			// Setup test.
			global.tribe_events_filter_bar_js_config.events.reverse_currency_position = true;

			// Test.
			tribe.filterBar.filterRange.handleRangeSlide( event, ui );

			// Confirm final state.
			expect( $rangeLabel.text() ).toMatchSnapshot();

			// Cleanup test.
			global.tribe_events_filter_bar_js_config.events.reverse_currency_position = false;
		} );
	} );
} );
