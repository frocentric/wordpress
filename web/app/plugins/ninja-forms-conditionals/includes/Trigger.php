<?php

interface NF_ConditionalLogic_Trigger
{
    public function process( &$target, &$fieldCollection, &$data );
}