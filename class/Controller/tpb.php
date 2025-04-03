<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem, Utility\Excel, Utility\Email; 
use Controller\documentHelper, Controller\formLayout, Controller\decision, Controller\tpbStatus;

class tpb implements Listable {
	private $stmStatus = null;
    private $stmClient = null;
    private $stmDecision = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("tpb")->where(['id', '=', $id]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

    public static function findByTPBIDUserID($tpbID, $userID, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("tpbOfficer")->where(['tpbID', '=', $tpbID])->where(['userID', '=', $userID]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

    public static function findByTPBIDZoningID($tpbID, $zoningID, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("tpbZoning")->where(['tpbID', '=', $tpbID])->where(['zoningID', '=', $zoningID]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

    public static function findAll($condition="", $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("tpb");

        if(!empty($condition)) {
            $sql->where(['tpbStatusID', '=', $condition]);
        }
        
		$stm = $sql->prepare();
		$stm->execute();
		return $stm;
	}    

	public static function findOfficer($tpbID, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("tpbOfficer")->where(['tpbID', '=', $tpbID]);        
		$stm = $sql->prepare();
		$stm->execute();
		return $stm;
	}   
    
    public static function findAllByOfficerID($userID="", $fetchMode=\PDO::FETCH_OBJ) {
        $sql = Sql::select(['tpb', 'tpb'])->leftJoin(['tpbOfficer', 'tpbOfficer'], "tpb.id = tpbOfficer.tpbID");

        $sql->where(['tpbOfficer.userID', '=', $userID]);
        
        $sql->setFieldValue('
            tpb.id id, 
            tpbOfficer.userID userID,  
            tpb.TPBNo TPBNo
        ');

		$stm = $sql->prepare();
		$stm->execute();
		return $stm;
	}  

	public static function findZoning($tpbID, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("tpbZoning")->where(['tpbID', '=', $tpbID]);
		$stm = $sql->prepare();
		$stm->execute();
		return $stm;
	}     
    
	public static function findCondition($tpbID, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("tpbCondition")->where(['tpbID', '=', $tpbID]);
		$stm = $sql->prepare();
		$stm->execute();
		return $stm;
	}     

	public static function getEOTDetail($eotID, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("tpbEOT")->where(['id', '=', $eotID]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

	public static function getConditionDetail($conditionID, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("tpbCondition")->where(['id', '=', $conditionID]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}    

	public function extraProcess($listObj) {

		if (is_null($this->stmClient))
			$this->stmClient = Sql::select('client')->where(['id', '=', "?"])->prepare();
			
		$this->stmClient->execute([$listObj->clientID]);
		$objClient = $this->stmClient->fetch();
		$listObj->client = $objClient['contactPerson'];

		if (is_null($this->stmDecision))
			$this->stmDecision = Sql::select('tpbDecision')->where(['id', '=', "?"])->prepare();
			
		$this->stmDecision->execute([$listObj->decisionID]);
		$objDecision = $this->stmDecision->fetch();
		$listObj->decision = $objDecision['name'];

		if (is_null($this->stmStatus))
			$this->stmStatus = Sql::select('tpbStatus')->where(['id', '=', "?"])->prepare();
			
		$this->stmStatus->execute([$listObj->tpbStatusID]);
		$objStatus = $this->stmStatus->fetch();
		$listObj->statusName = $objStatus['name'];
		
		return $listObj;
	}

    public function list($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false)); 

		$obj = null;
		return new FormPage('tpb/list', $obj);
	}

    public function delete($request) {	
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	

        $currentUserObj = unserialize($_SESSION['user']);              
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyID')]);	

        $tpbObj = self::find($request->get->id);	

        if(is_null($tpbObj))
            return new Data(['success'=>false, 'message'=>L('error.tpbNotFound'), 'field'=>'notice']);	             
			
		$sql = Sql::delete('tpb')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "TPB";
			$logData['referenceID'] = $request->get->id;
			$logData['action'] = "Delete";
			$logData['description'] = "Delete TPB [ID: ".$tpbObj->id."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $request->get->id;
			$logData['changes'] = [];
			systemLog::add($logData);	

			return new Data(['success'=>true, 'message'=>L('info.tpbDeleted')]);	
		} else {
			return new Data(['success'=>false, 'message'=>L('error.tpbDeleteFailed')]);	
		}					
	}    

    public function eotDelete($request) {	
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	

        $currentUserObj = unserialize($_SESSION['user']);               
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.eotEmptyID')]);	

        $eotObj = self::getEOTDetail($request->get->id);	

        if(is_null($eotObj))
            return new Data(['success'=>false, 'message'=>L('error.eotNotFound'), 'field'=>'notice']);	                     
        
		$sql = Sql::delete('tpbEOT')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "EOT";
			$logData['referenceID'] = $request->get->id;
			$logData['action'] = "Delete";
			$logData['description'] = "Delete EOT [ID: ".$eotObj->id."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $request->get->id;
			$logData['changes'] = [];
			systemLog::add($logData);	

			return new Data(['success'=>true, 'message'=>L('info.eotDeleted')]);	
		} else {
			return new Data(['success'=>false, 'message'=>L('error.eotDeleteFailed')]);	
		}					
	}   

    public function conditionDelete($request) {	
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	

        $currentUserObj = unserialize($_SESSION['user']);
            
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.conditionEmptyID')]);	

        $conditionObj = self::getConditionDetail($request->get->id);	

        if(is_null($conditionObj))
            return new Data(['success'=>false, 'message'=>L('error.conditionNotFound'), 'field'=>'notice']);	             

			
		$sql = Sql::delete('tpbCondition')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "Condition";
			$logData['referenceID'] = $request->get->id;
			$logData['action'] = "Delete";
			$logData['description'] = "Delete Condition [ID: ".$conditionObj->description."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $request->get->id;
			$logData['changes'] = [];
			systemLog::add($logData);	

			return new Data(['success'=>true, 'message'=>L('info.conditionDeleted')]);	
		} else {
			return new Data(['success'=>false, 'message'=>L('error.conditionDeleteFailed')]);	
		}					
	}      

    public static function updateTBPStatus($tpbID, $tpbStatusID) {

        $currentUserObj = unserialize($_SESSION['user']);          

        $sql = Sql::update('tpb')->setFieldValue(['tpbStatusID'=>$tpbStatusID])->where(['id', '=', $tpbID]);

        $tpbObj = self::find($tpbID);

		if ($sql->prepare()->execute()) {    
            
			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "TPB";
			$logData['referenceID'] = $tpbID;
			$logData['action'] = "Update";
			$logData['description'] = "Update TPB [ID: ".$tpbID."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $tpbStatusID;
			$logData['changes'] = 				[
                [
                    "key"=>"tpbStatusID", 
                    "valueFrom"=>$tpbObj->tpbStatusID, 
                    "valueTo"=>strip_tags($tpbStatusID)
                ]
            ];
			systemLog::add($logData);	

            return true;
        }

        return false;

    }

    // tpb add form 
    public function tpbFormAdd($request) {

        if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$formName = "form-addTpb";
        $content = "";        
        $content .= "<form id='".$formName."' class='' autocomplete='off'>";

            $content .= "<div class='row'>";
                $content .= "<div class='col-md-12'>";
                    $content .= "<div class='card'>";

                        $content .= "<div class='card-header'>";
                            $content .= "<div class='d-flex align-items-center'>";
                                $content .= "<h4 class='card-title' id='notice'>".L('info.tpbAddHelperMessage')."</h4>";
                            $content .= "</div>";
                        $content .= "</div>";

                        $content .= "<div class='card-body'>";

                            $content .= "<ul class='nav nav-pills nav-secondary' id='pills-tab-tpbAdd' role='tablist'>";
                                $content .= "<li class='nav-item submenu tpbAddMenu' role='presentation'>";
                                    $content .= "<a class='tpbAddMenuA nav-link active' id='pills-applicant-tab' data-bs-toggle='pill' href='#pills-applicant' role='tab' aria-controls='pills-applicant' aria-selected='false' tabindex='-1'>".L('tpb.applicantDetail')."</a>";
                                $content .= "</li>";

                                $content .= "<li class='nav-item submenu tpbAddMenu' role='presentation'>";
                                    $content .= "<a class='tpbAddMenuA nav-link' id='pills-application-tab' data-bs-toggle='pill' href='#pills-application' role='tab' aria-controls='pills-application' aria-selected='false' tabindex='-1'>".L('tpb.applicationDetail')."</a>";
                                $content .= "</li>";

                                $content .= "<li class='nav-item submenu tpbAddMenu' role='presentation'>";
                                    $content .= "<a class='tpbAddMenuA nav-link' id='pills-submission-tab' data-bs-toggle='pill' href='#pills-submission' role='tab' aria-controls='pills-submission' aria-selected='false' tabindex='-1'>".L('tpb.submissionDetail')."</a>";
                                $content .= "</li>";

                                $content .= "<li class='nav-item submenu tpbAddMenu' role='presentation'>";
                                    $content .= "<a class='tpbAddMenuA nav-link' id='pills-receive-tab' data-bs-toggle='pill' href='#pills-receive' role='tab' aria-controls='pills-receive' aria-selected='false' tabindex='-1'>".L('tpb.receiveDetail')."</a>";
                                $content .= "</li>";
                                
                                $content .= "<li class='nav-item submenu tpbAddMenu' role='presentation'>";
                                    $content .= "<a class='tpbAddMenuA nav-link' id='pills-decision-tab' data-bs-toggle='pill' href='#pills-decision' role='tab' aria-controls='pills-decision' aria-selected='false' tabindex='-1'>".L('tpb.decisionDetail')."</a>";
                                $content .= "</li>";
                                
                                $content .= "<li class='nav-item submenu tpbAddMenu' role='presentation'>";
                                    $content .= "<a class='tpbAddMenuA nav-link' id='pills-condition-tab' data-bs-toggle='pill' href='#pills-condition' role='tab' aria-controls='pills-condition' aria-selected='false' tabindex='-1'>".L('tpb.conditionDetail')."</a>";
                                $content .= "</li>";
                                
                                $content .= "<li class='nav-item submenu tpbAddMenu' role='presentation'>";
                                    $content .= "<a class='tpbAddMenuA nav-link' id='pills-EOT-tab' data-bs-toggle='pill' href='#pills-EOT' role='tab' aria-controls='pills-EOT' aria-selected='false' tabindex='-1'>".L('tpb.EOTDetail')."</a>";
                                $content .= "</li>";   

                                $content .= "<li class='nav-item submenu tpbAddMenu' role='presentation'>";
                                    $content .= "<a class='tpbAddMenuA nav-link' id='pills-STT-tab' data-bs-toggle='pill' href='#pills-STT' role='tab' aria-controls='pills-STT' aria-selected='false' tabindex='-1'>".L('tpb.STTDetail')."</a>";
                                $content .= "</li>";  
                                
                                $content .= "<li class='nav-item submenu tpbAddMenu' role='presentation'>";
                                    $content .= "<a class='tpbAddMenuA nav-link' id='pills-STW-tab' data-bs-toggle='pill' href='#pills-STW' role='tab' aria-controls='pills-STW' aria-selected='false' tabindex='-1'>".L('tpb.STWDetail')."</a>";
                                $content .= "</li>";                                  

                            $content .= "</ul>";
                            
                            $content .= "<div class='tab-content mt-2 mb-3' id='pills-tabContent-tpbAdd'>";

                                // applicant tab
                                $content .= "<div class='tab-pane tpbAddMenuDiv fade show active' id='pills-applicant' role='tabpanel' aria-labelledby='pills-applicant-tab'>";
                                
                                    $content .= "<div class='row'>";

                                        $option = [""=>"", "Add"=>"[".L('Add')."]"];
                                        $stm = Sql::select('client')->where(['status', '=', 1])->prepare();
                                        $stm->execute();                                          
                                        foreach ($stm as $opt) {  
                                            $option[$opt['id']] = $opt['contactPerson'];			  
                                        }
                                        
                                        $content .= formLayout::rowSelectNew(L('tpb.client'), 'clientID', 'clientID', $option,  6, ['clientSelect'], ['required']);  

                                    $content .= "</div>";

                                    $content .= "<div class='row' id='tpbSelectedClentDetail'>";
                                        
                                    $content .= "</div>";
                                $content .= "</div>";

                                // application tab
                                $content .= "<div class='tab-pane tpbAddMenuDiv fade' id='pills-application' role='tabpanel' aria-labelledby='pills-application-tab'>";                                
                                    $content .= "<div class='row'>";
                                        $content .= formLayout::rowInputNew(L('tpb.refNo'),'refNo', 'refNo', 'text',  6, [], []);

                                        $option = [];
                                        $stm = Sql::select('user')->where(['status', '=', 1])->prepare();
                                        $stm->execute();                                          
                                        foreach ($stm as $opt) {  
                                            $option[$opt['id']] = $opt['displayName'];			  
                                        }

                                        
                                        $content .= formLayout::rowMultiSelectNew(L('tpb.officer'), 'userID[]', 'userID', $option,  6, ['select2'], ['required']); 
                                    
                                        $content .= formLayout::rowInputNew(L('tpb.addressDDLot'),'addressDDLot', 'addressDDLot', 'text',  12, [], []);
                                        $content .= formLayout::rowInputNew(L('tpb.ozpName'),'OZPName', 'OZPName', 'text',  6, [], []);
                                        $content .= formLayout::rowInputNew(L('tpb.ozpNo'),'OZPNo', 'OZPNo', 'text',  6, [], []);
                                
                                        
                                        $option = [];
                                        $stm = Sql::select('zoning')->where(['status', '=', 1])->prepare();
                                        $stm->execute();                                          
                                        foreach ($stm as $opt) {  
                                             $option[$opt['id']] = $opt['name'];			  
                                        }
                                        $content .= formLayout::rowMultiSelectNew(L('tpb.zoning'), 'zoningID[]', 'zoningID', $option,  6, ['select2'], []);             
                                        
                                        $content .= formLayout::rowInputNew(L('tpb.proposedUse'),'proposedUse', 'proposedUse', 'text',  6, [], []);

                                        $content .= formLayout::rowInputNew(L('tpb.authorizationLetter'),'authorizationLetterDoc', 'authorizationLetterDoc', 'file',  6, [], ['accept="image/*, application/pdf"']);
                                    $content .= "</div>";

                                    $content .= "<div class='row'>";
                                        $option = ["Y"=>L('Y'), "N"=>L('N')];
                                        $content .= formLayout::rowRadioNew(L('tpb.isLandOwner'), 'isLandOwner', 'isLandOwner', $option,  12, ['isLandOwnerSelect'], [], "Y");
                                    $content .= "</div>";

                                    $content .= "<div class='row landOwnerSection'>";
                                        $content .= formLayout::rowInputNew(L('tpb.landRegistry'),'landRegistryDoc', 'landRegistryDoc', 'file',  6, [], ['accept="image/*, application/pdf"']);
                                    $content .= "</div>";

                                    $content .= "<div class='row notLandOwnerSection'>";
                                        $content .= formLayout::rowInputNew(L('tpb.siteNotice'),'siteNoticeDoc', 'siteNoticeDoc', 'file',  6, [], ['accept="image/*, application/pdf"']);
                                    $content .= "</div>";
                                    
                                    $content .= "<div class='row notLandOwnerSection'>";                                        
                                        $content .= formLayout::rowInputNew(L('tpb.letterToRC'),'letterToRCDoc', 'letterToRCDoc', 'file',  6, [], ['accept="image/*, application/pdf"']);
                                    $content .= "</div>";

                                    $content .= "<div class='row'>";
                                        $content .= formLayout::rowTextAreaNew(L('tpb.remarks'), 'remarks', 'remarks',  12, [], []);
                                    $content .= "</div>";

                                    $content .= "<div class='row'>";
                                        $content .= "<div class='col-md-12 col-lg-12  text-end'>";
                                            $content .= "<button type='button' class='btn btn-md btn-success' id='next_pills-submission'>".L('Next')."</button>";
                                        $content .= "</div>";
                                    $content .= "</div>";                                        

                                $content .= "</div>";

                                // submission tab
                                $content .= "<div class='tab-pane tpbAddMenuDiv fade' id='pills-submission' role='tabpanel' aria-labelledby='pills-submission-tab'>";
                                    
                                    $content .= "<div class='row'>";
                                        $content .= formLayout::rowInputNew(L('tpb.submissionDate'),'submissionDate', 'submissionDate', 'text',  6, ['customDate'], []);
                                        $option = [];
                                        $stm = Sql::select('submissionMode')->where(['status', '=', 1])->prepare();
                                        $stm->execute();                                          
                                        foreach ($stm as $opt) {  
                                            $option[$opt['id']] = $opt['name'];			  
                                        }
                                        $content .= formLayout::rowRadioNew(L('tpb.submissionMode'), 'submissionModeID', 'submissionModeID', $option,  6, ['submissionModeSelect'], [], "1");
                                    $content .= "</div>";

                                    $content .= "<div class='row'>";
                                        $content .= formLayout::rowInputNew(L('tpb.submissionDocument'),'submissionDoc', 'submissionDoc', 'file',  6, [], ['accept="image/*, application/pdf"']);                                        
                                    $content .= "</div>";  

                                    $content .= "<div class='row'>";
                                        $option = [""=>""];
                                        $stm = Sql::select('rntpc')->where(['status', '=', 1])->prepare();
                                        $stm->execute();                                          
                                        foreach ($stm as $opt) {  
                                            $option[$opt['id']] = $opt['meetingDate'];			  
                                        }                                    
                                        $content .= formLayout::rowSelectNew(L('tpb.rntpc'), 'rntpcID', 'rntpcID', $option,  6, [], []);    
                                    $content .= "</div>";                                        

                                $content .= "</div>";

                                // received tab
                                $content .= "<div class='tab-pane tpbAddMenuDiv fade' id='pills-receive' role='tabpanel' aria-labelledby='pills-receive-tab'>";
                                   
                                    $content .= "<div class='row'>";
                                        $content .= formLayout::rowInputNew(L('tpb.number'),'TPBNo', 'TPBNo', 'text',  6, [], []);
                                        $content .= formLayout::rowInputNew(L('tpb.website'),'TPBWebsite', 'TPBWebsite', 'text',  6, [], []);                                    
                                        $content .= formLayout::rowInputNew(L('tpb.receiveDate'),'TPBReceiveDate', 'TPBReceiveDate', 'text',  6, ['customDate'], []);
                                        $content .= formLayout::rowInputNew(L('tpb.considerationDate'),'tentativeConsiderationDate', 'tentativeConsiderationDate', 'text',  6, ['customDate'], []);
                                    $content .= "</div>";             

                                $content .= "</div>";      
                                
                                // decision tab
                                $content .= "<div class='tab-pane tpbAddMenuDiv fade' id='pills-decision' role='tabpanel' aria-labelledby='pills-decision-tab'>";
                                    $content .= "<div class='row'>";
                                    
                                    $option = [""=>""];
                                    $stm = Sql::select('decision')->prepare();
                                    $stm->execute();                                          
                                    foreach ($stm as $opt) {  
                                        $option[$opt['id']] = $opt['name'];
                                    }
                                    $content .= formLayout::rowSelectNew(L('tpb.decision'), 'decisionID', 'decisionID', $option, 6, [], []);                                
                            
                                    $content .= formLayout::rowInputNew(L('tpb.decisionDate'),'decisionDate', 'decisionDate', 'text',  6, ['customDate'], []);
                                    $content .= formLayout::rowInputNew(L('tpb.approvalValidUntil'),'approvalValidUntil', 'approvalValidUntil', 'text',  6, ['customDate'], []);

                                        $content .= '<div id="conditionArea">';
                                            $content .= '<div class="row mb-0">';
                                                $content .= '<div class="col-md-6 col-lg-6 mt-3"><label for="" class="" style="margin-left:-5px;">'.L('tpb.condition').' & '.L('Deadline').'*</label></div>';
                                                $content .= '<div class="col-md-6 col-lg-6 text-end"><label for="" class=""><button type="button" class="btn btn-sm btn-primary btn-round" id="addConditionRow"><i class="fas fa-plus"></i></button></label></div>';
                                            $content .= '</div>';
                                            $row=0;
                                            $content .= '<div class="col-md-12 col-lg-12 conditionRow" id="conditionRow_'.$row.'">';
                                                $content .= '<div class="form-group">';                   
                                                        $content .= '<div class="input-group">';
                                                            $content .= '<input type="hidden" class="form-control" placeholder="Line Status" id="lineStatus_'.$row.'" name="lineStatus[]" value="0" >';
                                                            $content .= '<input type="text" class="form-control w-20" placeholder="Number" id="number_'.$row.'" name="number[]" value="">';
                                                            $content .= '<input type="text" class="form-control w-50" placeholder="Description" id="description_'.$row.'" name="description[]" value="">';
                                                            $content .= '<input type="text" class="form-control customDateTime w-20" placeholder="Deadline" id="deadline_'.$row.'" name="deadline[]" value="">';
                                                            $content .= '<button type="button" class="btn btn-sm btn-danger removeConditionRow"><i class="fas fa-trash"></i></button>';
                                                        $content .= '</div>';                
                                                    $content .= '<small id="condition_'.$row.'Help" class="form-text text-muted hintHelp"></small>';
                                                $content .= '</div>';
                                            $content .= '</div>';                   

                                        $content .= "</div>";   
                                    $content .= "</div>";  
                                $content .= "</div>";  

                                // condition tab
                                $content .= "<div class='tab-pane tpbAddMenuDiv fade' id='pills-condition' role='tabpanel' aria-labelledby='pills-condition-tab'>";
                                    $content .= "Create a TPB Application First";
                                $content .= "</div>";   
                                
                                // EOT tab
                                $content .= "<div class='tab-pane tpbAddMenuDiv fade' id='pills-EOT' role='tabpanel' aria-labelledby='pills-EOT-tab'>";                                    
                                $content .= "Create a TPB Application First";
                                $content .= "</div>";       
                                
                                // STT tab
                                $content .= "<div class='tab-pane tpbAddMenuDiv fade' id='pills-STT' role='tabpanel' aria-labelledby='pills-STT-tab'>";                                    
                                $content .= "Create a TPB Application First";
                                $content .= "</div>";   
                                
                                // STW tab
                                $content .= "<div class='tab-pane tpbAddMenuDiv fade' id='pills-STW' role='tabpanel' aria-labelledby='pills-STW-tab'>";                                    
                                $content .= "Create a TPB Application First";
                                $content .= "</div>";                                   

                            $content .= "</div>";

                        $content .= "</div>"; // end card body

                    $content .= "</div>"; // end card
                $content .= "</div>"; // end grad-12
            $content .= "</div>"; // end row

        $content .= "</form>"; // end form

        /* 
        $content .= "<script>$(document).ready(function() {
            $('.select2').select2({
                placeholder: 'Select an option',
                allowClear: true
            });
        });</script>"; */
        

        return new Data(['success'=>true, 'message'=>$content]);
    }

    /*
    public function tpbForm($request) {
                
		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) {
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);
            $obj['userID'] = null;
            $officerList = self::findOfficer($request->get->id);
            foreach($officerList as $officerInfo) {
                $obj['userID'][] = $officerInfo['userID'];
            }
        }

		$formName = "form-addTpb";

		if(!is_null($obj)) {
			$formName = "form-editTpb";
		}				
        
		$content = "<form id='".$formName."' class='' autocomplete='off'>";
		$content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.tpbAddHelperMessage')."</p></div>";
        
        //$content .= formLayout::rowInputNew(L('tpb.submissionDate'),'submissionDate', 'submissionDate', 'date',  12, [], ['required'], is_null($obj)?'':$obj['submissionDate']);
        $content .= "<div class='row'>";
        $content .= formLayout::rowInputNew(L('tpb.refNo'),'refNo', 'refNo', 'text',  6, [], ['required'], is_null($obj)?'':$obj['refNo']);

        $option = [""=>"", "Add"=>"[".L('Add')."]"];
        $stm = Sql::select('client')->where(['status', '=', 1])->prepare();
        $stm->execute();                                          
        foreach ($stm as $opt) {  
             $option[$opt['id']] = $opt['contactPerson'];			  
        }
        
        $content .= formLayout::rowSelectNew(L('tpb.client'), 'clientID', 'clientID', $option,  6, ['clientSelect'], ['required'], is_null($obj)?'':$obj['clientID']);  
        
        //$content .= formLayout::rowSeparatorLineNew(12);
        
        $option = [];
        $stm = Sql::select('user')->where(['status', '=', 1])->prepare();
        $stm->execute();                                          
        foreach ($stm as $opt) {  
             $option[$opt['id']] = $opt['username'];			  
        }
        $content .= formLayout::rowMultiSelectNew(L('tpb.officer'), 'userID[]', 'userID', $option,  6, ['select2'], ['required'], empty($obj['userID'])?[]:$obj['userID']);             

        if(!is_null($obj)) {
            $option = [];
            $stm = Sql::select('tpbStatus')->prepare();
            $stm->execute();                                          
            foreach ($stm as $opt) {  
                $option[$opt['id']] = $opt['name'];
            }
            $content .= formLayout::rowSelectNew(L('Status'), 'tpbStatusID', 'tpbStatusID', $option, 6, [], ['required'], is_null($obj)?'':$obj['tpbStatusID']);
        }
		$content .= "</div>";
		$content .= "</form>";
        
		return new Data(['success'=>true, 'message'=>$content]);
		
	}
    */	

    // tpb add action
    public function add($request) {	
       
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

        $currentUserObj = unserialize($_SESSION['user']);

        // form check	
		if (!isset($request->post->clientID) || empty($request->post->clientID)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyClientID'), 'field'=>'clientID', 'tab'=>'pills-applicant']);

        /*
        if (!isset($request->post->refNo) || empty($request->post->refNo)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyRefNo'), 'field'=>'refNo', 'tab'=>'pills-application']);
        */

        if (!isset($request->post->userID) || empty($request->post->userID) || count($request->post->userID)==0) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyOfficer'), 'field'=>'userID', 'tab'=>'pills-application']);

        /*
		if (!isset($request->post->addressDDLot) || empty($request->post->addressDDLot)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyAddressDDLot'), 'field'=>'addressDDLot', 'tab'=>'pills-application']);

		if (!isset($request->post->OZPName) || empty($request->post->OZPName)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyOzpName'), 'field'=>'OZPName', 'tab'=>'pills-application']);
        
        if (!isset($request->post->OZPNo) || empty($request->post->OZPNo)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyOzpNo'), 'field'=>'OZPNo', 'tab'=>'pills-application']);     
                
        if (!isset($request->post->zoningID) || empty($request->post->zoningID) || count($request->post->zoningID)==0) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyZoning'), 'field'=>'zoningID', 'tab'=>'pills-application']);     

        if (!isset($request->post->proposedUse) || empty($request->post->proposedUse)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyProposedUse'), 'field'=>'proposedUse', 'tab'=>'pills-application']); 
        
        if (!isset($request->files->authorizationLetterDoc) || empty($request->files->authorizationLetterDoc['tmp_name'])) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyAuthorizationLetterDoc'), 'field'=>'authorizationLetterDoc', 'tab'=>'pills-application']);

        if (!isset($request->post->isLandOwner) || empty($request->post->isLandOwner)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyIsLandOwner'), 'field'=>'isLandOwner', 'tab'=>'pills-application']); 

        if ($request->post->isLandOwner=="Y") {

            if (!isset($request->files->landRegistryDoc) || empty($request->files->landRegistryDoc['tmp_name'])) 
			    return new Data(['success'=>false, 'message'=>L('error.tpbEmptyLandRegistryDoc'), 'field'=>'landRegistryDoc', 'tab'=>'pills-application']);

        } else {
            
            if (!isset($request->files->siteNoticeDoc) || empty($request->files->siteNoticeDoc['tmp_name'])) 
			    return new Data(['success'=>false, 'message'=>L('error.tpbEmptySiteNoticeDoc'), 'field'=>'siteNoticeDoc', 'tab'=>'pills-application']);

            if (!isset($request->files->letterToRCDoc) || empty($request->files->letterToRCDoc['tmp_name'])) 
			    return new Data(['success'=>false, 'message'=>L('error.tpbEmptyLetterToRCDoc'), 'field'=>'letterToRCDoc', 'tab'=>'pills-application']);                

        }

        if (!isset($request->files->submissionDoc) || empty($request->files->submissionDoc['tmp_name'])) 
            return new Data(['success'=>false, 'message'=>L('error.tpbEmptySubmissionDoc'), 'field'=>'submissionDoc', 'tab'=>'pills-application']);        

        if (!isset($request->post->submissionDate) || empty($request->post->submissionDate)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptySubmissionDate'), 'field'=>'submissionDate', 'tab'=>'pills-submission']);   

        if (!isset($request->post->submissionModeID) || empty($request->post->submissionModeID)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptySubmissionMode'), 'field'=>'submissionModeID', 'tab'=>'pills-submission']);  

        if (!isset($request->post->rntpcID) || empty($request->post->rntpcID)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyRntpc'), 'field'=>'rntpcID', 'tab'=>'pills-submission']);    
        
        //if (!isset($request->post->remarks) || empty($request->post->remarks)) 
		  //      return new Data(['success'=>false, 'message'=>L('error.tpbEmptyRemarks'), 'field'=>'remarks', 'tab'=>'pills-application']);               


		if (!isset($request->post->TPBNo) || empty($request->post->TPBNo)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyNumber'), 'field'=>'TPBNo']);

		if (!isset($request->post->TPBWebsite) || empty($request->post->TPBWebsite)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyWebsite'), 'field'=>'TPBWebsite']);
        
        if (!isset($request->post->TPBReceiveDate) || empty($request->post->TPBReceiveDate)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyReceiveDate'), 'field'=>'TPBReceiveDate']);     
                
        if (!isset($request->post->tentativeConsiderationDate) || empty($request->post->tentativeConsiderationDate)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyConsiderationDate'), 'field'=>'tentativeConsiderationDate']); 


		if (!isset($request->post->decisionID) || empty($request->post->decisionID)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyDecision'), 'field'=>'decisionID']);

		if (!isset($request->post->decisionDate) || empty($request->post->decisionDate)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyDecisionDate'), 'field'=>'decisionDate']);
        
        if (!isset($request->post->approvalValidUntil) || empty($request->post->approvalValidUntil)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyApprovalValidUntil'), 'field'=>'approvalValidUntil']);     
        
        if ($request->post->decisionID==2) {
            if(count($request->post->condition)==0) {
                return new Data(['success'=>false, 'message'=>L('error.tpbEmptyCondition'), 'field'=>'condition_0']);     
            }

            foreach($request->post->description as $ids => $description){
                if($description=="") {
                    return new Data(['success'=>false, 'message'=>L('error.tpbEmptyCondition'), 'field'=>'description_'.$ids]);     
                }

                if($request->post->number[$ids]=="") {
                    return new Data(['success'=>false, 'message'=>L('error.tpbEmptyNumber'), 'field'=>'number_'.$ids]);     
                }                

                if($request->post->deadline[$ids]=="") {
                    return new Data(['success'=>false, 'message'=>L('error.tpbEmptyDeadline'), 'field'=>'deadline_'.$ids]);     
                }               
            }
        }

        */
        
        // file upload

        $authorizationLetterDocID = 0;
        $landRegistryDocID = 0;
        $siteNoticeDocID = 0;
        $letterToRCDocID = 0;
        $submissionDocID = 0;

        
        if (isset($request->files->authorizationLetterDoc) && !empty($request->files->authorizationLetterDoc)) {
            $authorizationLetterDocID = documentHelper::upload($request->files->authorizationLetterDoc, "AUTHORIZATION_LETTER");
        }

        if ($request->post->isLandOwner=="Y") {
            if (isset($request->files->landRegistryDoc) && !empty($request->files->landRegistryDoc)) {
                $landRegistryDocID = documentHelper::upload($request->files->landRegistryDoc, "LAND_REGISTRY");
            }    
        } else {
            if (isset($request->files->siteNoticeDoc) && !empty($request->files->siteNoticeDoc)) {
                $siteNoticeDocID = documentHelper::upload($request->files->siteNoticeDoc, "SITE_NOTICE");
            } 

            if (isset($request->files->letterToRCDoc) && !empty($request->files->letterToRCDoc)) {
                $letterToRCDocID = documentHelper::upload($request->files->letterToRCDoc, "LETTER_TO_RC");
            }             
        }

        if (isset($request->files->submissionDoc) && !empty($request->files->submissionDoc)) {
            $submissionDocID = documentHelper::upload($request->files->submissionDoc, "SUBMISSION");
        }

        if (!empty($request->post->TPBNo) && empty($request->post->TPBWebsite)) {
            $request->post->TPBWebsite = "https://www.tpb.gov.hk/tc/plan_application/".$request->post->TPBNo.".html";
        }

        $sql = Sql::insert('tpb')->setFieldValue([
            'refNo' => "?", 
            'clientID' => "?", 
            'addressDDLot' => "?", 
            'OZPName' => "?", 
            'OZPNo' => "?", 
            'proposedUse' => "?", 
            'authorizationLetterDocID' => "?",
            'isLandOwner' => "?",
            'landRegistryDocID' => "?",
            'siteNoticeDocID' => "?",
            'letterToRCDocID' => "?",
            'rntpcID' => "?",     
            'submissionDate' => "?", 
            'submissionDocID' => "?",
            'submissionModeID' => "?", 
            'remarks' => "?", 
            'TPBNo'=> "?", 
            'TPBWebsite'=> "?", 
            'TPBReceiveDate'=> "?", 
            'tentativeConsiderationDate'=> "?", 
            'decisionID' => "?", 
            'decisionDate' => "?", 
            'approvalValidUntil' => "?", 
            'tpbStatusID' => "?",
            'createBy'=>$currentUserObj->id, 
            'modifyBy'=>$currentUserObj->id
        ]);

        $addValues = [
            strip_tags($request->post->refNo),
            strip_tags($request->post->clientID),
            strip_tags($request->post->addressDDLot),
            strip_tags($request->post->OZPName),
            strip_tags($request->post->OZPNo),
            strip_tags($request->post->proposedUse),
            strip_tags($authorizationLetterDocID),
            strip_tags($request->post->isLandOwner),
            strip_tags($landRegistryDocID),
            strip_tags($siteNoticeDocID),
            strip_tags($letterToRCDocID),
            strip_tags($request->post->rntpcID),
            strip_tags($request->post->submissionDate),
            strip_tags($submissionDocID),
            strip_tags($request->post->submissionModeID),
            strip_tags($request->post->remarks),
            strip_tags($request->post->TPBNo),
            strip_tags($request->post->TPBWebsite),
            strip_tags($request->post->TPBReceiveDate),
            strip_tags($request->post->tentativeConsiderationDate),                
            strip_tags($request->post->decisionID),
            strip_tags($request->post->decisionDate),
            strip_tags($request->post->approvalValidUntil),
            strip_tags("1")
        ];

        if ($sql->prepare()->execute($addValues)) {
            
            $tpbID = db()->lastInsertId();

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "TPB";
			$logData['referenceID'] = $tpbID;
			$logData['action'] = "Insert";
			$logData['description'] = "Create New TPB [ID: ".$tpbID."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $addValues;
			$logData['changes'] = 
				[
					[
						"key"=>"refNo", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->refNo)
                    ],[
						"key"=>"clientID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->clientID)
                    ],[
						"key"=>"addressDDLot", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->addressDDLot)
                    ],[
						"key"=>"OZPName", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->OZPName)
                    ],[
						"key"=>"OZPNo", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->OZPNo)
                    ],[
						"key"=>"proposedUse", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->proposedUse)
                    ],[
						"key"=>"authorizationLetterDocID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($authorizationLetterDocID)
                    ],[
						"key"=>"isLandOwner", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->isLandOwner)
                    ],[
						"key"=>"landRegistryDocID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($landRegistryDocID)
                    ],[
						"key"=>"siteNoticeDocID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($siteNoticeDocID)
                    ],[
						"key"=>"letterToRCDocID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($letterToRCDocID)
                    ],[
						"key"=>"rntpcID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->rntpcID)
                    ],[
						"key"=>"submissionDate", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->submissionDate)
                    ],[
						"key"=>"submissionDocID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($submissionDocID)
                    ],[
						"key"=>"submissionModeID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->submissionModeID)
                    ],[
						"key"=>"remarks", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->remarks)
                    ],[
						"key"=>"TPBNo", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->TPBNo)
                    ],[
						"key"=>"TPBWebsite", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->TPBWebsite)
                    ],[
						"key"=>"TPBReceiveDate", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->TPBReceiveDate)
                    ],[
						"key"=>"tentativeConsiderationDate", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->tentativeConsiderationDate)
                    ],[
						"key"=>"decisionID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->decisionID)
                    ],[
						"key"=>"decisionDate", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->decisionDate)
                    ],[
						"key"=>"approvalValidUntil", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->approvalValidUntil)
                    ],[
						"key"=>"tpbStatusID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags(1)
                    ]
				];

            systemLog::add($logData);            




            // insert tpbOfficer
            foreach($request->post->userID as $officerID) {
                $sql = Sql::insert('tpbOfficer')->setFieldValue([
                    'tpbID' => "?", 
                    'userID' => "?"
                ]);               

                $addOfficerValues = [
                    strip_tags($tpbID),
                    strip_tags($officerID)
                ];

                if($sql->prepare()->execute($addOfficerValues)){

                    $id = db()->lastInsertId();

                    $logData = [];
                    $logData['userID']= $currentUserObj->id;
                    $logData['module'] = "TPB";
                    $logData['referenceID'] = $id;
                    $logData['action'] = "Insert";
                    $logData['description'] = "Assign Officer to TPB [ID: ".$tpbID."]";
                    $logData['sqlStatement'] = $sql;
                    $logData['sqlValue'] = $addOfficerValues;
                    $logData['changes'] = 
                        [
                            [
                                "key"=>"userID", 
                                "valueFrom"=>"", 
                                "valueTo"=>strip_tags($officerID)
                            ]
                        ];
        
                    systemLog::add($logData);  
                }

            }

            // insert tpbZoning
            foreach($request->post->zoningID as $zoningID) {
                $sql = Sql::insert('tpbZoning')->setFieldValue([
                    'tpbID' => "?", 
                    'zoningID' => "?"
                ]);               

                $addZoningValues = [
                    strip_tags($tpbID),
                    strip_tags($zoningID)
                ];

                if($sql->prepare()->execute($addZoningValues)){
                    $id = db()->lastInsertId();

                    $logData = [];
                    $logData['userID']= $currentUserObj->id;
                    $logData['module'] = "TPB";
                    $logData['referenceID'] = $id;
                    $logData['action'] = "Insert";
                    $logData['description'] = "Assign Zoning to TPB [ID: ".$tpbID."]";
                    $logData['sqlStatement'] = $sql;
                    $logData['sqlValue'] = $addZoningValues;
                    $logData['changes'] = 
                        [
                            [
                                "key"=>"zoningID", 
                                "valueFrom"=>"", 
                                "valueTo"=>strip_tags($zoningID)
                            ]
                        ];
        
                    systemLog::add($logData);                    
                }
            }     

            // insert condition
            foreach($request->post->description as $idx=>$description) {
                $sql = Sql::insert('tpbCondition')->setFieldValue([
                    'tpbID' => "?", 
                    'conditionNo' => "?",
                    'description' => "?",
                    'deadline' => "?",
                    'status' => "?",
                    'createBy'=>$currentUserObj->id, 
                    'modifyBy'=>$currentUserObj->id                    
                ]);               

                $addConditionValues = [
                    strip_tags($tpbID),
                    strip_tags($request->post->number[$idx]),
                    strip_tags("(TPB ID: ".$tpbID.") ".$description),
                    strip_tags($request->post->deadline[$idx]),
                    strip_tags(1)
                ];

                if($sql->prepare()->execute($addConditionValues)){

                    $conditionID = db()->lastInsertId();


                    $logData = [];
                    $logData['userID']= $currentUserObj->id;
                    $logData['module'] = "TPB";
                    $logData['referenceID'] = $conditionID;
                    $logData['action'] = "Insert";
                    $logData['description'] = "Assign Condition to TPB [ID: ".$tpbID."]";
                    $logData['sqlStatement'] = $sql;
                    $logData['sqlValue'] = $addConditionValues;
                    $logData['changes'] = 
                        [
                            [
                                "key"=>"tpbID", 
                                "valueFrom"=>"", 
                                "valueTo"=>strip_tags($tpbID)
                            ],[
                                "key"=>"conditionNo", 
                                "valueFrom"=>"", 
                                "valueTo"=>strip_tags($request->post->number[$idx])
                            ],[
                                "key"=>"description", 
                                "valueFrom"=>"", 
                                "valueTo"=>strip_tags("(TPB ID: ".$tpbID.") ".$description)
                            ],[
                                "key"=>"deadline", 
                                "valueFrom"=>"", 
                                "valueTo"=>strip_tags($request->post->deadline[$idx])
                            ],[
                                "key"=>"status", 
                                "valueFrom"=>"", 
                                "valueTo"=>strip_tags(1)
                            ]
                        ];
        
                    systemLog::add($logData);   

                    // auto create condition task
                    foreach($request->post->userID as $officerID) {
    
                        $conditionArray = [
                            'userID'=> strip_tags($officerID),
                            'tpbID'=> strip_tags($tpbID),
                            'conditionID'=> strip_tags($conditionID),
                            'description'=> strip_tags("(Condition ID: ".$conditionID.") ".$description),
                            'deadline'=> strip_tags($request->post->deadline[$idx])
                        ];             
            
                        task::createTask($conditionArray);
                    }

                }

            }            

            self::autoGenerateInitialTask($tpbID);
            
            return new Data(['success'=>true, 'message'=>L('info.saved'), 'id'=>$tpbID, 'name'=>$request->post->refNo]);
            
        } else {
            return new Data(['success'=>false, 'message'=>L('error.unableInsert'), 'field'=>'notice']);
        }	            
                
	}

    // tpb edit form
    public function tpbFormEdit($request) {

        if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$obj = null;
		if (isset($request->get->id)) {
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

            $obj['userID'] = null;
            $officerList = self::findOfficer($request->get->id);
            foreach($officerList as $officerInfo) {
                $obj['userID'][] = $officerInfo['userID'];
            }

            $obj['zoningID'] = null;
            $zoningList = self::findZoning($request->get->id);
            foreach($zoningList as $zoningInfo) {
                $obj['zoningID'][] = $zoningInfo['zoningID'];
            }            

            $clientObj = client::find($obj['clientID']);
        }

        $content = "";
        $content .= "<div class='row'>";
            $content .= "<div class='col-md-12'>";
                $content .= "<div class='card'>";
                    $content .= "<div class='card-header'>";
                        $content .= "<div class='d-flex align-items-center'>";
                            $content .= "<h4 class='card-title' id='notice'>".L('info.tpbAddHelperMessage')."</h4>";
                        $content .= "</div>";
                    $content .= "</div>";

                    $content .= "<div class='card-body'>";
                        $content .= "<ul class='nav nav-pills nav-secondary' id='pills-tab-tpbEdit' role='tablist'>";
                            $content .= "<li class='nav-item submenu tpbEditMenu' role='presentation'>";
                                $content .= "<a class='tpbEditMenuA nav-link active' id='pills-applicant-tab' data-bs-toggle='pill' href='#pills-applicant' role='tab' aria-controls='pills-applicant' aria-selected='false' tabindex='-1'>".L('tpb.applicantDetail')."</a>";
                            $content .= "</li>";

                            $content .= "<li class='nav-item submenu tpbEditMenu' role='presentation'>";
                                $content .= "<a class='tpbEditMenuA nav-link' id='pills-application-tab' data-bs-toggle='pill' href='#pills-application' role='tab' aria-controls='pills-application' aria-selected='false' tabindex='-1'>".L('tpb.applicationDetail')."</a>";
                            $content .= "</li>";

                            $content .= "<li class='nav-item submenu tpbEditMenu' role='presentation'>";
                                $content .= "<a class='tpbEditMenuA nav-link' id='pills-submission-tab' data-bs-toggle='pill' href='#pills-submission' role='tab' aria-controls='pills-submission' aria-selected='false' tabindex='-1'>".L('tpb.submissionDetail')."</a>";
                            $content .= "</li>";

                            $content .= "<li class='nav-item submenu tpbEditMenu' role='presentation'>";
                                $content .= "<a class='tpbEditMenuA nav-link' id='pills-receive-tab' data-bs-toggle='pill' href='#pills-receive' role='tab' aria-controls='pills-receive' aria-selected='false' tabindex='-1'>".L('tpb.receiveDetail')."</a>";
                            $content .= "</li>";
                            
                            $content .= "<li class='nav-item submenu tpbEditMenu' role='presentation'>";
                                $content .= "<a class='tpbEditMenuA nav-link' id='pills-decision-tab' data-bs-toggle='pill' href='#pills-decision' role='tab' aria-controls='pills-decision' aria-selected='false' tabindex='-1'>".L('tpb.decisionDetail')."</a>";
                            $content .= "</li>";
                            
                            $content .= "<li class='nav-item submenu tpbEditMenu' role='presentation'>";
                                $content .= "<a class='tpbEditMenuA nav-link' id='pills-condition-tab' data-bs-toggle='pill' href='#pills-condition' role='tab' aria-controls='pills-condition' aria-selected='false' tabindex='-1'>".L('tpb.conditionDetail')."</a>";
                            $content .= "</li>";
                            
                            $content .= "<li class='nav-item submenu tpbEditMenu' role='presentation'>";
                                $content .= "<a class='tpbEditMenuA nav-link' id='pills-EOT-tab' data-bs-toggle='pill' href='#pills-EOT' role='tab' aria-controls='pills-EOT' aria-selected='false' tabindex='-1'>".L('tpb.EOTDetail')."</a>";
                            $content .= "</li>";                            

                            $content .= "<li class='nav-item submenu tpbEditMenu' role='presentation'>";
                                $content .= "<a class='tpbEditMenuA nav-link' id='pills-STT-tab' data-bs-toggle='pill' href='#pills-STT' role='tab' aria-controls='pills-STT' aria-selected='false' tabindex='-1'>".L('tpb.STTDetail')."</a>";
                            $content .= "</li>";
                            
                            $content .= "<li class='nav-item submenu tpbEditMenu' role='presentation'>";
                                $content .= "<a class='tpbEditMenuA nav-link' id='pills-STW-tab' data-bs-toggle='pill' href='#pills-STW' role='tab' aria-controls='pills-STW' aria-selected='false' tabindex='-1'>".L('tpb.STWDetail')."</a>";
                            $content .= "</li>";                            

                        $content .= "</ul>";
                
                        $content .= "<div class='tab-content mt-2 mb-3' id='pills-tabContent-tpbEdit'>";

                            // applicant tab
                            $content .= "<div class='tab-pane tpbEditMenuDiv fade show active' id='pills-applicant' role='tabpanel' aria-labelledby='pills-applicant-tab'>";
                                $content .= "<form id='form-editTPB-applicant' class='' autocomplete='off'>";
                                    $content .= "<div class='row'>";

                                        $option = [""=>"", "Add"=>"[".L('Add')."]"];
                                        $stm = Sql::select('client')->where(['status', '=', 1])->prepare();
                                        $stm->execute();                                          
                                        foreach ($stm as $opt) {  
                                            $option[$opt['id']] = $opt['contactPerson'];			  
                                        }
                                        
                                        $content .= formLayout::rowSelectNew(L('tpb.client'), 'clientID', 'clientID', $option,  6, ['clientSelect'], ['required'], $obj['clientID']);  

                                        $content .= "<div class='row' id='tpbSelectedClentDetail'>"; 

                                            $content .= "<div class='row'>";
                                                $content .= formLayout::rowDisplayLineNew(L('client.type'),clientType::find($clientObj->clientTypeID)->name??"", 6);
                                                $content .= formLayout::rowDisplayLineNew(L('client.title'),$clientObj->title??"", 6);
                                            $content .= "</div>";
                                    
                                            $content .= "<div class='row'>";
                                                $content .= formLayout::rowDisplayLineNew(L('client.contactPerson'),$clientObj->contactPerson??"", 6);
                                                $content .= formLayout::rowDisplayLineNew(L('client.position'),$clientObj->position??"", 6);
                                            $content .= "</div>";
                                    
                                            $content .= "<div class='row'>";
                                                $content .= formLayout::rowDisplayLineNew(L('client.phone'),$clientObj->phone??"", 6);
                                                $content .= formLayout::rowDisplayLineNew(L('client.email'),$clientObj->email??"", 6);
                                            $content .= "</div>";
                                    
                                            $content .= "<div class='row'>";
                                            $content .= formLayout::rowDisplayLineNew(L('client.idCardNo'),$clientObj->idCardNo??"", 6);    
                                            if($clientObj->idCardDocID>0) {
                                                $content .= formLayout::rowDisplayClearLineNew('<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs" data-id="'.$clientObj->idCardDocID.'"><i class="fas fa-download"></i></button></div>', 6);           
                                            }
                                            $content .= "</div>";        
                                    
                                            $content .= "<div class='row'>";
                                                $content .= formLayout::rowDisplayLineNew(L('client.address'),$clientObj->address??"", 12);            
                                            $content .= "</div>";            
                                    
                                            $content .= formLayout::rowSeparatorLineNew(12);        
                                    
                                            $content .= "<div class='row'>";
                                                $content .= formLayout::rowDisplayLineNew(L('client.companyEnglishName'),$clientObj->companyEnglishName??"", 6);
                                                $content .= formLayout::rowDisplayLineNew(L('client.companyChineseName'),$clientObj->companyChineseName??"", 6);
                                            $content .= "</div>";
                                    
                                            $content .= "<div class='row'>";
                                                $content .= formLayout::rowDisplayLineNew(L('client.companyAddress'),$clientObj->companyAddress??"", 12);            
                                            $content .= "</div>";  
                                            
                                            $content .= "<div class='row'>";
                                            $content .= formLayout::rowDisplayLineNew(L('client.CINo'),$clientObj->CINo??"", 6);
                                            if($clientObj->CIDocID>0) {
                                                $content .= formLayout::rowDisplayClearLineNew('<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs" data-id="'.$clientObj->CIDocID.'"><i class="fas fa-download"></i></button></div>', 6);           
                                            }       
                                            $content .= "</div>";  
                                    
                                            $content .= "<div class='row'>";
                                                $content .= formLayout::rowDisplayLineNew(L('client.BRNo'),$clientObj->BRNo??"", 6);
                                                if($clientObj->BRDocID>0) {
                                                    $content .= formLayout::rowDisplayClearLineNew('<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs" data-id="'.$clientObj->BRDocID.'"><i class="fas fa-download"></i></button></div>', 6);           
                                                }  
                                            $content .= "</div>"; 
                                            
                                        $content .= "</div>";
                                        $content .= formLayout::rowSeparatorLineNew(12);        
                                
                                        $content .= "<div class='row'>";
                                            $content .= "<div class='col-md-12 col-lg-12 text-end'>";
                                                $content .= "<button type='button' class='btn btn-md btn-success' id='save_pills-applicant'>".L('Save')." ".L('tpb.applicantDetail')."</button>";
                                            $content .= "</div>";                 
                                        $content .= "</div>"; 

                                    $content .= "</div>";
                                $content .= "</form>";
                            $content .= "</div>";

                            // application tab
                            $content .= "<div class='tab-pane tpbEditMenuDiv fade' id='pills-application' role='tabpanel' aria-labelledby='pills-application-tab'>";
                                $content .= "<form id='form-editTPB-application' class='' autocomplete='off'>";
                                    $content .= "<div class='row'>";
                                        $content .= formLayout::rowInputNew(L('tpb.refNo'),'refNo', 'refNo', 'text',  6, [], [], $obj['refNo']);

                                        $option = [];
                                        $stm = Sql::select('user')->where(['status', '=', 1])->prepare();
                                        $stm->execute();                                          
                                        foreach ($stm as $opt) {  
                                            $option[$opt['id']] = $opt['displayName'];			  
                                        }
                                        
                                        $content .= formLayout::rowMultiSelectNew(L('tpb.officer'), 'userID[]', 'userID', $option,  6, ['select2'], ['required'], $obj['userID']??[]); 
                                    
                                        $content .= formLayout::rowInputNew(L('tpb.addressDDLot'),'addressDDLot', 'addressDDLot', 'text',  12, [], [], $obj['addressDDLot']??"");
                                        $content .= formLayout::rowInputNew(L('tpb.ozpName'),'OZPName', 'OZPName', 'text',  6, [], [], $obj['OZPName']??"");
                                        $content .= formLayout::rowInputNew(L('tpb.ozpNo'),'OZPNo', 'OZPNo', 'text',  6, [], [], $obj['OZPNo']??"");
                                
                                        
                                        $option = [];
                                        $stm = Sql::select('zoning')->where(['status', '=', 1])->prepare();
                                        $stm->execute();                                          
                                        foreach ($stm as $opt) {  
                                            $option[$opt['id']] = $opt['name'];			  
                                        }
                                        $content .= formLayout::rowMultiSelectNew(L('tpb.zoning'), 'zoningID[]', 'zoningID', $option,  6, ['select2'], [], $obj['zoningID']??[]);
                                        
                                        $content .= formLayout::rowInputNew(L('tpb.proposedUse'),'proposedUse', 'proposedUse', 'text',  6, [], [], $obj['proposedUse']??"");

                                        $content .= formLayout::rowInputNew(L('tpb.authorizationLetter'),'authorizationLetterDoc', 'authorizationLetterDoc', 'file',  6, [], ['accept="image/*, application/pdf"']);

                                        if(isset($obj['authorizationLetterDocID']) && $obj['authorizationLetterDocID']>0) {
                                            $content .= formLayout::rowDisplayClearGroupLineNew('<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs mt-2 authorizationLetterDownload" data-id="'.$obj['authorizationLetterDocID'].'"><i class="fas fa-download"></i></button><button type="button" class="btn btn-danger removeDoc btn-xs mt-2 authorizationLetterRemove" data-id="'.$obj['authorizationLetterDocID'].'" data-tpb="'.$obj['id'].'" data-doc="authorizationLetterDocID"><i class="fas fa-trash"></i></button></div>',6);
                                        } else {
                                            $content .= formLayout::rowDisplayClearGroupLineNew('<div class="d-flex gap-2 btnGrp" style="display: none !important"><button type="button" class="btn btn-black downloadDoc btn-xs mt-2 authorizationLetterDownload" data-id="0"><i class="fas fa-download"></i></button><button type="button" class="btn btn-danger removeDoc btn-xs mt-2 authorizationLetterRemove" data-id="0" data-tpb="'.$obj['id'].'" data-doc="authorizationLetterDocID"><i class="fas fa-trash"></i></button></div>',6);
                                        }    

                                    $content .= "</div>";

                                    $content .= "<div class='row'>";
                                        $option = ["Y"=>L('Y'), "N"=>L('N')];
                                        $content .= formLayout::rowRadioNew(L('tpb.isLandOwner'), 'isLandOwner', 'isLandOwner', $option,  12, ['isLandOwnerSelect'], [], $obj['isLandOwner']??"");
                                    $content .= "</div>";

                                    if($obj['isLandOwner']=="Y") {
                                        $landOwnerSectionStyle="display: flex";
                                        $notLandOwnerSection="display: none";
                                    } else {
                                        $landOwnerSectionStyle="display: none";
                                        $notLandOwnerSection="display: flex";                                  
                                    }

                                    $content .= "<div class='row landOwnerSection' style='".$landOwnerSectionStyle."'>";
                                        $content .= formLayout::rowInputNew(L('tpb.landRegistry'),'landRegistryDoc', 'landRegistryDoc', 'file',  6, [], ['accept="image/*, application/pdf"']);
                                        if(isset($obj['landRegistryDocID']) && $obj['landRegistryDocID']>0) {
                                            $content .= formLayout::rowDisplayClearGroupLineNew('<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs mt-2 landRegistryDownload" data-id="'.$obj['landRegistryDocID'].'"><i class="fas fa-download"></i></button><button type="button" class="btn btn-danger removeDoc btn-xs mt-2 landRegistryRemove" data-id="'.$obj['landRegistryDocID'].'" data-tpb="'.$obj['id'].'" data-doc="landRegistryDocID"><i class="fas fa-trash"></i></button></div>',6);
                                        } else {
                                            $content .= formLayout::rowDisplayClearGroupLineNew('<div class="d-flex gap-2 btnGrp" style="display: none !important"><button type="button" class="btn btn-black downloadDoc btn-xs mt-2 landRegistryDownload" data-id="0"><i class="fas fa-download"></i></button><button type="button" class="btn btn-danger removeDoc btn-xs mt-2 landRegistryRemove" data-id="0" data-tpb="'.$obj['id'].'" data-doc="landRegistryDocID"><i class="fas fa-trash"></i></button></div>',6);
                                        }    
                                    $content .= "</div>";

                                    $content .= "<div class='row notLandOwnerSection' style='".$notLandOwnerSection."'>";
                                        $content .= formLayout::rowInputNew(L('tpb.siteNotice'),'siteNoticeDoc', 'siteNoticeDoc', 'file',  6, [], ['accept="image/*, application/pdf"']);
                                        if(isset($obj['siteNoticeDocID']) && $obj['siteNoticeDocID']>0) {
                                            $content .= formLayout::rowDisplayClearGroupLineNew('<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs mt-2 siteNoticeDownload" data-id="'.$obj['siteNoticeDocID'].'"><i class="fas fa-download"></i></button><button type="button" class="btn btn-danger removeDoc btn-xs mt-2 siteNoticeRemove" data-id="'.$obj['siteNoticeDocID'].'" data-tpb="'.$obj['id'].'" data-doc="siteNoticeDocID"><i class="fas fa-trash"></i></button></div>',6);
                                        } else {
                                            $content .= formLayout::rowDisplayClearGroupLineNew('<div class="d-flex gap-2 btnGrp" style="display: none !important"><button type="button" class="btn btn-black downloadDoc btn-xs mt-2 siteNoticeDownload" data-id="0"><i class="fas fa-download"></i></button><button type="button" class="btn btn-danger removeDoc btn-xs mt-2 siteNoticeRemove" data-id="0" data-tpb="'.$obj['id'].'" data-doc="siteNoticeDocID"><i class="fas fa-trash"></i></button></div>',6);
                                        }                                       
                                    $content .= "</div>";
                                    
                                    $content .= "<div class='row notLandOwnerSection' style='".$notLandOwnerSection."'>";                                        
                                        $content .= formLayout::rowInputNew(L('tpb.letterToRC'),'letterToRCDoc', 'letterToRCDoc', 'file',  6, [], ['accept="image/*, application/pdf"']);
                                        if(isset($obj['letterToRCDocID']) && $obj['letterToRCDocID']>0) {
                                            $content .= formLayout::rowDisplayClearGroupLineNew('<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs mt-2 letterToRCDownload" data-id="'.$obj['letterToRCDocID'].'"><i class="fas fa-download"></i></button><button type="button" class="btn btn-danger removeDoc btn-xs mt-2 letterToRCRemove" data-id="'.$obj['letterToRCDocID'].'" data-tpb="'.$obj['id'].'" data-doc="letterToRCDocID"><i class="fas fa-trash"></i></button></div>',6);
                                        } else {
                                            $content .= formLayout::rowDisplayClearGroupLineNew('<div class="d-flex gap-2 btnGrp" style="display: none !important"><button type="button" class="btn btn-black downloadDoc btn-xs mt-2 letterToRCDownload" data-id="0"><i class="fas fa-download"></i></button><button type="button" class="btn btn-danger removeDoc btn-xs mt-2 letterToRCRemove" data-id="0" data-tpb="'.$obj['id'].'" data-doc="letterToRCDocID"><i class="fas fa-trash"></i></button></div>',6);
                                        }                                      
                                    $content .= "</div>";
                                    
                                    $content .= "<div class='row'>";
                                        $content .= formLayout::rowTextAreaNew(L('tpb.remarks'), 'remarks', 'remarks',  12, [], [], $obj['remarks']??"");
                                    $content .= "</div>";

                                    $content .= "<div class='row'>";
                                        if(!is_null($obj)) {
                                            $option = [];
                                            $stm = Sql::select('tpbStatus')->prepare();
                                            $stm->execute();                                          
                                            foreach ($stm as $opt) {  
                                                $option[$opt['id']] = $opt['name'];
                                            }
                                            $content .= formLayout::rowSelectNew(L('Status'), 'tpbStatusID', 'tpbStatusID', $option, 6, [], ['required'], is_null($obj)?'':$obj['tpbStatusID']);
                                        }
                                    $content .= "</div>";   

                                    $content .= "<div class='row'>";
                                        $content .= "<div class='col-md-12 col-lg-12 text-end'>";
                                            $content .= "<button type='button' class='btn btn-md btn-success' id='save_pills-application'>".L('Save')." ".L('tpb.applicationDetail')."</button>";
                                        $content .= "</div>";                 
                                    $content .= "</div>"; 
                                $content .= "</form>";
                            $content .= "</div>";

                            // submission tab
                            $content .= "<div class='tab-pane tpbEditMenuDiv fade' id='pills-submission' role='tabpanel' aria-labelledby='pills-submission-tab'>";
                                $content .= "<form id='form-editTPB-submission' class='' autocomplete='off'>";
                                    $content .= "<div class='row'>";
                                        $content .= formLayout::rowInputNew(L('tpb.submissionDate'),'submissionDate', 'submissionDate', 'text',  6, ['customDate'], [], ($obj['submissionDate']=="0000-00-00" || empty($obj['submissionDate'])?"":$obj['submissionDate']));
                                        $option = [];
                                        $stm = Sql::select('submissionMode')->where(['status', '=', 1])->prepare();
                                        $stm->execute();                                          
                                        foreach ($stm as $opt) {  
                                            $option[$opt['id']] = $opt['name'];			  
                                        }
                                        $content .= formLayout::rowRadioNew(L('tpb.submissionMode'), 'submissionModeID', 'submissionModeID', $option,  6, ['submissionModeSelect'], [], $obj['submissionModeID']??"");
                                    $content .= "</div>";

                                    $content .= "<div class='row'>";
                                        $content .= formLayout::rowInputNew(L('tpb.submissionDocument'),'submissionDoc', 'submissionDoc', 'file',  6, [], ['accept="image/*, application/pdf"']);
                                        if(isset($obj['submissionDocID']) && $obj['submissionDocID']>0) {
                                            $content .= formLayout::rowDisplayClearGroupLineNew('<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs mt-2" data-id="'.$obj['submissionDocID'].'"><i class="fas fa-download"></i></button><button type="button" class="btn btn-danger removeDoc btn-xs mt-2" data-id="'.$obj['submissionDocID'].'" data-tpb="'.$obj['id'].'" data-doc="submissionDocID"><i class="fas fa-trash"></i></button></div>',6);
                                        } else {
                                            $content .= formLayout::rowDisplayClearGroupLineNew('<div class="d-flex gap-2 btnGrp" style="display: none !important"><button type="button" class="btn btn-black downloadDoc btn-xs mt-2" data-id="0"><i class="fas fa-download"></i></button><button type="button" class="btn btn-danger removeDoc btn-xs mt-2" data-id="0" data-tpb="'.$obj['id'].'" data-doc="submissionDocID"><i class="fas fa-trash"></i></button></div>',6);
                                        }                                    
                                    $content .= "</div>";  

                                    $content .= "<div class='row'>";
                                        $option = [""=>""];
                                        $stm = Sql::select('rntpc')->where(['status', '=', 1])->prepare();
                                        $stm->execute();                                          
                                        foreach ($stm as $opt) {  
                                            $option[$opt['id']] = $opt['meetingDate'];			  
                                        }                                    
                                        $content .= formLayout::rowSelectNew(L('tpb.rntpc'), 'rntpcID', 'rntpcID', $option,  6, [], [], $obj['rntpcID']??"");    
                                    $content .= "</div>";   
                                    
                                    $content .= "<div class='row'>";
                                        $content .= "<div class='col-md-12 col-lg-12 text-end'>";
                                            $content .= "<button type='button' class='btn btn-md btn-success' id='save_pills-submission'>".L('Save')." ".L('tpb.submissionDetail')."</button>";
                                        $content .= "</div>";                 
                                    $content .= "</div>";                                   

                                $content .= "</form>";
                            $content .= "</div>";

                            // receive tab
                            $content .= "<div class='tab-pane tpbEditMenuDiv fade' id='pills-receive' role='tabpanel' aria-labelledby='pills-receive-tab'>";
                                $content .= "<form id='form-editTPB-receive' class='' autocomplete='off'>";
                                    $content .= "<div class='row'>";
                                        $content .= formLayout::rowInputNew(L('tpb.number'),'TPBNo', 'TPBNo', 'text',  6, [], [], $obj['TPBNo']??"");
                                        $content .= formLayout::rowInputNew(L('tpb.website'),'TPBWebsite', 'TPBWebsite', 'text',  6, [], [], $obj['TPBWebsite']??"");                                    
                                        $content .= formLayout::rowInputNew(L('tpb.receiveDate'),'TPBReceiveDate', 'TPBReceiveDate', 'text',  6, ['customDate'], [], ($obj['TPBReceiveDate']=="0000-00-00 00:00:00" || empty($obj['TPBReceiveDate'])?"":$obj['TPBReceiveDate']));
                                        $content .= formLayout::rowInputNew(L('tpb.considerationDate'),'tentativeConsiderationDate', 'tentativeConsiderationDate', 'text',  6, ['customDate'], [], ($obj['tentativeConsiderationDate']=="0000-00-00 00:00:00" || empty($obj['tentativeConsiderationDate'])?"":$obj['tentativeConsiderationDate']));
                                    $content .= "</div>";      
                                    
                                    $content .= "<div class='row'>";
                                        $content .= "<div class='col-md-12 col-lg-12 text-end'>";
                                            $content .= "<button type='button' class='btn btn-md btn-success' id='save_pills-receive'>".L('Save')." ".L('tpb.receiveDetail')."</button>";
                                        $content .= "</div>";                 
                                    $content .= "</div>";                                   

                                $content .= "</form>";
                            $content .= "</div>";    
                            
                            // decision tab
                            $content .= "<div class='tab-pane tpbEditMenuDiv fade' id='pills-decision' role='tabpanel' aria-labelledby='pills-decision-tab'>";
                                $content .= "<form id='form-editTPB-decision' class='' autocomplete='off'>";

                                    $content .= "<div class='row'>";
                                    if(!is_null($obj)) {
                                        $option = [""=>""];
                                        $stm = Sql::select('decision')->prepare();
                                        $stm->execute();                                          
                                        foreach ($stm as $opt) {  
                                            $option[$opt['id']] = $opt['name'];
                                        }
                                        $content .= formLayout::rowSelectNew(L('tpb.decision'), 'decisionID', 'decisionID', $option, 6, [], ['required'], is_null($obj)?'':$obj['decisionID']);
                                    }
                            
                                        $content .= formLayout::rowInputNew(L('tpb.decisionDate'),'decisionDate', 'decisionDate', 'text',  6, ['customDate'], [], ($obj['decisionDate']=="0000-00-00" || empty($obj['decisionDate'])?"":$obj['decisionDate']));
                                        $content .= formLayout::rowInputNew(L('tpb.approvalValidUntil'),'approvalValidUntil', 'approvalValidUntil', 'text',  6, ['customDate'], ['required'], ($obj['approvalValidUntil']=="0000-00-00" || empty($obj['approvalValidUntil'])?"":$obj['approvalValidUntil']));
                                
                                    $content .= '</div>';
                                
                                    
                                    $content .= "<div class='row'>";
                                        $content .= "<div class='col-md-12 col-lg-12 text-end'>";
                                            $content .= "<button type='button' class='btn btn-md btn-success' id='save_pills-decision'>".L('Save')." ".L('tpb.decisionDetail')."</button>";
                                        $content .= "</div>";                 
                                    $content .= "</div>";                                   

                                $content .= "</form>";
                            $content .= "</div>";     
                            
                            // condition tab
                            $content .= "<div class='tab-pane tpbEditMenuDiv fade' id='pills-condition' role='tabpanel' aria-labelledby='pills-condition-tab'>";
                                $content .= "<div class='row'><div class='col-md-12 col-lg-12 text-end'><button class='btn btn-primary btn-round ms-auto addConditionBtn' data-id='".$request->get->id."'><i class='fa fa-plus'></i></button></div></div>";
                                $content .= "<div class='table-responsive'>";
                                    $content .= "<table id='conditionTable' class='display table table-striped table-hover conditionTable'>";
                                    $content .= tpb::genConditionTableHeader();
                                    $content .= tpb::genConditionTableFooter();
                                    $content .= "<tbody>";                                    
                                    $dataList = tpb::genConditionTableContentData($request->get->id);
                                    foreach($dataList as $listObj) {
                                        $content .= tpb::genConditionTableBodyRow($listObj);                                    
                                    }
                                    $content .= "</tbody>";
                                    $content .= "</table>";
                                $content .= "</div>";

                                $content .= "<div class='row'>";
                                    $content .= formLayout::rowSeparatorLineNew(12);
                                $content .= "</div>";  
                                
                                $selected_condition_month = (isset($_GET['month']) && $_GET['month']!="")?$_GET['month']:date("Y-m");                             

                                 $content .= "<div class='row'>";
                                     $content .= "<div class='col col-sm-12 col-md-3, col-lg-3'>";
                                         $content .= "<input type='text' class='form-control flatpickr-input' id='selected_condition_month' name='selected_condition_month' value='".$selected_condition_month."' readonly>";
                                     $content .= "</div>";
                                 $content .= "</div>";

                                $content .= "<div id='conditionCalendar'><i class='fa fa-spinner fa-spin'></i></div>";  

                            $content .= "</div>";                                   
                            
                            // EOT tab
                            $content .= "<div class='tab-pane tpbEditMenuDiv fade' id='pills-EOT' role='tabpanel' aria-labelledby='pills-EOT-tab'>";
                                $content .= "<div class='row'><div class='col-md-12 col-lg-12 text-end'><button class='btn btn-primary btn-round ms-auto addEotBtn' data-id='".$request->get->id."'><i class='fa fa-plus'></i></button></div></div>";
                                $content .= "<div class='table-responsive'>";
                                    $content .= "<table id='eotTable' class='display table table-striped table-hover eotTable'>";
                                    $content .= tpb::genEOTTableHeader();
                                    $content .= tpb::genEOTTableFooter();
                                    $content .= "<tbody>";                                    
                                    $dataList = tpb::genEOTTableContentData($request->get->id);
                                    foreach($dataList as $listObj) {
                                        $content .= tpb::genEOTTableBodyRow($listObj);                                    
                                    }
                                    $content .= "</tbody>";
                                    $content .= "</table>";
                                $content .= "</div>";
                            $content .= "</div>";   
                            
                            // STT tab
                            $content .= "<div class='tab-pane tpbEditMenuDiv fade' id='pills-STT' role='tabpanel' aria-labelledby='pills-STT-tab'>";
                                $content .= "<div class='row'><div class='col-md-12 col-lg-12 text-end'><button class='btn btn-primary btn-round ms-auto addSttBtn' data-id='".$request->get->id."'><i class='fa fa-plus'></i></button></div></div>";
                                $content .= "<div class='table-responsive'>";
                                    $content .= "<table id='sttTable' class='display table table-striped table-hover sttTable'>";
                                    $content .= stt::genTableHeader();
                                    $content .= stt::genTableFooter();
                                    $content .= "<tbody>";                                    
                                    $dataList = stt::genTableContentData($request->get->id);
                                    foreach($dataList as $listObj) {
                                        $content .= stt::genTableBodyRow($listObj);                                    
                                    }
                                    $content .= "</tbody>";
                                    $content .= "</table>";
                                $content .= "</div>";
                            $content .= "</div>"; 
                            
                            // STW tab
                            $content .= "<div class='tab-pane tpbEditMenuDiv fade' id='pills-STW' role='tabpanel' aria-labelledby='pills-STW-tab'>";
                                $content .= "<div class='row'><div class='col-md-12 col-lg-12 text-end'><button class='btn btn-primary btn-round ms-auto addStwBtn' data-id='".$request->get->id."'><i class='fa fa-plus'></i></button></div></div>";
                                $content .= "<div class='table-responsive'>";
                                    $content .= "<table id='stwTable' class='display table table-striped table-hover stwTable'>";
                                    $content .= stw::genTableHeader();
                                    $content .= stw::genTableFooter();
                                    $content .= "<tbody>";                                    
                                    $dataList = stw::genTableContentData($request->get->id);
                                    foreach($dataList as $listObj) {
                                        $content .= stw::genTableBodyRow($listObj);                                    
                                    }
                                    $content .= "</tbody>";
                                    $content .= "</table>";
                                $content .= "</div>";
                            $content .= "</div>";                             

                        $content .= "</div>";

                    $content .= "</div>"; // end card body

                $content .= "</div>"; // end card
            $content .= "</div>"; // end grad-12
        $content .= "</div>"; // end row

        return new Data(['success'=>true, 'message'=>$content]);
        
    }

    // tpb applicant edit action
    public function applicantEdit($request) {

		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']); 
        
        $currentUserObj = unserialize($_SESSION['user']);

        if (!isset($request->get->id) || empty($request->get->id))
            return new Data(['success'=>false, 'message'=>L('error.tpbEmptyID'), 'field'=>'notice']);

        $tpbObj = self::find($request->get->id);
        if(is_null($tpbObj))
            return new Data(['success'=>false, 'message'=>L('error.tpbNotFound'), 'field'=>'notice']);     

        if (!isset($request->post->clientID) || empty($request->post->clientID)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyClientID'), 'field'=>'clientID', 'tab'=>'pills-applicant']);      
            
        $editFields = [];
		$editValues = [];
        $logContent = [];
        
        if (isset($request->post->clientID) && !empty($request->post->clientID)) {

            $editFields['clientID'] = "?";
            $editValues[] = $request->post->clientID;

            if($request->post->clientID!=$tpbObj->clientID) {
				$logContent[] = [
					"key"=>"clientID", 
					"valueFrom"=>$tpbObj->clientID, 
					"valueTo"=>strip_tags($request->post->clientID)					
				];	
			}			
		}	   

        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('tpb')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
            if (count($logContent)) {
                $logData = [];
                $logData['userID']= $currentUserObj->id;
                $logData['module'] = "TPB";
                $logData['referenceID'] = $request->get->id;
                $logData['action'] = "Update";
                $logData['description'] = "Edit TPB Applicant Section [ID: ".$tpbObj->id."]";
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

    // tpb application edit action
    public function applicationEdit($request) {
        if (!user::checklogin()) 
            return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']); 
    
        $currentUserObj = unserialize($_SESSION['user']);

        if (!isset($request->get->id) || empty($request->get->id))
            return new Data(['success'=>false, 'message'=>L('error.tpbEmptyID'), 'field'=>'notice']);    

        $tpbObj = self::find($request->get->id);
        if(is_null($tpbObj))
            return new Data(['success'=>false, 'message'=>L('error.tpbNotFound'), 'field'=>'notice']);         
        /*
        if (!isset($request->post->refNo) || empty($request->post->refNo)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyRefNo'), 'field'=>'refNo']);           
        */
        if (!isset($request->post->userID) || empty($request->post->userID) || count($request->post->userID)==0) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyOfficer'), 'field'=>'userID']);    
        /*
        if (!isset($request->post->addressDDLot) || empty($request->post->addressDDLot)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyAddressDDLot'), 'field'=>'addressDDLot']);

		if (!isset($request->post->OZPName) || empty($request->post->OZPName)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyOzpName'), 'field'=>'OZPName']);
        
        if (!isset($request->post->OZPNo) || empty($request->post->OZPNo)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyOzpNo'), 'field'=>'OZPNo']);     
                
        if (!isset($request->post->zoningID) || empty($request->post->zoningID) || count($request->post->zoningID)==0) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyZoning'), 'field'=>'zoningID']);     

        if (!isset($request->post->proposedUse) || empty($request->post->proposedUse)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyProposedUse'), 'field'=>'proposedUse']); 
        
        if (!isset($request->files->authorizationLetterDoc) || empty($request->files->authorizationLetterDoc['tmp_name'])) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyAuthorizationLetterDoc'), 'field'=>'authorizationLetterDoc', 'tab'=>'pills-application']);

        if (!isset($request->post->isLandOwner) || empty($request->post->isLandOwner)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyIsLandOwner'), 'field'=>'isLandOwner', 'tab'=>'pills-application']); 

        if ($request->post->isLandOwner=="Y") {

            if (!isset($request->files->landRegistryDoc) || empty($request->files->landRegistryDoc['tmp_name'])) 
			    return new Data(['success'=>false, 'message'=>L('error.tpbEmptyLandRegistryDoc'), 'field'=>'landRegistryDoc', 'tab'=>'pills-application']);

        } else {
            
            if (!isset($request->files->siteNoticeDoc) || empty($request->files->siteNoticeDoc['tmp_name'])) 
			    return new Data(['success'=>false, 'message'=>L('error.tpbEmptySiteNoticeDoc'), 'field'=>'siteNoticeDoc', 'tab'=>'pills-application']);

            if (!isset($request->files->letterToRCDoc) || empty($request->files->letterToRCDoc['tmp_name'])) 
			    return new Data(['success'=>false, 'message'=>L('error.tpbEmptyLetterToRCDoc'), 'field'=>'letterToRCDoc', 'tab'=>'pills-application']);                

        } 
        */

        $editFields = [];
		$editValues = [];
        $logContent = [];

        $authorizationLetterDocID = $tpbObj->authorizationLetterDocID;

        if (isset($request->files->authorizationLetterDoc) && !empty($request->files->authorizationLetterDoc)) {
            $authorizationLetterDocID = documentHelper::upload($request->files->authorizationLetterDoc, "AUTHORIZATION_LETTER");
            $editFields['authorizationLetterDocID'] = "?";
            $editValues[] = $authorizationLetterDocID;  

            if($authorizationLetterDocID>0) {
                $logContent[] = [
					"key"=>"authorizationLetterDocID", 
					"valueFrom"=>$tpbObj->authorizationLetterDocID, 
					"valueTo"=>strip_tags($authorizationLetterDocID)					
				];	
            }
        }

        $landRegistryDocID = $tpbObj->landRegistryDocID;

        if (isset($request->files->landRegistryDoc) && !empty($request->files->landRegistryDoc)) {
            $landRegistryDocID = documentHelper::upload($request->files->landRegistryDoc, "LAND_REGISTRY");
            $editFields['landRegistryDocID'] = "?";
            $editValues[] = $landRegistryDocID; 

            if($landRegistryDocID>0) {
                $logContent[] = [
					"key"=>"landRegistryDocID", 
					"valueFrom"=>$tpbObj->landRegistryDocID, 
					"valueTo"=>strip_tags($landRegistryDocID)					
				];	

            }
        }

        $siteNoticeDocID = $tpbObj->siteNoticeDocID;

        if (isset($request->files->siteNoticeDoc) && !empty($request->files->siteNoticeDoc)) {
            $siteNoticeDocID = documentHelper::upload($request->files->siteNoticeDoc, "SITE_NOTICE");
            $editFields['siteNoticeDocID'] = "?";
            $editValues[] = $siteNoticeDocID; 

            if($siteNoticeDocID>0) {               
                $logContent[] = [
					"key"=>"siteNoticeDocID", 
					"valueFrom"=>$tpbObj->siteNoticeDocID, 
					"valueTo"=>strip_tags($siteNoticeDocID)					
				];	                
            }
        }
        
        $letterToRCDocID = $tpbObj->letterToRCDocID;

        if (isset($request->files->letterToRCDoc) && !empty($request->files->letterToRCDoc)) {
            $letterToRCDocID = documentHelper::upload($request->files->letterToRCDoc, "LETTER_TO_RC");
            $editFields['letterToRCDocID'] = "?";
            $editValues[] = $letterToRCDocID;  

            if($letterToRCDocID>0) {
                $logContent[] = [
					"key"=>"letterToRCDocID", 
					"valueFrom"=>$tpbObj->letterToRCDocID, 
					"valueTo"=>strip_tags($letterToRCDocID)					
				];                
            }
        }    


        if (isset($request->post->refNo) && !empty($request->post->refNo)) {

            $editFields['refNo'] = "?";
            $editValues[] = $request->post->refNo;

            if($request->post->refNo!=$tpbObj->refNo) {
				$logContent[] = [
					"key"=>"refNo", 
					"valueFrom"=>$tpbObj->refNo, 
					"valueTo"=>strip_tags($request->post->refNo)					
				];	
			}			
		}	

        if (isset($request->post->addressDDLot) && !empty($request->post->addressDDLot)) {

            $editFields['addressDDLot'] = "?";
            $editValues[] = $request->post->addressDDLot;

            if($request->post->addressDDLot!=$tpbObj->addressDDLot) {
				$logContent[] = [
					"key"=>"addressDDLot", 
					"valueFrom"=>$tpbObj->addressDDLot, 
					"valueTo"=>strip_tags($request->post->addressDDLot)					
				];	
			}			
		}	

        if (isset($request->post->OZPName) && !empty($request->post->OZPName)) {

            $editFields['OZPName'] = "?";
            $editValues[] = $request->post->OZPName;

            if($request->post->OZPName!=$tpbObj->OZPName) {
				$logContent[] = [
					"key"=>"OZPName", 
					"valueFrom"=>$tpbObj->OZPName, 
					"valueTo"=>strip_tags($request->post->OZPName)					
				];	
			}			
		}	


        if (isset($request->post->OZPNo) && !empty($request->post->OZPNo)) {

            $editFields['OZPNo'] = "?";
            $editValues[] = $request->post->OZPNo;

            if($request->post->OZPNo!=$tpbObj->OZPNo) {
				$logContent[] = [
					"key"=>"OZPNo", 
					"valueFrom"=>$tpbObj->OZPNo, 
					"valueTo"=>strip_tags($request->post->OZPNo)					
				];	
			}			
		}	

        if (isset($request->post->proposedUse) && !empty($request->post->proposedUse)) {

            $editFields['proposedUse'] = "?";
            $editValues[] = $request->post->proposedUse;

            if($request->post->proposedUse!=$tpbObj->proposedUse) {
				$logContent[] = [
					"key"=>"proposedUse", 
					"valueFrom"=>$tpbObj->proposedUse, 
					"valueTo"=>strip_tags($request->post->proposedUse)					
				];	
			}			
		}	
        
        if (isset($request->post->isLandOwner) && !empty($request->post->isLandOwner)) {

            $editFields['isLandOwner'] = "?";
            $editValues[] = $request->post->isLandOwner;

            if($request->post->isLandOwner!=$tpbObj->isLandOwner) {
				$logContent[] = [
					"key"=>"isLandOwner", 
					"valueFrom"=>$tpbObj->isLandOwner, 
					"valueTo"=>strip_tags($request->post->isLandOwner)					
				];	
			}			
		}	       


        if (isset($request->post->remarks) && !empty($request->post->remarks)) {

            $editFields['remarks'] = "?";
            $editValues[] = $request->post->remarks;

            if($request->post->remarks!=$tpbObj->remarks) {
				$logContent[] = [
					"key"=>"remarks", 
					"valueFrom"=>$tpbObj->remarks, 
					"valueTo"=>strip_tags($request->post->remarks)					
				];	
			}			
		}	  


        if (isset($request->post->tpbStatusID) && !empty($request->post->tpbStatusID)) {

            $editFields['tpbStatusID'] = "?";
            $editValues[] = $request->post->tpbStatusID;

            if($request->post->tpbStatusID!=$tpbObj->tpbStatusID) {
				$logContent[] = [
					"key"=>"tpbStatusID", 
					"valueFrom"=>$tpbObj->tpbStatusID, 
					"valueTo"=>strip_tags($request->post->tpbStatusID)					
				];	
			}			
		}	  	
  

		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}  
        
		$sql = Sql::update('tpb')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
            if (count($logContent)) {
                $logData = [];
                $logData['userID']= $currentUserObj->id;
                $logData['module'] = "TPB";
                $logData['referenceID'] = $request->get->id;
                $logData['action'] = "Update";
                $logData['description'] = "Edit TPB Application Section [ID: ".$tpbObj->id."]";
                $logData['sqlStatement'] = $sql;
                $logData['sqlValue'] = $editValues;			
                $logData['changes'] = $logContent;
                systemLog::add($logData); 
            }       

            $currentOfficer = [];
			foreach(self::findOfficer($request->get->id) as $officerObj){
				$currentOfficer[] = $officerObj['userID'];
			}

            $newOfficer = array_diff($request->post->userID, $currentOfficer);

            foreach($newOfficer as $officerID) {
				
                $sql = Sql::insert('tpbOfficer')->setFieldValue([
                    'tpbID' => "?", 
                    'userID' => "?"
                ]);				
				
				$addOfficerValues = [
                    strip_tags($request->get->id),
                    strip_tags($officerID)
                ];

                $sql->prepare()->execute($addOfficerValues);

				$id = db()->lastInsertId();

				$logOfficerData = [];
				$logOfficerData['userID']= $currentUserObj->id;
				$logOfficerData['module'] = "TPB";
				$logOfficerData['referenceID'] = $id;
				$logOfficerData['action'] = "Insert";
                $logOfficerData['description'] = "Assigned Officer To TPB [ID: ".$tpbObj->id."]";
                $logOfficerData['sqlStatement'] = $sql;
                $logOfficerData['sqlValue'] = $addOfficerValues;
				$logOfficerData['changes'] = 
					[
						[
							"key"=>"userID", 
							"valueFrom"=>"", 
							"valueTo"=>strip_tags($officerID)
						]
					];
	
				systemLog::add($logOfficerData);

            }             

			$removeOfficer = array_diff($currentOfficer, $request->post->userID);
			foreach($removeOfficer as $officerID) {

				$officerObj = self::findByTPBIDUserID($request->get->id, $officerID);

				$sql = Sql::delete('tpbOfficer')->where(['id', '=', $officerObj->id]);
				if ($sql->prepare()->execute()) {		
					$logData = [];
					$logData['userID']= $currentUserObj->id;
					$logData['module'] = "TPB";
					$logData['referenceID'] = $officerObj->id;
					$logData['action'] = "Delete";
					$logData['description'] = "Remove Assigned Officer From TPB [ID: ".$tpbObj->id."]";
					$logData['sqlStatement'] = $sql;
					$logData['sqlValue'] = $request->get->id;
					$logData['changes'] = [];
					systemLog::add($logData);	
				} 				

			}





            $currentZoning = [];
			foreach(self::findZoning($request->get->id) as $zoningObj){
				$currentZoning[] = $zoningObj['zoningID'];
			}

            $newZoning = array_diff($request->post->zoningID, $currentZoning);

            foreach($newZoning as $zoningID) {
				
                $sql = Sql::insert('tpbZoning')->setFieldValue([
                    'tpbID' => "?", 
                    'zoningID' => "?"
                ]);				
				
				$addZoningValues = [
                    strip_tags($request->get->id),
                    strip_tags($zoningID)
                ];

                $sql->prepare()->execute($addZoningValues);

				$id = db()->lastInsertId();

				$logZoningData = [];
				$logZoningData['userID']= $currentUserObj->id;
				$logZoningData['module'] = "TPB";
				$logZoningData['referenceID'] = $id;
				$logZoningData['action'] = "Insert";
                $logZoningData['description'] = "Assigned Zoning To TPB [ID: ".$tpbObj->id."]";
                $logZoningData['sqlStatement'] = $sql;
                $logZoningData['sqlValue'] = $addZoningValues;
				$logZoningData['changes'] = 
					[
						[
							"key"=>"zoningID", 
							"valueFrom"=>"", 
							"valueTo"=>strip_tags($zoningID)
						]
					];
	
				systemLog::add($logZoningData);

            }             

			$removeZoning = array_diff($currentZoning, $request->post->zoningID);
			foreach($removeZoning as $zoningID) {

				$zoningObj = self::findByTPBIDZoningID($request->get->id, $officerID);

				$sql = Sql::delete('tpbZoning')->where(['id', '=', $officerObj->id]);
				if ($sql->prepare()->execute()) {		
					$logData = [];
					$logData['userID']= $currentUserObj->id;
					$logData['module'] = "TPB";
					$logData['referenceID'] = $zoningObj->id;
					$logData['action'] = "Delete";
					$logData['description'] = "Remove Assigned Zoning From TPB [ID: ".$tpbObj->id."]";
					$logData['sqlStatement'] = $sql;
					$logData['sqlValue'] = $request->get->id;
					$logData['changes'] = [];
					systemLog::add($logData);	
				} 				

			}

			return new Data([
                'success'=>true, 
                'message'=>L('info.updated'), 
                'authorizationLetterDocID'=>$authorizationLetterDocID, 
                'authorizationLetterDocPath'=>documentHelper::find($authorizationLetterDocID)->downloadPath,
                'landRegistryDocID'=>$landRegistryDocID, 
                'landRegistryDocPath'=>documentHelper::find($landRegistryDocID)->downloadPath,
                'siteNoticeDocID'=>$siteNoticeDocID, 
                'siteNoticeDocPath'=>documentHelper::find($siteNoticeDocID)->downloadPath,
                'letterToRCDocID'=>$letterToRCDocID, 
                'letterToRCDocPath'=>documentHelper::find($letterToRCDocID)->downloadPath
            ]);			
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableUpdate'), 'field'=>'notice']);
		}	        
    }

    // tpb submission edit action
    public function submissionEdit($request) {

		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']); 
        
        $currentUserObj = unserialize($_SESSION['user']);

        if (!isset($request->get->id) || empty($request->get->id))
            return new Data(['success'=>false, 'message'=>L('error.tpbEmptyID'), 'field'=>'notice']);

        $tpbObj = self::find($request->get->id);
        if(is_null($tpbObj))
            return new Data(['success'=>false, 'message'=>L('error.tpbNotFound'), 'field'=>'notice']);     
        
        /*
        if (!isset($request->post->submissionDate) || empty($request->post->submissionDate)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptySubmissionDate'), 'field'=>'submissionDate', 'tab'=>'pills-submission']);   

        if (!isset($request->post->submissionModeID) || empty($request->post->submissionModeID)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptySubmissionMode'), 'field'=>'submissionModeID', 'tab'=>'pills-submission']);  

        if (!isset($request->post->rntpcID) || empty($request->post->rntpcID)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyRntpc'), 'field'=>'rntpcID', 'tab'=>'pills-submission']);            
        */

        $editFields = [];
        $editValues = [];
        $logContent = [];

        $submissionDocID = $tpbObj->submissionDocID;

        if (isset($request->files->submissionDoc) && !empty($request->files->submissionDoc)) {
            $submissionDocID = documentHelper::upload($request->files->submissionDoc, "SUBMISSION");
            $editFields['submissionDocID'] = "?";
            $editValues[] = $submissionDocID;  

            if($submissionDocID>0) {
				$logContent[] = [
					"key"=>"submissionDocID", 
					"valueFrom"=>$tpbObj->submissionDocID, 
					"valueTo"=>strip_tags($submissionDocID)					
				];	                
            }
        }


        if (isset($request->post->rntpcID) && !empty($request->post->rntpcID)) {

            $editFields['rntpcID'] = "?";
            $editValues[] = $request->post->rntpcID;

            if($request->post->rntpcID!=$tpbObj->rntpcID) {
				$logContent[] = [
					"key"=>"rntpcID", 
					"valueFrom"=>$tpbObj->rntpcID, 
					"valueTo"=>strip_tags($request->post->rntpcID)					
				];	
			}			
		}	   
        
        if (isset($request->post->submissionDate) && !empty($request->post->submissionDate)) {

            $editFields['submissionDate'] = "?";
            $editValues[] = $request->post->submissionDate;

            if($request->post->submissionDate!=$tpbObj->submissionDate) {
				$logContent[] = [
					"key"=>"submissionDate", 
					"valueFrom"=>$tpbObj->submissionDate, 
					"valueTo"=>strip_tags($request->post->submissionDate)					
				];	
			}			
		}	 
        
        if (isset($request->post->submissionModeID) && !empty($request->post->submissionModeID)) {

            $editFields['submissionModeID'] = "?";
            $editValues[] = $request->post->submissionModeID;

            if($request->post->submissionModeID!=$tpbObj->submissionModeID) {
				$logContent[] = [
					"key"=>"submissionModeID", 
					"valueFrom"=>$tpbObj->submissionModeID, 
					"valueTo"=>strip_tags($request->post->submissionModeID)					
				];	
			}			
		}	                
        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('tpb')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
            if (count($logContent)) {
                $logData = [];
                $logData['userID']= $currentUserObj->id;
                $logData['module'] = "TPB";
                $logData['referenceID'] = $request->get->id;
                $logData['action'] = "Update";
                $logData['description'] = "Edit TPB Submission Section [ID: ".$tpbObj->id."]";
                $logData['sqlStatement'] = $sql;
                $logData['sqlValue'] = $editValues;			
                $logData['changes'] = $logContent;
                systemLog::add($logData);  
            }
			return new Data(['success'=>true, 'message'=>L('info.updated'), 'submissionDocID'=>$submissionDocID, 'submissionDocPath'=>documentHelper::find($submissionDocID)->downloadPath]);			
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableUpdate'), 'field'=>'notice']);
		}		                
    }    

    // tpb receive edit action
    public function receiveEdit($request) {

		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']); 
        
        $currentUserObj = unserialize($_SESSION['user']);

        if (!isset($request->get->id) || empty($request->get->id))
            return new Data(['success'=>false, 'message'=>L('error.tpbEmptyID'), 'field'=>'notice']);

        $tpbObj = self::find($request->get->id);

        if(is_null($tpbObj))
            return new Data(['success'=>false, 'message'=>L('error.tpbNotFound'), 'field'=>'notice']);     
        
        /*
        if (!isset($request->post->TPBNo) || empty($request->post->TPBNo)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyNumber'), 'field'=>'TPBNo']);

		if (!isset($request->post->TPBWebsite) || empty($request->post->TPBWebsite)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyWebsite'), 'field'=>'TPBWebsite']);
        
        if (!isset($request->post->TPBReceiveDate) || empty($request->post->TPBReceiveDate)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyReceiveDate'), 'field'=>'TPBReceiveDate']);     
                
        if (!isset($request->post->tentativeConsiderationDate) || empty($request->post->tentativeConsiderationDate)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyConsiderationDate'), 'field'=>'tentativeConsiderationDate']); 
        */
        if (!empty($request->post->TPBNo) && empty($request->post->TPBWebsite)) {
            $request->post->TPBWebsite = "https://www.tpb.gov.hk/tc/plan_application/".$request->post->TPBNo.".html";
        }
        
        $editFields = [];
		$editValues = [];
        $logContent = [];

        if (isset($request->post->TPBNo) && !empty($request->post->TPBNo)) {

            $editFields['TPBNo'] = "?";
            $editValues[] = $request->post->TPBNo;

            if($request->post->TPBNo!=$tpbObj->TPBNo) {
				$logContent[] = [
					"key"=>"TPBNo", 
					"valueFrom"=>$tpbObj->TPBNo, 
					"valueTo"=>strip_tags($request->post->TPBNo)					
				];	
			}			
		}	     

        if (isset($request->post->TPBWebsite) && !empty($request->post->TPBWebsite)) {

            $editFields['TPBWebsite'] = "?";
            $editValues[] = $request->post->TPBWebsite;

            if($request->post->TPBWebsite!=$tpbObj->TPBWebsite) {
				$logContent[] = [
					"key"=>"TPBWebsite", 
					"valueFrom"=>$tpbObj->TPBWebsite, 
					"valueTo"=>strip_tags($request->post->TPBWebsite)					
				];	
			}			
		}	
	
        if (isset($request->post->TPBReceiveDate) && !empty($request->post->TPBReceiveDate)) {

            $editFields['TPBReceiveDate'] = "?";
            $editValues[] = $request->post->TPBReceiveDate;

            if($request->post->TPBReceiveDate!=$tpbObj->TPBReceiveDate) {
				$logContent[] = [
					"key"=>"TPBReceiveDate", 
					"valueFrom"=>$tpbObj->TPBReceiveDate, 
					"valueTo"=>strip_tags($request->post->TPBReceiveDate)					
				];	
			}			
		}	
        
        if (isset($request->post->tentativeConsiderationDate) && !empty($request->post->tentativeConsiderationDate)) {

            $editFields['tentativeConsiderationDate'] = "?";
            $editValues[] = $request->post->tentativeConsiderationDate;

            if($request->post->tentativeConsiderationDate!=$tpbObj->tentativeConsiderationDate) {
				$logContent[] = [
					"key"=>"tentativeConsiderationDate", 
					"valueFrom"=>$tpbObj->tentativeConsiderationDate, 
					"valueTo"=>strip_tags($request->post->tentativeConsiderationDate)					
				];	
			}			
		}	        

		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('tpb')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
            if (count($logContent)) {
                $logData = [];
                $logData['userID']= $currentUserObj->id;
                $logData['module'] = "TPB";
                $logData['referenceID'] = $request->get->id;
                $logData['action'] = "Update";
                $logData['description'] = "Edit TPB Receive Section [ID: ".$tpbObj->id."]";
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

    // tpb decision edit action
    public function decisionEdit($request) {

		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']); 
        
        $currentUserObj = unserialize($_SESSION['user']);

        if (!isset($request->get->id) || empty($request->get->id))
            return new Data(['success'=>false, 'message'=>L('error.tpbEmptyID'), 'field'=>'notice']);

        $tpbObj = self::find($request->get->id);

        if(is_null($tpbObj))
            return new Data(['success'=>false, 'message'=>L('error.tpbNotFound'), 'field'=>'notice']);     
        
        /*
        if (!isset($request->post->decisionID) || empty($request->post->decisionID)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyDecision'), 'field'=>'decisionID']);

		if (!isset($request->post->decisionDate) || empty($request->post->decisionDate)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyDecisionDate'), 'field'=>'decisionDate']);
        
        if (!isset($request->post->approvalValidUntil) || empty($request->post->approvalValidUntil)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyApprovalValidUntil'), 'field'=>'approvalValidUntil']);     


        if ($request->post->decisionID==2) {
            if(count($request->post->condition)==0) {
                return new Data(['success'=>false, 'message'=>L('error.tpbEmptyCondition'), 'field'=>'condition_0']);     
            }

            foreach($request->post->condition as $ids => $condition){
                if($condition=="") {
                    return new Data(['success'=>false, 'message'=>L('error.tpbEmptyCondition'), 'field'=>'condition_'.$ids]);     
                }

                if($request->post->deadline[$ids]=="") {
                    return new Data(['success'=>false, 'message'=>L('error.tpbEmptyDeadline'), 'field'=>'condition_'.$ids]);     
                }               
            }
        }
        */
        
        $editFields = [];
		$editValues = [];
		$logContent = [];	

        if (isset($request->post->decisionID) && !empty($request->post->decisionID)) {

            $editFields['decisionID'] = "?";
            $editValues[] = $request->post->decisionID;

            if($request->post->decisionID!=$tpbObj->decisionID) {
				$logContent[] = [
					"key"=>"decisionID", 
					"valueFrom"=>$tpbObj->decisionID, 
					"valueTo"=>strip_tags($request->post->decisionID)					
				];	
			}			
		}	


        if (isset($request->post->decisionDate) && !empty($request->post->decisionDate)) {

            $editFields['decisionDate'] = "?";
            $editValues[] = $request->post->decisionDate;

            if($request->post->decisionDate!=$tpbObj->decisionDate) {
				$logContent[] = [
					"key"=>"decisionDate", 
					"valueFrom"=>$tpbObj->decisionDate, 
					"valueTo"=>strip_tags($request->post->decisionDate)					
				];	
			}			
		}	


        if (isset($request->post->approvalValidUntil) && !empty($request->post->approvalValidUntil)) {

            $editFields['approvalValidUntil'] = "?";
            $editValues[] = $request->post->approvalValidUntil;

            if($request->post->approvalValidUntil!=$tpbObj->approvalValidUntil) {
				$logContent[] = [
					"key"=>"approvalValidUntil", 
					"valueFrom"=>$tpbObj->approvalValidUntil, 
					"valueTo"=>strip_tags($request->post->approvalValidUntil)					
				];	
			}			
		}	

        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('tpb')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
            if (count($logContent)) {
                $logData = [];
                $logData['userID']= $currentUserObj->id;
                $logData['module'] = "TPB";
                $logData['referenceID'] = $request->get->id;
                $logData['action'] = "Update";
                $logData['description'] = "Edit TPB Decision Section [ID: ".$tpbObj->id."]";
                $logData['sqlStatement'] = $sql;
                $logData['sqlValue'] = $editValues;			
                $logData['changes'] = $logContent;
                systemLog::add($logData);            
            }

            if($request->post->decisionID=="1") {
                self::updateTBPStatus($request->get->id, 5);
            }

            if($request->post->decisionID=="2") {
                self::updateTBPStatus($request->get->id, 4);
            }            

            if($request->post->decisionID=="3") {
                self::updateTBPStatus($request->get->id, 6);
            }

			return new Data(['success'=>true, 'message'=>L('info.updated')]);			
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableUpdate'), 'field'=>'notice']);
		}	
    }       

    // tpb condition edit action
    /*
    public function conditionEdit($request) {

		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']); 
        
        $currentUserObj = unserialize($_SESSION['user']);

        if (!isset($request->get->id) || empty($request->get->id))
            return new Data(['success'=>false, 'message'=>L('error.tpbEmptyID'), 'field'=>'notice']);

        $tpbObj = self::find($request->get->id);
        if(is_null($tpbObj))
            return new Data(['success'=>false, 'message'=>L('error.tpbNotFound'), 'field'=>'notice']);     


        // delete removed 
        $conditionList = self::findCondition($request->get->id);
        foreach($conditionList as $oriCondition){			
            if(!in_array($oriCondition['id'], $request->post->lineStatus)){				
                
                $delSql = Sql::delete('tpbCondition')->where(['id', '=', "'".$oriCondition['id']."'"]);
                if($delSql->execute()){
                    ;
                }
                
            }
        }            

        // all visible media
        foreach($request->post->lineStatus as $idx => $status){

            if($status==0){ // new item
                $sql = Sql::insert('tpbCondition')->setFieldValue([
                    'tpbID' => "?", 
                    'conditionNo' => "?",
                    'description' => "?",
                    'deadline' => "?",
                    'status' => "?",
                    'createBy'=>$currentUserObj->id, 
                    'modifyBy'=>$currentUserObj->id                    
                ]);               

                $sql->prepare()->execute([
                    strip_tags($request->get->id),
                    strip_tags($request->post->number[$idx]),
                    strip_tags($request->post->description[$idx]),
                    strip_tags($request->post->deadline[$idx]),
                    strip_tags($request->post->generalStatus[$idx])
                ]);
                
            } else if($status>0) { //edit record

                $editFields = [];
                $editValues = [];
                
                if (isset($request->post->number[$idx]) && !empty($request->post->number[$idx])) {		
                    $editFields['conditionNo'] = "?";
                    $editValues[] = strip_tags($request->post->number[$idx]);
                }

                if (isset($request->post->description[$idx]) && !empty($request->post->description[$idx])) {		
                    $editFields['description'] = "?";
                    $editValues[] = strip_tags($request->post->description[$idx]);
                }

                if (isset($request->post->deadline[$idx]) && !empty($request->post->deadline[$idx])) {		
                    $editFields['deadline'] = "?";
                    $editValues[] = strip_tags($request->post->deadline[$idx]);
                }  
                
                if (isset($request->post->generalStatus[$idx]) && !empty($request->post->generalStatus[$idx])) {		
                    $editFields['status'] = "?";
                    $editValues[] = strip_tags($request->post->generalStatus[$idx]);
                }                   

                if (count($editFields)) {
                    $editFields['modifyDate'] = "NOW()";
                    $editFields['modifyBy'] = $currentUserObj->id;
                }
                
                $sql = Sql::update('tpbCondition')->setFieldValue($editFields)->where(['id', '=', $status]);

                if ($sql->prepare()->execute($editValues)) {
                    ;
                }

            }
            
        }        
        /*
        $editFields = [];
		$editValues = [];

		if (isset($request->post->decisionID) && !empty($request->post->decisionID)) {
			$editFields['decisionID'] = "?";
			$editValues[] = $request->post->decisionID;
		}		
        
		if (isset($request->post->decisionDate) && !empty($request->post->decisionDate)) {
			$editFields['decisionDate'] = "?";
			$editValues[] = $request->post->decisionDate;
		}		
        
		if (isset($request->post->approvalValidUntil) && !empty($request->post->approvalValidUntil)) {
			$editFields['approvalValidUntil'] = "?";
			$editValues[] = $request->post->approvalValidUntil;
		}	    
        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}           

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('tpb')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
			return new Data(['success'=>true, 'message'=>L('info.updated')]);			
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableUpdate'), 'field'=>'notice']);
		}	
            


        return new Data(['success'=>true, 'message'=>L('info.updated')]);		    
    }       
    */

    // eot add form 
    public function eotFormAdd($request) {
		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$formName = "form-addEOT";

		$content = "<form id='".$formName."' class='' autocomplete='off'>";
            $content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.eotAddHelperMessage')."</p></div>";

            $content .= "<div class='row'>";

                $content .= formLayout::rowInputNew(L('tpb.TPBNo'),'tpbID', 'tpbID', 'hidden',  6, [], [], $request->get->id);   

                $option = [];
                $stm = Sql::select('tpbCondition')->where(['tpbID', '=', $request->get->id])->prepare();
                $stm->execute();                                          
                foreach ($stm as $opt) {  
                    $option[$opt['id']] = "#".$opt['conditionNo'].": ".$opt['description']." - [".$opt['deadline']."]";			  
                }
                $content .= formLayout::rowMultiSelectNew(L('tpb.condition'), 'conditionID[]', 'conditionID', $option,  12, ['select2'], ['required']); 


                $content .= formLayout::rowInputNew(L('eot.extendMonth'),'extendMonth', 'extendMonth', 'text',  6, [], ['required']);               
               

                $content .= formLayout::rowTextAreaNew(L('eot.reason'), 'reason', 'reason',  12, [], []);

                $content .= formLayout::rowInputNew(L('eot.submissionDate'),'submissionDate', 'submissionDate', 'text',  6, ['customDate'], []);  


                $option = [""=>""];
                $stm = Sql::select('submissionMode')->prepare();
                $stm->execute();                                          
                foreach ($stm as $opt) {  
                    $option[$opt['id']] = $opt['name'];
                }
                $content .= formLayout::rowSelectNew(L('eot.submissionMode'), 'submissionModeID', 'submissionModeID', $option, 6, [], []); 

                $content .= formLayout::rowInputNew(L('eot.submissionDoc'),'EOTDoc', 'EOTDoc', 'file',  6, [], ['accept="image/*, application/pdf"']);

                $option = [];
                $stm = Sql::select('generalStatus')->prepare();
                $stm->execute();                                          
                foreach ($stm as $opt) {  
                    $option[$opt['id']] = $opt['name'];
                }
                $content .= formLayout::rowSelectNew(L('Status'), 'status', 'status', $option, 6, [], []); 

         
            $content .= "</div>";

		$content .= "</form>";

		return new Data(['success'=>true, 'message'=>$content]);

    }

    // eot add action
    public function eotAdd($request) {	
       
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

        $currentUserObj = unserialize($_SESSION['user']);

        // form check	        
        if (!isset($request->post->tpbID) || empty($request->post->tpbID)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyID'), 'field'=>'tpbID', 'tab'=>'pills-EOT']);

        if (!isset($request->post->conditionID) || empty($request->post->conditionID) || count($request->post->conditionID)==0) 
			return new Data(['success'=>false, 'message'=>L('error.eotEmptyCondition'), 'field'=>'conditionID', 'tab'=>'pills-EOT']);

        if (!isset($request->post->extendMonth) || empty($request->post->extendMonth)) 
			return new Data(['success'=>false, 'message'=>L('error.eotEmptyExtendMonth'), 'field'=>'extendMonth', 'tab'=>'pills-EOT']);
        /*
        if (!isset($request->post->reason) || empty($request->post->reason)) 
			return new Data(['success'=>false, 'message'=>L('error.eotEmptyReaon'), 'field'=>'reason', 'tab'=>'pills-EOT']);

        if (!isset($request->post->submissionDate) || empty($request->post->submissionDate)) 
			return new Data(['success'=>false, 'message'=>L('error.eotEmptySubmissionDate'), 'field'=>'submissionDate', 'tab'=>'pills-EOT']);            

        if (!isset($request->post->submissionModeID) || empty($request->post->submissionModeID)) 
			return new Data(['success'=>false, 'message'=>L('error.eotEmptySubmissionMode'), 'field'=>'submissionModeID', 'tab'=>'pills-EOT']);                   
        */              
        // file upload
        $EOTDocID = 0;        

        if (isset($request->files->EOTDoc) && !empty($request->files->EOTDoc)) {
            $EOTDocID = documentHelper::upload($request->files->EOTDoc, "EOT");
        }

        $sql = Sql::insert('tpbEOT')->setFieldValue([
            'tpbID' => "?", 
            'conditionID' => "?", 
            'extendMonth' => "?", 
            'reason' => "?", 
            'submissionDate' => "?", 
            'submissionModeID' => "?", 
            'EOTDocID' => "?",
            'status' => "?",
            'createBy'=>$currentUserObj->id, 
            'modifyBy'=>$currentUserObj->id
        ]);

        $addValues = [
            strip_tags($request->post->tpbID),
            strip_tags(json_encode($request->post->conditionID)),
            strip_tags($request->post->extendMonth),
            strip_tags($request->post->reason),
            strip_tags($request->post->submissionDate),
            strip_tags($request->post->submissionModeID),
            strip_tags($EOTDocID),
            strip_tags($request->post->status),
        ];

        if ($sql->prepare()->execute($addValues)) {    
            $eotID = db()->lastInsertId();   
            
            $logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "EOT";
			$logData['referenceID'] = $eotID;
			$logData['action'] = "Insert";
			$logData['description'] = "Create New EOT [ID: ".$eotID."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $addValues;
			$logData['changes'] = 
				[
					[
						"key"=>"tpbID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->tpbID)
                    ],[
						"key"=>"conditionID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags(json_encode($request->post->conditionID))
                    ],[
						"key"=>"extendMonth", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->extendMonth)
                    ],[
						"key"=>"reason", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->reason)
                    ],[
						"key"=>"submissionDate", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->submissionDate)
                    ],[
						"key"=>"submissionModeID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->submissionModeID)
                    ],[
						"key"=>"EOTDocID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($EOTDocID)
                    ],[
						"key"=>"status", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->status)
                    ]
				];

			systemLog::add($logData);  

            return new Data(['success'=>true, 'message'=>L('info.saved'), 'id'=>$eotID]);
        } else {
            return new Data(['success'=>false, 'message'=>L('error.unableInsert'), 'field'=>'notice']);
        }	
         
        
	}    

    // eot edit form 
    public function eotFormEdit($request) {
        
		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) {
			$obj = self::getEOTDetail($request->get->id, \PDO::FETCH_NAMED);        

            $obj['conditions'] = null;
            $conditionIDList = json_decode($obj['conditionID']);
            foreach($conditionIDList as $conditionID) {
                $obj['conditions'][] = $conditionID;
            }               
        }

        
		$formName = "form-editEOT";

		$content = "<form id='".$formName."' class='' autocomplete='off'>";
            $content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.eotAddHelperMessage')."</p></div>";

            $content .= "<div class='row'>";

                $option = [];
                $stm = Sql::select('tpbCondition')->where(['tpbID', '=', $request->get->tpbID])->prepare();
                $stm->execute();                                          
                foreach ($stm as $opt) {  
                    $option[$opt['id']] = "#".$opt['conditionNo'].": ".$opt['description']." - [".$opt['deadline']."]";			  
                }
               
                $content .= formLayout::rowMultiSelectNew(L('tpb.condition'), 'conditionID[]', 'conditionID', $option,  12, ['select2'], ['required'], is_null($obj)?[]:$obj['conditions']); 

                $content .= formLayout::rowInputNew(L('eot.extendMonth'),'extendMonth', 'extendMonth', 'text',  6, [], ['required'], is_null($obj)?'':$obj['extendMonth']);               

                $content .= formLayout::rowTextAreaNew(L('eot.reason'), 'reason', 'reason',  12, [], [], is_null($obj)?'':$obj['reason']);

                $content .= formLayout::rowInputNew(L('eot.submissionDate'),'submissionDate', 'submissionDate', 'text',  6, ['customDate'], [], ($obj['submissionDate']=="0000-00-00" || empty($obj['submissionDate'])?"":$obj['submissionDate']));  


                $option = [""=>""];
                $stm = Sql::select('submissionMode')->prepare();
                $stm->execute();                                          
                foreach ($stm as $opt) {  
                    $option[$opt['id']] = $opt['name'];
                }
                $content .= formLayout::rowSelectNew(L('eot.submissionMode'), 'submissionModeID', 'submissionModeID', $option, 6, [], [], is_null($obj)?'':$obj['submissionModeID']); 
            $content .= "</div>";

            $content .= "<div class='row'>";
                $content .= formLayout::rowInputNew(L('eot.submissionDoc'),'EOTDoc', 'EOTDoc', 'file',  6, [], ['accept="image/*, application/pdf"']);
                if(isset($obj['EOTDocID']) && $obj['EOTDocID']>0) {
                    $content .= formLayout::rowDisplayClearGroupLineNew('<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs mt-2" data-id="'.$obj['EOTDocID'].'"><i class="fas fa-download"></i></button><button type="button" class="btn btn-danger removeDoc btn-xs mt-2" data-id="'.$obj['EOTDocID'].'" data-eot="'.$obj['id'].'" data-doc="EOTDocID"><i class="fas fa-trash"></i></button></div>',6);
                } else {
                    $content .= formLayout::rowDisplayClearGroupLineNew('<div class="d-flex gap-2 btnGrp" style="display: none !important"><button type="button" class="btn btn-black downloadDoc btn-xs mt-2" data-id="0"><i class="fas fa-download"></i></button><button type="button" class="btn btn-danger removeDoc btn-xs mt-2" data-id="0" data-eot="'.$obj['id'].'" data-doc="EOTDocID"><i class="fas fa-trash"></i></button></div>',6);
                }                                    
            $content .= "</div>";  


            $content .= "<div class='row'>";
                $option = [];
                $stm = Sql::select('generalStatus')->prepare();
                $stm->execute();                                          
                foreach ($stm as $opt) {  
                    $option[$opt['id']] = $opt['name'];
                }
                $content .= formLayout::rowSelectNew(L('Status'), 'status', 'status', $option, 6, [], [], is_null($obj)?'':$obj['status']); 

         
            $content .= "</div>";

		$content .= "</form>";

		return new Data(['success'=>true, 'message'=>$content]);

    }    

    // eot edit action
    public function eotEdit($request) {	
        
        if (!user::checklogin()) 
            return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

        $currentUserObj = unserialize($_SESSION['user']);

        // form check	        
        if (!isset($request->get->id) || empty($request->get->id)) 
            return new Data(['success'=>false, 'message'=>L('error.eotEmptyID'), 'field'=>'eotID', 'tab'=>'pills-EOT']);

        $eotObj = self::getEOTDetail($request->get->id);
        if(is_null($eotObj))
            return new Data(['success'=>false, 'message'=>L('error.eotNotFound'), 'field'=>'notice']);               

        if (!isset($request->post->conditionID) || empty($request->post->conditionID) || count($request->post->conditionID)==0) 
            return new Data(['success'=>false, 'message'=>L('error.eotEmptyCondition'), 'field'=>'conditionID', 'tab'=>'pills-EOT']);

        if (!isset($request->post->extendMonth) || empty($request->post->extendMonth)) 
            return new Data(['success'=>false, 'message'=>L('error.eotEmptyExtendMonth'), 'field'=>'extendMonth', 'tab'=>'pills-EOT']);
        /*
        if (!isset($request->post->reason) || empty($request->post->reason)) 
            return new Data(['success'=>false, 'message'=>L('error.eotEmptyReaon'), 'field'=>'reason', 'tab'=>'pills-EOT']);

        if (!isset($request->post->submissionDate) || empty($request->post->submissionDate)) 
            return new Data(['success'=>false, 'message'=>L('error.eotEmptySubmissionDate'), 'field'=>'submissionDate', 'tab'=>'pills-EOT']);            

        if (!isset($request->post->submissionModeID) || empty($request->post->submissionModeID)) 
            return new Data(['success'=>false, 'message'=>L('error.eotEmptySubmissionMode'), 'field'=>'submissionModeID', 'tab'=>'pills-EOT']);                   
        */              

        $editFields = [];
		$editValues = [];
		$logContent = [];	

        // file upload        
        $EOTDocID = $eotObj->EOTDocID;

        if (isset($request->files->EOTDoc) && !empty($request->files->EOTDoc)) {
            $EOTDocID = documentHelper::upload($request->files->EOTDoc, "EOT");
            $editFields['EOTDocID'] = "?";
            $editValues[] = $EOTDocID;  

            if($EOTDocID>0) {
				$logContent[] = [
					"key"=>"EOTDocID", 
					"valueFrom"=>$eotObj->EOTDocID, 
					"valueTo"=>strip_tags($EOTDocID)					
				];	                
            }
        }

        if (isset($request->post->conditionID) && !empty($request->post->conditionID)) {

            $editFields['conditionID'] = "?";
            $editValues[] = json_encode($request->post->conditionID);

            if($request->post->conditionID!=$eotObj->conditionID) {
				$logContent[] = [
					"key"=>"conditionID", 
					"valueFrom"=>$eotObj->conditionID, 
					"valueTo"=>strip_tags(json_encode($request->post->conditionID))					
				];	
			}			
		}	 

        if (isset($request->post->extendMonth) && !empty($request->post->extendMonth)) {

            $editFields['extendMonth'] = "?";
            $editValues[] = $request->post->extendMonth;

            if($request->post->extendMonth!=$eotObj->extendMonth) {
				$logContent[] = [
					"key"=>"extendMonth", 
					"valueFrom"=>$eotObj->extendMonth, 
					"valueTo"=>strip_tags($request->post->extendMonth)					
				];	
			}			
		}	

        if (isset($request->post->reason) && !empty($request->post->reason)) {

            $editFields['reason'] = "?";
            $editValues[] = $request->post->reason;

            if($request->post->reason!=$eotObj->reason) {
				$logContent[] = [
					"key"=>"reason", 
					"valueFrom"=>$eotObj->reason, 
					"valueTo"=>strip_tags($request->post->reason)					
				];	
			}			
		}	

        if (isset($request->post->submissionDate) && !empty($request->post->submissionDate)) {

            $editFields['submissionDate'] = "?";
            $editValues[] = $request->post->submissionDate;

            if($request->post->submissionDate!=$eotObj->submissionDate) {
				$logContent[] = [
					"key"=>"submissionDate", 
					"valueFrom"=>$eotObj->submissionDate, 
					"valueTo"=>strip_tags($request->post->submissionDate)					
				];	
			}			
		}	

        if (isset($request->post->submissionModeID) && !empty($request->post->submissionModeID)) {

            $editFields['submissionModeID'] = "?";
            $editValues[] = $request->post->submissionModeID;

            if($request->post->submissionModeID!=$eotObj->submissionModeID) {
				$logContent[] = [
					"key"=>"submissionModeID", 
					"valueFrom"=>$eotObj->submissionModeID, 
					"valueTo"=>strip_tags($request->post->submissionModeID)					
				];	
			}			
		}	

        if (isset($request->post->status) && !empty($request->post->status)) {

            $editFields['status'] = "?";
            $editValues[] = $request->post->status;

            if($request->post->status!=$eotObj->status) {
				$logContent[] = [
					"key"=>"status", 
					"valueFrom"=>$eotObj->status, 
					"valueTo"=>strip_tags($request->post->status)					
				];	
			}			
		}	

        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('tpbEOT')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
            if (count($logContent)) {
                $logData = [];
                $logData['userID']= $currentUserObj->id;
                $logData['module'] = "EOT";
                $logData['referenceID'] = $request->get->id;
                $logData['action'] = "Update";
                $logData['description'] = "Edit EOT [ID: ".$eotObj->id."]";
                $logData['sqlStatement'] = $sql;
                $logData['sqlValue'] = $editValues;			
                $logData['changes'] = $logContent;
                systemLog::add($logData);                
            }

            if($eotObj->status!=$request->post->status) {
                if($request->post->status==2) { // aproved
                    // update related condition dead line
                    foreach($request->post->conditionID as $conditionID){
                        $conditionObj = self::getConditionDetail($conditionID);
                        $newConditionDeadline = date('Y-m-d H:i:s', strtotime('+'.$eotObj->extendMonth.' months', strtotime($conditionObj->deadline)));
                        $sql = Sql::update('tpbCondition')->setFieldValue(['deadline'=>'"'.$newConditionDeadline.'"'])->where(['id', '=', $conditionID])->where(['status', '=', 1]);
                        if($sql->prepare()->execute()){
                            $logData = [];
                            $logData['userID']= $currentUserObj->id;
                            $logData['module'] = "Condition";
                            $logData['referenceID'] = $conditionID;
                            $logData['action'] = "Update";
                            $logData['description'] = "Approved EOT [ID: ".$eotObj->id."]";
                            $logData['sqlStatement'] = $sql;
                            $logData['sqlValue'] = $newConditionDeadline;			
                            $logData['changes'] = [[
                                "key"=>"deadline", 
                                "valueFrom"=>$conditionObj->deadline, 
                                "valueTo"=>strip_tags($newConditionDeadline)					
                            ]];
                            systemLog::add($logData);
                        }

                        // update related task dead line 
                        $taskList = task::findByConditionID($conditionID);
                        foreach($taskList as $task) {
                            $newTaskDeadline = date('Y-m-d H:i:s', strtotime('+'.$eotObj->extendMonth.' months', strtotime($task['deadline'])));
                            $sql = Sql::update('task')->setFieldValue(['deadline'=>'"'.$newTaskDeadline.'"'])->where(['id', '=', $task['id']])->where(['status', '=', 1]);
                            if($sql->prepare()->execute()){
                                $logData = [];
                                $logData['userID']= $currentUserObj->id;
                                $logData['module'] = "Task";
                                $logData['referenceID'] = $task['id'];
                                $logData['action'] = "Update";
                                $logData['description'] = "Approved EOT [ID: ".$eotObj->id."]";
                                $logData['sqlStatement'] = $sql;
                                $logData['sqlValue'] = $newTaskDeadline;			
                                $logData['changes'] = [[
                                    "key"=>"deadline", 
                                    "valueFrom"=>$task['deadline'], 
                                    "valueTo"=>strip_tags($newTaskDeadline)					
                                ]];
                                systemLog::add($logData);
                            }                       
                        }

                    }

                } elseif($request->post->status==3) { // rejected

                    foreach($request->post->conditionID as $conditionID){
                        // update related condition status
                        $conditionObj = self::getConditionDetail($conditionID);
                        $sql = Sql::update('tpbCondition')->setFieldValue(['status'=>'3'])->where(['id', '=', $conditionID]);
                        if($sql->prepare()->execute()){
                            $logData = [];
                            $logData['userID']= $currentUserObj->id;
                            $logData['module'] = "Condition";
                            $logData['referenceID'] = $conditionID;
                            $logData['action'] = "Update";
                            $logData['description'] = "Rejected EOT [ID: ".$eotObj->id."]";
                            $logData['sqlStatement'] = $sql;
                            $logData['sqlValue'] = 3;			
                            $logData['changes'] = [[
                                "key"=>"status", 
                                "valueFrom"=>$conditionObj->status, 
                                "valueTo"=>strip_tags(3)					
                            ]];
                            systemLog::add($logData);
                        } 

                        // update related task status
                        $taskList = task::findByConditionID($conditionID);
                        foreach($taskList as $task) {
                            $sql = Sql::update('task')->setFieldValue(['status'=>'2'])->where(['id', '=', $task['id']]);
                            if($sql->prepare()->execute()){
                                $logData = [];
                                $logData['userID']= $currentUserObj->id;
                                $logData['module'] = "Task";
                                $logData['referenceID'] = $task['id'];
                                $logData['action'] = "Update";
                                $logData['description'] = "Rejected EOT [ID: ".$eotObj->id."]";
                                $logData['sqlStatement'] = $sql;
                                $logData['sqlValue'] = 2;			
                                $logData['changes'] = [[
                                    "key"=>"deadline", 
                                    "valueFrom"=>$task['status'], 
                                    "valueTo"=>strip_tags(2)					
                                ]];
                                systemLog::add($logData);
                            }                           
                        }
                    }
                    
                    // withdrawn application
                    if($eotObj->tpbID>0){
                        self::updateTBPStatus($eotObj->tpbID, 8);
                    }

                }
            }

			return new Data(['success'=>true, 'message'=>L('info.updated'), 'EOTDocID'=>$EOTDocID, 'EOTDocPath'=>documentHelper::find($EOTDocID)->downloadPath]);			
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableUpdate'), 'field'=>'notice']);
		}	                
        
    }  

    // condition form 
    public function conditionFormAdd($request) {
        if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

        $currentUserObj = unserialize($_SESSION['user']);
        
        $formName = "form-addCondition";

        $content = "<form id='".$formName."' class='' autocomplete='off'>";
            $content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.conditionAddHelperMessage')."</p></div>";

            $content .= "<div class='row'>";
                $content .= formLayout::rowInputNew(L('tpb.TPBNo'),'tpbID', 'tpbID', 'hidden',  6, [], [], $request->get->id);   
            $content .= "</div>";

            $content .= "<div class='row'>";
                $content .= formLayout::rowInputNew(L('condition.no'),'conditionNo', 'conditionNo', 'text',  6, [], []);           

                $content .= formLayout::rowInputNew(L('condition.deadline'),'deadline', 'deadline', 'text',  6, ['customDateTime'], []); 

                $content .= formLayout::rowInputNew(L('condition.description'),'description', 'description', 'text',  12, [], ['required']);                   
            
            $content .= "</div>";

        $content .= "</form>";

        return new Data(['success'=>true, 'message'=>$content]);

    }

    // condition add action
    public function conditionAdd($request) {	
       
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

        $currentUserObj = unserialize($_SESSION['user']);

        // form check	        
        if (!isset($request->post->tpbID) || empty($request->post->tpbID)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyID'), 'field'=>'tpbID', 'tab'=>'pills-EOT']);
        /*
        if (!isset($request->post->conditionNo) || empty($request->post->conditionNo)) 
			return new Data(['success'=>false, 'message'=>L('error.conditionEmptyNo'), 'field'=>'conditionNo', 'tab'=>'pills-condition']);
        */
        if (!isset($request->post->description) || empty($request->post->description)) 
			return new Data(['success'=>false, 'message'=>L('error.conditionEmptyDescription'), 'field'=>'description', 'tab'=>'pills-condition']);
        /*
        if (!isset($request->post->deadline) || empty($request->post->deadline)) 
			return new Data(['success'=>false, 'message'=>L('error.conditionEmptyDeadline'), 'field'=>'deadline', 'tab'=>'pills-condition']);            
        */   
        $sql = Sql::insert('tpbCondition')->setFieldValue([
            'tpbID' => "?", 
            'conditionNo' => "?", 
            'description' => "?", 
            'deadline' => "?", 
            'status' => "?",
            'createBy'=>$currentUserObj->id, 
            'modifyBy'=>$currentUserObj->id
        ]);

        $addValues = [
            strip_tags($request->post->tpbID),
            strip_tags($request->post->conditionNo),
            strip_tags($request->post->description),
            strip_tags($request->post->deadline),
            strip_tags(1)
        ];

        if ($sql->prepare()->execute($addValues)) {                

            $conditionID = db()->lastInsertId();

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "Condition";
			$logData['referenceID'] = $conditionID;
			$logData['action'] = "Insert";
			$logData['description'] = "Create New Condition [".$request->post->description."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $addValues;
			$logData['changes'] = 
				[
					[
						"key"=>"tpbID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->tpbID)
                    ],[
						"key"=>"conditionNo", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->conditionNo)
                    ],[
						"key"=>"description", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->description)
                    ],[
						"key"=>"deadline", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->deadline)
                    ]
				];       
                
            systemLog::add($logData);

            return new Data(['success'=>true, 'message'=>L('info.saved'), 'id'=>$conditionID]);
        } else {
            return new Data(['success'=>false, 'message'=>L('error.unableInsert'), 'field'=>'notice']);
        }	
         
        
	}   

    // condition edit form 
    public function conditionFormEdit($request) {
        
		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) {
			$obj = self::getConditionDetail($request->get->id, \PDO::FETCH_NAMED);
        }
        
		$formName = "form-editCondition";

		$content = "<form id='".$formName."' class='' autocomplete='off'>";
            $content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.conditionAddHelperMessage')."</p></div>";


            $content .= "<div class='row'>";
                $content .= formLayout::rowInputNew(L('condition.no'),'conditionNo', 'conditionNo', 'text',  6, [], [], is_null($obj)?[]:$obj['conditionNo']);           

                $content .= formLayout::rowInputNew(L('condition.deadline'),'deadline', 'deadline', 'text',  6, ['customDateTime'], [], ($obj['deadline']=="0000-00-00 00:00:00" || empty($obj['deadline'])?"":$obj['deadline']));  

                $content .= formLayout::rowInputNew(L('condition.description'),'description', 'description', 'text',  12, [], ['required'], is_null($obj)?'':$obj['description']);             
            
                $option = [];
                $stm = Sql::select('generalStatus')->prepare();
                $stm->execute();                                          
                foreach ($stm as $opt) {  
                    $option[$opt['id']] = $opt['name'];
                }
                $content .= formLayout::rowSelectNew(L('Status'), 'status', 'status', $option, 6, [], [], is_null($obj)?'':$obj['status']); 

            $content .= "</div>";

		$content .= "</form>";

		return new Data(['success'=>true, 'message'=>$content]);

    }  

        // condition edit action
    public function conditionEdit($request) {	
        
        if (!user::checklogin()) 
            return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

        $currentUserObj = unserialize($_SESSION['user']);

        // form check	        
        if (!isset($request->get->id) || empty($request->get->id)) 
            return new Data(['success'=>false, 'message'=>L('error.conditionEmptyID'), 'field'=>'conditionID', 'tab'=>'pills-condition']);

        $conditionObj = self::getConditionDetail($request->get->id);

        if(is_null($conditionObj))
            return new Data(['success'=>false, 'message'=>L('error.conditionNotFound'), 'field'=>'notice']);               

        /*
        if (!isset($request->post->conditionNo) || empty($request->post->conditionNo)) 
			return new Data(['success'=>false, 'message'=>L('error.conditionEmptyNo'), 'field'=>'conditionNo', 'tab'=>'pills-condition']);
        */
        if (!isset($request->post->description) || empty($request->post->description)) 
			return new Data(['success'=>false, 'message'=>L('error.conditionEmptyDescription'), 'field'=>'description', 'tab'=>'pills-condition']);
        /*
        if (!isset($request->post->deadline) || empty($request->post->deadline)) 
			return new Data(['success'=>false, 'message'=>L('error.conditionEmptyDeadline'), 'field'=>'deadline', 'tab'=>'pills-condition']);            
        */  

        // file upload
        $editFields = [];
        $editValues = [];
		$logContent = [];	    
        
		if (isset($request->post->conditionNo) && !empty($request->post->conditionNo)) {

            $editFields['conditionNo'] = "?";
            $editValues[] = $request->post->conditionNo;

            if($request->post->conditionNo!=$conditionObj->conditionNo) {
				$logContent[] = [
					"key"=>"conditionNo", 
					"valueFrom"=>$conditionObj->conditionNo, 
					"valueTo"=>strip_tags($request->post->conditionNo)					
				];	
			}			
		}	        
       
		if (isset($request->post->description) && !empty($request->post->description)) {

            $editFields['description'] = "?";
            $editValues[] = $request->post->description;

            if($request->post->description!=$conditionObj->description) {
				$logContent[] = [
					"key"=>"description", 
					"valueFrom"=>$conditionObj->description, 
					"valueTo"=>strip_tags($request->post->description)					
				];	
			}			
		}	
        
		if (isset($request->post->deadline) && !empty($request->post->deadline)) {

            $editFields['deadline'] = "?";
            $editValues[] = $request->post->deadline;

            if($request->post->deadline.":00"!=$conditionObj->deadline) {
				$logContent[] = [
					"key"=>"deadline", 
					"valueFrom"=>$conditionObj->deadline, 
					"valueTo"=>strip_tags($request->post->deadline)					
				];	
			}			
		}	
        
		if (isset($request->post->status) && !empty($request->post->status)) {

            $editFields['status'] = "?";
            $editValues[] = $request->post->status;

            if($request->post->status!=$conditionObj->status) {
				$logContent[] = [
					"key"=>"status", 
					"valueFrom"=>$conditionObj->status, 
					"valueTo"=>strip_tags($request->post->status)					
				];	
			}			
		}	        
        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('tpbCondition')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
            if (count($logContent)) {
                $logData = [];
                $logData['userID']= $currentUserObj->id;
                $logData['module'] = "Condition";
                $logData['referenceID'] = $request->get->id;
                $logData['action'] = "Update";
                $logData['description'] = "Update Condition [ID: ".$conditionObj->id."]";
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

    // old
    public function edit($request) {	
      
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);

		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyID'), 'field'=>'notice']);

		$tpbObj = self::find($request->get->id);
		if(is_null($tpbObj))
			return new Data(['success'=>false, 'message'=>L('error.tpbNotFound'), 'field'=>'notice']);

        // form check
		//if (!isset($request->post->submissionDate) || empty($request->post->submissionDate)) 
			//return new Data(['success'=>false, 'message'=>L('error.tpbEmptySubmissionDate'), 'field'=>'submissionDate']);

		if (!isset($request->post->refNo) || empty($request->post->refNo)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyRefNo'), 'field'=>'refNo']);

		if (!isset($request->post->clientID) || empty($request->post->clientID)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyClientID'), 'field'=>'clientID']);
        
        if (!isset($request->post->userID) || empty($request->post->userID) || count($request->post->userID)==0) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyOfficer'), 'field'=>'userID']);     
                
        $editFields = [];
		$editValues = [];

        /*
		if (isset($request->post->submissionDate) && !empty($request->post->submissionDate)) {
			$editFields['submissionDate'] = "?";
			$editValues[] = $request->post->submissionDate;
		}	
            */	

		if (isset($request->post->refNo) && !empty($request->post->refNo)) {
			$editFields['refNo'] = "?";
			$editValues[] = $request->post->refNo;
		}		
        
		if (isset($request->post->clientID) && !empty($request->post->clientID)) {
			$editFields['clientID'] = "?";
			$editValues[] = $request->post->clientID;
		}		        
		
		if (isset($request->post->tpbStatusID) && !empty($request->post->tpbStatusID)) {
			$editFields['tpbStatusID'] = "?";
			$editValues[] = $request->post->tpbStatusID;
		}	        
        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('tpb')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {


            $sql = Sql::delete('tpbOfficer')->where(['tpbID', '=', $request->get->id]);
            $sql->prepare()->execute();

            foreach($request->post->userID as $officerID) {
                $sql = Sql::insert('tpbOfficer')->setFieldValue([
                    'tpbID' => "?", 
                    'userID' => "?"
                ]);               

                $sql->prepare()->execute([
                    strip_tags($request->get->id),
                    strip_tags($officerID)
                ]);
            }



			return new Data(['success'=>true, 'message'=>L('info.updated')]);			
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableUpdate'), 'field'=>'notice']);
		}		        
        
	}      

    // old    
    public function followUpTpbForm($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) {
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

            $obj['userID'] = null;
            $officerList = self::findOfficer($request->get->id);
            foreach($officerList as $officerInfo) {
                $obj['userID'][] = $officerInfo['userID'];
            }

            $obj['zoningID'] = null;
            $zoningList = self::findZoning($request->get->id);
            foreach($zoningList as $zoningInfo) {
                $obj['zoningID'][] = $zoningInfo['zoningID'];
            }            

            $clientObj = client::find($obj['clientID']);
        }
        
		$formName = "form-addTpbFollowUp";

		if(!is_null($obj)) {
			$formName = "form-editTpbFollowUp";
		}				
        
		$content = "<form id='".$formName."' class='' autocomplete='off'>";
		$content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.tpbAddHelperMessage')."</p></div>";
        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.type'),clientType::find($clientObj->clientTypeID)->name, 6);
            $content .= formLayout::rowDisplayLineNew(L('client.title'),$clientObj->title, 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.contactPerson'),$clientObj->contactPerson, 6);
            $content .= formLayout::rowDisplayLineNew(L('client.position'),$clientObj->position, 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.phone'),$clientObj->phone, 6);
            $content .= formLayout::rowDisplayLineNew(L('client.email'),$clientObj->email, 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.companyEnglishName'),$clientObj->companyEnglishName, 6);
            $content .= formLayout::rowDisplayLineNew(L('client.companyChineseName'),$clientObj->companyChineseName, 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('tpb.refNo'),$obj['refNo'], 6);
        $content .= "</div>";

        $content .= formLayout::rowSeparatorLineNew(12);
        $content .= "<div class='row'>";
        $content .= formLayout::rowInputNew(L('tpb.addressDDLot'),'addressDDLot', 'addressDDLot', 'text',  12, [], ['required'], is_null($obj)?'':$obj['addressDDLot']);
        $content .= formLayout::rowInputNew(L('tpb.ozpName'),'OZPName', 'OZPName', 'text',  6, [], ['required'], is_null($obj)?'':$obj['OZPName']);
        $content .= formLayout::rowInputNew(L('tpb.ozpNo'),'OZPNo', 'OZPNo', 'text',  6, [], ['required'], is_null($obj)?'':$obj['OZPNo']);

        
        $option = [];
        $stm = Sql::select('zoning')->where(['status', '=', 1])->prepare();
        $stm->execute();                                          
        foreach ($stm as $opt) {  
             $option[$opt['id']] = $opt['name'];			  
        }
        $content .= formLayout::rowMultiSelectNew(L('tpb.zoning'), 'zoningID[]', 'zoningID', $option,  6, ['select2'], ['required'], empty($obj['zoningID'])?[]:$obj['zoningID']);             
        
        $content .= formLayout::rowInputNew(L('tpb.proposedUse'),'proposedUse', 'proposedUse', 'text',  6, [], ['required'], is_null($obj)?'':$obj['proposedUse']);

        $option = [""=>""];
        $stm = Sql::select('rntpc')->where(['status', '=', 1])->prepare();
        $stm->execute();                                          
        foreach ($stm as $opt) {  
             $option[$opt['id']] = $opt['meetingDate'];			  
        }
        $content .= formLayout::rowSelectNew(L('tpb.rntpc'), 'rntpcID', 'rntpcID', $option,  6, [], ['required'], empty($obj['rntpcID'])?"":$obj['rntpcID']);                    


        $content .= formLayout::rowInputNew(L('tpb.submissionDate'),'submissionDate', 'submissionDate', 'text',  6, ['customDate'], ['required'], empty($obj['submissionDate'])?'':$obj['submissionDate']);

        $content .= formLayout::rowTextAreaNew(L('tpb.remarks'), 'remarks', 'remarks',  12, [], [], is_null($obj)?'':$obj['remarks']);

        $content .= formLayout::rowInputNew(L('tpb.lastUpdateDate'),'lastUpdateDate', 'lastUpdateDate', 'text',  6, ['customDate'], ['required'], empty($obj['lastUpdateDate'])?'':$obj['lastUpdateDate']);

        if(!is_null($obj)) {
            $option = [];
            $stm = Sql::select('tpbStatus')->prepare();
            $stm->execute();                                          
            foreach ($stm as $opt) {  
                $option[$opt['id']] = $opt['name'];
            }
            $content .= formLayout::rowSelectNew(L('Status'), 'tpbStatusID', 'tpbStatusID', $option, 6, [], ['required'], is_null($obj)?'':$obj['tpbStatusID']);
        }
		$content .= "</div>";
		$content .= "</form>";
        
		return new Data(['success'=>true, 'message'=>$content]);
		
	}    

    // old
    public function followUpEdit($request) {	
      
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);

		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyID'), 'field'=>'notice']);

		$tpbObj = self::find($request->get->id);
		if(is_null($tpbObj))
			return new Data(['success'=>false, 'message'=>L('error.tpbNotFound'), 'field'=>'notice']);

        // form check
		//if (!isset($request->post->submissionDate) || empty($request->post->submissionDate)) 
			//return new Data(['success'=>false, 'message'=>L('error.tpbEmptySubmissionDate'), 'field'=>'submissionDate']);

		if (!isset($request->post->addressDDLot) || empty($request->post->addressDDLot)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyAddressDDLot'), 'field'=>'addressDDLot']);

		if (!isset($request->post->OZPName) || empty($request->post->OZPName)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyOzpName'), 'field'=>'OZPName']);
        
        if (!isset($request->post->OZPNo) || empty($request->post->OZPNo)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyOzpNo'), 'field'=>'OZPNo']);     
                
        if (!isset($request->post->zoningID) || empty($request->post->zoningID) || count($request->post->zoningID)==0) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyZoning'), 'field'=>'zoningID']);     

        if (!isset($request->post->proposedUse) || empty($request->post->proposedUse)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyProposedUse'), 'field'=>'proposedUse']); 
        
        if (!isset($request->post->rntpcID) || empty($request->post->rntpcID)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyRntpc'), 'field'=>'rntpcID']); 
        
        if (!isset($request->post->submissionDate) || empty($request->post->submissionDate)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptySubmissionDate'), 'field'=>'submissionDate']);             

        if (!isset($request->post->lastUpdateDate) || empty($request->post->lastUpdateDate)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyLastUpdateDate'), 'field'=>'lastUpdateDate']);      

        $editFields = [];
		$editValues = [];

        /*
		if (isset($request->post->submissionDate) && !empty($request->post->submissionDate)) {
			$editFields['submissionDate'] = "?";
			$editValues[] = $request->post->submissionDate;
		}	
            */	

		if (isset($request->post->addressDDLot) && !empty($request->post->addressDDLot)) {
			$editFields['addressDDLot'] = "?";
			$editValues[] = $request->post->addressDDLot;
		}		
        
		if (isset($request->post->OZPName) && !empty($request->post->OZPName)) {
			$editFields['OZPName'] = "?";
			$editValues[] = $request->post->OZPName;
		}		
        
		if (isset($request->post->OZPNo) && !empty($request->post->OZPNo)) {
			$editFields['OZPNo'] = "?";
			$editValues[] = $request->post->OZPNo;
		}	        

        if (isset($request->post->proposedUse) && !empty($request->post->proposedUse)) {
			$editFields['proposedUse'] = "?";
			$editValues[] = $request->post->proposedUse;
		}	       

        if (isset($request->post->rntpcID) && !empty($request->post->rntpcID)) {
			$editFields['rntpcID'] = "?";
			$editValues[] = $request->post->rntpcID;
		}	 

        if (isset($request->post->submissionDate) && !empty($request->post->submissionDate)) {
			$editFields['submissionDate'] = "?";
			$editValues[] = $request->post->submissionDate;
		}	  

        if (isset($request->post->remarks) && !empty($request->post->remarks)) {
			$editFields['remarks'] = "?";
			$editValues[] = $request->post->remarks;
		}	 
        
        if (isset($request->post->lastUpdateDate) && !empty($request->post->lastUpdateDate)) {
			$editFields['lastUpdateDate'] = "?";
			$editValues[] = $request->post->lastUpdateDate;
		}	        
		
		if (isset($request->post->tpbStatusID) && !empty($request->post->tpbStatusID)) {
			$editFields['tpbStatusID'] = "?";
			$editValues[] = $request->post->tpbStatusID;
		}	        
        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('tpb')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {

            $sql = Sql::delete('tpbZoning')->where(['tpbID', '=', $request->get->id]);
            $sql->prepare()->execute();

            foreach($request->post->zoningID as $zoningID) {
                $sql = Sql::insert('tpbZoning')->setFieldValue([
                    'tpbID' => "?", 
                    'zoningID' => "?"
                ]);               

                $sql->prepare()->execute([
                    strip_tags($request->get->id),
                    strip_tags($zoningID)
                ]);
            }

			return new Data(['success'=>true, 'message'=>L('info.updated')]);			
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableUpdate'), 'field'=>'notice']);
		}		        
        
	}  

    // old    
    public function receiveTpbForm($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) {
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

            $obj['userID'] = null;
            $officerList = self::findOfficer($request->get->id);
            foreach($officerList as $officerInfo) {
                $obj['userID'][] = $officerInfo['userID'];
            }

            $obj['zoningID'] = null;
            $zoningList = self::findZoning($request->get->id);
            foreach($zoningList as $zoningInfo) {
                $obj['zoningID'][] = $zoningInfo['zoningID'];
            }            

            $clientObj = client::find($obj['clientID']);
        }
        
		$formName = "form-addTpbReceive";

		if(!is_null($obj)) {
			$formName = "form-editTpbReceive";
		}				
        
		$content = "<form id='".$formName."' class='' autocomplete='off'>";
		$content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.tpbAddHelperMessage')."</p></div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.type'),clientType::find($clientObj->clientTypeID)->name, 6);
            $content .= formLayout::rowDisplayLineNew(L('client.title'),$clientObj->title, 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.contactPerson'),$clientObj->contactPerson, 6);
            $content .= formLayout::rowDisplayLineNew(L('client.position'),$clientObj->position, 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.phone'),$clientObj->phone, 6);
            $content .= formLayout::rowDisplayLineNew(L('client.email'),$clientObj->email, 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.companyEnglishName'),$clientObj->companyEnglishName, 6);
            $content .= formLayout::rowDisplayLineNew(L('client.companyChineseName'),$clientObj->companyChineseName, 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('tpb.refNo'),$obj['refNo'], 6);
        $content .= "</div>";
        
        $content .= formLayout::rowSeparatorLineNew(12);

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('tpb.addressDDLot'),$obj['addressDDLot'], 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('tpb.ozpName'),$obj['OZPName'], 6);
            $content .= formLayout::rowDisplayLineNew(L('tpb.ozpNo'),$obj['OZPNo'], 6);
        $content .= "</div>";

        $zoningName = [];
        foreach($obj['zoningID'] as $zoneID) {
            $zoningName[] = '<span class="badge bg-dark">'.zoning::find($zoneID)->name.'</span>';
        }
        
        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('tpb.zoning'),implode(" ",$zoningName), 6);
            $content .= formLayout::rowDisplayLineNew(L('tpb.proposedUse'),$obj['proposedUse'], 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('tpb.rntpc'),rntpc::find($obj['rntpcID'])->meetingDate??"", 6);
            $content .= formLayout::rowDisplayLineNew(L('tpb.submissionDate'),$obj['submissionDate']??"", 6);
        $content .= "</div>";

        $content .= "<div class='row'>";        
            $content .= formLayout::rowDisplayLineNew(L('tpb.remarks'),$obj['remarks'], 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('tpb.lastUpdateDate'),$obj['lastUpdateDate']??"", 6);
        $content .= "</div>";

        $content .= formLayout::rowSeparatorLineNew(12);

        $content .= "<div class='row'>";
            $content .= formLayout::rowInputNew(L('tpb.number'),'TPBNo', 'TPBNo', 'text',  6, [], ['required'], is_null($obj)?'':$obj['TPBNo']);
            $content .= formLayout::rowInputNew(L('tpb.website'),'TPBWebsite', 'TPBWebsite', 'text',  6, [], ['required'], is_null($obj)?'':$obj['TPBWebsite']);
        
            $content .= formLayout::rowInputNew(L('tpb.receiveDate'),'TPBReceiveDate', 'TPBReceiveDate', 'text',  6, ['customDate'], ['required'], empty($obj['TPBReceiveDate'])?'':$obj['TPBReceiveDate']);
            $content .= formLayout::rowInputNew(L('tpb.considerationDate'),'tentativeConsiderationDate', 'tentativeConsiderationDate', 'text',  6, ['customDate'], ['required'], empty($obj['tentativeConsiderationDate'])?'':$obj['tentativeConsiderationDate']);
        
        
        if(!is_null($obj)) {
            $option = [];
            $stm = Sql::select('tpbStatus')->prepare();
            $stm->execute();                                          
            foreach ($stm as $opt) {  
                $option[$opt['id']] = $opt['name'];
            }
            $content .= formLayout::rowSelectNew(L('Status'), 'tpbStatusID', 'tpbStatusID', $option, 6, [], ['required'], is_null($obj)?'':$obj['tpbStatusID']);
        }
		$content .= "</div>";
        
		$content .= "</form>";
        
		return new Data(['success'=>true, 'message'=>$content]);
		
	}    

    /*  old
    public function receiveEdit($request) {	
      
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);

		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyID'), 'field'=>'notice']);

		$tpbObj = self::find($request->get->id);
		if(is_null($tpbObj))
			return new Data(['success'=>false, 'message'=>L('error.tpbNotFound'), 'field'=>'notice']);

        // form check
		//if (!isset($request->post->submissionDate) || empty($request->post->submissionDate)) 
			//return new Data(['success'=>false, 'message'=>L('error.tpbEmptySubmissionDate'), 'field'=>'submissionDate']);

		if (!isset($request->post->TPBNo) || empty($request->post->TPBNo)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyNumber'), 'field'=>'TPBNo']);

		if (!isset($request->post->TPBWebsite) || empty($request->post->TPBWebsite)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyWebsite'), 'field'=>'TPBWebsite']);
        
        if (!isset($request->post->TPBReceiveDate) || empty($request->post->TPBReceiveDate)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyReceiveDate'), 'field'=>'TPBReceiveDate']);     
                
        if (!isset($request->post->tentativeConsiderationDate) || empty($request->post->tentativeConsiderationDate)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyConsiderationDate'), 'field'=>'tentativeConsiderationDate']); 
        

        $editFields = [];
		$editValues = [];

		if (isset($request->post->TPBNo) && !empty($request->post->TPBNo)) {
			$editFields['TPBNo'] = "?";
			$editValues[] = $request->post->TPBNo;
		}		
        
		if (isset($request->post->TPBWebsite) && !empty($request->post->TPBWebsite)) {
			$editFields['TPBWebsite'] = "?";
			$editValues[] = $request->post->TPBWebsite;
		}		
        
		if (isset($request->post->TPBReceiveDate) && !empty($request->post->TPBReceiveDate)) {
			$editFields['TPBReceiveDate'] = "?";
			$editValues[] = $request->post->TPBReceiveDate;
		}	        

        if (isset($request->post->tentativeConsiderationDate) && !empty($request->post->tentativeConsiderationDate)) {
			$editFields['tentativeConsiderationDate'] = "?";
			$editValues[] = $request->post->tentativeConsiderationDate;
		}
		
		if (isset($request->post->tpbStatusID) && !empty($request->post->tpbStatusID)) {
			$editFields['tpbStatusID'] = "?";
			$editValues[] = $request->post->tpbStatusID;
		}	        
        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('tpb')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
			return new Data(['success'=>true, 'message'=>L('info.updated')]);			
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableUpdate'), 'field'=>'notice']);
		}		        
        
	} 
    */

    // old    
    public function decisionTpbForm($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) {
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

            $obj['userID'] = null;
            $officerList = self::findOfficer($request->get->id);
            foreach($officerList as $officerInfo) {
                $obj['userID'][] = $officerInfo['userID'];
            }

            $obj['zoningID'] = null;
            $zoningList = self::findZoning($request->get->id);
            foreach($zoningList as $zoningInfo) {
                $obj['zoningID'][] = $zoningInfo['zoningID'];
            } 

            $clientObj = client::find($obj['clientID']);
        }
        
		$formName = "form-addTpbDecision";

		if(!is_null($obj)) {
			$formName = "form-editTpbDecision";
		}				
        
		$content = "<form id='".$formName."' class='' autocomplete='off'>";
		$content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.tpbAddHelperMessage')."</p></div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.type'),clientType::find($clientObj->clientTypeID)->name, 6);
            $content .= formLayout::rowDisplayLineNew(L('client.title'),$clientObj->title, 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.contactPerson'),$clientObj->contactPerson, 6);
            $content .= formLayout::rowDisplayLineNew(L('client.position'),$clientObj->position, 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.phone'),$clientObj->phone, 6);
            $content .= formLayout::rowDisplayLineNew(L('client.email'),$clientObj->email, 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.companyEnglishName'),$clientObj->companyEnglishName, 6);
            $content .= formLayout::rowDisplayLineNew(L('client.companyChineseName'),$clientObj->companyChineseName, 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('tpb.refNo'),$obj['refNo'], 6);
        $content .= "</div>";
        
        $content .= formLayout::rowSeparatorLineNew(12);

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('tpb.addressDDLot'),$obj['addressDDLot'], 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('tpb.ozpName'),$obj['OZPName'], 6);
            $content .= formLayout::rowDisplayLineNew(L('tpb.ozpNo'),$obj['OZPNo'], 6);
        $content .= "</div>";

        $zoningName = [];
        foreach($obj['zoningID'] as $zoneID) {
            $zoningName[] = '<span class="badge bg-dark">'.zoning::find($zoneID)->name.'</span>';
        }
        
        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('tpb.zoning'),implode(" ",$zoningName), 6);
            $content .= formLayout::rowDisplayLineNew(L('tpb.proposedUse'),$obj['proposedUse'], 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('tpb.rntpc'),rntpc::find($obj['rntpcID'])->meetingDate??"", 6);
            $content .= formLayout::rowDisplayLineNew(L('tpb.submissionDate'),$obj['submissionDate']??"", 6);
        $content .= "</div>";

        $content .= "<div class='row'>";        
            $content .= formLayout::rowDisplayLineNew(L('tpb.remarks'),$obj['remarks'], 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('tpb.lastUpdateDate'),$obj['lastUpdateDate']??"", 6);
        $content .= "</div>";

        $content .= formLayout::rowSeparatorLineNew(12);

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('tpb.number'),$obj['TPBNo'], 6);
            $content .= formLayout::rowDisplayLineNew(L('tpb.website'),$obj['TPBWebsite'], 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('tpb.receiveDate'),$obj['TPBReceiveDate']??"", 6);
            $content .= formLayout::rowDisplayLineNew(L('tpb.considerationDate'),$obj['tentativeConsiderationDate']??"", 6);
        $content .= "</div>";

        $content .= formLayout::rowSeparatorLineNew(12);

        $content .= "<div class='row'>";
        if(!is_null($obj)) {
            $option = [""=>""];
            $stm = Sql::select('decision')->prepare();
            $stm->execute();                                          
            foreach ($stm as $opt) {  
                $option[$opt['id']] = $opt['name'];
            }
            $content .= formLayout::rowSelectNew(L('tpb.decision'), 'decisionID', 'decisionID', $option, 6, [], ['required'], is_null($obj)?'':$obj['decisionID']);
        }

        $content .= formLayout::rowInputNew(L('tpb.approvalDate'),'approvalDate', 'approvalDate', 'text',  6, ['customDateTime'], [], empty($obj['approvalDate'])?'':$obj['approvalDate']);
        $content .= formLayout::rowInputNew(L('tpb.approvalValidUntil'),'approvalValidUntil', 'approvalValidUntil', 'text',  6, ['customDate'], ['required'], empty($obj['approvalValidUntil'])?'':$obj['approvalValidUntil']);

        $conditionList = self::findCondition($request->get->id);

        $content .= '<div id="conditionArea">';
            $content .= '<div class="row mb-0">';
                $content .= '<div class="col-md-6 col-lg-6 mt-3"><label for="" class="" style="margin-left:-5px;">'.L('tpb.condition').' & '.L('Deadline').'*</label></div>';
                $content .= '<div class="col-md-6 col-lg-6 text-end"><label for="" class=""><button type="button" class="btn btn-sm btn-primary btn-round" id="addConditionRow"><i class="fas fa-plus"></i></button></label></div>';
            $content .= '</div>';
            $row=0;
            if($conditionList->rowCount()>0){
                foreach ($conditionList as $condition) {        
                    $content .= '<div class="col-md-12 col-lg-12 conditionRow" id="conditionRow_'.$row.'">';
                        $content .= '<div class="form-group">';                   
                                $content .= '<div class="input-group">';
                                    $content .= '<input type="text" class="form-control" placeholder="Condition" id="condition_'.$row.'" name="condition[]" value="'.$condition['conditions'].'" required>';
                                    $content .= '<input type="text" class="form-control customDateTime" placeholder="Deadline" id="deadline_'.$row.'" name="deadline[]" value="'.$condition['deadline'].'" required>';
                                    $content .= '<button type="button" class="btn btn-sm btn-danger removeConditionRow"><i class="fas fa-trash"></i></button>';
                                $content .= '</div>';                
                            $content .= '<small id="condition_'.$row.'Help" class="form-text text-muted hintHelp"></small>';
                        $content .= '</div>';
                    $content .= '</div>';      
                    $row++;  
                }
            } else {
                $content .= '<div class="col-md-12 col-lg-12 conditionRow" id="conditionRow_'.$row.'">';
                $content .= '<div class="form-group">';                   
                        $content .= '<div class="input-group">';
                            $content .= '<input type="text" class="form-control" placeholder="Condition" id="condition_'.$row.'" name="condition[]" value="" required>';
                            $content .= '<input type="text" class="form-control customDateTime" placeholder="Deadline" id="deadline_'.$row.'" name="deadline[]" value="" required>';
                            $content .= '<button type="button" class="btn btn-sm btn-danger removeConditionRow"><i class="fas fa-trash"></i></button>';
                        $content .= '</div>';                
                    $content .= '<small id="condition_'.$row.'Help" class="form-text text-muted hintHelp"></small>';
                $content .= '</div>';
                $content .= '</div>';                   
            }
        $content .= '</div>';

        if(!is_null($obj)) {
            $option = [];
            $stm = Sql::select('tpbStatus')->prepare();
            $stm->execute();                                          
            foreach ($stm as $opt) {  
                $option[$opt['id']] = $opt['name'];
            }
            $content .= formLayout::rowSelectNew(L('Status'), 'tpbStatusID', 'tpbStatusID', $option, 6, [], ['required'], is_null($obj)?'':$obj['tpbStatusID']);
        }
		
        $content .= "</div>";
		$content .= "</form>";
        
		return new Data(['success'=>true, 'message'=>$content]);
		
	}  

    /* old    
    public function decisionEdit($request) {	
              
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);

		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyID'), 'field'=>'notice']);

		$tpbObj = self::find($request->get->id);
		if(is_null($tpbObj))
			return new Data(['success'=>false, 'message'=>L('error.tpbNotFound'), 'field'=>'notice']);

        // form check
		//if (!isset($request->post->submissionDate) || empty($request->post->submissionDate)) 
			//return new Data(['success'=>false, 'message'=>L('error.tpbEmptySubmissionDate'), 'field'=>'submissionDate']);

		if (!isset($request->post->decisionID) || empty($request->post->decisionID)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyDecision'), 'field'=>'decisionID']);

		//if (!isset($request->post->approvalDate) || empty($request->post->approvalDate)) 
		//	return new Data(['success'=>false, 'message'=>L('error.tpbEmptyApprovalDate'), 'field'=>'approvalDate']);
        
        if (!isset($request->post->approvalValidUntil) || empty($request->post->approvalValidUntil)) 
			return new Data(['success'=>false, 'message'=>L('error.tpbEmptyApprovalValidUntil'), 'field'=>'approvalValidUntil']);     
        
        if ($request->post->decisionID==2) {
            if(count($request->post->condition)==0) {
                return new Data(['success'=>false, 'message'=>L('error.tpbEmptyCondition'), 'field'=>'condition_0']);     
            }

            foreach($request->post->condition as $ids => $condition){
                if($condition=="") {
                    return new Data(['success'=>false, 'message'=>L('error.tpbEmptyCondition'), 'field'=>'condition_'.$ids]);     
                }

                if($request->post->deadline[$ids]=="") {
                    return new Data(['success'=>false, 'message'=>L('error.tpbEmptyDeadline'), 'field'=>'condition_'.$ids]);     
                }               
            }
        }

        // check condition
      
        $editFields = [];
		$editValues = [];

		if (isset($request->post->decisionID) && !empty($request->post->decisionID)) {
			$editFields['decisionID'] = "?";
			$editValues[] = $request->post->decisionID;
		}		
        
		if (isset($request->post->approvalDate) && !empty($request->post->approvalDate)) {
			$editFields['approvalDate'] = "?";
			$editValues[] = $request->post->approvalDate;
		}		
        
		if (isset($request->post->approvalValidUntil) && !empty($request->post->approvalValidUntil)) {
			$editFields['approvalValidUntil'] = "?";
			$editValues[] = $request->post->approvalValidUntil;
		}	        

		if (isset($request->post->tpbStatusID) && !empty($request->post->tpbStatusID)) {
			$editFields['tpbStatusID'] = "?";
			$editValues[] = $request->post->tpbStatusID;
		}	        
        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('tpb')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {


            $sql = Sql::delete('tpbCondition')->where(['tpbID', '=', $request->get->id]);
            $sql->prepare()->execute();

            foreach($request->post->condition as $idx=>$condition) {
                $sql = Sql::insert('tpbCondition')->setFieldValue([
                    'tpbID' => "?", 
                    'conditions' => "?",
                    'deadline' => "?"
                ]);               

                $sql->prepare()->execute([
                    strip_tags($request->get->id),
                    strip_tags($condition),
                    strip_tags($request->post->deadline[$idx]),
                ]);
            }



			return new Data(['success'=>true, 'message'=>L('info.updated')]);			
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableUpdate'), 'field'=>'notice']);
		}		        
        
	} 
    */    
        
    public function detail($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) {
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

            $obj['userID'] = null;
            $obj['userName'] = null;
            $officerList = self::findOfficer($request->get->id);
            foreach($officerList as $officerInfo) {
                $obj['userID'][] = $officerInfo['userID'];
                $obj['userName'][] = user::find($officerInfo['userID'])->displayName;
            }

            $obj['zoningID'] = null;
            $obj['zoningName'] = null;
            $zoningList = self::findZoning($request->get->id);
            foreach($zoningList as $zoningInfo) {
                $obj['zoningID'][] = $zoningInfo['zoningID'];
                $obj['zoningName'][] = zoning::find($zoningInfo['zoningID'])->name;
            } 

            $clientObj = client::find($obj['clientID']);
        }

        $content = "<div class='card'><div class='card-body'>";

            $content .= "<ul class='nav nav-pills nav-secondary' id='pills-tab-tpbView' role='tablist'>";
                $content .= "<li class='nav-item submenu tpbViewMenu' role='presentation'>";
                    $content .= "<a class='tpbViewMenuA nav-link active' id='pills-applicant-tab' data-bs-toggle='pill' href='#pills-applicant' role='tab' aria-controls='pills-applicant' aria-selected='false' tabindex='-1'>".L('tpb.applicantDetail')."</a>";
                $content .= "</li>";

                $content .= "<li class='nav-item submenu tpbViewMenu' role='presentation'>";
                    $content .= "<a class='tpbViewMenuA nav-link' id='pills-application-tab' data-bs-toggle='pill' href='#pills-application' role='tab' aria-controls='pills-application' aria-selected='false' tabindex='-1'>".L('tpb.applicationDetail')."</a>";
                $content .= "</li>";

                $content .= "<li class='nav-item submenu tpbViewMenu' role='presentation'>";
                    $content .= "<a class='tpbViewMenuA nav-link' id='pills-submission-tab' data-bs-toggle='pill' href='#pills-submission' role='tab' aria-controls='pills-submission' aria-selected='false' tabindex='-1'>".L('tpb.submissionDetail')."</a>";
                $content .= "</li>";

                $content .= "<li class='nav-item submenu tpbViewMenu' role='presentation'>";
                    $content .= "<a class='tpbViewMenuA nav-link' id='pills-receive-tab' data-bs-toggle='pill' href='#pills-receive' role='tab' aria-controls='pills-receive' aria-selected='false' tabindex='-1'>".L('tpb.receiveDetail')."</a>";
                $content .= "</li>";
                
                $content .= "<li class='nav-item submenu tpbViewMenu' role='presentation'>";
                    $content .= "<a class='tpbViewMenuA nav-link' id='pills-decision-tab' data-bs-toggle='pill' href='#pills-decision' role='tab' aria-controls='pills-decision' aria-selected='false' tabindex='-1'>".L('tpb.decisionDetail')."</a>";
                $content .= "</li>";
                
                $content .= "<li class='nav-item submenu tpbViewMenu' role='presentation'>";
                    $content .= "<a class='tpbViewMenuA nav-link' id='pills-condition-tab' data-bs-toggle='pill' href='#pills-condition' role='tab' aria-controls='pills-condition' aria-selected='false' tabindex='-1'>".L('tpb.conditionDetail')."</a>";
                $content .= "</li>";
                
                $content .= "<li class='nav-item submenu tpbViewMenu' role='presentation'>";
                    $content .= "<a class='tpbViewMenuA nav-link' id='pills-EOT-tab' data-bs-toggle='pill' href='#pills-EOT' role='tab' aria-controls='pills-EOT' aria-selected='false' tabindex='-1'>".L('tpb.EOTDetail')."</a>";
                $content .= "</li>";                            

                $content .= "<li class='nav-item submenu tpbViewMenu' role='presentation'>";
                    $content .= "<a class='tpbViewMenuA nav-link' id='pills-STT-tab' data-bs-toggle='pill' href='#pills-STT' role='tab' aria-controls='pills-STT' aria-selected='false' tabindex='-1'>".L('tpb.STTDetail')."</a>";
                $content .= "</li>";
                
                $content .= "<li class='nav-item submenu tpbViewMenu' role='presentation'>";
                    $content .= "<a class='tpbViewMenuA nav-link' id='pills-STW-tab' data-bs-toggle='pill' href='#pills-STW' role='tab' aria-controls='pills-STW' aria-selected='false' tabindex='-1'>".L('tpb.STWDetail')."</a>";
                $content .= "</li>";                            

            $content .= "</ul>";

            $content .= "<div class='tab-content mt-2 mb-3' id='pills-tabContent-tpbView'>";

                // applicant tab // done
                $content .= "<div class='tab-pane tpbViewMenuDiv fade show active' id='pills-applicant' role='tabpanel' aria-labelledby='pills-applicant-tab'>";
                
                    $content .= "<div class='row'>";
                                               
                        $content .= formLayout::rowInputNew('','clientID','clientID','hidden',6,[],[],$obj['clientID']);  

                        $content .= "<div class='row' id='tpbSelectedClentDetail'></div>";                            

                    $content .= "</div>";
                
                $content .= "</div>";

                // application tab // done
                $content .= "<div class='tab-pane tpbViewMenuDiv fade' id='pills-application' role='tabpanel' aria-labelledby='pills-application-tab'>";
                    
                    $content .= "<div class='row'>";
                        $content .= formLayout::rowDisplayLineNew(L('tpb.refNo'), $obj['refNo']??"", 6);
                        $content .= formLayout::rowDisplayLineNew(L('tpb.officer'), implode(", ",$obj['userName']??[]), 6); 
                    $content .= "</div>";
                    $content .= "<div class='row'>";   
                        $content .= formLayout::rowDisplayLineNew(L('tpb.addressDDLot'), $obj['addressDDLot']??"", 12);  
                    $content .= "</div>";
                    $content .= "<div class='row'>";         
                        $content .= formLayout::rowDisplayLineNew(L('tpb.ozpName'), $obj['OZPName']??"", 6);                
                        $content .= formLayout::rowDisplayLineNew(L('tpb.ozpNo'), $obj['OZPNo']??"", 6); 
                        $content .= formLayout::rowDisplayLineNew(L('tpb.zoning'), isset($obj['zoningName'])?implode(", ",$obj['zoningName']):"", 6); 
                        $content .= formLayout::rowDisplayLineNew(L('tpb.proposedUse'), $obj['proposedUse']??"", 6); 
                        $content .= formLayout::rowDisplayLineNew(L('tpb.authorizationLetter'), $obj['authorizationLetterDocID']>0?'<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs authorizationLetterDownload" data-id="'.$obj['authorizationLetterDocID'].'"><i class="fas fa-download"></i></button></div>':'', 6);
                        $content .= formLayout::rowDisplayLineNew(L('tpb.isLandOwner'), L($obj['isLandOwner']), 6); 
                    

                    if($obj['isLandOwner']=="Y") {
                        $landOwnerSectionStyle="display: flex";
                        $notLandOwnerSection="display: none";
                    } else {
                        $landOwnerSectionStyle="display: none";
                        $notLandOwnerSection="display: flex";                                  
                    }

                    if($obj['isLandOwner']=="Y") {
                        $content .= formLayout::rowDisplayLineNew(L('tpb.landRegistry'), $obj['landRegistryDocID']>0?'<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs landRegistryDownload" data-id="'.$obj['landRegistryDocID'].'"><i class="fas fa-download"></i></button></div>':'', 6);
                    } else {
                        $content .= formLayout::rowDisplayLineNew(L('tpb.siteNotice'), $obj['siteNoticeDocID']>0?'<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs siteNoticeDownload" data-id="'.$obj['siteNoticeDocID'].'"><i class="fas fa-download"></i></button></div>':'', 6);
                        $content .= formLayout::rowDisplayLineNew(L('tpb.letterToRC'), $obj['letterToRCDocID']>0?'<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs letterToRCDownload" data-id="'.$obj['letterToRCDocID'].'"><i class="fas fa-download"></i></button></div>':'', 6);
                    }
                    $content .= "</div>";

                    $content .= "<div class='row'>";
                        $content .= formLayout::rowDisplayLineNew(L('tpb.remarks'), $obj['remarks']??"", 12);     
                    $content .= "</div>";

                    $content .= "<div class='row'>";
                        $content .= formLayout::rowDisplayLineNew(L('Status'), tpbStatus::find($obj['tpbStatusID'])->name, 6);                             
                    $content .= "</div>";  

                    
                $content .= "</div>";

                // submission tab
                $content .= "<div class='tab-pane tpbViewMenuDiv fade' id='pills-submission' role='tabpanel' aria-labelledby='pills-submission-tab'>";
                    
                    $content .= "<div class='row'>";
                        $content .= formLayout::rowDisplayLineNew(L('tpb.submissionDate'), $obj['submissionDate']=="0000-00-00" || empty($obj['submissionDate'])?"":$obj['submissionDate'], 6);              
                        $content .= formLayout::rowDisplayLineNew(L('tpb.submissionMode'), submissionMode::find($obj['submissionModeID'])->name??"", 6);                    
                        $content .= formLayout::rowDisplayLineNew(L('tpb.landRegistry'), $obj['submissionDocID']>0?'<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs submissionDocDownload" data-id="'.$obj['submissionDocID'].'"><i class="fas fa-download"></i></button></div>':'', 6);
                        $content .= formLayout::rowDisplayLineNew(L('tpb.rntpc'), rntpc::find($obj['rntpcID'])->meetingDate??"", 6);                    
                    $content .= "</div>";  
                    
                $content .= "</div>";

                // receive tab
                $content .= "<div class='tab-pane tpbViewMenuDiv fade' id='pills-receive' role='tabpanel' aria-labelledby='pills-receive-tab'>";
                    
                    $content .= "<div class='row'>";
                        $content .= formLayout::rowDisplayLineNew(L('tpb.number'), $obj['TPBNo']??"", 6);
                    $content .= "</div>";
                    $content .= "<div class='row'>";
                        $content .= formLayout::rowDisplayLineNew(L('tpb.website'), $obj['TPBWebsite']??"", 12);
                    $content .= "</div>";
                    $content .= "<div class='row'>";                        
                        $content .= formLayout::rowDisplayLineNew(L('tpb.receiveDate'), $obj['TPBReceiveDate']=="0000-00-00" || empty($obj['TPBReceiveDate'])?"":$obj['TPBReceiveDate'], 6);              
                        $content .= formLayout::rowDisplayLineNew(L('tpb.considerationDate'), $obj['tentativeConsiderationDate']=="0000-00-00" || empty($obj['tentativeConsiderationDate'])?"":$obj['tentativeConsiderationDate'], 6);              
                    $content .= "</div>";      
                $content .= "</div>";    

                // decision tab
                $content .= "<div class='tab-pane tpbViewMenuDiv fade' id='pills-decision' role='tabpanel' aria-labelledby='pills-decision-tab'>";                    
                    $content .= "<div class='row'>";
                        $content .= formLayout::rowDisplayLineNew(L('tpb.decision'), decision::find($obj['decisionID'])->name??"", 6);
                        $content .= formLayout::rowDisplayLineNew(L('tpb.decisionDate'), $obj['decisionDate']=="0000-00-00" || empty($obj['decisionDate'])?"":$obj['decisionDate'], 6);              
                        $content .= formLayout::rowDisplayLineNew(L('tpb.approvalValidUntil'), $obj['approvalValidUntil']=="0000-00-00" || empty($obj['approvalValidUntil'])?"":$obj['approvalValidUntil'], 6);              
                    $content .= "</div>";
                $content .= "</div>";     

                // condition tab
                $content .= "<div class='tab-pane tpbViewMenuDiv fade' id='pills-condition' role='tabpanel' aria-labelledby='pills-condition-tab'>";
                    
                    $content .= "<div class='table-responsive'>";
                        $content .= "<table id='conditionTable' class='display table table-striped table-hover conditionTable'>";
                        $content .= tpb::genConditionTableHeader();
                        $content .= tpb::genConditionTableFooter();
                        $content .= "<tbody>";                                    
                        $dataList = tpb::genConditionTableContentData($request->get->id);
                        foreach($dataList as $listObj) {
                            $content .= tpb::genConditionTableBodyRow($listObj);                                    
                        }
                        $content .= "</tbody>";
                        $content .= "</table>";
                    $content .= "</div>";

                    $content .= "<div class='row'>";
                        $content .= formLayout::rowSeparatorLineNew(12);
                    $content .= "</div>";  

                    $selected_condition_month = (isset($_GET['month']) && $_GET['month']!="")?$_GET['month']:date("Y-m");                             

                        $content .= "<div class='row'>";
                            $content .= "<div class='col col-sm-12 col-md-3, col-lg-3'>";
                                $content .= "<input type='text' class='form-control flatpickr-input' id='selected_condition_month' name='selected_condition_month' value='".$selected_condition_month."' readonly>";
                            $content .= "</div>";
                        $content .= "</div>";

                    $content .= "<div id='conditionCalendar'><i class='fa fa-spinner fa-spin'></i></div>";  

                $content .= "</div>";                                   

                // EOT tab
                $content .= "<div class='tab-pane tpbViewMenuDiv fade' id='pills-EOT' role='tabpanel' aria-labelledby='pills-EOT-tab'>";                    
                    $content .= "<div class='table-responsive'>";
                        $content .= "<table id='eotTable' class='display table table-striped table-hover eotTable'>";
                        $content .= tpb::genEOTTableHeader();
                        $content .= tpb::genEOTTableFooter();
                        $content .= "<tbody>";                                    
                        $dataList = tpb::genEOTTableContentData($request->get->id);
                        foreach($dataList as $listObj) {
                            $content .= tpb::genEOTTableBodyRow($listObj);                                    
                        }
                        $content .= "</tbody>";
                        $content .= "</table>";
                    $content .= "</div>";
                $content .= "</div>";   

                // STT tab
                $content .= "<div class='tab-pane tpbViewMenuDiv fade' id='pills-STT' role='tabpanel' aria-labelledby='pills-STT-tab'>";                    
                    $content .= "<div class='table-responsive'>";
                        $content .= "<table id='sttTable' class='display table table-striped table-hover sttTable'>";
                        $content .= stt::genTableHeader();
                        $content .= stt::genTableFooter();
                        $content .= "<tbody>";                                    
                        $dataList = stt::genTableContentData($request->get->id);
                        foreach($dataList as $listObj) {
                            $content .= stt::genTableBodyRow($listObj);                                    
                        }
                        $content .= "</tbody>";
                        $content .= "</table>";
                    $content .= "</div>";
                $content .= "</div>"; 

                // STW tab
                $content .= "<div class='tab-pane tpbViewMenuDiv fade' id='pills-STW' role='tabpanel' aria-labelledby='pills-STW-tab'>";                    
                    $content .= "<div class='table-responsive'>";
                        $content .= "<table id='stwTable' class='display table table-striped table-hover stwTable'>";
                        $content .= stw::genTableHeader();
                        $content .= stw::genTableFooter();
                        $content .= "<tbody>";                                    
                        $dataList = stw::genTableContentData($request->get->id);
                        foreach($dataList as $listObj) {
                            $content .= stw::genTableBodyRow($listObj);                                    
                        }
                        $content .= "</tbody>";
                        $content .= "</table>";
                    $content .= "</div>";
                $content .= "</div>";                             

            $content .= "</div>";

        $content .= "</div></div>"; // end card body    


		        
		return new Data(['success'=>true, 'message'=>$content]);
		
	}      

    public function eotDetail($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) {
			$obj = self::getEOTDetail($request->get->id, \PDO::FETCH_NAMED);    
        }

        $content = "";

        foreach(json_decode($obj['conditionID']) as $idx => $conditionID){                
            $content .= "<div class='row'>";    
                $displayString = "#".tpb::getConditionDetail($conditionID)->conditionNo.": ".tpb::getConditionDetail($conditionID)->description." - [".tpb::getConditionDetail($conditionID)->deadline."]";
                $content .= formLayout::rowDisplayLineNew(L('tpb.condition')." ".($idx+1), $displayString, 12);
            $content .= "</div>";
        }
        
		$content .= "<div class='row'>";     
            $content .= formLayout::rowDisplayLineNew(L('eot.extendMonth'), $obj['extendMonth'], 6);
            $content .= formLayout::rowDisplayLineNew(L('Status'), generalStatus::find($obj['status'])->name, 6);
        $content .= "</div>";
        $content .= "<div class='row'>";        
            $content .= formLayout::rowDisplayLineNew(L('eot.reason'), $obj['reason'], 12);
        $content .= "</div>";
        $content .= "<div class='row'>";        
            $content .= formLayout::rowDisplayLineNew(L('eot.submissionDate'), $obj['submissionDate'], 6);
			$content .= formLayout::rowDisplayLineNew(L('eot.submissionMode'), submissionMode::find($obj['submissionModeID'])->name, 6);
        $content .= "</div>";
        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('eot.submissionDoc'),$obj['EOTDocID']>0?'<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs" data-id="'.$obj['EOTDocID'].'"><i class="fas fa-download"></i></button></div>':'', 6);
        $content .= "</div>";

        $content .= "<div class='row'>";
			$content .= formLayout::rowDisplayLineNew(L('Status'), generalStatus::find($obj['status'])->name, 6);
		$content .= "</div>";

		return new Data(['success'=>true, 'message'=>$content]);
		
	}	

    public function conditionDetail($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::getConditionDetail($request->get->id, \PDO::FETCH_NAMED);

		$content = "<div class='row'>";     
			$content .= formLayout::rowDisplayLineNew(L('condition.no'), $obj['conditionNo'], 6);
            $content .= formLayout::rowDisplayLineNew(L('condition.deadline'), $obj['deadline'], 6);
        $content .= "</div>";
        $content .= "<div class='row'>";     
            $content .= formLayout::rowDisplayLineNew(L('condition.description'), $obj['description'], 12);
        $content .= "</div>";
        $content .= "<div class='row'>";         
			$content .= formLayout::rowDisplayLineNew(L('Status'), generalStatus::find($obj['status'])->name, 6);
		$content .= "</div>";

		return new Data(['success'=>true, 'message'=>$content]);
		
	}	    

    public static function getStatusStatistic() {
        $data = [];

		$statusList = tpbStatus::findAll();

		foreach($statusList as $status) {
			$data[$status['name']] = self::findAll($status['id'])->rowCount();
		}

		return $data;
    }

    public static function getStatusCount() {
        
        $data[] = ["id"=>0, "count"=>self::findAll(0)->rowCount()];

		$statusList = tpbStatus::findAll();

		foreach($statusList as $status) {
			$data[] = ["id"=>$status['id'], "count"=>self::findAll($status['id'])->rowCount()];
		}

        return new Data(['success'=>true, 'message'=>json_encode($data)]);
    }    

    public static function genTableHeader() {
        $htmlContent = "";
        $htmlContent .= "<thead>";
            $htmlContent .= "<tr>";
                $htmlContent .= "<th>".L('ID')."</th>";
                $htmlContent .= "<th>".L('tpb.refNo')."</th>";
                $htmlContent .= "<th>".L('tpb.client')."</th>";
                $htmlContent .= "<th>".L('tpb.officer')."</th>";
                $htmlContent .= "<th>".L('tpb.submissionDate')."</th>";
                $htmlContent .= "<th>".L('tpb.lastUpdateDate')."</th>";
                $htmlContent .= "<th>".L('tpb.number')."</th>";                                     
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
                $htmlContent .= "<th>".L('tpb.refNo')."</th>";
                $htmlContent .= "<th>".L('tpb.client')."</th>";
                $htmlContent .= "<th></th>";
                $htmlContent .= "<th>".L('tpb.submissionDate')."</th>";
                $htmlContent .= "<th>".L('tpb.lastUpdateDate')."</th>";
                $htmlContent .= "<th>".L('tpb.number')."</th>";
                $htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }

    public static function genTableContentData($tpbStatusID=0) {
        $sql = Sql::select(['tpb', 'tpb'])
        ->leftJoin(['tpbStatus', 'tpbStatus'], "tpb.tpbStatusID = tpbStatus.id")
        ->leftJoin(['client', 'clients'], "tpb.clientID = clients.id")
        ->leftJoin(['tpbOfficer', 'tpbOfficer'], "tpb.id = tpbOfficer.tpbID")
        ->leftJoin(['user', 'officer'], "officer.id = tpbOfficer.userID");

        if(!empty($tpbStatusID)){
            $sql->where(['tpb.tpbStatusID', '=', $tpbStatusID]);
        }
        
        $sql->where(['1', 'GROUP BY', 'tpb.id']);
        $sql->setFieldValue('
            tpb.id id, 
            tpb.refNo refNo, 
            clients.contactPerson clientName, 
            tpbOfficer.userID userID, 
            tpb.submissionDate submissionDate, 
            tpb.lastUpdateDate lastUpdateDate, 
            tpb.TPBNo TPBNo, 
            tpbStatus.name statusName                  
        ');

        $stm = $sql->prepare();
        $stm->execute();
        return $stm;
    }

    public static function genTableBodyRow($listObj) {

        $officeList = self::findOfficer($listObj['id']);
        $arrOfficerName = [];
        foreach($officeList as $officer) {
            $arrOfficerName[] =user::find($officer['userID'])->displayName;
        }

        $htmlContent = "";
        $htmlContent .= "<tr>";
            $htmlContent .= "<td>".$listObj['id']."</td>";
            $htmlContent .= "<td>".$listObj['refNo']."</td>";
            $htmlContent .= "<td>".$listObj['clientName']."</td>";
            $htmlContent .= "<td>".implode(",", $arrOfficerName)."</td>";
            $htmlContent .= "<td>".($listObj['submissionDate']=="0000-00-00"?" ":$listObj['submissionDate'])."</td>";
            $htmlContent .= "<td>".($listObj['lastUpdateDate']=="0000-00-00"?" ":$listObj['lastUpdateDate'])."</td>";
            $htmlContent .= "<td>".$listObj['TPBNo']."</td>";                                   
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
                            //$htmlContent .= "<button class='btn btn-md btn-outline-dark btnFollowUp' type='button' data-id='".$listObj['id']."'><i class='far fa-sm fa-clipboard'></i> ".L('FollowUp')."</button>";
                            //$htmlContent .= "<button class='btn btn-md btn-outline-dark btnReceive' type='button' data-id='".$listObj['id']."'><i class='fas fa-sm fa-reply'></i> ".L('Receive')."</button>";
                            //$htmlContent .= "<button class='btn btn-md btn-outline-dark btnDecision' type='button' data-id='".$listObj['id']."'><i class='fas fa-sm fa-gavel'></i> ".L('Decision')."</button>";
                            //$htmlContent .= "<button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$listObj['id']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>";
                        $htmlContent .= "</div></li>";
                    $htmlContent .= "</ul>";
                    $htmlContent .= "</div>";
                $htmlContent .= "</div>";
            $htmlContent .= "</td>";
        $htmlContent .= "</tr>";

        return $htmlContent;
    }

    public static function genConditionTableHeader() {
        $htmlContent = "";
        $htmlContent .= "<thead>";
            $htmlContent .= "<tr>";
               $htmlContent .= "<th>".L('ID')."</th>";
                $htmlContent .= "<th>".L('condition.no')."</th>";
                $htmlContent .= "<th>".L('condition.description')."</th>";
                $htmlContent .= "<th>".L('condition.deadline')."</th>";
                $htmlContent .= "<th>".L('Status')."</th>";                                    
                $htmlContent .= "<th>".L('Actions')."</th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</thead>";

        return $htmlContent;
    }

    public static function genConditionTableFooter() {
        $htmlContent = "";
        $htmlContent .= "<tfoot>";
                $htmlContent .= "<tr>";
                $htmlContent .= "<th>".L('ID')."</th>";
                $htmlContent .= "<th>".L('condition.no')."</th>";
                $htmlContent .= "<th>".L('condition.description')."</th>";
                $htmlContent .= "<th>".L('condition.deadline')."</th>";
                $htmlContent .= "<th>".L('Status')."</th>";  
                $htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }

    public static function genConditionTableContentData($tpbID=0) {
        $sql = Sql::select(['tpbCondition', 'conditions'])
        ->leftJoin(['tpb', 'tpb'], "conditions.tpbID = tpb.id")
        ->leftJoin(['generalStatus', 'generalStatus'], "conditions.status = generalStatus.id");

        if(!empty($tpbID)){
            $sql->where(['conditions.tpbID', '=', $tpbID]);
        }

        $sql->setFieldValue('
            conditions.id id, 
            tpb.id tpbID, 
            conditions.conditionNo conditionNo, 
            conditions.description description, 
            conditions.deadline deadline, 
            generalStatus.name statusName                  
        ');

        $stm = $sql->prepare();
        $stm->execute();
        return $stm;
    }    

    public static function genConditionTableBodyRow($listObj) {

        $htmlContent = "";
        $htmlContent .= "<tr>";
            $htmlContent .= "<td>".$listObj['id']."</td>";
            $htmlContent .= "<td>".$listObj['conditionNo']."</td>";
            $htmlContent .= "<td>".$listObj['description']."</td>";
            $htmlContent .= "<td>".($listObj['deadline']=="0000-00-00 00:00:00"?" ":$listObj['deadline'])."</td>";
            $htmlContent .= "<td>".$listObj['statusName']."</td>";                               
            $htmlContent .= "<td>";
                $htmlContent .= "<div class='btn-group' role='group' aria-label=''>";
                    $htmlContent .= "<div class='btn-group' role='group'>";
                    $htmlContent .= "<button id='btnGroupDrop".$listObj['id']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>";
                    $htmlContent .= L('Actions');
                    $htmlContent .= "</button>";
                    $htmlContent .= "<ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$listObj['id']."'>";
                        $htmlContent .= "<li><div class='d-grid'>";
                            $htmlContent .= "<button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$listObj['id']."' data-tpbid='".$listObj['tpbID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>";
                            $htmlContent .= "<button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$listObj['id']."' data-tpbid='".$listObj['tpbID']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>";
                            $htmlContent .= "<button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$listObj['id']."' data-tpbid='".$listObj['tpbID']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>";                           
                        $htmlContent .= "</div></li>";
                    $htmlContent .= "</ul>";
                    $htmlContent .= "</div>";
                $htmlContent .= "</div>";
            $htmlContent .= "</td>";
        $htmlContent .= "</tr>";

        return $htmlContent;
    }    

    public static function genEOTTableHeader() {
        $htmlContent = "";
        $htmlContent .= "<thead>";
            $htmlContent .= "<tr>";
               $htmlContent .= "<th>".L('ID')."</th>";
                $htmlContent .= "<th>".L('eot.extendMonth')."</th>";
                $htmlContent .= "<th>".L('eot.reason')."</th>";
                $htmlContent .= "<th>".L('eot.submissionDate')."</th>";
                $htmlContent .= "<th>".L('eot.submissionMode')."</th>";
               // $htmlContent .= "<th>".L('eot.submissionDoc')."</th>";   
                $htmlContent .= "<th>".L('Status')."</th>";                                    
                $htmlContent .= "<th>".L('Actions')."</th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</thead>";

        return $htmlContent;
    }

    public static function genEOTTableFooter() {
        $htmlContent = "";
        $htmlContent .= "<tfoot>";
                $htmlContent .= "<tr>";
                $htmlContent .= "<th>".L('ID')."</th>";
                $htmlContent .= "<th>".L('eot.extendMonth')."</th>";
                $htmlContent .= "<th>".L('eot.reason')."</th>";
                $htmlContent .= "<th>".L('eot.submissionDate')."</th>";
                $htmlContent .= "<th>".L('eot.submissionMode')."</th>";
                //$htmlContent .= "<th>".L('eot.submissionDoc')."</th>";   
                $htmlContent .= "<th>".L('Status')."</th>";  
                $htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }

    public static function genEOTTableContentData($tpbID=0) {
        $sql = Sql::select(['tpbEOT', 'eot'])
        ->leftJoin(['tpb', 'tpb'], "eot.tpbID = tpb.id")
        ->leftJoin(['submissionMode', 'submissionMode'], "eot.submissionModeID = submissionMode.id")
        ->leftJoin(['generalStatus', 'generalStatus'], "eot.status = generalStatus.id");

        if(!empty($tpbID)){
            $sql->where(['eot.tpbID', '=', $tpbID]);
        }

        $sql->setFieldValue('
            eot.id id, 
            tpb.id tpbID, 
            eot.extendMonth extendMonth, 
            eot.reason reason, 
            eot.submissionDate submissionDate, 
            submissionMode.name sumissionMode,
            eot.EOTDocID EOTDocID, 
            generalStatus.name statusName                  
        ');

        $stm = $sql->prepare();
        $stm->execute();
        return $stm;
    }    

    public static function genEOTTableBodyRow($listObj) {

        $htmlContent = "";
        $htmlContent .= "<tr>";
            $htmlContent .= "<td>".$listObj['id']."</td>";
            $htmlContent .= "<td>".$listObj['extendMonth']."</td>";
            $htmlContent .= "<td>".$listObj['reason']."</td>";
            $htmlContent .= "<td>".($listObj['submissionDate']=="0000-00-00"?" ":$listObj['submissionDate'])."</td>";
            $htmlContent .= "<td>".$listObj['sumissionMode']."</td>";
            //$htmlContent .= "<td>".$listObj['EOTDocID']."</td>";    
            $htmlContent .= "<td>".$listObj['statusName']."</td>";                               
            $htmlContent .= "<td>";
                $htmlContent .= "<div class='btn-group' role='group' aria-label=''>";
                    $htmlContent .= "<div class='btn-group' role='group'>";
                    $htmlContent .= "<button id='btnGroupDrop".$listObj['id']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>";
                    $htmlContent .= L('Actions');
                    $htmlContent .= "</button>";
                    $htmlContent .= "<ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$listObj['id']."'>";
                        $htmlContent .= "<li><div class='d-grid'>";
                            $htmlContent .= "<button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$listObj['id']."' data-tpbid='".$listObj['tpbID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>";    
                            $htmlContent .= "<button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$listObj['id']."' data-tpbid='".$listObj['tpbID']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>";
                            $htmlContent .= "<button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$listObj['id']."' data-tpbid='".$listObj['tpbID']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>";                           
                        $htmlContent .= "</div></li>";
                    $htmlContent .= "</ul>";
                    $htmlContent .= "</div>";
                $htmlContent .= "</div>";
            $htmlContent .= "</td>";
        $htmlContent .= "</tr>";

        return $htmlContent;
    }    

    public static function removeDoc($request) {	
       
        if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.documentEmptyID')]);	

        $docObj = documentHelper::find($request->get->id);

		if (is_null($docObj))
			return new Data(['success'=>false, 'message'=>L('error.documentNotFound')]);	        
        
        if (!isset($request->post->tpbID) || empty($request->post->tpbID))
		    return new Data(['success'=>false, 'message'=>L('error.clientEmptyID')]);	
        
        $tpbObj = self::find($request->post->tpbID);

        if (is_null($tpbObj))
            return new Data(['success'=>false, 'message'=>L('error.clientNotFound')]);	
		
        if(documentHelper::delete($docObj->id)) {

            $sql = Sql::update('tpb')->setFieldValue([$request->post->docType => '0'])->where(['id', '=', $request->post->tpbID]);
            if($sql->prepare()->execute()){
                return new Data(['success'=>true, 'message'=>L('info.documentDeleted')]);	
            } else {
                return new Data(['success'=>false, 'message'=>L('error.documentDeleteFailed')]);	
            }

        } else {
            return new Data(['success'=>false, 'message'=>L('error.documentDeleteFailed')]);	
        }
        
	}   

    public static function removeEOTDoc($request) {	
       
        if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.documentEmptyID')]);	

        $docObj = documentHelper::find($request->get->id);

		if (is_null($docObj))
			return new Data(['success'=>false, 'message'=>L('error.documentNotFound')]);	        
        
        if (!isset($request->post->eotID) || empty($request->post->eotID))
		    return new Data(['success'=>false, 'message'=>L('error.eotEmptyID')]);	
        
        $eotObj = self::getEOTDetail($request->post->eotID);

        if (is_null($eotObj))
            return new Data(['success'=>false, 'message'=>L('error.eotNotFound')]);	
		
        if(documentHelper::delete($docObj->id)) {

            $sql = Sql::update('tpbEOT')->setFieldValue([$request->post->docType => '0'])->where(['id', '=', $request->post->eotID]);
            if($sql->prepare()->execute()){
                return new Data(['success'=>true, 'message'=>L('info.documentDeleted')]);	
            } else {
                return new Data(['success'=>false, 'message'=>L('error.documentDeleteFailed')]);	
            }

        } else {
            return new Data(['success'=>false, 'message'=>L('error.documentDeleteFailed')]);	
        }
        
	}   

    // test only
    public static function sendNotification($data) {
        
        $data = new \stdClass;

        $data->emailTemplateID = 4;
        $data->recipientEmail = "samieltsang@hotmail.com";
        $data->recipientName = "samiel tsang";
        $data->TPBNo = "TEST";

        if(notification::sendEmail($data)){
            return new Data(['success'=>false, 'message'=>L('EmailSent')]);	
        } else {
            return new Data(['success'=>false, 'message'=>L('error.unableSendEmail')]);	
        }
    }

    public static function autoGenerateInitialTask($tpbID) {

        /*
        Generate Tasks with deadline 4 month after created New Application
        Preparation of authorization letter
        Land Registry IF Land Owner
        Site Notice IF NOT Land Owner
        Letter to RC IF NOT Land Owner
        Submit Application
        */

        $tpbObj = self::find($tpbID);
        $officerObj = self::findOfficer($tpbID);

        // preparation of authoization letter 
        foreach($officerObj as $officerInfo) {
            
            $authorizationLetterArray = [
                'userID'=> $officerInfo['userID'],
                'tpbID'=> $tpbID,
                'conditionID'=> 0,
                'description'=> "(TPB ID: ".$tpbID.") Preparation of Authorization Letter",
                'deadline'=> date('Y-m-d H:i:s', strtotime('+4 months'))
            ];             

            task::createTask($authorizationLetterArray);

            if($tpbObj->isLandOwner=="Y") {
                // land registry
    
                $authorizationLetterArray = [
                    'userID'=> $officerInfo['userID'],
                    'tpbID'=> $tpbID,
                    'conditionID'=> 0,
                    'description'=> "(TPB ID: ".$tpbID.") Land Registry",
                    'deadline'=> date('Y-m-d H:i:s', strtotime('+4 months'))
                ];             
    
                task::createTask($authorizationLetterArray);           
    
            } else {
    
                // site notice
                $siteNoticeArray = [
                    'userID'=> $officerInfo['userID'],
                    'tpbID'=> $tpbID,
                    'conditionID'=> 0,
                    'description'=> "(TPB ID: ".$tpbID.") Site Notice",
                    'deadline'=> date('Y-m-d H:i:s', strtotime('+4 months'))
                ];             
    
                task::createTask($siteNoticeArray);      
                
                // letter to RC
                $letterToRCArray = [
                    'userID'=> $officerInfo['userID'],
                    'tpbID'=> $tpbID,
                    'conditionID'=> 0,
                    'description'=> "(TPB ID: ".$tpbID.") Letter to RC",
                    'deadline'=> date('Y-m-d H:i:s', strtotime('+4 months'))
                ];             
    
                task::createTask($letterToRCArray);                  
    
            }
    
            // Submit Application
            $submitApplicationArray = [
                'userID'=> $officerInfo['userID'],
                'tpbID'=> $tpbID,
                'conditionID'=> 0,
                'description'=> "(TPB ID: ".$tpbID.") Submit TPB Application",
                'deadline'=> date('Y-m-d H:i:s', strtotime('+4 months'))
            ];             
    
            task::createTask($submitApplicationArray);            
            
        }


    }

    public static function getConditionDateByMonth($request) {

		$calendar = new \donatj\SimpleCalendar($request->get->month);
		$calendar->setWeekDayNames([ L('Sun'), L('Mon'), L('Tue'), L('Wed'), L('Thu'), L('Fri'), L('Sat') ]);
		$calendar->setStartOfWeek('Sunday');   
		
		// tpb
		$sqlAll = Sql::select('tpbCondition')->where(['tpbID', '=', $request->post->tpbID]);
		$sqlAll->order('tpbCondition.deadline', 'asc');
		
		$stmAll = $sqlAll->prepare();
		$stmAll->execute();		
		
		foreach($stmAll as $obj) {			
			//$content = "<div class='d-grid gap-2'><button type='button' class='btn btn-info btn-sm btnEdit' data-id='".$obj['id']."'>Condition#".$obj['conditionNo']."<br>".$obj['deadline']."</button></div>";
            $content = "<div class='d-grid gap-2'><button type='button' class='btn btn-info btn-sm btnCalendarEdit' data-type='condition' data-id='".$obj['id']."'>Condition#".$obj['conditionNo']."</button></div>";
			$calendar->addDailyHtml($content, date('Y-m-d', strtotime($obj['deadline'])));	
		}	

		return new Data(['success'=>true, 'message'=>$calendar->render()]);

	}
}