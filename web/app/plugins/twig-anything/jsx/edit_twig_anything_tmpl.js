'use strict';

var TwigAnythingEmptyReactClass = React.createClass({
    displayName: 'TwigAnythingEmptyReactClass',

    render: function render() {
        return React.createElement('div', null);
    }
});

var DataSourceMetaBox = React.createClass({
    displayName: 'DataSourceMetaBox',

    propTypes: {
        dataSources: React.PropTypes.array.isRequired,
        formats: React.PropTypes.array.isRequired,
        wpHomePath: React.PropTypes.string.isRequired
    },
    getInitialState: function getInitialState() {
        return {
            source_type: 'empty',
            format: 'raw',
            cache_seconds: '',

            on_data_error: 'use_cache_or_display_nothing'
        };
    },
    setDataSourceState: function setDataSourceState(state, callback) {
        if (!callback) {
            callback = function callback() {};
        }
        this.refs['data_source_settings'].setState(state, callback);
    },
    setFormatState: function setFormatState(state, callback) {
        if (!callback) {
            callback = function callback() {};
        }
        this.refs['format_settings'].setState(state, callback);
    },
    handleSourceTypeChange: function handleSourceTypeChange(e) {
        var newSourceType = e.target.value;
        var newState = {
            'source_type': newSourceType
        };
        if (newSourceType == 'empty') {
            newState.format = 'raw';
            newState.cache_seconds = '';
        }
        this.setState(newState);
    },
    handleFormatChange: function handleFormatChange(e) {
        this.setState({ format: e.target.value });
    },
    handleCacheSecondsChange: function handleCacheSecondsChange(e) {
        this.setState({ cache_seconds: e.target.value });
    },
    handleOnDataErrorChange: function handleOnDataErrorChange(e) {
        this.setState({ on_data_error: e.target.value });
    },
    render: function render() {
        var dataSourceReactClass = window['TwigAnythingDataSource_' + this.state.source_type];
        if (typeof dataSourceReactClass == "undefined") {
            dataSourceReactClass = TwigAnythingEmptyReactClass;
        }
        var dataSourceSettings = React.createElement(dataSourceReactClass, {
            ref: 'data_source_settings',
            wpHomePath: this.props.wpHomePath
        });

        var formatReactClass = window['TwigAnythingFormat_' + this.state.format];
        if (typeof formatReactClass == "undefined") {
            formatReactClass = TwigAnythingEmptyReactClass;
        }
        var formatSettings = React.createElement(formatReactClass, {
            ref: 'format_settings',
            wpHomePath: this.props.wpHomePath
        });

        var i;
        var dataSourceOptions = [];
        var dataSourceDescription = '';
        for (i = 0; i < this.props.dataSources.length; i++) {
            var ds = this.props.dataSources[i];
            dataSourceOptions.push(React.createElement(
                'option',
                { key: ds.slug, value: ds.slug },
                ds.longName
            ));
            if (ds.slug == this.state.source_type) {
                dataSourceDescription = ds.description;
            }
        }

        var formatsOptions = [];
        var formatDescription = '';
        for (i = 0; i < this.props.formats.length; i++) {
            var format = this.props.formats[i];
            formatsOptions.push(React.createElement(
                'option',
                { key: format.slug, value: format.slug },
                format.longName
            ));
            if (format.slug == this.state.format) {
                formatDescription = format.description;
            }
        }

        return React.createElement(
            'div',
            null,
            React.createElement(
                'table',
                { className: 'form-table' },
                React.createElement(
                    'tbody',
                    null,
                    React.createElement(
                        'tr',
                        null,
                        React.createElement(
                            'th',
                            { scope: 'row' },
                            React.createElement(
                                'label',
                                { htmlFor: 'twig_anything_source_type' },
                                'Source Type'
                            )
                        ),
                        React.createElement(
                            'td',
                            null,
                            React.createElement(
                                'select',
                                {
                                    name: 'twig_anything_source_type',
                                    id: 'twig_anything_source_type',
                                    value: this.state.source_type,
                                    onChange: this.handleSourceTypeChange },
                                dataSourceOptions
                            ),
                            React.createElement(
                                'p',
                                { className: 'description' },
                                dataSourceDescription
                            )
                        )
                    ),
                    React.createElement(
                        'tr',
                        null,
                        React.createElement(
                            'th',
                            { scope: 'row' },
                            React.createElement(
                                'label',
                                { htmlFor: 'twig_anything_format' },
                                'Data Format'
                            )
                        ),
                        React.createElement(
                            'td',
                            null,
                            React.createElement(
                                'select',
                                {
                                    name: 'twig_anything_format',
                                    id: 'twig_anything_format',
                                    value: this.state.format,
                                    onChange: this.handleFormatChange },
                                formatsOptions
                            ),
                            React.createElement(
                                'p',
                                { className: 'description' },
                                formatDescription
                            )
                        )
                    ),
                    React.createElement(
                        'tr',
                        null,
                        React.createElement(
                            'th',
                            { scope: 'row' },
                            React.createElement(
                                'label',
                                { htmlFor: 'twig_anything_cache_seconds' },
                                'Cache lifetime in seconds'
                            )
                        ),
                        React.createElement(
                            'td',
                            null,
                            React.createElement('input', {
                                name: 'twig_anything_cache_seconds',
                                id: 'twig_anything_cache_seconds',
                                value: this.state.cache_seconds,
                                onChange: this.handleCacheSecondsChange }),
                            React.createElement(
                                'p',
                                { className: 'description' },
                                'Leave empty to avoid both reading from and writing to cache'
                            )
                        )
                    ),
                    React.createElement(
                        'tr',
                        null,
                        React.createElement(
                            'th',
                            { scope: 'row' },
                            React.createElement(
                                'label',
                                { htmlFor: 'twig_anything_on_data_error' },
                                'Data errors handling'
                            )
                        ),
                        React.createElement(
                            'td',
                            null,
                            React.createElement(
                                'select',
                                {
                                    name: 'twig_anything_on_data_error',
                                    id: 'twig_anything_on_data_error',
                                    value: this.state.on_data_error,
                                    onChange: this.handleOnDataErrorChange },
                                React.createElement(
                                    'option',
                                    { value: 'use_cache_or_display_nothing' },
                                    'Use cache or display nothing'
                                ),
                                React.createElement(
                                    'option',
                                    { value: 'use_cache_or_display_error' },
                                    'Use cache or display error'
                                ),
                                React.createElement(
                                    'option',
                                    { value: 'always_display_error' },
                                    'Always display error'
                                )
                            ),
                            React.createElement(
                                'p',
                                { className: 'description' },
                                'What to do if data cannot be retrieved or parsed?',
                                React.createElement('br', null),
                                '\xB7 ',
                                React.createElement(
                                    'code',
                                    null,
                                    'use cache or display nothing'
                                ),
                                ' - try using data from out-of-date cache if it exists, otherwise display nothing (an empty string)',
                                React.createElement('br', null),
                                '\xB7 ',
                                React.createElement(
                                    'code',
                                    null,
                                    'use cache or display error'
                                ),
                                ' - try using data from out-of-date cache if it exists, otherwise display an error message',
                                React.createElement('br', null),
                                '\xB7 ',
                                React.createElement(
                                    'code',
                                    null,
                                    'always display error'
                                ),
                                ' - always display an error message'
                            )
                        )
                    )
                )
            ),
            dataSourceSettings,
            formatSettings
        );
    }
});

jQuery(function () {
    jQuery("#wp-word-count").after(jQuery("<td>").attr('id', 'twig-anything-hotkeys-hint').append(jQuery("<strong>").text("Ctrl-Enter"), jQuery("<span>").text(" - fullscreen mode")));

    TwigAnythingCodeMirrorWrapper.CodeMirror.defineMode("twig_html", function (config) {
        return TwigAnythingCodeMirrorWrapper.CodeMirror.multiplexingMode(TwigAnythingCodeMirrorWrapper.CodeMirror.getMode(config, "text/html"), {
            open: /\{[\{%#]/, close: /[}%#]\}/,
            mode: TwigAnythingCodeMirrorWrapper.CodeMirror.getMode(config, "twig"),
            parseDelimiters: true
        });
    });

    var contentCodeMirror = TwigAnythingCodeMirrorWrapper.CodeMirror.fromTextArea(document.getElementById("content"), {
        mode: "twig_html",
        lineNumbers: true,
        indentUnit: 2,
        tabSize: 2,
        indentWithTabs: false,
        viewportMargin: Infinity,
        placeholder: "Enter your HTML template here...",
        extraKeys: {
            "Ctrl-Enter": function CtrlEnter(cm) {
                cm.setOption("fullScreen", !cm.getOption("fullScreen"));
            },
            "Esc": function Esc(cm) {
                if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
            },

            "Tab": function Tab(cm) {
                cm.replaceSelection('  ');
            }
        }
    });

    var input = twigAnythingDataSourceMetaBoxInputData || {
        commonSettings: {},
        dataSourceSettings: {},
        formatSettings: {},
        dataSourcesMeta: [],
        formatsMeta: [],
        wpHomePath: ''
    };

    ReactDOM.render(React.createElement(DataSourceMetaBox, {
        dataSources: input.dataSourcesMeta,
        formats: input.formatsMeta,
        wpHomePath: input.wpHomePath }), document.getElementById('data_source_react_container'), function () {
        var rootComponent = this;

        function updateFormatState() {
            if (input.formatSettings) {
                rootComponent.setFormatState(input.formatSettings);
            }
        }

        {}
        if (input.commonSettings) {
            rootComponent.setState(input.commonSettings, function () {

                {}

                {}
                if (input.dataSourceSettings) {
                    rootComponent.setDataSourceState(input.dataSourceSettings, function () {
                        updateFormatState();
                    });
                } else {
                    {}
                    updateFormatState();
                }
            });
        }
    });

    var clipboardClient = new Clipboard('#twig-anything-copy-shortcode-to-clipboard');

    clipboardClient.on('success', function (e) {
        e.clearSelection();

        jQuery("#twig-anything-copy-shortcode-to-clipboard-copied").show().delay(3000).hide(4000);
    });

    clipboardClient.on('error', function (e) {
        if (console && console.error) {
            console.error("Could not copy to clipboard, Action: ", e.action, ', Trigger: ', e.trigger);
        }
    });
});