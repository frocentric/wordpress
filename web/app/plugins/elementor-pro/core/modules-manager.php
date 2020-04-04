<?php
namespace ElementorPro\Core;

use ElementorPro\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

final class Modules_Manager {
	/**
	 * @var Module_Base[]
	 */
	private $modules = [];

	public function __construct() {
		$modules = [
			'query-control',
			'custom-attributes',
			'custom-css',
			// role-manager Must be before Global Widget
			'role-manager',
			'global-widget',
			'assets-manager',
			'popup',
			'motion-fx',
			'usage',

			// Modules with Widgets.
			'theme-builder',
			'posts',
			'gallery',
			'forms',
			'slides',
			'nav-menu',
			'animated-headline',
			'pricing',
			'flip-box',
			'call-to-action',
			'carousel',
			'table-of-contents',
			'countdown',
			'share-buttons',
			'theme-elements',
			'blockquote',
			'woocommerce',
			'social',
			'library',
			'dynamic-tags',
			'sticky',
			'wp-cli',
		];

		foreach ( $modules as $module_name ) {
			$class_name = str_replace( '-', ' ', $module_name );
			$class_name = str_replace( ' ', '', ucwords( $class_name ) );
			$class_name = '\ElementorPro\Modules\\' . $class_name . '\Module';

			/** @var Module_Base $class_name */
			if ( $class_name::is_active() ) {
				$this->modules[ $module_name ] = $class_name::instance();
			}
		}
	}

	/**
	 * @param string $module_name
	 *
	 * @return Module_Base|Module_Base[]
	 */
	public function get_modules( $module_name ) {
		if ( $module_name ) {
			if ( isset( $this->modules[ $module_name ] ) ) {
				return $this->modules[ $module_name ];
			}

			return null;
		}

		return $this->modules;
	}
}
