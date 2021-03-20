describe( 'Filter Bar Slider', () => {
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
		require( '../filter-bar-slider' );
		tribe.filterBar.filterToggle = {};
	} );

	afterAll( () => {
		delete String.prototype.className;
		delete global.tribe;
	} );

	describe( 'Selectors', () => {
		test( 'Should match snapshot', () => {
			const selectors = JSON.stringify( tribe.filterBar.filterBarSlider.selectors );
			expect( selectors ).toMatchSnapshot();
		} );
	} );

	describe( 'Handle slider overflow', () => {
		let $container;
		let $filterBar;
		let filtersSliderContainer;
		let $filtersSliderNav;

		beforeEach( () => {
			$container = $();
			$container.find = () => $filterBar;
			$filterBar = $();
			filtersSliderContainer = document.createElement( 'div' );
			filtersSliderContainer.swiper = {};
			$filtersSliderNav = $();
			$filtersSliderNav.addClass = jest.fn( () => $filtersSliderNav );
			$filtersSliderNav.removeClass = jest.fn( () => $filtersSliderNav );
		} );

		test( 'Should remove all overflow classes', () => {
			// Setup test.
			filtersSliderContainer.swiper.isBeginning = true;
			filtersSliderContainer.swiper.isEnd = true;
			const $filtersSliderContainer = $( filtersSliderContainer );
			$filtersSliderContainer.find = () => $filtersSliderNav;
			$filterBar.find = () => $filtersSliderContainer;

			// Test.
			tribe.filterBar.filterBarSlider.handleSliderOverflow( $container );

			// Confirm final states.
			expect( $filtersSliderNav.removeClass.mock.calls.length ).toBe( 2 );
			expect( $filtersSliderNav.addClass.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should add overflow start class', () => {
			// Setup test.
			filtersSliderContainer.swiper.isBeginning = false;
			filtersSliderContainer.swiper.isEnd = true;
			const $filtersSliderContainer = $( filtersSliderContainer );
			$filtersSliderContainer.find = () => $filtersSliderNav;
			$filterBar.find = () => $filtersSliderContainer;

			// Test.
			tribe.filterBar.filterBarSlider.handleSliderOverflow( $container );

			// Confirm final states.
			expect( $filtersSliderNav.removeClass.mock.calls.length ).toBe( 2 );
			expect( $filtersSliderNav.addClass.mock.calls.length ).toBe( 1 );
			expect( $filtersSliderNav.addClass.mock.calls[ 0 ][ 0 ] )
				.toBe( tribe.filterBar.filterBarSlider.selectors.filtersSliderNavOverflowStart.className() );
		} );

		test( 'Should add overflow end class', () => {
			// Setup test.
			filtersSliderContainer.swiper.isBeginning = true;
			filtersSliderContainer.swiper.isEnd = false;
			const $filtersSliderContainer = $( filtersSliderContainer );
			$filtersSliderContainer.find = () => $filtersSliderNav;
			$filterBar.find = () => $filtersSliderContainer;

			// Test.
			tribe.filterBar.filterBarSlider.handleSliderOverflow( $container );

			// Confirm final states.
			expect( $filtersSliderNav.removeClass.mock.calls.length ).toBe( 2 );
			expect( $filtersSliderNav.addClass.mock.calls.length ).toBe( 1 );
			expect( $filtersSliderNav.addClass.mock.calls[ 0 ][ 0 ] )
				.toBe( tribe.filterBar.filterBarSlider.selectors.filtersSliderNavOverflowEnd.className() );
		} );

		test( 'Should add both overflow start and end classes', () => {
			// Setup test.
			filtersSliderContainer.swiper.isBeginning = false;
			filtersSliderContainer.swiper.isEnd = false;
			const $filtersSliderContainer = $( filtersSliderContainer );
			$filtersSliderContainer.find = () => $filtersSliderNav;
			$filterBar.find = () => $filtersSliderContainer;

			// Test.
			tribe.filterBar.filterBarSlider.handleSliderOverflow( $container );

			// Confirm final states.
			expect( $filtersSliderNav.removeClass.mock.calls.length ).toBe( 2 );
			expect( $filtersSliderNav.addClass.mock.calls.length ).toBe( 2 );
			expect( $filtersSliderNav.addClass.mock.calls[ 0 ][ 0 ] )
				.toBe( tribe.filterBar.filterBarSlider.selectors.filtersSliderNavOverflowStart.className() );
			expect( $filtersSliderNav.addClass.mock.calls[ 1 ][ 0 ] )
				.toBe( tribe.filterBar.filterBarSlider.selectors.filtersSliderNavOverflowEnd.className() );
		} );
	} );

	describe( 'Handle slider translate', () => {
		test( 'Should update filters container styles', () => {
			// Setup test.
			tribe.filterBar.filterToggle.closeAllFilters = jest.fn();
			const $filtersContainer = $( '<div></div>' );
			const $filterBar = $();
			$filterBar.find = () => $filtersContainer;
			const $container = $();
			$container.find = () => $filterBar;

			// Confirm initial state.
			expect( $filtersContainer.css( 'transform' ) ).toMatchSnapshot();

			// Test.
			tribe.filterBar.filterBarSlider.handleSliderTranslate( $container, 80 );

			// Confirm final states.
			expect( $filtersContainer.css( 'transform' ) ).toMatchSnapshot();
			expect( tribe.filterBar.filterToggle.closeAllFilters.mock.calls.length ).toBe( 1 );
		} );
	} );

	describe( 'Deinitialize slider', () => {
		test( 'Should deinitialize filter slider', () => {
			// Setup test.
			const filtersSliderContainer = document.createElement( 'div' );
			filtersSliderContainer.swiper = {
				destroy: jest.fn(),
			};
			const $filtersSliderContainer = $( filtersSliderContainer );
			const $filtersContainer = $( '<div></div>' );
			const $container = $();
			$container.find = ( selector ) => {
				switch ( selector ) {
					case tribe.filterBar.filterBarSlider.selectors.filtersSliderContainer:
						return $filtersSliderContainer;
					case tribe.filterBar.filterBarSlider.selectors.filtersContainer:
						return $filtersContainer;
					default:
						return $();
				}
			};

			// Test.
			tribe.filterBar.filterBarSlider.deinitSlider( $container );

			// Confirm final state.
			expect( filtersSliderContainer.swiper.destroy.mock.calls.length ).toBe( 1 );
			expect( $filtersContainer.css( 'transform' ) ).toMatchSnapshot();
		} );
	} );

	describe( 'Initialize slider', () => {
		beforeEach( () => {
			global.Swiper = jest.fn();
		} );

		afterEach( () => {
			delete global.Swiper;
		} );

		test( 'Should initialize filter slider', () => {
			// Setup test.
			const $filtersSliderContainer = $( '<div></div>' );
			const $container = $();
			$container.find = () => $filtersSliderContainer;

			// Test.
			tribe.filterBar.filterBarSlider.initSlider( $container );

			// Confirm final state.
			expect( global.Swiper.mock.calls.length ).toBe( 1 );
		} );

		test( 'Should update and return early', () => {
			// Setup test.
			const filtersSliderContainer = document.createElement( 'div' );
			filtersSliderContainer.swiper = {
				update: jest.fn(),
			};
			const $filtersSliderContainer = $( filtersSliderContainer );
			const $container = $();
			$container.find = () => $filtersSliderContainer;

			// Test.
			tribe.filterBar.filterBarSlider.initSlider( $container );

			// Confirm final state.
			expect( global.Swiper.mock.calls.length ).toBe( 0 );
			expect( filtersSliderContainer.swiper.update.mock.calls.length ).toBe( 1 );
		} );
	} );

	describe( 'Handle resize', () => {
		let deinitSliderHold;
		let initSliderHold;

		beforeEach( () => {
			deinitSliderHold = tribe.filterBar.filterBarSlider.deinitSlider;
			initSliderHold = tribe.filterBar.filterBarSlider.initSlider;
			tribe.filterBar.filterBarSlider.deinitSlider = jest.fn();
			tribe.filterBar.filterBarSlider.initSlider = jest.fn();
		} );

		afterEach( () => {
			tribe.filterBar.filterBarSlider.deinitSlider = deinitSliderHold;
			tribe.filterBar.filterBarSlider.initSlider = initSliderHold;
		} );

		test( 'Should deinitialize slider on mobile', () => {
			// Setup test.
			const $container = $( '<div></div>' );
			$container.data( 'tribeEventsState', { isMobile: true } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterBarSlider.handleResize( event );

			// Confirm final state.
			expect( tribe.filterBar.filterBarSlider.deinitSlider.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filterBarSlider.initSlider.mock.calls.length ).toBe( 0 );
		} );

		test( 'Should initialize slider on desktop', () => {
			// Setup test.
			const $container = $( '<div></div>' );
			$container.data( 'tribeEventsState', { isMobile: false } );
			const event = {
				data: {
					container: $container,
				},
			};

			// Test.
			tribe.filterBar.filterBarSlider.handleResize( event );

			// Confirm final state.
			expect( tribe.filterBar.filterBarSlider.deinitSlider.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filterBarSlider.initSlider.mock.calls.length ).toBe( 1 );
		} );
	} );

	describe( 'Unbind events', () => {
		test( 'Should unbind events', () => {
			// Setup test.
			const $container = $();
			$container.off = jest.fn();

			// Test.
			tribe.filterBar.filterBarSlider.unbindEvents( $container );

			// Confirm final state.
			expect( $container.off.mock.calls.length ).toBe( 1 );
		} );
	} );

	describe( 'Bind events', () => {
		test( 'Should bind events', () => {
			// Setup test.
			const $container = $();
			$container.on = jest.fn();

			// Test.
			tribe.filterBar.filterBarSlider.bindEvents( $container );

			// Confirm final state.
			expect( $container.on.mock.calls.length ).toBe( 1 );
		} );
	} );

	describe( 'Deinitialize', () => {
		test( 'Should deinitialize filter bar slider', () => {
			// Setup test.
			const deinitSliderHold = tribe.filterBar.filterBarSlider.deinitSlider;
			const unbindEventsHold = tribe.filterBar.filterBarSlider.unbindEvents;
			tribe.filterBar.filterBarSlider.deinitSlider = jest.fn();
			tribe.filterBar.filterBarSlider.unbindEvents = jest.fn();
			const event = {
				data: {
					container: $(),
				},
			};

			// Test.
			tribe.filterBar.filterBarSlider.deinit( event, {}, {} );

			// Confirm final state.
			expect( tribe.filterBar.filterBarSlider.deinitSlider.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filterBarSlider.unbindEvents.mock.calls.length ).toBe( 1 );

			// Cleanup test.
			tribe.filterBar.filterBarSlider.deinitSlider = deinitSliderHold;
			tribe.filterBar.filterBarSlider.unbindEvents = unbindEventsHold;
		} );
	} );

	describe( 'Initialize', () => {
		let handleResizeHold;
		let bindEventsHold;

		beforeEach( () => {
			handleResizeHold = tribe.filterBar.filterBarSlider.handleResize;
			bindEventsHold = tribe.filterBar.filterBarSlider.bindEvents;
			tribe.filterBar.filterBarSlider.handleResize = jest.fn();
			tribe.filterBar.filterBarSlider.bindEvents = jest.fn();
		} );

		afterEach( () => {
			tribe.filterBar.filterBarSlider.handleResize = handleResizeHold;
			tribe.filterBar.filterBarSlider.bindEvents = bindEventsHold;
		} );

		test( 'Should initialize filter bar slider', () => {
			// Setup test.
			const event = {};
			const index = 0;
			const $container = $( '<div class="' + tribe.filterBar.filterBarSlider.selectors.filterBarHorizontal.className() + '"></div>' );
			const data = {};

			// Test.
			tribe.filterBar.filterBarSlider.init( event, index, $container, data );

			// Confirm final state.
			expect( tribe.filterBar.filterBarSlider.handleResize.mock.calls.length ).toBe( 1 );
			expect( tribe.filterBar.filterBarSlider.bindEvents.mock.calls.length ).toBe( 1 );
		} );

		test( 'Should return early if filter bar is not horizontal', () => {
			// Setup test.
			const event = {};
			const index = 0;
			const $container = $();
			const data = {};

			// Test.
			tribe.filterBar.filterBarSlider.init( event, index, $container, data );

			// Confirm final state.
			expect( tribe.filterBar.filterBarSlider.handleResize.mock.calls.length ).toBe( 0 );
			expect( tribe.filterBar.filterBarSlider.bindEvents.mock.calls.length ).toBe( 0 );
		} );
	} );
} );
