<?php

final class NF_ConditionalLogic_Triggers_ShowPart implements NF_ConditionalLogic_Trigger
{
    public function process( &$target, &$fieldCollection, &$data )
    {
        $field_trigger = NF_ConditionalLogic()->trigger( 'show_field' );
        $field_keys = NF_ConditionalLogic_Integrations_MultiPart::extract_field_keys( $target );

        foreach( $field_keys as $field_key )
        {
            $field = $fieldCollection->get_field( $field_key );
            $field_trigger->process( $field, $fieldCollection, $data );
        }
    }
}