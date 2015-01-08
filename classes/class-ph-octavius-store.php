<?php

/**
 *	Storage class of octavius
 * 
 */
class PH_Octavius_Store{

	/**
	 * array of table names
	 */
	private $tables;


	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() 
	{
		global $wpdb;
		$this->tables = array();
		$this->tables["day"] = $wpdb->prefix."octavius_ga_day";
		$this->tables["week"] = $wpdb->prefix."octavius_ga_week";
		$this->tables["month"] = $wpdb->prefix."octavius_ga_month";
		$this->tables["ever"] = $wpdb->prefix."octavius_ga_ever";
		$this->tables["facebook"] = $wpdb->prefix."octavius_facebook";
		$this->tables["twitter"] = $wpdb->prefix."octavius_twitter";
	}

	/**
	 * returns all available service types
	 * @return array
	 */
	public function getServiceTypes()
	{
		return array("day", "week", "month", "ever", "facebook", "twitter");
	}

	/**
	 * get top List by type
	 * @param  string 	$type 	Service type string
	 * @return array 			Array of post ids
	 */
	public function getTop($type){
		$table = $this->tables[$type];
		global $wpdb;
		return $wpdb->get_results( 'SELECT * FROM '.$table.' ORDER BY views DESC', OBJECT );
	}

	/**
	 * gets octaivus options as object
	 * 
	 */
	public function get_options(){
		return (object)array(
			"client"=> get_option("ph_octavius_client", ""),
			"pw"=> get_option("ph_octavius_pw", ""),
			"domain"=> get_option("ph_octavius_domain", ""),
		);
	}

	/**
	 * gets username from options
	 */
	public function  update_options($client, $pw, $domain){
		update_option("ph_octavius_client", sanitize_text_field( $client ) );
		update_option("ph_octavius_pw", sanitize_text_field( $pw ) );
		update_option("ph_octavius_domain", rtrim(sanitize_text_field( $domain ), "/") );
	}

	/**
	 * Set Octavius Page Data to Meta Field
  	 */
	public function get_data_from_remote() {
		$options = $this->get_options();
		// get Latest Date from Octavius
		$location = $options->domain."/v1.0/".$options->client."/getTopLists";

		$curl = new PH_Octavius_CURL($location, $options->client, $options->pw );
		$json_result = $curl->get_JSON();

        foreach ($json_result as $type => $data) {
        	$this->import_tops($options->domain.$data->url, $this->tables[$type], $options);
        }
        
	}

	/**
	 * imports the top pages from octavius
	 * @param  url where the json is waiting to be red
	 * @param  table that get the data
	 * @param  htaccess user:password
	 */
	private function import_tops($url, $table, $options){

		$curl = new PH_Octavius_CURL($url, $options->client, $options->pw);
		$json_result = $curl->get_JSON();

		global $wpdb;
		if(is_null($json_result)) {
			echo "[ERROR] NO JSON RESULT";
		} else {
			foreach($json_result as $item) {
	        	$wpdb->replace( 
	        		$table, 
	        		array( 
	        			"pid" => $item->page_id , 
	        			"views" => $item->pageviews,
	        		)
	        	);
	       	}
		}
	}

	/**
	 * installation method that creates tables in database
	 */
	public function install(){
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		/**
		 * Create content_relations_relations table
		 */
		foreach ($this->tables as $key => $table) {
			dbDelta( "CREATE TABLE IF NOT EXISTS `".$table."` (
				pid BIGINT unsigned NOT NULL,
				views INT unsigned NOT NULL,
				PRIMARY KEY pid (pid),
				KEY `views` (`views`)
			);");
		}

	}

	/**
	 * drop all octavius tables from database
	 */
	public function uninstall(){
		global $wpdb;
		foreach ($this->tables as $key => $table) {
			$wpdb->query( "DROP TABLE `".$table."`;" );
		}
		
	}

}