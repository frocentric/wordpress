<?php

final class NF_ConditionalLogic_Triggers_HideField implements NF_ConditionalLogic_Trigger
{
    public function process( &$field, &$fieldCollection, &$data )
    {
        $value = $field->get_setting( 'value' );
        $field->update_setting( 'value', false );
        $field->update_setting( 'submitted_value', $value );
        $field->update_setting( 'visible', false );

        // Hidden fields should NOT be validated for required.
        if( 1 == $field->get_setting( 'required' ) ) {

            // Set bypass flag.
            $field->update_setting( 'conditionally_required', false );
        }
    }
}