<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://presstigers.com
 * @since      1.0.0
 *
 * @package    Woo_Gf_Variations_Exporter
 * @subpackage Woo_Gf_Variations_Exporter/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Woo_Gf_Variations_Exporter
 * @subpackage Woo_Gf_Variations_Exporter/includes
 * @author     Waqas Hass <waqas.hassan@nxb.com.pk>
 */
class Woo_Gf_Variations_Exporter_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		if ( ! current_user_can( 'activate_plugins' ) )
				return;
			self::remove_cap();

	}
	
	private static function remove_cap() {
		$capability_name = apply_filters( 'nb_gravity_woo_remove_capablility', "woo_gravity_exporter" );
		$roles = get_editable_roles();
		foreach ($GLOBALS['wp_roles']->role_objects as $key => $role) {
			if (isset($roles[$key]) && $role->has_cap($capability_name)) {
				$role->remove_cap($capability_name);
			}
		}
	}

}
