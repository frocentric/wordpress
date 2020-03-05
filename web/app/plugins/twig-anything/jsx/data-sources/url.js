'use strict';

var TwigAnythingDataSource_url = React.createClass({
    displayName: 'TwigAnythingDataSource_url',

    getInitialState: function getInitialState() {
        return {
            url: '',
            method: 'GET'
        };
    },
    getSettings: function getSettings() {
        return {
            url: this.state.url,
            method: this.state.method
        };
    },
    handleUrlChange: function handleUrlChange(e) {
        this.setState({ url: e.target.value });
    },
    handleMethodChange: function handleMethodChange(e) {
        this.setState({ method: e.target.value });
    },
    render: function render() {
        return React.createElement(
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
                            { htmlFor: 'twig_anything_data_source_url' },
                            'Data URL'
                        )
                    ),
                    React.createElement(
                        'td',
                        null,
                        React.createElement('input', {
                            name: 'twig_anything_data_source_url',
                            id: 'twig_anything_data_source_url',
                            value: this.state.url,
                            onChange: this.handleUrlChange,
                            className: 'large-text' }),
                        React.createElement(
                            'p',
                            { className: 'description' },
                            'Full URL to fetch data from, including ',
                            React.createElement(
                                'code',
                                null,
                                'http://'
                            ),
                            'or ',
                            React.createElement(
                                'code',
                                null,
                                'https://'
                            )
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
                            { htmlFor: 'twig_anything_data_source_method' },
                            'HTTP Request Method'
                        )
                    ),
                    React.createElement(
                        'td',
                        null,
                        React.createElement(
                            'select',
                            {
                                name: 'twig_anything_data_source_method',
                                id: 'twig_anything_data_source_method',
                                value: this.state.method,
                                onChange: this.handleMethodChange },
                            React.createElement(
                                'option',
                                { value: 'GET' },
                                'GET'
                            ),
                            React.createElement(
                                'option',
                                { value: 'POST' },
                                'POST'
                            )
                        ),
                        React.createElement(
                            'p',
                            { className: 'description' },
                            'Which HTTP method to use when fetching data from URL'
                        )
                    )
                )
            )
        );
    }
});