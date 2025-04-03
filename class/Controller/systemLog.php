<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem, Utility\Excel, Utility\Email; 
use Controller\formLayout;

class systemLog implements Listable {
	private $userDisplayName = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("systemLog")->where(['id', '=', $id]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

	public function extraProcess($listObj) {
        
		if (is_null($this->userDisplayName))
			$this->userDisplayName = Sql::select('user')->where(['id', '=', "?"])->prepare();
			
		$this->userDisplayName->execute([$listObj->userID]);
		$objStatus = $this->userDisplayName->fetch();
		$listObj->userDisplayName = $objStatus['displayName'];
		

		return $listObj;
	}

    public function list($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false)); 

		$obj = null;
		return new FormPage('systemLog/list', $obj);
	}

    public function delete($request) {	
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.systemLogEmptyID')]);	
			
		$sql = Sql::delete('systemLog')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {
			return new Data(['success'=>true, 'message'=>L('info.systemLogDeleted')]);	
		} else {
			return new Data(['success'=>false, 'message'=>L('error.systemLogDeleteFailed')]);	
		}					
	}    

    public function systemLogForm($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

		$formName = "form-addSystemLog";

		if(!is_null($obj)) {
			$formName = "form-editSystemLog";
		}				

        $content = "";

        // no input form for system log
		
		return new Data(['success'=>true, 'message'=>$content]);
		
	}
	
    public static function add($data) {	
        
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

            // insert database
		$sql = Sql::insert('systemLog')->setFieldValue([
			'userID' => "?",
            'module' => "?",
			'referenceID' => "?",
			'action' => "?",
			'description' => "?",
			'sqlStatement' => "?",
			'sqlValue' => "?",
			'changes' => "?",
			'ip' => "?"
        ]);   
        
        if ($sql->prepare()->execute([
				strip_tags($data['userID']),      
                strip_tags($data['module']),  
				strip_tags($data['referenceID']),  
				strip_tags($data['action']),  
				strip_tags($data['description']),  
				strip_tags($data['sqlStatement']),   
				strip_tags(json_encode($data['sqlValue'])),    
				strip_tags(json_encode($data['changes'])),       
                strip_tags(WebSystem::get_client_ip())  
         ])) {			

            $id = db()->lastInsertId();

            //$sqlUpdate = Sql::update('systemLog')->setFieldValue(["sqlStatement"=>"?", "sqlValue"=>"?"])->where(['id', '=', $id]);
            //$sqlUpdate->prepare()->execute([strip_tags($sql),json_encode($data)]);

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
			return new Data(['success'=>false, 'message'=>L('error.systemLogEmptyID'), 'field'=>'notice']);

		$systemLogObj = self::find($request->get->id);
		if(is_null($systemLogObj))
			return new Data(['success'=>false, 'message'=>L('error.systemLogNotFound'), 'field'=>'notice']);

        // form check
        // system log cannot be edited
        
        
	}  
    
	public static function genTableHeader() {
        $htmlContent = "";

        $htmlContent .= "<thead>";
            $htmlContent .= "<tr>";
                $htmlContent .= "<th>".L('ID')."</th>";
				$htmlContent .= "<th>".L('systemLog.logTime')."</th>";
                $htmlContent .= "<th>".L('systemLog.user')."</th>";
                $htmlContent .= "<th>".L('systemLog.module')."</th>";
                $htmlContent .= "<th>".L('systemLog.action')."</th>";
                $htmlContent .= "<th>".L('systemLog.description')."</th>";
                $htmlContent .= "<th>".L('systemLog.IP')."</th>";                  
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
				$htmlContent .= "<th>".L('systemLog.logTime')."</th>";
                $htmlContent .= "<th>".L('systemLog.user')."</th>";
                $htmlContent .= "<th>".L('systemLog.module')."</th>";
                $htmlContent .= "<th>".L('systemLog.action')."</th>";
                $htmlContent .= "<th>".L('systemLog.description')."</th>";
                $htmlContent .= "<th>".L('systemLog.IP')."</th>";                 
                $htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }

	public static function genTableContentData() {
		$sql = Sql::select(['systemLog', 'systemLog'])->leftJoin(['user', 'user'], "user.id = systemLog.userID");
		$sql->setFieldValue('
		   systemLog.id id, 
		   systemLog.logTime logTime, 
		   systemLog.module module, 
           systemLog.action action, 
           systemLog.description description, 
           systemLog.IP IP, 
		   user.displayName userDisplayName                         
		');
        $stm = $sql->prepare();
        $stm->execute();
        return $stm;
    }

	public static function genTableBodyRow($listObj) {
        $htmlContent = "";
        $htmlContent .= "<tr>";
            $htmlContent .= "<td>".$listObj['id']."</td>";
			$htmlContent .= "<td>".$listObj['logTime']."</td>";
            $htmlContent .= "<td>".$listObj['userDisplayName']."</td>";
            $htmlContent .= "<td>".$listObj['module']."</td>";
            $htmlContent .= "<td>".$listObj['action']."</td>";
            $htmlContent .= "<td>".$listObj['description']."</td>";
            $htmlContent .= "<td>".$listObj['IP']."</td>";
            $htmlContent .= "<td>";
                $htmlContent .= "<div class='btn-group' role='group' aria-label=''>";
                    $htmlContent .= "<div class='btn-group' role='group'>";
                    $htmlContent .= "<button id='btnGroupDrop".$listObj['id']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>";
                    $htmlContent .= L('Actions');
                    $htmlContent .= "</button>";
                    $htmlContent .= "<ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$listObj['id']."'>";
                        $htmlContent .= "<li><div class='d-grid'>";
                            $htmlContent .= "<button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$listObj['id']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>";
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

		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

		$content = "<div class='row'>";     

            $content .= formLayout::rowInputNew(L('systemLog.logTime'),'', '', 'text',  6, [], ['readonly'], is_null($obj)?'':$obj['logTime']);
            $content .= formLayout::rowInputNew(L('systemLog.user'),'', '', 'text',  6, [], ['readonly'], is_null($obj)?'':user::find($obj['userID'])->displayName);
            $content .= formLayout::rowInputNew(L('systemLog.module'),'', '', 'text',  6, [], ['readonly'], is_null($obj)?'':$obj['module']);
            $content .= formLayout::rowInputNew(L('systemLog.referenceID'),'', '', 'text',  6, [], ['readonly'], is_null($obj)?'':$obj['referenceID']);
            $content .= formLayout::rowInputNew(L('systemLog.action'),'', '', 'text',  6, [], ['readonly'], is_null($obj)?'':$obj['action']);
            $content .= formLayout::rowInputNew(L('systemLog.description'),'', '', 'text',  6, [], ['readonly'], is_null($obj)?'':$obj['description']);
            $content .= formLayout::rowTextAreaNew(L('systemLog.sqlStatement'), '', '',  12, [], ['readonly'], is_null($obj)?'':$obj['sqlStatement']);
            $content .= formLayout::rowTextAreaNew(L('systemLog.sqlValue'), '', '',  12, [], ['readonly'], is_null($obj)?'':$obj['sqlValue']);

            $changesArr = json_decode($obj['changes'], true) ;

			$changeDisplay = "";
			foreach($changesArr as $changesStr) {
            	$changeDisplay .= $changesStr['key'].": [".$changesStr['valueFrom']."] => [".$changesStr['valueTo']."]\r\n";
			}

            $content .= formLayout::rowTextAreaNew(L('systemLog.changes'), '', '',  12, [], ['readonly'], is_null($obj)?'':$changeDisplay);
            $content .= formLayout::rowInputNew(L('systemLog.IP'),'', '', 'text',  6, [], ['readonly'], is_null($obj)?'':$obj['ip']);

		$content .= "</div>";

		return new Data(['success'=>true, 'message'=>$content]);
		
	}	
}