<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 * 
 */
class PH_Octavius_Admin {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	/**
	 * settings page name
	 */
	private $settings_page;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->settings_page = $this->plugin_name."_settings";
		$this->version = $version;

	}

	/**
	 * Register the octavius menu page
	 * 
	 */
	public function menu_page() {
		add_submenu_page( 'options-general.php', 'Octavius Settings', 'Octavius Settings', 'manage_options', $this->settings_page, array($this, "render_menu"));
	}

	/**
	 *  renders settings page for octavius
	 */
	public function render_menu(){
		global $wp;

		$store = new PH_Octavius_Store();



		$id_client = "ph_octavius_client";
		$id_pw = "ph_octavius_pw";
		$id_domain = "ph_octavius_domain";

		if(isset($_POST[$id_client]) && $_POST[$id_client] != ""
			&& isset($_POST[$id_pw]) && $_POST[$id_pw] != ""
			&& isset($_POST[$id_domain]) && $_POST[$id_domain] != ""){
			$store->update_options($_POST[$id_client], $_POST[$id_pw], $_POST[$id_domain]);
		}

		$options = $store->get_options();

		$submit_button = "save_ph_octavius";
		$submit_button_text = "Speichern";

		require dirname(__FILE__)."/partials/octavius-settings-display.php";
	}

}
