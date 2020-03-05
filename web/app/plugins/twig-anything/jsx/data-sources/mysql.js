'use strict';

var TwigAnythingDataSource_mysql = React.createClass({
    displayName: 'TwigAnythingDataSource_mysql',

    getInitialState: function getInitialState() {
        return {
            mysql: '',
            mysql_type: 'get_results' };
    },
    getSettings: function getSettings() {
        return {
            mysql: this.state.mysql,
            mysql_type: this.state.mysql_type
        };
    },
    handleMysqlChange: function handleMysqlChange(e) {
        this.setState({ mysql: e.target.value });
    },
    handleMysqlTypeChange: function handleMysqlTypeChange(e) {
        this.setState({ mysql_type: e.target.value });
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
                            { htmlFor: 'twig_anything_data_source_mysql_type' },
                            'MySQL Result Type'
                        )
                    ),
                    React.createElement(
                        'td',
                        null,
                        React.createElement(
                            'select',
                            {
                                name: 'twig_anything_data_source_mysql_type',
                                id: 'twig_anything_data_source_mysql_type',
                                value: this.state.mysql_type,
                                onChange: this.handleMysqlTypeChange },
                            React.createElement(
                                'option',
                                { value: 'get_results' },
                                'The entire query result (get_results)'
                            ),
                            React.createElement(
                                'option',
                                { value: 'get_col' },
                                'A one dimensional array for a particular column (get_col)'
                            ),
                            React.createElement(
                                'option',
                                { value: 'get_row' },
                                'A single row array (get_row)'
                            ),
                            React.createElement(
                                'option',
                                { value: 'get_var' },
                                'A single value (get_var)'
                            )
                        ),
                        React.createElement(
                            'p',
                            { className: 'description' },
                            'See ',
                            React.createElement(
                                'a',
                                { href: 'https://codex.wordpress.org/Class_Reference/wpdb#SELECT_a_Variable', target: '_blank' },
                                'WordPress documentation'
                            ),
                            ' for more details.'
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
                            { htmlFor: 'twig_anything_data_source_mysql' },
                            'MySQL Query'
                        )
                    ),
                    React.createElement(
                        'td',
                        null,
                        React.createElement('textarea', {
                            name: 'twig_anything_data_source_mysql',
                            id: 'twig_anything_data_source_mysql',
                            value: this.state.mysql,
                            onChange: this.handleMysqlChange,
                            className: 'large-text',
                            style: { height: '14em' } }),
                        React.createElement(
                            'p',
                            null,
                            React.createElement(
                                'code',
                                null,
                                '{{ wp_globals.wpdb.posts }}'
                            ),
                            'gets the correct name of your blog\'s ',
                            React.createElement(
                                'em',
                                null,
                                'posts'
                            ),
                            ' table. See\xA0',
                            React.createElement(
                                'a',
                                { target: '_blank', href: 'https://codex.wordpress.org/Class_Reference/wpdb#Tables' },
                                'the full list of WordPress tables.'
                            ),
                            React.createElement('br', null),
                            'All\xA0',
                            React.createElement(
                                'a',
                                { target: '_blank', href: 'https://codex.wordpress.org/Global_Variables' },
                                'WordPress globals'
                            ),
                            '\xA0are available, e.g. use ',
                            React.createElement(
                                'code',
                                null,
                                '{{ wp_globals.post.ID }}'
                            ),
                            'to get the current post ID in the posts loop.',
                            React.createElement('br', null),
                            React.createElement(
                                'code',
                                null,
                                '{{ get_the_ID() }}'
                            ),
                            '\xA0retrieves the numeric ID of the current post (',
                            React.createElement(
                                'a',
                                { target: '_blank', href: 'https://codex.wordpress.org/Function_Reference/get_the_ID' },
                                'read more'
                            ),
                            ')',
                            React.createElement('br', null),
                            React.createElement(
                                'code',
                                null,
                                '{{ get_current_blog_id() }}'
                            ),
                            '\xA0retrieves the current blog ID (',
                            React.createElement(
                                'a',
                                { target: '_blank', href: 'https://codex.wordpress.org/Function_Reference/get_current_blog_id' },
                                'read more'
                            ),
                            ')',
                            React.createElement('br', null),
                            React.createElement(
                                'code',
                                null,
                                '{{ wp_get_current_user() }}'
                            ),
                            '\xA0retrieves the current user object WP_user (',
                            React.createElement(
                                'a',
                                { target: '_blank', href: 'https://codex.wordpress.org/Function_Reference/wp_get_current_user' },
                                'read more'
                            ),
                            ')'
                        )
                    )
                )
            )
        );
    }
});