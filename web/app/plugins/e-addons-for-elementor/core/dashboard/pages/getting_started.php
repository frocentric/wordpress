<?php
// check user capabilities
if (!current_user_can('manage_options')) {
    return;
}
?>
<div class="wrap">
    <div class="e-addons-start-page">
        <div class="e-addons-start-page__box postbox">

            <div class="e-addons-start-page__header">
                <div class="e-addons-start-page__title">
                    Getting Started
                </div>
                <a class="e-addons-start-page__skip" href="<?php echo admin_url('admin.php?page=e_addons'); ?>">
                    <span class="dashicons dashicons-no"></span>
                    <span class="elementor-screen-only">Skip</span>
                </a>
            </div>

            <div class="e-addons-start-page__content">
                <div class="e-addons-logo-wrapper">
                    <img src="<?php echo E_ADDONS_URL; ?>/assets/img/e-addons-anim.svg">
                </div>
                <div class="e-addons-start-page__content_first e-addons-start-page__scheme">
                    <h4 class="e-addons-version">3.x</h4>
                    <h2>Welcome to <b>e-addons</b> <br>for Elementor</h2>
                    <p class="e-addons-evidence">The plugin has been completely revamped.<br>Once installed, remember to activate the desired features.</p>
                    <a href="https://e-addons.com/the-evolution/" target="_blank" class="button button-primary button-hero button-e-addons">Read More</a>
                    <p class="e-addons-steps"><b>3</b> steps &raquo; <b>for</b> many colors</p>
                    <img class="e-addons-start-page__scheme_processf" src="https://e-addons.com/wp-content/uploads/getting-started/process_scheme.jpg">
                </div>

                <div class="e-addons-start-page__content_intro">
                    <p>e-addons is a platform that allows you to get many useful features, such as extensions, widgets and unique functionality for your workflow with Elementor.</p>
                </div>

                <div class="e-addons-start-page__scheme">
                    <img class="e-addons-start-page__scheme_install" src="https://e-addons.com/wp-content/uploads/getting-started/install_scheme.jpg">
                    <p>From the e-Addons Dashboard you can activate every available addons in just one click.</p>
                    <img class="e-addons-start-page__scheme_panel" src="https://e-addons.com/wp-content/uploads/getting-started/panel_scheme.jpg">
                </div>

                <div class="e-addons-start-page__actions e-addons-start-page__content--narrow">
                    <a href="<?php echo admin_url('admin.php?page=e_addons'); ?>" class="button button-primary button-hero">Start now</a>
                    <a href="https://e-addons.com/" target="_blank" class="button button-secondary button-hero">e-addons.com</a>
                </div>
            </div>
        </div>
        <div class="e-addons-row">
            <div class="e-addons-col e-addons-col-3 e-addons-banner"><a href="https://e-addons.com" target="_blank"><img src="https://e-addons.com/wp-content/uploads/getting-started/banner1.jpg"></a></div>
            <div class="e-addons-col e-addons-col-3 e-addons-banner"><a href="https://e-addons.com/the-evolution" target="_blank"><img src="https://e-addons.com/wp-content/uploads/getting-started/banner2.jpg"></a></div>
            <div class="e-addons-col e-addons-col-3 e-addons-banner"><a href="https://www.facebook.com/eaddonselementor" target="_blank"><img src="https://e-addons.com/wp-content/uploads/getting-started/banner3.jpg"></a></div>
        </div>
    </div>
</div>