<?php

/**
 *	Storage class of octavius
 * 
 */
class PH_Octavius_Store{

	/**
	 * main table of octavius 
	 */
	private $table;

	/**
	 * url checker table
	 */
	private $table_url_checker;


	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() 
	{
		global $wpdb;
		$this->table = $wpdb->prefix."octavius_top_contents";
		$this->table_url_checker = $wpdb->prefix."octavius_all_ga_urls";
	}

	/**
	 * returns all available service types
	 * @return array
	 */
	public function getServiceTypes()
	{
		global $wpdb;
		$items = $wpdb->get_results("SELECT DISTINCT type FROM ".$wpdb->prefix."octavius_top_contents");
		$types = array();
		foreach ($items as $item) {
			$types[] = $item->type;
		}
		return $types;
	}

	/**
	 * get top List by type
	 * @param  string 	$type 	Service type string
	 * @param  date      minimum date of posts
	 * @return array 			Array of post ids
	 */
	public function getTop($type, $min_date = null){
		global $wpdb;

		$date_query = "";
		if($min_date != null){
			$date_query = "AND p.post_date > '".$min_date."' ";
		}
		$query = 'SELECT * FROM '.$this->table.' as o, '.
				$wpdb->prefix.'posts as p WHERE p.ID = o.pid '.
				$date_query.'AND o.type = "'.$type.'" ORDER BY o.views DESC';
		return $wpdb->get_results( $query, OBJECT );
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
	 * updates octavius options
	 */
	public function update_options($client, $pw, $domain){
		update_option("ph_octavius_client", sanitize_text_field( $client ) );
		update_option("ph_octavius_pw", sanitize_text_field( $pw ) );
		/**
		 * Save the domain like we need it
		 */
		if( strpos($domain,"http://") !== 0 )
		{
			$domain = "http://".$domain;
		}
		$domain = rtrim($domain, "/");
		update_option("ph_octavius_domain", sanitize_text_field( $domain ) );
	}

	/**
	 *	get new data from octavius service
  	 */
	public function get_data_from_remote($toponly = true) {
		$options = $this->get_options();
		// get Latest Date from Octavius
		$location = $options->domain."/v1.0/".$options->client."/getTopLists";
		$curl = new PH_Octavius_CURL($location, $options->client, $options->pw );
		$json_result = $curl->get_JSON();
		$return = array();
		if(!is_array($json_result) && !is_object($json_result)) return $return;
        foreach ($json_result as $type => $data) {
        	if($toponly){
        		$return[$type] = $this->import_tops($options->domain.$data->url, $type, $options);
        	} else {
        		$return[$type] = $this->import_all($options->domain.$data->url, $type, $options);
        	}
        	
        }
        return $return;
	}

	/**
	 * imports the top pages from octavius
	 * @param  url where the json is waiting to be red
	 * @param  table that get the data
	 * @param  htaccess user:password
	 */
	private function import_tops($url, $type, $options){
		$curl = new PH_Octavius_CURL($url, $options->client, $options->pw);
		if(is_null($json_result)) {
			return "[ERROR] NO JSON RESULT";
		} else if(count($json_result) < 10){
			return "[WARNING] Only got ".count($json_result)." results";
		} else {
			
	       	return $this->insert_data($type, $curl->get_JSON() );
		}
	}

	/**
	 * imports the all pages from octavius
	 * @param  url where the json is waiting to be red
	 * @param  type of data
	 * @param  htaccess user:password
	 */
	public function import_all($url, $type, $options)
	{	
		$page = 1;
		$inserted = 0;
		do{
			$curl = new PH_Octavius_CURL(rtrim($url, "/")."/".$page, $options->client, $options->pw);
			$json_result = $curl->get_JSON();
			if( $page == 1 && count($json_result) > 0){
				$wpdb->query("TRUNCATE TABLE ".$this->table);
			}
			$inserted = $inserted+$this->insert_data($type, $json_result);
			$page++;
		} while( count($json_result) > 0 );
		return $inserted;
	}

	/**
	 * inserts octavius datas
	 * @param  string $type   	which type of data to save
	 * @param  array $items 	array of data items
	 * @return int       number of rows affected
	 */
	public function insert_data($type, $items){
		global $wpdb;
		
		$values = array();
		foreach($items as $item) 
		{
			$count = null;
			if($type == "facebook"){
				$count = $item->like;
			} else if($type == "twitter"){
				$count = $item->count;
			} else {
				$count = $item->pageviews;
			}

			$values[] = "( '".$item->page_id."', '".$count."', '".$type."' )";

       	}
       	if(count($values) > 0){
       		$query = "INSERT INTO ".$this->table." (pid, views, type) VALUES ".implode(",", $values).";";
       		return $wpdb->query($query);
       	} 
       	return 0;
       	
	}

	/**
	 * resets all urls for url checker
	 */
	public function reset_all_ga_urls()
	{
		global $wpdb;
		$wpdb->query("TRUNCATE TABLE ".$this->table_url_checker);
	}

	/**
	 * get urls for url checker
	 */
	public function get_ga_urls($page = 1, $server = true)
	{
		$options = $this->get_options();
		$domain = $options->domain;
		$client = $options->client;

		if($server){
			$url = $domain."/v1.0/".$client."/url-checker/".$page;
			$curl = new PH_Octavius_CURL($url, $options->client, $options->pw);
			$json = $curl->get_JSON();
			$urls = $json->urls;
			$result = $this->save_ga_urls($urls);
			unset($json->urls);
			$this->save_ga_url_attributes($json);
			$json->urls = $urls;
		} else {
			$json = $this->get_ga_url_attributes();
			$json->urls =  $this->get_ga_urls_local($page, $json->limit);
		}
		$json->page = $page;
		$json->server = $server;
		
		return $json;
	}

	public function save_ga_url_attributes($attrs){
		return update_site_option("ph_octavius_ga_url_attributes", $attrs);
	}

	public function get_ga_url_attributes(){
		return (object)get_site_option("ph_octavius_ga_url_attributes", array());
	}

	public function save_ga_urls($urls_array){
		global $wpdb;
		$count =0;
		foreach ($urls_array as $url) {
			$wpdb->replace(
				$this->table_url_checker,
				array(
					"url" => $url,
				),
				array(
					"%s",
				)
			);
			$count++;
		}
		return $count;
	}

	public function get_ga_urls_local($page = 1, $limit = 10000){
		global $wpdb;
		$low_limit = ( $page - 1 )  * $limit ;
		return $wpdb->get_col( 'SELECT url FROM '.$this->table_url_checker." LIMIT $low_limit, $limit" );
	}

	/**
	 * return lost and found statistics for url checker
	 * 
	 */
	public function get_ga_matched_statistics($postmeta_key)
	{
		global $wpdb;
		$stats = (object) array();
		
		$stats->found =  $this->get_ga_found_count($postmeta_key);
		$stats->lost =  $this->get_ga_lost_count($postmeta_key);

		return $stats;
	}

	public function get_ga_found_count($postmeta_key)
	{
		global $wpdb;
		// return "SELECT COUNT(ga.id) FROM `".$this->table_url_checker."` ga INNER JOIN ".
		// 	$wpdb->prefix."postmeta wp ON ga.url = wp.meta_value AND wp.meta_key='".$postmeta_key."'";
		return intval($wpdb->get_var("SELECT COUNT(ga.id) FROM `".$this->table_url_checker."` ga INNER JOIN ".
			$wpdb->prefix."postmeta wp ON ga.url = wp.meta_value AND wp.meta_key='".$postmeta_key."'"));
	}

	public function get_ga_lost_count($postmeta_key)
	{
		global $wpdb;
		return intval($wpdb->get_var("SELECT COUNT(url) FROM `".$this->table_url_checker."` WHERE id NOT IN( SELECT ga.id FROM `".
			$this->table_url_checker."` ga INNER JOIN ".$wpdb->prefix."postmeta wp ON ga.url = wp.meta_value ".
			"AND wp.meta_key='".$postmeta_key."')"));
	}

	/**
	 * get all found elements 
	 * 
	 */
	public function get_ga_found_elements($postmeta_key, $limit = null, $page = null)
	{
		global $wpdb;
		return $wpdb->get_results(
			"SELECT ga.id, post_id, meta_id, url FROM `".$this->table_url_checker."` ga INNER JOIN ".
			$wpdb->prefix."postmeta wp ON ga.url = wp.meta_value AND wp.meta_key='".$postmeta_key."' ORDER BY url".
			$this->build_limit_query($limit, $page)
		);
	}
	/**
	 * get all lost urls from database
	 */
	public function get_ga_lost_elements($postmeta_key, $limit = null, $page = null)
	{	
		
		global $wpdb;
		return $wpdb->get_results(
			"SELECT url FROM `".$this->table_url_checker."` WHERE id NOT IN( SELECT ga.id FROM `".
			$this->table_url_checker."` ga INNER JOIN ".$wpdb->prefix."postmeta wp ON ga.url = wp.meta_value ".
			"AND wp.meta_key='".$postmeta_key."') ORDER BY url".
			$this->build_limit_query($limit, $page)
		);
	}

	private function build_limit_query($limit = null, $page = null)
	{
		if($page != null){
			return " LIMIT ".($page-1)*$limit.", ".$limit;
		} else if($limit != null)
		{
			return " LIMIT ".$limit;
		}
		return "";
	}

	/**
	 * installation method that creates tables in database
	 */
	public function install()
	{
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		/**
		 * Create content_relations_relations table
		 */
		dbDelta( "CREATE TABLE IF NOT EXISTS `".$this->table."` (
			id INT unsigned NOT NULL AUTO_INCREMENT,
			pid BIGINT unsigned NOT NULL,
			type VARCHAR(30) NOT NULL,
			views INT unsigned NOT NULL,
			PRIMARY KEY id (id),
			UNIQUE KEY `pid_per_type` (`pid`,`type`),
			KEY `type` (`type`),
			KEY `views` (`views`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_bin;");

		dbDelta( "CREATE TABLE IF NOT EXISTS `".$this->table_url_checker."` (
			id INT unsigned NOT NULL AUTO_INCREMENT,
			url VARCHAR(255) NOT NULL,
			PRIMARY KEY id (id),
			UNIQUE KEY `url` (`url`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_bin;");
	}

	/**
	 * drop all octavius tables from database
	 */
	public function uninstall(){
		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS `".$this->table."`;" );
		$wpdb->query( "DROP TABLE IF EXISTS `".$this->table_url_checker."`;" );		
	}

}