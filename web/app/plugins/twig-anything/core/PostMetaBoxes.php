<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything;

class PostMetaBoxes
{
    const POST_META_DATA_SOURCE_SETTINGS = '_twig_anything';

    public function addMetaBoxes() {
        # Data Source Settings
        add_meta_box(
            'twig_anything_data_source',
            'Data Source',
            array($this, 'renderDataSourceMetaBox'),
            TwigAnything::POST_TYPE,
            'normal',
            'high'
        );

        # Shortcode info with a "copy" button
        add_meta_box(
            'twig_anything_shortcode',
            'Shortcode',
            array($this, 'renderShortcodeMetaBox'),
            TwigAnything::POST_TYPE,
            'side',
            'default'
        );
    }

    /**
     * Loads the settings of Data Source meta box of our custom post.
     * @param $postId
     * @return array|mixed
     */
    public static function loadDataSourceMetaBoxSettings($postId) {
        # Already encoded correctly
        $json = get_post_meta($postId, self::POST_META_DATA_SOURCE_SETTINGS, true);

        /*echo "<pre>";
        print_r($json);
        echo "</pre>";
        die();*/

        # Check that it decodes well and reset if it does not.
        $config = json_decode($json, true);
        if (empty($config) || !is_array($config)) {
            $config = array();
        }

        # Initialize common settings if does not exist yet
        if (!array_key_exists('commonSettings', $config) || !is_array($config['commonSettings'])) {
            $config['commonSettings'] = array();
        }

        # Initialize data source settings
        if (!array_key_exists('dataSourceSettings', $config) || !is_array($config['dataSourceSettings'])) {
            $settings = array();
            if (array_key_exists('source_type', $config['commonSettings'])) {
                $sourceTypeSlug = (string) $config['commonSettings']['source_type'];
                $dataSource = twigAnything()->getDataSources()->getBySlug($sourceTypeSlug);
                if ($dataSource) {
                    $settings = $dataSource->getDefaultConfig();
                }
            }
            $config['dataSourceSettings'] = $settings;
        }

        # Initialize format settings
        if (!array_key_exists('formatSettings', $config) || !is_array($config['formatSettings'])) {
            $settings = array();
            if (array_key_exists('format', $config['commonSettings'])) {
                $formatSlug = (string) $config['commonSettings']['format'];
                $format = twigAnything()->getFormats()->getBySlug($formatSlug);
                if ($format) {
                    $settings = $format->getDefaultConfig();
                }
            }
            $config['formatSettings'] = $settings;
        }

        return $config;
    }

    public function renderDataSourceMetaBox($post, $metabox) {
        # Add a nonce field so we can check for it later.
        wp_nonce_field('twig_anything_post_meta_box', 'twig_anything_post_meta_box_nonce');

        $config = self::loadDataSourceMetaBoxSettings($post->ID);

        $config['wpHomePath'] = ABSPATH;
        $config['dataSourcesMeta'] = twigAnything()->getDataSources()->getMetaForGui();
        $config['formatsMeta'] = twigAnything()->getFormats()->getMetaForGui();

        # Encode it back to JSON but in a stricter way
        # so that it's suitable for HTML
        $json = WpUtils::jsonEncodeForHtml($config);

        $loadingLocalized = __('Loading...', 'twig-anything');

        echo <<<HTML
<div id="data_source_react_container">$loadingLocalized</div>
<script>
    var twigAnythingDataSourceMetaBoxInputData = {$json};
</script>
HTML;
    }

    public static function getDataSourceMetaBoxSettingsFromPostVars() {
        # Common settings
        $sourceType = trim(WpUtils::postVar('twig_anything_source_type', ''));
        $format = trim(WpUtils::postVar('twig_anything_format', ''));
        $cacheSeconds = trim(WpUtils::postVar('twig_anything_cache_seconds', ''));
        $onDataError = trim(WpUtils::postVar('twig_anything_on_data_error'));

        $commonSettings = array(
            'source_type' => $sourceType,
            'format' => $format,
            'cache_seconds' => $cacheSeconds,
            'on_data_error' => $onDataError,
        );

        # Data Source settings
        $dataSource = twigAnything()->getDataSources()->getBySlug($sourceType);
        if ($dataSource) {
            $dataSourceSettings = array();
            $defaultConfig = $dataSource->getDefaultConfig();
            foreach ($defaultConfig as $key => $value) {
                $postVarName = 'twig_anything_data_source_'.$key;
                $postVarValue = WpUtils::postVar($postVarName, $value);
                if (!is_array($postVarValue)) {
                    $postVarValue = trim($postVarValue);
                }
                $dataSourceSettings[$key] = $postVarValue;
            }
        }
        else {
            $dataSourceSettings = null;
        }
        /*echo "<pre>"; print_r($dataSourceSettings); echo "</pre>"; exit;*/

        # Format settings
        $format = twigAnything()->getFormats()->getBySlug($format);
        if ($format) {
            $formatSettings = array();
            $defaultConfig = $format->getDefaultConfig();
            foreach ($defaultConfig as $key => $value) {
                $postVarName = 'twig_anything_format_'.$key;
                $postVarValue = WpUtils::postVar($postVarName, $value);
                if (!is_array($postVarValue)) {
                    $postVarValue = trim($postVarValue);
                }
                $formatSettings[$key] = $postVarValue;
            }
        }
        else {
            $formatSettings = null;
        }

        $settings = array(
            'version' => '3',
            'timestamp' => time(),
            'commonSettings' => $commonSettings,
            'dataSourceSettings' => $dataSourceSettings,
            'formatSettings' => $formatSettings
        );

        # Very early in its execution, WordPress intentionally
        # adds "magic quotes" for the sake of consistency.
        # We don't want it!
        # Read more: https://codex.wordpress.org/Function_Reference/stripslashes_deep
        $settings = stripslashes_deep($settings);

        return $settings;
    }

    private function saveDataSourceConfiguration($postId, $config) {
        # Save our settings as a JSON-encoded string.
        # Use the lightest encoding - enough to save into database.
        # We will encode stricter when we output to HTML.
        $json = json_encode($config);

        /*echo "DATA TO SAVE:<pre>";
        print_r($json);
        echo "</pre>";
        die();*/

        # Save with using db queries directly to avoid
        # WP's boredom of metadata sensitization

        /** @var \wpdb $wpdb  */
        global $wpdb;
        $sql = $wpdb->prepare("
            SELECT meta_id FROM $wpdb->postmeta
            WHERE meta_key = %s AND post_id = %d",
            self::POST_META_DATA_SOURCE_SETTINGS, $postId);
        $metaIds = $wpdb->get_col($sql);

        // If no metadata exists, create a new entry
        if (empty($metaIds)) {
            $sql = $wpdb->prepare("
                INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value)
                VALUES (%d, %s, %s)",
                $postId, self::POST_META_DATA_SOURCE_SETTINGS, $json);
        }

        // If metadata exists, update the existing one
        else {
            $sql = $wpdb->prepare("
                UPDATE $wpdb->postmeta
                SET meta_value = %s
                WHERE post_id = %d AND meta_key = %s",
                $json, $postId, self::POST_META_DATA_SOURCE_SETTINGS);
        }
        $wpdb->query($sql);
    }

    public function onSavePost($postId) {
        if (!isset($_POST['twig_anything_post_meta_box_nonce'])) {
            return;
        }

        $nonce = $_POST['twig_anything_post_meta_box_nonce'];

        if (!wp_verify_nonce($nonce, 'twig_anything_post_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!isset($_POST['post_type'])) {
            return;
        }

        if ($_POST['post_type'] !== TwigAnything::POST_TYPE) {
            return;
        }

        if (!current_user_can('edit_post', $postId)) {
            return;
        }

        $config = self::getDataSourceMetaBoxSettingsFromPostVars();
        $this->saveDataSourceConfiguration($postId, $config);
    }

    public function onPrePostUpdate($postId) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
		}
		
		if (!is_admin()) {
			return;
		}

        $screen = get_current_screen();
        if ($screen->post_type !== TwigAnything::POST_TYPE || $screen->base !== 'post') {
            return;
        }

        if (!isset($_POST['post_type'])) {
            return;
        }

        if ($_POST['post_type'] !== TwigAnything::POST_TYPE) {
            return;
        }

        $config = self::getDataSourceMetaBoxSettingsFromPostVars();
        /*echo "<pre>"; print_r($config); echo "</pre>"; exit;*/
        $this->saveDataSourceConfiguration($postId, $config);
    }

    public function renderShortcodeMetaBox($post) {
        $shortcode = "[twig-anything slug=\"{$post->post_name}\"]";
        $shortcodeAsAttribute = esc_attr($shortcode);

        echo <<<HTML
<p>
    Use the following shortcode to render the template in a post, widget etc:
</p>
<p id="twig-anything-shortcode-value">
    $shortcode
</p>
<div id="twig-anything-copy-shortcode-to-clipboard-container">
    <button type="button" class="button"
        id="twig-anything-copy-shortcode-to-clipboard"
        data-clipboard-text="$shortcodeAsAttribute">
        copy to clipboard
    </button>
</div>
<div id="twig-anything-copy-shortcode-to-clipboard-copied" style="display: none;">
    <i>copied</i>
</div>
HTML;

    }
}