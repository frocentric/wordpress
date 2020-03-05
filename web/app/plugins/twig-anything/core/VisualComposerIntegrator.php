<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything;

class VisualComposerIntegrator
{
    const TEXT_BLOCK_NATIVE_ICON = 'icon-wpb-layer-shape-text';

    public function integrate() {
        add_action('vc_before_init', array($this, 'onVcBeforeInit'));
        add_action('vc_after_init',  array($this, 'onVcMapperAfterInit'));
    }

    public function onVcBeforeInit() {
        vc_map(array(
            'name' => __('Twig Anything Template', 'twig-anything'),
            'base' => 'twig-anything',
            'class' => '',
            'icon' => self::TEXT_BLOCK_NATIVE_ICON,
            # Use composer's text and translations for category name
            "category" => __('Content', 'js_composer'),
            "params" => array(
                array(
                    'type' => 'textfield',
                    'holder' => 'div',
                    'class' => '',
                    # Composer uses the "slug" text and translates it as well,
                    # we want to reuse it
                    'heading' => __('Slug', 'js_composer'),
                    'param_name' => 'slug',
                    'value' => '',
                    'description' => __('Enter the slug of a Twig Template', 'twig-anything')
                )
            )
        ));
    }

    public function onVcMapperAfterInit() {
        $this->syncCustomBlockIcon();
    }

    private function syncCustomBlockIcon() {
        # We don't know for sure if self::TEXT_BLOCK_NATIVE_ICON icon exists
        # in all VC versions. As an additional means to use an existing icon,
        # try getting the same icon that is used by the native Text Block.

        if (!class_exists('\\WPBMap')
            || !method_exists('\\WPBMap', 'getShortCodes')
            || !method_exists('\\WPBMap', 'modify')) {
            return;
        }
        $vcShortcodes = \WPBMap::getShortCodes();
        if (!is_array($vcShortcodes) && !array_key_exists('vc_column_text', $vcShortcodes)) {
            return;
        }

        $meta = $vcShortcodes['vc_column_text'];
        if (!is_array($meta) || !array_key_exists('icon', $meta)) {
            return;
        }

        $textBlockIcon = $meta['icon'];
        if ($textBlockIcon != self::TEXT_BLOCK_NATIVE_ICON) {
            \WPBMap::modify('twig-anything', 'icon', $textBlockIcon);
        }
    }
}