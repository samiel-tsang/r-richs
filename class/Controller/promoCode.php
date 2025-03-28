<?php
namespace Controller;

use Responses\Message, Responses\Action;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem;

class promoCode implements Listable {
	private $stmStatus = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ, $includePW=false) {
		$sql = Sql::select("promoCode")->where(['id', '=', $id]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

	public static function findCode($code, $fetchMode=\PDO::FETCH_OBJ, $id=0) {
		$sql = Sql::select("promoCode")->where(['code', '=', '?']);
		if (!empty($id))
			$sql->where(['id', '!=', $id]);
		$stm = $sql->prepare();
		$stm->execute([$code]);
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

	/* Page Function */
	public function list($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false)); 
		
		$sql = Sql::select('promoCode');
	
		if (isset($request->get->q)) {
			$hash = $request->get->q;
			if (!empty($_SESSION['search'][$hash]['sql_order_field']))
				$sql->order($_SESSION['search'][$hash]['sql_order_field'], (!empty($_SESSION['search'][$hash]['sql_order_seq']))?$_SESSION['search'][$hash]['sql_order_seq']:'ASC');
		}

		$listPage = new ListPage('promoCode/list', $sql);
		$listPage->setLister($this);
		return $listPage;
	}

	public function extraProcess($listObj) {
		if (is_null($this->stmStatus))
			$this->stmStatus = Sql::select('status')->where(['id', '=', "?"])->prepare();
			
		$this->stmStatus->execute([$listObj->status]);
		$objStatus = $this->stmStatus->fetch();
		$listObj->statusName = $objStatus['name'];
		
		return $listObj;
	}

	public function search($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false)); 
		
		$hash = '';
		if ($request->method == 'POST') {
			unset($_SESSION['search']);
			$hash = sha1("search_".time());
		}
		if ($request->method == 'GET') {
			$hash = (isset($request->get->q))?$request->get->q:sha1("order_".time());
			$_SESSION['search'][$hash]['sql_order_field'] = $request->get->field;
			$_SESSION['search'][$hash]['sql_order_seq'] = $request->get->order;
		}

		$param = ['pg'=>1];
		if (!empty($hash)) $param['q'] = $hash;
		return new Action('redirect', WebSystem::path(Route::getRouteByName('page.cardList')->path($param), false, false));
	}
	
	/* Page Function */
	public function form($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);
		
		return new FormPage('promoCode/form', $obj);
	}
	
	/* Page Function */
	public function info($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		
		$obj = self::find($request->get->id);
		return new Page('card/info', ['obj'=>$obj]);
	}

	/* Page Function */
	public function searchform($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		
		$obj = (isset($request->get->q))?$_SESSION['search'][$request->get->q]:null;
		return new FormPage('card/search', $obj);
	}
	
	public function add($request) {		
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		$userObj = unserialize($_SESSION['user']);

		$existCode = self::findCode($request->post->code);
		if (!is_null($existCode)) 
			return new Message('alert', L('error.promoCodeExists'));
		
		$addFields = ['code' => "?"];
		$addValues = [$request->post->code];

		if (isset($request->post->name) && !empty($request->post->name)) {
			$addFields['name'] = "?";
			$addValues[] = $request->post->name;
		}

		if (isset($request->post->amount) && !empty($request->post->amount)) {
			$addFields['amount'] = "?";
			$addValues[] = $request->post->amount;
		}

		if (isset($request->post->status) && !empty($request->post->status)) {
			$addFields['status'] = "?";
			$addValues[] = $request->post->status;
		}
		
		$sql = Sql::insert('promoCode')->setFieldValue($addFields);

		if ($sql->prepare()->execute($addValues)) {
			$id = db()->lastInsertId();
			return new Action('redirect', WebSystem::path(Route::getRouteByName('page.promoCodeList')->path(['pg'=>1]), false, false));
		} else {
			return new Message('alert', L('error.unableInsert'));
		}
	}
	
	public function edit($request) {	
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		$userObj = unserialize($_SESSION['user']);
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Message('alert', L('error.promoEmptyID'));	

		if (!isset($request->post->code) || empty($request->post->code)) 
			return new Message('alert', L('error.promoEmptyCode'));

		$existCode = self::findCode($request->post->code, \PDO::FETCH_OBJ, $request->get->id);
		if (!is_null($existCode)) 
			return new Message('alert', L('error.promoCodeExists'));

		$editFields = [];
		$editValues = [];

		$obj = self::find($request->get->id);

		if (isset($request->post->name) && !empty($request->post->name)) {
			$editFields['name'] = "?";
			$editValues[] = $request->post->name;
		}

		if (isset($request->post->code) && !empty($request->post->code)) {
			$editFields['code'] = "?";
			$editValues[] = $request->post->code;
		}		

		if (isset($request->post->amount) && !empty($request->post->amount)) {
			$editFields['amount'] = "?";
			$editValues[] = $request->post->amount;
		}
		
		if (isset($request->post->status) && !empty($request->post->status)) {
			$editFields['status'] = "?";
			$editValues[] = $request->post->status;
		}	
		
/*		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
		} */
		
		if (count($editFields) == 0) return new Message('alert', L('error.nothingEdit'));
		
		$sql = Sql::update('promoCode')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute($editValues)) {
			return new Action('redirect', WebSystem::path(Route::getRouteByName('page.promoCodeList')->path(['pg'=>1]), false, false));
		} else {
			return new Message('alert', L('error.unableUpdate'));
		}
	}

	public function delete($request) {	
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
			
		if (!isset($request->get->id) || empty($request->get->id))
			return new Message('alert', L('error.promoEmptyID'));	
			
		$sql = Sql::delete('promoCode')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {
			return new Message('info', L('info.promoCodeDeleted'));
		} else {
			return new Message('alert', L('error.promoCodeDeleteFailed'));
		}
	}
}