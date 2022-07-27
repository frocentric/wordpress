<?php

namespace EAddonsDev\Modules\Query\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use EAddonsForElementor\Base\Base_Widget;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;
use EAddonsForElementor\Modules\Query\Base\Query as Base_Query;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Query Rest API
 *
 * Elementor widget for E-Addons
 *
 */
class Query_Rss extends Base_Query {

    use Traits\Common;
    use \EAddonsDev\Modules\Remote\Traits\Cache;

    /*
      public function get_item_type_options() {
      return [
      'title' => 'Title',
      'description' => 'Description',
      'link' => 'Link',
      'item_custommeta' => esc_html__('Custom Fields', 'e-addons'),
      'item_label' => esc_html__('Static', 'e-addons'),
      'item_index' => esc_html__('Loop Index', 'e-addons'),
      'item_template' => esc_html__('Template', 'e-addons'),
      ];
      }
     */

    public function get_pid() {
        return 30606;
    }

    public function get_icon() {
        return 'eadd-query-rss';
    }

    public function get_name() {
        return 'e-query-rss';
    }

    public function get_title() {
        return esc_html__('Query RSS', 'e-addons');
    }

    public function get_categories() {
        return ['query-dev'];
    }

    protected $querytype = 'rss';

    protected function register_controls() {
        parent::register_controls();

        $this->controls_dev_common_content();

        $this->start_controls_section(
                'section_query_api', [
            'label' => '<i class="eaddicon eicon-settings" aria-hidden="true"></i> ' . esc_html__('Query', 'e-addons'),
            'tab' => 'e_query',
                ]
        );

        $this->add_control(
                'query_debug', [
            'label' => '<span style="color: #fff; background-color: #93003c; padding: 5px 10px; border-radius: 20px;">' . esc_html__('Show return data for DEBUG', 'e-addons') . '</span>',
            'type' => Controls_Manager::SWITCHER,
                ]
        );

        $this->add_control(
                'url', [
            'label' => esc_html__('RSS Feed URL', 'e-addons'),
            'description' => esc_html__('The full URL of RSS Feed', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'http://www.fao.org/biotech/biotech-news/rss/en/',
            'label_block' => true,
                ]
        );
        
        $this->add_remote_options();

        /* $this->add_control(
          'rows_per_page', [
          'label' => esc_html__('Rows per Page', 'e-addons'),
          'type' => Controls_Manager::NUMBER,
          'description' => esc_html__('Limit results for a specific amount.', 'e-addons'),
          'min' => -1,
          ]
          ); */

        $this->add_control(
                'offset', [
            'label' => esc_html__('Offset', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'min' => 0,
            'description' => esc_html__('Set 0 or blank to start with the first one', 'e-addons'),
                /* 'condition' => [
                  'rows_per_page!' => 1,
                  ], */
                ]
        );
        $this->add_control(
                'limit', [
            'label' => esc_html__('Limit', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
                /* 'condition' => [
                  'rows_per_page!' => 1,
                  ], */
                ]
        );

        $this->add_cache_options();

        $this->end_controls_section();

        $this->add_no_result_section();
    }

    public function get_repeater_default() {
        return [
            [
                'item_type' => 'item_custommeta',
                'metafield_key' => 'title',
            ],
            [
                'item_type' => 'item_custommeta',
                'metafield_key' => 'description',
            ],
            [
                'item_type' => 'item_custommeta',
                'metafield_key' => 'link',
            ]
        ];
    }

    // La QUERY
    public function query_the_elements() {

        $response = $this->maybe_get_cache();

        //$rss = new \SimpleXMLElement($response);
        $rss = simplexml_load_string($response, null, LIBXML_NOCDATA);
        //var_dump($response);
        // DEBUG
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if (!empty($this->get_settings_for_display('query_debug'))) {
                echo '<pre>';
                var_dump($rss);
                echo '</pre>';
            }
        }

        $this->query = $rss;
    }

    public function loop($skin, $query) {

        $settings = $skin->parent->get_settings_for_display();
        $namespaces = $query->getNamespaces(true);
        $response = $query->channel->item;

        if (!empty($query->entry)) {
            // YOUTUBE
            $response = $query->entry;
        }

        $results = array();
        foreach ($response as $item) {

            $node = $item;
            $item = (array) $item;
            //var_dump($node->children('media',true)->group); die();
            $medias = array();
            if (is_object($node)) {
                if (!empty($node->children('media', true)->group)) {
                    $medias = $node->children('media', true)->group;
                } else {
                    if (!empty($node->children('media', true))) {
                        $medias[] = $node->children('media', true);
                    }
                }
                if (!empty($medias)) {
                    foreach ($medias as $media) {
                        if (empty($media->content->attributes())) {
                            $item['media'][] = $media;
                        } else {
                            $tmp = (array) $media->content->attributes();
                            //var_dump($tmp); die();
                            if (!empty($tmp["@attributes"])) {
                                $item['media'][] = $tmp["@attributes"];
                            }
                        }
                    }
                }

                if (!empty($namespaces)) {
                    //var_dump($namespaces);
                    foreach ($namespaces as $ns => $nsdtd) {
                        if ($ns == '') {
                            
                        } else {
                            $item[$ns] = $node->children($nsdtd);
                        }
                    }
                }
            }

            //$item['title'] = $node->title;
            //$item['description'] = $node->description;
            //$item['link'] = $node->link;           
            if (!empty($item['media'])) {
                $media = reset($item['media']);
                if (!empty($media['url'])) {
                    $item['image'] = $media['url'];
                }
            }


            foreach ($item as $ns => $it) {
                if (is_object($it)) {
                    //var_dump($ns); var_dump($it); echo '<hr>';                        
                    $tmp = json_decode(json_encode($it), true);
                    foreach ($tmp as $tey => $tvalue) {
                        if (empty($tvalue)) {
                            $attr = $it->{$tey}->attributes();
                            if (!empty($attr)) {
                                $attr = json_decode(json_encode($attr), true);
                                //$tmp[$tey] = $attr;
                                $tmp[$tey]['attributes'] = $attr['@attributes'];
                            }
                        }
                    }
                    $item[$ns] = $tmp;
                }
                if (isset($item[$ns]['@attributes'])) {
                    $item[$ns]['attributes'] = $item[$ns]['@attributes'];
                    unset($item[$ns]['@attributes']);
                }
            }

            // YOUTUBE integration
            if (!empty($query->entry)) {
                $videoId = (string) $node->children('yt', true)->videoId;
                $item['image'] = (string) 'https://img.youtube.com/vi/' . $videoId . '/maxresdefault.jpg';
                $item['media']['group']['thumbnail'] = (string) 'https://img.youtube.com/vi/' . $videoId . '/mqdefault.jpg';
                if (!empty($node->children('media', true)->group->community)) {
                    $item['media']['group']['community']['statistics']['views'] = (string) $node->children('media', true)->group->community->statistics->attributes()['views'];
                    $item['media']['group']['community']['starRating']['count'] = (string) $node->children('media', true)->group->community->starRating->attributes()['count'];
                    $item['media']['group']['community']['starRating']['average'] = (string) $node->children('media', true)->group->community->starRating->attributes()['average'];
            }   }

            //echo '<pre>';var_dump($item);echo '</pre>'; die();
            $results[] = $item;
        }

        $i = 0;
        $j = 0;

        $offset = intval($settings['offset']);
        $limit = intval($settings['limit']);
        //echo '<pre>'; var_dump($results); echo '</pre>'; 
        foreach ($results as $key => $row) {
            $i++;
            $continue = false;
            if ($limit || $offset) {
                if ($offset) {
                    if ($i <= $offset) {
                        $continue = true;
                    }
                }
                if (!$continue) {
                    $j++;
                }
                if ($limit && $j > $limit) {
                    $continue = true;
                }
            }
            if (!$continue) {
                $skin->current_permalink = !empty($row['link']) ? $row['link'] : false;
                if (!empty($skin->current_permalink['attributes']['href'])) { // YT
                    $skin->current_permalink = $skin->current_permalink['attributes']['href'];
                }
                $skin->current_id = $key;
                $skin->current_data = $row;
                $skin->render_element_item();
            }
        }
    }

    public function should_render($render, $skin, $query) {
        //$results = $skin->parent->maybe_get_cache();
        //if (empty($results)) {

        if (empty($query) || (empty($query->channel->item) && empty($query->entry))) {
            return false;
        }
        return $render;
    }

    public function pagination__page_limit($page_limit, $skin, $query, $settings) {
        //$no = $settings['rows_per_page'];
        $no = empty($settings['rows_per_page']) ? get_option('posts_per_page') : intval($settings['rows_per_page']);
        if ($no && !empty($query)) {
            $total_rows = count($query->channel->item);
            $page_limit = ceil($total_rows / $no);
        }
        return $page_limit;
    }

}
