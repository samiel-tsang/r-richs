<?php
namespace Error {

class ExceptionHandler {
	private static $handler;
	
	public static function getHandler() {
		if (is_null(self::$handler)) {
			self::$handler = new ExceptionHandler;
		}
		return self::$handler;
	}
	
	public function handle(\Throwable $ex) {
		$pg = new \Pages\Page('error/500', ['ex'=>$ex,]);
		$pg->render();
	}
	
	public function error($request) {
		$errorCode = $request->get->code;
		$pg = new Pages\Page('error/'.$errorCode);
		if ($errorCode != 500 && $pg->exists()) return $pg;
		$pg->setView('error/404');
		if ($pg->exists()) return $pg;
		return new Responses\Message('alert', 'No Available Page');
	}
}

}

namespace {
Routing\Route::add('GET', '/error/{code}', 'Error\ExceptionHandler@error', 'page.error');
}