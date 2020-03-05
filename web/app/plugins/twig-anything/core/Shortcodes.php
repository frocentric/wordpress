<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything;

class Shortcodes
{
    public function shortcodeTwigAnything($atts) {
        # Default attribute values
        $atts = shortcode_atts(array(
            'slug' => '',
            'id' => ''
        ), $atts);

        try {
            # Load twig template
            $template = null;
            try {
                # If loading fails, an exception is thrown instead of
                # returning NULL
                if (!empty($atts['slug'])) {
                    $template = TwigTemplate::loadPublishedBySlug($atts['slug']);
                }
                elseif (!empty($atts['id'])) {
                    $template = TwigTemplate::loadById($atts['id']);
                }
                else {
                    throw new ShortcodeException('Please specify either slug="..." or id="..." attribute.');
                }
            }
            catch (TwigTemplateLoadException $e) {
                throw new ShortcodeException($e->getMessage(), 0, $e);
            }

            return $template->render();
        }
        catch (TwigAnythingException $e) {
            return '[twig-anything] shortcode error: ' . wp_kses_post($e->getMessage());
        }
    }

    /*public function shortcodeTwigAnythingTemplateCode($atts) {
        # Default attribute values
        $atts = shortcode_atts([
            'slug' => '',
            'wrap-tag' => 'pre',
            'wrap-tag-class' => '',
        ], $atts);

        try {
            if (empty($atts['slug'])) {
                throw new RuntimeException('Please specify slug="..." attribute.');
            }

            list($templateId, $templateBody, $templateConfig) = self::loadTemplate($atts['slug']);

            $res = $templateBody;

            if ($atts['wrap-tag']) {
                $wrapped = '<'.$atts['wrap-tag'];
                if ($atts['wrap-tag-class']) {
                    $wrapped .= ' class="'.$atts['wrap-tag-class'].'"';
                }
                $wrapped .= '>'.$res.'</'.$atts['wrap-tag'].'>';
                $res = $wrapped;
            }

            return htmlentities($res, ENT_QUOTES|ENT_HTML401);
        }
        catch (Exception $e) {
            return '[twig-anything-template-code] shortcode error: ' . wp_kses_post($e->getMessage());
        }
    }*/
}

class ShortcodeException extends TwigAnythingException {};