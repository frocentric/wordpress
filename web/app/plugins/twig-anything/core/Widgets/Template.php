<?php

namespace TwigAnything\Widgets;

use TwigAnything\Shortcodes;
use TwigAnything\TwigTemplate;

class Template extends \WP_Widget
{
    /**
     * Sets up the widgets name, description and configuration.
     */
    public function __construct() {
        parent::__construct(
            'twig_anything_template_widget',
            __('Twig Template', 'twig-anything'),
            array(
                'description' => __('A rendered Twig Template', 'twig-anything'),
            )
        );
    }

    /**
     * @param $widgetInstance
     * @return TwigTemplate
     */
    private static function loadTwigTemplate($widgetInstance) {
        $slugOrId = array_key_exists('template_slug_or_id', $widgetInstance)?
            trim($widgetInstance['template_slug_or_id']) : '';
        if (ctype_digit($slugOrId)) {
            return TwigTemplate::loadById($slugOrId);
        }
        else {
            return TwigTemplate::loadPublishedBySlug($slugOrId);
        }
    }

    /**
     * Outputs the content of the widget.
     *
     * @param array $args     Display arguments including before_title, after_title,
     *                        before_widget, and after_widget.
     * @param array $instance The settings for the particular instance of the widget.
     */
    public function widget($args, $instance) {
        $slugOrId = array_key_exists('template_slug_or_id', $instance)?
            trim($instance['template_slug_or_id']) : '';

        $title = empty($instance['title'])? '' : $instance['title'];

        # If none of parameters are specified, skip rendering the widget entirely
        if ($slugOrId === '' && $title === '') {
            return;
        }

        # Doing it the WP-way - just adapted this line from WP_Widget_Text
        $title = apply_filters(
            'widget_title',
            $title,
            $instance,
            $this->id_base
        );

        # Only render template if slug or id is not empty
        if (!empty($slugOrId)) {
            if (ctype_digit($slugOrId)) {
                $shortcodeAtts = array('id' => $slugOrId);
            }
            else {
                $shortcodeAtts = array('slug' => $slugOrId);
            }
            $shortcodes = new Shortcodes;
            $text = $shortcodes->shortcodeTwigAnything($shortcodeAtts);
        }
        # If slug/id is empty, do not show any errors, just output empty text
        else {
            $text = '';
        }

        # Doing it the WP-way - just adapted this line from WP_Widget_Text
        $text = apply_filters('widget_text', $text, $instance);

        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        echo $text;
        echo $args['after_widget'];
    }

    /**
     * Outputs the options form on admin.
     *
     * @param array $instance The widget options
     * @return void
     */
    public function form($instance) {
        $title = array_key_exists('title', $instance)? $instance['title'] : '';
        $titleFieldId = $this->get_field_id('title');
        $titleFieldName = $this->get_field_name('title');
        $titleLabelLocalized = _('Title:');
        $titleValueEscaped = esc_attr($title);

        $slugOrId = array_key_exists('template_slug_or_id', $instance)? trim($instance['template_slug_or_id']) : '';
        $slugOrIdFieldId = $this->get_field_id('template_slug_or_id');
        $slugOrIdFieldName = $this->get_field_name('template_slug_or_id');
        $slugOrIdLabelLocalized = __('Slug or ID of a Twig Template:', 'twig-anything');
        $slugOrIdValueEscaped = esc_attr($slugOrId);

        echo <<<HTML
<p>
    <label for="$titleFieldId">$titleLabelLocalized</label>
	<input class="widefat" id="$titleFieldId" name="$titleFieldName" type="text" value="$titleValueEscaped">
</p>
<p>
    <label for="$slugOrIdFieldId">$slugOrIdLabelLocalized</label>
	<input class="widefat" id="$slugOrIdFieldId" name="$slugOrIdFieldName" type="text" value="$slugOrIdValueEscaped">
</p>
HTML;
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     * @return array
     */
    public function update($new_instance, $old_instance) {
        $title = array_key_exists('title', $new_instance)? trim(strip_tags($new_instance['title'])) : '';
        $slugOrId = array_key_exists('template_slug_or_id', $new_instance)? trim($new_instance['template_slug_or_id']) : '';

        return array_merge($old_instance, array(
            'title' => $title,
            'template_slug_or_id' => $slugOrId,
        ));
    }
}
