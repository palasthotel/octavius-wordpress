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
	public function __construct( $plugin_name, $version ) 
	{

		$this->plugin_name = $plugin_name;
		$this->settings_page = $this->plugin_name."_settings";
		$this->tool_url_checker_page = $this->plugin_name."_url_checker";
		$this->version = $version;

	}

	/**
	 * Register the octavius menu page
	 * 
	 */
	public function menu_pages() 
	{
		add_submenu_page( 'options-general.php', 'Octavius', 'Octavius', 'manage_options', $this->settings_page, array($this, "render_octavius_settings"));
		add_submenu_page( 'tools.php', 'URL Checker', 'URL Checker', 'manage_options', $this->tool_url_checker_page, array($this, "render_tool_url_checker"));
	}

	/**
	 *  renders settings page for octavius
	 */
	public function render_octavius_settings()
	{
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

	/**
	 *  renders settings page for octavius
	 */
	public function render_tool_url_checker()
	{
		global $wp;

		$store = new PH_Octavius_Store();
		$options = $store->get_options();

		require dirname(__FILE__)."/partials/octavius-url-check-display.php";
	}

	/**
	 * add grid boxes
	 */
	public function load_grid_boxes()
	{
		require dirname(__FILE__)."/../grid-boxes/grid-octavius-box.inc";
	}

}
