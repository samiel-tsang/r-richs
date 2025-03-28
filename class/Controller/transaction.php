<?php
namespace Controller;

use Responses\Message, Responses\Action;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem;

class transaction implements Listable {
	private $stmTeam = null;
	private $stmStatus = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ, $includePW=false) {
		$sql = Sql::select("transaction")->where(['id', '=', $id]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

	public static function findByOrderID($id, $fetchMode=\PDO::FETCH_OBJ, $includePW=false) {
		$sql = Sql::select("transaction")->where(['txnOrder', '=', '?'])->order('createDate', 'DESC');
		$stm = $sql->prepare();
		$stm->execute([$id]);
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

	/* Page Function */
	public function list($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false)); 
		
		$sql = Sql::select('transaction');
	
		if (isset($request->get->q)) {
			$hash = $request->get->q;
			if (!empty($_SESSION['search'][$hash]['sql_order_field']))
				$sql->order($_SESSION['search'][$hash]['sql_order_field'], (!empty($_SESSION['search'][$hash]['sql_order_seq']))?$_SESSION['search'][$hash]['sql_order_seq']:'ASC');
		}

		$listPage = new ListPage('transaction/list', $sql);
		$listPage->setLister($this);
		return $listPage;
	}

	public function extraProcess($listObj) {
		if (is_null($this->stmTeam))
			$this->stmTeam = Sql::select('team')->where(['id', '=', "?"])->prepare();
			
		$this->stmTeam->execute([$listObj->teamID]);
		$objTeam = $this->stmTeam->fetch();
		$listObj->teamName = $objTeam['teamName'];

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
		
		return new FormPage('card/form', $obj);
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
		return; // no use
		
		
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		$userObj = unserialize($_SESSION['user']);

		if ($userObj->roleID != 1) return new Message('alert', L('error.unauthenticated'));
		
		$addFields = ['cardUrl' => "?"];
		$addValues = [$request->post->cardUrl];

		if (isset($request->post->userID) && !empty($request->post->userID)) {
			$addFields['userID'] = "?";
			$addValues[] = $request->post->userID;
		}

		if (isset($request->post->cardName) && !empty($request->post->cardName)) {
			$addFields['cardName'] = "?";
			$addValues[] = $request->post->cardName;
		}

		if (isset($request->post->cardUrl) && !empty($request->post->cardUrl)) {
			if (filter_var($request->post->cardUrl, FILTER_VALIDATE_URL) === FALSE)
				return new Message('alert', L('error.cardUrlInvalid'));

			$addFields['cardUrl'] = "?";
			$addValues[] = $request->post->cardUrl;
		}
		
		$sql = Sql::insert('card')->setFieldValue($addFields);

		if ($sql->prepare()->execute($addValues)) {
			$id = db()->lastInsertId();

			$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]".
				WebSystem::path(Route::getRouteByName('api.cardUrl')->path(['id'=>$id]), false, false);
			$firebaseUrl = "";

			$firebase = new firebase;
			try {
				$firebaseUrl = $firebase->createDynamicLink($url);
			} catch (FailedToCreateDynamicLink $e) {

				$sql = Sql::delete('card')->where(['id', '=', $id]);
				$sql->prepare()->execute();
				return new Message('alert', L('error.cardShortLinkCreateFailed'));
			}
			if (!empty($firebaseUrl)) {
				file_put_contents("firebaseUrl.txt", "$id:$firebaseUrl".PHP_EOL, FILE_APPEND);
				$sql = Sql::update('card')->setFieldValue(['firebaseUrl'=>'?'])->where(['id', '=', $id]);
				$sql->prepare()->execute([$firebaseUrl]);
			}
			if (isset($request->post->userID) && !empty($request->post->userID))
				return new Action('redirect', WebSystem::path(Route::getRouteByName('page.userInfo')->path(['id'=>$request->post->userID]), false, false));
			else
				return new Action('redirect', WebSystem::path(Route::getRouteByName('page.dashboard')->path(), false, false));
		} else {
			return new Message('alert', L('error.unableInsert'));
		}
	}
	
	public function edit($request) {
		return; // no use
	
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		$userObj = unserialize($_SESSION['user']);
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Message('alert', L('error.cardEmptyID'));	

		if (!isset($request->post->cardUrl) || empty($request->post->cardUrl)) 
			return new Message('alert', L('error.cardEmptyUrl'));

		if (filter_var($request->post->cardUrl, FILTER_VALIDATE_URL) === FALSE)
			return new Message('alert', L('error.cardUrlInvalid'));

		$editFields = [];
		$editValues = [];

		$obj = self::find($request->get->id);

		if (isset($request->post->cardName) && !empty($request->post->cardName)) {
			$editFields['cardName'] = "?";
			$editValues[] = $request->post->cardName;
		}
		
		if (isset($request->post->cardUrl) && !empty($request->post->cardUrl)) {

			$editFields['cardUrl'] = "?";
			$editValues[] = $request->post->cardUrl;
		}				
		
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
		}
		
		if (count($editFields) == 0) return new Message('alert', L('error.nothingEdit'));
		
		$sql = Sql::update('card')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute($editValues)) {
			$url = WebSystem::path(Route::getRouteByName('page.userInfo')->path(['id'=>$obj->userID]), false, false);

			if ($userObj->roleID == 2) $url = WebSystem::path(Route::getRouteByName('page.dashboard')->path(), false, false);

			return new Action('redirect', $url);
		} else {
			return new Message('alert', L('error.unableUpdate'));
		}
	}
}