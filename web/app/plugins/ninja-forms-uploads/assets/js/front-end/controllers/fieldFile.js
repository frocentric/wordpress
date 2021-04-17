(function( $ ) {
	var nfRadio = Backbone.Radio;
	var radioChannel = nfRadio.channel( 'file_upload' );

	var fileModel = Backbone.Model.extend( {
		id: 0,
		name: '',
		tmpName: '',
		fieldID: 0
	} );

	var FileCollection = Backbone.Collection.extend( {
		model: fileModel
	} );

	var fileView = Marionette.ItemView.extend( {
		tagName: 'nf-section',
		template: '#tmpl-nf-field-file-row',

		events: {
			'click .delete': 'clickDelete'
		},

		clickDelete: function( event ) {
			radioChannel.trigger( 'click:deleteFile', event, this.model );
		}

	} );

	var fileCollectionView = Marionette.CollectionView.extend( {
		childView: fileView
	} );

	var uploadController = Marionette.Object.extend( {

		jqXHR: [],
		$progress_bars: [],

		initialize: function() {
			this.listenTo( radioChannel, 'init:model', this.initFile );
			this.listenTo( radioChannel, 'render:view', this.initFileUpload );
			this.listenTo( radioChannel, 'click:deleteFile', this.deleteFile );
			radioChannel.reply( 'validate:required', this.validateRequired );
			radioChannel.reply( 'get:submitData', this.getSubmitData );
		},

		initFile: function( model ) {
			model.set( 'uploadMulti', 1 != model.get( 'upload_multi_count' ) ) ;
		},

		renderView: function( view ) {
			var el = $( view.el ).find( '.files_uploaded' );
			view.fileCollectionView = new fileCollectionView( {
				el: el,
				collection: view.model.get( 'files' ),
				thisModel: this.model
			} );

			view.model.bind( 'change:files', this.changeCollection, view );

			/*
			 * This radio responder is only necessary if we have Multi-Part Forms active.
			 * Thankfully, it won't fire if the add-on isn't active.
			 *
			 * When we change our parts in a Multi-Part Form, re-render our file collection.
			 */
			view.listenTo( nfRadio.channel( 'nfMP' ), 'change:part', this.changeCollection, view );
		},

		changeCollection: function() {
			this.fileCollectionView.render();
		},

		getFieldID: function( e ) {
			var $parent = $( e.target ).parents( '.field-wrap' );

			return $parent.data( 'field-id' );
		},

		getProgressBar: function( e ) {
			var fieldID = this.getFieldID( e );

			return this.$progress_bars[ fieldID ];
		},

		resetProgress: function( e ) {
			var self = this;
			setTimeout( function() {
				self.getProgressBar( e ).css( 'width', 0 );
			}, 1500 );
		},

		checkFilesLimit: function( view, e, data ) {
			var limit = view.model.get( 'upload_multi_count' );

			if ( 1 == limit ) {
				return true;
			}

			var files = view.model.get( 'files' );

			if ( ( files && files.length >= limit ) || data.files.length > limit ) {
				var error_msg = nf_upload.strings.file_limit.replace( '%n', limit );
				alert( error_msg );
				return false;
			}

			return true;
		},

		maybeSubmitFieldData: function( data ) {
			if ( data.paramName === 'files-' + data.formData.field_id || data.paramName === 'files-' + data.formData.field_id + '[]' ) {
				this.jqXHR[ data.formData.field_id ] = data.submit();
			}
		},

		showError: function( error, view ) {
			nfRadio.channel( 'fields' ).request( 'add:error', view.model.id, 'upload-file-error', error );
		},

		resetError: function( view, fieldID  ) {
			nfRadio.channel( 'fields' ).request( 'remove:error', fieldID, 'upload-file-error' );
			this.$progress_bars[ fieldID ].css( 'width', 0 );
			$( view.el ).find( '.nf-fu-button-cancel' ).hide();
			var formID = view.model.get( 'formID' );
			nfRadio.channel( 'form-' + formID ).trigger( 'enable:submit', view.model );
		},

		cancelUpload: function( e ) {
			var fieldID = e.data.view.model.id;
			e.data.controller.jqXHR[ fieldID ].abort();
			e.data.controller.resetError( e.data.view, fieldID );
		},

		isNonceValid: function( view ) {
			var nonceExpiry = view.model.get( 'uploadNonceExpiry' );
			if ( typeof nonceExpiry === 'undefined' || nonceExpiry === null ) {
				return false
			}

			var now = Math.round( (new Date()).getTime() / 1000 );

			return now < nonceExpiry;
		},

		getValidNonce: function( field_id ) {
			return jQuery.post( {
				url: nfFrontEnd.adminAjax + '?action=nf_fu_get_new_nonce',
				type: 'POST',
				data: {
					'field_id': field_id
				},
				cache: false
			} );
		},

		initFileUpload: function( view ) {
			var fieldID = view.model.id;
			var formID = view.model.get( 'formID' );
			var nonce = view.model.get( 'uploadNonce' );
			var $file = $( view.el ).find( '.nf-element' );
			var $files_uploaded = $( view.el ).find( '.files_uploaded' );
			this.$progress_bars[ fieldID ] = $( view.el ).find( '.nf-fu-progress-bar' );
			var url = nfFrontEnd.adminAjax + '?action=nf_fu_upload';
			var self = this;
			var files = view.model.get( 'files' );

			$( view.el ).find( 'button.nf-fu-button-cancel' ).on( 'click', { view: view, controller: self }, self.cancelUpload );

			$( view.el ).find( 'button.nf-fu-fileinput-button' ).on( 'click', function() {
				// Don't show required validation error on click of the file upload button
				view.model.set( 'firstTouch', false );
			} );

			/*
			 * Make sure that our files array isn't undefined.
			 * If it is, set it to an empty array.
			 */
			files = files || [];

			/*
			 * If "files" isn't a collection, turn it into one.
			 */
			if ( ! ( files instanceof FileCollection ) ) {
				files = new FileCollection( files );
				view.model.set( 'files', files );
			}

			this.renderView( view );

			var formData = {
				form_id: formID,
				field_id: fieldID,
				nonce: nonce,
				abort: false
			};

			$file.fileupload( {
				url: url,
				dataType: 'json',
				messages: {
					maxFileSize: nf_upload.strings.max_file_size_error.replace( '%n', view.model.get( 'max_file_size_mb' ) ),
					minFileSize: nf_upload.strings.min_file_size_error.replace( '%n', view.model.get( 'min_file_size_mb' ) )
				},
				maxChunkSize: view.model.get( 'max_chunk_size' ),
				dropZone: $( view.el ),
				maxFileSize: view.model.get( 'max_file_size' ),
				minFileSize: view.model.get( 'min_file_size' ),
				add: function (e, data) {
					$( view.el ).find( '.nf-fu-button-cancel' ).show();
					formData.abort = false;
					data.formData = formData;
					if ( self.isNonceValid( view ) ) {
						self.maybeSubmitFieldData( data );

						return;
					}

					self.getValidNonce( formData.field_id ).done( function( response ) {
						if ( !response.success ) {
							self.showError( nf_upload.strings.upload_nonce_error, view );
							return false;
						}

						view.model.set( 'uploadNonce', response.data.nonce );
						view.model.set( 'uploadNonceExpiry', response.data.nonce_expiry );

						data.formData.nonce = response.data.nonce;
						self.maybeSubmitFieldData( data );
					} );
				},
				change: function( e, data ) {
					if ( !self.checkFilesLimit( view, e, data ) ) {
						return false;
					}
					// Remove any errors on this field.
					nfRadio.channel( 'fields' ).request( 'remove:error', view.model.get( 'id' ), 'required-error' );
				},
				drop: function( e, data ) {
					if ( !self.checkFilesLimit( view, e, data ) ) {
						return false;
					}
				},
				done: function( e, data ) {
					if ( !data.result || data.result === undefined ) {
						self.showError( nf_upload.strings.unknown_upload_error, view );
						self.resetProgress( e );
						return;
					}

					if ( -1 === data.result ) {
						self.showError( nf_upload.strings.upload_error, view );
						self.resetProgress( e );
						return;
					}

					// Check for errors
					if ( data.result.errors.length ) {
						$.each( data.result.errors, function( index, error ) {
							self.showError( error, view );
						} );
					}

					if ( data.result.data.files === undefined || !data.result.data.files.length ) {
						self.resetProgress( e );

						return;
					}

					var allowed = view.model.get( 'upload_multi_count' );
					var limit = 1;

					if ( 1 != allowed ) {
						var uploaded = view.model.get( 'files' ).length;
						limit = allowed - uploaded;

						if ( limit <= 0 ) {
							var error_msg = nf_upload.strings.file_limit.replace( '%n', allowed );
							self.showError( error_msg, view );
							self.resetProgress( e );
							return;
						}
					}

					$( view.el ).find( '.nf-fu-button-cancel' ).hide();

					var count = 0;
					$.each( data.result.data.files, function( index, file ) {
						count++;
						if ( count > limit ) {
							return false;
						}
						files.add( new fileModel( { name: file.name, tmp_name: file.tmp_name, fieldID: fieldID } ) );
					} );

					view.model.set( 'files', files );
					view.model.trigger( 'change:files', view.model );
					view.model.set( 'value', 1 );

					self.resetProgress( e );

					nfRadio.channel( 'fields' ).trigger( 'change:field', view.el, view.model );
					nfRadio.channel( 'form-' + formID ).trigger( 'enable:submit', view.model );
				},
				start: function() {
					if ( 1 == view.model.get( 'upload_multi_count' ) ) {
						// Remove the files uploaded display and reset the collection
						$files_uploaded.empty();
						files.reset();
					}
					nfRadio.channel( 'fields' ).request( 'remove:error', view.model.id, 'upload-file-error' );
					nfRadio.channel( 'form-' + formID ).trigger( 'disable:submit', view.model );
				},
				progressall: function( e, data ) {
					var progress = parseInt( data.loaded / data.total * 100, 10 );
					self.getProgressBar( e ).css( 'width', progress + '%' );
				}
			} ).on( 'fileuploadprocessalways', function( e, data ) {
				var index = data.index,
					file = data.files[ index ];
				if ( file.error ) {
					nfRadio.channel( 'fields' ).request( 'add:error', view.model.id, 'upload-file-error', file.error );
				}
			} ).on( 'fileuploadchunkdone', function( e, data ) {
				// Check for errors
				if ( data.result.errors.length ) {
					$.each( data.result.errors, function( index, error ) {
						self.showError( error, view );
					} );

					self.jqXHR[ formData.field_id ].abort();
					self.$progress_bars[  formData.field_id ].css( 'width', 0 );
					formData.abort = true;
					return;
				}

				var key = data.result.data.files[ 0 ][ 'new_tmp_key' ];
				formData[ key ] = data.result.data.files[ 0 ][ 'tmp_name' ];
			} ).on( "fileuploadchunksend", function( e, data ) {
				if ( formData.abort === true ) {
					return false;
				}
			} )
				.prop( 'disabled', !$.support.fileInput )
				.parent().addClass( $.support.fileInput ? undefined : 'disabled' );
		},

		getSubmitData: function( fieldData, field ) {
			fieldData.files = field.get( 'files' );

			return fieldData;
		},

		deleteFile: function( event, model ) {
			event.preventDefault();
			model.collection.remove( model );
			// send off AJAX request to delete temp file or uploaded file

			var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', model.get( 'fieldID' ) );
			nfRadio.channel( 'fields' ).trigger( 'change:field', '', fieldModel );
			// Remove any errors on this field.
			nfRadio.channel( 'fields' ).request( 'remove:error', fieldModel, 'required-error' );
		},

		/**
		 * Check files have been submitted successfully for required field check
		 *
		 * @param el
		 * @param model
		 * @returns {boolean}
		 */
		validateRequired: function( el, model ) {
			if ( !model.get( 'firstTouch' ) ) {
				model.set( 'firstTouch', true );
				return true;
			}

			var files = model.get( 'files' );
			if ( typeof files === 'undefined' || !files.length ) {
				model.set( 'value', '' );
				return false;
			}

			return true;
		}

	} );

	new uploadController();

	$( document ).ready( function() {
		$( 'body' ).on( 'click', 'button.nf-fu-fileinput-button', function( e ) {
			$( this ).parent().find( 'input.nf-element' ).click();
		} );

		$( document ).bind( 'drop dragover', function( e ) {
			e.preventDefault();
		} );
	} );
})( jQuery );