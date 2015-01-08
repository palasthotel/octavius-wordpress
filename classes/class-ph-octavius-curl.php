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
		return json_decode($this->execute());
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
		$ch = curl_init ();
        curl_setopt($ch,CURLOPT_URL, $this->url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        if($this->pw != ""){
        	curl_setopt($ch,CURLOPT_USERPWD,$this->client.":".$this->pw);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        /**
		 * bugfix for errors in WP plugins
		 */
		curl_setopt(
			$ch, 
			CURLOPT_USERAGENT, 
			"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.2 (KHTML, like Gecko) Chrome/22.0.1216.0 Safari/537.2"
		);
        $this->returned = curl_exec ($ch);
        return $this->returned;
	}

}