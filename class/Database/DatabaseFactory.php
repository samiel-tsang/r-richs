<?php
namespace Database {

class DatabaseFactory {
	public static $dbList;
	
	public static function createDatabaseByConfig($cfg) {
		foreach ($cfg as $config) {
			$options = $config['options'] ?? [];
			if (array_key_exists('dsn', $config))
				self::createDatabaseByDSN($config['dsn'], $config['username'], $config['password'], $options);
			else {
				self::createDatabaseByParam($config['host'], $config['username'], $config['password'], $config['schema'], 
						$options, $config['driver'] ?? "mysql", $config['port'] ?? 3306, $config['charset'] ?? "utf8");
			}
		}
	}
	
	public static function createDatabaseByParam($host, $username, $password, $schema, $options=[], $driver="mysql", $port=3306, $charset="utf8") {
		$dsn = $driver.':host='.$host.';port='.$port.';dbname='.$schema.';charset='.$charset;
			
		return self::createDatabaseByDSN($dsn, $username, $password, $options);
	}
	
	
	public static function createDatabaseByDSN($dsn, $username, $password, $options = []) {
		self::$dbList = self::$dbList ?? [];
		self::$dbList[] = new \PDO($dsn, $username, $password, $options);
		
		return count(self::$dbList)-1;
	}
	
	public static function getDatabase($index = 0) {
		return self::$dbList[$index];
	}
}

} // end of namespace Database
namespace {
function db($idx = 0) { return Database\DatabaseFactory::getDatabase($idx); }
function dbes($str, $datatype = PDO::PARAM_STR, $idx = 0) { return db($idx)->quote($str, $datatype); }
}