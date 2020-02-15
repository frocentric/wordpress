<?php
/**
* @package     WPeMatico Professional
* @subpackage  XML Importer
* @since       2.1
*/
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
/**
* Campaign Edit Class 
* @since 1.7.5
*/
if (!class_exists('WPeMaticoPro_XML_Importer')) :
class WPeMaticoPro_XML_Importer {


	public static function hooks() {
		add_action('wpematico_xml_input_nodes_print', array(__CLASS__, 'input_nodes_print'));
		add_filter('wpematico_xml_simplepie_item_before_add', array(__CLASS__, 'add_item_simplepie'), 10, 4);
		add_filter('wpematico_pos_item_filters', array(__CLASS__, 'add_tags'), 10, 4);
        add_filter('wpematico_pos_item_filters', array(__CLASS__, 'add_format'), 10, 4);
        add_filter('wpematico_get_author',  array(__CLASS__, 'author'), 10, 4 );
	}
    public static function author($current_item, $campaign, $feed, $item ) {
        if ($campaign['campaign_type'] == 'xml') {
            if (class_exists('WPeMaticoPro_Campaign_Fetch')) {
                if ( method_exists('WPeMaticoPro_Campaign_Fetch', 'get_author_from_feed') ) {
                    $current_item = WPeMaticoPro_Campaign_Fetch::get_author_from_feed($current_item, $campaign, $feed, $item );
                }
            }
        }
        return $current_item;
    }
	public static function add_tags($current_item, $campaign, $feed, $item) {
		if ($campaign['campaign_type'] == 'xml') {
			$current_tags = $item->get_post_meta('post_tags');
			if ( ! empty( $current_tags ) ) {
				$current_item['campaign_tags'] = array_merge($current_item['campaign_tags'], $current_tags);
			}
		}
		return $current_item;
	}
    public static function add_format($current_item, $campaign, $feed, $item) {
        if ($campaign['campaign_type'] == 'xml') {
            $current_format = $item->get_post_meta('post_format');
            if ( ! empty( $current_format ) ) {
                if ( current_theme_supports( 'post-formats' ) ) {
                    $post_formats = get_theme_support( 'post-formats' );
                    if ( is_array( $post_formats[0] ) )  {
                        if ( in_array($current_format, $post_formats[0]) ) {
                            $current_item['campaign_post_format'] = $current_format;
                        }
                    }
                }
            }
        }
        return $current_item;
    }
	public static function add_item_simplepie($new_simplepie_item, $key_node_title, $xml, $campaign) {
		$campaign_xml_node  = $campaign['campaign_xml_node'];
        $campaign_xml_node_parent  = $campaign['campaign_xml_node_parent'];
        $xml_categories_separated_commas = empty($campaign['xml_categories_separated_commas']) ? false : true;
        $xml_tags_separated_commas = empty($campaign['xml_tags_separated_commas']) ? false : true;

		$xpath_categories         		= ( !empty( $campaign_xml_node['post_categories'] ) ? $campaign_xml_node['post_categories'] : '' );
		$xpath_tags         			= ( !empty( $campaign_xml_node['post_tags'] ) ? $campaign_xml_node['post_tags'] : '' );
		$xpath_author                   = ( !empty( $campaign_xml_node['post_author'] ) ? $campaign_xml_node['post_author'] : '' );
        $xpath_format                   = ( !empty( $campaign_xml_node['post_format'] ) ? $campaign_xml_node['post_format'] : '' );

		$xpath_parent_categories        = ( !empty( $campaign_xml_node_parent['post_categories'] ) ? $campaign_xml_node_parent['post_categories'] : '' );
		$xpath_parent_tags        		= ( !empty( $campaign_xml_node_parent['post_tags'] ) ? $campaign_xml_node_parent['post_tags'] : '' );
        $xpath_parent_author            = ( !empty( $campaign_xml_node_parent['post_author'] ) ? $campaign_xml_node_parent['post_author'] : '' );
        $xpath_parent_format            = ( !empty( $campaign_xml_node_parent['post_format'] ) ? $campaign_xml_node_parent['post_format'] : '' );

		$nodes_categories            	=  ( ! empty($xpath_parent_categories) ? $xml->xpath( $xpath_parent_categories ) : ( ! empty( $xpath_categories ) ? $xml->xpath( $xpath_categories ) : array() )  );  
		$nodes_tags            			=  ( ! empty($xpath_parent_tags) ? $xml->xpath( $xpath_parent_tags ) : ( ! empty( $xpath_tags ) ? $xml->xpath( $xpath_tags ) : array() )  );  
		$nodes_author                   =  ( ! empty($xpath_parent_author) ? $xml->xpath( $xpath_parent_author ) : ( ! empty( $xpath_author ) ? $xml->xpath( $xpath_author ) : array() )  );  
		$nodes_format                   =  ( ! empty($xpath_parent_format) ? $xml->xpath( $xpath_parent_format ) : ( ! empty( $xpath_format ) ? $xml->xpath( $xpath_format ) : array() )  );  
        

        $new_categories = array();
        if ( ! empty($xpath_parent_categories) ) {
            $child_xpath_categories      = str_replace($xpath_parent_categories.'/', '', $xpath_categories);
            $child_nodes_categories      = $nodes_categories[$key_node_title]->xpath($child_xpath_categories);
            $new_categories              = $child_nodes_categories;
            if ( empty($new_categories) ) {
                $new_categories          = array();
            }
        } else {
            $new_categories              = ( ! empty( $nodes_categories[$key_node_title] ) ? $nodes_categories[$key_node_title] : '' );
        }
        if ( ! is_array( $new_categories ) ) {
            $new_categories = array($new_categories);
        }

      
        foreach ($new_categories as $keycat => $category_name) {
            
        	if ( ! empty( $category_name ) ) {
                
                $new_categories_array = array();


                if ( empty( $xml_categories_separated_commas ) ) {
                    $new_categories_array[] = (string)$category_name;
                } else {
                    $delimiter_categories = apply_filters('wpematico_xml_categories_delimiter', ',');
                    $new_categories_array = explode($delimiter_categories, (string)$category_name);
                }

                foreach ($new_categories_array as $keycnn => $cat_name_new) {
                    $new_item_category = new WPeMatico_SimplePie_Category($cat_name_new);
                    $new_simplepie_item->add_category($new_item_category);
                }
        		

        	}
        	
        }


        $new_tags = array();
        if ( ! empty($xpath_parent_tags) ) {
            $child_xpath_tags      	= str_replace($xpath_parent_tags.'/', '', $xpath_tags);
            $child_nodes_tags      	= $nodes_tags[$key_node_title]->xpath($child_xpath_tags);
            $new_tags              	= $child_nodes_tags;
            if ( empty($new_tags) ) {
                $new_tags          = array();
            }
        } else {
            $new_tags              = ( ! empty( $nodes_tags[$key_node_title] ) ? $nodes_tags[$key_node_title] : '' );
        }
        if ( ! is_array( $new_tags ) ) {
            $new_tags = array($new_tags);
        }

       	$current_tags = array();
        foreach ($new_tags as $keytag => $tag_name) {
        	if ( ! empty( $tag_name ) ) {
        		
                if ( empty( $xml_tags_separated_commas ) ) {
                    $current_tags[]  = (string)$tag_name;
                } else {
                    $delimiter_tags = apply_filters('wpematico_xml_tags_delimiter', ',');
                    $new_tags_array = explode($delimiter_tags, (string)$tag_name);
                    $current_tags = array_merge($current_tags, $new_tags_array);
                }

        	}
        }
        $new_simplepie_item->set_post_meta('post_tags', $current_tags);


        $new_author = '';
        if ( ! empty($xpath_parent_author) ) {
            $child_xpath_author     = str_replace($xpath_parent_author.'/', '', $xpath_author);
            $child_nodes_author     = ( ! empty($nodes_author[$key_node_title]) ? $nodes_author[$key_node_title]->xpath($child_xpath_author) : array() );
            $new_author             = (string)array_shift($child_nodes_author);
            if ( ! empty($new_author) ) {
                $new_author         = new WPeMatico_SimplePie_Item_Author($new_author);
            } else {
                $new_author         = '';
            }
        } else {
            $new_author             = ( ! empty( $nodes_author[$key_node_title] ) ? new WPeMatico_SimplePie_Item_Author($nodes_author[$key_node_title])  : '' );
        }
        
        $new_simplepie_item->set_author($new_author);

        $new_format = '';
        if ( ! empty($xpath_parent_format) ) {
            $child_xpath_format      = str_replace($xpath_parent_format.'/', '', $xpath_format);
            $child_nodes_format      = $nodes_format[$key_node_title]->xpath($child_xpath_format);
            $new_format             = (string)array_shift($child_nodes_format);
            if ( empty($new_format) ) {
                $new_format         = '';
            }
        } else {
            $new_format             = (string)( ! empty( $nodes_format[$key_node_title] ) ? $nodes_format[$key_node_title] : '' );
        }

        $new_simplepie_item->set_post_meta('post_format', $new_format);

		return $new_simplepie_item;
	}
	public static function input_nodes_print($campaign_data) {
        global $helptip;
		$campaign_xml_node = $campaign_data['campaign_xml_node'];
        $campaign_xml_node_parent = $campaign_data['campaign_xml_node_parent'];
        $xml_categories_separated_commas = empty($campaign_data['xml_categories_separated_commas']) ? false : true;
        $xml_tags_separated_commas = empty($campaign_data['xml_tags_separated_commas']) ? false : true;
        ?>
        <tr>
            <td><?php _e('Post author', 'wpematico' ); ?></td>
            <td><?php WPeMatico_XML_Importer::get_select_node_html('post_author', ( !empty( $campaign_xml_node['post_author'] ) ? $campaign_xml_node['post_author'] : '' )  ); ?></td>
            <td><?php WPeMatico_XML_Importer::get_select_node_html('post_author', ( !empty( $campaign_xml_node_parent['post_author'] ) ? $campaign_xml_node_parent['post_author'] : '' ),  'campaign_xml_node_parent', false  ); ?></td>
        </tr>
        <tr>
            <td><?php _e('Post categories', 'wpematico' ); ?>
                <span class="dashicons dashicons-warning help_tip" title-heltip="<?php _e('The post categories of XML campaign type work with Auto categories features.', 'wpematico'); ?>"  title="<?php _e('The post categories of XML campaign type work with Auto categories features.', 'wpematico'); ?>"></span>
            </td>
            <td>
                <?php WPeMatico_XML_Importer::get_select_node_html('post_categories', ( !empty( $campaign_xml_node['post_categories'] ) ? $campaign_xml_node['post_categories'] : '' )  ); ?>
                    <input class="checkbox" type="checkbox"<?php checked($xml_categories_separated_commas,true);?> name="xml_categories_separated_commas" value="1" id="xml_categories_separated_commas"/> 
                    <span class="dashicons dashicons-warning help_tip" title-heltip="<?php echo $helptip['xml_categories_separated_commas']; ?>"  title="<?php echo $helptip['xml_categories_separated_commas']; ?>"></span>
                </td>
            <td><?php WPeMatico_XML_Importer::get_select_node_html('post_categories', ( !empty( $campaign_xml_node_parent['post_categories'] ) ? $campaign_xml_node_parent['post_categories'] : '' ),  'campaign_xml_node_parent', false  ); ?></td>
        </tr>

        <tr>
            <td><?php _e('Post tags', 'wpematico' ); ?></td>
            <td>
                <?php WPeMatico_XML_Importer::get_select_node_html('post_tags', ( !empty( $campaign_xml_node['post_tags'] ) ? $campaign_xml_node['post_tags'] : '' )  ); ?>
                    <input class="checkbox" type="checkbox"<?php checked($xml_tags_separated_commas,true);?> name="xml_tags_separated_commas" value="1" id="xml_tags_separated_commas"/> 
                    <span class="dashicons dashicons-warning help_tip" title-heltip="<?php echo $helptip['xml_categories_separated_commas']; ?>"  title="<?php echo $helptip['xml_categories_separated_commas']; ?>"></span>
                </td>
            <td><?php WPeMatico_XML_Importer::get_select_node_html('post_tags', ( !empty( $campaign_xml_node_parent['post_tags'] ) ? $campaign_xml_node_parent['post_tags'] : '' ),  'campaign_xml_node_parent', false  ); ?></td>
        </tr>

        <tr>
            <td><?php _e('Post format', 'wpematico' ); ?></td>
            <td><?php WPeMatico_XML_Importer::get_select_node_html('post_format', ( !empty( $campaign_xml_node['post_format'] ) ? $campaign_xml_node['post_format'] : '' )  ); ?></td>
            <td><?php WPeMatico_XML_Importer::get_select_node_html('post_format', ( !empty( $campaign_xml_node_parent['post_format'] ) ? $campaign_xml_node_parent['post_format'] : '' ),  'campaign_xml_node_parent', false  ); ?></td>
        </tr>

        <?php
	}
}
endif;
WPeMaticoPro_XML_Importer::hooks();
?>