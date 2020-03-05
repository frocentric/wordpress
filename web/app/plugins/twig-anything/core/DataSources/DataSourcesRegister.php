<?php
/**
 * Copyright (c) 2015-2016 by ANTON ANDRIIEVSKYI, the plugin author.
 * See LICENSE.txt for license details.
 */

namespace TwigAnything\DataSources;

class DataSourcesRegister
{
    /**
     * @var DataSourceInterface[]
     */
    private $register = array();

    /**
     * @param DataSourceInterface $source
     */
    public function register(DataSourceInterface $source) {
        $slug = $source->getSlug();
        if (array_key_exists($slug, $this->register)) {
            throw new DataSourceSlugExistsException('Data Source with this slug already exists.');
        }
        $this->register[$slug] = $source;
    }

    public function registerStandardDataSources() {
        do_action('twig_anything_before_register_standard_data_sources', $this);
        $this->register(new EmptyDataSource);
        $this->register(new Url);
        $this->register(new MySQL);
        $this->register(new File);
        do_action('twig_anything_after_register_standard_data_sources', $this);
        do_action('twig_anything_register_custom_data_sources', $this);
    }

    /**
     * @return DataSourceInterface[]
     */
    public function dataSources() {
        return $this->register;
    }

    /**
     * Get a registered instance of DataSourceInterface by its slug.
     * Returns NULL of not found.
     *
     * @param $slug
     * @return DataSourceInterface
     */
    public function getBySlug($slug) {
        if (!array_key_exists($slug, $this->register)) {
            return null;
        }
        return $this->register[$slug];
    }

    /**
     * Get an array with meta information for GUI building
     * @return array
     */
    public function getMetaForGui() {
        $res = array();
        foreach($this->register as $ds) {
            $res[] = array(
                'slug' => $ds->getSlug(),
                'shortName' => $ds->getShortName(),
                'longName' => $ds->getLongName(),
                'description' => $ds->getDescription()
            );
        }
        return $res;
    }
}