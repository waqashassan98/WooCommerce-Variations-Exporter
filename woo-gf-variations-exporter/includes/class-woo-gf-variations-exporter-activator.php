<?php

/**
 * Fired during plugin activation
 *
 * @link       https://presstigers.com
 * @since      1.0.0
 *
 * @package    Woo_Gf_Variations_Exporter
 * @subpackage Woo_Gf_Variations_Exporter/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woo_Gf_Variations_Exporter
 * @subpackage Woo_Gf_Variations_Exporter/includes
 * @author     Waqas Hass <waqas.hassan@nxb.com.pk>
 */
class Woo_Gf_Variations_Exporter_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if ( ! current_user_can( 'activate_plugins' ) )
				return;
		if( !class_exists( 'WooCommerce' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( __( 'Please install and Activate WooCommerce.', 'woocommerce-addon-slug' ), 'Plugin dependency check', array( 'back_link' => true ) );
		}
			self::add_cap();
	}
	private static function add_cap(){
		$capability_name = apply_filters( 'nb_gravity_woo_add_capablility', "woo_gravity_exporter" );
		$roles = get_editable_roles();
		foreach ($GLOBALS['wp_roles']->role_objects as $key => $role) {
			if (isset($roles[$key]) && !$role->has_cap($capability_name)) {
				$role->add_cap($capability_name);
			}
		}
	}

}
