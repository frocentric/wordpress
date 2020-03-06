<?php

class NF_ConditionalLogic_Comparators_Less implements NF_ConditionalLogic_Comparator
{
    public function compare( $comparison, $value )
    {
        return ( $comparison < $value );
    }
}