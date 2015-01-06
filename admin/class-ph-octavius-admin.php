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
	 * Register the stylesheets for the Dashboard.
	 * 
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-name-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 * 
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );

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
		$submitted = (isset($_GET[$submit_button]) && $_GET[$submit_button] == $submit_button_text )? true: false;

		?>
		<div class="wrap">
			<h2>Octavius Settings</h2>
			<form method="post" action="<?php echo $_SERVER["PHP_SELF"]."?page=".$this->settings_page; ?>">

				<table class="form-table">
					<tr>
						<th scope="row"><label for="ph_octavius_client">Client</label></th>
						<td><input type="text" id="ph_octavius_client" name="ph_octavius_client" value="<?php echo $options->client; ?>" class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="ph_octavius_pw">Passwort</label></th>
						<td><input type="text" id="ph_octavius_pw" name="ph_octavius_pw" value="<?php echo $options->pw; ?>" class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="ph_octavius_doimain">Domain</label></th>
						<td><input type="text" id="ph_octavius_domain" name="ph_octavius_domain" value="<?php echo $options->domain; ?>" class="regular-text" /></td>
					</tr>
				</table>

				<?php submit_button($submit_button_text ,"primary",$submit_button); ?>
			</form>
		</div>
		<?php
	}

}
