"use strict";

var TwigAnythingDataSource_file = React.createClass({
    displayName: "TwigAnythingDataSource_file",

    getInitialState: function getInitialState() {
        return {
            is_file_path_absolute: false,
            file_path: ''
        };
    },
    getSettings: function getSettings() {
        return {
            is_file_path_absolute: this.state.is_file_path_absolute,
            file_path: this.state.file_path
        };
    },
    handleIsFilePathAbsoluteChange: function handleIsFilePathAbsoluteChange(e) {
        this.setState({ is_file_path_absolute: e.target.checked });
    },
    handleFilePathChange: function handleFilePathChange(e) {
        this.setState({ file_path: e.target.value });
    },
    render: function render() {
        return React.createElement(
            "table",
            { className: "form-table" },
            React.createElement(
                "tbody",
                null,
                React.createElement(
                    "tr",
                    null,
                    React.createElement(
                        "th",
                        { scope: "row" },
                        "File Path"
                    ),
                    React.createElement(
                        "td",
                        null,
                        React.createElement(
                            "fieldset",
                            null,
                            React.createElement(
                                "legend",
                                { className: "screen-reader-text" },
                                React.createElement(
                                    "span",
                                    null,
                                    "File Path"
                                )
                            ),
                            React.createElement("input", {
                                name: "twig_anything_data_source_file_path",
                                id: "twig_anything_data_source_file_path",
                                value: this.state.file_path,
                                onChange: this.handleFilePathChange,
                                className: "large-text" }),
                            React.createElement("br", null),
                            React.createElement(
                                "label",
                                { htmlFor: "twig_anything_data_source_is_file_path_absolute" },
                                React.createElement("input", {
                                    name: "twig_anything_data_source_is_file_path_absolute",
                                    id: "twig_anything_data_source_is_file_path_absolute",
                                    type: "checkbox",
                                    checked: this.state.is_file_path_absolute,
                                    onChange: this.handleIsFilePathAbsoluteChange }),
                                "File path is absolute"
                            )
                        ),
                        React.createElement(
                            "p",
                            { className: "description" },
                            "If the path is not absolute, it is relative to the root of the WordPress installation:",
                            React.createElement(
                                "code",
                                null,
                                this.props.wpHomePath
                            )
                        )
                    )
                )
            )
        );
    }
});