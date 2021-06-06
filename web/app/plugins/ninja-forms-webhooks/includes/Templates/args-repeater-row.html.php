<?php $update_templates = version_compare( get_option( 'ninja_forms_version', '0' ), '3.0', '>' ); ?>
<?php if( $update_templates ): ?>
<script id="tmpl-nf-webhooks-args-repeater-row" type="text/template">
    <div>
        <span class="dashicons dashicons-menu handle"></span>
    </div>
    <div>
        <label>
            <input type="text" class="setting" value="{{{ data.key }}}" data-id="key" list="wh-args">
            <datalist id="wh-args">
                {{{ data.renderOptions( 'key', data.key ) }}}
            </datalist>
        </label>
    </div>
    <div>
        <label class="has-merge-tags">
            <input type="text" class="setting" value="{{{ data.value }}}" data-id="value">
            <span class="dashicons dashicons-list-view merge-tags"></span>
        </label>
    </div>
    <div>
        <span class="dashicons dashicons-dismiss nf-delete"></span>
    </div>
</script>
<?php else: ?>
<script id="tmpl-nf-webhooks-args-repeater-row" type="text/template">
    <div>
        <span class="dashicons dashicons-menu handle"></span>
    </div>
    <div>
        <label>
            <input type="text" class="setting" value="<%= key %>" data-id="key" list="wh-args">
            <datalist id="wh-args">
                <%= renderOptions( 'key', key ) %>
            </datalist>
        </label>
    </div>
    <div>
        <label class="has-merge-tags">
            <input type="text" class="setting" value="<%= value %>" data-id="value">
            <span class="dashicons dashicons-list-view merge-tags"></span>
        </label>
    </div>
    <div>
        <span class="dashicons dashicons-dismiss nf-delete"></span>
    </div>
</script>
<?php endif; ?>
