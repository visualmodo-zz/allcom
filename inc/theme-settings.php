<?php
/**
 * Check and setup theme's default settings
 *
 * @package AllCom
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'allcom_setup_theme_default_settings' ) ) {
	/**
	 * Store default theme settings in database.
	 */
	function allcom_setup_theme_default_settings() {
		$defaults = allcom_get_theme_default_settings();
		$settings = get_theme_mods();
		foreach ( $defaults as $setting_id => $default_value ) {
			// Check if setting is set, if not set it to its default value.
			if ( ! isset( $settings[ $setting_id ] ) ) {
				set_theme_mod( $setting_id, $default_value );
			}
		}
	}
}

if ( ! function_exists( 'allcom_get_theme_default_settings' ) ) {
	/**
	 * Retrieve default theme settings.
	 *
	 * @return array
	 */
	function allcom_get_theme_default_settings() {
		$defaults = array(
			'allcom_posts_index_style' => 'default',   // Latest blog posts style.
			'allcom_sidebar_position'  => 'right',     // Sidebar position.
			'allcom_container_type'    => 'container', // Container width.
		);

		/**
		 * Filters the default theme settings.
		 *
		 * @param array $defaults Array of default theme settings.
		 */
		return apply_filters( 'allcom_theme_default_settings', $defaults );
	}
}
