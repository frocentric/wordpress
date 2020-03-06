/**
 * Modified from original source by Kevin Stover.
 *
 * In order to get access to dragging data, line 102 chaned from:
 * options.onDragStart()
 * to
 * options.onDragStart( this )
 * 
 * In order to get access to dragging data, line 144 changed from:
 * options.onDragEnd()
 * to
 * options.onDragEnd( this )
 *
 * In order to get access to dragging data, line 202 changed from:
 * options.onDrag()
 * to
 * options.onDrag( this )
 * 
 */
'use strict';

(function() {

var global = this
  , addEventListener = 'addEventListener'
  , removeEventListener = 'removeEventListener'
  , getBoundingClientRect = 'getBoundingClientRect'
  , isIE8 = global.attachEvent && !global[addEventListener]
  , document = global.document

  , calc = (function () {
        var el
          , prefixes = ["", "-webkit-", "-moz-", "-o-"]

        for (var i = 0; i < prefixes.length; i++) {
            el = document.createElement('div')
            el.style.cssText = "width:" + prefixes[i] + "calc(9px)"

            if (el.style.length) {
                return prefixes[i] + "calc"
            }
        }
    })()
  , elementOrSelector = function (el) {
        if (typeof el === 'string' || el instanceof String) {
            return document.querySelector(el)
        } else {
            return el
        }
    }

  , Split = function (ids, options) {
    var dimension
      , i
      , clientDimension
      , clientAxis
      , position
      , gutterClass
      , paddingA
      , paddingB
      , pairs = []

    // Set defaults

    options = typeof options !== 'undefined' ?  options : {}

    if (!options.gutterSize) options.gutterSize = 10
    if (!options.minSize) options.minSize = 100
    if (!options.snapOffset) options.snapOffset = 30
    if (!options.direction) options.direction = 'horizontal'

    if (options.direction == 'horizontal') {
        dimension = 'width'
        clientDimension = 'clientWidth'
        clientAxis = 'clientX'
        position = 'left'
        gutterClass = 'gutter gutter-horizontal'
        paddingA = 'paddingLeft'
        paddingB = 'paddingRight'
        if (!options.cursor) options.cursor = 'ew-resize'
    } else if (options.direction == 'vertical') {
        dimension = 'height'
        clientDimension = 'clientHeight'
        clientAxis = 'clientY'
        position = 'top'
        gutterClass = 'gutter gutter-vertical'
        paddingA = 'paddingTop'
        paddingB = 'paddingBottom'
        if (!options.cursor) options.cursor = 'ns-resize'
    }

    // Event listeners for drag events, bound to a pair object.
    // Calculate the pair's position and size when dragging starts.
    // Prevent selection on start and re-enable it when done.

    var startDragging = function (e) {
            var self = this
              , a = self.a
              , b = self.b

            if (!self.dragging && options.onDragStart) {
                options.onDragStart( this )
            }

            e.preventDefault()

            self.dragging = true
            self.move = drag.bind(self)
            self.stop = stopDragging.bind(self)

            global[addEventListener]('mouseup', self.stop)
            global[addEventListener]('touchend', self.stop)
            global[addEventListener]('touchcancel', self.stop)

            self.parent[addEventListener]('mousemove', self.move)
            self.parent[addEventListener]('touchmove', self.move)

            a[addEventListener]('selectstart', preventSelection)
            a[addEventListener]('dragstart', preventSelection)
            b[addEventListener]('selectstart', preventSelection)
            b[addEventListener]('dragstart', preventSelection)

            a.style.userSelect = 'none'
            a.style.webkitUserSelect = 'none'
            a.style.MozUserSelect = 'none'
            a.style.pointerEvents = 'none'

            b.style.userSelect = 'none'
            b.style.webkitUserSelect = 'none'
            b.style.MozUserSelect = 'none'
            b.style.pointerEvents = 'none'

            self.gutter.style.cursor = options.cursor
            self.parent.style.cursor = options.cursor

            calculateSizes.call(self)
        }
      , stopDragging = function () {
            var self = this
              , a = self.a
              , b = self.b

            if (self.dragging && options.onDragEnd) {
                options.onDragEnd( this )
            }

            self.dragging = false

            global[removeEventListener]('mouseup', self.stop)
            global[removeEventListener]('touchend', self.stop)
            global[removeEventListener]('touchcancel', self.stop)

            self.parent[removeEventListener]('mousemove', self.move)
            self.parent[removeEventListener]('touchmove', self.move)

            delete self.stop
            delete self.move

            a[removeEventListener]('selectstart', preventSelection)
            a[removeEventListener]('dragstart', preventSelection)
            b[removeEventListener]('selectstart', preventSelection)
            b[removeEventListener]('dragstart', preventSelection)

            a.style.userSelect = ''
            a.style.webkitUserSelect = ''
            a.style.MozUserSelect = ''
            a.style.pointerEvents = ''

            b.style.userSelect = ''
            b.style.webkitUserSelect = ''
            b.style.MozUserSelect = ''
            b.style.pointerEvents = ''

            self.gutter.style.cursor = ''
            self.parent.style.cursor = ''
        }
      , drag = function (e) {
            var offset

            if (!this.dragging) return

            // Get the relative position of the event from the first side of the
            // pair.

            if ('touches' in e) {
                offset = e.touches[0][clientAxis] - this.start
            } else {
                offset = e[clientAxis] - this.start
            }

            // If within snapOffset of min or max, set offset to min or max

            if (offset <=  this.aMin + options.snapOffset) {
                offset = this.aMin
            } else if (offset >= this.size - this.bMin - options.snapOffset) {
                offset = this.size - this.bMin
            }

            adjust.call(this, offset)

            if (options.onDrag) {
                options.onDrag( this )
            }
        }
      , calculateSizes = function () {
            // Calculate the pairs size, and percentage of the parent size
            var computedStyle = global.getComputedStyle(this.parent)
              , parentSize = this.parent[clientDimension] - parseFloat(computedStyle[paddingA]) - parseFloat(computedStyle[paddingB])

            this.size = this.a[getBoundingClientRect]()[dimension] + this.b[getBoundingClientRect]()[dimension] + this.aGutterSize + this.bGutterSize
            /*
             * This library has been modified for our use-case.
             * We are only interested in whole-numbers, so we're rounding all cell widths up if they have any remainders.
             */
            // this.percentage = Math.min( Math.ceil( this.size / parentSize * 100 ), 100)
            this.percentage = Math.min(this.size / parentSize * 100, 100)

            // if( isNaN( this.percentage ) ) {
            //     this.percentage = ( 100 / options.cellCollection.length ) * 2;
            // }

            this.start = this.a[getBoundingClientRect]()[position]
        }
      , adjust = function (offset) {
            // A size is the same as offset. B size is total size - A size.
            // Both sizes are calculated from the initial parent percentage.

             /*
             * If there are two cells, the percentage will always be 100.
             * TODO: This seems like a hacky fix. Need something more programmatic.
             */
            if ( 2 == options.cellCollection.length ) {
                this.percentage = 100;
            }

            var tmpA = (offset / this.size * this.percentage);
            var tmpB = (this.percentage - (offset / this.size * this.percentage));

            var test = getPercentages.call( this, [ tmpA, tmpB ], this.percentage );
            tmpA = test[0];
            tmpB = test[1];

            var total = 0;
            _.each( options.cellCollection.models, function( model, index ) {
                if ( model == jQuery( this.a ).data( 'model' ) ) {
                    total += tmpA;
                } else if ( model == jQuery( this.b ).data( 'model' ) ) {
                    total += tmpB;
                } else {
                   total += model.get( 'width' ); 
                }
            }, this );

            /*
             * Custom code that checks to see if we are under 100 in width.
             * If we are under 100, add the difference to the smaller of the two cells we are splitting.
             */
            if ( 100 > total ) {
                var diff = 100 - total;
                if ( tmpA > tmpB ) {
                    tmpB += diff;
                } else if ( tmpB > tmpA ) {
                    tmpA += diff;
                }
            }

            this.a.style[dimension] = calc + '(' + tmpA + '% - ' + 10 + 'px)'
            // this.a.style[dimension] = calc + '(' + tmpA + '% - ' + this.aGutterSize + 'px)'
            this.b.style[dimension] = calc + '(' + tmpB + '% - ' + 10 + 'px)'
            // this.b.style[dimension] = calc + '(' + tmpB + '% - ' + this.bGutterSize + 'px)'

            // console.log( pairs );

            /*
             * Custom code to fit our use case.
             * We set the 'width' data attribute when we drag.
             */
            jQuery( this.a ).data( 'width', tmpA );
            jQuery( this.b ).data( 'width', tmpB );

        }
      , getPercentages = function ( l, target ) {
            var off = target - _.reduce(l, function(acc, x) { return acc + Math.round(x) }, 0);
            return _.chain(l).
                    map(function(x, i) { return Math.round(x) + (off > i) - (i >= (l.length + off)) }).
                    value();
        }
      ,fitMin = function () {
            var self = this
              , a = self.a
              , b = self.b

            if (a[getBoundingClientRect]()[dimension] < self.aMin) {
                a.style[dimension] = (self.aMin - self.aGutterSize) + 'px'
                b.style[dimension] = (self.size - self.aMin - self.aGutterSize) + 'px'
            } else if (b[getBoundingClientRect]()[dimension] < self.bMin) {
                a.style[dimension] = (self.size - self.bMin - self.bGutterSize) + 'px'
                b.style[dimension] = (self.bMin - self.bGutterSize) + 'px'
            }
        }
      , fitMinReverse = function () {
            var self = this
              , a = self.a
              , b = self.b

            if (b[getBoundingClientRect]()[dimension] < self.bMin) {
                a.style[dimension] = (self.size - self.bMin - self.bGutterSize) + 'px'
                b.style[dimension] = (self.bMin - self.bGutterSize) + 'px'
            } else if (a[getBoundingClientRect]()[dimension] < self.aMin) {
                a.style[dimension] = (self.aMin - self.aGutterSize) + 'px'
                b.style[dimension] = (self.size - self.aMin - self.aGutterSize) + 'px'
            }
        }
      , balancePairs = function (pairs) {
            for (var i = 0; i < pairs.length; i++) {
                calculateSizes.call(pairs[i])
                fitMin.call(pairs[i])
            }

            for (i = pairs.length - 1; i >= 0; i--) {
                calculateSizes.call(pairs[i])
                fitMinReverse.call(pairs[i])
            }
        }
      , preventSelection = function () { return false }
      , parent = elementOrSelector(ids[0]).parentNode

    if (!options.sizes) {
        var percent = 100 / ids.length

        options.sizes = []

        for (i = 0; i < ids.length; i++) {
            options.sizes.push(percent)
        }
    }

    if (!Array.isArray(options.minSize)) {
        var minSizes = []

        for (i = 0; i < ids.length; i++) {
            minSizes.push(options.minSize)
        }

        options.minSize = minSizes
    }

    for (i = 0; i < ids.length; i++) {
        var el = elementOrSelector(ids[i])
          , isFirst = (i == 1)
          , isLast = (i == ids.length - 1)
          , size
          , gutterSize = options.gutterSize
          , pair

        if (i > 0) {
            pair = {
                a: elementOrSelector(ids[i - 1]),
                b: el,
                aMin: options.minSize[i - 1],
                bMin: options.minSize[i],
                dragging: false,
                parent: parent,
                isFirst: isFirst,
                isLast: isLast,
                direction: options.direction
            }

            // For first and last pairs, first and last gutter width is half.

            pair.aGutterSize = options.gutterSize
            pair.bGutterSize = options.gutterSize

            if (isFirst) {
                pair.aGutterSize = options.gutterSize / 2
            }

            if (isLast) {
                pair.bGutterSize = options.gutterSize / 2
            }
        }

        // IE9 and above
        if (!isIE8) {
            if (i > 0) {
                var gutter = document.createElement('div')

                gutter.className = gutterClass
                gutter.style[dimension] = options.gutterSize + 'px'

                gutter[addEventListener]('mousedown', startDragging.bind(pair))
                gutter[addEventListener]('touchstart', startDragging.bind(pair))

                parent.insertBefore(gutter, el)

                pair.gutter = gutter
            }

            if (i === 0 || i == ids.length - 1) {
                gutterSize = options.gutterSize / 2
            }

            if (typeof options.sizes[i] === 'string' || options.sizes[i] instanceof String) {
                size = options.sizes[i]
            } else {
                size = calc + '(' + options.sizes[i] + '% - ' + gutterSize + 'px)'
            }

        // IE8 and below
        } else {
            if (typeof options.sizes[i] === 'string' || options.sizes[i] instanceof String) {
                size = options.sizes[i]
            } else {
                size = options.sizes[i] + '%'
            }
        }
        el.style[dimension] = size

        if (i > 0) {
            pairs.push(pair)
        }
    }

    balancePairs(pairs)
}

if (typeof exports !== 'undefined') {
    if (typeof module !== 'undefined' && module.exports) {
        exports = module.exports = Split
    }
    exports.Split = Split
} else {
    global.Split = Split
}

}).call(window)
