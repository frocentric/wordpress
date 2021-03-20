describe( 'Filter Toggle', () => {
	const openedFilter = `
		<div class="tribe-filter-bar-c-filter tribe-filter-bar-c-filter--has-selections tribe-filter-bar-c-filter--pill tribe-filter-bar-c-filter--open">
			<div class="tribe-filter-bar-c-filter__toggle-wrapper">
				<button
					class="tribe-filter-bar-c-filter__toggle tribe-common-h7 tribe-common-h--alt"
					id="toggle-id"
					type="button"
					aria-controls="container-id"
					aria-expanded="true"
					data-js="tribe-events-accordion-trigger tribe-filter-bar-c-filter-toggle"
				>
				</button>
			</div>
			<div
				class="tribe-filter-bar-c-filter__container"
				id="container-id"
				aria-hidden="false"
				aria-labelledby="toggle-id"
			>
			</div>
		</div>
	`;

	const closedFilter = `
		<div class="tribe-filter-bar-c-filter tribe-filter-bar-c-filter--has-selections tribe-filter-bar-c-filter--pill">
			<div class="tribe-filter-bar-c-filter__toggle-wrapper">
				<button
					class="tribe-filter-bar-c-filter__toggle tribe-common-h7 tribe-common-h--alt"
					id="toggle-id"
					type="button"
					aria-controls="container-id"
					aria-expanded="true"
					data-js="tribe-events-accordion-trigger tribe-filter-bar-c-filter-toggle"
				>
				</button>
			</div>
			<div
				class="tribe-filter-bar-c-filter__container"
				id="container-id"
				aria-hidden="false"
				aria-labelledby="toggle-id"
			>
			</div>
		</div>
	`;

	const openedPill = `
		<div class="tribe-filter-bar-c-pill tribe-filter-bar-c-pill--button">
			<button
				class="tribe-filter-bar-c-pill__pill tribe-common-b2 tribe-common-b3--min-medium"
				id="pill-toggle-id"
				aria-controls="container-id"
				aria-expanded="true"
				data-js="tribe-events-accordion-trigger tribe-filter-bar-filters-slide-pill"
				type="button"
			>
			</button>
		</div>
	`;

	const closedPill = `
		<div class="tribe-filter-bar-c-pill tribe-filter-bar-c-pill--button">
			<button
				class="tribe-filter-bar-c-pill__pill tribe-common-b2 tribe-common-b3--min-medium"
				id="pill-toggle-id"
				aria-controls="container-id"
				aria-expanded="false"
				data-js="tribe-events-accordion-trigger tribe-filter-bar-filters-slide-pill"
				type="button"
			>
			</button>
		</div>
	`;

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
		require( '../filter-toggle' );
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

	describe( 'Selectors', () => {
		test( 'Should match snapshot', () => {
			const selectors = JSON.stringify( tribe.filterBar.filterToggle.selectors );
			expect( selectors ).toMatchSnapshot();
		} );
	} );

	describe( 'Open filter', () => {
		let filter;
		let pill;

		beforeEach( () => {
			filter = closedFilter;
			pill = closedPill;
		} );

		test( 'Should return early if filter container does not have ID', () => {
			// Setup test.
			tribe.events.views.accordion.openAccordion = jest.fn();
			const $filterBar = $();
			const $filter = $( filter );

			// Confirm initial state.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeFalsy();

			// Test.
			$filter.find( '.tribe-filter-bar-c-filter__container' ).removeAttr( 'id' );
			tribe.filterBar.filterToggle.openFilter( $filterBar, $filter );

			// Confirm final states.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeFalsy();
			expect( tribe.events.views.accordion.openAccordion.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should open filter', () => {
			// Setup test.
			tribe.events.views.accordion.openAccordion = jest.fn();
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
						${ filter }
					</form>
				</div>
			`;
			const $filterBar = $( filterBar );
			const $filter = $( filter );

			// Confirm initial state.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeFalsy();

			// Test.
			tribe.filterBar.filterToggle.openFilter( $filterBar, $filter );

			// Confirm final states.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeTruthy();
			expect( tribe.events.views.accordion.openAccordion.mock.calls.length ).toBe( 1 );
		} );

		test( 'Should open filter and pill toggle', () => {
			// Setup test.
			tribe.events.views.accordion.openAccordion = jest.fn();
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
						${ filter }
						${ pill }
					</form>
				</div>
			`;
			const $filterBar = $( filterBar );
			const $filter = $( filter );

			// Confirm initial state.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeFalsy();

			// Test.
			tribe.filterBar.filterToggle.openFilter( $filterBar, $filter );

			// Confirm final states.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeTruthy();
			expect( tribe.events.views.accordion.openAccordion.mock.calls.length ).toBe( 2 );
		} );
	} );

	describe( 'Close filter', () => {
		let filter;
		let pill;

		beforeEach( () => {
			filter = openedFilter;
			pill = openedPill;
		} );

		test( 'Should return early if filter container does not have ID', () => {
			// Setup test.
			tribe.events.views.accordion.closeAccordion = jest.fn();
			const $filterBar = $();
			const $filter = $( filter );

			// Confirm initial state.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeTruthy();

			// Test.
			$filter.find( '.tribe-filter-bar-c-filter__container' ).removeAttr( 'id' );
			tribe.filterBar.filterToggle.closeFilter( $filterBar, $filter );

			// Confirm final states.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeTruthy();
			expect( tribe.events.views.accordion.closeAccordion.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should close filter', () => {
			// Setup test.
			tribe.events.views.accordion.closeAccordion = jest.fn();
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
						${ filter }
					</form>
				</div>
			`;
			const $filterBar = $( filterBar );
			const $filter = $( filter );

			// Confirm initial state.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeTruthy();

			// Test.
			tribe.filterBar.filterToggle.closeFilter( $filterBar, $filter );

			// Confirm final states.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeFalsy();
			expect( tribe.events.views.accordion.closeAccordion.mock.calls.length ).toBe( 1 );
		} );

		test( 'Should close filter and pill toggle', () => {
			// Setup test.
			tribe.events.views.accordion.closeAccordion = jest.fn();
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
						${ filter }
						${ pill }
					</form>
				</div>
			`;
			const $filterBar = $( filterBar );
			const $filter = $( filter );

			// Confirm initial state.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeTruthy();

			// Test.
			tribe.filterBar.filterToggle.closeFilter( $filterBar, $filter );

			// Confirm final states.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeFalsy();
			expect( tribe.events.views.accordion.closeAccordion.mock.calls.length ).toBe( 2 );
		} );
	} );

	describe( 'Handle toggle click', () => {
		let event;

		beforeEach( () => {
			event = {
				data: {
					target: {},
				},
			};
		} );

		test( 'Should open filter', () => {
			// Setup test.
			const filter = closedFilter;
			const $filter = $( filter );
			event.data.target.closest = () => $filter;

			// Confirm initial state.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeFalsy();

			// Test.
			tribe.filterBar.filterToggle.handleToggleClick( event );

			// Confirm final state.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeTruthy();
		} );

		test( 'Should close filter', () => {
			// Setup test.
			const filter = openedFilter;
			const $filter = $( filter );
			event.data.target.closest = () => $filter;

			// Confirm initial state.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeTruthy();

			// Test.
			tribe.filterBar.filterToggle.handleToggleClick( event );

			// Confirm final state.
			expect( $filter.hasClass( tribe.filterBar.filterToggle.selectors.filterOpen.className() ) ).toBeFalsy();
		} );
	} );

	describe( 'Handle close click', () => {
		test( 'Should close filter', () => {
			// Setup test.
			const $filter = $();
			$filter.closest = () => $();
			const event = {
				data: {
					target: {
						closest: () => $filter,
					},
				},
			};
			const closeFilterHold = tribe.filterBar.filterToggle.closeFilter;
			tribe.filterBar.filterToggle.closeFilter = jest.fn();

			// Test.
			tribe.filterBar.filterToggle.handleCloseClick( event );

			// Confirm final state.
			expect( tribe.filterBar.filterToggle.closeFilter.mock.calls.length ).toBe( 1 );

			// Cleanup test.
			tribe.filterBar.filterToggle.closeFilter = closeFilterHold;
		} );
	} );

	describe( 'Handle pill toggle click', () => {
		let closeFilterHold;
		let openFilterHold;

		beforeEach( () => {
			closeFilterHold = tribe.filterBar.filterToggle.closeFilter;
			openFilterHold = tribe.filterBar.filterToggle.openFilter;
			tribe.filterBar.filterToggle.closeFilter = jest.fn();
			tribe.filterBar.filterToggle.openFilter = jest.fn();
		} );

		afterEach( () => {
			tribe.filterBar.filterToggle.closeFilter = closeFilterHold;
			tribe.filterBar.filterToggle.openFilter = openFilterHold;
		} );

		test( 'Should return early if aria-controls does not exist.', () => {
			// Setup test.
			const $pill = $( closedPill );
			const $pillToggle = $pill.find( tribe.filterBar.filterToggle.selectors.pillFilterToggle );
			$pillToggle.removeAttr( 'aria-controls' );
			const event = {
				data: {
					target: $pillToggle,
				},
			};

			// Test.
			tribe.filterBar.filterToggle.handlePillToggleClick( event );

			// Confirm final states.
			expect( tribe.filterBar.filterToggle.closeFilter.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filterToggle.openFilter.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should close the filter', () => {
			// Setup test.
			const pill = openedPill;
			const $pill = $( pill );
			const $pillToggle = $pill.find( tribe.filterBar.filterToggle.selectors.pillFilterToggle );
			const filter = openedFilter;
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
						${ filter }
						${ pill }
					</form>
				</div>
			`;
			const $filterBar = $( filterBar );
			$pillToggle.closest = () => $filterBar;
			const event = {
				data: {
					target: $pillToggle,
				},
			};

			// Test.
			tribe.filterBar.filterToggle.handlePillToggleClick( event );

			// Confirm final states.
			expect( tribe.filterBar.filterToggle.closeFilter.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filterToggle.openFilter.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should open the filter', () => {
			// Setup test.
			const pill = closedPill;
			const $pill = $( pill );
			const $pillToggle = $pill.find( tribe.filterBar.filterToggle.selectors.pillFilterToggle );
			const filter = closedFilter;
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
						${ filter }
						${ pill }
					</form>
				</div>
			`;
			const $filterBar = $( filterBar );
			$pillToggle.closest = () => $filterBar;
			const event = {
				data: {
					target: $pillToggle,
				},
			};

			// Test.
			tribe.filterBar.filterToggle.handlePillToggleClick( event );

			// Confirm final states.
			expect( tribe.filterBar.filterToggle.closeFilter.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filterToggle.openFilter.mock.calls.length ).toBe( 1 );
		} );
	} );

	describe( 'Handle resize', () => {
		let closeFilterHold;
		let $filterBar;
		let $container;

		beforeEach( () => {
			closeFilterHold = tribe.filterBar.filterToggle.closeFilter;
			tribe.filterBar.filterToggle.closeFilter = jest.fn();

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
						${ openedFilter }
						${ openedPill }
					</form>
				</div>
			`;
			$filterBar = $( filterBar );
			$container = $( '<div></div>' );
			$container.find = () => $filterBar;
		} );

		afterEach( () => {
			tribe.filterBar.filterToggle.closeFilter = closeFilterHold;
		} );

		test( 'Should close all filters on resize from mobile to desktop', () => {
			// Setup test.
			$filterBar.data( 'tribeEventsState', { filterToggleDesktopInitialized: false } );
			$container.data( 'tribeEventsState', { isMobile: false } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Confirm initial state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();

			// Test.
			tribe.filterBar.filterToggle.handleResize( event );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filterToggle.closeFilter.mock.calls.length ).toBe( 1 );
		} );

		test( 'Should not close filters on resize from desktop to mobile', () => {
			// Setup test.
			$filterBar.data( 'tribeEventsState', { filterToggleDesktopInitialized: true } );
			$container.data( 'tribeEventsState', { isMobile: true } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Confirm initial state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();

			// Test.
			tribe.filterBar.filterToggle.handleResize( event );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filterToggle.closeFilter.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should not close filters on resize from mobile to mobile', () => {
			// Setup test.
			$filterBar.data( 'tribeEventsState', { filterToggleDesktopInitialized: false } );
			$container.data( 'tribeEventsState', { isMobile: true } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Confirm initial state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();

			// Test.
			tribe.filterBar.filterToggle.handleResize( event );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filterToggle.closeFilter.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should not close filters on resize from desktop to desktop', () => {
			// Setup test.
			$filterBar.data( 'tribeEventsState', { filterToggleDesktopInitialized: true } );
			$container.data( 'tribeEventsState', { isMobile: false } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Confirm initial state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();

			// Test.
			tribe.filterBar.filterToggle.handleResize( event );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filterToggle.closeFilter.mock.calls.length ).toBe( 0 );
		} );
	} );

	describe( 'Handle click', () => {
		let closeFilterHold;
		let filterBar;
		let $filterBar;
		let $container;

		beforeEach( () => {
			closeFilterHold = tribe.filterBar.filterToggle.closeFilter;
			tribe.filterBar.filterToggle.closeFilter = jest.fn();

			filterBar = `
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
						${ openedFilter }
						<div class="tribe-filter-bar__filters-slider-container">
							<div class="tribe-filter-bar__filters-slider-wrapper">
								<div class="tribe-filter-bar__filters-slide">
									${ openedPill }
								</div>
							</div>
						</div>
					</form>
				</div>
			`;
			$filterBar = $( filterBar );
			$container = $( '<div></div>' );
			$container.find = () => $filterBar;
		} );

		afterEach( () => {
			tribe.filterBar.filterToggle.closeFilter = closeFilterHold;
		} );

		test( 'Should return early if mobile', () => {
			// Setup test.
			$container.data( 'tribeEventsState', { isMobile: true } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterToggle.handleClick( event );

			// Confirm final state.
			expect( tribe.filterBar.filterToggle.closeFilter.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should not close filters if click target parent is filter', () => {
			// Setup test.
			document.body.innerHTML = filterBar;
			$container.data( 'tribeEventsState', { isMobile: false } );
			const event = {
				target: $filterBar.find( tribe.filterBar.filterToggle.selectors.filterContainer )[ 0 ],
				data: {
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterToggle.handleClick( event );

			// Confirm final state.
			expect( tribe.filterBar.filterToggle.closeFilter.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should not close filters if click target parent is filter slider', () => {
			// Setup test.
			document.body.innerHTML = filterBar;
			$container.data( 'tribeEventsState', { isMobile: false } );
			const event = {
				target: $filterBar.find( tribe.filterBar.filterToggle.selectors.filtersSliderContainer )[ 0 ],
				data: {
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterToggle.handleClick( event );

			// Confirm final state.
			expect( tribe.filterBar.filterToggle.closeFilter.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should close filters if click target parent is neither filter nor filter slider', () => {
			// Setup test.
			document.body.innerHTML = filterBar;
			$container.data( 'tribeEventsState', { isMobile: false } );
			const event = {
				target: $container[ 0 ],
				data: {
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterToggle.handleClick( event );

			// Confirm final state.
			expect( tribe.filterBar.filterToggle.closeFilter.mock.calls.length ).toBe( 1 );
		} );
	} );

	describe( 'Initialize state', () => {
		test( 'Should return early if filter bar is not horizontal', () => {
			// Setup test.
			const $container = $( '<div></div>' );
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
						${ openedFilter }
						${ openedPill }
					</form>
				</div>
			`;
			const $filterBar = $( filterBar );
			$container.find = () => $filterBar;

			// Test.
			tribe.filterBar.filterToggle.initState( $container );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
		} );

		test( 'Should initialize state if filter bar is horizontal', () => {
			// Setup test.
			const container = '<div class="' + tribe.filterBar.filterToggle.selectors.filterBarHorizontal.className() + '"></div>';
			const $container = $( container );
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
						${ openedFilter }
						${ openedPill }
					</form>
				</div>
			`;
			const $filterBar = $( filterBar );
			$container.find = () => $filterBar;

			// Test.
			tribe.filterBar.filterToggle.initState( $container );

			// Confirm final state.
			expect( $filterBar.data( 'tribeEventsState' ) ).toMatchSnapshot();
		} );
	} );
} );
