<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class NF_CreatePost_TaxonomyAdapter
{
    private $post_type = '';

    private $prefix = '';

    private $group = '';

    private $terms = array();

    private $settings = array();

    public function __construct( $taxonomy )
    {
        if( ! is_array( $taxonomy->object_type ) ) return;
        $this->post_type = $taxonomy->object_type[ 0 ];
        $this->prefix = $taxonomy->name . '_';
        $this->group = 'taxonomy_' . $taxonomy->name;
        $this->terms = get_terms( $taxonomy->name, array( 'hide_empty' => false ) );
        $this->taxonomy_name = $taxonomy->name;
    }

    public function get_term_settings()
    {
        if( $this->terms && is_array( $this->terms ) ){
            $this->settings[] = array(
                'name' => $this->taxonomy_name,
                'type' => 'field-select',
                'group' => $this->group,
                'label' => __( 'Term Field Mapping', 'ninja-forms-create-post' ),
                'width' => 'full',
                'field_types' => array( 'terms' ),
                'field_filter' => array(
                    'terms' => array(
                        'taxonomy' => $this->taxonomy_name
                    ),
                ),
                'help' => __( 'Use a Term Field to select terms on the form.', 'ninja-forms-create-post' )
            );

            foreach( $this->terms as $term ){
                $this->get_term_setting( $term );
            }
        }

        if( $this->settings ) {
            return $this->settings;
        } else {
            return array(
                array(
                    'name' => $this->group . '_not_terms',
                    'type' => 'html',
                    'group' => $this->group,
                    'label' => '',
                    'value' => sprintf( __( 'No available terms for this taxonomy. %sAdd a term%s (Will require a page refresh)', 'ninja-forms-create-post' ), '<a target="_blank" href="' . admin_url( "edit-tags.php?taxonomy=$this->taxonomy_name" ) . '">', ' <i class="fa fa-external-link" aria-hidden="true"></i></a>' ),
                    'width' => 'full'
                )
            );
        }
    }

    private function get_term_setting( $term )
    {
        $name = $this->prefix . $term->term_id;
        $this->settings[] = array(
            'name' => $name,
            'type' => 'toggle',
            'group' => $this->group,
            'label' => $term->name,
            'width' => 'full'
        );
    }
}
