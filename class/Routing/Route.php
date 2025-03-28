<?php
namespace Routing;

class Route {
	
	const Request_Method = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'CONNECT', 'OPTIONS', 'TRACE'];
	public static $routeList;
	public static $currRoute;
	public static $currRouteName;
	
	public static function add($method, $path, $target, $name="") {
		self::$routeList = self::$routeList ?? [];
		self::$routeList[$method] = self::$routeList[$method] ?? [];
		self::$routeList[$method][$name] = new Route($path, $target);;
	}
	
	public static function find($request) {
		$reqPath = $request->requestUrl();
		if (!isset(self::$routeList[$request->method])) return null;
		$routeList = self::$routeList[$request->method];
		//if (array_key_exists($reqPath, $routeList)) return $routeList[$reqPath];
		
		$slashCount = substr_count($reqPath, '/');
		$routeList = array_filter($routeList, function ($route) use ($slashCount) {
			return (substr_count($route->path, '/') == $slashCount);
		});
		
		foreach ($routeList as $key => $route) {
			$regex = preg_replace('/{([^{}]*)}/', '([^/]*)', $route->path);
			$count = preg_match_all('|^'.$regex.'$|', $reqPath, $values);
			if ($count) {
				preg_match_all('/{([^{}]*)}/', $route->path, $matches);
				$val = [];
				for ($i = 1; $i < count($values); $i++) 
					$val[] = $values[$i][0];
				$reqGet = array_combine($matches[1], $val);
				$request->get->addDataArr($reqGet);
				self::$currRoute = $route;
				self::$currRouteName = $key;
				return $route;
			}
		}
		return null; // Throw Exception for Handling
	}
	
	public static function getRouteByName($name, $method = '') {
		if (!empty($name)) {
			if (empty($method)) {
				foreach (self::Request_Method as $reqMet)
					if (isset(self::$routeList[$reqMet][$name]))
						return self::$routeList[$reqMet][$name];
			} else 
				return self::$routeList[$method][$name];
		}
		return null;
	}
	
	private $path;
	private $className;
	private $classMethod;
	
	public function __construct($path, $target) {
		$this->path = $path;
		list($this->className, $this->classMethod) = explode('@', $target);
	}
	
	public function __get($name) { 
		if (property_exists($this, $name)) 
			return $this->$name; 
		return null; 
	}
	
	public function path(Array $value = []) {
		$count = preg_match_all('/{([^{}]*)}/', $this->path, $matches);
		$path = $this->path;
		if ($count) {
			$path = preg_replace_callback('/{([^{}]*)}/', function ($m) use (&$value) {
				if (isset($value[$m[1]])) {
					$ret = $value[$m[1]];
					unset($value[$m[1]]);
					return $ret;
				}
				return '';
			}, $this->path);
		}
		if (count($value)) {
			array_walk($value, function (&$val, $key) {
				$val = urlencode($key).'='.urlencode($val);
			});
			$path .= '?'.implode('&', $value);
		}
		return $path;
	}
	
	public function __toString() {
		return "$this->path=>$this->className.$this->classMethod";
	}
}