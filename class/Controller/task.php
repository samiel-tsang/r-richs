<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem, Utility\Excel, Utility\Email; 
use Controller\formLayout;

class task implements Listable {
	private $stmStatus = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("task")->where(['id', '=', $id]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

    public static function findAll($condition="", $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("task");

        if(!empty($condition)) {
            $sql->where(['status', '=', $condition]);
        }

		$stm = $sql->prepare();
		$stm->execute();
		return $stm;
	}  

    public static function findByConditionID($conditionID="", $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("task");

        if(!empty($conditionID)) {
            $sql->where(['conditionID', '=', $conditionID]);
        }

        $sql->where(['status', '=', 1]);       

		$stm = $sql->prepare();
		$stm->execute();
		return $stm;
	}  


	public function extraProcess($listObj) {

		if (is_null($this->stmStatus))
			$this->stmStatus = Sql::select('conditionStatus')->where(['id', '=', "?"])->prepare();
			
		$this->stmStatus->execute([$listObj->status]);
		$objStatus = $this->stmStatus->fetch();
		$listObj->statusName = $objStatus['name'];
		
		return $listObj;
	}

    public function list($request) {
		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	

		$obj = null;
		return new FormPage('task/list', $obj);
	}

    public function delete($request) {	
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	

		$currentUserObj = unserialize($_SESSION['user']);			
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.taskEmptyID')]);	

		$taskObj = self::find($request->get->id);		

		if(is_null($taskObj))
			return new Data(['success'=>false, 'message'=>L('error.taskNotFound'), 'field'=>'notice']);				
			
		$sql = Sql::delete('task')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "Task";
			$logData['referenceID'] = $request->get->id;
			$logData['action'] = "Delete";
			$logData['description'] = "Delete Task [".$taskObj->description."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $request->get->id;
			$logData['changes'] = [];
			systemLog::add($logData);	

			return new Data(['success'=>true, 'message'=>L('info.taskDeleted')]);	
		} else {
			return new Data(['success'=>false, 'message'=>L('error.taskmDeleteFailed')]);	
		}					
	}    

    public function taskForm($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

		$formName = "form-addTask";

		if(!is_null($obj)) {
			$formName = "form-editTask";
		}				

		$content = "<form id='".$formName."' class='' autocomplete='off'>";
		$content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.taskAddHelperMessage')."</p></div>";

		$content .= "<div class='row'>";        

            $option = [""=>""];
            if($currentUserObj->roleID!=1){
                $stm = Sql::select('user')->where(['status', '=', 1])->where(['id', '=', $currentUserObj->id])->prepare();
            } else {
                $stm = Sql::select('user')->where(['status', '=', 1])->prepare();
            }

            $stm->execute();                                          
            foreach ($stm as $opt) {  
                $option[$opt['id']] = $opt['displayName'];			  
            }

            $content .= formLayout::rowSelectNew(L('tpb.officer'), 'userID', 'userID', $option,  6, [], ['required'], is_null($obj)?'':$obj['userID']); 

            $content .= formLayout::rowInputNew(L('task.deadline'),'deadline', 'deadline', 'text',  6, ['customDateTime'], ['required'], is_null($obj)?'':$obj['deadline']);
			$content .= formLayout::rowInputNew(L('task.description'),'description', 'description', 'text',  12, [], [], is_null($obj)?'':$obj['description']);

            $option = [""=>""];
                    
            $sql = Sql::select(['tpb', 'tpb'])->leftJoin(['tpbOfficer', 'tpbOfficer'], "tpb.id = tpbOfficer.tpbID");
            $sql->where(['tpb.TPBNo', '!=', '""']);

            if($currentUserObj->roleID!=1){
                $sql->where(['tpbOfficer.userID', '=', $currentUserObj->id]);
            }
            
            $sql->setFieldValue('
                tpb.id id, 
                tpbOfficer.userID userID,  
                tpb.TPBNo TPBNo
            ');

            $stm = $sql->prepare();

            $stm->execute();                                          
            foreach ($stm as $opt) {                    
                $option[$opt['id']] = $opt['TPBNo'];			  
            }

            $content .= formLayout::rowSelectNew(L('tpb.number'), 'tpbID', 'tpbID', $option,  6, [], [], is_null($obj)?'':$obj['tpbID']); 

            $option = [""=>""];
            $sql = Sql::select(['tpbCondition', 'conditions'])->leftJoin(['tpb', 'tpb'], "tpb.id = conditions.tpbID")
            ->leftJoin(['tpbOfficer', 'tpbOfficer'], "tpb.id = tpbOfficer.tpbID");

            if($currentUserObj->roleID!=1){
                $sql->where(['tpbOfficer.userID', '=', $currentUserObj->id]);
            }
            
            $sql->setFieldValue('
                conditions.id id, 
                conditions.conditionNo conditionNo,  
                conditions.description description
            ');

            $stm = $sql->prepare();
            $stm->execute();
            foreach ($stm as $opt) {

                $option[$opt['id']] = $opt['description'];			  
            }
           

            $content .= formLayout::rowSelectNew(L('tpb.condition'), 'conditionID', 'conditionID', $option,  6, [], [], is_null($obj)?'':$obj['conditionID']);             

			if(!is_null($obj)) {
				$option = [];
				$stm = Sql::select('conditionStatus')->prepare();
				$stm->execute();                                          
				foreach ($stm as $opt) {  
					$option[$opt['id']] = $opt['name'];
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
		if (!isset($request->post->userID) || empty($request->post->userID)) 
			return new Data(['success'=>false, 'message'=>L('error.taskEmptyOfficer'), 'field'=>'userID']);

        if (!isset($request->post->deadline) || empty($request->post->deadline)) 
			return new Data(['success'=>false, 'message'=>L('error.taskEmptyDeadline'), 'field'=>'deadline']);            

        /*
        if (!isset($request->post->description) || empty($request->post->description)) 
			return new Data(['success'=>false, 'message'=>L('error.taskEmptyDescription'), 'field'=>'description']);                   
        */

        // insert database
		$sql = Sql::insert('task')->setFieldValue([
            'userID' => "?",
            'tpbID' => "?",
            'conditionID' => "?",
            'description' => "?",
            'deadline' => "?",
            'status' => "?",
            'createBy'=>$currentUserObj->id, 
            'modifyBy'=>$currentUserObj->id
        ]);

		$addValues = [
			strip_tags($request->post->userID),     
			strip_tags($request->post->tpbID),    
			strip_tags($request->post->conditionID),    
			strip_tags($request->post->description),    
			strip_tags($request->post->deadline),    
			strip_tags(1)           
		];


		if ($sql->prepare()->execute($addValues)) {
			
            $id = db()->lastInsertId();

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "Task";
			$logData['referenceID'] = $id;
			$logData['action'] = "Insert";
			$logData['description'] = "Create New Task [".$request->post->description."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $addValues;
			$logData['changes'] = 
				[
					[
						"key"=>"userID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->userID)
					],[
						"key"=>"tpbID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->tpbID)
					],[
						"key"=>"conditionID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->conditionID)
					],[
						"key"=>"description", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->description)
					],[
						"key"=>"deadline", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->deadline)
					],[
						"key"=>"status", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags(1)
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
			return new Data(['success'=>false, 'message'=>L('error.taskEmptyID'), 'field'=>'notice']);

		$taskObj = self::find($request->get->id);
		if(is_null($taskObj))
			return new Data(['success'=>false, 'message'=>L('error.taskNotFound'), 'field'=>'notice']);

            // form check
		if (!isset($request->post->userID) || empty($request->post->userID)) 
			return new Data(['success'=>false, 'message'=>L('error.taskEmptyOfficer'), 'field'=>'userID']);

        if (!isset($request->post->deadline) || empty($request->post->deadline)) 
			return new Data(['success'=>false, 'message'=>L('error.taskEmptyDeadline'), 'field'=>'deadline']);            

        /*
        if (!isset($request->post->description) || empty($request->post->description)) 
			return new Data(['success'=>false, 'message'=>L('error.taskEmptyDescription'), 'field'=>'description']);                   
        */

        $editFields = [];
		$editValues = [];
		$logContent = [];


		if (isset($request->post->userID) && !empty($request->post->userID)) {

			$editFields['userID'] = "?";
			$editValues[] = $request->post->userID;

			if($request->post->userID!=$taskObj->userID) {
				$logContent[] = [
					"key"=>"userID", 
					"valueFrom"=>$taskObj->userID, 
					"valueTo"=>strip_tags($request->post->userID)					
				];	
			}			
		}	

		if (isset($request->post->description) && !empty($request->post->description)) {

			$editFields['description'] = "?";
			$editValues[] = $request->post->description;

			if($request->post->description!=$taskObj->description) {
				$logContent[] = [
					"key"=>"description", 
					"valueFrom"=>$taskObj->description, 
					"valueTo"=>strip_tags($request->post->description)					
				];	
			}			
		}			

		if (isset($request->post->deadline) && !empty($request->post->deadline)) {

			$editFields['deadline'] = "?";
			$editValues[] = $request->post->deadline;

			if($request->post->deadline.":00"!=$taskObj->deadline) {
				$logContent[] = [
					"key"=>"deadline", 
					"valueFrom"=>$taskObj->deadline, 
					"valueTo"=>strip_tags($request->post->deadline)					
				];	
			}			
		}	

		if (isset($request->post->tpbID) && !empty($request->post->tpbID)) {

			$editFields['tpbID'] = "?";
			$editValues[] = $request->post->tpbID;

			if($request->post->tpbID!=$taskObj->tpbID) {
				$logContent[] = [
					"key"=>"tpbID", 
					"valueFrom"=>$taskObj->tpbID, 
					"valueTo"=>strip_tags($request->post->tpbID)					
				];	
			}			
		}	       

		if (isset($request->post->conditionID) && !empty($request->post->conditionID)) {

			$editFields['conditionID'] = "?";
			$editValues[] = $request->post->conditionID;

			if($request->post->conditionID!=$taskObj->conditionID) {
				$logContent[] = [
					"key"=>"conditionID", 
					"valueFrom"=>$taskObj->conditionID, 
					"valueTo"=>strip_tags($request->post->conditionID)					
				];	
			}			
		}	    

		if (isset($request->post->status) && !empty($request->post->status)) {

			$editFields['status'] = "?";
			$editValues[] = $request->post->status;

			if($request->post->status!=$taskObj->status) {
				$logContent[] = [
					"key"=>"status", 
					"valueFrom"=>$taskObj->status, 
					"valueTo"=>strip_tags($request->post->status)					
				];	
			}			
		}	               

        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('task')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {

			if (count($logContent)) {
				$logData = [];
				$logData['userID']= $currentUserObj->id;
				$logData['module'] = "Task";
				$logData['referenceID'] = $request->get->id;
				$logData['action'] = "Update";
				$logData['description'] = "Edit Task [".$taskObj->description."]";
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

    public function done($request) {	
      
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);

		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.taskEmptyID'), 'field'=>'notice']);

		$taskObj = self::find($request->get->id);
		if(is_null($taskObj))
			return new Data(['success'=>false, 'message'=>L('error.taskNotFound'), 'field'=>'notice']);

        $editFields = [];
		$editValues = [];
		$logContent = [];

		$editFields['status'] = "?";
		$editValues[] = 2;		

		$logContent[] = [
			"key"=>"status", 
			"valueFrom"=>$taskObj->status, 
			"valueTo"=>strip_tags(2)					
		];	
        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('task')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
						
			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "Task";
			$logData['referenceID'] = $request->get->id;
			$logData['action'] = "Update";
			$logData['description'] = "Complete Task [".$taskObj->description."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $editValues;			
			$logData['changes'] = $logContent;
			systemLog::add($logData);			

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
                $htmlContent .= "<th>".L('task.officer')."</th>";
                $htmlContent .= "<th>".L('tpb.number')."</th>";
                $htmlContent .= "<th>".L('tpb.conditionNo')."</th>";
                $htmlContent .= "<th>".L('task.description')."</th>";
                $htmlContent .= "<th>".L('task.deadline')."</th>";
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
                $htmlContent .= "<th>".L('task.officer')."</th>";
                $htmlContent .= "<th>".L('tpb.number')."</th>";
                $htmlContent .= "<th>".L('tpb.conditionNo')."</th>";
                $htmlContent .= "<th>".L('task.description')."</th>";
                $htmlContent .= "<th>".L('task.deadline')."</th>";
                $htmlContent .= "<th>".L('Status')."</th>";                              
                $htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }	

	public static function genTableContentData() {
		$sql = Sql::select(['task', 'task'])->leftJoin(['conditionStatus', 'status'], "task.status = status.id")
        ->leftJoin(['tpbCondition', 'conditions'], "task.conditionID = conditions.id")
        ->leftJoin(['tpb', 'tpb'], "task.tpbID = tpb.id")
        ->leftJoin(['user', 'user'], "task.userID = user.id");

		$sql->setFieldValue('
		   task.id id, 
           user.displayName officer,
           task.deadline deadline, 
           task.description description, 
           conditions.conditionNo conditionNo,
           tpb.TPBNo tpbNumber,
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
            $htmlContent .= "<td>".$listObj['officer']."</td>";
            $htmlContent .= "<td>".$listObj['tpbNumber']."</td>";
            $htmlContent .= "<td>".$listObj['conditionNo']."</td>";
            $htmlContent .= "<td>".$listObj['description']."</td>";
            $htmlContent .= "<td>".$listObj['deadline']."</td>";
            $htmlContent .= "<td>".$listObj['statusName']."</td>";
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
			$calendar->addDailyHtml($content, date('Y-m-d', strtotime($obj['scheduleDate'])));	
		}		

		return new Data(['success'=>true, 'message'=>$calendar->render()]);

	}

    public static function createTask($data) {

		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);        

        $sql = Sql::insert('task')->setFieldValue([
            'userID' => "?",
            'tpbID' => "?",
            'conditionID' => "?",
            'description' => "?",
            'deadline' => "?",
            'status' => "?",
            'createBy'=>$currentUserObj->id, 
            'modifyBy'=>$currentUserObj->id
        ]);

		$addValues = [
			strip_tags($data['userID']),     
			strip_tags($data['tpbID']),    
			strip_tags($data['conditionID']),    
			strip_tags($data['description']),    
			strip_tags($data['deadline']),    
			strip_tags(1)           
		];


		if ($sql->prepare()->execute($addValues)) {
			
            $id = db()->lastInsertId();

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "Task";
			$logData['referenceID'] = $id;
			$logData['action'] = "Insert";
			$logData['description'] = "Auto Create New Task [".$data['description']."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $addValues;
			$logData['changes'] = 
				[
					[
						"key"=>"userID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($data['userID'])
					],[
						"key"=>"tpbID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($data['tpbID'])
					],[
						"key"=>"conditionID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($data['conditionID'])
					],[
						"key"=>"description", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($data['description'])
					],[
						"key"=>"deadline", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($data['deadline'])
					],[
						"key"=>"status", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags(1)
					]
				];

			systemLog::add($logData);


			return true;
			
		} else {
			return false;
		}	

    }

    public function detail($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

		
		$content = "<div class='row'>";    
        
        
            $content .= formLayout::rowDisplayLineNew(L('tpb.officer'), user::find($obj['userID'])->displayName??"", 6);
            $content .= formLayout::rowDisplayLineNew(L('task.deadline'), $obj['deadline']??"", 6);

            $content .= formLayout::rowDisplayLineNew(L('task.description'), $obj['description']??"", 12);

            $content .= formLayout::rowDisplayLineNew(L('tpb.number'), tpb::find($obj['tpbID'])->TPBNo??"", 6);  
            
            $content .= formLayout::rowDisplayLineNew(L('tpb.conditionNo'), tpb::getConditionDetail($obj['conditionID'])->conditionNo??"", 6); 
		
            $content .= formLayout::rowDisplayLineNew(L('Status'), generalStatus::find($obj['status'])->name??"", 6);

			

		$content .= "</div>";		

		return new Data(['success'=>true, 'message'=>$content]);
		
	}    
    
}