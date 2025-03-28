<?php
namespace Pages;

use Requests\Request;
use Responses\iResponse;
use Routing\Route;
use Utility\WebSystem;

class Page implements iResponse {
	private $view;
	private $vals;

	public function __construct(String $view, Array $vals = []) {
		$this->setView($view);
		$this->setValues($vals);
	}
	
	public function setView(String $view) {
		$this->view = $view;
		if (!$this->exists()) $this->view = '';
		return $this;
	}
	
	public function setValues(Array $vals = []) {
		if (is_null($this->vals)) $this->vals = $vals;
		else $this->vals = array_merge($this->vals, $vals);
		return $this;
	}
	
	public function emptyValues() {
		$this->vals = [];
		return $this;
	}
	
	public function viewName() { return $this->view; }
	
	public function viewFilePath() {
		return ((!empty(cfg()['viewPath']))?cfg()['viewPath']:'').$this->view.".php";
	}

	public function exists() {
		return file_exists($this->viewFilePath());
	}

	public function render() {
		$request = Request::get();
		foreach ($this->vals as $name => $vals) {
			${$name} = $vals;
		}
		if ($this->exists())
			include($this->viewFilePath());
	}
	
	public static function pagelink(string $pathName, Array $querystr = [], string $requestMethod = '') {
		return WebSystem::path(Route::getRouteByName($pathName, $requestMethod)->path($querystr), false, false);
	}
	
	// no use function
	/*
	public function add($view, $type = 'view') {
		if (property_exists('Page', $type)) {
			$this->$type[] = $view;
		}
	}
	*/
	
   public function display() {
      $this->render();
   }

   public function objArr() {
      return array('object'=>'page', 'view'=>$this->view, 'vals'=>$this->vals);
   }

   public function json() {
      return json_encode($this->objArr());
   }

   
}