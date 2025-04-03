<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem;


class documentHelper {
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("document")->where(['id', '=', "?"]);
		$stm = $sql->prepare();
		$stm->execute([$id]);
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;

		return $obj;
	}
	
	public static function upload($fileObj, $type) {

		$currentUserObj = unserialize($_SESSION['user']);

		$url =  $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."/";   
		$userObj = unserialize($_SESSION['user']);
		$target_dir = "upload/".$type."/";
		$target_file_name = uniqid()."_".basename($fileObj['name']);
		$target_file_path = $target_dir.$target_file_name;
		$documentID = 0;

		if(move_uploaded_file($fileObj['tmp_name'], $target_file_path)){
		
			$addFields = [
				'fileName'=>"?", 
				'filePath'=>"?", 
				'downloadPath'=>"?",
				'fileType'=>"?", 
				'fileSize'=>"?", 
				'docType'=>"?", 
				'createBy'=>"?",
				'modifyBy'=>"?"
			];			
			
			$addValues = [
				strip_tags($fileObj['name']), 
				strip_tags($target_file_name), 
				strip_tags($url.$target_file_path), 
				strip_tags($fileObj['type']),
				strip_tags($fileObj['size']),
				strip_tags($type), 
				strip_tags($userObj->id),
				strip_tags($userObj->id)
			];
								
			$sql = Sql::insert('document')->setFieldValue($addFields);
			if ($sql->prepare()->execute($addValues)) {
				$documentID = db()->lastInsertId();

				$logData = [];
				$logData['userID']= $currentUserObj->id;
				$logData['module'] = $fileObj['type'];
				$logData['referenceID'] = $documentID;
				$logData['action'] = "Insert";
				$logData['description'] = "Add Document [".$target_file_name."]";
				$logData['sqlStatement'] = $sql;
				$logData['sqlValue'] = $addFields;
				$logData['changes'] = [
					[
						"key"=>"fileName", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($fileObj['name'])
					],[
						"key"=>"filePath", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($target_file_name)
					]
					,[
						"key"=>"downloadPath", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($url.$target_file_path)
					]
					,[
						"key"=>"fileType", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($fileObj['type'])
					]
					,[
						"key"=>"fileSize", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($fileObj['size'])
					]
					,[
						"key"=>"docType", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($type)
					]
				];

				systemLog::add($logData);


			}						
		}

		return $documentID;
			
	}	

	public static function delete($docID) {	

		if (!user::checklogin()) 
			return false;
			
		$currentUserObj = unserialize($_SESSION['user']);
		
		if (!isset($docID) || empty($docID))
			return false;

		$docObj = self::find($docID);

		if (is_null($docObj))
			return false;	
			
		$sql = Sql::delete('document')->where(['id', '=', $docID]);
		if ($sql->prepare()->execute()) {
			
			$file_real_path = $_SERVER['DOCUMENT_ROOT']."/upload/".$docObj->docType."/".$docObj->filePath;
			unlink($file_real_path);

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = $docObj->docType;
			$logData['referenceID'] = $docID;
			$logData['action'] = "Delete";
			$logData['description'] = "Delete Document [".$docObj->filePath."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $docID;
			$logData['changes'] = [];
			systemLog::add($logData);			

			return true;
		} else {
			return false;
		}					
	}

	public static function detail($request) {

        $docObj = self::find($request->get->id);

		if(is_null($docObj))
			return new Data(['success'=>false, 'message'=>L('error.documentNotFound')]);

		return new Data(['success'=>true, 'message'=>json_encode($docObj)]);

	}
	
}