<?php

/**
 * Fired when the plugin is uninstalled.
 *
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
/**
 * Delete all database on uninstall with PH_Octavius_Store class
 */
$store = new PH_Octavius_Store();
$store->uninstall();