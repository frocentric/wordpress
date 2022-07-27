<?php

namespace EAddonsDev\Modules\Remote\Widgets;

use Elementor\Controls_Manager;
use EAddonsForElementor\Base\Base_Widget;
use EAddonsForElementor\Core\Utils;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor Scrap
 *
 * Elementor widget for e-addons
 *
 */
class Scrap extends Base_Widget {
    
    use \EAddonsDev\Modules\Remote\Traits\Cache;

    public function get_name() {
        return 'e-scrap';
    }

    public function get_title() {
        return esc_html__('Scrap', 'e-addons');
    }

    public function get_pid() {
        return 619;
    }

    public function get_icon() {
        return 'eadd-remote-scraping';
    }

    protected function register_controls() {
        $this->start_controls_section(
                'section_scrap', [
            'label' => esc_html__('Scraped Content', 'e-addons'),
                ]
        );

        $this->add_control(
                'url', [
            'label' => esc_html__('External Page URL to scrap', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'https://e-addons.com/',
            'label_block' => true,
                ]
        );

        $this->add_remote_options();

        $this->add_control(
                'tag_id', [
            'label' => esc_html__('Unique CSS ID, Tag Name or CSS Class', 'e-addons'),
            'description' => esc_html__('Include a specific piece of content of remote page', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'body, footer, #my_section, h1.big',
            'default' => 'body',
                ]
        );

        $this->add_control(
                'outer_html', [
            'label' => esc_html__('Outer HTML', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'description' => esc_html__('Include Outer HTML tag', 'e-addons'),
                ]
        );

        $this->add_control(
                'limit_tags', [
            'label' => esc_html__('Limit', 'e-addons'),
            'type' => Controls_Manager::NUMBER,
            'description' => esc_html__('Limit the results to a specified maximum size. Set 0 or blank for all.', 'e-addons'),
            'min' => 0
                ]
        );

        $this->add_control(
                'offset_contents', [
            'label' => esc_html__('Offset', 'e-addons'),
            'type' => Controls_Manager::NUMBER,            
            'description' => esc_html__('Set 0 or blank to start with the first one', 'e-addons'),
            'min' => 0,
                ]
        );
        
        $this->add_control(
                'fix_links', [
            'label' => esc_html__('Fix Links', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
            'description' => esc_html__('Enable to fix every relative link adding the remote url as prefix', 'e-addons'),
            'separator' => 'before',
                ]
        );
        $this->add_control(
                'blank_links', [
            'label' => esc_html__('Force Target Blank links', 'e-addons'),
            'type' => Controls_Manager::SWITCHER,
                ]
        );

        $this->add_cache_options();


        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        if (!empty($settings['url']) && $url = $settings['url']) {

            if (filter_var($url, FILTER_VALIDATE_URL)) {

                $content = $this->maybe_get_cache();

                if ($content !== false && !is_wp_error($content)) {

                    //$content = str_replace('https', 'http', $content); // remove ssl
                    if ($settings['tag_id']) {
                        $crawler = new \Symfony\Component\DomCrawler\Crawler($content);
                        $content = $crawler->filter($settings['tag_id'])->each(function (\Symfony\Component\DomCrawler\Crawler $node, $i) use ($settings) {
                            if ($settings['outer_html']) {
                                return $node->outerHtml();
                            }
                            return $node->html();
                        });
                    } else {
                        $content = array($content);
                    }

                    if (!Utils::empty($content)) {

                        $s = 0;
                        $offset = intval($settings['offset_contents']);
                        $limit = intval($settings['limit_tags']) + $offset;
                        foreach ($content as $key => $value) {
                            if ($limit <= 0 || $s < $limit) {
                                if ($key >= $offset) {
                                    $s++;

                                    // LAZY IMAGES
                                    $imgs = explode('<img ', $value);
                                    foreach ($imgs as $ikey => $img) {
                                        if (strpos($img, 'data-lazy-src') !== false) {
                                            $imgs[$ikey] = str_replace(' src="', 'data-src="', $imgs[$ikey]);
                                            $imgs[$ikey] = str_replace('data-lazy-src="', 'src="', $imgs[$ikey]);
                                        }
                                        if (strpos($img, 'data-lazy-srcset') !== false) {
                                            $imgs[$ikey] = str_replace(' srcset="', 'data-srcset="', $imgs[$ikey]);
                                            $imgs[$ikey] = str_replace('data-lazy-srcset="', 'srcset="', $imgs[$ikey]);
                                        }
                                        if (strpos($img, 'data-lazy-sizes') !== false) {
                                            $imgs[$ikey] = str_replace(' sizes="', 'data-sizes="', $imgs[$ikey]);
                                            $imgs[$ikey] = str_replace('data-lazy-sizes="', 'sizes="', $imgs[$ikey]);
                                        }
                                    }
                                    $value = implode('<img ', $imgs);

                                    // LINKS
                                    if (!empty($settings['fix_links'])) {
                                        $value = str_replace('href="/', 'href="' . $this->get_host($url) . '/', $value);
                                    }
                                    if (!empty($settings['blank_links'])) {
                                        $anchors = explode('<a ', $value);
                                        foreach ($anchors as $a => $anchor) {
                                            list($prev, $next) = explode('>', $anchor, 2);
                                            if (strpos($prev, ' target=') === false && $a) {
                                                $anchors[$a] = $prev.' target="_blank">' . $next;
                                            }
                                        }
                                        $value = implode('<a ', $anchors);
                                    }

                                    echo $value;                                    
                                }
                            }
                        }
                    }
                }
            } else {
                if (Utils::is_preview()) {
                    esc_html_e('Please check the remote resource url: it isn\'t in a valid format', 'e-addons');
                }
            }
        }
    }
    
    public function get_host($url) {
        $pezzi = explode('/', $url, 4);
        if (count($pezzi) > 3) {
            array_pop($pezzi);
        }
        $host = implode('/', $pezzi);
        return $host;
    }
}
