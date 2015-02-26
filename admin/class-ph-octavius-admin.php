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
			$store->update_options( $_POST[ $id_client ], $_POST[ $id_pw ], $_POST[ $id_domain ] );
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
		
		if(
			isset($_GET["show_results"]) && ($_GET["show_results"] == "found" || $_GET["show_results"] == "lost") &&
			isset($_GET["meta_key"]) &&  $_GET["meta_key"] != ""
			){
			/**
			 * style for url checker
			 */
			wp_enqueue_style( 
				$this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/url-results.css', 
				array(), 
				$this->version, 
				'all' 
			);
			/**
			 * Scripts for url checker
			 */
			wp_enqueue_script(
				$this->plugin_name,
				plugin_dir_url( __FILE__ )  . '/js/url-results.js',
				array( 'jquery' )
			);
			$type = sanitize_text_field($_GET["show_results"]);
			$meta_key = sanitize_text_field($_GET["meta_key"]);
			$elements = array();
			$overall = 0;
			$limit = 100;
			$paged = (isset($_GET["paged"]))? $_GET["paged"]: 1;

			$store = new PH_Octavius_Store();

			if($type == "found"){
				$overall = $store->get_ga_found_count($meta_key);
				$elements = $store->get_ga_found_elements($meta_key, $limit, $paged);
				
			} else {
				$overall = $store->get_ga_lost_count($meta_key);
				$elements = $store->get_ga_lost_elements($meta_key, $limit, $paged);
			}
			$pages = ceil($overall/$limit);

			$base_url = "/wp-admin/tools.php?page=ph-octavius_url_checker";

			$paged_url = $base_url."&meta_key=".$meta_key."&show_results=".$type."&paged=";

			$prev_page = ($paged > 1)? $paged-1:1;
			$next_page = ($paged < $pages )? $paged+1: $pages;			

			require dirname(__FILE__)."/partials/octavius-url-results-display.php";
		} else {
			/**
			 * style for url checker
			 */
			wp_enqueue_style( 
				$this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/url-checker.css', 
				array(), 
				$this->version, 
				'all' 
			);
		
			/**
			 * Scripts for url checker
			 */
			wp_enqueue_script(
				$this->plugin_name,
				plugin_dir_url( __FILE__ )  . '/js/url-checker.js',
				array( 'jquery' )
			);

			require dirname(__FILE__)."/partials/octavius-url-check-display.php";
		}
		
	}

	/**
	 * add grid boxes
	 */
	public function load_grid_boxes()
	{
		require dirname(__FILE__)."/../grid-boxes/grid-octavius-box.inc";
	}

	/**
	 * loads googleAnalytics urls from octavius server
	 */
	public function get_ga_urls()
	{
		$page = 1;
		if( isset($_GET["page"]) )
		{
			$page = intval($_GET["page"]);
		}
		$store = new PH_Octavius_Store();
		$result =  $store->get_ga_urls($page);
		if(is_wp_error($result)){
			print json_encode( array(
				"error" => true,
				"error_msg" => $result->get_error_message(),
			) );
		} else {
			print json_encode($result);
		}
		wp_die();
	}

	/**
	 * get url checker statistics
	 */
	public function get_ga_statistics()
	{
		$result = (object) array();
		$result->error = false;
		$result->error_msg = "";
		if( !isset($_GET["meta_key"]) ){
			$result->error = true;
			$result->error_msg = "No meta key for urls";
			print json_encode($result);
			wp_die();
		}
		$store = new PH_Octavius_Store();
		$key = sanitize_text_field($_GET["meta_key"]);
		update_option("octavius_url_checker_meta_key",$key);
		$result->stats = $store->get_ga_matched_statistics($key);
		print json_encode($result);
		wp_die();
	}

}
