describe( 'Filter Remove', () => {
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
		require( '../filter-remove' );
	} );

	afterAll( () => {
		delete String.prototype.className;
		delete global.tribe;
	} );

	describe( 'Selectors', () => {
		test( 'Should match snapshot', () => {
			const selectors = JSON.stringify( tribe.filterBar.filterRemove.selectors );
			expect( selectors ).toMatchSnapshot();
		} );
	} );

	describe( 'Handle remove click', () => {
		let windowHold;

		beforeEach( () => {
			windowHold = global.window;
			delete global.window.location;
			global.window = Object.create( window );
			// url = 'https://test.tri.be/events/month/?range=0-50'
			global.window.location = {
				href: 'https://test.tri.be/events/month/?tribe_eventcategory[0]=hello&tribe_eventcategory[1]=world&tribe_cost=0-100&tribe_custom=moderntribe',
				origin: 'https://test.tri.be',
				pathname: '/events/month/',
				search: '?tribe_eventcategory[0]=hello&tribe_eventcategory[1]=world&tribe_cost=0-100&tribe_custom=moderntribe',
				hash: '',
			};
			global.tribe.filterBar.filters = {
				removeKeyValueFromQuery: jest.fn().mockImplementation( () => ( {} ) ),
				submitRequest: jest.fn(),
			};
		} );

		afterEach( () => {
			global.window = windowHold;
		} );

		test( 'Should remove filter', () => {
			// Setup test.
			const $removeButton = $( '<button></button>' );
			const $pill = $( '<div data-filter-name="tribe_eventcategory[]"></div>' );
			$removeButton.closest = jest.fn().mockImplementation( () => $pill );
			const event = {
				data: {
					target: $removeButton,
					container: $(),
				},
			};

			// Test.
			tribe.filterBar.filterRemove.handleRemoveClick( event );

			// Confirm final states.
			expect( tribe.filterBar.filters.removeKeyValueFromQuery.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filters.submitRequest.mock.calls.length ).toBe( 1 );
		} );

		test( 'Should return early if pill does not have data-filter-name attribute', () => {
			// Setup test.
			const $removeButton = $( '<button></button>' );
			const $pill = $( '<div></div>' );
			$removeButton.closest = jest.fn().mockImplementation( () => $pill );
			const event = {
				data: {
					target: $removeButton,
					container: $(),
				},
			};

			// Test.
			tribe.filterBar.filterRemove.handleRemoveClick( event );

			// Confirm final states.
			expect( tribe.filterBar.filters.removeKeyValueFromQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.submitRequest.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should return early if data-filter-name attribute is empty', () => {
			// Setup test.
			const $removeButton = $( '<button></button>' );
			const $pill = $( '<div data-filter-name></div>' );
			$removeButton.closest = jest.fn().mockImplementation( () => $pill );
			const event = {
				data: {
					target: $removeButton,
					container: $(),
				},
			};

			// Test.
			tribe.filterBar.filterRemove.handleRemoveClick( event );

			// Confirm final states.
			expect( tribe.filterBar.filters.removeKeyValueFromQuery.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filters.submitRequest.mock.calls.length ).toBe( 0 );
		} );
	} );
} );
