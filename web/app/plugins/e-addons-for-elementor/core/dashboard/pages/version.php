<?php

use EAddonsForElementor\Core\Utils;

// check user capabilities
if (!current_user_can('manage_options')) {
    return;
}
?>
<div class="wrap">
    <?php
    if (!empty($_POST['action']) && $_POST['action'] == 'backup') {
        if (!empty($_POST['e_addons_backup_disable'])) {
            update_option('e_addons_backup_disable', true);
        } else {
            delete_option('e_addons_backup_disable');
        }
        Utils::e_admin_notice('Backup preferences saved', 'success');
    }
    $backup_disable = get_option('e_addons_backup_disable');

    if (!empty($_POST['action']) && $_POST['action'] == 'beta') {
        if (!empty($_POST['e_addons_beta'])) {
            update_option('e_addons_beta', true);
        } else {
            delete_option('e_addons_beta');
        }
        Utils::e_admin_notice('Beta preferences saved', 'success');
    }
    $beta = get_option('e_addons_beta');
    ?>


    <?php $this->top_menu(); ?>

    <h1 class="e_addons-title"><span class="e_addons_ic elementor-icon eicon-history"></span> Version Control</h1>

    <?php if (!$backup_disable) { ?><div class="e_addons-panel e_addons-panel-success"><?php } else { ?><div class="e_addons-panel e_addons-panel-error"><?php } ?>
            <h2 class="e_addons-title">Safe Update</h2>
            <form method="POST" action="">
                <input type="hidden" name="page" value="e_addons_version">
                <input type="hidden" name="action" value="backup">
                <input type="checkbox" name="e_addons_backup_disable" id="e_addons_backup_disable"<?php
                if ($backup_disable) {
                    echo ' checked="checked"';
                }
                ?>> <label for="e_addons_backup_disable"><strong><?php _e('Turn-OFF Safe Update'); ?></strong></label>
                <br> &nbsp; &nbsp; &nbsp; &nbsp; <?php _e('It will not automatically do a backup copy of your e-addons plugins before the update and you will not do a Rollback.'); ?>
                <br> &nbsp; &nbsp; &nbsp; &nbsp; <strong style="color: green;">Please Note: We recommend to execute the automatic plugin backup on every update.</strong>
                <br><br><input class="e_addons-button e_addons-button-primary" type="submit" value="Save"><br>&nbsp;
            </form>
        </div>

        <?php
        if (!$backup_disable) {
            if (defined('E_ADDONS_BACKUP_PATH') && is_dir(E_ADDONS_BACKUP_PATH)) {
                $versions = Utils::glob(E_ADDONS_BACKUP_PATH . '/*.zip');
                $rollbacks = array();
                foreach ($versions as $aversion) {
                    $tmp = explode('.', pathinfo($aversion, PATHINFO_FILENAME));
                    $name = array_shift($tmp);
                    $version = implode('.', $tmp);
                    $rollbacks[$name][] = $version;
                }
                if (!empty($rollbacks)) {
                    ?>
                    <div class="e_addons-panel e_addons-panel-warning">
                        <h2 class="e_addons-title">Rollback to Previous Version</h2>
                        <p>Experiencing an issue with your e-addons? Rollback to a previous version before the issue appeared.</p>
                        <ul>
                            <?php
                            foreach ($rollbacks as $name => $versions) {
                                ?>
                                <li>
                                    <form action="" method="POST">
                                        <input type="hidden" name="action" value="e_addons_rollback">
                                        <input type="hidden" name="e_addons_plugin" value="<?php echo $name; ?>">
                                        <strong><?php echo $name; ?></strong> 
                                        <select name="e_addons_version">
                                            <?php
                                            foreach ($versions as $aver) {
                                                echo '<option value="' . $aver . '">' . $aver . '</option>';
                                            }
                                            ?>
                                        </select> 
                                        <input class="e_addons-button e_addons-button-primary" type="submit" value="Reinstall" onclick="return confirm('Are you sure to proceed with RollBack?');">
                                    </form>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <?php
                }
            }
        }
        ?>

        <div class="e_addons-panel e_addons-panel-warning">
            <h2 class="e_addons-title">Become a Beta Tester</h2>
            <form method="POST" action="">
                <input type="hidden" name="page" value="e_addons_version">
                <input type="hidden" name="action" value="beta">
                <input type="checkbox" name="e_addons_beta" id="e_addons_beta"<?php
                if ($beta) {
                    echo ' checked="checked"';
                }
                ?>> <label for="e_addons_beta"><strong><?php _e('Turn-ON Beta Tester, to get notified when a new beta version of e-addons is available.'); ?></strong></label>
                <br> &nbsp; &nbsp; &nbsp; &nbsp; <?php _e('The Beta version will not install automatically. You always have the option to ignore it.'); ?>
                <br> &nbsp; &nbsp; &nbsp; &nbsp; <strong style="color: red;">Please Note: We do not recommend updating to a beta version on production sites.</strong>
                <br><br><input class="e_addons-button e_addons-button-primary" type="submit" value="Save"><br>
            </form>
        </div>


    </div>