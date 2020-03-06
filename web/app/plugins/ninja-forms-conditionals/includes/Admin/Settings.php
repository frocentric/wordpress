<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_ConditionalLogic_Admin_Settings
 */
final class NF_ConditionalLogic_Admin_Settings
{
    public function __construct()
    {
        add_filter( 'ninja_forms_from_settings_types',     array( $this, 'form_settings_types' ), 10, 1 );
        add_filter( 'ninja_forms_localize_forms_settings', array( $this, 'form_settings' ),      10, 1 );
    }

    public function form_settings_types( $types )
    {
        return array_merge( $types, NF_ConditionalLogic::config( 'AdvancedSettingsTypes' ) );
    }

    public function form_settings( $settings )
    {
        return array_merge( $settings, NF_ConditionalLogic::config( 'AdvancedSettings' ) );
    }

} // End Class NF_ConditionalLogic_Admin_Settings
