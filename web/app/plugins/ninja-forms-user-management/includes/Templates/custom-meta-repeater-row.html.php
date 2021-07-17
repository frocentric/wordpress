<?php $update_templates = version_compare( get_option( 'ninja_forms_version', '0' ), '3.0', '>' ); ?>
<?php if( $update_templates ): ?>
    <script id="tmpl-nf-user-registration-custom-meta-repeater-row" type="text/template">
        <div>
            <span class="dashicons dashicons-menu handle"></span>
        </div>
        <div>
            <label>
                <input type="text" class="setting" value="{{{ data.key }}}" data-id="key" list="custom_meta">
                <datalist id="custom_meta">
                    {{{ data.renderOptions( 'key', data.key ) }}}
                </datalist>
            </label>
            <span class="nf-option-error"></span>
        </div>
        <div>
            <label class="has-merge-tags">
                <input type="text" class="setting" value="{{{ data.value }}}" data-id="value">
                <span class="dashicons dashicons-list-view merge-tags"></span>
            </label>
            <span class="nf-option-error"></span>
        </div>
        <div>
            <span class="dashicons dashicons-dismiss nf-delete"></span>
        </div>
    </script>
<?php else: ?>
    <script id="tmpl-nf-user-registration-custom-meta-repeater-row" type="text/template">
        <div>
            <span class="dashicons dashicons-menu handle"></span>
        </div>
        <div>
            <label>
                <input type="text" class="setting" value="{{{ key }}}" data-id="key" list="custom_meta">
                <datalist id="custom_meta">
                    {{{ renderOptions( 'key', key ) }}}
                </datalist>
            </label>
            <span class="nf-option-error"></span>
        </div>
        <div>
            <label class="has-merge-tags">
                <input type="text" class="setting" value="{{{ value }}}" data-id="value">
                <span class="dashicons dashicons-list-view merge-tags"></span>
            </label>
            <span class="nf-option-error"></span>
        </div>
        <div>
            <span class="dashicons dashicons-dismiss nf-delete"></span>
        </div>
    </script>
<?php endif; ?>
