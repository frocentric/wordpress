<?php

class NF_ConditionalLogic_Comparators_NullComparator implements NF_ConditionalLogic_Comparator
{
    public function compare( $comparison, $value )
    {
        return true;
    }
}