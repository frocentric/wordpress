<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_MailChimp_Admin_Metaboxes_Submission
 */
final class NF_MailChimp_Admin_Metaboxes_Submission extends NF_Abstracts_SubmissionMetabox
{
    /**
     * NF_MailChimp_Admin_Metaboxes_Submission constructor.
     */
    public function __construct()
    {

        parent::__construct();

        $this->_title = __( 'MailChimp Subscription', 'ninja-forms' );

        if( $this->sub && ! $this->sub->get_extra_value( 'mailchimp_euid' ) ){
            remove_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        }
    }

    public function render_metabox( $post, $metabox )
    {
        $list_id = $this->sub->get_extra_value( 'mailchimp_list' );
        $list_name = Ninja_Forms()->get_setting( 'mail_chimp_list_' . $list_id );

        $data = array(
            __( 'List', 'ninja-forms-mail-chimp' ) => ( $list_name ) ? $list_name : $list_id,
            __( 'Email', 'ninja-forms-mail-chimp' ) => $this->sub->get_extra_value( 'mailchimp_email' ),
            'EUID' => $this->sub->get_extra_value( 'mailchimp_euid' ),
            'LEID' => $this->sub->get_extra_value( 'mailchimp_leid' ),
            '----' => '',
        );

        $merge_vars = $this->sub->get_extra_value( 'mailchimp_merge_vars' );
        foreach( $merge_vars as $key => $value ){
            if( ! $value ) $value = '(' . __( 'empty', 'ninja-forms-mail-chimp' ) . ')';

            if( 'groupings' == $key ){
                $group_label = __( 'Groups', 'ninja-forms-mail-chimp' );
                $grouping = reset( $value );
                $data[ $group_label ] = implode( ', ', $grouping[ 'groups' ] );
            } else {
                $data[ $key ] = $value;
            }
        }

        NF_MailChimp()->template( 'admin-metaboxes-submission.html.php', $data );
    }
}