/*
 * E-ADDONS for Elementor - EDITOR
 * e-addons.com
 * credits to Unlimited Elementor Inner Sections By TaspriStudio
 */
!function (e) {
    function n(e, n) {
        var t = e.findIndex(function (e) {
            return 'addNew' === e.name;
        });
        return e[t].actions.push({
            name: 'add-nested-section',
            title: 'Add Inner Section',
            icon: 'eicon-inner-section eadd-inner_sections',
            callback: function () {
                !function (e) {
                    var n = e.getContainer().view;
                    'column' === n.getElementType() && n.addElement({
                        elType: 'section',
                        isInner: true,
                        settings: {},
                        elements: [
                            {
                                id: elementor.helpers.getUniqueID(),
                                elType: 'column',
                                isInner: true,
                                settings: {
                                    _column_size: 100
                                },
                                elements: []
                            }
                        ]
                    })
                }(n)
            },
            isEnabled: function () {
                return true;
            }
        }),
        e
    }
    jQuery(document).ready(function () {
        window.elementor && (elementor.hooks.addFilter('element/view', function (e, n) {
            return 'column' === n.get('elType') ? e.extend({
                getContextMenuGroups: function () {
                    return e.prototype.getContextMenuGroups.apply(this, arguments);
                }
            }) : e
        }), elementor.hooks.addFilter('elements/column/contextMenuGroups', n));
    });
}();



