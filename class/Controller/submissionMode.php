<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem;

class submissionMode implements Listable {
	private $stmStatus = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("submissionMode")->where(['id', '=', "?"]);
		$stm = $sql->prepare();
		$stm->execute([$id]);
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;

		return $obj;
	}

	public static function find_all($fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("submissionMode")->where(['status', '=', "'1'"]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetchAll($fetchMode);
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
		return new FormPage('submissionMode/list', $obj);
	}	


    public function delete($request) {	
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.submissionModeEmptyID')]);	
			
		$sql = Sql::delete('submissionMode')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {
			return new Data(['success'=>true, 'message'=>L('info.submissionModeDeleted')]);	
		} else {
			return new Data(['success'=>false, 'message'=>L('error.submissionModeDeleteFailed')]);	
		}					
	}    

    public function submissionModeForm($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

		$formName = "form-addSubmissionMode";

		if(!is_null($obj)) {
			$formName = "form-editSubmissionMode";
		}				

		$content = "<form id='".$formName."' class='' autocomplete='off'>";
		$content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.SubmissionModeAddHelperMessage')."</p></div>";

		$content .= "<div class='row'>";        
			$content .= formLayout::rowInputNew(L('submissionMode.name'),'name', 'name', 'text',  6, [], ['required'], is_null($obj)?'':$obj['name']);
		
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
		if (!isset($request->post->name) || empty($request->post->name)) 
			return new Data(['success'=>false, 'message'=>L('error.submissionModeEmptyName'), 'field'=>'name']);

        // insert database
		$sql = Sql::insert('submissionMode')->setFieldValue([
            'name' => "?",
            'createBy'=>$currentUserObj->id, 
            'modifyBy'=>$currentUserObj->id
        ]);

		if ($sql->prepare()->execute([
                strip_tags($request->post->name),               
         ])) {
			
            $id = db()->lastInsertId();

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
			return new Data(['success'=>false, 'message'=>L('error.submissionModeEmptyID'), 'field'=>'notice']);

		$submissionModeObj = self::find($request->get->id);
		if(is_null($submissionModeObj))
			return new Data(['success'=>false, 'message'=>L('error.submissionModeNotFound'), 'field'=>'notice']);

        // form check
        if (!isset($request->post->name) || empty($request->post->name)) 
            return new Data(['success'=>false, 'message'=>L('error.submissionModeEmptyName'), 'field'=>'name']);

        $editFields = [];
		$editValues = [];

		if (isset($request->post->name) && !empty($request->post->name)) {
			$editFields['name'] = "?";
			$editValues[] = $request->post->name;
		}		

		if (isset($request->post->status) && !empty($request->post->status)) {
			$editFields['status'] = "?";
			$editValues[] = $request->post->status;
		}	  
        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('submissionMode')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
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
				$htmlContent .= "<th>".L('submissionMode.name')."</th>";
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
				$htmlContent .= "<th>".L('submissionMode.name')."</th>";
				$htmlContent .= "<th>".L('Status')."</th>"; 
				$htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }

	public static function genTableContentData() {
		$sql = Sql::select(['submissionMode', 'submissionMode'])->leftJoin(['status', 'status'], "submissionMode.status = status.id");
		$sql->setFieldValue('
		   submissionMode.id id, 
		   submissionMode.name submissionModeName,
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
			$htmlContent .= "<td>".$listObj['submissionModeName']."</td>";
            $htmlContent .= "<td>".L($listObj['statusName'])."</td>";  
			$htmlContent .= "<td>";
				$htmlContent .= "<div class='btn-group' role='group' aria-label=''>";
					$htmlContent .= "<div class='btn-group' role='group'>";
					$htmlContent .= "<button id='btnGroupDrop".$listObj['id']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>";
					$htmlContent .= L('Actions');
					$htmlContent .= "</button>";
					$htmlContent .= "<ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$listObj['id']."'>";
						$htmlContent .= "<li><div class='d-grid'>";
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

    public function detail($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

		$content = "<div class='row'>";     

			$content .= formLayout::rowDisplayLineNew(L('submissionMode.name'), $obj['name'], 6);
			$content .= formLayout::rowDisplayLineNew(L('Status'), L(status::find($obj['status'])->name), 6);

		$content .= "</div>";

		return new Data(['success'=>true, 'message'=>$content]);
		
	}		

    /*
	public static function getTotalClientByType($submissionModeID) {

		$sql = Sql::select("tpb")->where(['status', '=', "'1'"])->where(['submissionModeID', '=', $submissionModeID]);
		$stm = $sql->prepare();
		$stm->execute();

		return $stm->rowCount();
	}
        */

}