<?php
_deprecated_file( __FILE__, '4.4.30', 'Deprecated class in favor of using `tribe_asset` registration' );

class Tribe__Events__Pro__Asset__Factory extends Tribe__Events__Asset__Factory {
		/**
		 * @return Tribe__Events__Asset__Factory
		 */
		public static function instance() {
			return new self;
		}

		/**
		 * @return string
		 */
		protected function get_asset_class_name_prefix() {
			return 'Tribe__Events__Pro__Asset__';
		}
}
