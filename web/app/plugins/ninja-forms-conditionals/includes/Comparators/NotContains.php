<?php

class NF_ConditionalLogic_Comparators_NotContains implements NF_ConditionalLogic_Comparator
{
    public function compare( $comparison, $value )
    {

        if ( is_array( $comparison ) ) {
            $comparison = implode( ',', $comparison );
        }

        if( $this->is_not_case_sensitive( $value ) ) {
            $value      = trim( $value, '"' );
            $value      = strtolower( $value );
            $comparison = strtolower( $comparison );
        }
        
        return ( false === strpos( $comparison, $value ) );
    }

    private function is_not_case_sensitive( $value )
    {
        return ( 0 !== stripos( $value, '"' ) );
    }
}