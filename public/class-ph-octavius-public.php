<?php

/**
 * The public-facing functionality of the plugin.
 * 
 */
class PH_Octavius_Public {

	/**
	 * The ID of this plugin.
	 * 
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 * 
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 * 
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/** Add public query vars
	*	@param array $vars List of current public query vars
	*	@return array $vars
	*/
	public function add_query_vars($vars){
		$vars[] = '__api';
		$vars[] = '__ph_octavius';
		$vars[] = '__action'; 
		$vars[] = '__page'; 
		return $vars;
	}

	/** Add API Endpoint
	*	This is where the magic happens
	*	@return void
	*/
	public function add_endpoint() {
		add_rewrite_rule(
			'^__api/octavius/?([^/]*)?/?([0-9]+)?/?',
			'index.php?__api=2&__ph_octavius=1&__action=$matches[1]&__page=$matches[2]',
			'top'
		);
	}

	/**	Sniff Requests
	*	This is where we hijack all API requests
	* 	If $_GET['__api'] is set, we kill WP and serve response
	*	@return die if API request
	*/
	public function sniff_requests() {
		global $wp;
		if(isset($wp->query_vars['__ph_octavius'])){
     		$this->handle_api_request();
        	exit;
    	}

	}

	/** Handle API Requests
	 *  This is where we handle off API requests
	 *  and return proper responses
	 *  @return void
	 */
	protected function handle_api_request() {
		global $wp;
		switch ($wp->query_vars['__action']) {
		    case "importer":
				$store = new PH_Octavius_Store();
				print json_encode( $store->get_data_from_remote() );
				break; 
		    case "list":
				$this->get_the_url_list((int) $wp->query_vars['__page']);
				break;
			default:
				print "Sorry, what?";
		}
	}


	/**
	 * Return JSON formed URL List 
	 */
  	public function get_the_url_list($page = 1) {

  		// if there is no page id -> redirect to first page 
  		if($page < 1) {
  			//$page = 1;
  			$location = "http://".$_SERVER["HTTP_HOST"]."/__api/octavius/list/1";
  			wp_redirect( $location, 301 );
  			exit();
  		}

  		$root = array();
	    $root["pubDate"] = date("D, d M Y H:i:s O");
	    $root["lastBuildDate"] = date("D, d M Y H:i:s O");
	    $root["items"] = array();

	    global $wpdb;

	    // last 30 days;
	    // $lowdate = date("Y-m-d", time() - (30*24*60*60));
		// $results = $wpdb->get_results( 'SELECT ID, post_type, guid, post_date FROM ' . 
		// $wpdb->prefix . 'posts WHERE post_status ="publish" AND post_date > "' . $lowdate . '"  ORDER BY post_date DESC LIMIT 1000' , OBJECT );

	    $limit = 1000;
	    $low_limit = ( $page - 1 )  * $limit ;

	    $results = $wpdb->get_results( 'SELECT ID, post_type, guid, post_date FROM ' . $wpdb->prefix . 'posts '.
	    	'WHERE post_status ="publish" AND post_type="post" ORDER BY post_date DESC LIMIT '.$low_limit.','.$limit, OBJECT );

		foreach ($results as $result) {
			$item = array();
			$item["type"] = $result->post_type;
			$item["id"] = $result->ID;
			$item["guid"] =$result->guid;
			$link_path = str_replace( home_url(), "", get_permalink($result->ID) );
			$item["permalink"] =  $link_path;
			$item["pubDate"] = $result->post_date;
			$root["items"][] = $item;
		}

	    header("Content-Type: application/json;charset=UTF-8");
	    print json_encode($root);
	    exit();
  	}

}
