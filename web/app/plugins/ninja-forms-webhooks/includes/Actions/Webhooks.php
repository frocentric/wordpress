<?php if ( ! defined( 'ABSPATH' ) || ! class_exists( 'NF_Abstracts_Action' )) exit;

/**
 * Class NF_Action_Webhooks
 */
final class NF_Webhooks_Actions_Webhooks extends NF_Abstracts_Action
{
    /**
     * @var string
     */
    protected $_name  = 'webhooks';

    /**
     * @var array
     */
    protected $_tags = array();

    /**
     * @var string
     */
    protected $_timing = 'late';

    /**
     * @var int
     */
    protected $_priority = '10';

    /**
     * @var array
     */
    protected $_debug = array();

    /**
     * Constructor
     */
    public function __construct()
{
    parent::__construct();

    $this->_nicename = __( 'Webhooks', 'ninja-forms-webhooks' );

    add_action( 'admin_init', array( $this, 'init_settings' ) );

    add_action( 'ninja_forms_builder_templates', array( $this, 'builder_templates' ) );

}

    /*
    * PUBLIC METHODS
    */

    public function save( $action_settings )
    {
    
    }

    public function init_settings()
    {
        $settings = NF_Webhooks::config( 'ActionWebhooksSettings' );
        $this->_settings = array_merge( $this->_settings, $settings );

    }

    public function builder_templates()
    {

        NF_Webhooks::template( 'args-repeater-row.html.php' );
    }


    public function process( $action_settings, $form_id, $data )
    {
        $json_encode     = $action_settings[ 'wh-encode-json' ];
        $json_use_arg    = $action_settings[ 'wh-json-use-arg' ];
        $json_arg        = $action_settings[ 'wh-json-arg' ];
        $remote_url      = $action_settings[ 'wh-remote-url' ];
        $remote_method   = $action_settings[ 'wh-remote-method' ];
        $debug           = $action_settings[ 'wh-debug-mode' ];
        $wh_args         = $action_settings[ 'wh-args' ];

        if( ! isset( $remote_url ) ) return $data;

        $args = array();
        foreach ( $wh_args as $arg_data ) {
            $args[ $arg_data[ 'key' ] ] = $arg_data[ 'value' ];
        }

        if ( 1 == $json_encode ) {
            if ( 1 == $json_use_arg || 'get' == $remote_method ) {
                $args = array( $json_arg => json_encode( $args ) );
            } else {
                $args = json_encode( $args );
            }
        }

        if ( 'get' == $remote_method ) {
            $args = apply_filters( 'nf_remote_get_args', $args, $form_id );
            if ( is_array( $args ) ) {
                $args = http_build_query( $args );
            }

            if ( strpos( $remote_url, '?' ) === false ) {
                $remote_url .= '?';
            } else {
                if ( substr( $remote_url, -1 ) !== '?' ) {
                    $remote_url .= '&';
                }
            }

            $remote_url = apply_filters( 'nf_remote_get_url', $remote_url . $args, $form_id );
            $response = wp_remote_get( $remote_url, apply_filters( 'nf_remote_get_args', array(), $form_id ) );
            do_action( 'nf_remote_get', $response, $form_id );
            $data[ 'actions' ][ 'webhooks' ][ 'remote_url' ] = $remote_url;

            if ( 1 == $debug ) {
                $data[ 'debug' ][ 'form' ][ 'webhooks_response' ] .= "<pre><dl>";
                $data[ 'debug' ][ 'form' ][ 'webhooks_response' ] .= "<dt><strong>Remote URL: </strong></dt>" . "<dd>" . $remote_url . "</dd>";
            }
        } else {
            $remote_url = apply_filters( 'nf_remote_post_url', $remote_url, $form_id );
            $response = wp_remote_post( $remote_url, apply_filters( 'nf_remote_post_args', array( 'body' => $args ) ) );
            do_action( 'nf_remote_post', $response, $form_id );

            $data[ 'actions' ][ 'webhooks' ][ 'args' ] = $args;
            $data[ 'actions' ][ 'webhooks' ][ 'response' ] = $response;
            if ( 1 == $debug ) {
                $data[ 'debug' ][ 'form' ][ 'webhooks_response' ] .= "<dl>";
                $data[ 'debug' ][ 'form' ][ 'webhooks_response' ] .= "<dt><strong>Remote URL: </strong></dt>" . "<dd>" . $remote_url . "</dd>";
                $data[ 'debug' ][ 'form' ][ 'webhooks_response' ] .= "<dt><strong>Args: </strong></dt>";

                if( is_array( $args ) ) {
                    foreach ($args as $key => $value) {
                        $data[ 'debug' ][ 'form' ][ 'webhooks_response' ] .= "<dd>" . $key . ' => ' . $value . "</dd>";
                    }
                } else {
                    $data[ 'debug' ][ 'form' ][ 'webhooks_response' ] .= "<dd>" . $args . "</dd>";
                }
            }
        }
        if ( 1 == $debug ) {
            if( is_wp_error( $response ) ){
                $data[ 'debug' ][ 'form' ][ 'webhooks_response' ] .= $response->get_error_message();
            } else {
                $data[ 'debug' ][ 'form' ][ 'webhooks_response' ] .= "<dt><strong>Response: </strong></dt>";
                foreach ($response['headers'] as $key => $value) {
                    $data[ 'debug' ][ 'form' ][ 'webhooks_response' ] .= "<dd>" . $key . ' => ' . $value . "</dd>";
                }
                $data[ 'debug' ][ 'form' ][ 'webhooks_response' ] .= "<strong>Body: </strong>" . "<dd>" . $response['body'] . "</dd>";
                $data[ 'debug' ][ 'form' ][ 'webhooks_response' ] .= "<dt><strong>Response Code: </strong></dt>";
                foreach ($response['response'] as $key => $value) {
                    $data[ 'debug' ][ 'form' ][ 'webhooks_response' ] .= "<dd>" . $key . ' => ' . $value . "</dd>";
                }
                $data[ 'debug' ][ 'form' ][ 'webhooks_response' ] .= "</dl>";
            }
        }

        return $data;
    }
}
