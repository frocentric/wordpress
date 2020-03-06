<?php

final class NF_Layouts_Conversion
{
	var $part_array = array();
    var $part_count = - 1;
    var $conditions = array();

    public function __construct()
    {
        add_filter( 'ninja_forms_after_upgrade_settings', array( $this, 'upgrade_field_settings' ), 11 );
    }

    public function upgrade_field_settings( $form_data )
    {

        if ( isset( $form_data[ 'settings' ][ 'formContentData' ] ) ) return $form_data;

        if ( isset ( $form_data[ 'settings' ][ 'conditions' ] ) ) {
            $this->conditions = $form_data[ 'settings' ][ 'conditions' ];
        }
        
        /*
         * Add our fields to the appropriate part.
         */
        array_walk( $form_data[ 'fields' ], array( $this, 'divider_check' ), $form_data );

        /*
         * If we have parts, set our formContentData to the parts array.
         */
        if ( -1 != $this->part_count ) {
            /*
             * If Layouts & Styles data is present, pass our parts through a layout conversion.
             */
            if ( isset ( $form_data[ 'settings' ][ 'style' ] ) ) {
                array_walk( $this->part_array, array( $this, 'layout_check' ), $form_data );
            }

            /*
             * Add else statements to our conditions.
             */
            array_walk( $this->conditions, array( $this, 'conditions_add_else' ) );

            /*
             * Remove any page_divider types we have in our array.
             */
            usort( $form_data[ 'fields' ], array( $this, 'sort_fields' ) );
            $form_data[ 'fields' ] = array_filter( $form_data[ 'fields' ], array( $this, 'remove_dividers' ) );
            $form_data[ 'settings' ][ 'formContentData' ] = $this->part_array;
            $form_data[ 'settings' ][ 'conditions' ] = $this->conditions;
        } else {
            /*
             * We don't have any parts, so pass the fields through our convert layouts function.
             */
            $cols = ( isset ( $form_data[ 'settings' ][ 'style' ] ) ) ? $form_data[ 'settings' ][ 'style' ][ 'cols' ] : 0;
            $form_data[ 'settings' ][ 'formContentData' ] = $this->convert_layouts( $cols, $form_data[ 'fields' ] );
        }

        return $form_data;
    }

    public function divider_check( $field, $index, $form_data )
    {
        if ( 'page_divider' == $field[ 'type' ] ) {
            $this->part_count++;

            $this->part_array[ $this->part_count ] = array(
                'title'             => ( isset( $field[ 'label' ] ) && ! empty( $field[ 'label' ] ) ) ? $field[ 'label' ] : 'Part ' . ( $this->part_count + 1 ),
                'order'             => $this->part_count,
                'type'              => 'part',
                'key'               => uniqid(),
                'formContentData'   => array(),
            );

            /* 
             * Check to see if this page divider is referenced in any conditions. If it is, update those references.
             */
            $this->check_conditions( $field, $this->part_array[ $this->part_count ] );
        } else if ( ! isset ( $form_data[ 'settings' ][ 'style' ] ) ) {
            $this->part_array[ $this->part_count ][ 'formContentData' ][] = $field[ 'key' ];
        } else {
            $this->part_array[ $this->part_count ][ 'formContentData' ][] = $field;
        }
    }

    public function remove_dividers( $field ) {
        return 'page_divider' != $field[ 'type' ];
    }

    public function layout_check( &$part, $index, $form_data ) {
        $part[ 'formContentData' ] = $this->convert_layouts( $form_data[ 'settings' ][ 'style' ][ 'mp' ][ $index + 1 ][ 'cols' ], $part[ 'formContentData' ] );
    }

    public function convert_layouts( $cols, $fields ) {
        /*
         * If we have no columns, return a default formContentData
         */
        if ( empty( $cols ) ) {
            usort( $fields, array( $this, 'sort_fields' ) );
            $formContentData = array();
            for( $i = 0; $i < count( $fields ); $i++ ){
                $formContentData[] = array(
                    'order' => 0,
                    'cells' => array(
                        array(
                            'order' => 0,
                            'fields' => array(
                                $fields[ $i ][ 'key' ]
                            ),
                            'width' => 100
                        )
                    )
                );
            }

            return $formContentData;
        }

        /*
         * Try to catch any bad layout errors.
         */
        $rows = array();
        $roworder = 0;
        $coltrack = 0;
        $cells = array();
        $cellorder = 0;

        for ( $i = 0; $i < count( $fields ); $i++ ) {
            /*
             * If we don't have a colspan set, it should be equal to our cols.
             */
            if ( ! isset( $fields[ $i ][ 'style' ][ 'colspan' ] ) ) {
                $fields[ $i ][ 'style' ][ 'colspan' ] = $cols;
            }

            if ( $fields[ $i ][ 'style' ][ 'colspan' ] > $cols ) {
                $fields[ $i ][ 'style' ][ 'colspan' ] = $cols;
            }

            /*
             * If our colspan + coltrack is less than or equal to cols, we add this to our cells.
             */
            if ( $fields[ $i ][ 'style' ][ 'colspan' ] + $coltrack <= $cols ) {
                
                if( ! isset( $fields[ $i ][ 'key' ] ) ){
                    $fields[ $i ][ 'key' ] = ltrim( $fields[ $i ][ 'type' ], '_' ) . '_asdf' . $fields[ $i ][ 'id' ];
                }

                if( '_text' == $fields[ $i ][ 'type' ] && isset( $fields[ $i ][ 'datepicker' ] ) && $fields[ $i ][ 'datepicker' ] ){
                    $fields[ $i ][ 'key' ] = 'date_' . $fields[ $i ][ 'id' ];
                }

                $cells[] = array(
                    'order'     => $cellorder,
                    'fields'    => array(
                        $fields[ $i ][ 'key' ]
                    ),
                    'width'     => $fields[ $i ][ 'style' ][ 'colspan' ],
                );
             
                $coltrack += $fields[ $i ][ 'style' ][ 'colspan' ];
                $cellorder++;
            } else {
                $rows[] = $this->layouts_new_row( $cols, $cells, $roworder, $cellorder );
                
                $roworder++;
                $coltrack = 0;
                $cellorder = 0;
                $cells = array();
                $i--;
            }

            if ( $i == count( $fields ) - 1 ) {
                $rows[] = $this->layouts_new_row( $cols, $cells, $roworder, $cellorder );
            }

        } // for field loop
        return $rows;
    }

    private function layouts_new_row( $cols, $cells, $roworder, $cellorder )
    {
        /*
         * We're on a new row. We now need to add the previous row, represented by the $cells variable, to our rows array.
         *
         * 1) Add any blank cells necessary.
         * 1) Add the cells to a new row.
         * 2) Move our $i pointer back one field.
         * 
         * We need to add an extra blank cell to make up the difference.
         */
        $diff = 0;
        foreach( $cells as $cell ) {
            $diff += $cell[ 'width' ];
        }
        $diff = $cols - $diff;

        if ( 0 != $diff ) {
            $cells[] = array(
                'order'     => $cellorder,
                'fields'    => array(),
                'width'     => $diff,
            );
        }

        foreach( $cells as $index => $cell ) {
            $cells[ $index ][ 'width' ] = $this->convert_width( $cells[ $index ][ 'width' ], $cols, $cells );
        }

        return array(
            'order' => $roworder,
            'cells' => $cells
        );
    }

    private function convert_width( $width, $cols, $cells )
    {

        /*
         * width will be set to the colspan of our initial cell.
         */
        switch ( $cols ) {
            case 1:
                if ( 1 == $width ) {
                    $width = 100;
                }
                break;
            case 2:
                /*
                 * If we have a colspan of 2, either it's 100%, which is handled above, or 50%.
                 */
                if ( 2 == $width ) {
                    $width = 100;
                } else {
                    $width = 50; 
                }
                break;
            
            case 3:
                /*
                 * If we have a cols value of 3, either all cells are 33% or one is 75% and the other is 25%.
                 */
                if ( 1 == $width && 2 == count( $cells ) ) {
                    $width = 25;
                } else if ( 2 == $width ) {
                    $width = 75;
                } else if ( 3 == $width ){
                    $width = 100;
                } else {
                    $width = 33;
                }

                break;
            
            case 4:
                /*
                 * If we have a cols value of 4, we can get our percentages with simple math.
                 */
                $width = $width / 4 * 100;
                break;
        }

        return $width;
    }

    private function check_conditions( $field, $part )
    {
        array_walk( $this->conditions, array( $this, 'check_conditions_walk' ), array( 'field' => $field, 'part' => $part ) );
    }

    private function check_conditions_walk( &$condition, $index, $data )
    {

        $field = $data[ 'field' ];
        $part = $data[ 'part' ];

        /*
         * If we have any "then" statements that reference our divider, replace them with part info.
         * Also replace the trigger.
         */
        array_walk( $condition[ 'then' ], array( $this, 'conditions_replace_then' ), $data );
    }

    private function conditions_replace_then( &$then, $index, $data )
    {
        $field = $data[ 'field' ];
        $part = $data[ 'part' ];

        if ( $then[ 'key' ] == $field[ 'key' ] ) {
            $then[ 'key' ] = $part[ 'key' ];
            $then[ 'type' ] = 'part';
            if ( 'ninja_forms_show_mp_page' == $then[ 'trigger' ] ) {
                $then[ 'trigger' ] = 'show_part';
            } else if ( 'ninja_forms_hide_mp_page' == $then[ 'trigger' ] ) {
                $then[ 'trigger' ] = 'hide_part';
            }
        }
    }

    private function conditions_add_else( &$condition, $index )
    {
        foreach( $condition[ 'then' ] as $tindex => $then ) {
            if ( 'part' != $then[ 'type' ] ) continue;
            if ( 'show_part' == $then[ 'trigger' ] ) {
                $opposite = 'hide_part';
            } else {
                $opposite = 'show_part';
            }
            if ( empty ( $condition[ 'else' ][0] ) ) {
                unset( $condition[ 'else' ][0] );
                $condition[ 'else' ] = array_values( $condition[ 'else' ] );
            }
            $condition[ 'else' ][] = array(
                'key' => $then[ 'key' ],
                'trigger' => $opposite,
                'value' => '',
                'type'  => 'part'
            );
        }
    }

    private function sort_fields($a, $b) {
        return $a['order'] - $b['order'];
    }
} // End of Class

new NF_Layouts_Conversion();