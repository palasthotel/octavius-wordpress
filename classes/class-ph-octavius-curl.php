<?php

/**
 *	Storage class of octavius
 * 
 */
class PH_Octavius_CURL{

	/**
	 * the url that needs to be executed
	 */
	private $url;

	/**
	 * client/user for htaccess
	 */
	private $client;

	/**
	 * password for htaccess
	 */
	private $pw;

	/**
	 * string that has been returned
	 */
	private $returned;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct($url, $client = "", $pw = "") {
		$this->url = $url;
		$this->client = $client;
		$this->pw = $pw;
		$this->returned = "";
	}

	/**
	 * get json from returned
	 */
	public function get_JSON()
	{
		$result = $this->execute();
		if( is_wp_error($result) ){
			return $result;
		}
		return json_decode($result);
	}

	/**
	 * get string result
	 */
	public function get_result()
	{
		return $this->execute();
	}

	/**
	 * executes the curl request
	 * 
	 */
	private function execute(){

		if($this->pw != ""){
			$args['headers'] = array(
				'Authorization' => 'Basic ' . base64_encode( $this->client . ':' . $this->pw ),
			);
		}
		$result = wp_remote_request( $this->url, $args );
		if( is_wp_error($result) ){
			return $result;
		}
		return $result["body"];

	}

}