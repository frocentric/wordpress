jQuery( document ).ready( function($) {

	var bLazy = new Blazy({
	    selector: '.lazyload',
		success: function(ele){
            $( ele ).parent().addClass( 'image-loaded' );
        }
	});

	/**
	 * Demo sites
	 */
	$( '.site-box .preview-site' ).on( 'click', function( e ) {
		e.preventDefault();
		var _this = $( this );
		var site_box = _this.closest( '.site-box' );

		if ( ! site_box.find( 'iframe' ).attr( 'src' ) ) {
			site_box.find( 'iframe' ).attr( 'src', site_box.data( 'site-data' ).preview_url );
		}

		site_box.find( 'iframe' ).on( 'load', function () {
			site_box.find( '.demo-loading' ).fadeOut().remove();
		});

		site_box.find( '.site-demo' ).show().addClass( 'open' );
	} );

	$( '.site-demo .close-demo' ).on( 'click', function( e ) {
		$( '.site-demo' ).hide().removeClass( 'open' );
		bLazy.revalidate();
	} );

	$( '.demo-panel .show-desktop' ).on( 'click', function( e ) {
		$( this ).addClass( 'active' ).siblings().removeClass( 'active' );
		$( '.site-demo' ).removeClass( 'mobile' ).removeClass( 'tablet' );
	} );

	$( '.demo-panel .show-tablet' ).on( 'click', function( e ) {
		$( this ).addClass( 'active' ).siblings().removeClass( 'active' );
		$( '.site-demo' ).removeClass( 'mobile' ).addClass( 'tablet' );
	} );

	$( '.demo-panel .show-mobile' ).on( 'click', function( e ) {
		$( this ).addClass( 'active' ).siblings().removeClass( 'active' );
		$( '.site-demo' ).addClass( 'mobile' ).removeClass( 'tablet' );
	} );

	$( '.site-demo .get-started' ).on( 'click', function( e ) {
		$( '.site-demo' ).hide().removeClass( 'open' );

		if ( ! $( '.generatepress-sites' ).hasClass( 'site-open' ) ) {
			var this_site = $( this ).closest( '.site-box' );

			$( '.generatepress-sites' ).addClass( 'site-open' );
			$( '.library-filters' ).hide();
			var screenshot = this_site.find( '.site-card-screenshot img' ).attr( 'src' );

			this_site.find( '.site-overview-screenshot img' ).attr( 'src', screenshot );
			this_site.siblings().hide();
			this_site.find( '.step-one' ).hide().next().show();
		}
	} );

	/**
	 * Site card controls
	 */
	$( '.site-box .close' ).on( 'click', function( e ) {
		e.preventDefault();
		var siteBox = $( '.site-box' ),
			page_builder = $( '.generatepress-sites' ).attr( 'data-page-builder' );

		siteBox.find( '.steps' ).hide();
		siteBox.find( '.step-one' ).fadeIn().css( 'display', '' );

		$( '.generatepress-sites' ).removeClass( 'site-open' );
		$( '.library-filters' ).show();
		siteBox.siblings( page_builder ).fadeIn( 'fast' );

		bLazy.revalidate();

		if ( $( 'body' ).hasClass( 'site-import-data-exists' ) ) {
			$( '.remove-site' ).show();
			$( '.remove-site .do-remove-site' ).show();
			$( '.remove-site .skip-remove-site' ).show();
			$( '.generatepress-sites' ).addClass( 'remove-site-needed' );
			window.scrollTo( { top: 0 } );
		}
	} );

	$( '.site-box .next' ).on( 'click', function( e ) {
		var page_builder = $( '.generatepress-sites' ).attr( 'data-page-builder' );
		var this_site = $( this ).closest( '.site-box' );
		var next_site = this_site.nextAll( page_builder ).not( '.disabled-site' ).first();

		if ( ! next_site.length ) {
			next_site = $( '.generatepress-sites' ).find( '.site-box' + page_builder ).first();
		}

		var screenshot = next_site.find( '.site-card-screenshot img' ).attr( 'data-src' );

		if ( ! screenshot ) {
			screenshot = next_site.find( '.site-card-screenshot img' ).attr( 'src' );
		}

		if ( this_site.parent().hasClass( 'site-open' ) ) {
			this_site.hide();
			next_site.show().find( '.step-one' ).hide().next().show();
			next_site.find( '.site-overview-screenshot img' ).attr( 'src', screenshot );
		}

		if ( this_site.find( '.site-demo' ).hasClass( 'open' ) ) {
			this_site.find( '.site-demo' ).hide().removeClass( 'open' );

			if ( ! next_site.find( 'iframe' ).attr( 'src' ) ) {
				next_site.find( 'iframe' ).attr( 'src', next_site.data( 'site-data' ).preview_url );
			}

			next_site.find( 'iframe' ).on( 'load', function () {
				next_site.find( '.demo-loading' ).fadeOut().remove();
			});

			next_site.find( '.site-demo' ).show().addClass( 'open' );
		}

		if ( $( 'body' ).hasClass( 'site-import-data-exists' ) ) {
			$( '.remove-site' ).show();
			$( '.remove-site .do-remove-site' ).show();
			$( '.remove-site .skip-remove-site' ).show();
			$( '.generatepress-sites' ).addClass( 'remove-site-needed' );
		}
	} );

	$( '.site-box .prev' ).on( 'click', function( e ) {
		var page_builder = $( '.generatepress-sites' ).attr( 'data-page-builder' );
		var this_site = $( this ).closest( '.site-box' );
		var prev_site = this_site.prevAll( page_builder ).not( '.disabled-site' ).first();

		if ( ! prev_site.length ) {
			prev_site = $( '.generatepress-sites' ).find( '.site-box' + page_builder ).last();
		}

		var screenshot = prev_site.find( '.site-card-screenshot img' ).attr( 'data-src' );

		if ( ! screenshot ) {
			screenshot = prev_site.find( '.site-card-screenshot img' ).attr( 'src' );
		}

		if ( this_site.parent().hasClass( 'site-open' ) ) {
			this_site.hide();
			prev_site.show().find( '.step-one' ).hide().next().show();
			prev_site.find( '.site-overview-screenshot img' ).attr( 'src', screenshot );
		}

		if ( this_site.find( '.site-demo' ).hasClass( 'open' ) ) {
			this_site.find( '.site-demo' ).hide().removeClass( 'open' );

			if ( ! prev_site.find( 'iframe' ).attr( 'src' ) ) {
				prev_site.find( 'iframe' ).attr( 'src', prev_site.data( 'site-data' ).preview_url );
			}

			prev_site.find( 'iframe' ).on( 'load', function () {
				prev_site.find( '.demo-loading' ).fadeOut().remove();
			});

			prev_site.find( '.site-demo' ).show().addClass( 'open' );
		}

		if ( $( 'body' ).hasClass( 'site-import-data-exists' ) ) {
			$( '.remove-site' ).show();
			$( '.remove-site .do-remove-site' ).show();
			$( '.remove-site .skip-remove-site' ).show();
			$( '.generatepress-sites' ).addClass( 'remove-site-needed' );
		}
	} );

	$( '.site-box .site-details' ).on( 'click', function( e ) {
		var _this = $( this ),
			siteBox = _this.closest( '.site-box' ),
			step = _this.closest( '.steps' ),
			screenshot = step.find( '.site-card-screenshot img' ).attr( 'data-src' );

		if ( ! screenshot ) {
			screenshot = step.find( '.site-card-screenshot img' ).attr( 'src' );
		}

		$( '.generatepress-sites' ).addClass( 'site-open' );
		$( '.library-filters' ).hide();

		_this.closest( '.site-box' ).siblings().hide();
		step.hide();
		step.next().fadeIn( 'fast' );
		siteBox.find( '.site-overview-screenshot img' ).attr( 'src', screenshot );
	} );

	$( '.confirm-content-import' ).on( 'change', function() {
		var siteBox = $( this ).closest( '.site-box' );

		if ( $( this ).is( ':checked' ) ) {
			siteBox.find( 'input.import-content' ).attr( 'disabled', false );
		} else {
			siteBox.find( 'input.import-content' ).attr( 'disabled', 'disabled' );
		}
	} );

	$( '.confirm-options-import' ).on( 'change', function() {
		var siteBox = $( this ).closest( '.site-box' );

		if ( $( this ).is( ':checked' ) ) {
			siteBox.find( 'input.backup-options' ).attr( 'disabled', false );
			siteBox.find( 'input.import-options' ).attr( 'disabled', false );
		} else {
			siteBox.find( 'input.backup-options' ).attr( 'disabled', 'disabled' );
			siteBox.find( 'input.import-options' ).attr( 'disabled', 'disabled' );
		}
	} );

	$( '.page-builder-group' ).on( 'change', function( e ) {
		e.preventDefault();

		var _this = $( this ),
			filter = _this.val();

		// _this.siblings().removeClass( 'active' );
		// _this.addClass( 'active' );

		if ( '' == filter ) {
			$( '.site-box' ).show();
			$( '.generatepress-sites' ).attr( 'data-page-builder', '' );
		} else {
			$( '.site-box:not(.' + filter + ')' ).hide();
			$( '.site-box.' + filter ).show();
			$( '.generatepress-sites' ).attr( 'data-page-builder', '.' + filter );
		}

		bLazy.revalidate();
	} );

	var setup_demo_content = function( _this ) {
		var site_box = _this.closest( '.site-box' );
		var site_data = site_box.data( 'site-data' );

		if ( ! site_box.hasClass( 'data-content-loaded' ) ) {
			// Prevent duplicate setup.
			site_box.addClass( 'data-content-loaded' );

			$.ajax({
				type: 'POST',
				url: generate_sites_params.ajaxurl,
				data: {
					action: 'generate_setup_demo_content_' + site_data.slug,
					nonce: generate_sites_params.nonce,
				},
				success: function( data ) {
					console.log(data);

					if ( data.content ) {
						if ( data.widgets ) {
							site_box.find( '.site-action.import-content' ).attr( 'data-widgets', true );
						} else {
							site_box.find( '.site-action.import-content' ).attr( 'data-widgets', false );
						}

						if ( data.plugins ) {
							$.ajax( {
								type: 'POST',
								url: generate_sites_params.ajaxurl,
								data: {
									action: 'generate_check_plugins_' + site_data.slug,
									nonce: generate_sites_params.nonce,
									data: site_box.data( 'site-data' ),
								},
								success: function( data ) {
									console.log( data );
									site_box.find( '.site-plugins' ).hide();
									site_box.find( '.plugin-area' ).show();
									site_box.find( '.site-action.import-content' ).attr( 'data-plugins', JSON.stringify( data.plugin_data ) );

									$.each( data.plugin_data, function( index, value ) {
										var slug = value.slug.substring( 0, value.slug.indexOf( '/' ) );

										if ( value.repo && ! value.installed ) {
											site_box.find( '.automatic-plugins' ).fadeIn();
											site_box.find( '.automatic-plugins ul' ).append( '<li data-slug="' + slug + '">' + value.name + '</li>' );
										} else if ( value.installed || value.active ) {
											site_box.find( '.installed-plugins' ).fadeIn();
											site_box.find( '.installed-plugins ul' ).append( '<li class="plugin-installed" data-slug="' + slug + '">' + value.name + '</li>' );
										} else {
											site_box.find( '.manual-plugins' ).fadeIn();
											site_box.find( '.manual-plugins ul' ).append( '<li>' + value.name + '</li>' );
										}
									} );

									if ( $.isEmptyObject( data.plugin_data ) ) {
										site_box.find( '.no-plugins' ).fadeIn();
									}

									site_box.find( '.loading' ).hide();
									site_box.find( '.site-action.import-content' ).show();
									site_box.find( '.confirm-content-import-message' ).show();
									site_box.find( '.skip-content-import' ).show();
								},
								error: function( data ) {
									console.log( data );
									site_box.find( '.site-message' ).hide();
									site_box.find( '.error-message' ).html( data.status + ' ' + data.statusText + ' <a href="https://docs.generatepress.com/article/error-codes-importing/" target="_blank" rel="noopener">[?]</a>' ).show();
									siteBox.find( '.theme-options .big-loader' ).css( 'opacity', '0' );
									siteBox.find( '.theme-options .number' ).css( 'opacity', '1' );
								}
							} );
						} else {
							site_box.find( '.loading' ).hide();
							site_box.find( '.site-action.import-content' ).show();
							site_box.find( '.confirm-content-import-message' ).show();
							site_box.find( '.skip-content-import' ).show();
						}
					} else {
						site_box.find( '.loading' ).hide();
						site_box.find( '.demo-content .big-loader' ).css( 'opacity', '0' );
						site_box.find( '.demo-content .number' ).css( 'opacity', '1' ).addClass( 'step-complete' );
						_this.next( 'input' ).show();

						setTimeout( function() {
							site_box.find( '.import-complete .number' ).css( 'opacity', '1' ).addClass( 'step-complete' );
						}, 500 );
					}


				},
				error: function( data ) {
					console.log( data );
					site_box.removeClass( 'data-loaded' );
					site_box.find( '.loading' ).hide();
					site_box.find( '.error-message' ).html( data.status + ' ' + data.statusText + ' <a href="https://docs.generatepress.com/article/error-codes-importing/" target="_blank" rel="noopener">[?]</a>' ).show();
				}
			});
		}
	};

	/**
	 * Backup options.
	 */
	$( '.site-box .backup-options' ).on( 'click', function(e) {
		e.preventDefault();

		var _this = $( this ),
			siteBox = _this.closest( '.site-box' );

		_this.hide();
		siteBox.find( '.confirm-backup-options' ).hide();
		backup_options( _this );
	} );

	/**
	 * Backup and import theme options.
	 */
	$( '.site-box .import-options' ).on( 'click', function( e ) {
		e.preventDefault();

		var _this = $( this );

		_this.hide();
		_this.closest( '.site-box' ).find( '.confirm-backup-options' ).hide();
		import_options( _this );
	} );

	function backup_options( _this ) {
		var siteBox = _this.closest( '.site-box' )
			data = siteBox.data( 'site-data' );

		siteBox.find( '.site-message' ).text( generate_sites_params.backing_up_options );
		siteBox.find( '.loading' ).show();

		$.ajax( {
			type: 'POST',
			url: generate_sites_params.ajaxurl,
			data: {
				action: 'generate_backup_options_' + data.slug,
				nonce: generate_sites_params.nonce,
			},
			success: function( data ) {
				download( data, 'generatepress-options-backup.json', 'application/json' );

				siteBox.find( '.loading' ).hide();
				_this.next( 'input' ).show();
			},
			error: function( data ) {
				console.log( data );
				siteBox.find( '.error-message' ).html( data.status + ' ' + data.statusText + ' <a href="https://docs.generatepress.com/article/error-codes-importing/" target="_blank" rel="noopener">[?]</a>' ).show();
			}
		} );
	}

	function import_options( _this ) {
		var siteBox = _this.closest( '.site-box' ),
			data = siteBox.data( 'site-data' );

		siteBox.find( '.site-message' ).text( generate_sites_params.importing_options );
		siteBox.find( '.loading' ).show();
		siteBox.find( '.theme-options .big-loader' ).css( 'opacity', '1' );;
		siteBox.find( '.theme-options .number' ).css( 'opacity', '0' );

		$.ajax( {
			type: 'POST',
			url: generate_sites_params.ajaxurl,
			data: {
				action: 'generate_import_options_' + data.slug,
				nonce: generate_sites_params.nonce,
			},
			success: function( data ) {
				if ( 'undefined' !== typeof data.success && ! data.success ) {
					siteBox.find( '.loading' ).hide();
					siteBox.find( '.error-message' ).html( data.data ).show();
					siteBox.find( '.theme-options .big-loader' ).css( 'opacity', '0' );
					siteBox.find( '.theme-options .number' ).css( 'opacity', '1' );
					return;
				}

				console.log( 'Options imported.' );

				_this.hide();
				siteBox.find( '.loading' ).hide();
				siteBox.find( '.theme-options .big-loader' ).css( 'opacity', '0' );
				siteBox.find( '.theme-options .number' ).css( 'opacity', '1' ).addClass( 'step-complete' );
				$( 'body' ).addClass( 'site-import-data-exists' );

				siteBox.find( '.site-message' ).text( generate_sites_params.checking_demo_content );
				siteBox.find( '.loading' ).show();
				setup_demo_content( _this );
			},
			error: function( data ) {
				console.log( data );
				siteBox.find( '.loading' ).hide();
				siteBox.find( '.error-message' ).html( data.status + ' ' + data.statusText + ' <a href="https://docs.generatepress.com/article/error-codes-importing/" target="_blank" rel="noopener">[?]</a>' ).show();
				siteBox.find( '.theme-options .big-loader' ).css( 'opacity', '0' );
				siteBox.find( '.theme-options .number' ).css( 'opacity', '1' );
			}
		} );
	}

	$( '.site-box' ).on( 'click', '.skip-content-import a', function(e) {
		e.preventDefault();

		var _this = $( this ),
			siteBox = _this.closest( '.site-box' );

		siteBox.find( '.skip-content-import' ).hide();
		siteBox.find( '.demo-content' ).css( 'opacity', '0.5' );
		siteBox.find( '.confirm-content-import-message' ).hide();
		siteBox.find( '.action-buttons input' ).hide();
		siteBox.find( '.action-buttons input.view-site' ).show();
		siteBox.find( '.import-complete .number' ).css( 'opacity', '1' ).addClass( 'step-complete' );
	} );

	/**
	 * Install and activate plugins.
	 * Before content, as content may be depedent on plugins.
	 */
	$( '.site-box' ).on( 'click', '.import-content', function(e) {
		e.preventDefault();

		var _this = $( this )
			siteBox = _this.closest( '.site-box' ),
			plugins = _this.data( 'plugins' ),
			plugin_text = siteBox.find( '.automatic-plugins li' );

		_this.hide();
		siteBox.find( '.skip-content-import' ).hide();
		siteBox.find( '.loading' ).show();
		siteBox.find( '.confirm-content-import-message' ).hide();
		siteBox.find( '.demo-content .big-loader' ).css( 'opacity', '1' );
		siteBox.find( '.demo-content .number' ).css( 'opacity', '0' );
		$( 'body' ).addClass( 'site-import-content-exists' );

		siteBox.attr( 'data-plugins', JSON.stringify( plugins ) );

		if ( ! $.isEmptyObject( plugins ) ) {
			siteBox.find( '.site-message' ).text( generate_sites_params.installing_plugins );

			$.each( plugins, function( index, value ) {
				var plugin_slug = value.slug.split('/')[0];

				var plugin_row = plugin_text.filter( function () {
					return $( this ).attr( 'data-slug' ) == plugin_slug;
				} );

				if ( 'elementor' === plugin_slug ) {
					siteBox.find( '.replace-elementor-urls' ).show();
				}

				if ( ! value.installed ) {
					plugin_row.find( '.loading' ).show();
					plugin_row.addClass( 'installing-plugins' );

					// Install BB Lite if Pro doesn't exist.
					if ( 'bb-plugin' == plugin_slug ) {
						plugin_slug = 'beaver-builder-lite-version';
					}

					wp.updates.installPlugin( {
						slug: plugin_slug,
						success: function( data ) {
							console.log( data );

							plugin_row.removeClass( 'installing-plugins' ).addClass( 'plugin-installed' );
							plugin_row.removeClass( 'show-loading' ).next().addClass( 'show-loading' );

							// Remove current plugin from queue
							delete plugins[index];

							if ( $.isEmptyObject( plugins ) ) {
								// Onto the next step
								activate_plugins( _this );
							}
						},
						error: function( data ) {
							console.log(data);

							plugin_row.append( '<span class="plugin-error">' + data.errorMessage + '</span>' );
							plugin_row.removeClass( 'installing-plugins' ).addClass( 'plugin-install-failed' );
							plugin_row.removeClass( 'show-loading' ).next().addClass( 'show-loading' );

							// Remove current plugin from queue
							delete plugins[index];

							if ( $.isEmptyObject( plugins ) ) {
								// Onto the next step
								activate_plugins( _this );
							}
						}
					} );
				} else {
					// Remove current plugin from queue
					delete plugins[index];

					if ( $.isEmptyObject( plugins ) ) {
						// Onto the next step
						activate_plugins( _this );
					}
				}

			} );
		} else {
			download_content( _this );
		}
	} );

	function activate_plugins( _this ) {
		var siteBox = _this.closest( '.site-box' ),
			data = siteBox.data( 'site-data' );

		siteBox.find( '.site-message' ).text( generate_sites_params.activating_plugins );

		setTimeout( function() {
			$.ajax( {
				type: 'POST',
				url: generate_sites_params.ajaxurl,
				data: {
					action: 'generate_activate_plugins_' + data.slug,
					nonce: generate_sites_params.nonce,
				},
				success: function( data ) {
					console.log( data );
					download_content( _this );
				},
				error: function( data ) {
					console.log( data );
					siteBox.find( '.loading' ).hide();
					siteBox.find( '.error-message' ).html( data.status + ' ' + data.statusText + ' <a href="https://docs.generatepress.com/article/error-codes-importing/" target="_blank" rel="noopener">[?]</a>' ).show();
					siteBox.find( '.theme-options .big-loader' ).css( 'opacity', '0' );
					siteBox.find( '.theme-options .number' ).css( 'opacity', '1' );
				}
			} );
		}, 250 );
	}

	function download_content( _this ) {
		var siteBox = _this.closest( '.site-box' ),
			data = siteBox.data( 'site-data' );

		siteBox.find( '.site-message' ).text( generate_sites_params.downloading_content );

		$.ajax( {
			type: 'POST',
			url: generate_sites_params.ajaxurl,
			data: {
				action: 'generate_download_content_' + data.slug,
				nonce: generate_sites_params.nonce,
			},
			success: function( data ) {
				console.log( data );

				import_content( _this );
			},
			error: function( data ) {
				console.log( data );

				siteBox.find( '.loading' ).hide();
				siteBox.find( '.error-message' ).html( data.status + ' ' + data.statusText + ' <a href="https://docs.generatepress.com/article/error-codes-importing/" target="_blank" rel="noopener">[?]</a>' ).show();
				siteBox.find( '.theme-options .big-loader' ).css( 'opacity', '0' );
				siteBox.find( '.theme-options .number' ).css( 'opacity', '1' );
			}
		} );
	}

	function import_content( _this ) {
		var siteBox = _this.closest( '.site-box' ),
			data = siteBox.data( 'site-data' );

		siteBox.find( '.site-message' ).text( generate_sites_params.importing_content );

		$.ajax( {
			type: 'POST',
			url: generate_sites_params.ajaxurl,
			data: {
				action: 'generate_import_content_' + data.slug,
				nonce: generate_sites_params.nonce,
			},
			success: function( data ) {
				console.log( data );

				import_site_options( _this );
			},
			error: function( data ) {
				console.log( data );

				siteBox.find( '.loading' ).hide();
				siteBox.find( '.error-message' ).html( data.status + ' ' + data.statusText + ' <a href="https://docs.generatepress.com/article/error-codes-importing/" target="_blank" rel="noopener">[?]</a>' ).show();
				siteBox.find( '.theme-options .big-loader' ).css( 'opacity', '0' );
				siteBox.find( '.theme-options .number' ).css( 'opacity', '1' );
			}
		} );
	}

	/**
	 * Import site options.
	 * Comes last, as options may be dependent on plugins.
	 */
	function import_site_options( _this ) {
		var siteBox = _this.closest( '.site-box' ),
			data = siteBox.data( 'site-data' );

		siteBox.find( '.site-message' ).text( generate_sites_params.importing_site_options );

		setTimeout( function() {
			$.ajax( {
				type: 'POST',
				url: generate_sites_params.ajaxurl,
				data: {
					action: 'generate_import_site_options_' + data.slug,
					nonce: generate_sites_params.nonce,
				},
				success: function( data ) {
					console.log( data );

					if ( '1' == _this.data( 'widgets' ) ) {

						import_widgets( _this );

					} else {

						siteBox.find( '.loading' ).hide();
						siteBox.find( '.site-message' ).hide();
						siteBox.find( '.demo-content .big-loader' ).css( 'opacity', '0' );
						siteBox.find( '.demo-content .number' ).css( 'opacity', '1' ).addClass( 'step-complete' );
						_this.next( 'input' ).show();
						siteBox.find( '.import-complete .number' ).css( 'opacity', '1' ).addClass( 'step-complete' );

					}
				},
				error: function( data ) {
					console.log( data );

					siteBox.find( '.loading' ).hide();
					siteBox.find( '.error-message' ).html( data.status + ' ' + data.statusText + ' <a href="https://docs.generatepress.com/article/error-codes-importing/" target="_blank" rel="noopener">[?]</a>' ).show();
					siteBox.find( '.theme-options .big-loader' ).css( 'opacity', '0' );
					siteBox.find( '.theme-options .number' ).css( 'opacity', '1' );
				}
			} );
		}, 250 );
	}

	/**
	 * Import widgets.
	 */
	 function import_widgets( _this ) {
		 var siteBox = _this.closest( '.site-box' ),
 			data = siteBox.data( 'site-data' );

		 siteBox.find( '.site-message' ).text( generate_sites_params.importing_widgets );

		 setTimeout( function() {
 			$.ajax( {
 				type: 'POST',
 				url: generate_sites_params.ajaxurl,
 				data: {
 					action: 'generate_import_widgets_' + data.slug,
 					nonce: generate_sites_params.nonce,
 				},
 				success: function( data ) {
 					console.log( data );

					siteBox.find( '.loading' ).hide();
					siteBox.find( '.site-message' ).hide();
					siteBox.find( '.demo-content .big-loader' ).css( 'opacity', '0' );
					siteBox.find( '.demo-content .number' ).css( 'opacity', '1' ).addClass( 'step-complete' );
					_this.next( 'input' ).show();
					siteBox.find( '.import-complete .number' ).css( 'opacity', '1' ).addClass( 'step-complete' );
 				},
 				error: function( data ) {
 					console.log( data );

					siteBox.find( '.loading' ).hide();
					siteBox.find( '.error-message' ).html( data.status + ' ' + data.statusText + ' <a href="https://docs.generatepress.com/article/error-codes-importing/" target="_blank" rel="noopener">[?]</a>' ).show();
					siteBox.find( '.theme-options .big-loader' ).css( 'opacity', '0' );
					siteBox.find( '.theme-options .number' ).css( 'opacity', '1' );
				}
 			} );
 		}, 250 );
	 }

	 /**
	  * View our completed site.
	  */
	 $( '.site-box .view-site, .remove-site .view-site' ).on( 'click', function( e ) {
	 	e.preventDefault();

	 	window.location.href = generate_sites_params.home_url;
	 } );

	 function restoreThemeOptions() {
		 var restoreBox = $( '.remove-site' );

		 restoreBox.find( '.do-remove-site' ).hide()
		 restoreBox.find( '.skip-remove-site' ).hide();
		 restoreBox.find( '.loading' ).show();
		 restoreBox.find( '.remove-site-message' ).text( generate_sites_params.restoreThemeOptions );

		 $.ajax( {
			 type: 'POST',
			 url: generate_sites_params.ajaxurl,
			 data: {
				 action: 'generate_restore_theme_options',
				 nonce: generate_sites_params.nonce,
			 },
			 success: function( data ) {
				 if ( generate_sites_params.hasContentBackup || $( 'body' ).hasClass( 'site-import-content-exists' ) ) {
				 	restoreSiteOptions();
				} else {
					restoreCSS();
				}
				 console.log( data );
			 },
			 error: function( data ) {
				 console.log( data );
			 }
		 } );
	 }

	 function restoreSiteOptions() {
		 var restoreBox = $( '.remove-site' );

		 restoreBox.find( '.remove-site-message' ).text( generate_sites_params.restoreSiteOptions );

		 $.ajax( {
			 type: 'POST',
			 url: generate_sites_params.ajaxurl,
			 data: {
				 action: 'generate_restore_site_options',
				 nonce: generate_sites_params.nonce,
			 },
			 success: function( data ) {
				 restoreContent();
				 console.log( data );
			 },
			 error: function( data ) {
				 console.log( data );
			 }
		 } );
	 }

	 function restoreContent() {
		 var restoreBox = $( '.remove-site' );

		 restoreBox.find( '.remove-site-message' ).text( generate_sites_params.restoreContent );

		 $.ajax( {
			 type: 'POST',
			 url: generate_sites_params.ajaxurl,
			 data: {
				 action: 'generate_restore_content',
				 nonce: generate_sites_params.nonce,
			 },
			 success: function( data ) {
				 restorePlugins();
				 console.log( data );
			 },
			 error: function( data ) {
				 console.log( data );
			 }
		 } );
	 }

	 function restorePlugins() {
		 var restoreBox = $( '.remove-site' );

		 restoreBox.find( '.remove-site-message' ).text( generate_sites_params.restorePlugins );

		 $.ajax( {
			 type: 'POST',
			 url: generate_sites_params.ajaxurl,
			 data: {
				 action: 'generate_restore_plugins',
				 nonce: generate_sites_params.nonce,
			 },
			 success: function( data ) {
				 restoreWidgets();
				 console.log( data );
			 },
			 error: function( data ) {
				 console.log( data );
			 }
		 } );
	 }

	 function restoreWidgets() {
		 var restoreBox = $( '.remove-site' );

		 restoreBox.find( '.remove-site-message' ).text( generate_sites_params.restoreWidgets );

		 $.ajax( {
			 type: 'POST',
			 url: generate_sites_params.ajaxurl,
			 data: {
				 action: 'generate_restore_widgets',
				 nonce: generate_sites_params.nonce,
			 },
			 success: function( data ) {
				 restoreCSS();
				 console.log( data );
			 },
			 error: function( data ) {
				 console.log( data );
			 }
		 } );
	 }

	 function restoreCSS() {
		 var restoreBox = $( '.remove-site' );

		 restoreBox.find( '.remove-site-message' ).text( generate_sites_params.restoreCSS );

		 $.ajax( {
			 type: 'POST',
			 url: generate_sites_params.ajaxurl,
			 data: {
				 action: 'generate_restore_css',
				 nonce: generate_sites_params.nonce,
			 },
			 success: function( data ) {
				 cleanUp();
				 console.log( data );
			 },
			 error: function( data ) {
				 console.log( data );
			 }
		 } );
	 }

	 function cleanUp() {
		 var restoreBox = $( '.remove-site' );

		 restoreBox.find( '.remove-site-message' ).text( generate_sites_params.cleanUp );

		 $.ajax( {
			 type: 'POST',
			 url: generate_sites_params.ajaxurl,
			 data: {
				 action: 'generate_restore_site_clean_up',
				 nonce: generate_sites_params.nonce,
			 },
			 success: function( data ) {
				 restoreBox.find( '.loading' ).hide();
				 restoreBox.hide();

				 $( '.generatepress-sites' ).removeClass( 'remove-site-needed' );
				 $( 'body' ).removeClass( 'site-import-content-exists' );
				 $( 'body' ).removeClass( 'site-import-data-exists' );
				 console.log( data );
			 },
			 error: function( data ) {
				 console.log( data );
			 }
		 } );
	 }

	 $( '.do-remove-site' ).on( 'click', function( e ) {
		 e.preventDefault();

		 if ( confirm( generate_sites_params.confirmRemoval ) ) {
		 	restoreThemeOptions();
		}
	 } );

	 $( '.skip-remove-site' ).on( 'click', function( e ) {
		 e.preventDefault();

		 $( '.remove-site' ).hide();
		 $( '.generatepress-sites' ).removeClass( 'remove-site-needed' );
		 $( 'body' ).removeClass( 'site-import-content-exists' );
		 $( 'body' ).removeClass( 'site-import-data-exists' );
	 } );
} );
