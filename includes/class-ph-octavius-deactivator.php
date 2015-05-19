<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */
class PH_Octavius_Deactivator {

	/**
	 * clean the mess up that octavius leaves behind but wont delete any data
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}

}
