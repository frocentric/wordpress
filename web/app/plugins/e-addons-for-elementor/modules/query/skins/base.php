<?php

namespace EAddonsForElementor\Modules\Query\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use EAddonsForElementor\Base\Base_Skin;
use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Core\Utils\Query as Query_Utils;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Base Skin
 *
 * Elementor widget query-posts for e-addons
 *
 */
class Base extends Base_Skin {

    //use Traits\Infinite_Scroll;
    //use Traits\Pagination;
    //use Traits\Hover;
    //use Traits\Reveal;

    //use Traits\Post;
    //use Traits\Term;
    //use Traits\User;
    //use Traits\Media;
    //use Traits\Item;
    use Traits\Common;
    use Traits\Custommeta;
    use Traits\Label;

    public static $widgets = [
        'e-query-posts',
        'e-query-media',
        'e-query-users',
        'e-query-terms',
        'e-query-itemslist',
        'e-query-repeater',
    ];
    public $current_permalink;
    public $current_id;
    public $current_data;
    public $counter = 0;
    public $index = 0;
    public $itemindex = 0;
    public $depended_scripts = [];
    public $depended_styles = [];
    public $parent;
    public $widget;

    public function show_in_settings() {
        return in_array(get_class($this), ["EAddonsForElementor\Modules\Query\Skins\Base", "EAddonsForElementor\Modules\Query\Skins\Grid"]) ? false : true;
    }

    public function _register_controls_actions() {
        if ($this->parent) {

            parent::_register_controls_actions();

            /* if (!has_action('elementor/element/' . $this->parent->get_name() . '/section_items/after_section_end', [$this, 'register_controls_layout'])) {
              add_action('elementor/element/' . $this->parent->get_name() . '/section_items/after_section_end', [$this, 'register_controls_layout']);
              }

              if (!has_action('elementor/element/' . $this->parent->get_name() . '/section_items/before_section_start', [$this, 'register_controls_hovereffects'])) {
              add_action('elementor/element/' . $this->parent->get_name() . '/section_items/before_section_start', [$this, 'register_controls_hovereffects']);
              } */

            add_action('elementor/element/' . $this->parent->get_name() . '/section_e_query/after_section_end', [$this, 'register_additional_controls'], 20);
            add_action('elementor/element/' . $this->parent->get_name() . '/section_blocks_style/after_section_end', [$this, 'register_style_controls']);
            
            add_action('elementor/preview/enqueue_scripts', [$this, 'preview_enqueue']);

            add_action('e_addons/query/skin_icon', [$this, 'add_skin_icon']);
        }
        //var_dump(get_class($this));
    }

    //public function add_skin_icon($widget) {}
    public function add_skin_icon($widget) {
        if (empty($widget->get_controls('skin_dis_' . $this->get_id()))) {
            // skin: Carousel
            $widget->add_control(
                    'skin_dis_' . $this->get_id(),
                    [
                        'type' => Controls_Manager::RAW_HTML,
                        'show_label' => false,
                        'raw' => '<i class="eaddicon-skin ' . $this->get_icon() . '"></i>',
                        'content_classes' => 'e-add-skin-dis',
                        'condition' => [
                            '_skin' => $this->get_id()
                        ],
                    ]
            );
        }
    }

    public function register_additional_controls() {
        //$querytype = $this->parent->get_querytype();
    }
    public function register_style_controls() {
        //$querytype = $this->parent->get_querytype();
    }
    

    public function get_instance_value($control_base_id) {
        if ($this->parent->get_settings_for_display($control_base_id))
            return $this->parent->get_settings_for_display($control_base_id);
        $control_id = $this->get_control_id($control_base_id);
        return $this->parent->get_settings_for_display($control_id);
    }

    public function render() {

        if (!$this->parent) {
            return;
        }
        //var_dump($this->parent);

        $this->parent->render();

        /** @p elaboro la query... */
        $this->parent->query_the_elements();

        /** @p qui prendo il valore di $query elaborato in query-base.php */
        $query = $this->parent->get_query();
        $querytype = $this->parent->get_querytype();
        $element_id = $this->parent->get_id();
        //var_dump($element_id);

        if (apply_filters('e_addons/query/should_render/' . $element_id, true, $this, $query)) {

            $this->parent->skin = $this; // access to current skin from widget

            global $e_widget_query;
            $previuos = $e_widget_query;

            $e_widget_query = $this;

            /** @p enquequo gli script e gli style... */
            $this->enqueue();

            $this->render_loop_start();

            $this->index = $this->get_index_start();
            $this->counter = 0;

            //var_dump('e_addons/query/'.$querytype);
            do_action('e_addons/query/' . $element_id, $this, $query);

            $this->render_loop_end();

            $this->parent->render_pagination();

            if ($previuos) {
                $e_widget_query = $previuos;
            }
        } else {
            $this->render_no_results();
        }
    }

    public function render_no_results() {
        $query_no_result = $this->parent->get_settings_for_display('query_no_result');
        //var_dump($query_no_result);
        if ($query_no_result) {
            $query_no_result_txt = $this->parent->get_settings_for_display('query_no_result_txt');
            if (!empty($query_no_result_txt)) {
                $query_no_result_txt = Utils::get_dynamic_data($query_no_result_txt);
                echo $query_no_result_txt;
            }
        }
    }

    public function render_full_block_link() {
        $settings = $this->parent->get_settings_for_display();
        if (!empty($settings['templatemode_linkable'])) {
            $link = $this->get_item_link($settings, $this->current_id);
            echo '<a class="e-full-block-link" href="' . $link . '"></a>';
        }
    }

    public function render_element_item() {
        $parent = $this->parent;
        $this->index++;

        $style_items = $this->parent->get_settings_for_display('style_items');

        $this->render_item_start();

        switch ($style_items) {
            case 'template':
                $this->render_template();
                break;
            case 'html':
                $this->render_custom_html();
                break;
            default:
                $this->render_items();
        }

        //
        $this->render_full_block_link();

        $this->render_item_end();

        $this->counter++;
        if ($parent) {
            $this->parent = $parent;
        }
    }

    public function render_custom_html() {
        $html = $this->parent->get_settings_for_display('block_custom_html');
        $vars = [
            $this->parent->get_querytype() => $this->current_data,
            'block' => $this->current_data,
        ];
        $html = Utils::get_dynamic_data($html, $vars);
        //$html = Utils::get_dynamic_data($html, $this->current_data, $this->parent->get_querytype());
        echo $html;
    }

    public function render_template($template_id = false) {

        $template_id = $template_id ? $template_id : $this->parent->get_settings_for_display('template_id');
        $templatemode_enable_2 = $this->parent->get_settings_for_display('templatemode_enable_2');
        $template_2_id = $this->parent->get_settings_for_display('template_2_id');

        $post_template_id = $template_id;
        if ($templatemode_enable_2 && !($this->index % 2 )) {
            // Odd
            $post_template_id = $template_2_id;
        }

        if ($post_template_id)
            $this->render_e_template($post_template_id);
    }

    public function render_e_template($id_temp) {

        $args = array();
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $args['css'] = true;
        }

        $querytype = $this->parent->get_querytype();
        if ($querytype == 'user') {
            $querytype = 'author';
        }
        if (in_array($querytype, ['media', 'attachment','product'])) {
            $querytype = 'post';
        }
        $args[$querytype . '_id'] = $this->current_id;
        //var_dump($args);
        echo \EAddonsForElementor\Core\Managers\Template::e_template($id_temp, $args);
    }

    /* !!!!!!!!!!!!!!!!!!! ITEMS !!!!!!!!!!!!!!!!! */

    public function render_items() {
        $_skin = $this->parent->get_settings_for_display('_skin');
        $style_items = $this->parent->get_settings_for_display('style_items');

        //@p in caso di justifiedgrid forzo lo style_items in "float"
        //@p probabilmente farò lo stesso anche per lo skin rhombus ex diamond
        if ($_skin == 'justifiedgrid') {
            $style_items = 'float';
        }
        //@p in caso di rapidimmages forzo lo style_items in "right"
        if ($_skin == 'rapidimages') {
            $style_items = 'right';
        }

        //@p questo interviene per animare al rollhover il blocco di contenuto
        $hover_animation = $this->get_instance_value('hover_content_animation');
        $animation_class = !empty($hover_animation) && $style_items != 'float' ? ' elementor-animation-' . $hover_animation : '';

        //@p questo interviene per fare gli effetti di animazione al rollhover in caso di layout-FLOAT
        $hover_effects = $this->get_instance_value('hover_text_effect');
        $hoverEffects_class = !empty($hover_effects) && $style_items == 'float' ? ' e-add-hover-effect-' . $hover_effects . ' e-add-hover-effect-content e-add-close' : '';

        $hoverEffects_start = !empty($hover_effects) && $style_items == 'float' ? '<div class="e-add-hover-effect-' . $hover_effects . ' e-add-hover-effect-content e-add-close">' : '';
        $hoverEffects_end = !empty($hover_effects) && $style_items == 'float' ? '</div>' : '';

        //@p NOTA:  [x timeline] una piccola considerazione a timeline....
        //forzo la timeliine al layout default e non uso l'immagine nel content
        if (($style_items && $style_items != 'default' && $_skin != 'timeline') || $_skin == 'justifiedgrid') {
            // Layouts
            //echo 'sono un layout: left/right/alternate/textZone/overlay/float';
            echo '<div class="e-add-image-area e-add-item-area">';
            $this->render_items_image();
            echo '</div>';
            //if (!empty($this->parent->get_settings_for_display('list_items'))) {
            echo $hoverEffects_start . '<div class="e-add-content-area e-add-item-area' . $animation_class . '">';
            $this->render_items_content(false); //@p il false non produce l'immagine
            echo '</div>' . $hoverEffects_end;
            //}
        } else {
            // Layout-default
            //echo 'sono layout-default';
            if ($_skin == 'timeline') {
                $this->render_items_content(false); //@p il false non produce l'immagine
            } else {
                $this->render_items_content(true);
            }
        }
    }

    // IMAGE
    public function render_items_image() {
        $_items = $this->parent->get_settings_for_display('list_items');
        $querytype = $this->parent->get_querytype();

        //QUERY_MEDIA //////////////////
        //@p l'immagine viene renderizzata sempre per il query_media widget
        if ($querytype == 'attachment') {
            $item = array(
                '_id' => 'e-add-media-image',
                'item_type' => 'item_image',
            );
            $this->render_repeateritem_start($item);
            //----------------------------------
            $this->render_item_image($this->parent->get_settings_for_display());
            //----------------------------------
            $this->render_repeateritem_end();
        } else {
            if (!empty($_items)) {
                // ITEMS ///////////////////////
                foreach ($_items as $key => $item) {
                    //
                    if (!empty($item['item_type'])) {
                        switch ($item['item_type']) {
                            case 'item_image':
                                $this->render_repeateritem_start($item);
                                //----------------------------------
                                $this->render_item_image($item, $key);
                                //----------------------------------
                                $this->render_repeateritem_end();
                                break;
                            case 'item_imageoricon':
                                $this->render_repeateritem_start($item);
                                //----------------------------------
                                //$this->render_item_imageoricon($item);
                                //do_action('e_addons/query/item', 'item_imageoricon', $item, $key);
                                do_action('e_addons/query/render_item/item_imageoricon', $item, $key, $this->parent);
                                //do_action('e_addons/query/item/item_imageoricon', $item, $key, $this->parent);
                                //----------------------------------
                                $this->render_repeateritem_end();
                                break;
                            case 'item_avatar':
                                $this->render_repeateritem_start($item);
                                //----------------------------------
                                do_action('e_addons/query/render_item/item_avatar', $item, $key, $this->parent);
                                //----------------------------------
                                $this->render_repeateritem_end();
                        }
                    }
                }
            }
        }
    }

    // FIELDS
    public function render_items_content($useimg = true) {
        $_items = $this->parent->get_settings_for_display('list_items');
        $querytype = $this->parent->get_querytype();
        //
        // QUERY_MEDIA //////////////////
        //@p l'immagine viene renderizzata sempre per il query_media widget
        if ($querytype == 'attachment' && $useimg) {
            $item = array(
                '_id' => 'e-add-media-image',
                'item_type' => 'item_image',
            );
            $this->render_repeateritem_start($item);
            //----------------------------------
            $this->render_item_image($this->parent->get_settings_for_display());
            //----------------------------------
            $this->render_repeateritem_end();
        }
        if (!empty($_items)) {
            // ITEMS ///////////////////////

            foreach ($_items as $key => $item) {

                if (in_array($item['item_type'], ['item_image', 'item_avatar', 'item_imageoricon']) && !$useimg)
                    continue;

                $this->render_repeateritem_start($item);
                //----------------------------------
                //
                $this->render_repeateritem($item, $key);

                //----------------------------------
                $this->render_repeateritem_end();

                $this->itemindex = $key;
            }
        }
    }

    public function render_repeateritem($item, $key = 0) {
        //var_dump($item['item_type']);
        if (!empty($item['item_type'])) {
            $item_type = $item['item_type'];

            ob_start();
            do_action('e_addons/query/item', $item_type, $item, $key);
            do_action('e_addons/query/render_item/' . $item_type, $item, $key, $this->parent);
            $item_html = ob_get_clean();
            $item_html = apply_filters('e_addons/query/render/' . $item_type, $item_html, $item, $key, $this->parent);
            echo $item_html;

            switch ($item_type) {
                // post
                //case 'item_title': $this->render_item_title($item, $key); break;
                //case 'item_date': $this->render_item_date($item, $key); break;
                //case 'item_author': $this->render_item_author($item); break;
                //case 'item_termstaxonomy': $this->render_item_termstaxonomy($item); break;
                //case 'item_excerpt': $this->render_item_content($item, true); break;
                //case 'item_content': $this->render_item_content($item); break;
                //case 'item_posttype': $this->render_item_posttype($item); break;
                //----------------------------------
                // media
                //case 'item_imagemeta': $this->render_item_imagemeta($item); break;
                //case 'item_mimetype': $this->render_item_mimetype($item); break;
                //case 'item_caption': $this->render_item_caption($item); break;
                //case 'item_alternativetext': $this->render_item_alternativetext($item); break;
                //case 'item_uploadedto': $this->render_item_uploadedto($item); break;
                //case 'item_guid': $this->render_item_guid($item); break;
                //----------------------------------
                // common
                //case 'item_custommeta': $this->render_item_custommeta($item, $key); break;
                //case 'item_readmore': $this->render_item_readmore($item); break;
                //case 'item_label': $this->render_item_labelhtml($item); break;
                //case 'item_index': $this->render_item_index($item); break;
                //case 'item_template': $this->render_item_template($item); break;
                //case 'item_image': if ($useimg) { $this->render_item_image($item, $key); } break;
                // user
                //case 'item_displayname': $this->render_item_userdata('displayname', $item); break;
                //case 'item_user': $this->render_item_userdata('user', $item); break;
                //case 'item_role': $this->render_item_userdata('role', $item); break;
                //case 'item_firstname': $this->render_item_userdata('firstname', $item); break;
                //case 'item_lastname': $this->render_item_userdata('lastname', $item); break;
                //case 'item_nickname': $this->render_item_userdata('nickname', $item); break;
                //case 'item_email': $this->render_item_userdata('email', $item); break;
                //case 'item_website': $this->render_item_userdata('website', $item); break;
                //case 'item_bio': $this->render_item_userdata('bio', $item); break;
                //case 'item_registered': $this->render_item_date($item); break; //render_item_userdata('registered', $item); break;
                //case 'item_avatar': if ($useimg) { do_action('e_addons/query/item', 'item_avatar', $item, $key); } break;
                //----------------------------------
                // terms
                //case 'item_counts': $this->render_item_postscount($item); break;
                //case 'item_taxonomy': $this->render_item_taxonomy($item); break;
                //case 'item_description': $this->render_item_description($item); break;
                //----------------------------------
                // items list
                //case 'item_subtitle': $this->render_item_subtitle($item); break;
                //case 'item_descriptiontext': $this->render_item_descriptiontext($item); break;
                //case 'item_imageoricon': if ($useimg) { $this->render_item_imageoricon($item); } break;
                //----------------------------------
                default:
                //var_dump($item_type);
            }
        }
    }

    // REPEATER-ITEM start
    public function render_repeateritem_start($item, $tag = 'div') {
        $itemcustomclass = !empty($item['item_custom_class']) ? $item['item_custom_class'] . ' ' : '';
        $classItem = 'class="' . $itemcustomclass . ' e-add-item e-add-' . $item['item_type'] . ' elementor-repeater-item-' . $item['_id'] . '"';
        $dataIdItem = ' data-item-id="' . $item['_id'] . '"';

        echo '<' . $tag . ' ' . $classItem . $dataIdItem /* $this->parent->get_render_attribute_string('eadditem_' . $id . '_' . $item_type) */ . '>';
        $this->render_iteminner_before();
    }

    // REPEATE-ITEM end
    public function render_repeateritem_end($tag = 'div') {
        $this->render_iteminner_after();
        echo '</' . $tag . '>';
    }

    /////////////////////////////////////////////////////////////
    // render post item -----------------------------------------
    public function render_item_start($key = 'post') {
        $hover_animation = $this->get_instance_value('hover_animation');
        $animation_class = !empty($hover_animation) ? ' elementor-animation-' . $hover_animation : '';

        $use_overlay_hover = $this->get_instance_value('use_overlay_hover');

        $_skin = $this->parent->get_settings_for_display('_skin');
        $style_items = $this->parent->get_settings_for_display('style_items');

        //@p in caso di justifiedgrid forzo lo style_items in "float"
        //@p probabilmente farò lo stesso anche per lo skin rhombus ex diamond
        if ($_skin == 'justifiedgrid') {
            $style_items = 'float';
        }
        if ($_skin == 'rapidimages') {
            $style_items = 'rights';
        }
        //@p ..questo è il background per il block
        $overlayhover = '';
        if ($use_overlay_hover) {
            $overlayhover = ' e-add-post-overlayhover';
        }
        $hover_effects = $this->get_instance_value('hover_text_effect');
        $hoverEffects_class = !empty($hover_effects) && $style_items == 'float' ? ' e-add-hover-effects' : '';

        //@p data post ID
        $data_post_id = ' data-post-id="' . $this->current_id . '"';
        //@p data post INDEX
        $data_post_index = ' data-index="' . $this->counter . '"';
        //@p una classe personalizzata per lo skin
        $item_class = ' ' . $this->get_item_class() . ' ' . $this->parent->get_item_class();
        //@p una serie di id-nome utili
        $data_atttributes = $this->get_item_dataattributes();
        ?>
        <article <?php
        post_class(['e-add-querytype-' . $key . ' e-add-post e-add-post-item e-add-post-item-' . $this->parent->get_id() . $item_class]);
        echo $data_post_id . $data_post_index . $data_atttributes;
        ?>>
        <?php
        //prima di block
        $this->render_item_before();
        ?>
            <div class="e-add-post-block e-add-post-block-<?php echo $this->counter . $overlayhover . $hoverEffects_class . $animation_class; ?>">

                <?php
            }

            public function render_item_end() {
                ?>
            </div>
                <?php
                //dopo di block
                $this->render_item_after();
                ?>
        </article>
            <?php
        }

        ////////////////////////////////////////////////////////////////
        // render loop wrapper -----------------------------------------
        public function render_loop_start() {


            // TO DO
            /** @p qui prendo il valore di $query elaborato in query.php /base */
            /*
              $wp_query = $this->parent->get_query();

              // @p questa è una classe che c'è solo se ci sono posts, identifica se non è vuoto.
              if ( $wp_query->found_posts ) {
              $classes[] = 'e-add-grid';
              }
             */
            $this->parent->add_render_attribute('eaddposts_container', [
                'class' => [
                    'e-add-posts-container',
                    'e-add-posts',
                    $this->get_scrollreveal_class(), //@p prevedo le classi per generare il reveal,
                    $this->get_container_class(), //@p una classe personalizzata per lo skin
                    $this->parent->get_container_class(),
                ],
            ]);
            $this->parent->add_render_attribute('eaddposts_container_wrap', [
                'class' => [
                    'e-add-posts-wrapper',
                    $this->get_wrapper_class(), //@p una classe personalizzata per lo skin
                    $this->parent->get_wrapper_class(),
                ],
            ]);
            ?>
        <?php $this->render_container_before(); ?>
        <div <?php echo $this->parent->get_render_attribute_string('eaddposts_container'); ?>>
        <?php $this->render_posts_before(); ?>
            <div <?php echo $this->parent->get_render_attribute_string('eaddposts_container_wrap'); ?>>
        <?php
        $this->render_postsWrapper_before();
    }

    public function render_loop_end() {
        $this->render_postsWrapper_after();
        ?>
            </div>
                <?php
                $this->render_posts_after();
                ?>
        </div>
            <?php $this->render_container_after(); ?>
            <?php
        }

        //container
        public function render_container_before() {
            
        }

        public function render_container_after() {
            
        }

        //posts
        public function render_posts_before() {
            
        }

        public function render_posts_after() {
            
        }

        //item
        public function render_item_before() {
            
        }

        public function render_item_after() {
            
        }

        //item inner
        public function render_iteminner_before() {
            
        }

        public function render_iteminner_after() {
            
        }

        //postsWrapper
        public function render_postsWrapper_before() {
            
        }

        public function render_postsWrapper_after() {
            
        }

        //img
        public function add_img_class() {
            
        }

        public function render_img_before() {
            
        }

        public function render_img_after() {
            
        }

        // Classes ----------
        public function get_container_class() {
            return 'e-add-proto-container e-add-skin-' . $this->get_id() . ' e-add-skin-' . parent::get_id();
        }

        public function get_wrapper_class() {
            return 'e-add-proto-wrapper e-add-wrapper-' . $this->get_id() . ' e-add-wrapper-' . parent::get_id();
        }

        public function get_item_class() {
            return 'e-add-proto-item e-add-item-' . $this->get_id() . ' e-add-item-' . parent::get_id();
        }

        public function get_item_dataattributes() {
            
        }

        public function get_image_class() {
            
        }

        public function get_scrollreveal_class() {
            return '';
        }

        // Utility ----------
        public function filter_excerpt_length() {
            return $this->get_instance_value('textcontent_limit');
        }

        public function filter_excerpt_more($more) {
            return '';
        }

        public function limit_content($limit = 100) {
            $post = get_post();
            $content = $post->post_content; //do_shortcode($post['post_content']); //$content_post->post_content; //
            $content = $this->limit_text($content, $limit);
            return $content;
        }

        public function limit_text($content, $limit = 100, $extra = '...') {
            $content = wp_strip_all_tags($content);
            if (strlen($content) > $limit) {
                $content = substr($content, 0, $limit) . $extra; //
                // TODO preserve words
            }
            return $content;
        }

        public function get_item_link($settings, $obj_id = 0) {

            if (!empty($settings['gallery_link'])) {
                // MEDIA
                $settings['use_link'] = $settings['gallery_link'];
            }
            if (!empty($settings['templatemode_linkable'])) {
                // BLOCK
                $settings['use_link'] = $settings['templatemode_linkable'];
            }
            if (!empty($settings['link_to'])) {
                // CUSTOM META
                $settings['use_link'] = $settings['link_to'];
            }

            if (!empty($settings['use_link'])) {
                switch ($settings['use_link']) {
                    case 'custom':
                        $link = false;
                        if (!empty($settings['link']['url'])) {
                            $link = $settings['link']['url'];
                        }
                        if (!empty($settings['templatemode_linkable_link']['url'])) {
                            $link = $settings['templatemode_linkable_link']['url'];
                        }
                        if (!empty($settings['shortcode_link'])) {
                            $link = $settings['shortcode_link'];
                            $raw_settings = $this->parent->get_settings('list_items');
                            foreach ($raw_settings as $raw_setting) {
                                if (!empty($raw_setting['_id']) && !empty($settings['_id']) && $raw_setting['_id'] == $settings['_id']) {
                                    //var_dump($this->current_data); die();
                                    $link = $raw_setting['shortcode_link'];
                                }
                            }
                        }
                        if ($link) {
                            $type = $this->parent->get_querytype();
                            $args = [
                                $type => $this->current_data,
                                'block' => $this->current_data,
                            ];
                            $link = Utils::get_dynamic_data($link, $args);
                            return $link;
                        }
                        break;
                    case 'shortcode':
                        if (!empty($settings['shortcode_link'])) {
                            ob_start();
                            do_shortcode($settings['shortcode_link']);
                            $link = ob_get_clean();
                            return $link;
                        }
                        break;
                    case 'popup':
                        if (!empty($settings['popup_link'])) {
                            $theme_builder = \Elementor\Plugin::instance()->modules_manager->get_modules('theme-builder');
                            if (Utils::is_plugin_active('elementor-pro')) {
                                if (!$theme_builder) {
                                    $class_name = '\ElementorPro\Modules\ThemeBuilder\Module';
                                    /** @var Module_Base $class_name */
                                    if ($class_name::is_active()) {
                                        $theme_builder = $class_name::instance();
                                    }
                                }
                            }
                            if ($theme_builder) {
                                $popup_id = $settings['popup_link'];
                                $link_action_url = \Elementor\Plugin::instance()->frontend->create_action_hash('popup:open', [
                                    'id' => $popup_id,
                                    'toggle' => true,
                                ]);
                                $link_action_url = str_replace(':', '%3A', $link_action_url);
                                $link_action_url = str_replace('=', '%3D', $link_action_url);
                                $link_action_url = str_replace('%3D%3D', '%3D', $link_action_url);
                                $link_action_url = str_replace('&', '%26', $link_action_url);
                                //
                                $theme_builder->get_locations_manager()->add_doc_to_location('popup', $popup_id);
                                return $link_action_url;
                            }
                        }
                        break;
                    case 'file':
                        if ($this->current_permalink == $this->current_data) {
                            return $this->current_permalink;
                        }
                        $media = get_post($obj_id);
                        return $media->guid;
                    case 'attachment':
                        //@p se il lightbox è attivo $page_permalink va in false
                        //$url = ($this->parent->get_settings_for_display('open_lightbox') == 'no') ? get_attachment_link($obj_id) : wp_get_attachment_url($obj_id);
                        $url = get_attachment_link($obj_id);
                        return $url;
                    case 'none':
                        break;
                    case 'yes':
                    default:
                        if (!is_wp_error($this->current_permalink)) {
                            return $this->current_permalink;
                        }
                }
            }
            return false;
        }

        public function get_index_start() {
            $paged = Utils::get_current_page_num();
            $settings = $this->parent->get_settings_for_display();
            $offset = empty($settings['posts_offset']) ? 0 : intval($settings['posts_offset']);
            $offset = empty($settings['offset']) ? $offset : intval($settings['offset']);
            if ($paged > 1) {
                if (!empty($settings['pagination_enable'])) {
                    $query = $this->parent->get_query();
                    $querytype = $this->parent->get_querytype();
                    $no = apply_filters('e_addons/query/per_page/' . $this->parent->get_id(), get_option('posts_per_page'), $this, $query, $settings);
                    $start = $no * ($paged - 1) + $offset;
                    //var_dump($start);
                    return $start;
                }
            }
            return $offset;
        }
        
        public function set_widget($parent) {
            $this->widget = $parent;
        }
        public function restore_parent() {
            if ($this->widget) {
                $this->parent = $this->widget;
            }
        }

    }
    