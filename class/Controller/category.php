<?php
namespace Controller;

use Responses\Message, Responses\Action;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem;


class category {
	//private $stmStatus = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("category")->where(['id', '=', "?"]);
		$stm = $sql->prepare();
		$stm->execute([$id]);
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;

		return $obj;
	}

	public static function find_all($fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("category")->where(['status', '=', "'1'"]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetchAll($fetchMode);
		if ($obj === false) return null;

		return $obj;
	}	
	
}