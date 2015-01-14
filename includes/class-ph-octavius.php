<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 */
class PH_Octavius {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 */
	public function __construct() {
		$this->plugin_name = 'ph-octavius';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - PH_Octavius_Loader. Orchestrates the hooks of the plugin.
	 * - PH_Octavius_i18n. Defines internationalization functionality.
	 * - PH_Octavius_Admin. Defines all hooks for the dashboard.
	 * - PH_Octavius_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ph-octavius-loader.php';

		/**
		 * The class that handles curl requests to octavius service
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/class-ph-octavius-curl.php';

		/**
		 * The class that stores all octavius info
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/class-ph-octavius-store.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ph-octavius-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ph-octavius-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ph-octavius-public.php';

		$this->loader = new PH_Octavius_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 */
	private function set_locale() {

		$plugin_i18n = new PH_Octavius_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 */
	private function define_admin_hooks() {

		$plugin_admin = new PH_Octavius_Admin( $this->get_plugin_name(), $this->get_version() );

		/**
		 * registers all menu pages
		 */
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'menu_pages' );

		/**
		 * grid box
		 */
		$this->loader->add_action( 'grid_load_classes', $plugin_admin, 'load_grid_boxes' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 */
	private function define_public_hooks() {

		$plugin_public = new PH_Octavius_Public( $this->get_plugin_name(), $this->get_version() );

		// register urls for octavius
		$this->loader->add_action( 'init', $plugin_public, 'add_endpoint' );
		$this->loader->add_filter( 'query_vars', $plugin_public, 'add_query_vars' );
		$this->loader->add_action( 'parse_request', $plugin_public, 'sniff_requests' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 */
	public function get_version() {
		return $this->version;
	}

}
