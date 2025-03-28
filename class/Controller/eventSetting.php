<?php
namespace Controller;

use Responses\Message, Responses\Action;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem;

class eventSetting {
	
	/* Page Function */
	public function form($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		
		$obj = null;
		
		return new FormPage('eventSetting/form', $obj);
	}
	
	public function edit($request) {		
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		
        $startDateMeta = self::getSettingMeta("regStartDate");

        if(is_null($startDateMeta)){   
            $sql = Sql::insert('eventSetting')->setFieldValue([
                "metaKey"=>"'regStartDate'", 
                "metaValue"=>"'".strip_tags($request->post->startDate)."'"
            ]);
            $sql->prepare()->execute();			
        } else {
            $sql = Sql::update('eventSetting')->setFieldValue([
                "metaValue"=>"'".strip_tags($request->post->startDate)."'"
            ])->where(['id', '=', $startDateMeta->id]);
            $sql->prepare()->execute();		  
        }

        $endDateMeta = self::getSettingMeta("regEndDate");

        if(is_null($endDateMeta)){   
            $sql = Sql::insert('eventSetting')->setFieldValue([
                "metaKey"=>"'regEndDate'", 
                "metaValue"=>"'".strip_tags($request->post->endDate)."'"
            ]);
            $sql->prepare()->execute();			
        } else {
            $sql = Sql::update('eventSetting')->setFieldValue([
                "metaValue"=>"'".strip_tags($request->post->endDate)."'"
            ])->where(['id', '=', $endDateMeta->id]);
            $sql->prepare()->execute();		  
        }
		
        return new Message('info', L('info.updated'));
		
	}
	
	// fetch mata data 
	public static function getSettingMeta($metaKey, $fetchMode=\PDO::FETCH_OBJ){
	 	$sql = Sql::select("eventSetting")->where(['metaKey', '=', '?']);
		$stm = $sql->prepare();
		$stm->execute([$metaKey]);
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}    

}