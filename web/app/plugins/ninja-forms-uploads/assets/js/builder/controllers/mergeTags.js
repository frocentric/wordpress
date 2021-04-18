var fileUploadsMergeTagsController = Marionette.Object.extend( {
	initialize: function() {
		this.listenTo( Backbone.Radio.channel( 'app' ), 'after:appStart', this.afterNFLoad );
	},

	afterNFLoad: function() {
		this.listenTo( Backbone.Radio.channel( 'fields' ), 'add:field', this.addFieldTags );
		this.listenTo( Backbone.Radio.channel( 'fields' ), 'delete:field', this.deleteFieldTags );
		this.listenTo( Backbone.Radio.channel( 'actions' ), 'update:setting', this.maybeAlterExternalFieldTags );
		this.listenTo( Backbone.Radio.channel( 'fields' ), 'update:setting', this.maybeAlterAttachmentFieldTags );
		this.listenTo( Backbone.Radio.channel( 'fieldSetting-key' ), 'update:setting', this.maybeAlterFieldTags );
		this.listenTo( Backbone.Radio.channel( 'fieldSetting-label' ), 'update:setting', this.maybeAlterFieldTags );

		var fieldCollection = Backbone.Radio.channel( 'fields' ).request( 'get:collection' );
		var that = this;
		var fileUploadTags = this.getFileUploadTags();

		_.each( fieldCollection.models, function( field ) {
			if ( 'file_upload' !== field.get( 'type' ) ) {
				return;
			}
			var variants = that.getMergeTagVariants( field );
			that.addField( field, fileUploadTags, variants );
		} );
	},

	getFieldwithExternalService: function() {
		var actionCollection = Backbone.Radio.channel( 'actions' ).request( 'get:collection' );

		var externalActions = _.filter( actionCollection.models, function( model ) {
			return model.get( 'type' ) === 'file-upload-external';
		} );

		var fieldsWithExternalServices = {};
		_.each( externalActions, function( action ) {
			_.each( action.attributes, function( value, key ) {
				if ( value == "1" && key.indexOf( 'field_list_' ) === 0 ) {
					key = key.replace( 'field_list_', '' );
					var keyParts = key.split( '-' );
					var service = keyParts.shift();
					var field = keyParts.join('-');
					if ( !fieldsWithExternalServices.hasOwnProperty( field ) ) {
						fieldsWithExternalServices[ field ] = [];
					}

					if ( !fieldsWithExternalServices[ field ].includes( service ) ) {
						fieldsWithExternalServices[ field ].push( service );
					}
				}
			} );
		} );

		return fieldsWithExternalServices;
	},

	getFileUploadTags: function() {
		var mergeTagCollection = Backbone.Radio.channel( 'mergeTags' ).request( 'get:collection' );

		return mergeTagCollection.get( 'file_uploads' ).get( 'tags' );
	},

	getMergeTagVariants: function( field, withAttachmentVariants ) {
		var variants = nfFileUploadsAdmin.mergeTagVariants;

		if ( (typeof withAttachmentVariants !== 'undefined' && !withAttachmentVariants) || 'false' == field.get( 'media_library' ) ) {
			variants = variants.filter( function( variant ) {
				return variant.indexOf( 'attachment_' ) < 0;
			} );
		}

		var externalVariants = this.addExternalMergeTagVariants( field.get( 'key' ) );

		variants = _.union( variants, externalVariants );

		return variants;
	},

	addExternalMergeTagVariants: function( fieldKey ) {
		var fieldsWithExternalServices = this.getFieldwithExternalService();
		var externalServices = fieldsWithExternalServices[ fieldKey ];
		var variants = [];
		if ( externalServices !== undefined ) {
			_.each( externalServices, function( externalService ) {
				variants.push( externalService );
				variants.push( externalService + '_plain' );
			} );
		}

		return variants;
	},

	addField: function( field, fileUploadTags, variants ) {
		if ( 'file_upload' !== field.get( 'type' ) ) {
			return;
		}

		var that = this;
		_.each( variants, function( variant ) {
			fileUploadTags.add( {
				id: field.get( 'id' ) + '_' + variant,
				label: field.get( 'label' ) + ' ' + variant,
				tag: that.getFieldKeyFormat( field.get( 'key' ), variant )
			} );
		} );
	},

	addFieldTags: function( fieldModel, withAttachmentVariants ) {
		var fileUploadTags = this.getFileUploadTags();
		var variants = this.getMergeTagVariants( fieldModel, withAttachmentVariants );
		this.addField( fieldModel, fileUploadTags, variants );
	},

	deleteFieldTags: function( fieldModel ) {
		var fieldID = fieldModel.get( 'id' );
		var fileUploadTags = this.getFileUploadTags();
		var variants = this.getMergeTagVariants( fieldModel, true );
		_.each( variants, function( variant ) {
			var ID = fieldID + '_' + variant;
			var tagModel = fileUploadTags.get( ID );
			fileUploadTags.remove( tagModel );
		} );
	},

	maybeAlterFieldTags: function( field, settingModel ) {
		if ( typeof settingModel === 'undefined' ) {
			return;
		}

		if ( field.get( 'type' ) !== 'file_upload' ) {
			return;
		}

		this.deleteFieldTags( field );
		this.addFieldTags( field );
	},

	maybeAlterAttachmentFieldTags: function( field, settingModel ) {
		if ( typeof settingModel === 'undefined' ) {
			return;
		}

		if ( field.get( 'type' ) !== 'file_upload' ) {
			return;
		}

		if ( settingModel.get( 'name' ) !== 'media_library' ) {
			return;
		}

		var isChecked = jQuery( '#' + settingModel.get( 'name' ) ).is( ':checked' );

		this.deleteFieldTags( field );
		this.addFieldTags( field, isChecked );
	},

	maybeAlterExternalFieldTags: function( action, settingModel ) {
		if ( typeof settingModel === 'undefined' ) {
			return;
		}

		if ( action.get( 'type' ) !== 'file-upload-external' ) {
			return;
		}

		if ( settingModel.get( 'type' ) !== 'toggle' ) {
			return;
		}

		var isChecked = jQuery( '#' + settingModel.get( 'name' ) ).is( ':checked' );

		this.alterExternalFieldTags( settingModel.get( 'name' ), isChecked );
	},

	alterExternalFieldTags: function( settingKey, enabled ) {
		var fieldCollection = Backbone.Radio.channel( 'fields' ).request( 'get:collection' );

		settingKey = settingKey.replace( 'field_list_', '' );
		var keyParts = settingKey.split( '-' );
		var externalService = keyParts[ 0 ];
		var fieldKey = keyParts[ 1 ];
		var fieldModel = _.find( fieldCollection.models, function( field ) {
			if ( field.get( 'key' ) === fieldKey ) {
				return field;
			}
		} );

		var that = this;
		var variants = [];
		variants.push( externalService );
		variants.push( externalService + '_plain' );

		var fieldID = fieldModel.get( 'id' );

		var fileUploadTags = this.getFileUploadTags();
		_.each( variants, function( variant ) {
			var ID = fieldID + '_' + variant;
			var tagModel = fileUploadTags.get( ID );
			fileUploadTags.remove( tagModel );
			if ( enabled ) {
				that.addField( fieldModel, fileUploadTags, variants );
			}
		} );
	},

	getFieldKeyFormat: function( key, variant ) {
		return '{field:' + key + ':' + variant + '}';
	}
} );

jQuery( document ).ready( function( $ ) {
	new fileUploadsMergeTagsController();
} );