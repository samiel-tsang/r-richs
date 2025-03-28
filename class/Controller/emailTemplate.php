<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql;
use Pages\FormPage;
use Routing\Route;
use Utility\WebSystem;
use Utility\Email; // Temporary for test Email

class emailTemplate {

	public static function find($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("emailTemplate")->where(['id', '=', $id]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

	public static function findName($name, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("emailTemplate")->where(['name', '=', "?"]);
		$stm = $sql->prepare();
		$stm->execute([$name]);
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

	public function variableList($request) {	
		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	

		$sql = Sql::select("emailTemplateVariable");
		$stm = $sql->prepare();
		$stm->execute();		
		$arr_variable = array();

		$content = "<table class='table table table-bordered'>";
			$content .= "<thead>";
				$content .= "<tr>";					
					$content .= "<th>".L('emailTemplate.emailVarDesc')."</th>";
					$content .= "<th>".L("emailTemplate.emailVariable")."</th>";
				$content .= "</tr>";
			$content .= "</thead>";
			$content .= "<body>";
		foreach($stm as $field){
			$content .= "<tr>";				
				$content .= "<td>".$field['description']."</td>";
				$content .= "<td>{{".$field['name']."}}</td>";
			$content .= "</tr>";
		}		
			$content .= "</body>";
		$content .= "</table>";

		$displayContent = "<div class='row'>";
			$displayContent .= formLayout::rowDisplayClearLineNew($content,12);
		$displayContent .= "</div>";
		
		return new Data(['success'=>true, 'message'=>$displayContent]);
	}

	public static function replaceVar($content, array $var = []) {
		//$tpl = $template->content;
		foreach ($var as $name => $value) {
			$content = str_replace('{{'.$name.'}}', $value, $content);
		}
		return $content;
	}
	
	public function form($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));

		$sqlTemp = Sql::select("emailTemplate")->where(['status', '=', 1]);
		$stmTemp = $sqlTemp->prepare();
		$stmTemp->execute();

		$obj = self::findName($request->get->template, \PDO::FETCH_NAMED);
		$stm = null;
		if (!is_null($obj)) {
			$sql = Sql::select("emailTemplateVariable")->where(['templateID', '=', "?"]);
			$stm = $sql->prepare();
			$stm->execute([$obj['id']]);
		}
		return new FormPage('template/form', $obj, ['stmTemp' => $stmTemp, 'stmVar' => $stm]);
	}

	public function emailform($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));

		return new FormPage('emailTemplate/emailform', null);
	}	
	
	public function sendEmail($request) {


		return null;
	}

	public function list($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false)); 

		$obj = null;
		return new FormPage('emailTemplate/list', $obj);
	}

    public static function genTableHeader() {
        $htmlContent = "";
        $htmlContent .= "<thead>";
            $htmlContent .= "<tr>";
                $htmlContent .= "<th>".L('ID')."</th>";
                $htmlContent .= "<th>".L('emailTemplate.name')."</th>";
                $htmlContent .= "<th>".L('emailTemplate.emailSubject')."</th>";
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
				$htmlContent .= "<th>".L('emailTemplate.name')."</th>";
				$htmlContent .= "<th>".L('emailTemplate.emailSubject')."</th>";
				$htmlContent .= "<th>".L('Status')."</th>";   
                $htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }

    public static function genTableContentData($tpbStatusID=0) {
        $sql = Sql::select(['emailTemplate', 'emailTemplate'])->leftJoin(['status', 'status'], "emailTemplate.status = status.id");
        
        //$sql->where(['1', 'GROUP BY', 'tpb.id']);
        $sql->setFieldValue('
            emailTemplate.id id, 
            emailTemplate.name emailTemplateName, 
			emailTemplate.subject emailTemplateSubject, 
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
            $htmlContent .= "<td>".$listObj['emailTemplateName']."</td>";
            $htmlContent .= "<td>".$listObj['emailTemplateSubject']."</td>";                                   
            $htmlContent .= "<td>";
                $htmlContent .= "<div class='btn-group' role='group' aria-label=''>";
                    $htmlContent .= "<div class='btn-group' role='group'>";
                    $htmlContent .= "<button id='btnGroupDrop".$listObj['id']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>";
                    $htmlContent .= L('Actions');
                    $htmlContent .= "</button>";
                    $htmlContent .= "<ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$listObj['id']."'>";
                        $htmlContent .= "<li><div class='d-grid'>";
                        	$htmlContent .= "<button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$listObj['id']."'><i class='fas fa-sm fa-view'></i> ".L('View')."</button>";    
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

	public function delete($request) {	
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.emailTemplateEmptyID')]);	
			
		$sql = Sql::delete('emailTemplate')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {
			return new Data(['success'=>true, 'message'=>L('info.emailTemplateDeleted')]);	
		} else {
			return new Data(['success'=>false, 'message'=>L('error.emailTemplateDeleteFailed')]);	
		}					
	}   
	
	public function emailTemplateForm($request) {

        if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

		$formName = "form-addEmailTemplate";

		if(!is_null($obj)) {
			$formName = "form-editEmailTemplate";
		}			

		$content = "<form id='".$formName."' class='' autocomplete='off'>";
		$content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.clientAddHelperMessage')."</p></div>";
		$content .= "<div class='row' id='variableList'><i class='fa fa-spinner fa-spin'></i>";
		$content .= "</div>";
        $content .= "<div class='row'>";
                        
			$content .= formLayout::rowInputNew(L('emailTemplate.name'),'name', 'name', 'text',  6, [], ['required'], is_null($obj)?'':$obj['name']);           
            $content .= formLayout::rowInputNew(L('emailTemplate.emailSubject'),'subject', 'subject', 'text',  12, [], ['required'], is_null($obj)?'':$obj['subject']);
			$content .= formLayout::rowTextAreaNew(L('emailTemplate.emailContent'), 'content', 'content',  12, ['tinymce'], [' rows="10"'], is_null($obj)?'':$obj['content']);

            if(!is_null($obj)) {
                $option = [];
                $stm = Sql::select('status')->prepare();
                $stm->execute();                                          
                foreach ($stm as $opt) {  
                    $option[$opt['id']] = L($opt['name']);
                }
                $content .= formLayout::rowSelectNew(L('Status'), 'status', 'status', $option, 6, [], [], is_null($obj)?'':$obj['status']);
            }
		$content .= "</div>";
		$content .= "</form>";

		return new Data(['success'=>true, 'message'=>$content, 'emailTemplateID'=>is_null($obj)?'1':$obj['emailTemplateID']]);
    }

	public function add($request) {

        if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);	
		
		if (!isset($request->post->name) || empty($request->post->name))
			return new Data(['success'=>false, 'message'=>L('error.emailTemplateEmptyName'), 'field'=>'name']);

		if (!isset($request->post->subject) || empty($request->post->subject))
			return new Data(['success'=>false, 'message'=>L('error.emailTemplateEmptySubject'), 'field'=>'subject']);
		
		/*
		if (!isset($request->post->content) || empty($request->post->content)) {
			return new Data(['success'=>false, 'message'=>L('error.emailTemplateEmptyName'), 'field'=>'content']);
        }
		*/	

		$addFields = ['name'=>"?", 'subject'=>"?", 'content'=>"?", 'status'=>"?", 'createBy'=>$currentUserObj->id, 'modifyBy'=>$currentUserObj->id];
		$addValues = [
			strip_tags($request->post->name), 
			strip_tags($request->post->subject), 
			$request->post->content, 
			strip_tags(1)
		];

		$sql = Sql::insert('emailTemplate')->setFieldValue($addFields);
		if ($sql->prepare()->execute($addValues)) {
			$id = db()->lastInsertId();

			return new Data(['success'=>true, 'message'=>L('info.saved'), 'id'=>$id, 'name'=>$request->post->name]);
			
		} else {
			return new Data(['success'=>true, 'message'=>L('error.unableInsert')]);
		}	
		
	}

	public function edit($request) {
        if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);

		if (!isset($request->post->name) || empty($request->post->name))
			return new Data(['success'=>false, 'message'=>L('error.emailTemplateEmptyName'), 'field'=>'name']);

		if (!isset($request->post->subject) || empty($request->post->subject))
			return new Data(['success'=>false, 'message'=>L('error.emailTemplateEmptySubject'), 'field'=>'subject']);
		
		/*
		if (!isset($request->post->content) || empty($request->post->content)) {
			return new Data(['success'=>false, 'message'=>L('error.emailTemplateEmptyName'), 'field'=>'content']);
        }
		*/	

		$editFields = ['subject' => "?", 'content' => "?", 'modifyDate' => 'NOW()', 'modifyBy' => $currentUserObj->id];
		$editValues = [strip_tags($request->post->subject), $request->post->content];

		$sql = Sql::update('emailTemplate')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute($editValues)) {
			return new Data(['success'=>true, 'message'=>L('info.saved'), 'id'=>$request->get->id, 'name'=>$request->post->name]);
		} else {
			return new Data(['success'=>true, 'message'=>L('error.unableUpdate')]);
		}
	}	

	public function detail($request) {

        if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

		$content = "<div class='row' id='variableList'><i class='fa fa-spinner fa-spin'></i></div>";
        $content .= "<div class='row'>";                       
		$content .= formLayout::rowDisplayLineNew(L('emailTemplate.name'), $obj['name'], 6);
		$content .= formLayout::rowDisplayLineNew(L('emailTemplate.emailSubject'), $obj['subject'], 6);
		$content .= formLayout::rowTextAreaNew(L('emailTemplate.emailContent'), 'content', 'content',  12, ['tinymce'], [' rows="10"', 'readonly'], $obj['content']);
		$content .= formLayout::rowDisplayLineNew(L('Status'), L(status::find($obj['status'])->name), 6);
		$content .= "</div>";




		return new Data(['success'=>true, 'message'=>$content]);
    }	

}