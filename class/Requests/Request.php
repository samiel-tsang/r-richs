<?php
namespace Requests;

class Request {
	const REFERER_URLONLY 	= 1;
	const REFERER_QUERY		= 2;
	const REFERER_FRAGMENT	= 4;
	
	const REQUEST_METHOD	= ['get', 'post', 'files', 'json', 'cookies'];
	
	private static $instance = null;
	
	public static function get() {
		if (is_null(self::$instance)) {
			self::$instance = new Request;
		}
		return self::$instance;
	}
	
	public $post;
	public $get;
	public $files;
	public $json;
	public $cookies;
	
	public $url;
	public $method;
	
	public function __construct() {
		$this->post = new \Core\Data($_POST);
		$this->get = new \Core\Data($_GET);
		$this->files = new \Core\Data($_FILES);
		$this->cookies = new \Core\Data($_COOKIE);
		$this->json = new \Core\Data;
		if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == 'application/json') {
			$json = json_decode(file_get_contents('php://input'), true);
			$this->json->addDataArr($json);
		}
		
		$this->url = "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$this->method = $_SERVER['REQUEST_METHOD'];
	}
	
	public function baseUrl($showSlash=false) {
		$reqUrl = parse_url($this->url, PHP_URL_PATH);
		if ($reqUrl == '/') return ($showSlash)?'/':'';
		
		if ($_SERVER['PHP_SELF'] == $reqUrl) return substr($reqUrl, 0, strrpos($reqUrl, '/'));
		
    	$phpSelfArr = explode("/", $_SERVER['PHP_SELF']);
    	$reqURLArr = explode("/", $reqUrl);
    	$basePathArr = [];
    	for($i = 1; $i < count($phpSelfArr); $i++) {
    	   if (!empty($phpSelfArr[$i]) && $phpSelfArr[$i] == $reqURLArr[$i]) {
    	      $basePathArr[] = $phpSelfArr[$i];
    	   }
    	}
		if (count($basePathArr) == 0) return ($showSlash)?'/':'';
		
    	return '/'.implode("/", $basePathArr);
	}
	
	public function requestUrl() {
		$reqUrl = parse_url($this->url, PHP_URL_PATH);
		if ($_SERVER['PHP_SELF'] == $reqUrl) return substr($reqUrl, strrpos($reqUrl, '/'));
		
		$phpSelfArr = explode("/", $_SERVER['PHP_SELF']);
    	$reqURLArr = explode("/", parse_url($this->url, PHP_URL_PATH));
    	$pathArr = [];
    	for($i = 1; $i < count($reqURLArr); $i++) {
    	   if (!empty($reqURLArr[$i]) && !(isset($phpSelfArr[$i]) && $phpSelfArr[$i] == $reqURLArr[$i])) {
    	      $pathArr[] = $reqURLArr[$i];
    	   }
    	}

    	return '/'.implode("/", $pathArr); 
	}
	
	public function queryStr() {
		return parse_url($this->url, PHP_URL_QUERY);
	}
	
	public function fragment() {
		return parse_url($this->url, PHP_URL_FRAGMENT);
	}
	
	public function referer($options = self::REFERER_URLONLY) {
		if (!isset($_SERVER['HTTP_REFERER'])) return '';
		$referUrl = parse_url($_SERVER['HTTP_REFERER']);
		
		if ($referUrl['host'] != parse_url($this->url, PHP_URL_HOST)) return $_SERVER['HTTP_REFERER'];
		
		$from = '/'.preg_quote($this->baseUrl(), '/').'/';
        $urlPath = preg_replace($from, "", $referUrl['path'], 1);
		
		if (!empty($referUrl['query']) && ($options & self::REFERER_QUERY))
			$urlPath .= '?'.$referUrl['query'];
		if (!empty($referUrl['fragment']) && ($options & self::REFERER_FRAGMENT))
			$urlPath .= '#'.$referUrl['fragment'];
		
		return $urlPath;
	}
	
	/** 
	 * Get header Authorization
	 * */
	public function getAuthorizationHeader(){
			$headers = null;
			if (isset($_SERVER['Authorization'])) {
				$headers = trim($_SERVER["Authorization"]);
			}
			else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
				$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
			} elseif (function_exists('apache_request_headers')) {
				$requestHeaders = apache_request_headers();
				// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
				$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
				//print_r($requestHeaders);
				if (isset($requestHeaders['Authorization'])) {
					$headers = trim($requestHeaders['Authorization']);
				}
			}
			return $headers;
		}
	/**
	 * get access token from header
	 * */
	public function getBearerToken() {
		$headers = self::getAuthorizationHeader();
		// HEADER: Get the access token from the header
		if (!empty($headers)) {
			if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
				return $matches[1];
			}
		}
		return null;
	}
	
	public function isAjaxRequest() {
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}
	
	public function isExists($name) {
		$value = $this->existsIn($name);
		return (!is_null($value));
	}
	
	public function isValued($name) {
		$value = $this->existsIn($name);
		return (!is_null($value) && !empty($value));
	}
	
	public function existsIn($name) {
		foreach (self::REQUEST_METHOD as $method) {
			if (isset($this->$method->$name)) {
				return $this->$method->$name;
			}
		}
		return null;
	}
	
	public function __toString() {
		$ret  = "Request Object\n";
		$ret .= "Url: $this->url\nPost: $this->post\nGet: $this->get\nCookies: $this->cookies\n";
		$ret .= "File: $this->files\nJson: $this->json\n";
		return $ret;
	}
}