<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql;
//use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem, Utility\Security;

class api { 

    // get card url
    public function cardUrl($request) {
        if (!isset($request->get->id) || empty($request->get->id)) return null;

        $sql = Sql::select('card')->where(['id', '=', '?']);
        $stm = $sql->prepare();
        $stm->execute([$request->get->id]);
        $cardObj = $stm->fetch();

	$url = $cardObj['cardUrl'];
	if (empty($url)) {
		$_SESSION['card'] = serialize($cardObj);;
	        WebSystem::redirect(WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
	}
        WebSystem::redirect($url);
    }

}