<?php

/**
 * Octavius Plugin for PALASTHOTEL Octavius service
 *
 * @package           PH_Octavius
 *
 * @wordpress-plugin
 * Plugin Name:       PALASTHOTEL Octavius
 * Plugin URI:        https://github.com/palasthotel/octavius-wordpress
 * Description:       This plugin retrieves data from PALASTHOTEL Octavius service
 * Version:           1.0.0
 * Author:            PALASTHOTEL Gesellschaft für digitale Pracht mbH
 * License:           GPL-3.0
 * License URI:       https://github.com/palasthotel/octavius-wordpress/blob/master/LICENSE
 * Text Domain:       ph-octavius
 * Domain Path:       /languages
 * 
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-ph-octavius-activator.php';

/** This action is documented in includes/class-ph-octavius-activator.php */
register_activation_hook( __FILE__, array( 'PH_Octavius_Activator', 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-ph-octavius-deactivator.php';

/** This action is documented in includes/class-ph-octavius-deactivator.php */
register_deactivation_hook( __FILE__, array( 'PH_Octavius_Deactivator', 'deactivate' ) );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-ph-octavius.php';

/**
 * Begins execution of the plugin.
 *
 */
function run_ph_octavius() {
	$plugin = new PH_Octavius();
	$plugin->run();

}
run_ph_octavius();
