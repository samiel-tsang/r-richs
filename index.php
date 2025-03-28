<?php
include_once('inc/global.php');
include_once("config/route.php");

$request = Requests\Request::get();
$route = Routing\Route::find($request);
$responses = new Pages\Page('error/404');

if (!is_null($route)) {
	$object = new $route->className;
	if (is_callable([$object, $route->classMethod]))
		$responses = $object->{$route->classMethod}($request);
	
	if ($responses === false || is_null($responses)) exit();
}

// need update condition for better checking
if ($request->isExists('api_key') || $request->isAjaxRequest() ||
	!is_null($request->getAuthorizationHeader()) || isset($_SERVER['HTTP_PROGRAM']) ||
	substr(Routing\Route::$currRouteName, -5) == '.json') {
	header('Content-Type: text/json; charset=utf-8');
	echo $responses->json();
	exit();
}

if ($responses instanceof Responses\Action) {
	switch ($responses->getAction()) {
		case 'redirect': Utility\WebSystem::redirect($responses->getScript()); break;
		case 'refresh': 
			list($timeout, $url) = explode(';', $responses->getScript());
			Utility\WebSystem::refresh($imeout, 'header', $url);
			break;
		default: Utility\WebSystem::redirect($request->baseUrl());
	}
} else if ($responses instanceof Pages\Page) {
	if (substr($responses->viewName(), 0, 6) == 'error/') {
		http_response_code(intval(substr($responses->viewName(), 6)));
	}
	$responses->render();
} else {
	$responses->display();
}