<?php

class NF_ConditionalLogic_Comparators_GreaterEqual implements NF_ConditionalLogic_Comparator
{
    public function compare( $comparison, $value )
    {
        return ( $comparison >= $value );
    }
}
