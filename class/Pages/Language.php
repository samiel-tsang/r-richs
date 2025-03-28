<?php
namespace Pages {

class Language {
	private static $currLang;
	
	public static function get() {
		if (is_null(self::$currLang)) {
			self::$currLang = new Language;
		}
		return self::$currLang;
	}
	
	public static function build($lang = "en") {
		if (is_null(self::$currLang)) {
			self::$currLang = new Language($lang);
		} else {
			self::$currLang->switchLang($lang);
		}
		return self::$currLang;
	}
	public static function getAvailableLang() {
		$filePath = realpath(BASEPATH."/lang").DIRECTORY_SEPARATOR;
		foreach (glob($filePath."*.ini") as $file) {
			$langCode = substr($file, strlen($filePath), -4);
			$langList = parse_ini_file($file);
			yield ['langCode'=>$langCode, 'langName'=>$langList['langName']];
		}
	}
	
	private $langCode;
	private $langList;
	
	public function __construct($lang = "en") {
		$this->switchLang($lang);
	}
	
	public function switchLang($lang) {
		$this->langCode = $lang;
		$this->loadLang();
	}
	
	public function loadLang() {
		$defLangFilePath = BASEPATH."/lang/".(cfg()['lang'] ?? "en").".ini";
		if (file_exists($defLangFilePath)) 
			$this->langList = parse_ini_file($defLangFilePath);
		
		$filePath = BASEPATH."/lang/".$this->langCode.".ini";
		if (!file_exists($filePath)) return false;
		
		$this->langList = array_merge($this->langList, parse_ini_file($filePath));
		return true;
	}
	
	public function getText($code) {
		if (isset($this->langList[$code]))
			return $this->langList[$code];
		return '';
	}
	
	public function switcher($request) {
		$refererUrl = $request->referer(\Requests\Request::REFERER_QUERY);
		$_SESSION['lang'] = $request->get->code;
		\Utility\WebSystem::redirect($request->baseUrl().$refererUrl);
	}
}

}

namespace {
function L($code) { return Pages\Language::get()->getText($code); }
Routing\Route::add('GET', '/lang/{code}', 'Pages\Language@switcher', 'page.langSwitcher');
}