<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem, Utility\Excel, Utility\Email; 
use Controller\promoCode, Controller\documentHelper, Controller\formLayout, Controller\template, Controller\payment;

class zoning implements Listable {
	private $stmStatus = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("zoning")->where(['id', '=', $id]);
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
		return new FormPage('zoning/list', $obj);
	}

    public function delete($request) {	
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	
		
		$currentUserObj = unserialize($_SESSION['user']);				

		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.zoningEmptyID')]);	

		$zoningObj = self::find($request->get->id);		
	
		if(is_null($zoningObj))
			return new Data(['success'=>false, 'message'=>L('error.zoningNotFound'), 'field'=>'notice']);					
			
		$sql = Sql::delete('zoning')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "zoning";
			$logData['referenceID'] = $request->get->id;
			$logData['action'] = "Delete";
			$logData['description'] = "Delete zoning [".$zoningObj->name."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $request->get->id;
			$logData['changes'] = [];
			systemLog::add($logData);

			return new Data(['success'=>true, 'message'=>L('info.zoningDeleted')]);	
		} else {
			return new Data(['success'=>false, 'message'=>L('error.zoningDeleteFailed')]);	
		}					
	}    

    public function zoningForm($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

		$formName = "form-addZoning";

		if(!is_null($obj)) {
			$formName = "form-editZoning";
		}				

		$content = "<form id='".$formName."' class='' autocomplete='off'>";
		$content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.zoningAddHelperMessage')."</p></div>";
		$content .= "<div class='row'>";   

        $content .= formLayout::rowInputNew(L('zoning.name'),'name', 'name', 'text',  6, [], ['required'], is_null($obj)?'':$obj['name']);
	   
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
			return new Data(['success'=>false, 'message'=>L('error.zoningEmptyName'), 'field'=>'name']);

        // insert database
		$sql = Sql::insert('zoning')->setFieldValue([
            'name' => "?",
            'createBy'=>$currentUserObj->id, 
            'modifyBy'=>$currentUserObj->id
        ]);

		$addValues = [
			strip_tags($request->post->name),               
	 	];

		if ($sql->prepare()->execute($addValues)) {
			
            $id = db()->lastInsertId();

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "Zoning";
			$logData['referenceID'] = $id;
			$logData['action'] = "Insert";
			$logData['description'] = "Create New Zoning [".$request->post->name."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $addValues;
			$logData['changes'] = 
				[
					[
						"key"=>"name", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->name)
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
			return new Data(['success'=>false, 'message'=>L('error.zoningEmptyID'), 'field'=>'notice']);

		$zoningObj = self::find($request->get->id);
		if(is_null($zoningObj))
			return new Data(['success'=>false, 'message'=>L('error.zoningNotFound'), 'field'=>'notice']);

        // form check
        if (!isset($request->post->name) || empty($request->post->name)) 
            return new Data(['success'=>false, 'message'=>L('error.zoningEmptyName'), 'field'=>'name']);

        $editFields = [];
		$editValues = [];
		$logContent = [] ;

		if (isset($request->post->name) && !empty($request->post->name)) {

			$editFields['name'] = "?";
			$editValues[] = $request->post->name;

			if($request->post->name!=$zoningObj->name) {
				$logContent[] = [
					"key"=>"name", 
					"valueFrom"=>$zoningObj->name, 
					"valueTo"=>strip_tags($request->post->name)					
				];	
			}			
		}	

		if (isset($request->post->status) && !empty($request->post->status)) {

			$editFields['status'] = "?";
			$editValues[] = $request->post->status;

			if($request->post->status!=$zoningObj->status) {
				$logContent[] = [
					"key"=>"status", 
					"valueFrom"=>$zoningObj->status, 
					"valueTo"=>strip_tags($request->post->status)					
				];	
			}			
		}	
        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('zoning')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
			if (count($logContent)) {
				$logData = [];
				$logData['userID']= $currentUserObj->id;
				$logData['module'] = "Zoning";
				$logData['referenceID'] = $request->get->id;
				$logData['action'] = "Update";
				$logData['description'] = "Edit Zoning [".$zoningObj->name."]";
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
				$htmlContent .= "<th>".L('zoning.name')."</th>";
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
                $htmlContent .= "<th>".L('zoning.name')."</th>";
                $htmlContent .= "<th>".L('Status')."</th>";                             
                $htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }

	public static function genTableContentData() {
		$sql = Sql::select(['zoning', 'zoning'])->leftJoin(['status', 'status'], "zoning.status = status.id");
		$sql->setFieldValue('
		   zoning.id id, 
		   zoning.name name, 
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
            $htmlContent .= "<td>".$listObj['name']."</td>";
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

	public function detail($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);
		
		$content = "<div class='row'>";

		   $content .= formLayout::rowDisplayLineNew(L('zoning.name'), $obj['name'], 6);
		   $content .= formLayout::rowDisplayLineNew(L('Status'), L(status::find($obj['status'])->name), 6);

		$content .= "</div>";


		return new Data(['success'=>true, 'message'=>$content]);
		
	}		
    
}