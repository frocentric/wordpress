<!--
    Template used for adding the "Add Condition" button to the Advanced drawer header.
-->
<script id="nf-tmpl-cl-advanced-drawer-header" type="text/template">
    <header class="nf-drawer-header">
        <a class="nf-add-new nf-add-new-condition" title="<%= nfcli18n.updateSettingsTitleAddNewCondition %>" href="#" data-drawerid="addField">
            <%= nfcli18n.updateSettingsTitleAddNewCondition %>
        </a>
        <a href="#" title="<%= nfcli18n.builderEditSettingsTitleDone %>" class="nf-button primary nf-close-drawer" tabindex="-1">
            <%= nfcli18n.builderEditSettingsTitleDone %>
        </a>
    </header>
</script>

<!--
    Template used for conditions on the Advanced domain.
-->
<script id="nf-tmpl-cl-advanced-condition" type="text/template">
    <!-- Condition Layout View -->
    <div class="nf-condition">
        <div class="nf-first-when-container">
            <div class="nf-condition-label">
                <%= nfcli18n.builderEditSettingsWhen %>
                <div class="nf-when-controls">
                    <i class="fa fa-chevron-circle-<%= ( collapsed ) ? 'down' : 'up' %> nf-collapse-condition" aria-hidden="true"></i>
                    <i class="fa fa-minus-circle nf-remove-condition" aria-hidden="true"></i>
                </div>
            </div>
            <div class="nf-first-when">

            </div>
        </div>
        <div class="nf-when-container">
            <div class="nf-when-region">
                <!-- WHEN Region -->
            </div>
            <%
            if ( ! collapsed ) {
            %>
            <i class="fa fa-plus-circle nf-add-when" aria-hidden="true"></i>
            <%
            }
            %>
        </div>
        <%
        if ( ! collapsed ) {
        %>
        <div class="nf-then-container">
            <div class="nf-condition-label"><%= nfcli18n.builderEditSettingsDo %></div>
            <div class="nf-then-region">
                <!-- THEN Region -->
            </div>
            <i class="fa fa-plus-circle nf-add-then" aria-hidden="true"></i>
        </div>

        <div class="nf-else-container">
            <div class="nf-condition-label"><%= nfcli18n.builderEditSettingsConditionNotMet %></div>
            <div class="nf-else-region">
                <!-- ELSE Region -->
            </div>
            <i class="fa fa-plus-circle nf-add-else" aria-hidden="true"></i>
        </div>

        <%
        }
        %>
    </div>
</script>


<!--
    Template parts for the Advanced domain conditions display

    First-When-Item is used for the first "when" statement of a condition.
-->

<script id="nf-tmpl-cl-advanced-first-when-item" type="text/template">
    <div class="nf-setting nf-one-third">
        <label class="nf-select"><%= renderKeySelect( key, modelType ) %><div></div></label>
    </div>
    <div class="nf-setting nf-one-third">
        <label class="nf-select">
            <%= renderComparators( type, key, comparator ) %>
            <div></div>
        </label>
    </div>
    <div class="nf-setting nf-one-third">
        <label class="nf-input">
            <%= renderWhenValue( type, key, comparator, value ) %>
        </label>
    </div>
</script>


<!--
    When/Then/Else templates
-->

<script id="nf-tmpl-cl-advanced-when-item" type="text/template">
    <div class="nf-when">
        <div class="nf-setting nf-one-fourth">
            <i class="fa fa-minus-circle nf-remove-when" aria-hidden="true"></i>
        </div>
        <div class="nf-setting nf-one-fourth">
            <label class="nf-select">
                <select data-id="connector" class="setting">
                    <option value="AND" <%= ( 'AND' == connector ) ? 'selected="selected"' : '' %>><%= nfcli18n.builderEditSettingsAnd %></option>
                    <option value="OR" <%= ( 'OR' == connector ) ? 'selected="selected"' : '' %>><%= nfcli18n.builderEditSettingsOr %></option>
                </select>
                <div></div>
            </label>
        </div>
        <div class="nf-setting nf-one-fourth">
            <label class="nf-select"><%= renderKeySelect( key, modelType ) %><div></div></label>
        </div>
        <div class="nf-setting nf-one-fourth">
            <label class="nf-select">
                <%= renderComparators( type, key, comparator ) %>
                <div></div>
            </label>
        </div>
        <div class="nf-setting nf-one-fourth">
            <label class="nf-input">
                <%= renderWhenValue( type, key, comparator, value ) %>
            </label>
        </div>
    </div>
</script>

<script id="nf-tmpl-cl-trigger-item" type="text/template">
    <div class="nf-<%= modelType %>">
        <div class="nf-setting nf-one-fourth">
            <i class="fa fa-minus-circle nf-remove-<%= modelType %>" aria-hidden="true"></i>
        </div>
        <div class="nf-setting nf-one-fourth">
            <label class="nf-select">
                <%= renderKeySelect( key, modelType ) %>
                <div></div>
            </label>
        </div>
        <div class="nf-setting nf-one-fourth">
            <label class="nf-select">
                <%= renderTriggers( type, key, trigger, value ) %>
                <div></div>
            </label>
        </div>
        <div class="nf-setting nf-one-fourth">
            <label class="nf-input">
                <%= renderItemValue( key, trigger, value ) %>
            </label>
        </div>
    </div>
</script>


<!--
    Templates for our per-action conditions.
    Main condition layout template
-->

<script id="nf-tmpl-cl-actions-condition-layout" type="text/template">
    <!-- Condition Layout View -->
    <div class="nf-condition actions">
        <div class="nf-when-container">
            <div class="nf-condition-label">
                <div class="nf-setting nf-one-third">
                    <label class="nf-select">
                        <select class="condition-setting" data-id="process">
                            <option>Select One</option>
                            <option value="1" <%= ( 1 == data.process ) ? 'selected="selected"' : '' %>><%= nfcli18n.builderEditSettingsProcessThis %></option>
                            <option value="0" <%= ( 0 == data.process ) ? 'selected="selected"' : '' %>><%= nfcli18n.builderEditSettingsDoNotProcessThis %></option>
                        </select>
                        <div></div>
                    </label>
                </div>
                <div class="nf-setting nf-one-third action-when-label">
                    When
                </div>
                <div class="nf-setting nf-one-third">
                    <label class="nf-select">
                        <select class="condition-setting" data-id="connector">
                            <option value="all" <%= ( 'all' == data.connector ) ? 'selected="selected"' : '' %>><%= nfcli18n.builderEditSettingsAll %></option>
                            <option value="any" <%= ( 'any' == data.connector ) ? 'selected="selected"' : '' %>><%= nfcli18n.builderEditSettingsAny %></option>
                        </select>
                        <div></div>
                    </label>
                </div>
            </div>
            <div class="nf-when">

            </div>
            <i class="fa fa-plus-circle nf-add-when" aria-hidden="true"></i>
        </div>
    </div>
</script>

<!--
    Template for our action condition "When" statement.
-->

<script id="nf-tmpl-cl-actions-condition-when" type="text/template">
    <div class="nf-setting nf-one-fourth">
        <i class="fa fa-minus-circle nf-remove-when" aria-hidden="true"></i>
    </div>
    <div class="nf-setting nf-one-fourth">
        <label class="nf-select"><%= renderKeySelect( key, modelType ) %><div></div></label>
    </div>
    <div class="nf-setting nf-one-fourth">
        <label class="nf-select">
            <%= renderComparators( type, key, comparator ) %>
            <div></div>
        </label>
    </div>
    <div class="nf-setting nf-one-fourth">
        <label class="nf-input">
            <%= renderWhenValue( type, key, comparator, value ) %>
        </label>
    </div>
</script>

<!--
    Templates for our component parts:
    Key Select,
    Comparator Select,
    Values (default and field-specific),
    Triggers
-->

<script id="nf-tmpl-cl-key-select" type="text/template">
    <select data-id="key" class="setting">
        <option value="">Select One</option>
        <%
        _.each( groups, function( group ) {
        %>
        <optgroup label="<%= group.label %>">
            <%
            _.each( group.options, function( option ) {
            var selected = ( option.key == currentValue ) ? 'selected="selected"' : '';
            %>
            <option value="<%= option.key %>" <%= selected %> data-type="<%= group.type %>"><%= option.label %></option>
            <%
            } );
            %>
        </optgroup>
        <%
        } );
        %>
    </select>
</script>

<script id="nf-tmpl-cl-comparators" type="text/template">
    <select data-id="comparator" class="setting">
        <option value="">Select One</option>
        <%
        _.each( data.comparators, function( comparator ) {
        %>
        <option value="<%= comparator.value %>" <%= ( comparator.value == data.currentComparator ) ? 'selected="selected"' : '' %>><%= comparator.label %></option>
        <%
        } );
        %>
    </select>
</script>

<script id="nf-tmpl-cl-value-default" type="text/template">
    <input type="text" data-id="value" class="setting" value="<%= value %>">
</script>

<script id="nf-tmpl-cl-value-textarea" type="text/template">
    <textarea data-id="value" class="setting"><%= value %></textarea>
</script>

<script id="nf-tmpl-cl-value-checkbox" type="text/template">
    <label class="nf-select">
        <select data-id="value" class="setting">
            <option value="">Select One</option>
            <option value="1" <%= ( 1 == data.value ) ? 'selected="selected"': '' %>><%= nfcli18n.builderEditSettingsChecked %></option>
            <option value="0" <%= ( 0 === data.value ) ? 'selected="selected"': '' %>><%= nfcli18n.builderEditSettingsUnchecked %></option>
        </select>
        <div></div>
    </label>
</script>

<script id="nf-tmpl-cl-value-list" type="text/template">
    <label class="nf-select">
        <select data-id="value" class="setting">
            <option value=""><%= nfcli18n.builderEditSettingsSelectOne %></option>
            <%
            _.each( options.models, function( option ) {
            %>
            <option value="<%= option.get( 'value' ) %>" <%= ( option.get( 'value' ) === value ) ? 'selected="selected"': '' %>><%= option.get( 'label' ) %></option>
            <%
            } );
            %>

        </select>
        <div></div>
    </label>
</script>

<script id="nf-tmpl-cl-value-date" type="text/template">
    <!-- Date Value Template -->
    <input type="text" data-id="value" class="setting setting-date" value="<%= value %>" placeholder="date">
</script>

<script id="nf-tmpl-cl-triggers" type="text/template">
    <select data-id="trigger" class="setting">
        <option value=""><%= nfcli18n.builderEditSettingsSelectOne %></option>
        <%
        _.each( triggers, function( trigger ) {
        %>
        <option value="<%= trigger.value %>" <%= ( trigger.value == currentTrigger ) ? 'selected="selected"' : '' %>><%= trigger.label %></option>
        <%
        } );
        %>
    </select>
</script>