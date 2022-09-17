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

/**
 * Adds template helpers for the fields conditional logic setting type
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/templateHelpers',[], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'conditions' ), 'init:model', this.addTemplateHelpers );
			this.listenTo( nfRadio.channel( 'conditions' ), 'init:thenModel', this.addTemplateHelpers );
			this.listenTo( nfRadio.channel( 'conditions' ), 'init:whenModel', this.addTemplateHelpers );
			this.listenTo( nfRadio.channel( 'conditions' ), 'init:elseModel', this.addTemplateHelpers );
			
		},

		addTemplateHelpers: function( model ) {
			model.set( 'renderKeySelect', this.renderKeySelect );
			model.set( 'renderComparators', this.renderComparators );
			model.set( 'renderTriggers', this.renderTriggers );
			model.set( 'renderWhenValue', this.renderWhenValue );
			model.set( 'renderItemValue', this.renderItemValue );
		},

		renderKeySelect: function( currentValue, modelType ) {
			
			var groups = []

			var fieldCollection = nfRadio.channel( 'fields' ).request( 'get:collection' );
			var fieldOptions = _.chain( fieldCollection.models )
				.filter( function( field ) { return ! nfRadio.channel( 'conditions-key-select-field-' + field.get( 'type' ) ).request( 'hide', modelType ) || false; })
				.filter( function( field )  {

					// filter out these fields for the when condition
					var notForWhen = [ 'submit', 'hr', 'html', 'save', 'file-upload', 'password', 'passwordconfirm', 'product' ];
					
					if( field.get( 'key' ) === currentValue ) {
						notForWhen = notForWhen.splice( notForWhen.indexOf( field.get( 'type' ), 1) );
					}

					if( notForWhen.includes( field.get( 'type' ) ) && 'when' === modelType ) {
						return false;
					}

					return true;
				})
				.map( function( field ) {
                    var label = field.get( 'label' )
					if( 'undefined' !== typeof field.get( 'admin_label' ) && 0 < field.get( 'admin_label' ).length ){
                    	label = field.get( 'admin_label' );
					}
					return { key: field.get( 'key' ), label: label }; }
				)
				.sortBy( function( field ){
					return field.label.toLowerCase();
				} )
				.value();
				
			groups.push( { label: 'Fields', type: 'field', options: fieldOptions } );
			
			var calcCollection = nfRadio.channel( 'settings' ).request( 'get:setting', 'calculations' );

			/*
			 * If we are working on a 'when' model and we have calculations, add them to our select options.
			 */
			if ( 'when' == modelType && 0 < calcCollection.length ) {
				var calcOptions = calcCollection.map( function( calc ) {
					return { key: calc.get( 'name' ), label: calc.get( 'name' ) };
				} );

				groups.push( { label: 'Calculations', type: 'calc', options: calcOptions } );
			}

			/*
			 * Pass our groups through any 'when/then' group filters we have.
			 */
			var filters = nfRadio.channel( 'conditions' ).request( 'get:groupFilters' );
			_.each( filters, function( filter ) {
				groups = filter( groups, modelType );
			} );

			/*
			 * Use a template to get our field select
			 */
			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-key-select' );

			var tmp = template( { groups: groups, currentValue: currentValue } );
			return tmp;
		},

		renderComparators: function( type, key, currentComparator ) {
			var defaultComparators = {
				equal: {
					label: nfcli18n.templateHelperEquals,
					value: 'equal'
				},

				notequal: {
					label: nfcli18n.templateHelperDoesNotEqual,
					value: 'notequal'
				},

				contains: {
					label: nfcli18n.templateHelperContains,
					value: 'contains'
				},

				notcontains: {
					label: nfcli18n.templateHelperDoesNotContain,
					value: 'notcontains'
				},

				greater: {
					label: nfcli18n.templateHelperGreaterThan,
					value: 'greater'
				},

				less: {
					label: nfcli18n.templateHelperLessThan,
					value: 'less'
				}
			};

			if ( key ) {
				/*
				 * This could be a field or a calculation key. If it's a calc key, get the calc model.
				 */
				if ( 'calc' == type ) {
					var comparators = _.omit( defaultComparators, 'contains', 'notcontains' );
					_.extend( comparators, {
						lessequal: {
							label: nfcli18n.templateHelperLessThanOrEqual,
							value: 'lessequal'
						},

						greaterequal: {
							label: nfcli18n.templateHelperGreaterThanOrEqual,
							value: 'greaterequal'
						}
					} );
				} else {
					/*
					 * Send out a radio request for an html value on a channel based upon the field type.
					 *
					 * Get our field by key
					 * Get our field type model
					 *
					 * Send out a message on the type channel
					 * If we don't get a response, send a message out on the parent type channel
					 */
					var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', key );

					if( fieldModel ) {
						var comparators = nfRadio.channel('conditions-' + fieldModel.get('type')).request('get:comparators', defaultComparators, currentComparator );
						if (!comparators) {
							var typeModel = nfRadio.channel('fields').request('get:type', fieldModel.get('type'));
							comparators = nfRadio.channel('conditions-' + typeModel.get('parentType')).request('get:comparators', defaultComparators, currentComparator ) || defaultComparators;
						}
					} else {
						var comparators = defaultComparators;
					}
				}
			} else {
				var comparators = defaultComparators;
			}

			/*
			 * Use a template to get our comparator select
			 */
			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-comparators' );
			return template( { comparators: comparators, currentComparator: currentComparator } );
		},

		renderTriggers: function( type, key, currentTrigger, value ) {
			var defaultTriggers = {
				show_field: {
					label: nfcli18n.templateHelperShowField,
					value: 'show_field'
				},

				hide_field: {
					label: nfcli18n.templateHelperHideField,
					value: 'hide_field'
				},

				change_value: {
					label: nfcli18n.templateHelperChangeValue,
					value: 'change_value'
				},

				set_required: {
					label: nfcli18n.templateHelperSetRequired,
					value: 'set_required'
				},

				unset_required: {
					label: nfcli18n.templateHelperUnsetRequired,
					value: 'unset_required'
				}
			};

			if ( key && 'field' == type ) {
				/*
				 * Send out a radio request for an html value on a channel based upon the field type.
				 *
				 * Get our field by key
				 * Get our field type model
				 *
				 * Send out a message on the type channel
				 * If we don't get a response, send a message out on the parent type channel
				 */
				var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', key );

				if( 'undefined' != typeof fieldModel ) {
					var typeModel = nfRadio.channel('fields').request('get:type', fieldModel.get('type'));

					var triggers = nfRadio.channel('conditions-' + fieldModel.get('type')).request('get:triggers', defaultTriggers);
					if (!triggers) {
						triggers = nfRadio.channel('conditions-' + typeModel.get('parentType')).request('get:triggers', defaultTriggers) || defaultTriggers;
					}
				} else {
					var triggers = nfRadio.channel( 'conditions-' + type ).request( 'get:triggers', defaultTriggers ) || defaultTriggers;
				}
			} else {
				var triggers = nfRadio.channel( 'conditions-' + type ).request( 'get:triggers', defaultTriggers ) || defaultTriggers;
			}


			/*
			 * Use a template to get our comparator select
			 */
			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-triggers' );
			return template( { triggers: triggers, currentTrigger: currentTrigger } );
		},

		renderWhenValue: function( type, key, comparator, value ) {
			/*
			 * Use a template to get our value
			 */
			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-value-default' );
			var defaultHTML = template( { value: value } );

			/*
			 * If we have a key and it's not a calc, get our field type based HTML.
			 */
			if ( key && 'calc' != type ) {
				/*
				 * Send out a radio request for an html value on a channel based upon the field type.
				 *
				 * Get our field by key
				 * Get our field type model
				 *
				 * Send out a message on the type channel
				 * If we don't get a response, send a message out on the parent type channel
				 */
				var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', key );

				if( fieldModel ) {
					var html = nfRadio.channel('conditions-' + fieldModel.get('type')).request('get:valueInput', key, comparator, value);
					if (!html) {
						var typeModel = nfRadio.channel('fields').request('get:type', fieldModel.get('type'));
						html = nfRadio.channel('conditions-' + typeModel.get('parentType')).request('get:valueInput', key, comparator, value) || defaultHTML;
					}
				} else {
					html = defaultHTML;
				}
			} else {
				var html = defaultHTML;
			}
			
			return html;
		},

		renderItemValue: function( key, trigger, value ) {
			/*
			 * Use a template to get our value
			 *
			 * TODO: This should be much more dynamic.
			 * At the moment, we manually check to see if we are doing a "change_value" or similar trigger.
			 */
			if ( trigger != 'change_value'
				&& trigger != 'select_option'
				&& trigger != 'deselect_option'
				&& trigger != 'show_option'
				&& trigger != 'hide_option' 
			) {
				return '';
			}

			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-value-default' );
			var defaultHTML = template( { value: value } );

			if ( key ) {
				/*
				 * Send out a radio request for an html value on a channel based upon the field type.
				 *
				 * Get our field by key
				 * Get our field type model
				 *
				 * Send out a message on the type channel
				 * If we don't get a response, send a message out on the parent type channel
				 */
				var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', key );

				if( 'undefined' != typeof fieldModel ) {
					var typeModel = nfRadio.channel('fields').request('get:type', fieldModel.get('type'));
					var html = nfRadio.channel('conditions-' + fieldModel.get('type')).request('get:valueInput', key, trigger, value);
					if (!html) {
						html = nfRadio.channel('conditions-' + typeModel.get('parentType')).request('get:valueInput', key, trigger, value) || defaultHTML;
					}
				}
			} else {
				var html = defaultHTML;
			}

			return html;
		}
	});

	return controller;
} );

/**
 * Item view for our condition and
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'views/advanced/whenItem',[], function( ) {
	var view = Marionette.ItemView.extend({
		template: "#tmpl-nf-cl-advanced-when-item",

		initialize: function() {
			this.listenTo( this.model, 'change', this.render );
		},

		onRender: function() {
			let el = jQuery( this.el ).find( '[data-type="date"]' );
			jQuery( el ).mask( '9999-99-99' );
		},
		
		events: {
			'change .setting': 'changeSetting',
			'click .nf-remove-when': 'clickRemove'
		},

		changeSetting: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'change:setting', e, this.model )
		},

		clickRemove: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:removeWhen', e, this.model );
		}
	});

	return view;
} );
/**
 * Item view for our condition's first when
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'views/advanced/firstWhenItem',[], function( ) {
	var view = Marionette.ItemView.extend({
		template: "#tmpl-nf-cl-advanced-first-when-item",
		
		initialize: function() {
			this.listenTo( this.model, 'change', this.render );
		},

		onRender: function() {
			let el = jQuery( this.el ).find( '[data-type="date"]' );
			jQuery( el ).mask( '9999-99-99' );
		},

		events: {
			'change .setting': 'changeSetting',
			'change .extra': 'changeExtra',
		},

		changeSetting: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'change:setting', e, this.model );
		},

		changeExtra: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'change:extra', e, this.model );
		}
	});

	return view;
} );
/**
 * Collection view for our when collection
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'views/advanced/whenCollection',[ 'views/advanced/whenItem', 'views/advanced/firstWhenItem' ], function( WhenItem, FirstWhenItem ) {
	var view = Marionette.CollectionView.extend({
		getChildView: function( item ) {
			if ( item.collection.first() == item ) {
				return FirstWhenItem;
			} else {
				return WhenItem;
			}
			
		},

		initialize: function( options ) {
			this.firstWhenDiv = options.firstWhenDiv;
			this.conditionModel = options.conditionModel;
		},

    	// The default implementation:
	  	attachHtml: function( collectionView, childView, index ) {
		  	if ( 0 == index ) {
		  		this.firstWhenDiv.append( childView.el );
		  	} else {
		  		if ( ! this.conditionModel.get( 'collapsed' ) ) {
				    if (collectionView.isBuffering) {
				    	// buffering happens on reset events and initial renders
				    	// in order to reduce the number of inserts into the
				    	// document, which are expensive.
				    	collectionView._bufferedChildren.splice(index, 0, childView);
				    } else {
						// If we've already rendered the main collection, append
						// the new child into the correct order if we need to. Otherwise
						// append to the end.
						if (!collectionView._insertBefore(childView, index)){
							collectionView._insertAfter(childView);
						}
				    }			  			
		  		}
		  	}
	  	},

	} );

	return view;
} );
/**
 * Item view for our condition then
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'views/advanced/thenItem',[], function( ) {
	var view = Marionette.ItemView.extend({
		template: "#tmpl-nf-cl-trigger-item",

		initialize: function() {
			this.listenTo( this.model, 'change', this.render );
		},

		events: {
			'change .setting': 'changeSetting',
			'click .nf-remove-then': 'clickRemove'
		},

		changeSetting: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'change:setting', e, this.model );
			nfRadio.channel( 'conditions' ).trigger( 'change:then', e, this.model );
		},

		clickRemove: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:removeThen', e, this.model );
		}
	});

	return view;
} );
/**
 * Collection view for our then statements
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'views/advanced/thenCollection',[ 'views/advanced/thenItem' ], function( ThenItem ) {
	var view = Marionette.CollectionView.extend({
		childView: ThenItem,

		initialize: function( options ) {

		}
	});

	return view;
} );
/**
 * Item view for our condition then
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'views/advanced/elseItem',[], function( ) {
	var view = Marionette.ItemView.extend({
		template: "#tmpl-nf-cl-trigger-item",

		initialize: function() {
			this.listenTo( this.model, 'change', this.render );
		},

		events: {
			'change .setting': 'changeSetting',
			'click .nf-remove-else': 'clickRemove'
		},

		changeSetting: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'change:setting', e, this.model )
		},

		clickRemove: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:removeElse', e, this.model );
		}
	});

	return view;
} );
/**
 * Collection view for our else statements
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'views/advanced/elseCollection',[ 'views/advanced/elseItem' ], function( ElseItem ) {
	var view = Marionette.CollectionView.extend({
		childView: ElseItem,

		initialize: function( options ) {

		}
	});

	return view;
} );
/**
 * Layout view for our conditions
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'views/advanced/conditionItem',[ 'views/advanced/whenCollection', 'views/advanced/thenCollection', 'views/advanced/elseCollection' ], function( WhenCollectionView, ThenCollectionView, ElseCollectionView ) {
	var view = Marionette.LayoutView.extend({
		template: "#tmpl-nf-cl-advanced-condition",
		regions: {
			'when': '.nf-when-region',
			'then': '.nf-then-region',
			'else': '.nf-else-region'
		},

		initialize: function() {
			/*
			 * When we change the "collapsed" attribute of our model, re-render.
			 */
			this.listenTo( this.model, 'change:collapsed', this.render );

			/*
			 * When our drawer closes, send out a radio message on our setting type channel.
			 */
			this.listenTo( nfRadio.channel( 'drawer' ), 'closed', this.drawerClosed );
		},

		onRender: function() {
			var firstWhenDiv = jQuery( this.el ).find( '.nf-first-when' );
			this.when.show( new WhenCollectionView( { collection: this.model.get( 'when' ), firstWhenDiv: firstWhenDiv, conditionModel: this.model } ) );
			if ( ! this.model.get( 'collapsed' ) ) {
				this.then.show( new ThenCollectionView( { collection: this.model.get( 'then' ) } ) );
				this.else.show( new ElseCollectionView( { collection: this.model.get( 'else' ) } ) );
			}
		},

		events: {
			'click .nf-remove-condition': 'clickRemove',
			'click .nf-collapse-condition': 'clickCollapse',
			'click .nf-add-when': 'clickAddWhen',
			'click .nf-add-then': 'clickAddThen',
			'click .nf-add-else': 'clickAddElse'
		},

		clickRemove: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:removeCondition', e, this.model );
		},

		clickCollapse: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:collapseCondition', e, this.model );
		},

		clickAddWhen: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:addWhen', e, this.model );
		},

		clickAddThen: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:addThen', e, this.model );
		},

		clickAddElse: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:addElse', e, this.model );
		}
	});

	return view;
} );
/**
 * Collection view for our conditions
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'views/advanced/conditionCollection',[ 'views/advanced/conditionItem' ], function( conditionItem ) {
	var view = Marionette.CollectionView.extend({
		childView: conditionItem,

		initialize: function( options ) {
			this.collection = options.dataModel.get( 'conditions' );
		},

        onShow: function() {
            /*
             * If we don't have any conditions, add an empty one as we render.
             */
            if ( 0 == this.collection.length ) {
                this.collection.add( {} );
            }
        },

        onBeforeDestroy: function() {
            /*
             * If we don't have any conditions or we have more than one, just return.
             */
            if ( 0 == this.collection.length || 1 < this.collection.length ) return;
            /*
             * If we only have one condition, and we didn't change the "key" attribute, reset our collection.
             * This empties it.
             */
            if ( '' == this.collection.models[0].get( 'when' ).models[0].get( 'key' ) ) {
                this.collection.reset();
            }
        }
	});

	return view;
} );

/**
 * Item view for our condition and
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'views/actions/whenItem',[], function( ) {
	var view = Marionette.ItemView.extend({
		template: "#tmpl-nf-cl-actions-condition-when",

		initialize: function() {
			this.listenTo( this.model, 'change', this.render );
		},
		
		events: {
			'change .setting': 'changeSetting',
			'click .nf-remove-when': 'clickRemove'
		},

		changeSetting: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'change:setting', e, this.model )
		},

		clickRemove: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:removeWhen', e, this.model );
		}
	});

	return view;
} );
/**
 * Collection view for our when collection
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'views/actions/whenCollection',[ 'views/actions/whenItem' ], function( WhenItem ) {
	var view = Marionette.CollectionView.extend({
		childView: WhenItem,

		initialize: function( options ) {

		},

        onShow: function() {
            /*
             * If we don't have any conditions, add an empty one as we render.
             */
            if ( 0 == this.collection.length ) {
                this.collection.add( {} );
            }
        },

        onBeforeDestroy: function() {
            /*
             * If we don't have any conditions or we have more than one, just return.
             */
            if ( 0 == this.collection.length || 1 < this.collection.length ) return;
            /*
             * If we only have one condition, and we didn't change the "key" attribute, reset our collection.
             * This empties it.
             */
            if ( '' == this.collection.models[0].get( 'key' ) ) {
                this.collection.reset();
            }
        }

	} );

	return view;
} );
/**
 * Layout view for our Action condition
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'views/actions/conditionLayout',[ 'views/actions/whenCollection' ], function( WhenCollection ) {
	var view = Marionette.LayoutView.extend( {
		template: '#tmpl-nf-cl-actions-condition-layout',

		regions: {
			'when': '.nf-when'
		},

		initialize: function( options ) {
			this.model = options.dataModel.get( 'conditions' );
			if ( ! options.dataModel.get( 'conditions' ) ) return;

			this.collection = options.dataModel.get( 'conditions' ).get( 'when' );
			this.conditionModel = options.dataModel.get( 'conditions' );
		},

		onRender: function() {
			if ( ! this.collection ) return;
			/*
			 * Show our "when" collection in the "when" area.
			 */
			this.when.show( new WhenCollection( { collection: this.collection } ) );
		},

		events: {
			'change .condition-setting': 'changeSetting',
			'click .nf-add-when': 'clickAddWhen'
		},

		clickAddWhen: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:addWhen', e, this.model );
		},

		changeSetting: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'change:setting', e, this.model )
		}

	});

	return view;
} );
/**
 * Returns the childview we need to use for our conditional logic form settings.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/returnChildView',[ 'views/advanced/conditionCollection', 'views/actions/conditionLayout' ], function( AdvancedView, ActionsView ) {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'advanced_conditions' ).reply( 'get:settingChildView', this.getAdvancedChildView );
			nfRadio.channel( 'action_conditions' ).reply( 'get:settingChildView', this.getActionChildView );
		},

		getAdvancedChildView: function( settingModel ) {
			return AdvancedView;
		},

		getActionChildView: function( settingModel ) {
			return ActionsView;
		}

	});

	return controller;
} );

/**
 * When Model
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'models/whenModel',[], function() {
	var model = Backbone.Model.extend( {
		defaults: {
			connector: 'AND',
			key: '',
			comparator: '',
			value: '',
			type: 'field',
			modelType: 'when'
		},

		initialize: function() {
			nfRadio.channel( 'conditions' ).trigger( 'init:whenModel', this );
		}
	} );
	
	return model;
} );
/**
 * When Collection
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'models/whenCollection',['models/whenModel'], function( WhenModel ) {
	var collection = Backbone.Collection.extend( {
		model: WhenModel,

		initialize: function( models, options ) {
			this.options = options;
		}
	} );
	return collection;
} );
/**
 * Then Model
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'models/thenModel',[], function() {
	var model = Backbone.Model.extend( {
		defaults: {
			key: '',
			trigger: '',
			value: '',
			type: 'field',
			modelType: 'then'
		},

		initialize: function() {
			nfRadio.channel( 'conditions' ).trigger( 'init:thenModel', this );
		}
	} );
	
	return model;
} );
/**
 * Then Collection
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'models/thenCollection',['models/thenModel'], function( ThenModel ) {
	var collection = Backbone.Collection.extend( {
		model: ThenModel,

		initialize: function( models, options ) {
			this.options = options;
		}
	} );
	return collection;
} );
/**
 * Else Model
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'models/elseModel',[], function() {
	var model = Backbone.Model.extend( {
		defaults: {
			key: '',
			trigger: '',
			value: '',
			type: 'field',
			modelType: 'else'
		},

		initialize: function() {
			nfRadio.channel( 'conditions' ).trigger( 'init:elseModel', this );
		}
	} );
	
	return model;
} );
/**
 * Else Collection
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'models/elseCollection',['models/elseModel'], function( ElseModel ) {
	var collection = Backbone.Collection.extend( {
		model: ElseModel,

		initialize: function( models, options ) {
			this.options = options;
		}
	} );
	return collection;
} );
/**
 * Conditon Model
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'models/conditionModel',[ 'models/whenCollection', 'models/thenCollection', 'models/elseCollection' ], function( WhenCollection, ThenCollection, ElseCollection ) {
	var model = Backbone.Model.extend( {
		defaults: {
			collapsed: false,
			process: 1,
			connector: 'all',
			when: [ {} ],
			then: [ {} ],
			else: []
		},

		initialize: function() {
			this.set( 'when', new WhenCollection( this.get( 'when' ), { conditionModel: this } ) );
			this.set( 'then', new ThenCollection( this.get( 'then' ), { conditionModel: this } ) );
			this.set( 'else', new ElseCollection( this.get( 'else' ), { conditionModel: this } ) );

			nfRadio.channel( 'conditions' ).trigger( 'init:model', this );
		}
	} );
	
	return model;
} );
/**
 * Conditon Collection
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'models/conditionCollection',['models/conditionModel'], function( ConditionModel ) {
	var collection = Backbone.Collection.extend( {
		model: ConditionModel,

		initialize: function( models, options ) {
			this.options = options;
		}
	} );
	return collection;
} );
/**
 * Item view for our drawer header
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'views/drawerHeader',[], function( ) {
	var view = Marionette.ItemView.extend({
		template: "#tmpl-nf-cl-advanced-drawer-header",

		events: {
			'click .nf-add-new': 'clickAddNew'
		},

		clickAddNew: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:addNew', e );
		}
	});

	return view;
} );
/**
 * Adds a new condition when the add new button is clicked.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/newCondition',[ 'models/whenCollection', 'models/whenModel' ], function( WhenCollection, WhenModel ) {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:addNew', this.addNew );
		},

		addNew: function( e ) {
			var conditionCollection = nfRadio.channel( 'settings' ).request( 'get:setting', 'conditions' );
			var conditionModel = conditionCollection.add( {} );

			// Add our condition addition to our change log.
			var label = {
				object: 'Condition',
				label: nfcli18n.newConditionCondition,
				change: 'Added',
				dashicon: 'plus-alt'
			};

			var data = {
				collection: conditionCollection
			}

			nfRadio.channel( 'changes' ).request( 'register:change', 'addCondition', conditionModel, null, label, data );

			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			nfRadio.channel( 'app' ).request( 'update:db' );
		}

	});

	return controller;
} );

/**
 * Updates condition settings on field change or drawer close
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/updateSettings',[], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'conditions' ), 'change:setting', this.updateSetting );
		},

		updateSetting: function( e, dataModel ) {
			var value = jQuery( e.target ).val();
			var id = jQuery( e.target ).data( 'id' );
			var before = dataModel.get( id );

			if ( jQuery( e.target ).find( ':selected' ).data( 'type' ) ) {
				dataModel.set( 'type', jQuery( e.target ).find( ':selected' ).data( 'type' ) );
			}

			dataModel.set( id, value );

			var after = value;

			var changes = {
				attr: id,
				before: before,
				after: after
			};

			/*
			 * The "Advanced" domain uses a collection of conditions, while the "Actions" domain uses a single collection.
			 * Here, if we don't have a collection property, then dataModel must be our conditionModel.
			 */
			var conditionModel = ( 'undefined' == typeof dataModel.collection ) ? dataModel : dataModel.collection.options.conditionModel;

			var data = {
				conditionModel: conditionModel
			}

			var label = {
				object: 'Condition',
				label: 'Condition',
				change: 'Changed ' + id + ' from ' + before + ' to ' + after
			};

			nfRadio.channel( 'changes' ).request( 'register:change', 'changeSetting', dataModel, changes, label, data );

			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			nfRadio.channel( 'app' ).request( 'update:db' );
		}

	});

	return controller;
} );
/**
 * Listens for clicks on our different condition controls
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/clickControls',[], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:removeCondition', this.removeCondition );
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:collapseCondition', this.collapseCondition );
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:removeWhen', this.removeWhen );
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:removeThen', this.removeThen );
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:removeElse', this.removeElse );
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:addWhen', this.addWhen );
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:addThen', this.addThen );
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:addElse', this.addElse );
		},

		removeCondition: function( e, conditionModel ) {
			var conditionCollection = conditionModel.collection;
			conditionModel.collection.remove( conditionModel );

			/*
			 * Register our remove condition event with our changes manager
			 */

			var label = {
				object: 'Condition',
				label: nfcli18n.clickControlsConditionlabel,
				change: 'Removed',
				dashicon: 'dismiss'
			};

			var data = {
				collection: conditionCollection
			}

			nfRadio.channel( 'changes' ).request( 'register:change', 'removeCondition', conditionModel, null, label, data );

			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			nfRadio.channel( 'app' ).request( 'update:db' );
		},

		collapseCondition: function( e, conditionModel ) {
			conditionModel.set( 'collapsed', ! conditionModel.get( 'collapsed' ) );
		},

		removeWhen: function( e, whenModel ) {
			var collection = whenModel.collection;
			this.removeItem( whenModel );
			/*
			 * Register our remove when change.
			 */
			
			var label = {
				object: 'Condition - When',
				label: nfcli18n.clickControlsConditionWhen,
				change: 'Removed',
				dashicon: 'dismiss'
			};

			var data = {
				collection: collection
			}

			nfRadio.channel( 'changes' ).request( 'register:change', 'removeWhen', whenModel, null, label, data );
		},

		removeThen: function( e, thenModel ) {
			var collection = thenModel.collection;
			this.removeItem( thenModel );
			/*
			 * Register our remove then change.
			 */
			
			var label = {
				object: 'Condition - Then',
				label: nfcli18n.clickControlsConditionThen,
				change: 'Removed',
				dashicon: 'dismiss'
			};

			var data = {
				collection: collection
			}

			nfRadio.channel( 'changes' ).request( 'register:change', 'removeThen', thenModel, null, label, data );
		},

		removeElse: function( e, elseModel ) {
			var collection = elseModel.collection;
			this.removeItem( elseModel );
			/*
			 * Register our remove else change.
			 */
			
			var label = {
				object: 'Condition - Else',
				label: nfcli18n.clickControlsConditionElse,
				change: 'Removed',
				dashicon: 'dismiss'
			};

			var data = {
				collection: collection
			}

			nfRadio.channel( 'changes' ).request( 'register:change', 'removeElse', elseModel, null, label, data );
			
		},

		removeItem: function( itemModel ) {
			itemModel.collection.remove( itemModel );

			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			nfRadio.channel( 'app' ).request( 'update:db' );
		},

		addWhen: function( e, conditionModel ) {
			var whenModel = conditionModel.get( 'when' ).add( {} );

			/*
			 * Register our add when as a change.
			 */
			
			var label = {
				object: 'Condition - When Criteron',
				label: nfcli18n.clickControlsConditionWhenCriteron,
				change: 'Added',
				dashicon: 'plus-alt'
			};

			var data = {
				conditionModel: conditionModel
			}

			nfRadio.channel( 'changes' ).request( 'register:change', 'addWhen', whenModel, null, label, data );

			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			nfRadio.channel( 'app' ).request( 'update:db' );
		},

		addThen: function( e, conditionModel ) {
			var thenModel = conditionModel.get( 'then' ).add( {} );

			/*
			 * Register our add then as a change.
			 */
			
			var label = {
				object: 'Condition - Do Item',
				label: nfcli18n.clickControlsConditionDoItem,
				change: 'Added',
				dashicon: 'plus-alt'
			};

			var data = {
				conditionModel: conditionModel
			}

			nfRadio.channel( 'changes' ).request( 'register:change', 'addThen', thenModel, null, label, data );

			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			nfRadio.channel( 'app' ).request( 'update:db' );
		},

		addElse: function( e, conditionModel ) {
			var elseModel = conditionModel.get( 'else' ).add( {} );

			/*
			 * Register our add when as a change.
			 */
			
			var label = {
				object: 'Condition - Else Item',
				label: nfcli18n.clickControlsConditionElseItem,
				change: 'Added',
				dashicon: 'plus-alt'
			};

			var data = {
				conditionModel: conditionModel
			}

			nfRadio.channel( 'changes' ).request( 'register:change', 'addElse', elseModel, null, label, data );

			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			nfRadio.channel( 'app' ).request( 'update:db' );
		}

	});

	return controller;
} );

/**
 * Handles undoing everything for conditions.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/undo',[], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'changes' ).reply( 'undo:addCondition', this.undoAddCondition, this );
			nfRadio.channel( 'changes' ).reply( 'undo:removeCondition', this.undoRemoveCondition, this );
			nfRadio.channel( 'changes' ).reply( 'undo:addWhen', this.undoAddWhen, this );
			nfRadio.channel( 'changes' ).reply( 'undo:addThen', this.undoAddThen, this );
			nfRadio.channel( 'changes' ).reply( 'undo:addElse', this.undoAddElse, this );
			nfRadio.channel( 'changes' ).reply( 'undo:removeWhen', this.undoRemoveWhen, this );
			nfRadio.channel( 'changes' ).reply( 'undo:removeThen', this.undoRemoveThen, this );
			nfRadio.channel( 'changes' ).reply( 'undo:removeElse', this.undoRemoveElse, this );
		},

		undoAddCondition: function( change, undoAll ) {
			var dataModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.collection.remove( dataModel );

			/*
			 * Loop through our change collection and remove any setting changes that belong to the condition we've added.
			 */
			var changeCollection = nfRadio.channel( 'changes' ).request( 'get:collection' );
			var results = changeCollection.where( function( changeModel ) {
				if ( ( changeModel = dataModel ) || 'undefined' != typeof changeModel.get( 'data' ).conditionModel && changeModel.get( 'data' ).conditionModel == dataModel ) {
					return true;
				} else {
					return false;
				}
			} );

			_.each( results, function( model ) {
				changeCollection.remove( model );
			} );

			this.maybeRemoveChange( change, undoAll );
		},

		undoRemoveCondition: function( change, undoAll ) {
			var dataModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.collection.add( dataModel );

			this.maybeRemoveChange( change, undoAll );
		},

		undoAddWhen: function( change, undoAll ) {
			var whenModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.conditionModel.get( 'when' ).remove( whenModel );

			this.maybeRemoveChange( change, undoAll );
		},

		undoAddThen: function( change, undoAll ) {
			var thenModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.conditionModel.get( 'then' ).remove( thenModel );

			this.maybeRemoveChange( change, undoAll );
		},

		undoAddElse: function( change, undoAll ) {
			var elseModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.conditionModel.get( 'else' ).remove( elseModel );

			this.maybeRemoveChange( change, undoAll );
		},

		undoRemoveWhen: function( change, undoAll ) {
			var whenModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.collection.add( whenModel );

			this.maybeRemoveChange( change, undoAll );
		},

		undoRemoveThen: function( change, undoAll ) {
			var thenModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.collection.add( thenModel );

			this.maybeRemoveChange( change, undoAll );
		},

		undoRemoveElse: function( change, undoAll ) {
			var elseModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.collection.add( elseModel );

			this.maybeRemoveChange( change, undoAll );
		},

		/**
		 * If our undo action was requested to 'remove' the change from the collection, remove it.
		 * 
		 * @since  3.0
		 * @param  backbone.model 	change 	model of our change
		 * @param  boolean 			remove 	should we remove this item from our change collection
		 * @return void
		 */
		maybeRemoveChange: function( change, undoAll ) {			
			var undoAll = typeof undoAll !== 'undefined' ? undoAll : false;
			if ( ! undoAll ) {
				// Update preview.
				nfRadio.channel( 'app' ).request( 'update:db' );
				var changeCollection = nfRadio.channel( 'changes' ).request( 'get:collection' );
				changeCollection.remove( change );
				if ( 0 == changeCollection.length ) {
					nfRadio.channel( 'app' ).request( 'update:setting', 'clean', true );
					nfRadio.channel( 'app' ).request( 'close:drawer' );
				}
			}
		}

	});

	return controller;
} );

/**
 * Returns the type of input value we'd like to use.
 * This covers all the core field types.
 *
 * Add-ons can copy this code structure in order to get custom "values" for conditions.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/coreValues',[], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'conditions-checkbox' ).reply( 'get:valueInput', this.getCheckboxValue );
			nfRadio.channel( 'conditions-list' ).reply( 'get:valueInput', this.getListValue );
			nfRadio.channel( 'conditions-listcountry' ).reply( 'get:valueInput', this.getListCountryValue );
			nfRadio.channel( 'conditions-date' ).reply( 'get:valueInput', this.getDateValue );
		},

		getCheckboxValue: function( key, trigger, value ) {
			/*
			 * Checks our values ensures they've been converted to strings and
			 * sets the value.
			 */
			if( 1 == value && value.length > 1 ) {
				value = 'checked';
			} else if( 0 == value && value.length > 1 ) {
                value = 'unchecked';
            } else if( 0 == value.length ){
				value = '';
			}

			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-value-checkbox' );
			return template( { value: value } );
		},

		getListValue: function( key, trigger, value ) {
			var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', key );
			var options = fieldModel.get( 'options' );
			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-value-list' );
			return template( { options: options, value: value } );
		},

		getListCountryValue: function( key, trigger, value ) {
			var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', key );
			var options = fieldModel.get( 'options' );
			var template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-value-list' );

			options.reset();
			_.each( nfListCountries, function( value, label ) {
				options.add( { label: label, value: value } );
			});

			return template( { options: options, value: value } );
		},

		getDateValue: function( key, trigger, value ) {
			let fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', key );
			let dateMode = fieldModel.get( 'date_mode' );

			if ( 'undefined' == typeof dateMode ) {
				dateMode = 'date_only';
			}

			let timestamp = value * 1000;
			let dateObject = new Date( timestamp );
			dateObject = new Date( dateObject.getUTCFullYear(), dateObject.getUTCMonth(), dateObject.getUTCDate(), dateObject.getUTCHours(), dateObject.getUTCMinutes() );

			let selectedHour = dateObject.getHours();
			let selectedMinute = dateObject.getMinutes(); 

			let hourSelect = '<select class="extra" data-type="hour">';
			for (var i = 0; i < 24; i++) {
				let formattedOption = i;
				let selected = '';
				if ( i < 10 ) {
					formattedOption = '0' + formattedOption;
				}

				if ( selectedHour == formattedOption ) {
					selected = 'selected="selected"';
				}
				
				hourSelect += '<option value="' + formattedOption + '" ' + selected + '>' + formattedOption + '</option>';
			}
			hourSelect += '</select>';

			let minuteSelect = '<select class="extra" data-type="minute">';
			for (var i = 0; i < 60; i++) {
				let formattedOption = i;
				let selected = '';
				if ( i < 10 ) {
					formattedOption = '0' + formattedOption;
				}

				if ( selectedMinute == formattedOption ) {
					selected = 'selected="selected"';
				}
				
				minuteSelect += '<option value="' + formattedOption + '" ' + selected + '>' + formattedOption + '</option>';
			}
			minuteSelect += '</select>';

			let date = moment( dateObject.toUTCString() ).format( 'YYYY-MM-DD' );
			if ( '1970-01-01' == date ) {
				date = '';
			}

			let template = Backbone.Radio.channel( 'app' ).request( 'get:template', '#tmpl-nf-cl-value-date-' + dateMode );
			return template( { value: value, date: date, hourSelect: hourSelect, minuteSelect: minuteSelect  } );
		},


	});

	return controller;
} );

/**
 * Returns an object with each comparator we'd like to use.
 * This covers all the core field types.
 *
 * Add-ons can copy this code structure in order to get custom "comparators" for conditions.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/coreComparators',[], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'conditions-checkbox' ).reply( 'get:comparators', this.getCheckboxComparators );
			nfRadio.channel( 'conditions-listradio' ).reply( 'get:comparators', this.getListSingleComparators );
			nfRadio.channel( 'conditions-listselect' ).reply( 'get:comparators', this.getListSingleComparators );
			nfRadio.channel( 'conditions-list' ).reply( 'get:comparators', this.getListComparators );
			nfRadio.channel( 'conditions-date' ).reply( 'get:comparators', this.getDateComparators );
		},

		getCheckboxComparators: function( defaultComparators ) {
			return {
				is: {
					label: nfcli18n.coreComparatorsIs,
					value: 'equal'
				},

				isnot: {
					label: nfcli18n.coreComparatorsIsNot,
					value: 'notequal'
				}
			}
		},

		getListComparators: function( defaultComparators ) {
			return {
				has: {
					label: nfcli18n.coreComparatorsHasSelected,
					value: 'contains'
				},

				hasnot: {
					label: nfcli18n.coreComparatorsDoesNotHaveSelected,
					value: 'notcontains'
				}
			}
		},

		getListSingleComparators: function( defaultComparators, currentComparator ) {
			/*
			 * Radio and Select lists need to use equal and notequal.
			 * In previous versions, however, they used contains and notcontains.
			 * In order to keep forms working that were made in those previous versions,
			 * we check to see if the currentComparator is contains or notcontains.
			 * If it is, we return those values; else we return equal or not equal.
			 */
			if ( 'contains' == currentComparator || 'notcontains' == currentComparator ) {
				return {
					has: {
						label: nfcli18n.coreComparatorsHasSelected,
						value: 'contains'
					},

					hasnot: {
						label: nfcli18n.coreComparatorsDoesNotHaveSelected,
						value: 'notcontains'
					}
				}		
			}

			return {
					has: {
						label: nfcli18n.coreComparatorsHasSelected,
						value: 'equal'
					},

					hasnot: {
						label: nfcli18n.coreComparatorsDoesNotHaveSelected,
						value: 'notequal'
					}
				}	
		},

		getDateComparators: function( defaultComparators ) {
			return {
				before: {
					label: nfcli18n.coreComparatorsBefore,
					value: 'less'
				},

				onorbefore: {
					label: nfcli18n.coreComparatorsOnOrBefore,
					value: 'lessequal'
				},

				equal: {
					label: nfcli18n.coreComparatorsIs,
					value: 'equal'
				},

				onorafter: {
					label: nfcli18n.coreComparatorsOnOrAfter,
					value: 'greaterequal'
				},

				after: {
					label: nfcli18n.coreComparatorsAfter,
					value: 'greater'
				}
			}
		},

	});

	return controller;
} );

/**
 * Returns an object with each trigger we'd like to use.
 * This covers all the core field types.
 *
 * Add-ons can copy this code structure in order to get custom "triggers" for conditions.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/coreTriggers',[], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'conditions-list' ).reply( 'get:triggers', this.getListTriggers );
			nfRadio.channel( 'conditions-submit' ).reply( 'get:triggers', this.getSubmitTriggers );
			nfRadio.channel( 'conditions-html' ).reply( 'get:triggers', this.getHTMLTriggers );
			nfRadio.channel( 'conditions-hr' ).reply( 'get:triggers', this.getDividerTriggers );
			nfRadio.channel( 'conditions-hidden' ).reply( 'get:triggers', this.getHiddenTriggers );
		},

		getListTriggers: function( defaultTriggers ) {
			var triggers = _.extend( defaultTriggers, {
				select_option: {
					label: nfcli18n.coreTriggersSelectOption,
					value: 'select_option'
				},

				deselect_option: {
					label: nfcli18n.coreTriggersDeselectOption,
					value: 'deselect_option'
				},

				show_option: {
					label: nfcli18n.coreTriggersShowOption,
					value: 'show_option'
				},

				hide_option: {
					label: nfcli18n.coreTriggersHideOption,
					value: 'hide_option'
				}
			} );

			var triggers = _.omit( defaultTriggers, 'change_value' );

			return triggers;
		},

		getSubmitTriggers: function( defaultTriggers ) {
			return _.omit( defaultTriggers, ['change_value', 'set_required', 'unset_required'] );
		},

		getHTMLTriggers: function( defaultTriggers ) {
			return _.omit( defaultTriggers, ['set_required', 'unset_required'] );
		},

		getDividerTriggers: function( defaultTriggers ) {
			return _.omit( defaultTriggers, ['change_value', 'set_required', 'unset_required'] );
		},

		getHiddenTriggers: function( defaultTriggers ) {
			return _.omit( defaultTriggers, ['set_required', 'unset_required'] );
		}

	});

	return controller;
} );
/**
 * Returns the view to use in the drawer header.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/getDrawerHeader',[ 'views/drawerHeader' ], function( DrawerHeaderView ) {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'conditional_logic' ).reply( 'get:drawerHeaderView', this.getDrawerHeaderView, this );
		},

		getDrawerHeaderView: function() {
			return DrawerHeaderView;
		}
	});

	return controller;
} );

/**
 * Tracks key changes and updates when/then/else models
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/trackKeyChanges',[], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'conditions' ), 'init:whenModel', this.registerKeyChangeTracker );
			this.listenTo( nfRadio.channel( 'conditions' ), 'init:thenModel', this.registerKeyChangeTracker );
			this.listenTo( nfRadio.channel( 'conditions' ), 'init:elseModel', this.registerKeyChangeTracker );
		},

		registerKeyChangeTracker: function( itemModel ) {
            // Update selected field if the selected field's key changes.
            itemModel.listenTo( nfRadio.channel( 'app' ), 'replace:fieldKey', this.updateKey, itemModel );
        },

		updateKey: function( fieldModel, keyModel, settingModel ) {
			var oldKey = keyModel._previousAttributes[ 'key' ];
            var newKey = keyModel.get( 'key' );
            
            if( this.get( 'key' ) == oldKey ) {
                this.set( 'key', newKey );
            }
		}
	});

	return controller;
} );
/**
 * When we init our action model, check to see if we have a 'conditions' setting that needs to be converted into a collection.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/maybeConvertConditions',[ 'models/conditionModel' ], function( ConditionModel ) {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'actions' ), 'init:actionModel', this.maybeConvertConditions );
		},

		maybeConvertConditions: function( actionModel ) {
			var conditions = actionModel.get( 'conditions' );
			if ( ! conditions ) {
				actionModel.set( 'conditions', new ConditionModel() );
			} else if ( false === conditions instanceof Backbone.Model ) {
				actionModel.set( 'conditions', new ConditionModel( conditions ) );
			}
		}

	});

	return controller;
} );

/**
 * Register filters for our when/then key groups/settings.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/filters',[], function() {
	var controller = Marionette.Object.extend( {
		filters: [],

		initialize: function() {
			nfRadio.channel( 'conditions' ).reply( 'add:groupFilter', this.addFilter, this );
			nfRadio.channel( 'conditions' ).reply( 'get:groupFilters', this.getFilters, this );
		},

		addFilter: function( callback ) {
			this.filters.push( callback );
		},

		getFilters: function() {
			return this.filters;
		}

	});

	return controller;
} );

/**
 * Listens for changes in the "extra" settings in "when" settings.
 * We use this for the date field to update the "value" to a timestamp when we change a date value setting.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/fieldDate',[], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'conditions' ), 'change:extra', this.maybeUpdateSetting );
		},

		maybeUpdateSetting: function( e, dataModel ) {
			let dateString = '';
			// Get our date
			let date = jQuery( e.target ).parent().parent().find( "[data-type='date']" ).val();
			if ( 'undefined' == typeof date ) {
				date = '1970-01-02';
			}
			dateString += date + 'T';

			// Get our hour
			let hour = jQuery( e.target ).parent().parent().find( "[data-type='hour']" ).val();
			if ( 'undefined' == typeof hour ) {
				hour = '00';
			}
			dateString += hour + ':';

			// Get our minute
			let minute = jQuery( e.target ).parent().parent().find( "[data-type='minute']" ).val();
			if ( 'undefined' == typeof minute ) {
				minute = '00';
			}
			dateString += minute + 'Z';

			// Build a timestamp
			let dateObject = new Date( dateString );
			let timestamp = Math.floor( dateObject.getTime() / 1000 );

			// Update our value with the timestamp
			dataModel.set( 'value', timestamp );
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
		},

	});

	return controller;
} );
/**
 * Loads all of our custom controllers.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( 'controllers/loadControllers',[
	'controllers/templateHelpers',
	'controllers/returnChildView',
	'models/conditionCollection',
	'views/drawerHeader',
	'controllers/newCondition',
	'controllers/updateSettings',
	'controllers/clickControls',
	'controllers/undo',
	// 'controllers/maybeModifyElse',
	'controllers/coreValues',
	'controllers/coreComparators',
	'controllers/coreTriggers',
	'controllers/getDrawerHeader',
	'controllers/trackKeyChanges',
	'controllers/maybeConvertConditions',
	'controllers/filters',
	'controllers/fieldDate'

	], function(

	TemplateHelpers,
	ReturnChildView,
	ConditionCollection,
	DrawerHeaderView,
	NewCondition,
	UpdateSettings,
	ClickControls,
	Undo,
	// MaybeModifyElse,
	CoreValues,
	CoreComparators,
	CoreTriggers,
	GetDrawerHeader,
	TrackKeyChanges,
	MaybeConvertConditions,
	Filters,
	FieldDate
	) {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			new TemplateHelpers();
			new ReturnChildView();
			new NewCondition();
			new UpdateSettings();
			new ClickControls();
			new Undo();
			// new MaybeModifyElse();
			new CoreValues();
			new CoreComparators();
			new CoreTriggers();
			new GetDrawerHeader();
			new TrackKeyChanges();
			new MaybeConvertConditions();
			new Filters();
			new FieldDate();
		}
	});

	return controller;
} );

var nfRadio = Backbone.Radio;

require( [ 'controllers/loadControllers', 'models/conditionCollection' ], function( LoadControllers, ConditionCollection ) {

	var NFConditionalLogic = Marionette.Application.extend( {

		initialize: function( options ) {
			this.listenTo( nfRadio.channel( 'app' ), 'after:appStart', this.afterNFLoad );
		},

		onStart: function() {
			new LoadControllers();
		},

		afterNFLoad: function( app ) {
			/*
			 * Convert our form's "condition" setting into a collection.
			 */
			var conditions = nfRadio.channel( 'settings' ).request( 'get:setting', 'conditions' );

			if ( false === conditions instanceof Backbone.Collection ) {
				conditions = new ConditionCollection( conditions );
				nfRadio.channel( 'settings' ).request( 'update:setting', 'conditions', conditions, true );
			}
		}
	} );

	var nfConditionalLogic = new NFConditionalLogic();
	nfConditionalLogic.start();
} );
define("main", function(){});

}());
//# sourceMappingURL=builder.js.map
