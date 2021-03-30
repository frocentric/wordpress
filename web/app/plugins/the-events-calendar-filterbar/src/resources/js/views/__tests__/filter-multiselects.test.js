describe( 'Filter Multiselects', () => {
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
		require( '../filter-multiselects' );
		global.tribe.filterBar.filters = {};
		global.tribe_dropdowns = {};
	} );

	afterAll( () => {
		delete String.prototype.className;
		delete global.tribe;
		delete global.tribe_dropdowns;
	} );

	describe( 'Selectors', () => {
		test( 'Should match snapshot', () => {
			const selectors = JSON.stringify( tribe.filterBar.filterMultiselects.selectors );
			expect( selectors ).toMatchSnapshot();
		} );
	} );

	describe( 'Handle multiselect change', () => {
		beforeEach( () => {
			global.tribe.events = {
				views: {
					manager: {
						currentAjaxRequest: null,
					},
				},
			};
			global.tribe.filterBar.filters = {
				addKeyValueToQuery: jest.fn().mockImplementation( () => ( {} ) ),
				removeKeyValueFromQuery: jest.fn().mockImplementation( () => ( {} ) ),
				submitRequest: jest.fn(),
			};
		} );

		test( 'Should return early if name attribute is empty', () => {
			// Setup test.
			const event = {
				data: {
					target: $( '<div value="42"></div>' ),
					container: $( '<div></div>' ),
				},
			};

			// Test.
			tribe.filterBar.filterMultiselects.handleMultiselectChange( event );

			// Confirm final states.
			expect( tribe.filterBar.filters.addKeyValueToQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.removeKeyValueFromQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.submitRequest.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should return early if an ajax request is already happening', () => {
			// Setup test.
			global.tribe.events = {
				views: {
					manager: {
						currentAjaxRequest: {},
					},
				},
			};
			const event = {
				data: {
					target: $( '<div name="key" value="42"></div>' ),
					container: $( '<div></div>' ),
				},
			};

			// Test.
			tribe.filterBar.filterMultiselects.handleMultiselectChange( event );

			// Confirm final states.
			expect( tribe.filterBar.filters.addKeyValueToQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.removeKeyValueFromQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.submitRequest.mock.calls.length ).toBe( 0 );

			// Cleanup test.
			delete global.tribe.events.views.manager;
		} );

		test( 'Should remove key value from query', () => {
			// Setup test.
			const event = {
				data: {
					target: $( '<div name="key"></div>' ),
					container: $( '<div></div>' ),
				},
			};

			// Test.
			tribe.filterBar.filterMultiselects.handleMultiselectChange( event );

			// Confirm final states.
			expect( tribe.filterBar.filters.addKeyValueToQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.removeKeyValueFromQuery.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filters.submitRequest.mock.calls.length ).toBe( 1 );
		} );

		test( 'Should replace key value in query with selected option', () => {
			// Setup test.
			const event = {
				data: {
					target: $( '<div name="key" value="42"></div>' ),
					container: $( '<div></div>' ),
				},
			};

			// Test.
			tribe.filterBar.filterMultiselects.handleMultiselectChange( event );

			// Confirm final states.
			expect( tribe.filterBar.filters.addKeyValueToQuery.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filters.removeKeyValueFromQuery.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filters.submitRequest.mock.calls.length ).toBe( 1 );
		} );

		test( 'Should replace key value in query with selected options', () => {
			// Setup test.
			const event = {
				data: {
					target: $( '<div name="key" value="42,24"></div>' ),
					container: $( '<div></div>' ),
				},
			};

			// Test.
			tribe.filterBar.filterMultiselects.handleMultiselectChange( event );

			// Confirm final states.
			expect( tribe.filterBar.filters.addKeyValueToQuery.mock.calls.length ).toBe( 2 );
			expect( tribe.filterBar.filters.removeKeyValueFromQuery.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filters.submitRequest.mock.calls.length ).toBe( 1 );
		} );
	} );

	describe( 'Handle template selection', () => {
		test( 'Should return selected option wrapped in span element', () => {
			// Setup test.
			const state = {
				text: 'Hello world',
			};

			// Test.
			const $template = tribe.filterBar.filterMultiselects.handleTemplateSelection( state );

			// Confirm final states.
			expect( $template ).toMatchSnapshot();
		} );
	} );

	describe( 'Initialize multiselect', () => {
		test( 'Should initialize multiselect', () => {
			// Setup test.
			global.tribe_dropdowns = {
				dropdown: jest.fn(),
			};
			const $multiselectInput = $( '<input />' );
			const $container = $( '<div></div>' );
			$multiselectInput.on = jest.fn().mockImplementation( () => $multiselectInput );
			const addClass = jest.fn();
			const trigger = jest.fn();
			$multiselectInput.data = () => ( {
				$container: {
					addClass: addClass,
				},
				trigger: trigger,
			} );

			// Test.
			tribe.filterBar.filterMultiselects.initMultiselect( $multiselectInput, $container );

			// Confirm final states.
			expect( global.tribe_dropdowns.dropdown.mock.calls.length ).toBe( 1 );
			expect( $multiselectInput.on.mock.calls.length ).toBe( 1 );
			expect( addClass.mock.calls.length ).toBe( 1 );
			expect( trigger.mock.calls.length ).toBe( 1 );
			expect( $multiselectInput.on.mock.calls[ 0 ][ 0 ] ).toBe( 'change' );
		} );
	} );
} );
