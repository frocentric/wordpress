jQuery(window).on('elementor/frontend/init', () => {

    class WidgetQueryTableHandlerClass extends elementorModules.frontend.handlers.Base {
        getDefaultSettings() {
            // e-add-posts-container e-add-posts e-add-skin-grid e-add-skin-grid-masonry
            return {
                selectors: {
                    table: 'table',
                },
            };
        }

        getDefaultElements() {
            const selectors = this.getSettings('selectors');

            return {
                $scope: this.$element,
                $id_scope: this.getID(),
                $table: this.$element.find(selectors.table),
            };
        }

        initDataTables() {
            let scope = this.elements.$scope,
                    table = this.elements.$table;

            this.elementSettings = this.getElementSettings();

            var args = {
                order: [],
                dom: 'Bfrtip',
                info: Boolean(this.elementSettings['table_info']),
                fixedHeader: Boolean(this.elementSettings['table_fixed_header']),
                responsive: Boolean(this.elementSettings['table_responsive']),
                //scrollX: Boolean(this.elementSettings['table_scrollx']),
                searching: Boolean(this.elementSettings['table_searching']),         
                ordering: Boolean(this.elementSettings['table_ordering']),
                paging: false,
            };

            if (Boolean(this.elementSettings['table_buttons'])) {
                args['buttons'] = [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ];
            } else {
                args['buttons'] = [];
            }
            
            
                       
            if (this.elementSettings['table_searching'] !== 'undefined') {                                
                if (this.elementSettings['table_searching'] == 'fields' || this.elementSettings['table_searching'] == 'both') {
                    // Setup - add a text input to each footer cell
                    table.find('thead th').each( function () {
                        var title = jQuery(this).text();
                        jQuery(this).html( '<input type="text" placeholder="'+title+'" />' );
                    } );                    
                    args['initComplete'] = function () {
                        // Apply the search
                        this.api().columns().every( function () {
                            var that = this;
                            jQuery( 'input', this.header() ).on( 'keyup change clear', function () {
                                if ( that.search() !== this.value ) {
                                    that
                                        .search( this.value )
                                        .draw();
                                }
                            } );
                        } );
                    }
                }
                if (this.elementSettings['table_searching'] == 'general' || this.elementSettings['table_searching'] == 'both') {
                    args['language'] = {
                        search: "_INPUT_",
                        searchPlaceholder: "Search..."
                    }
                } else {
                    //args['searching'] = false;
                }
            }
            
            if (this.elementSettings['table_language'] !== 'undefined' && this.elementSettings['table_language']) {              
                let lng = this.elementSettings['table_language']; 
                let lng_url = lng;
                if (!lng.startsWith('http') && !lng.startsWith('//')) {
                    lng_url = "//cdn.datatables.net/plug-ins/1.11.0/i18n/"+lng+".json";
                }
                /*if (args['language']) {
                    args.language.url = lng_url;
                } else {*/
                    args['language'] = { "url": lng_url };
                //}
            }
            
            //console.log(args);
            var dtable = table.DataTable(args);
            
            if (this.elementSettings['table_searching'] == 'fields') {
                scope.find('.dataTables_filter').hide();
            }
        }

        bindEvents() {
            this.skinPrefix = this.$element.attr('data-widget_type').split('.').pop() + '_';
            this.elementSettings = this.getElementSettings();

            let scope = this.elements.$scope,
                    id_scope = this.elements.$id_scope;

            if (this.elementSettings['table_datatables']) {
                this.initDataTables()
            }

        }

    }

    const Widget_EADD_Query_table_Handler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(WidgetQueryTableHandlerClass, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-posts.table', Widget_EADD_Query_table_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-users.table', Widget_EADD_Query_table_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-terms.table', Widget_EADD_Query_table_Handler);
    //elementorFrontend.hooks.addAction('frontend/element_ready/e-query-itemslist.table', Widget_EADD_Query_table_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-media.table', Widget_EADD_Query_table_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-repeater.table', Widget_EADD_Query_table_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-products.table', Widget_EADD_Query_table_Handler);
    // comments   
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-rest-api.table', Widget_EADD_Query_table_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-db.table', Widget_EADD_Query_table_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-data-listing.table', Widget_EADD_Query_table_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-xml.table', Widget_EADD_Query_table_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-spreadsheet.table', Widget_EADD_Query_table_Handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/e-query-rss.table', Widget_EADD_Query_table_Handler);
});