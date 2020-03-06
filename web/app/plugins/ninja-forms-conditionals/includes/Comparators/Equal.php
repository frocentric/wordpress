<?php

class NF_ConditionalLogic_Comparators_Equal implements NF_ConditionalLogic_Comparator
{
    public function compare( $comparison, $value )
    {
        return ( trim( $comparison ) == trim( $value ) );
    }
}