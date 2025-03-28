<?php
namespace Core;

class Data {
	private $data;
	public function __construct(&$arrData = []) {
		$this->data = array();
		if (is_array($arrData))
			$this->data =& $arrData;
		/*
			foreach ($arrData as $key => $value)
				$this->data[$key] = $value;
		*/
	}
	public function addDataArr(&$arrData) {
		if (is_array($arrData)) {
			if (count($this->data))
				$this->data = array_merge($this->data, $arrData);
			else 
				$this->data =& $arrData;
		/*
			foreach ($arrData as $key => $value)
				$this->data[$key] = $value;
		*/
			return true;
		}
		return false;
	}
	public function toArray() {
	   return $this->data;
	}
	public function __get($name) {
		if (array_key_exists($name, $this->data)) return $this->data[$name];
		return null;
	}
	public function __set($name, $value) {
		$this->data[$name] = $value;
	}
	
	public function __isset($name) {
		return isset($this->data[$name]);
	}
	
	public function __toString() {
		return print_r($this->data, true);
	}
}