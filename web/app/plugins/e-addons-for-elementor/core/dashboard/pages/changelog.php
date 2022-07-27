<?php
// check user capabilities
if (!current_user_can('manage_options')) {
    return;
}
?>
<div class="wrap">

    <?php $this->top_menu(); ?>

    <h1 class="e_addons-title"><span class="e_addons_ic elementor-icon eicon-info-circle-o"></span> Changelog</h1>
    <?php
    $e_addons_plugins = \EAddonsForElementor\Plugin::instance()->get_addons(true);
    foreach ($e_addons_plugins as $e_plugin) {
        $changelog_file = $e_plugin['path'] . '/readme.txt';
        if (file_exists($changelog_file)) {
            ?>
            <div class="e_addons-panel e_addons-panel-info">
                <h2 class="e_addons-title"><?php echo $e_plugin['Name']; ?><span class="e_addons-info_version"><?php echo $e_plugin['Version']; ?></span></h2>
                <textarea style="width:100%;" rows="14"><?php
                    $changelog_content = file_get_contents($changelog_file);
                    list($pre, $changelog) = explode('== Changelog ==', $changelog_content, 2);
                    echo trim($changelog);
                    ?></textarea>
                <br>&nbsp;
            </div>
            <?php
        }
    }
    ?>
</div>
<?php
