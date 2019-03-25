<?php

/**
 *
 * @link              https://presstigers.com
 * @since             1.0.0
 * @package           Woo_Gf_Variations_Exporter
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Gravity Forms Variations Exporter
 * Plugin URI:        https://presstigers.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Waqas Hass
 * Author URI:        https://presstigers.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-gf-variations-exporter
 * Domain Path:       /languages
 */


if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WOO_GF_VARIATIONS_EXPORTER_VERSION', '1.0.0' );


function activate_woo_gf_variations_exporter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-gf-variations-exporter-activator.php';
	Woo_Gf_Variations_Exporter_Activator::activate();
}

function deactivate_woo_gf_variations_exporter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-gf-variations-exporter-deactivator.php';
	Woo_Gf_Variations_Exporter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_gf_variations_exporter' );
register_deactivation_hook( __FILE__, 'deactivate_woo_gf_variations_exporter' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-gf-variations-exporter.php';

/**
 *
 * @since    1.0.0
 */
function run_woo_gf_variations_exporter() {

	$plugin = new Woo_Gf_Variations_Exporter();
	$plugin->run();

}
run_woo_gf_variations_exporter();
