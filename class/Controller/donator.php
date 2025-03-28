<?php
namespace Controller;

use Responses\Message, Responses\Action;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem, Utility\Excel;

class donator implements Listable {
	private $stmTeam = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ, $includePW=false) {
		$sql = Sql::select("transaction")->where(['id', '=', $id]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		return $obj;
	}

	/* Page Function */
	public function list($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false)); 
		
		$sql = Sql::select('teamDonator')->where(['status', '=', 1]);
	
		if (isset($request->get->q)) {
			$hash = $request->get->q;
			if (!empty($_SESSION['search'][$hash]['sql_order_field']))
				$sql->order($_SESSION['search'][$hash]['sql_order_field'], (!empty($_SESSION['search'][$hash]['sql_order_seq']))?$_SESSION['search'][$hash]['sql_order_seq']:'ASC');
		}

		$listPage = new ListPage('donator/list', $sql);
		$listPage->setLister($this);
		return $listPage;
	}

	public function extraProcess($listObj) {
		if (is_null($this->stmTeam))
			$this->stmTeam = Sql::select('team')->where(['id', '=', "?"])->prepare();
			
		$this->stmTeam->execute([$listObj->teamID]);
		$objTeam = $this->stmTeam->fetch();
		$listObj->teamName = $objTeam['teamName'];

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
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		$userObj = unserialize($_SESSION['user']);

		$teamID = 0;
		// get team ID
		if ($userObj->roleID == 1) {
			if (!isset($request->post->teamID) || empty($request->post->teamID))
				return new Message('alert',  L('error.teamEmptyID'));
			$teamID = $request->post->teamID;
		} else {
			$teamObj = team::findByUserID($userObj->id);
			if (is_null($teamObj))
				return new Message('alert',  L('error.teamEmptyID'));
			$teamID = $teamObj->id;
		}

		if (!isset($request->post->donatorName) || empty($request->post->donatorName))
			return new Message('alert',  L('error.donatorEmptyName'));

		if (!isset($request->post->donatorAmount) || empty($request->post->donatorAmount) || !is_numeric($request->post->donatorAmount) || $request->post->donatorAmount < 0)
			return new Message('alert',  L('error.donatorAmountValid'));

		// check whether total donated amount + being added donoate amount is greater than team min. donate amount 
		$stmDonatorAmt = Sql::select('teamDonator')->setFieldValue('SUM(donatorAmount) donateAmt')->where(['teamID', '=', '?'])->where(['status', '=', 1])->prepare();
		$stmDonatorAmt->setFetchMode(\PDO::FETCH_OBJ);
		$stmDonatorAmt->execute([$teamID]);
		$objDonatorAmt = $stmDonatorAmt->fetch();

		$stmTxn = Sql::select('transaction')->setFieldValue('SUM(amount) paidAmt')->where(['teamID', '=', '?'])->where(['status', '=', 1])->prepare();
		$stmTxn->setFetchMode(\PDO::FETCH_OBJ);
		$stmTxn->execute([$teamID]);
		$objTxn = $stmTxn->fetch();

		$diffAmount =  ($objDonatorAmt->donateAmt - $objTxn->paidAmt + $teamObj->submissionFee) + $request->post->donatorAmount;		

		$addFields = ['teamID' => "?", 'donatorName' => "?", 'donatorAmount' => "?",
			'donatorRecvComm' => "?", 'donatorPermitPromo' => "?",
			'status' => "?"];
		$addValues = [$teamID, $request->post->donatorName, $request->post->donatorAmount,
			(isset($request->post->donatorRecvComm))?0:1, 
			(isset($request->post->donatorPermitPromo))?0:1, 
			$diffAmount>0?"2":"1"];

		$sql = Sql::insert('teamDonator')->setFieldValue($addFields);

		if ($sql->prepare()->execute($addValues)) {
			$id = db()->lastInsertId();

			$url = Route::getRouteByName('page.teamInfo')->path(['id'=>$teamID]);
			if ($userObj->roleID == 2) {
				//$url = Route::getRouteByName('page.dashboard')->path();
				
				$remark = "Donation Fee";
				$type = "donate";
				
				if($diffAmount>0){
					return new Action('redirect', payment::createOrder($teamID, $diffAmount, $remark, $type, $id));
				} else {
					$url = Route::getRouteByName('page.dashboard')->path();
				}
				
			}
			
			return new Action('redirect', WebSystem::path($url, false, false));
		} else {
			return new Message('alert', L('error.unableInsert'));
		}
	}
	
	public function edit($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		$userObj = unserialize($_SESSION['user']);

		if (!isset($request->post->donatorName) || empty($request->post->donatorName))
			return new Message('alert',  L('error.donatorEmptyName'));
		if (!isset($request->post->donatorAmount) || empty($request->post->donatorAmount) || 
			!is_numeric($request->post->donatorAmount) || $request->post->donatorAmount < 0)
			return new Message('alert',  L('error.donatorAmountValid'));
		
		$editFields = ['donatorName' => "?", 'donatorAmount' => "?"];
		$editValues = [$request->post->donatorName, $request->post->donatorAmount];		
		
/*		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
		}
		
		if (count($editFields) == 0) return new Message('alert', L('error.nothingEdit')); */
		
		$sql = Sql::update('teamDonator')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute($editValues)) {
			$url = Route::getRouteByName('page.teamInfo')->path(['id'=>$teamID]);
			if ($userObj->roleID == 2) 
				$url = Route::getRouteByName('page.dashboard')->path();
			
			return new Action('redirect', WebSystem::path($url, false, false));
		} else {
			return new Message('alert', L('error.unableUpdate'));
		}
	}

	public function export($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));

		$xls = new Excel("w");
		$xls->wsObj->setTitle("DonatorList");

		$headerStyle = [
			'font' => [ 'bold' =>true],
			'borders' => [ 'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] ],
			'alignment' => [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER ],
		];

		$headerArr = ['SystemID', 'TeamID', 'TeamName', 'DoantorName', 'DonatorAmount',
			'Receive Communication', 'Granted for Promotion'
				];
		$xls->setSheetHeader($headerArr, 0, $headerStyle);

		$sql = Sql::select('teamDonator')->where(['status', '=', 1]);
		$stm = $sql->prepare();
		$stm->setFetchMode(\PDO::FETCH_OBJ);
		$stm->execute();
		foreach ($stm as $row) {
			$obj = $this->extraProcess($row);

			$dataLine = [
	        	['value'=>$obj->id, 'type'=>'s'], 
	        	['value'=>$obj->teamID, 'type'=>'s'], 
	        	['value'=>$obj->teamName, 'type'=>'s'], 
	        	['value'=>$obj->donatorName, 'type'=>'s'], 
	        	['value'=>$obj->donatorAmount, 'type'=>'s'],
				['value'=>($obj->donatorRecvComm)?'TRUE':'FALSE', 'type'=>'s'], 
	        	['value'=>($obj->donatorPermitPromo)?'TRUE':'FALSE', 'type'=>'s'],
        		];
			$xls->writeRowData($dataLine);
		}

		$xls->downloadFile("donatorList");
		return null;
	}
}