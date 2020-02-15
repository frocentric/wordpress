<?php
/**
 * Image Background Process
 */

if ( class_exists( 'WP_Background_Process' ) ) {

	/**
	 * Image Background Process
	 *
	 * @since 1.0.11
	 */
	class GeneratePress_Site_Background_Process extends WP_Background_Process {

		protected $action = 'image_process';

		protected function task( $process ) {

			if ( method_exists( $process, 'import' ) ) {
				$process->import();
			}

			return false;
		}

		protected function complete() {

			parent::complete();

		}

	}

}
