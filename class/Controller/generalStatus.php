<?php
namespace Controller;

use Responses\Message, Responses\Action;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem;


class generalStatus {
	//private $stmStatus = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("generalStatus")->where(['id', '=', "?"]);
		$stm = $sql->prepare();
		$stm->execute([$id]);
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;

		return $obj;
	}

	public static function findAll($fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("generalStatus");
		$stm = $sql->prepare();
		$stm->execute();
		return $stm;
	}    
	
}