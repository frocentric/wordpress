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
		require( '../filter-checkboxes' );
		global.tribe.filterBar.filters = {};
	} );

	afterAll( () => {
		delete String.prototype.className;
		delete global.tribe;
	} );

	describe( 'Selectors', () => {
		test( 'Should match snapshot', () => {
			const selectors = JSON.stringify( tribe.filterBar.filterCheckboxes.selectors );
			expect( selectors ).toMatchSnapshot();
		} );
	} );

	describe( 'Handle checkbox change', () => {
		let event;

		beforeEach( () => {
			event = {
				data: {
					container: $(),
				},
				target: {
					name: 'filter_name',
					value: '1',
				},
			};
			global.tribe.filterBar.filters = {
				addKeyValueToQuery: jest.fn().mockImplementation( () => ( {} ) ),
				removeKeyValueFromQuery: jest.fn().mockImplementation( () => ( {} ) ),
				submitRequest: jest.fn(),
			};
		} );

		test( 'Should add key value to query if checked', () => {
			// Setup test.
			event.target.checked = true;

			// Test.
			tribe.filterBar.filterCheckboxes.handleCheckboxChange( event );

			// Confirm final state.
			expect( tribe.filterBar.filters.addKeyValueToQuery.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filters.removeKeyValueFromQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.submitRequest.mock.calls.length ).toBe( 1 );
		} );

		test( 'Should remove key value from query if not unchecked', () => {
			// Setup test.
			event.target.checked = false;

			// Test.
			tribe.filterBar.filterCheckboxes.handleCheckboxChange( event );

			// Confirm final state.
			expect( tribe.filterBar.filters.addKeyValueToQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.removeKeyValueFromQuery.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filters.submitRequest.mock.calls.length ).toBe( 1 );
		} );
	} );
} );
