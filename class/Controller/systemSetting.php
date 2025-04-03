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
        
        $sql = Sql::update('systemSetting')->setFieldValue(['metaValue'=>'"'.$metaValue.'"'])->where(['metaKey', '=', '"'.$metaKey.'"']);

		if ($sql->prepare()->execute()) {       
            return ["status"=>true, "sqlStatement"=>$sql, "sqlValue"=>$metaValue];
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

		$currentAlertEmailTemplateSettingObj = self::findMetaValue("alertEmailTemplateID");
		$currentAutoEmailHourSettingObj = self::findMetaValue("autoEmailHour");
		
		if($currentAlertEmailTemplateSettingObj->metaValue!=$request->post->alertEmailTemplateID){
     	   $updateEmailTemplateAction = self::updateMetaValue("alertEmailTemplateID", $request->post->alertEmailTemplateID);

			if($updateEmailTemplateAction) {
				$logData = [];
				$logData['userID']= $currentUserObj->id;
				$logData['module'] = "System Setting";
				$logData['referenceID'] = $currentAlertEmailTemplateSettingObj->id;
				$logData['action'] = "update";
				$logData['description'] = "Update Alert Email Template ID Setting";
				$logData['sqlStatement'] = $updateEmailTemplateAction['sqlStatement'];
				$logData['sqlValue'] = $updateEmailTemplateAction['sqlValue'];
				$logData['changes'] = [["key"=>"alertEmailTemplateID", "valueFrom"=>$currentAlertEmailTemplateSettingObj->metaValue, "valueTo"=>strip_tags($request->post->alertEmailTemplateID)]];
				systemLog::add($logData);
			}
		}	

		if($currentAutoEmailHourSettingObj->metaValue!=$request->post->autoEmailHour){
        	
			$updateAutoEmailHourAction = self::updateMetaValue("autoEmailHour", $request->post->autoEmailHour);

			if($updateAutoEmailHourAction) {
				$logData = [];
				$logData['userID']= $currentUserObj->id;
				$logData['module'] = "System Setting";
				$logData['referenceID'] = $currentAutoEmailHourSettingObj->id;
				$logData['action'] = "update";
				$logData['description'] = "Update Auto Email Hour Setting";
				$logData['sqlStatement'] = $updateAutoEmailHourAction['sqlStatement'];
				$logData['sqlValue'] = $updateAutoEmailHourAction['sqlValue'];				
				$logData['changes'] = [["key"=>"autoEmailHour", "valueFrom"=>$currentAutoEmailHourSettingObj->metaValue, "valueTo"=>strip_tags($request->post->autoEmailHour)]];
				systemLog::add($logData);
			}				
		}
		
        return new Data(['success'=>true, 'message'=>L('info.updated')]);		

    }


}