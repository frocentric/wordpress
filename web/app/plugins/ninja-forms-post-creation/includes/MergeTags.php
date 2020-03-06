<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_MergeTags_Fields
 */
final class NF_CreatePost_MergeTags extends NF_Abstracts_MergeTags
{
    protected $id = 'created_posts';

    protected $permalink = '';

    public function __construct()
    {
        parent::__construct();
        $this->title = __( 'Created Posts', 'ninja-forms-create-post' );

        $this->merge_tags[ 'permalink' ] = array(
            'id' => 'permalink',
            'label' => __( 'Permalink' ),
            'tag' => '{post:permalink}',
            'callback' => 'get_permalink',
            'value' => ''
        );
    }

    public function get_permalink()
    {
        return $this->permalink;
    }

    public function set_permalink( $post_id )
    {
        $this->permalink = get_permalink( $post_id );
    }

} // END CLASS NF_CreatePost_MergeTags
