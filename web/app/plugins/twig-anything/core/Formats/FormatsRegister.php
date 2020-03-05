<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything\Formats;

class FormatsRegister
{
    /**
     * @var FormatInterface[]
     */
    private $register = array();

    /**
     * @param FormatInterface $format
     */
    public function register(FormatInterface $format) {
        $slug = $format->getSlug();
        if (array_key_exists($slug, $this->register)) {
            throw new FormatSlugExistsException('Format with this slug already exists.');
        }
        $this->register[$slug] = $format;
    }

    public function registerStandardFormats() {
        do_action('twig_anything_before_register_standard_formats', $this);
        $this->register(new Raw);
        $this->register(new Json);
        $this->register(new XML);
        do_action('twig_anything_after_register_standard_formats', $this);
        do_action('twig_anything_register_custom_formats', $this);
    }

    /**
     * @return FormatInterface[]
     */
    public function getFormats() {
        return $this->register;
    }

    /**
     * Get an array with meta information for GUI building
     * @return array
     */
    public function getMetaForGui() {
        $res = array();
        foreach($this->register as $format) {
            $res[] = array(
                'slug' => $format->getSlug(),
                'shortName' => $format->getShortName(),
                'longName' => $format->getLongName(),
                'description' => $format->getDescription()
            );
        }
        return $res;
    }

    /**
     * Get a registered instance of FormatInterface by its slug.
     * Returns NULL of not found.
     *
     * @param $slug
     * @return FormatInterface
     */
    public function getBySlug($slug) {
        if (!array_key_exists($slug, $this->register)) {
            return null;
        }
        return $this->register[$slug];
    }
}