<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem, Utility\Excel, Utility\Email; 
use Controller\formLayout;

class dbm implements Listable {
	private $stmStatus = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("dbm")->where(['id', '=', $id]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

	public function extraProcess($listObj) {

		if (is_null($this->stmStatus))
			$this->stmStatus = Sql::select('status')->where(['id', '=', "?"])->prepare();
			
		$this->stmStatus->execute([$listObj->status]);
		$objStatus = $this->stmStatus->fetch();
		$listObj->statusName = $objStatus['name'];
		
		return $listObj;
	}

    public function list($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false)); 

		$obj = null;
		return new FormPage('dbm/list', $obj);
	}

    public function delete($request) {	
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	

		$currentUserObj = unserialize($_SESSION['user']);			
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.dbmEmptyID')]);	

		$dbmObj = self::find($request->get->id);		
		
		if(is_null($dbmObj))
			return new Data(['success'=>false, 'message'=>L('error.dbmNotFound'), 'field'=>'notice']);					
			
		$sql = Sql::delete('dbm')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "DBM";
			$logData['referenceID'] = $request->get->id;
			$logData['action'] = "Delete";
			$logData['description'] = "Delete DBM [".$dbmObj->scheduleDate."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $request->get->id;
			$logData['changes'] = [];
			systemLog::add($logData);

			return new Data(['success'=>true, 'message'=>L('info.dbmDeleted')]);	
		} else {
			return new Data(['success'=>false, 'message'=>L('error.dbmDeleteFailed')]);	
		}					
	}    

    public function dbmForm($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

		$formName = "form-addDbm";

		if(!is_null($obj)) {
			$formName = "form-editDbm";
		}				

		$content = "<form id='".$formName."' class='' autocomplete='off'>";
		$content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.dbmAddHelperMessage')."</p></div>";

		$content .= "<div class='row'>";        
			$content .= formLayout::rowInputNew(L('dbm.scheduleDate'),'scheduleDate', 'scheduleDate', 'text',  6, ['customDateTime'], ['required'], is_null($obj)?'':$obj['scheduleDate']);
		
			if(!is_null($obj)) {
				$option = [];
				$stm = Sql::select('status')->prepare();
				$stm->execute();                                          
				foreach ($stm as $opt) {  
					$option[$opt['id']] = L($opt['name']);
				}
				$content .= formLayout::rowSelectNew(L('Status'), 'status', 'status', $option, 6, [], ['required'], is_null($obj)?'':$obj['status']);
			}

		$content .= "</div>";		
		$content .= "</form>";

		return new Data(['success'=>true, 'message'=>$content]);
		
	}
	
    public function add($request) {	
      
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);

        // form check
		if (!isset($request->post->scheduleDate) || empty($request->post->scheduleDate)) 
			return new Data(['success'=>false, 'message'=>L('error.dbmEmptyScheduleDate'), 'field'=>'scheduleDate']);

        // insert database
		$sql = Sql::insert('dbm')->setFieldValue([
            'scheduleDate' => "?",
            'createBy'=>$currentUserObj->id, 
            'modifyBy'=>$currentUserObj->id
        ]);

		$addValues = [
			strip_tags($request->post->scheduleDate),               
	 	];

		if ($sql->prepare()->execute($addValues)) {
			
            $id = db()->lastInsertId();

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "DBM";
			$logData['referenceID'] = $id;
			$logData['action'] = "Insert";
			$logData['description'] = "Create New DBM [".$request->post->scheduleDate."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $addValues;
			$logData['changes'] = 
				[
					[
						"key"=>"scheduleDate", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->scheduleDate)
					]
				];

			systemLog::add($logData);				

			return new Data(['success'=>true, 'message'=>L('info.saved')]);
			
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableInsert'), 'field'=>'notice']);
		}	

	}

    public function edit($request) {	
      
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);

		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.dbmEmptyID'), 'field'=>'notice']);

		$dbmObj = self::find($request->get->id);

		if(is_null($dbmObj))
			return new Data(['success'=>false, 'message'=>L('error.dbmNotFound'), 'field'=>'notice']);

        // form check
        if (!isset($request->post->scheduleDate) || empty($request->post->scheduleDate)) 
            return new Data(['success'=>false, 'message'=>L('error.dbmEmptyScheduleDate'), 'field'=>'scheduleDate']);

		$editFields = [];
		$editValues = [];
		$logContent = [];

		if (isset($request->post->scheduleDate) && !empty($request->post->scheduleDate)) {

			$editFields['scheduleDate'] = "?";
			$editValues[] = $request->post->scheduleDate;

			if($request->post->scheduleDate.":00"!=$dbmObj->scheduleDate) {
				$logContent[] = [
					"key"=>"scheduleDate", 
					"valueFrom"=>$dbmObj->scheduleDate, 
					"valueTo"=>strip_tags($request->post->scheduleDate)					
				];	
			}			
		}	

		if (isset($request->post->status) && !empty($request->post->status)) {

			$editFields['status'] = "?";
			$editValues[] = $request->post->status;

			if($request->post->status!=$dbmObj->status) {
				$logContent[] = [
					"key"=>"status", 
					"valueFrom"=>$dbmObj->status, 
					"valueTo"=>strip_tags($request->post->status)					
				];	
			}			
		}	  	
        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('dbm')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
			if (count($logContent)) {
				$logData = [];
				$logData['userID']= $currentUserObj->id;
				$logData['module'] = "DBM";
				$logData['referenceID'] = $request->get->id;
				$logData['action'] = "Update";
				$logData['description'] = "Edit DBM [".$dbmObj->scheduleDate."]";
				$logData['sqlStatement'] = $sql;
				$logData['sqlValue'] = $editValues;			
				$logData['changes'] = $logContent;
				systemLog::add($logData);
			}

			return new Data(['success'=>true, 'message'=>L('info.updated')]);			
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableUpdate'), 'field'=>'notice']);
		}		        
        
	}  

    public static function genTableHeader() {
        $htmlContent = "";

        $htmlContent .= "<thead>";
            $htmlContent .= "<tr>";
                $htmlContent .= "<th>".L('ID')."</th>";
                $htmlContent .= "<th>".L('dbm.scheduleDate')."</th>";
                $htmlContent .= "<th>".L('Status')."</th>";                             
                $htmlContent .= "<th>".L('Actions')."</th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</thead>";

        return $htmlContent;
    }

    public static function genTableFooter() {
        $htmlContent = "";

        $htmlContent .= "<tfoot>";
            $htmlContent .= "<tr>";
                $htmlContent .= "<th>".L('ID')."</th>";
                $htmlContent .= "<th>".L('dbm.scheduleDate')."</th>";
                $htmlContent .= "<th>".L('Status')."</th>";                             
                $htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }	

	public static function genTableContentData() {
		$sql = Sql::select(['dbm', 'dbm'])->leftJoin(['status', 'status'], "dbm.status = status.id");
		$sql->setFieldValue('
		   dbm.id id, 
		   dbm.scheduleDate scheduleDate, 
		   status.name statusName                         
		');
        $stm = $sql->prepare();
        $stm->execute();
        return $stm;
    }

	public static function genTableBodyRow($listObj) {
        $htmlContent = "";
        $htmlContent .= "<tr>";
            $htmlContent .= "<td>".$listObj['id']."</td>";
            $htmlContent .= "<td>".$listObj['scheduleDate']."</td>";
            $htmlContent .= "<td>".L($listObj['statusName'])."</td>";
            $htmlContent .= "<td>";
                $htmlContent .= "<div class='btn-group' role='group' aria-label=''>";
                    $htmlContent .= "<div class='btn-group' role='group'>";
                    $htmlContent .= "<button id='btnGroupDrop".$listObj['id']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>";
                    $htmlContent .= L('Actions');
                    $htmlContent .= "</button>";
                    $htmlContent .= "<ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$listObj['id']."'>";
                        $htmlContent .= "<li><div class='d-grid'>";
							$htmlContent .= "<button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$listObj['id']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>";
							$htmlContent .= "<button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$listObj['id']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>";
                            $htmlContent .= "<button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$listObj['id']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>";                           
                        $htmlContent .= "</div></li>";
                    $htmlContent .= "</ul>";
                    $htmlContent .= "</div>";
                $htmlContent .= "</div>";
            $htmlContent .= "</td>";
        $htmlContent .= "</tr>";

        return $htmlContent;
    }

	public static function getMeetingDateByMonth($request) {
		
		//$firstDateOfMonth = $request->get->month."-01";
		//$lastDateOfMonth = date('Y-m-t', strtotime($request->get->month.'-01'));

		//$userObj = unserialize($_SESSION['user']);

		$calendar = new \donatj\SimpleCalendar($request->get->month);
		$calendar->setWeekDayNames([ L('Sun'), L('Mon'), L('Tue'), L('Wed'), L('Thu'), L('Fri'), L('Sat') ]);
		$calendar->setStartOfWeek('Sunday');   
		
		$sqlAll = Sql::select('dbm')->where(['status', '=', 1]);
		$sqlAll->order('dbm.scheduleDate', 'asc');
		
		$stmAll = $sqlAll->prepare();
		$stmAll->execute();
		
		
		foreach($stmAll as $obj) {			
			$content = "<div class='d-grid gap-2'><button type='button' class='btn btn-info btn-md btnEdit' data-id='".$obj['id']."'>".$obj['scheduleDate']."</button></div>";
			$content = "<div class='d-grid gap-2'><button type='button' class='btn btn-info btn-md btnEdit' data-id='".$obj['id']."'>DBM Scheduled Date</button></div>";			
			$calendar->addDailyHtml($content, date('Y-m-d', strtotime($obj['scheduleDate'])));	
		}		

		return new Data(['success'=>true, 'message'=>$calendar->render()]);

	}
    
	public function detail($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

		$content = "<div class='row'>";     

			$content .= formLayout::rowDisplayLineNew(L('dbm.scheduleDate'), $obj['scheduleDate'], 6);
			$content .= formLayout::rowDisplayLineNew(L('Status'), L(status::find($obj['status'])->name), 6);

		$content .= "</div>";

		return new Data(['success'=>true, 'message'=>$content]);
		
	}	
}