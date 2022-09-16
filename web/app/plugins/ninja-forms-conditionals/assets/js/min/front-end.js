(function () {
/**
 * @license almond 0.3.1 Copyright (c) 2011-2014, The Dojo Foundation All Rights Reserved.
 * Available via the MIT or new BSD license.
 * see: http://github.com/jrburke/almond for details
 */
//Going sloppy to avoid 'use strict' string cost, but strict practices should
//be followed.
/*jslint sloppy: true */
/*global setTimeout: false */

var requirejs, require, define;
(function (undef) {
    var main, req, makeMap, handlers,
        defined = {},
        waiting = {},
        config = {},
        defining = {},
        hasOwn = Object.prototype.hasOwnProperty,
        aps = [].slice,
        jsSuffixRegExp = /\.js$/;

    function hasProp(obj, prop) {
        return hasOwn.call(obj, prop);
    }

    /**
     * Given a relative module name, like ./something, normalize it to
     * a real name that can be mapped to a path.
     * @param {String} name the relative name
     * @param {String} baseName a real name that the name arg is relative
     * to.
     * @returns {String} normalized name
     */
    function normalize(name, baseName) {
        var nameParts, nameSegment, mapValue, foundMap, lastIndex,
            foundI, foundStarMap, starI, i, j, part,
            baseParts = baseName && baseName.split("/"),
            map = config.map,
            starMap = (map && map['*']) || {};

        //Adjust any relative paths.
        if (name && name.charAt(0) === ".") {
            //If have a base name, try to normalize against it,
            //otherwise, assume it is a top-level require that will
            //be relative to baseUrl in the end.
            if (baseName) {
                name = name.split('/');
                lastIndex = name.length - 1;

                // Node .js allowance:
                if (config.nodeIdCompat && jsSuffixRegExp.test(name[lastIndex])) {
                    name[lastIndex] = name[lastIndex].replace(jsSuffixRegExp, '');
                }

                //Lop off the last part of baseParts, so that . matches the
                //"directory" and not name of the baseName's module. For instance,
                //baseName of "one/two/three", maps to "one/two/three.js", but we
                //want the directory, "one/two" for this normalization.
                name = baseParts.slice(0, baseParts.length - 1).concat(name);

                //start trimDots
                for (i = 0; i < name.length; i += 1) {
                    part = name[i];
                    if (part === ".") {
                        name.splice(i, 1);
                        i -= 1;
                    } else if (part === "..") {
                        if (i === 1 && (name[2] === '..' || name[0] === '..')) {
                            //End of the line. Keep at least one non-dot
                            //path segment at the front so it can be mapped
                            //correctly to disk. Otherwise, there is likely
                            //no path mapping for a path starting with '..'.
                            //This can still fail, but catches the most reasonable
                            //uses of ..
                            break;
                        } else if (i > 0) {
                            name.splice(i - 1, 2);
                            i -= 2;
                        }
                    }
                }
                //end trimDots

                name = name.join("/");
            } else if (name.indexOf('./') === 0) {
                // No baseName, so this is ID is resolved relative
                // to baseUrl, pull off the leading dot.
                name = name.substring(2);
            }
        }

        //Apply map config if available.
        if ((baseParts || starMap) && map) {
            nameParts = name.split('/');

            for (i = nameParts.length; i > 0; i -= 1) {
                nameSegment = nameParts.slice(0, i).join("/");

                if (baseParts) {
                    //Find the longest baseName segment match in the config.
                    //So, do joins on the biggest to smallest lengths of baseParts.
                    for (j = baseParts.length; j > 0; j -= 1) {
                        mapValue = map[baseParts.slice(0, j).join('/')];

                        //baseName segment has  config, find if it has one for
                        //this name.
                        if (mapValue) {
                            mapValue = mapValue[nameSegment];
                            if (mapValue) {
                                //Match, update name to the new value.
                                foundMap = mapValue;
                                foundI = i;
                                break;
                            }
                        }
                    }
                }

                if (foundMap) {
                    break;
                }

                //Check for a star map match, but just hold on to it,
                //if there is a shorter segment match later in a matching
                //config, then favor over this star map.
                if (!foundStarMap && starMap && starMap[nameSegment]) {
                    foundStarMap = starMap[nameSegment];
                    starI = i;
                }
            }

            if (!foundMap && foundStarMap) {
                foundMap = foundStarMap;
                foundI = starI;
            }

            if (foundMap) {
                nameParts.splice(0, foundI, foundMap);
                name = nameParts.join('/');
            }
        }

        return name;
    }

    function makeRequire(relName, forceSync) {
        return function () {
            //A version of a require function that passes a moduleName
            //value for items that may need to
            //look up paths relative to the moduleName
            var args = aps.call(arguments, 0);

            //If first arg is not require('string'), and there is only
            //one arg, it is the array form without a callback. Insert
            //a null so that the following concat is correct.
            if (typeof args[0] !== 'string' && args.length === 1) {
                args.push(null);
            }
            return req.apply(undef, args.concat([relName, forceSync]));
        };
    }

    function makeNormalize(relName) {
        return function (name) {
            return normalize(name, relName);
        };
    }

    function makeLoad(depName) {
        return function (value) {
            defined[depName] = value;
        };
    }

    function callDep(name) {
        if (hasProp(waiting, name)) {
            var args = waiting[name];
            delete waiting[name];
            defining[name] = true;
            main.apply(undef, args);
        }

        if (!hasProp(defined, name) && !hasProp(defining, name)) {
            throw new Error('No ' + name);
        }
        return defined[name];
    }

    //Turns a plugin!resource to [plugin, resource]
    //with the plugin being undefined if the name
    //did not have a plugin prefix.
    function splitPrefix(name) {
        var prefix,
            index = name ? name.indexOf('!') : -1;
        if (index > -1) {
            prefix = name.substring(0, index);
            name = name.substring(index + 1, name.length);
        }
        return [prefix, name];
    }

    /**
     * Makes a name map, normalizing the name, and using a plugin
     * for normalization if necessary. Grabs a ref to plugin
     * too, as an optimization.
     */
    makeMap = function (name, relName) {
        var plugin,
            parts = splitPrefix(name),
            prefix = parts[0];

        name = parts[1];

        if (prefix) {
            prefix = normalize(prefix, relName);
            plugin = callDep(prefix);
        }

        //Normalize according
        if (prefix) {
            if (plugin && plugin.normalize) {
                name = plugin.normalize(name, makeNormalize(relName));
            } else {
                name = normalize(name, relName);
            }
        } else {
            name = normalize(name, relName);
            parts = splitPrefix(name);
            prefix = parts[0];
            name = parts[1];
            if (prefix) {
                plugin = callDep(prefix);
            }
        }

        //Using ridiculous property names for space reasons
        return {
            f: prefix ? prefix + '!' + name : name, //fullName
            n: name,
            pr: prefix,
            p: plugin
        };
    };

    function makeConfig(name) {
        return function () {
            return (config && config.config && config.config[name]) || {};
        };
    }

    handlers = {
        require: function (name) {
            return makeRequire(name);
        },
        exports: function (name) {
            var e = defined[name];
            if (typeof e !== 'undefined') {
                return e;
            } else {
                return (defined[name] = {});
            }
        },
        module: function (name) {
            return {
                id: name,
                uri: '',
                exports: defined[name],
                config: makeConfig(name)
            };
        }
    };

    main = function (name, deps, callback, relName) {
        var cjsModule, depName, ret, map, i,
            args = [],
            callbackType = typeof callback,
            usingExports;

        //Use name if no relName
        relName = relName || name;

        //Call the callback to define the module, if necessary.
        if (callbackType === 'undefined' || callbackType === 'function') {
            //Pull out the defined dependencies and pass the ordered
            //values to the callback.
            //Default to [require, exports, module] if no deps
            deps = !deps.length && callback.length ? ['require', 'exports', 'module'] : deps;
            for (i = 0; i < deps.length; i += 1) {
                map = makeMap(deps[i], relName);
                depName = map.f;

                //Fast path CommonJS standard dependencies.
                if (depName === "require") {
                    args[i] = handlers.require(name);
                } else if (depName === "exports") {
                    //CommonJS module spec 1.1
                    args[i] = handlers.exports(name);
                    usingExports = true;
                } else if (depName === "module") {
                    //CommonJS module spec 1.1
                    cjsModule = args[i] = handlers.module(name);
                } else if (hasProp(defined, depName) ||
                           hasProp(waiting, depName) ||
                           hasProp(defining, depName)) {
                    args[i] = callDep(depName);
                } else if (map.p) {
                    map.p.load(map.n, makeRequire(relName, true), makeLoad(depName), {});
                    args[i] = defined[depName];
                } else {
                    throw new Error(name + ' missing ' + depName);
                }
            }

            ret = callback ? callback.apply(defined[name], args) : undefined;

            if (name) {
                //If setting exports via "module" is in play,
                //favor that over return value and exports. After that,
                //favor a non-undefined return value over exports use.
                if (cjsModule && cjsModule.exports !== undef &&
                        cjsModule.exports !== defined[name]) {
                    defined[name] = cjsModule.exports;
                } else if (ret !== undef || !usingExports) {
                    //Use the return value from the function.
                    defined[name] = ret;
                }
            }
        } else if (name) {
            //May just be an object definition for the module. Only
            //worry about defining if have a module name.
            defined[name] = callback;
        }
    };

    requirejs = require = req = function (deps, callback, relName, forceSync, alt) {
        if (typeof deps === "string") {
            if (handlers[deps]) {
                //callback in this case is really relName
                return handlers[deps](callback);
            }
            //Just return the module wanted. In this scenario, the
            //deps arg is the module name, and second arg (if passed)
            //is just the relName.
            //Normalize module name, if it contains . or ..
            return callDep(makeMap(deps, callback).f);
        } else if (!deps.splice) {
            //deps is a config object, not an array.
            config = deps;
            if (config.deps) {
                req(config.deps, config.callback);
            }
            if (!callback) {
                return;
            }

            if (callback.splice) {
                //callback is an array, which means it is a dependency list.
                //Adjust args if there are dependencies
                deps = callback;
                callback = relName;
                relName = null;
            } else {
                deps = undef;
            }
        }

        //Support require(['a'])
        callback = callback || function () {};

        //If relName is a function, it is an errback handler,
        //so remove it.
        if (typeof relName === 'function') {
            relName = forceSync;
            forceSync = alt;
        }

        //Simulate async callback;
        if (forceSync) {
            main(undef, deps, callback, relName);
        } else {
            //Using a non-zero value because of concern for what old browsers
            //do, and latest browsers "upgrade" to 4 if lower value is used:
            //http://www.whatwg.org/specs/web-apps/current-work/multipage/timers.html#dom-windowtimers-settimeout:
            //If want a value immediately, use require('id') instead -- something
            //that works in almond on the global level, but not guaranteed and
            //unlikely to work in other AMD implementations.
            setTimeout(function () {
                main(undef, deps, callback, relName);
            }, 4);
        }

        return req;
    };

    /**
     * Just drops the config on the floor, but returns req in case
     * the config return value is used.
     */
    req.config = function (cfg) {
        return req(cfg);
    };

    /**
     * Expose module registry for debugging and tooling
     */
    requirejs._defined = defined;

    define = function (name, deps, callback) {
        if (typeof name !== 'string') {
            throw new Error('See almond README: incorrect module build, no module name');
        }

        //This module may not have dependencies
        if (!deps.splice) {
            //deps is not an array, so probably means
            //an object literal or factory function for
            //the value. Adjust args.
            callback = deps;
            deps = [];
        }

        if (!hasProp(defined, name) && !hasProp(waiting, name)) {
            waiting[name] = [name, deps, callback];
        }
    };

    define.amd = {
        jQuery: true
    };
}());

define("../lib/almond", function(){});

define( 'models/whenModel',[], function() {
	var model = Backbone.Model.extend( {
		initialize: function( models, options ) {
			/*
			 * If our key or comparator is empty, don't do anything else.
			 */
			if ( ! this.get( 'key' ) || ! this.get( 'comparator' ) ) return;

			/*
			 * Our key could be a field or a calc.
			 * We need to setup a listener on either the field or calc model for changes.
			 */
			if ( 'calc' == this.get( 'type' ) ) { // We have a calculation key
				/*
				 * Get our calc model
				 */
				var calcModel = nfRadio.channel( 'form-' + this.collection.options.condition.collection.formModel.get( 'id' ) ).request( 'get:calc', this.get( 'key' ) );
				/*
				 * When we update our calculation, update our compare
				 */
				this.listenTo( calcModel, 'change:value', this.updateCalcCompare );
				/*
				 * Update our compare status.
				 */
				this.updateCalcCompare( calcModel );
			} else { // We have a field key
				// Get our field model
				var fieldModel = nfRadio.channel( 'form-' + options.condition.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', this.get( 'key' ) );

				if( 'undefined' == typeof fieldModel ) return;

				// When we change the value of our field, update our compare status.
				fieldModel.on( 'change:value', this.updateFieldCompare, this );
				// When we keyup in our field, maybe update our compare status.
				this.listenTo( nfRadio.channel( 'field-' + fieldModel.get( 'id' ) ), 'keyup:field', this.maybeupdateFieldCompare );
				// Update our compare status.
				this.updateFieldCompare( fieldModel );

				/*
				 * TODO: This should be moved to the show_field/hide_field file because it is specific to showing and hiding.
				 * Create a radio message here so that the specific JS file can hook into whenModel init.
				 */
				fieldModel.on( 'change:visible', this.updateFieldCompare, this );
			}
		},

		updateCalcCompare: function( calcModel ) {
			this.updateCompare( calcModel.get( 'value' ) );
		},

		maybeupdateFieldCompare: function( el, fieldModel, keyCode ) {
			if( 'checkbox' == fieldModel.get( 'type' ) ){
                var fieldValue = ( 'checked' == jQuery( el ).attr( 'checked' ) ) ? 1 : 0;
            } else if( 'listcheckbox' == fieldModel.get( 'type' ) ) {
				// This field isn't a single element, so we need to reference the fieldModel, instead of the DOM.
                var fieldValue = fieldModel.get( 'value' ).join();
            } else if ( 'date' == fieldModel.get ('type' ) ) {
				var fieldValue = fieldModel.get( 'value' );

				if ( _.isEmpty( fieldValue ) ) {
					fieldValue = '1970/01/01';
				}

				let date_mode = fieldModel.get( 'date_mode' );
				if ( 'undefined' == typeof date_mode ) { // If 'date_mode' is undefined, then we assume it's date_only.
					date_mode = 'date_only';
				}
				let date = 0;
				// If we're in time_only mode, then we need to use 1970-01-01 as our date.
				if ( 'time_only' == fieldModel.get( 'date_mode' ) ) {
					date = '1970/01/01';
				} else {
					date = fieldValue;
				}

				// Convert field value into a timestamp
				let hour = fieldModel.get( 'selected_hour' );
				if ( 'undefined' == typeof hour ) {
					hour = '00';
				}

				let minute = fieldModel.get( 'selected_minute' );
				if ( 'undefined' == typeof minute ) {
					minute = '00';
				}

				// If we have a date_and_time field, but we haven't selected a date yet, we don't need to compare.
				if ( 'date_and_time' == date_mode && '1970/01/01' == date ) {
					fieldValue = false;
				} else {
					fieldValue = date + ' ' + hour + ':' + minute + ' UT';

					let dateObject = new Date( fieldValue );
					fieldValue = Math.floor( dateObject.getTime() / 1000 );					
				}
			} else {
				var fieldValue = jQuery( el ).val();
			}

			this.updateFieldCompare( fieldModel, null, fieldValue );
		},

		updateCompare: function( value ) {
			var this_val = this.get( 'value' );

			// if this is a calcModel then let's convert to number for comparison
			if ( 'calc' === this.get( 'type' ) ) {
				this_val = Number( this_val );
				value = Number( value );
			}
			// Check to see if the value of the field model value COMPARATOR the value of our when condition is true.
			var status = this.compareValues[ this.get( 'comparator' ) ]( value, this_val );
			this.set( 'status', status );
		},

		updateFieldCompare: function( fieldModel, val, fieldValue ) {
			if ( _.isEmpty( fieldValue ) ) {
				fieldValue = fieldModel.get( 'value' );
			}

			// Change the value of checkboxes to match the new convention.
			if( 'checkbox' == fieldModel.get( 'type' ) ) {
				if( 0 == fieldValue ) {
					fieldValue = 'unchecked';
				} else {
					fieldValue = 'checked';
				}
			} else if ( 'date' == fieldModel.get( 'type' ) ) {
				if ( _.isEmpty( fieldValue ) ) {
					fieldValue = '1970/01/01';
				}

				let date_mode = fieldModel.get( 'date_mode' );
				if ( 'undefined' == typeof date_mode ) { // If 'date_mode' is undefined, then we assume it's date_only.
					date_mode = 'date_only';
				}
				let date = 0;
				// If we're in time_only mode, then we need to use 1970-01-01 as our date.
				if ( 'time_only' == fieldModel.get( 'date_mode' ) ) {
					date = '1970/01/01';
				} else {
					date = fieldValue;
				}

				// Convert field value into a timestamp
				let hour = fieldModel.get( 'selected_hour' );
				if ( 'undefined' == typeof hour ) {
					hour = '00';
				}

				let ampm = fieldModel.get( 'selected_ampm' );
				if ( 'undefined' != typeof ampm ) {
					// Convert our hour into 24 hr format.
					if ( 'pm' == ampm && '12' != hour ) {
						hour = parseInt( hour ) + 12;
					} else if ( 'am' == ampm && '12' == hour ) {
						hour = '00';
					}
				}

				let minute = fieldModel.get( 'selected_minute' );
				if ( 'undefined' == typeof minute ) {
					minute = '00';
				}

				// If we have a date_and_time field, but we haven't selected a date yet, we don't need to compare.
				if ( 'date_and_time' == date_mode && '1970/01/01' == date ) {
					fieldValue = false;
				} else {
					fieldValue = date + ' ' + hour + ':' + minute + ' UT';

					let dateObject = new Date( fieldValue );
					fieldValue = Math.floor( dateObject.getTime() / 1000 );					
				}
			}

			this.updateCompare( fieldValue );

			/*
			 * TODO: This should be moved to the show_field/hide_field file because it is specific to showing and hiding.
			 */
			if ( ! fieldModel.get( 'visible' ) ) {
				this.set( 'status', false );
			}			
		},

		compareValues: {
			'equal': function( a, b ) {
				return a == b;
			},
			'notequal': function( a, b ) {
				return a != b;
			},
			'contains': function( a, b ) {
				if ( jQuery.isArray( a ) ) {
					/*
					 * If a is an array, then we're searching for an index.
					 */
					return a.indexOf( b ) >= 0;
				} else {
					/*
					 * If a is a string, then we're searching for a string position.
					 *
					 * If our b value has quotes in it, we want to find that exact word or phrase.
					 */
					if ( b.indexOf( '"' ) >= 0 ) {
						b = b.replace( /['"]+/g, '' );
						return new RegExp("\\b" + b + "\\b").test( a );
					}
					return a.toLowerCase().indexOf( b.toLowerCase() ) >= 0; 				
				}
			},
			'notcontains': function( a, b ) {
				return ! this.contains( a, b );
			},
			'greater': function( a, b ) {
				/*
				 * In 2.9.x, you could use the greater and less like string count.
				 * i.e. if textbox > (empty string) do something.
				 * This recreates that ability.
				 */
				if ( jQuery.isNumeric( b ) ) {
					return parseFloat( a ) > parseFloat( b );
				} else if ( 'string' == typeof a ) {
					return 0 < a.length;
				}
				
			},
			'less': function( a, b ) {
				/*
				 * In 2.9.x, you could use the greater and less like string count.
				 * i.e. if textbox > (empty string) do something.
				 * This recreates that ability.
				 */
				if ( jQuery.isNumeric( b ) ) {
					return parseFloat( a ) < parseFloat( b );
				} else if ( 'string' == typeof a ) {
					return 0 >= a.length;
				}
		
			},
			'greaterequal': function( a, b ) {
				return parseFloat( a ) > parseFloat( b ) || parseFloat( a ) == parseFloat( b );
			},
			'lessequal': function( a, b ) {
				return parseFloat( a ) < parseFloat( b ) || parseFloat( a ) == parseFloat( b );
			}
		} 
	} );
	
	return model;
} );
define( 'models/whenCollection',['models/whenModel'], function( WhenModel ) {
	var collection = Backbone.Collection.extend( {
		model: WhenModel,

		initialize: function( models, options ) {
			this.options = options;
		}
	} );
	return collection;
} );
define( 'models/conditionModel',[ 'models/whenCollection' ], function( WhenCollection ) {
	var model = Backbone.Model.extend( {
		initialize: function( options ) {
			/*
			 * Our "when" statement will be like:
			 * When field1 == value
			 * AND field2 == value
			 *
			 * We need to create a collection out of this when statement, with each row as a model.
			 */
			this.set( 'when', new WhenCollection( this.get( 'when' ), { condition: this } ) );
			/*
			 * When we update any of our "when" models' status, check to see if we should send a message.
			 */
			this.get( 'when' ).on( 'change:status', this.checkWhen, this );
			/*
			 * Check our initial status;
			 */
			this.checkWhen();
		},

		checkWhen: function() {
			/*
			 * If we have any OR connectors, then any status being true should trigger pass.
			 * Otherwise, we need every status to be true.
			 */
			var statusResults = this.get( 'when' ).pluck( 'status' );

			var connectors = this.get( 'when' ).pluck( 'connector' );
			var allAND = _.every( _.values( connectors ), function( v ) { return v == 'AND' }, this );
			if ( allAND ) {
				var status = _.every( _.values( statusResults ), function(v) { return v; }, this );
			} else {
				var status = _.some( _.values( statusResults ), function(v) { return v; }, this );
			}

			if ( status ) {
			   	/*
			 	 * Send out a request for each of our "then" statements.
			 	 */
				_.each( this.get( 'then' ), function( then, index ) {
					nfRadio.channel( 'condition:trigger' ).request( then.trigger, this, then );
				}, this );			 
			} else {
				/*
				 * Send out a request for each of our "else" statements.
				 */
				_.each( this.get( 'else' ), function( elseData, index ) {
					nfRadio.channel( 'condition:trigger' ).request( elseData.trigger, this, elseData );
				}, this );
			}
		}
	} );
	
	return model;
} );
define( 'models/conditionCollection',['models/conditionModel'], function( ConditionModel ) {
	var collection = Backbone.Collection.extend( {
		model: ConditionModel,

		initialize: function( models, options ) {
			this.formModel = options.formModel;
		}
	} );
	return collection;
} );
/**
 * Initialise condition collection
 * 
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/initCollection',[ 'models/conditionCollection' ], function( ConditionCollection ) {
	var controller = Marionette.Object.extend( {
		initialize: function( formModel ) {
			this.collection = new ConditionCollection( formModel.get( 'conditions' ), { formModel: formModel } );
            this.listenTo(nfRadio.channel('fields'), 'reset:collection', this.resetCollection);
		},
        resetCollection: function( fieldsCollection ) {
            var formModel = fieldsCollection.options.formModel;
            this.collection = new ConditionCollection( formModel.get( 'conditions' ), { formModel: formModel } );
        }
	});

	return controller;
} );
/**
 * Handle showing/hiding fields
 * 
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/showHide',[], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'condition:trigger' ).reply( 'hide_field', this.hideField, this );
			nfRadio.channel( 'condition:trigger' ).reply( 'show_field', this.showField, this );
		},

		hideField: function( conditionModel, then ) {
			var targetFieldModel = nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', then.key );

			if( 'undefined' == typeof targetFieldModel ) return;
			targetFieldModel.set( 'visible', false );
            if ( ! targetFieldModel.get( 'clean' ) ) {
				targetFieldModel.trigger( 'change:value', targetFieldModel );
			}
			
			nfRadio.channel( 'fields' ).request( 'remove:error', targetFieldModel.get( 'id' ), 'required-error' );
		},

		showField: function( conditionModel, then ) {
			var targetFieldModel = nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', then.key );
			//TODO: Add an error to let the user know the show/hide field is empty.
			if( 'undefined' == typeof targetFieldModel ) return;
			targetFieldModel.set( 'visible', true );
            if ( ! targetFieldModel.get( 'clean' ) ) {
                targetFieldModel.trigger( 'change:value', targetFieldModel );
			}
			if ( 'recaptcha' === targetFieldModel.get( 'type' ) ) {
				this.renderRecaptcha();
			}
			var viewEl = { el: nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:el' ) };
            nfRadio.channel( 'form' ).request( 'init:help', viewEl );
		},

		renderRecaptcha: function() {
			jQuery( '.g-recaptcha' ).each( function() {
                var callback = jQuery( this ).data( 'callback' );
                var fieldID = jQuery( this ).data( 'fieldid' );
                if ( typeof window[ callback ] !== 'function' ){
                    window[ callback ] = function( response ) {
                        nfRadio.channel( 'recaptcha' ).request( 'update:response', response, fieldID );
                    };
                }
				var opts = {
					theme: jQuery( this ).data( 'theme' ),
					sitekey: jQuery( this ).data( 'sitekey' ),
					callback: callback
				};
				
				grecaptcha.render( jQuery( this )[0], opts );
			} );
		}
	});

	return controller;
} );
/**
 * Setting/unsetting required.
 * 
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2019 WP Ninjas
 * @since 3.0
 */
define( 'controllers/changeRequired',[], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'condition:trigger' ).reply( 'set_required', this.setRequired, this );
			nfRadio.channel( 'condition:trigger' ).reply( 'unset_required', this.unsetRequired, this );
		},

		setRequired: function( conditionModel, then ) {
			var targetFieldModel = nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', then.key );

			if( 'undefined' == typeof targetFieldModel ) return;
            targetFieldModel.set( 'required', 1 );
			targetFieldModel.trigger( 'reRender', targetFieldModel );
		},

		unsetRequired: function( conditionModel, then ) {
			var targetFieldModel = nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', then.key );

			if( 'undefined' == typeof targetFieldModel ) return;
            targetFieldModel.set( 'required', 0 );
            targetFieldModel.trigger( 'reRender', targetFieldModel );
            // Ensure we resolve any errors when the field is no longer required.
			nfRadio.channel( 'fields' ).request( 'remove:error', targetFieldModel.get( 'id' ), 'required-error' );
        }
        
	});

	return controller;
} );
/**
 * Handle adding or removing an option from our list
 * 
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/showHideOption',[], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'condition:trigger' ).reply( 'show_option', this.showOption, this );

			nfRadio.channel( 'condition:trigger' ).reply( 'hide_option', this.hideOption, this );
		},

		showOption: function( conditionModel, then ) {
			var option = this.getOption( conditionModel, then );
			option.visible = true;
			this.updateFieldModel( conditionModel, then );
		},

		hideOption: function( conditionModel, then ) {
			var option = this.getOption( conditionModel, then );
			option.visible = false;
			this.updateFieldModel( conditionModel, then );
		},

		getFieldModel: function( conditionModel, then ) {
			return nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', then.key );
		},

		getOption: function( conditionModel, then ) {
			var targetFieldModel = this.getFieldModel( conditionModel, then );
			var options = targetFieldModel.get( 'options' );
			return _.find( options, function( option ) { return option.value == then.value } );
		},

		updateFieldModel: function( conditionModel, then ) {
			var targetFieldModel = this.getFieldModel( conditionModel, then );
			var options = targetFieldModel.get( 'options' );
			targetFieldModel.set( 'options', options );
			targetFieldModel.trigger( 'reRender' );
		}
	});

	return controller;
} );
/**
 * Handle changing a field's value
 * 
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/changeValue',[], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'condition:trigger' ).reply( 'change_value', this.changeValue, this );
		},

		changeValue: function( conditionModel, then ) {
			var targetFieldModel = nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', then.key );
			/*
			 * If we have a checkbox then we need to change the value that is set
			 * of the then variable to a 1 or 0 to re-render on the front end when
			 * the condition is met.
			 */
			if( 'checkbox' == targetFieldModel.get( 'type' ) ) {
				// We also need to do the opposite of the value that is in the changed model.
				if( 'unchecked' == targetFieldModel.changed.value ) {
					then.value = 1;
                } else if( 'checked' == targetFieldModel ) {
					then.value = 0;
				}
			}
            /*
             * Change the value of our field model, and then trigger a re-render of its view.
             */
			targetFieldModel.set( 'value', then.value );
			targetFieldModel.trigger( 'reRender', targetFieldModel );
		},

	});
	return controller;
} );
/**
 * Handle selecting/deselecting list options
 * 
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/selectDeselect',[], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'condition:trigger' ).reply( 'select_option', this.selectOption, this );

			nfRadio.channel( 'condition:trigger' ).reply( 'deselect_option', this.deselectOption, this );
		},

		selectOption: function( conditionModel, then ) {
			/*
			 * Get our field model and set this option's "selected" property to 1
			 */
			var targetFieldModel = nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', then.key );


			if( _.contains( [ 'listselect', 'listcountry', 'liststate' ], targetFieldModel.get( 'type' ) ) ) { // TODO: Make this more dynamic.
				targetFieldModel.set('clean', false); // Allows for changes to default values.
			}

			var options = targetFieldModel.get( 'options' );

			var option = _.find( options, { value: then.value } );
			option.selected = 1;

			targetFieldModel.set( 'options', options );

			if( _.contains( [ 'listselect', 'listcountry', 'liststate' ], targetFieldModel.get( 'type' ) ) ) { // TODO: Make this more dynamic.
				targetFieldModel.set( 'value', option.value ); // Propagate the selected option to the model's value.
			} else {
				var value = targetFieldModel.get( 'value' );
				if ( _.isArray( value ) ) {
                    if ( 0 > value.indexOf( option.value ) ) {
                        value.push( option.value );
                        // Set the value to nothing so it knows that something has changed.
                        targetFieldModel.set( 'value', '' );
                    }
				} else {
					value = option.value;
				}
				
				targetFieldModel.set( 'value', value ); // Propagate the selected option to the model's value.
			}

			/*
			 * Re render our field
			 */
			targetFieldModel.trigger( 'reRender', targetFieldModel );
		},

		deselectOption: function( conditionModel, then ) {
			/*
			 * When we are trying to deselect our option, we need to change it's "selected" property to 0 AND change its value.
			 */
			var targetFieldModel = nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', then.key );

			/*
			 * Set "selected" to 0.
			 */
			var options = targetFieldModel.get( 'options' );
			var option = _.find( options, { value: then.value } );
			option.selected = 0;
			targetFieldModel.set( 'options', options );

			/*
			 * Update our value
			 */
			var currentValue = targetFieldModel.get( 'value' );
			if ( _.isArray( currentValue ) ) {
				currentValue = _.without( currentValue, then.value );
			} else {
				currentValue = '';
			}
			targetFieldModel.set( 'value', currentValue );

			/*
			 * Re render our field
			 */
			targetFieldModel.trigger( 'reRender', targetFieldModel );
		},

	});

	return controller;
} );
/**
 * Keep an internal record for which actions are active and deactive.
 * 
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/actions',[], function() {
	var controller = Marionette.Object.extend( {
		actions: {},
		
		initialize: function() {
			/*
			 * Listen for activate/deactivate action messages.
			 */
			nfRadio.channel( 'condition:trigger' ).reply( 'activate_action', this.activateAction, this );
			nfRadio.channel( 'condition:trigger' ).reply( 'deactivate_action', this.deactivateAction, this );
		
			/*
			 * Reply to requests for action status.
			 */
			nfRadio.channel( 'actions' ).reply( 'get:status', this.getStatus, this );
		},

		activateAction: function( conditionModel, thenObject ) {
			this.actions[ thenObject.key ] = true;
			console.log( 'activate action' );
		},

		deactivateAction: function( conditionModel, thenObject ) {
			console.log( 'deactivate action' );
			this.actions[ thenObject.key ] = false;
		},

		getStatus: function( $id ) {
			return this.actions[ $id ];
		}
	});

	return controller;
} );
var nfRadio = Backbone.Radio;

require( [ 'controllers/initCollection', 'controllers/showHide', 'controllers/changeRequired', 'controllers/showHideOption', 'controllers/changeValue', 'controllers/selectDeselect', 'controllers/actions' ], function( InitCollection, ShowHide, ChangeRequired, ShowHideOption, ChangeValue, SelectDeselect, Actions ) {

	var NFConditionalLogic = Marionette.Application.extend( {

		initialize: function( options ) {
			this.listenTo( nfRadio.channel( 'form' ), 'after:loaded', this.loadControllers );
		},

		loadControllers: function( formModel ) {
			new ShowHide();
			new ChangeRequired();
			new ShowHideOption();
			new ChangeValue();
			new SelectDeselect();
			new Actions();
			new InitCollection( formModel );
		},

		onStart: function() {
			
		}
	} );

	var nfConditionalLogic = new NFConditionalLogic();
	nfConditionalLogic.start();
} );
define("main", function(){});

}());
//# sourceMappingURL=front-end.js.map
