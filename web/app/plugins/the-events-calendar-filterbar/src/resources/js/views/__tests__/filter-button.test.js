describe( 'Filter Button', () => {
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

		global.tribe = {
			filterBar: {
				filterBarState: {},
			},
		};
		require( '../filter-button' );
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
		tribe.filterBar.filterBarState.closeFilterBar = jest.fn();
		tribe.filterBar.filterBarState.openFilterBar = jest.fn();
	} );

	describe( 'Selectors', () => {
		test( 'Should match snapshot', () => {
			const selectors = JSON.stringify( tribe.filterBar.filterButton.selectors );
			expect( selectors ).toMatchSnapshot();
		} );
	} );

	describe( 'Handle resize', () => {
		let $filterBar;
		let $container;

		beforeEach( () => {
			$filterBar = $( '<div></div>' );
			$container = $( '<div></div>' );
			$container.find = () => $filterBar;
		} );

		test( 'Should open filter bar on resize from mobile to desktop if layout is vertical', () => {
			// Setup test.
			$filterBar.data( 'tribeEventsState', { filterButtonDesktopInitialized: false } );
			$container.data( 'tribeEventsState', { isMobile: false } );
			$container.is = () => true;
			const event = {
				data: {
					container: $container,
				},
			};

			// Confirm initial state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();

			// Test.
			tribe.filterBar.filterButton.handleResize( event );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filterBarState.openFilterBar.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filterBarState.closeFilterBar.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should not open filter bar on resize from mobile to desktop if layout is not vertical', () => {
			// Setup test.
			$filterBar.data( 'tribeEventsState', { filterButtonDesktopInitialized: false } );
			$container.data( 'tribeEventsState', { isMobile: false } );
			$container.is = () => true;
			const event = {
				data: {
					container: $container,
				},
			};

			// Confirm initial state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();

			// Test.
			tribe.filterBar.filterButton.handleResize( event );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filterBarState.openFilterBar.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filterBarState.closeFilterBar.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should close filter bar on resize from desktop to mobile', () => {
			// Setup test.
			$filterBar.data( 'tribeEventsState', { filterButtonDesktopInitialized: true } );
			$container.data( 'tribeEventsState', { isMobile: true } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Confirm initial state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();

			// Test.
			tribe.filterBar.filterButton.handleResize( event );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filterBarState.openFilterBar.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filterBarState.closeFilterBar.mock.calls.length ).toBe( 1 );
		} );

		test( 'Should not close filter bar on resize from mobile to mobile', () => {
			// Setup test.
			$filterBar.data( 'tribeEventsState', { filterButtonDesktopInitialized: false } );
			$container.data( 'tribeEventsState', { isMobile: true } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Confirm initial state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();

			// Test.
			tribe.filterBar.filterButton.handleResize( event );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filterBarState.openFilterBar.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filterBarState.closeFilterBar.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should not open filter bar on resize from desktop to desktop', () => {
			// Setup test.
			$filterBar.data( 'tribeEventsState', { filterButtonDesktopInitialized: true } );
			$container.data( 'tribeEventsState', { isMobile: false } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Confirm initial state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();

			// Test.
			tribe.filterBar.filterButton.handleResize( event );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filterBarState.openFilterBar.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filterBarState.closeFilterBar.mock.calls.length ).toBe( 0 );
		} );
	} );

	describe( 'Handle click', () => {
		let $container;

		beforeEach( () => {
			$container = $( '<div></div>' );
		} );

		test( 'Should return early if not mobile', () => {
			// Setup test.
			$container.data( 'tribeEventsState', { isMobile: false } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleClick( event );

			// Confirm final state.
			expect( tribe.filterBar.filterBarState.closeFilterBar.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should not close filter bar if click target parent is filter bar', () => {
			// Setup test.
			$container.data( 'tribeEventsState', { isMobile: true } );
			const filterBar = `
				<div
					class="tribe-filter-bar tribe-filter-bar--horizontal"
					id="tribe-filter-bar--12345"
					data-js="tribe-filter-bar"
				>
					<form
						class="tribe-filter-bar__form"
						method="post"
						action=""
						aria-labelledby="tribe-filter-bar__form-heading--12345"
						aria-describedby="tribe-filter-bar__form-description--12345"
					>
					</form>
				</div>
			`;
			const $filterBar = $( filterBar );
			const event = {
				target: $filterBar.find( 'form' )[ 0 ],
				data: {
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleClick( event );

			// Confirm final state.
			expect( tribe.filterBar.filterBarState.closeFilterBar.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should not close filter bar if click target parent is filter button', () => {
			// Setup test.
			$container.data( 'tribeEventsState', { isMobile: true } );
			const filterButton = `
				<button
					class="tribe-events-c-events-bar__filter-button"
					aria-controls="tribe-filter-bar--12345"
					aria-expanded="false"
					data-js="tribe-events-accordion-trigger tribe-events-filter-button"
				>
				</button>
			`;
			const $filterButton = $( filterButton );
			const event = {
				target: $filterButton[ 0 ],
				data: {
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleClick( event );

			// Confirm final state.
			expect( tribe.filterBar.filterBarState.closeFilterBar.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should close filter bar if click target parent not filter bar or filter button', () => {
			// Setup test.
			$container.data( 'tribeEventsState', { isMobile: true } );
			const event = {
				target: $container[ 0 ],
				data: {
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleClick( event );

			// Confirm final state.
			expect( tribe.filterBar.filterBarState.closeFilterBar.mock.calls.length ).toBe( 1 );
		} );
	} );

	describe( 'Handle action done click', () => {
		test( 'Should close filter bar', () => {
			// Setup test.
			const event = {
				data: {
					container: $(),
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleActionDoneClick( event );

			// Confirm final states.
			expect( tribe.filterBar.filterBarState.closeFilterBar.mock.calls.length ).toBe( 1 );
		} );
	} );

	describe( 'Handle filter button click', () => {
		let $container;
		let $filterButtonText;
		let $filterBar;

		beforeEach( () => {
			tribe.filterBar.filterBarSlider = {
				handleResize: jest.fn(),
			};
			$container = $( '<div></div>' );
			$filterButtonText = $( '<span></span>' );
			$filterBar = $( '<div></div>' );
			$container.find = () => $filterBar;
			$filterButtonText.text = jest.fn();
			$filterBar.toggleClass = jest.fn();
		} );

		afterEach( () => {
			delete global.tribe.filterBar.filterBarSlider;
		} );

		test( 'Should close filter bar on click', () => {
			// Setup test.
			const $filterButton = $( '<button></button>' );
			$container.data = () => ( { isMobile: false } );
			$filterButton.addClass( tribe.filterBar.filterButton.selectors.filterButtonActive.className() );
			$filterButton.find = () => $filterButtonText;
			$filterButton.toggleClass = jest.fn();
			const event = {
				data: {
					target: $filterButton,
					actionDone: $(),
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleFilterButtonClick( event );

			// Confirm final states.
			expect( tribe.filterBar.filterBarState.closeFilterBar.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filterBarState.openFilterBar.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filterBarSlider.handleResize.mock.calls.length ).toBe( 1 );
		} );

		test( 'Should open filter bar on click', () => {
			// Setup test.
			const $filterButton = $( '<button></button>' );
			$container.data = () => ( { isMobile: false } );
			$filterButton.find = () => $filterButtonText;
			$filterButton.toggleClass = jest.fn();
			const event = {
				data: {
					target: $filterButton,
					actionDone: $(),
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterButton.handleFilterButtonClick( event );

			// Confirm final states.
			expect( tribe.filterBar.filterBarState.closeFilterBar.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filterBarState.openFilterBar.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filterBarSlider.handleResize.mock.calls.length ).toBe( 1 );
		} );
	} );

	describe( 'Initialize state', () => {
		test( 'Should initialize state if filter bar is vertical', () => {
			// Setup test.
			const $container = $( '<div></div>' );
			const $filterBar = $( '<div></div>' );
			$container.find = () => $filterBar;
			$container.data( 'tribeEventsState', { isMobile: true } );

			// Test.
			tribe.filterBar.filterButton.initState( $container );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
		} );
	} );
} );
