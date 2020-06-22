<?php
/**
 * The interface any available recurrence engine must implement.
 *
 * Whatever intricacy or complexity a recurrence backend engine has it should stick to this interface and hide its
 * complexity and functionality behind it.
 *
 * @since 4.7
 */

/**
 * Interface Tribe__Events__Pro__Recurrence__Engines__Engine_Interface
 *
 * @since 4.7
 */
interface Tribe__Events__Pro__Recurrence__Engines__Engine_Interface {
	/**
	 * Returns the engine slug.
	 *
	 * @since 4.7
	 *
	 * @return string The engine slug.
	 */
	public function get_slug();

	/**
	 * Returns the localized engine name for use in settings or any user-facing UI or report.
	 *
	 * @since 4.7
	 *
	 * @return string A non HTML-escaped, localized, name for the engine.
	 */
	public function get_name();

	/**
	 * Returns a preview of what updates/creations/deletion/changes the engine would do
	 * provided the data.
	 *
	 * @since 4.7
	 *
	 * @param mixed $data The data the engine should provide a preview for.
	 *
	 * @return Tribe__Events__Pro__Recurrence__Engines__Work_Report A preview of what the engine would do provided the data.
	 */
	public function preview( $data );

	/**
	 * Returns a report of what updates/creations/deletion/changes the engine did
	 * provided the data.
	 *
	 * @since 4.7
	 *
	 * @param mixed $data The data the engine will act on.
	 *
	 * @return Tribe__Events__Pro__Recurrence__Engines__Work_Report A report of what the engine did provided the data.
	 */
	public function update( $data );

	/**
	 * Hooks the engine to the filters it needs to work.
	 *
	 * This method is the opposite of the `unhook` one.
	 *
	 * @since 4.7
	 *
	 * @return bool Whether the hooking correctly happened or not.
	 */
	public function
	hook();

	/**
	 * Completely removes the engine from any filter suppressing its functionalities.
	 *
	 * @since 4.7
	 *
	 * @return bool Whether the unhooking correctly happened or not.
	 */
	public function unhook();
}