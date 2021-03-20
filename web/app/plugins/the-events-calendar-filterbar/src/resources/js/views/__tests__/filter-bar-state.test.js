describe( 'Filter Bar State', () => {
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
		require( '../filter-bar-state' );
		tribe.events = {
			views: {
				accordion: {},
			},
		};
	} );

	afterAll( () => {
		delete String.prototype.className;
		delete global.tribe;
	} );

	beforeEach( () => {
		global.tribe_events_filter_bar_js_config = {
			l10n: {
				hide_filters: 'Hide filters',
			},
		};
	} );

	afterEach( () => {
		delete global.tribe_events_filter_bar_js_config;
	} );

	describe( 'Selectors', () => {
		test( 'Should match snapshot', () => {
			const selectors = JSON.stringify( tribe.filterBar.filterBarState.selectors );
			expect( selectors ).toMatchSnapshot();
		} );
	} );

	describe( 'Open filter bar', () => {
		test( 'Should open filter bar', () => {
			// Setup test.
			tribe.events.views.accordion.setOpenAccordionA11yAttrs = jest.fn();
			const $container = $();
			const $filterButton = $();
			const $filterButtonText = $();
			const $filterBar = $();
			$container.find = ( selector ) => {
				switch ( selector ) {
					case tribe.filterBar.filterBarState.selectors.filterButton:
						return $filterButton;
					case tribe.filterBar.filterBarState.selectors.filterBar:
						return $filterBar;
					default:
						return $();
				}
			};
			$filterButton.find = () => $filterButtonText;
			$filterButton.addClass = jest.fn();
			$filterButtonText.text = jest.fn();
			$filterBar.addClass = jest.fn();

			// Test.
			tribe.filterBar.filterBarState.openFilterBar( $container );

			// Confirm final states.
			expect( tribe.events.views.accordion.setOpenAccordionA11yAttrs.mock.calls.length ).toBe( 2 );
			expect( $filterButton.addClass.mock.calls.length ).toBe( 1 );
			expect( $filterButtonText.text.mock.calls.length ).toBe( 1 );
			expect( $filterBar.addClass.mock.calls.length ).toBe( 1 );
		} );
	} );

	describe( 'Close filter bar', () => {
		test( 'Should close filter bar', () => {
			tribe.events.views.accordion.setCloseAccordionA11yAttrs = jest.fn();
			const $container = $();
			const $filterButton = $();
			const $filterButtonText = $();
			const $filterBar = $();
			$container.find = ( selector ) => {
				switch ( selector ) {
					case tribe.filterBar.filterBarState.selectors.filterButton:
						return $filterButton;
					case tribe.filterBar.filterBarState.selectors.filterBar:
						return $filterBar;
					default:
						return $();
				}
			};
			$filterButton.find = () => $filterButtonText;
			$filterButton.removeClass = jest.fn();
			$filterButtonText.text = jest.fn();
			$filterBar.removeClass = jest.fn();

			// Test.
			tribe.filterBar.filterBarState.closeFilterBar( $container );

			// Confirm final states.
			expect( tribe.events.views.accordion.setCloseAccordionA11yAttrs.mock.calls.length ).toBe( 2 );
			expect( $filterButton.removeClass.mock.calls.length ).toBe( 1 );
			expect( $filterButtonText.text.mock.calls.length ).toBe( 1 );
			expect( $filterBar.removeClass.mock.calls.length ).toBe( 1 );
		} );
	} );
} );
