<?php

class NF_ConditionalLogic_Comparators_Greater implements NF_ConditionalLogic_Comparator
{
    public function compare( $comparison, $value )
    {
        return ( $comparison > $value );
    }
}