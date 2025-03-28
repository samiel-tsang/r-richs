<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem;


class systemSetting {

    public static function findMetaValue($metaKey, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("systemSetting")->where(['metaKey', '=', '?']);
		$stm = $sql->prepare();
		$stm->execute([$metaKey]);
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

    public static function updateMetaValue($metaKey, $metaValue) {
        
        $sql = Sql::update('systemSetting')->setFieldValue(['metaValue'=>$metaValue])->where(['metaKey', '=', '"'.$metaKey.'"']);

		if ($sql->prepare()->execute()) {       
            return true;
        }

        return false;
		
	}

    public function list($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false)); 

		$obj = null;
		return new FormPage('systemSetting/list', $obj);
	}	

    public function update($request) {

		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

        $currentUserObj = unserialize($_SESSION['user']);

        // form check	
		if (!isset($request->post->alertEmailTemplateID) || empty($request->post->alertEmailTemplateID)) 
			return new Data(['success'=>false, 'message'=>L('error.systemSettingEmptyAlertEmailTemplate'), 'field'=>'alertEmailTemplateID']);     
        
        if (!isset($request->post->autoEmailHour) || empty($request->post->autoEmailHour)) 
			return new Data(['success'=>false, 'message'=>L('error.systemSettingEmptyEmailSendHour'), 'field'=>'autoEmailHour']);               

        self::updateMetaValue("alertEmailTemplateID", $request->post->alertEmailTemplateID);
        self::updateMetaValue("autoEmailHour", $request->post->autoEmailHour);

        return new Data(['success'=>true, 'message'=>L('info.updated')]);		

    }


}