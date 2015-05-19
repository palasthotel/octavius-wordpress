<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 */
class PH_Octavius_Activator {

	/**
	 * installs the database tables with PH_Octavius_Store class
	 * 
	 */
	public static function activate() {
 		$store = new PH_Octavius_Store();
 		$store->install();
 		flush_rewrite_rules();
	}

}
