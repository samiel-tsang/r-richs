<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem;

class role implements Listable {
	private $stmStatus = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("role")->where(['id', '=', "?"]);
		$stm = $sql->prepare();
		$stm->execute([$id]);
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;

		return $obj;
	}

	public static function findByRoleIDNavItemID($roleID, $navItemID, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("rolePermission")->where(['roleID', '=', "?"])->where(['navItemID', '=', "?"]);
		$stm = $sql->prepare();
		$stm->execute([$roleID, $navItemID]);
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;

		return $obj;
	}

	public static function find_all($fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("role")->where(['status', '=', "'1'"]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetchAll($fetchMode);
		if ($obj === false) return null;

		return $obj;
	}	

	public static function findPermission($roleID, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("rolePermission")->where(['roleID', '=', $roleID]);
		$stm = $sql->prepare();
		$stm->execute();
		return $stm;
	}   
	
	public static function findNavItem($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("navItems")->where(['id', '=', "?"]);
		$stm = $sql->prepare();
		$stm->execute([$id]);
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
		return new FormPage('role/list', $obj);
	}	


    public function delete($request) {	
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	

		$currentUserObj = unserialize($_SESSION['user']);
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.roleEmptyID')]);	

		$roleObj = self::find($request->get->id);		
	
		if(is_null($roleObj))
			return new Data(['success'=>false, 'message'=>L('error.roleNotFound'), 'field'=>'notice']);					
			
		$sql = Sql::delete('role')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "Role";
			$logData['referenceID'] = $request->get->id;
			$logData['action'] = "Delete";
			$logData['description'] = "Delete Role [".$roleObj->name."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $request->get->id;
			$logData['changes'] = [];
			systemLog::add($logData);			

			return new Data(['success'=>true, 'message'=>L('info.roleDeleted')]);	
		} else {
			return new Data(['success'=>false, 'message'=>L('error.roleDeleteFailed')]);	
		}					
	}    

    public function roleForm($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) {
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);		
            $obj['navItemID'] = null;
            $navItemList = self::findPermission($request->get->id);
            foreach($navItemList as $navItemInfo) {
                $obj['navItemID'][] = $navItemInfo['navItemID'];
            }
		}			


		$formName = "form-addRole";

		if(!is_null($obj)) {
			$formName = "form-editRole";
		}				

		$content = "<form id='".$formName."' class='' autocomplete='off'>";
		$content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.roleAddHelperMessage')."</p></div>";

		$content .= "<div class='row'>";        
			$content .= formLayout::rowInputNew(L('role.name'),'name', 'name', 'text',  6, [], ['required'], is_null($obj)?'':$obj['name']);

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

		$content .= "<div class='row'>";   
			$option = [];
			$stm = Sql::select('navItems')->where(['pageName', '!=', '""'])->prepare();
			$stm->execute();                                          
			foreach ($stm as $opt) {  
				$option[$opt['id']] = L($opt['itemName']);
			}
			$content .= formLayout::rowMultiSelectNew(L('role.permission'), 'navItemID[]', 'navItemID', $option,  12, [], ['required'], empty($obj['navItemID'])?[]:$obj['navItemID']);  
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
			return new Data(['success'=>false, 'message'=>L('error.roleEmptyName'), 'field'=>'name']);

		if (!isset($request->post->navItemID) || empty($request->post->navItemID) || count($request->post->navItemID)==0) 
			return new Data(['success'=>false, 'message'=>L('error.roleEmptyPermission'), 'field'=>'navItemID']);  			

        // insert database
		$sql = Sql::insert('role')->setFieldValue([
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
			$logData['module'] = "Role";
			$logData['referenceID'] = $id;
			$logData['action'] = "Insert";
			$logData['description'] = "Create New Role [".$request->post->name."]";
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

            foreach($request->post->navItemID as $navItemID) {
                $sql = Sql::insert('rolePermission')->setFieldValue([
                    'roleID' => "?", 
                    'navItemID' => "?"
                ]);          

				$addNavItemValues = [
                    strip_tags($id),
                    strip_tags($navItemID)
                ];

                $sql->prepare()->execute($addNavItemValues);

				$permissionID = db()->lastInsertId();

				$logPermissionData = [];
				$logPermissionData['userID']= $currentUserObj->id;
				$logPermissionData['module'] = "Role";
				$logPermissionData['referenceID'] = $permissionID;
				$logPermissionData['action'] = "Insert";
				$logPermissionData['description'] = "Create New Role Permission [".$request->post->name."]";
				$logPermissionData['sqlStatement'] = $sql;
				$logPermissionData['sqlValue'] = $addNavItemValues;
				$logPermissionData['changes'] = 
					[
						[
							"key"=>"navItemID", 
							"valueFrom"=>"", 
							"valueTo"=>strip_tags($navItemID)
						]
					];
	
				systemLog::add($logPermissionData);


            }			

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
			return new Data(['success'=>false, 'message'=>L('error.roleEmptyID'), 'field'=>'notice']);

		$roleObj = self::find($request->get->id);
		if(is_null($roleObj))
			return new Data(['success'=>false, 'message'=>L('error.roleNotFound'), 'field'=>'notice']);

        // form check
        if (!isset($request->post->name) || empty($request->post->name)) 
            return new Data(['success'=>false, 'message'=>L('error.roleEmptyName'), 'field'=>'name']);

		if (!isset($request->post->navItemID) || empty($request->post->navItemID) || count($request->post->navItemID)==0) 
			return new Data(['success'=>false, 'message'=>L('error.roleEmptyPermission'), 'field'=>'navItemID']);  			

        $editFields = [];
		$editValues = [];
		$logContent = [];		


		if (isset($request->post->name) && !empty($request->post->name)) {

			$editFields['name'] = "?";
			$editValues[] = $request->post->name;

			if($request->post->name!=$roleObj->name) {
				$logContent[] = [
					"key"=>"name", 
					"valueFrom"=>$roleObj->name, 
					"valueTo"=>strip_tags($request->post->name)					
				];	
			}			
		}	

		if (isset($request->post->status) && !empty($request->post->status)) {

			$editFields['status'] = "?";
			$editValues[] = $request->post->status;

			if($request->post->status!=$roleObj->status) {
				$logContent[] = [
					"key"=>"status", 
					"valueFrom"=>$roleObj->status, 
					"valueTo"=>strip_tags($request->post->status)					
				];	
			}			
		}	 

		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('role')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {

			if (count($logContent)) {
				$logData = [];
				$logData['userID']= $currentUserObj->id;
				$logData['module'] = "Role";
				$logData['referenceID'] = $request->get->id;
				$logData['action'] = "Update";
				$logData['description'] = "Edit Role [".$roleObj->name."]";
				$logData['sqlStatement'] = $sql;
				$logData['sqlValue'] = $editValues;			
				$logData['changes'] = $logContent;
				systemLog::add($logData);	
			}		

            //$sql = Sql::delete('rolePermission')->where(['roleID', '=', $request->get->id]);
            //$sql->prepare()->execute();
			//print_r($request->post->navItemID);

			$currentRolePermission = [];
			foreach(self::findPermission($request->get->id) as $permissionObj){
				$currentRolePermission[] = $permissionObj['navItemID'];
			}

			//print_r($currentRolePermission);

			//check new permission
			$newPermission = array_diff($request->post->navItemID, $currentRolePermission);
            foreach($newPermission as $navItemID) {
				
                $sql = Sql::insert('rolePermission')->setFieldValue([
                    'roleID' => "?", 
                    'navItemID' => "?"
                ]);				
				
				$addNavItemValues = [
                    strip_tags($request->get->id),
                    strip_tags($navItemID)
                ];

                $sql->prepare()->execute($addNavItemValues);

				$permissionID = db()->lastInsertId();

				$logPermissionData = [];
				$logPermissionData['userID']= $currentUserObj->id;
				$logPermissionData['module'] = "Role";
				$logPermissionData['referenceID'] = $permissionID;
				$logPermissionData['action'] = "Insert";
				$logPermissionData['description'] = "Add Role Permission [".$request->post->name."]";
				$logPermissionData['sqlStatement'] = $sql;
				$logPermissionData['sqlValue'] = $addNavItemValues;
				$logPermissionData['changes'] = 
					[
						[
							"key"=>"navItemID", 
							"valueFrom"=>"", 
							"valueTo"=>strip_tags($navItemID)
						]
					];
	
				systemLog::add($logPermissionData);

            }

			//print_r($newPermission);

			$removePermission = array_diff($currentRolePermission, $request->post->navItemID);
			foreach($removePermission as $navItemID) {

				$permissionObj = self::findByRoleIDNavItemID($request->get->id, $navItemID);

				$sql = Sql::delete('rolePermission')->where(['id', '=', $permissionObj->id]);
				if ($sql->prepare()->execute()) {
		
					$logData = [];
					$logData['userID']= $currentUserObj->id;
					$logData['module'] = "Role";
					$logData['referenceID'] = $permissionObj->id;
					$logData['action'] = "Delete";
					$logData['description'] = "Delete Role Permission [".$request->post->name."]";
					$logData['sqlStatement'] = $sql;
					$logData['sqlValue'] = $request->get->id;
					$logData['changes'] = [];
					systemLog::add($logData);				
					
				} 				

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
				$htmlContent .= "<th>".L('role.name')."</th>";
				$htmlContent .= "<th>".L('role.permission')."</th>";
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
				$htmlContent .= "<th>".L('role.name')."</th>";
				$htmlContent .= "<th></th>";
				$htmlContent .= "<th>".L('Status')."</th>"; 
				$htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }

	public static function genTableContentData() {
		$sql = Sql::select(['role', 'role'])->leftJoin(['status', 'status'], "role.status = status.id")->leftJoin(['rolePermission', 'permission'], "role.id = permission.roleID")->leftJoin(['navItems', 'navItems'], "navItems.id = permission.navItemID");
        $sql->where(['1', 'GROUP BY', 'role.id']);
        $sql->setFieldValue('
            role.id roleID, 
            role.name roleName,  
            status.name statusName, 
            GROUP_CONCAT(" ", navItems.itemName) itemName                      
        ');
        $stm = $sql->prepare();
        $stm->execute();
        return $stm;
    }

	public static function genTableBodyRow($listObj) {
		
		$permissionList = self::findPermission($listObj['roleID']);
		$arrItemName = [];
		foreach($permissionList as $permission) {
			$arrItemName[] = self::findNavItem($permission['navItemID'])->itemName;
		}

        $htmlContent = "";
        $htmlContent .= "<tr>";
            $htmlContent .= "<td>".$listObj['roleID']."</td>";
			$htmlContent .= "<td>".$listObj['roleName']."</td>";
			$htmlContent .= "<td>".implode(", ", $arrItemName)."</td>";
            $htmlContent .= "<td>".L($listObj['statusName'])."</td>";    
			$htmlContent .= "<td>";
			$htmlContent .= "<div class='btn-group' role='group' aria-label=''>";
				$htmlContent .= "<div class='btn-group' role='group'>";
				$htmlContent .= "<button id='btnGroupDrop".$listObj['roleID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>";
				$htmlContent .= L('Actions');
				$htmlContent .= "</button>";
				$htmlContent .= "<ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$listObj['roleID']."'>";
					$htmlContent .= "<li><div class='d-grid'>";
						$htmlContent .= "<button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$listObj['roleID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>";
						$htmlContent .= "<button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$listObj['roleID']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>";
						$htmlContent .= "<button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$listObj['roleID']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>";						
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
		if (isset($request->get->id)) {
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);		
            $obj['navItemID'] = null;
            $navItemList = self::findPermission($request->get->id);
            foreach($navItemList as $navItemInfo) {
                $obj['navItemID'][] = $navItemInfo['navItemID'];
				$obj['navItemName'][] = L(navItems::find($navItemInfo['navItemID'])->itemName);
            }
		}			


		$content = "<div class='row'>";        

			$content .= formLayout::rowDisplayLineNew(L('role.name'), $obj['name'], 6);
			$content .= formLayout::rowDisplayLineNew(L('Status'), L(status::find($obj['status'])->name), 6);
			$content .= formLayout::rowDisplayLineNew(L('role.permission'), implode(", ",$obj['navItemName']), 12);

		$content .= "</div>";		


		return new Data(['success'=>true, 'message'=>$content]);
		
	}	
    /*
	public static function getTotalClientByType($roleID) {

		$sql = Sql::select("tpb")->where(['status', '=', "'1'"])->where(['roleID', '=', $roleID]);
		$stm = $sql->prepare();
		$stm->execute();

		return $stm->rowCount();
	}
        */

}