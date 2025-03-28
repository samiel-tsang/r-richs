<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem, Utility\Excel, Utility\Email; 
use Controller\documentHelper, Controller\formLayout;

class stt implements Listable {
	private $stmStatus = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("stt")->where(['id', '=', $id]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

    public static function findAll($condition="", $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("stt");

        if(!empty($condition)) {
            $sql->where(['status', '=', $condition]);
        }
        
		$stm = $sql->prepare();
		$stm->execute();
		return $stm;
	}    

    public static function findMailingLog($sttID, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("sttMailingLog")->where(['sttID', '=', $sttID]);
		$stm = $sql->prepare();
		$stm->execute();
		return $stm;
	}     

	public static function getMailingLogDetail($mailingLogID, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("sttMailingLog")->where(['id', '=', $mailingLogID]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}    

	public function extraProcess($listObj) {

		if (is_null($this->stmStatus))
			$this->stmStatus = Sql::select('generalStatus')->where(['id', '=', "?"])->prepare();
			
		$this->stmStatus->execute([$listObj->status]);
		$objStatus = $this->stmStatus->fetch();
		$listObj->statusName = $objStatus['name'];
		
		return $listObj;
	}

    public function list($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false)); 

		$obj = null;
		return new FormPage('stt/list', $obj);
	}

    public function delete($request) {	
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyID')]);	
			
		$sql = Sql::delete('stt')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {
			return new Data(['success'=>true, 'message'=>L('info.sttDeleted')]);	
		} else {
			return new Data(['success'=>false, 'message'=>L('error.sttFailed')]);	
		}					
	}    

    public function sttForm($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);

        $tpbID = 0;
        if (isset($request->post->tpbID) && !empty($request->post->tpbID)) {
            $tpbID = $request->post->tpbID;
            $tpbObj = tpb::find($tpbID);
        }
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

		$formName = "form-addStt";

		if(!is_null($obj)) {
			$formName = "form-editStt";
		}				

        $content = "<form id='".$formName."' class='' autocomplete='off'>";

            $content .= "<div class='row'>";
                $content .= "<div class='col-md-12'>";
                    $content .= "<div class='card'>";

                        $content .= "<div class='card-header'>";
                            $content .= "<div class='d-flex align-items-center'>";
                                $content .= "<h4 class='card-title' id='notice'>".L('info.sttAddHelperMessage')."</h4>";
                            $content .= "</div>";
                        $content .= "</div>";

                        $content .= "<div class='card-body'>";

                            $content .= "<ul class='nav nav-pills nav-secondary' id='pills-tab-sttAdd' role='tablist'>";

                                $content .= "<li class='nav-item submenu sttAddMenu' role='presentation'>";
                                    $content .= "<a class='sttAddMenuA nav-link active' id='pills-sttapplication-tab' data-bs-toggle='pill' href='#pills-sttapplication' role='tab' aria-controls='pills-sttapplication' aria-selected='false' tabindex='-1'>".L('stt.applicationDetail')."</a>";
                                $content .= "</li>";

                                $content .= "<li class='nav-item submenu sttAddMenu' role='presentation'>";
                                    $content .= "<a class='sttAddMenuA nav-link ' id='pills-mailing-tab' data-bs-toggle='pill' href='#pills-mailing' role='tab' aria-controls='pills-mailing' aria-selected='false' tabindex='-1'>".L('stt.mailingDetail')."</a>";
                                $content .= "</li>";                                

                            $content .= "</ul>";
                            
                            $content .= "<div class='tab-content mt-2 mb-3' id='pills-tabContent-sttAdd'>";

                               // application tab
                                $content .= "<div class='tab-pane sttAddMenuDiv fade show active' id='pills-sttapplication' role='tabpanel' aria-labelledby='pills-sttapplication-tab'>";
                                    
                                    $content .= "<div class='row'>";
                                        $content .= formLayout::rowInputNew(L('stt.refNo'),'refNo', 'refNo', 'text',  6, [], [], is_null($obj)?'':$obj['refNo']);                       
                             
                                        if($tpbID==0){
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
                                        } else {
                                            $option = [$tpbID=>$tpbObj->TPBNo];
                                        }
                                        
                                        $content .= formLayout::rowSelectNew(L('tpb.number'), 'tpbID', 'tpbID', $option,  6, [], ['required'], is_null($obj)?'':$obj['tpbID']);                                                  
                                                                
                                    $content .= "</div>";
                    
                                    $content .= "<div class='row'>";
                                        if($tpbID==0){                                        
                                            $option = [""=>"", "Add"=>"[".L('Add')."]"];
                                            $stm = Sql::select('client')->where(['status', '=', 1])->prepare();
                                            $stm->execute();                                          
                                            foreach ($stm as $opt) {  
                                                $option[$opt['id']] = $opt['contactPerson'];			  
                                            }
                                        } else {
                                            $option = [$tpbObj->clientID=>client::find($tpbObj->clientID)->contactPerson];
                                        }
                                        $content .= formLayout::rowSelectNew(L('stt.client'), 'clientID', 'clientID', $option,  6, ['clientSelect'], ['required'], is_null($obj)?'':$obj['clientID']);  
                                    $content .= "</div>";
                        
                                    $content .= "<div class='row' id='sttSelectedClentDetail'>";
                                        
                                    $content .= "</div>";
                        
                                    $content .= "<div class='row'>";

                                    if($tpbID==0){  
                                        $content .= formLayout::rowInputNew(L('stt.addressDDLot'),'addressDDLot', 'addressDDLot', 'text',  12, [], [], is_null($obj)?'':$obj['addressDDLot']);         
                                    } else {
                                        $content .= formLayout::rowInputNew(L('stt.addressDDLot'),'addressDDLot', 'addressDDLot', 'text',  12, [], [], is_null($obj)?$tpbObj->addressDDLot:$obj['addressDDLot']);         
                                    }
                                    
                                    $content .= "</div>";

                                    $content .= "<div class='row'>";
                                        $content .= formLayout::rowInputNew(L('stt.submissionDate'),'submissionDate', 'submissionDate', 'text',  6, ['customDateTime'], [], is_null($obj)?'':$obj['submissionDate']);         
                                    $content .= "</div>";

                                    $content .= "<div class='row'>";
                                        $content .= formLayout::rowInputNew(L('stt.submissionDoc'),'STTDoc', 'STTDoc', 'file',  6, [], ['accept="image/*, application/pdf"']);
                                        if(isset($obj['STTDocID']) && $obj['STTDocID']>0) {
                                            $content .= formLayout::rowDisplayClearGroupLineNew('<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs mt-2" data-id="'.$obj['STTDocID'].'"><i class="fas fa-download"></i></button><button type="button" class="btn btn-danger removeDoc btn-xs mt-2" data-id="'.$obj['STTDocID'].'" data-stt="'.$obj['id'].'" data-doc="STTDocID"><i class="fas fa-trash"></i></button></div>',6);
                                        } else {
                                            $content .= formLayout::rowDisplayClearGroupLineNew('<div class="d-flex gap-2 btnGrp" style="display: none !important"><button type="button" class="btn btn-black downloadDoc btn-xs mt-2" data-id="0"><i class="fas fa-download"></i></button><button type="button" class="btn btn-danger removeDoc btn-xs mt-2" data-id="0" data-stt="'.$obj['id'].'" data-doc="STTDocID"><i class="fas fa-trash"></i></button></div>',6);
                                        }     

                                    $content .= "</div>";   
                                    
                                    $content .= "<div class='row'>";
                                        if(!is_null($obj)) {
                                            $option = [];
                                            $stm = Sql::select('generalStatus')->prepare();
                                            $stm->execute();                                          
                                            foreach ($stm as $opt) {  
                                                $option[$opt['id']] = $opt['name'];
                                            }
                                            $content .= formLayout::rowSelectNew(L('Status'), 'status', 'status', $option, 6, [], [], is_null($obj)?'':$obj['status']);
                                        }        
                                    $content .= "</div>";                               

                                $content .= "</div>";                                 

                                // mailing tab
                                $content .= "<div class='tab-pane sttAddMenuDiv fade' id='pills-mailing' role='tabpanel' aria-labelledby='pills-mailing-tab'>";
                                    $content .= "<div class='row'>";
                                        if($formName == "form-addStt") {
                                            $content .= '<div id="mailingLogArea">';
                                                $content .= '<div class="row mb-0">';
                                                    $content .= '<div class="col-md-6 col-lg-6 mt-3"></div>';
                                                    $content .= '<div class="col-md-6 col-lg-6 text-end"><label for="" class=""><button type="button" class="btn btn-sm btn-primary btn-round" id="addMailingLogRow"><i class="fas fa-plus"></i></button></label></div>';
                                                $content .= '</div>';
                                                $row=0;
                                                $mailingLogList = self::findMailingLog(is_null($obj)?'0':$obj['id']);
                                                if($mailingLogList->rowCount()>0){

                                                    foreach ($mailingLogList as $mailingLog) {        
                                                        $content .= '<div class="col-md-12 col-lg-12 mailingLogRow" id="mailingLogRow_'.$row.'">';
                                                            $content .= '<div class="form-group">';                   
                                                                    $content .= '<div class="input-group">';
                                                                        $content .= '<input type="hidden" class="form-control" placeholder="Line Status" id="lineStatus_'.$row.'" name="lineStatus[]" value="'.$mailingLog['id'].'" >';
                                                                        $content .= '<input type="text" class="form-control customDateTime w-20" placeholder="Date" id="date_'.$row.'" name="date[]" value="'.($mailingLog['mailingDate']=="0000-00-00 00:00:00"?"":$mailingLog['mailingDate']).'" >';
                                                                        $content .= '<input type="text" class="form-control w-20" placeholder="From" id="from_'.$row.'" name="from[]" value="'.$mailingLog['mailingFrom'].'" >';
                                                                        $content .= '<input type="text" class="form-control w-50" placeholder="Content" id="content_'.$row.'" name="content[]" value="'.$mailingLog['mailingContent'].'" >';
                                                                        $content .= '<button type="button" class="btn btn-sm btn-danger removeMailingLogRow"><i class="fas fa-trash"></i></button>';
                                                                    $content .= '</div>';                
                                                                $content .= '<small id="mailingLog_'.$row.'Help" class="form-text text-muted hintHelp"></small>';
                                                            $content .= '</div>';
                                                        $content .= '</div>';      
                                                        $row++;  
                                                    }

                                                } else {
                                                    $content .= '<div class="col-md-12 col-lg-12 mailingLogRow" id="mailingLogRow_'.$row.'">';
                                                        $content .= '<div class="form-group">';                   
                                                                $content .= '<div class="input-group">';
                                                                    $content .= '<input type="hidden" class="form-control" placeholder="Line Status" id="lineStatus_'.$row.'" name="lineStatus[]" value="0" >';
                                                                    $content .= '<input type="text" class="form-control customDateTime w-20" placeholder="Date" id="date_'.$row.'" name="date[]" value="">';
                                                                    $content .= '<input type="text" class="form-control w-20" placeholder="From" id="from_'.$row.'" name="from[]" value="">';
                                                                    $content .= '<input type="text" class="form-control w-50" placeholder="Content" id="content_'.$row.'" name="content[]" value="">';
                                                                    $content .= '<button type="button" class="btn btn-sm btn-danger removeMailingLogRow"><i class="fas fa-trash"></i></button>';
                                                                $content .= '</div>';                
                                                            $content .= '<small id="mailingLog_'.$row.'Help" class="form-text text-muted hintHelp"></small>';
                                                        $content .= '</div>';
                                                    $content .= '</div>';                   
                                                }

                                            $content .= "</div>";   
                                        } else {

                                            $content .= "<div class='table-responsive'>";
                                                $content .= "<div class='row'><div class='col-md-12 col-lg-12 text-end'><button class='btn btn-primary btn-round ms-auto addMailingLogBtn' data-id='".$request->get->id."'><i class='fa fa-plus'></i></button></div></div>";
                                                $content .= "<table id='mailingLogTable' class='display table table-striped table-hover mailingLogTable'>";
                                                    $content .= self::genMailingLogTableHeader();
                                                    $content .= self::genMailingLogTableFooter();
                                                    $content .= "<tbody>";
                                                    $logContent = self::genMailingLogTableContentData($request->get->id);
                                                    foreach($logContent as $listObj) {
                                                        $content .= self::genMailingLogTableBodyRow($listObj);                                    
                                                    }
                                                    $content .= "</tbody>";
                                                $content .= "</table>";
                                            $content .= "</div>";
                                            
                                        }

                                    $content .= "</div>";                                   
                                $content .= "</div>";                                

                            $content .= "</div>";

                        $content .= "</div>"; // end card body

                    $content .= "</div>"; // end card
                $content .= "</div>"; // end grad-12
            $content .= "</div>"; // end row

        $content .= "</form>"; // end form

		return new Data(['success'=>true, 'message'=>$content, 'sttID'=>is_null($obj)?'1':$obj['id']]);
		
	}
	
    public function add($request) {	

		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);

        // form check
		/*
        if (!isset($request->post->refNo) || empty($request->post->refNo)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyRefNo'), 'field'=>'refNo', 'tab'=>'pills-sttapplication']);
        
        if (!isset($request->post->tpbID) || empty($request->post->tpbID)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyTPB'), 'field'=>'tpbID', 'tab'=>'pills-sttapplication']);
        */
        if (!isset($request->post->clientID) || empty($request->post->clientID)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyClient'), 'field'=>'clientID', 'tab'=>'pills-sttapplication']);

        /*
        if (!isset($request->post->addressDDLot) || empty($request->post->addressDDLot)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyAddressDDLot'), 'field'=>'addressDDLot', 'tab'=>'pills-sttapplication']);       
        
        if (!isset($request->post->submissionDate) || empty($request->post->submissionDate)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptySubmissionDate'), 'field'=>'submissionDate', 'tab'=>'pills-sttapplication']);           
        */

        // file upload
        $STTDocID = 0;

        if (isset($request->files->STTDoc) && !empty($request->files->STTDoc)) {
            $STTDocID = documentHelper::upload($request->files->STTDoc, "STT");
        }     
		
        // insert database
		$sql = Sql::insert('stt')->setFieldValue([
            'refNo' => "?", 
            'tpbID' => "?", 
            'clientID' => "?", 
            'addressDDLot' => "?", 
            'submissionDate'=>"?", 
            'STTDocID'=>"?",            
            'createBy'=>$currentUserObj->id, 
            'modifyBy'=>$currentUserObj->id
        ]);

		if ($sql->prepare()->execute([
                strip_tags($request->post->refNo),
                strip_tags($request->post->tpbID),
                strip_tags($request->post->clientID), 
                strip_tags($request->post->addressDDLot),
                strip_tags($request->post->submissionDate), 
                strip_tags($STTDocID)
         ])) {
			
            $id = db()->lastInsertId();


            // delete removed 
            $mailingLogList = self::findMailingLog($id);
            foreach($mailingLogList as $oriMailingLogList){			
                if(!in_array($oriMailingLogList['id'], $request->post->lineStatus)){				
                    
                    $delSql = Sql::delete('sttMailingLog')->where(['id', '=', "'".$oriMailingLogList['id']."'"]);
                    if($delSql->execute()){
                        ;
                    }
                    
                }
            }      


            foreach($request->post->lineStatus as $idx => $status){

                if($status==0){ // new item
                    $sql = Sql::insert('sttMailingLog')->setFieldValue([
                        'sttID' => "?", 
                        'mailingFrom' => "?",
                        'mailingDate' => "?",
                        'mailingContent' => "?"                
                    ]);               
    
                    $sql->prepare()->execute([
                        strip_tags($id),
                        strip_tags($request->post->from[$idx]),
                        strip_tags($request->post->date[$idx]),
                        strip_tags($request->post->content[$idx])
                    ]);
                    
                } else if($status>0) { //edit record
    
                    $editFields = [];
                    $editValues = [];
                    
                    if (isset($request->post->from[$idx]) && !empty($request->post->from[$idx])) {		
                        $editFields['mailingFrom'] = "?";
                        $editValues[] = strip_tags($request->post->from[$idx]);
                    }
    
                    if (isset($request->post->date[$idx]) && !empty($request->post->date[$idx])) {		
                        $editFields['mailingDate'] = "?";
                        $editValues[] = strip_tags($request->post->date[$idx]);
                    }
    
                    if (isset($request->post->content[$idx]) && !empty($request->post->content[$idx])) {		
                        $editFields['mailingContent'] = "?";
                        $editValues[] = strip_tags($request->post->content[$idx]);
                    }                

                    $sql = Sql::update('sttMailingLog')->setFieldValue($editFields)->where(['id', '=', $status]);
    
                    if ($sql->prepare()->execute($editValues)) {
                        ;
                    }
    
                }
                
            }  
            
            $officerList = tpb::findOfficer($request->post->tpbID);

            foreach($officerList as $officer) {
                $dataArray = [
                    'userID'=> strip_tags($officer['userID']),
                    'tpbID'=> strip_tags($request->post->tpbID),
                    'conditionID'=> strip_tags(0),
                    'description'=> strip_tags("(STT ID: ".$id.") STT Application Submission"),
                    'deadline'=> strip_tags(date('Y-m-d H:i:s', strtotime('+4 months')))
                ];             
    
                task::createTask($dataArray);
            }



			return new Data(['success'=>true, 'message'=>L('info.saved'), 'id'=>$id, 'name'=>$request->post->refNo]);
			
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableInsert'), 'field'=>'notice']);
		}	

	}

    public function edit($request) {	
      
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);

		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyID'), 'field'=>'notice']);

		$sttObj = self::find($request->get->id);
		if(is_null($sttObj))
			return new Data(['success'=>false, 'message'=>L('error.sttNotFound'), 'field'=>'notice']);

        // form check
		/*
        if (!isset($request->post->refNo) || empty($request->post->refNo)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyRefNo'), 'field'=>'refNo', 'tab'=>'pills-sttapplication']);
        
        if (!isset($request->post->tpbID) || empty($request->post->tpbID)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyTPB'), 'field'=>'tpbID', 'tab'=>'pills-sttapplication']);
        */

        if (!isset($request->post->clientID) || empty($request->post->clientID)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyClient'), 'field'=>'clientID', 'tab'=>'pills-sttapplication']);

        /*
        if (!isset($request->post->addressDDLot) || empty($request->post->addressDDLot)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyAddressDDLot'), 'field'=>'addressDDLot', 'tab'=>'pills-sttapplication']);       
        
        if (!isset($request->post->submissionDate) || empty($request->post->submissionDate)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptySubmissionDate'), 'field'=>'submissionDate', 'tab'=>'pills-sttapplication']);           
        */

        $editFields = [];
		$editValues = [];

        if (isset($request->post->refNo) && !empty($request->post->refNo)) {
			$editFields['refNo'] = "?";
			$editValues[] = $request->post->refNo;
		}	

		if (isset($request->post->clientID) && !empty($request->post->clientID)) {
			$editFields['clientID'] = "?";
			$editValues[] = $request->post->clientID;
		}		

		if (isset($request->post->tpbID) && !empty($request->post->tpbID)) {
			$editFields['tpbID'] = "?";
			$editValues[] = $request->post->tpbID;
		}		        
        
		if (isset($request->post->addressDDLot) && !empty($request->post->addressDDLot)) {
			$editFields['addressDDLot'] = "?";
			$editValues[] = $request->post->addressDDLot;
		}		
        
		if (isset($request->post->submissionDate) && !empty($request->post->submissionDate)) {
			$editFields['submissionDate'] = "?";
			$editValues[] = $request->post->submissionDate;
		}		        
		
        $STTDocID = $sttObj->STTDocID;

        if (isset($request->files->STTDoc) && !empty($request->files->STTDoc)) {
            $STTDocID = documentHelper::upload($request->files->STTDoc, "STT");
            if($STTDocID>0) {
                $editFields['STTDocID'] = "?";
                $editValues[] = $STTDocID;  
            }
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
		
		$sql = Sql::update('stt')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {


           // delete removed 
           /*
           $mailingLogList = self::findMailingLog($request->get->id);
           foreach($mailingLogList as $oriMailingLogList){			
               if(!in_array($oriMailingLogList['id'], $request->post->lineStatus)){				
                   
                   $delSql = Sql::delete('sttMailingLog')->where(['id', '=', "'".$oriMailingLogList['id']."'"]);
                   if($delSql->execute()){
                       ;
                   }
                   
               }
           }      
           


           foreach($request->post->lineStatus as $idx => $status){

               if($status==0){ // new item
                   $sql = Sql::insert('sttMailingLog')->setFieldValue([
                       'sttID' => "?", 
                       'mailingFrom' => "?",
                       'mailingDate' => "?",
                       'mailingContent' => "?"                
                   ]);               
   
                   $sql->prepare()->execute([
                       strip_tags($request->get->id),
                       strip_tags($request->post->from[$idx]),
                       strip_tags($request->post->date[$idx]),
                       strip_tags($request->post->content[$idx])
                   ]);
                   
               } else if($status>0) { //edit record
   
                   $editFields = [];
                   $editValues = [];
                   
                   if (isset($request->post->from[$idx]) && !empty($request->post->from[$idx])) {		
                       $editFields['mailingFrom'] = "?";
                       $editValues[] = strip_tags($request->post->from[$idx]);
                   }
   
                   if (isset($request->post->date[$idx]) && !empty($request->post->date[$idx])) {		
                       $editFields['mailingDate'] = "?";
                       $editValues[] = strip_tags($request->post->date[$idx]);
                   }
   
                   if (isset($request->post->content[$idx]) && !empty($request->post->content[$idx])) {		
                       $editFields['mailingContent'] = "?";
                       $editValues[] = strip_tags($request->post->content[$idx]);
                   }                

                   $sql = Sql::update('sttMailingLog')->setFieldValue($editFields)->where(['id', '=', $status]);
   
                   if ($sql->prepare()->execute($editValues)) {
                       ;
                   }
   
               }
               
           }  
                */

			return new Data(['success'=>true, 'message'=>L('info.updated')]);			
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableUpdate'), 'field'=>'notice']);
		}		        
        
	}  
    
    public static function removeDoc($request) {	
       
        if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.documentEmptyID')]);	

        $docObj = documentHelper::find($request->get->id);

		if (is_null($docObj))
			return new Data(['success'=>false, 'message'=>L('error.documentNotFound')]);	        
        
        if (!isset($request->post->sttID) || empty($request->post->sttID))
		    return new Data(['success'=>false, 'message'=>L('error.sttEmptyID')]);	
        
        $sttObj = self::find($request->post->sttID);

        if (is_null($sttObj))
            return new Data(['success'=>false, 'message'=>L('error.sttNotFound')]);	
	
        if(documentHelper::delete($docObj->id)) {

            $sql = Sql::update('stt')->setFieldValue([$request->post->docType => '0'])->where(['id', '=', $request->post->sttID]);
            if($sql->prepare()->execute()){
                return new Data(['success'=>true, 'message'=>L('info.documentDeleted')]);	
            } else {
                return new Data(['success'=>false, 'message'=>L('error.documentDeleteFailed')]);	
            }

        } else {
            return new Data(['success'=>false, 'message'=>L('error.documentDeleteFailed')]);	
        }
        
	}    

    public static function genTableHeader() {
        $htmlContent = "";

        $htmlContent .= "<thead>";
            $htmlContent .= "<tr>";
                $htmlContent .= "<th>".L('ID')."</th>";
                $htmlContent .= "<th>".L('stt.refNo')."</th>";
				$htmlContent .= "<th>".L('tpb.number')."</th>";
				$htmlContent .= "<th>".L('stt.client')."</th>";
				$htmlContent .= "<th>".L('stt.addressDDLot')."</th>";
                $htmlContent .= "<th>".L('stt.submissionDate')."</th>";
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
                $htmlContent .= "<th>".L('stt.refNo')."</th>";
				$htmlContent .= "<th>".L('tpb.number')."</th>";
				$htmlContent .= "<th>".L('stt.client')."</th>";
				$htmlContent .= "<th>".L('stt.addressDDLot')."</th>";
                $htmlContent .= "<th>".L('stt.submissionDate')."</th>";
                $htmlContent .= "<th>".L('Status')."</th>";                              
                $htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }	

	public static function genTableContentData($tpbID=0) {
        $sql = Sql::select(['stt', 'stt'])        
        ->leftJoin(['generalStatus', 'generalStatus'], "stt.status = generalStatus.id")
        ->leftJoin(['tpb', 'tpb'], "stt.tpbID = tpb.id")
        ->leftJoin(['client', 'client'], "stt.clientID = client.id");
        $sql->setFieldValue('
           stt.id id, 
           stt.refNo sttRefNo, 
           tpb.TPBNo tpbNo, 
           client.contactPerson contactPerson, 
           stt.addressDDLot addressDDLot, 
           stt.submissionDate submissionDate, 
           generalStatus.name statusName                         
        ');

        if($tpbID>0){
            $sql->where(["stt.tpbID","=",$tpbID]);
        }

        $stm = $sql->prepare();
        $stm->execute();
        return $stm;
    }

	public static function genTableBodyRow($listObj) {
        $htmlContent = "";
        $htmlContent .= "<tr>";
            $htmlContent .= "<td>".$listObj['id']."</td>";
			$htmlContent .= "<td>".$listObj['sttRefNo']."</td>";
			$htmlContent .= "<td>".$listObj['tpbNo']."</td>";
			$htmlContent .= "<td>".$listObj['contactPerson']."</td>";
            $htmlContent .= "<td>".$listObj['addressDDLot']."</td>";
            $htmlContent .= "<td>".$listObj['submissionDate']."</td>";
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

    public function detail($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);

        $tpbID = 0;
        if (isset($request->post->tpbID) && !empty($request->post->tpbID)) {
            $tpbID = $request->post->tpbID;
            $tpbObj = tpb::find($tpbID);
        }
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

            $content = "<div class='row'>";
                $content .= "<div class='col-md-12'>";
                    $content .= "<div class='card'>";                        
                        $content .= "<div class='card-body'>";
                            $content .= "<ul class='nav nav-pills nav-secondary' id='pills-tab-sttAdd' role='tablist'>";
                                $content .= "<li class='nav-item submenu sttAddMenu' role='presentation'>";
                                    $content .= "<a class='sttAddMenuA nav-link active' id='pills-sttapplication-tab' data-bs-toggle='pill' href='#pills-sttapplication' role='tab' aria-controls='pills-sttapplication' aria-selected='false' tabindex='-1'>".L('stt.applicationDetail')."</a>";
                                $content .= "</li>";
                                $content .= "<li class='nav-item submenu sttAddMenu' role='presentation'>";
                                    $content .= "<a class='sttAddMenuA nav-link ' id='pills-mailing-tab' data-bs-toggle='pill' href='#pills-mailing' role='tab' aria-controls='pills-mailing' aria-selected='false' tabindex='-1'>".L('stt.mailingDetail')."</a>";
                                $content .= "</li>";
                            $content .= "</ul>";
                            
                            $content .= "<div class='tab-content mt-2 mb-3' id='pills-tabContent-sttAdd'>";

                               // application tab
                                $content .= "<div class='tab-pane sttAddMenuDiv fade show active' id='pills-sttapplication' role='tabpanel' aria-labelledby='pills-sttapplication-tab'>";
                                    
                                    $content .= "<div class='row'>";
                                        
                                        $content .= formLayout::rowDisplayLineNew(L('stt.refNo'), $obj['refNo'], 6);    
                                        $content .= formLayout::rowDisplayLineNew(L('tpb.number'), tpb::find($obj['tpbID'])->TPBNo??"", 6);    
                                        $content .= formLayout::rowDisplayLineNew(L('stt.client'), client::find($obj['clientID'])->contactPerson, 6);   
                                        $content .= formLayout::rowInputNew('','clientID', 'clientID', 'hidden',  6, [], [], $obj['clientID']);                                                                                                                 
                                                                
                                    $content .= "</div>";

                                    $content .= "<div class='row'>";
                                        $content .= formLayout::rowSeparatorLineNew(12);
                                    $content .= "</div>";
                        
                                    $content .= "<div class='row' id='sttSelectedClentDetail'></div>";

                                    $content .= "<div class='row'>";
                                        $content .= formLayout::rowSeparatorLineNew(12);
                                    $content .= "</div>";                                    
                        
                                    $content .= "<div class='row'>";

                                    $content .= formLayout::rowDisplayLineNew(L('stt.addressDDLot'), $obj['addressDDLot'], 12);   
                                    $content .= formLayout::rowDisplayLineNew(L('stt.submissionDate'), $obj['submissionDate'], 6);  
                                    $content .= formLayout::rowDisplayLineNew(L('stt.submissionDoc'), $obj['STTDocID']>0?'<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs mt-2" data-id="'.$obj['STTDocID'].'"><i class="fas fa-download"></i></button></div>':"", 6); 
                                    $content .= formLayout::rowDisplayLineNew(L('Status'), generalStatus::find($obj['status'])->name, 6);

                                    $content .= "</div>";

                            

                                $content .= "</div>";                                 

                                // mailing tab
                                $content .= "<div class='tab-pane sttAddMenuDiv fade' id='pills-mailing' role='tabpanel' aria-labelledby='pills-mailing-tab'>";
                                    $content .= "<div class='row'>";
                                            $content .= "<div class='table-responsive'>";                                                
                                                $content .= "<table id='mailingLogTable' class='display table table-striped table-hover mailingLogTable'>";
                                                    $content .= self::genMailingLogTableHeader();
                                                    $content .= self::genMailingLogTableFooter();
                                                    $content .= "<tbody>";
                                                    $logContent = self::genMailingLogTableContentData($request->get->id);
                                                    foreach($logContent as $listObj) {
                                                        $content .= self::genMailingLogTableBodyRow($listObj);                                    
                                                    }
                                                    $content .= "</tbody>";
                                                $content .= "</table>";
                                            $content .= "</div>";
                                    $content .= "</div>";                                   
                                $content .= "</div>";                                

                            $content .= "</div>";

                        $content .= "</div>"; // end card body

                    $content .= "</div>"; // end card
                $content .= "</div>"; // end grad-12
            $content .= "</div>"; // end row

		return new Data(['success'=>true, 'message'=>$content]);
		
	}

    public function mailingLogdetail($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::getMailingLogDetail($request->get->id, \PDO::FETCH_NAMED);

		$content = "<div class='row'>";     

			$content .= formLayout::rowDisplayLineNew(L('stt.mailingDate'), $obj['mailingDate'], 6);
			$content .= formLayout::rowDisplayLineNew(L('stt.mailingFrom'), $obj['mailingFrom'], 6);
            $content .= formLayout::rowTextAreaNew(L('stt.mailingContent'), 'mailingContent', 'mailingContent',  12, [], ['readonly'], $obj['mailingContent']);        

		$content .= "</div>";

		return new Data(['success'=>true, 'message'=>$content]);
		
	}	

    public static function genMailingLogTableHeader() {
        $htmlContent = "";

        $htmlContent .= "<thead>";
            $htmlContent .= "<tr>";
                $htmlContent .= "<th>".L('ID')."</th>";
                $htmlContent .= "<th>".L('stt.mailingDate')."</th>";
				$htmlContent .= "<th>".L('stt.mailingFrom')."</th>";
                $htmlContent .= "<th>".L('stt.mailingContent')."</th>";                 
                $htmlContent .= "<th>".L('Actions')."</th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</thead>";

        return $htmlContent;
    }

    public static function genMailingLogTableFooter() {
        $htmlContent = "";

        $htmlContent .= "<tfoot>";
                $htmlContent .= "<tr>";
                $htmlContent .= "<th>".L('ID')."</th>";
                $htmlContent .= "<th>".L('stt.mailingDate')."</th>";
				$htmlContent .= "<th>".L('stt.mailingFrom')."</th>";
                $htmlContent .= "<th>".L('stt.mailingContent')."</th>";                  
                $htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }	

	public static function genMailingLogTableContentData($sttID) {
		$sql = Sql::select("sttMailingLog")->where(['sttID', '=', $sttID]);   
        $stm = $sql->prepare();
        $stm->execute();
        return $stm;
    }

	public static function genMailingLogTableBodyRow($listObj) {
        $htmlContent = "";
        $htmlContent .= "<tr>";
            $htmlContent .= "<td>".$listObj['id']."</td>";
			$htmlContent .= "<td>".$listObj['mailingDate']."</td>";
			$htmlContent .= "<td>".$listObj['mailingFrom']."</td>";
			$htmlContent .= "<td>".$listObj['mailingContent']."</td>";
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
    
    public function mailingLogFormAdd($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$formName = "form-addMailingLog";
		
        $content = "<form id='".$formName."' class='' autocomplete='off'>";

            $content .= "<div class='row'>";
                $content .= "<div class='col-md-12'>";
                    $content .= "<div class='card'>";

                        $content .= "<div class='card-header'>";
                            $content .= "<div class='d-flex align-items-center'>";
                                $content .= "<h4 class='card-title' id='notice'>".L('info.sttMailingLogAddHelperMessage')."</h4>";
                            $content .= "</div>";
                        $content .= "</div>";

                        $content .= "<div class='card-body'>";   
                            $content .= "<div class='row'>";                        
                                $content .= formLayout::rowInputNew(L('stt.id'),'sttID', 'sttID', 'hidden',  6, [], [], $request->get->id); 
                            $content .= "</div>";                            
                            $content .= "<div class='row'>";
                                $content .= formLayout::rowInputNew(L('stt.mailingDate'),'mailingDate', 'mailingDate', 'text',  6, ['customDateTime'], ['required']);
                                $content .= formLayout::rowInputNew(L('stt.mailingFrom'),'mailingFrom', 'mailingFrom', 'text',  6, [], ['required']);                
                            $content .= "</div>";

                            $content .= "<div class='row'>";  
                                $content .= formLayout::rowTextAreaNew(L('stt.mailingContent'), 'mailingContent', 'mailingContent',  12, [], []);                                         
                            $content .= "</div>";

                        $content .= "</div>"; // end card body

                    $content .= "</div>"; // end card
                $content .= "</div>"; // end grad-12
            $content .= "</div>"; // end row

        $content .= "</form>"; // end form

		return new Data(['success'=>true, 'message'=>$content, 'mailingLogID'=>is_null($obj)?'1':$obj['id']]);
		
	} 

    public function mailingLogAdd($request) {
        
        if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);

		$sttObj = self::find($request->post->sttID);
		if(is_null($sttObj))
			return new Data(['success'=>false, 'message'=>L('error.sttNotFound'), 'field'=>'notice']);        

        // form check
        if (!isset($request->post->mailingDate) || empty($request->post->mailingDate)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyClient'), 'field'=>'mailingDate']);

        if (!isset($request->post->mailingFrom) || empty($request->post->mailingFrom)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyClient'), 'field'=>'mailingFrom']);            

        /*        
        if (!isset($request->post->mailingContent) || empty($request->post->mailingContent)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyAddressDDLot'), 'field'=>'mailingContent');              
               
        */

        $sql = Sql::insert('sttMailingLog')->setFieldValue([
            'sttID' => "?", 
            'mailingDate' => "?", 
            'mailingFrom' => "?", 
            'mailingContent' => "?" 
        ]);

		if ($sql->prepare()->execute([
                strip_tags($request->post->sttID),
                strip_tags($request->post->mailingDate),
                strip_tags($request->post->mailingFrom), 
                strip_tags($request->post->mailingContent)
         ])) {
			
            $id = db()->lastInsertId();

			return new Data(['success'=>true, 'message'=>L('info.saved'), 'id'=>$id]);
			
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableInsert'), 'field'=>'notice']);
		}	        
       
    }

    public function mailingLogFormEdit($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);

        $obj = null;
		if (isset($request->get->id)) 
			$obj = self::getMailingLogDetail($request->get->id, \PDO::FETCH_NAMED);
		
		$formName = "form-editMailingLog";
		
        $content = "<form id='".$formName."' class='' autocomplete='off'>";

            $content .= "<div class='row'>";
                $content .= "<div class='col-md-12'>";
                    $content .= "<div class='card'>";

                        $content .= "<div class='card-header'>";
                            $content .= "<div class='d-flex align-items-center'>";
                                $content .= "<h4 class='card-title' id='notice'>".L('info.sttMailingLogAddHelperMessage')."</h4>";
                            $content .= "</div>";
                        $content .= "</div>";

                        $content .= "<div class='card-body'>";   
                            $content .= "<div class='row'>";
                                $content .= formLayout::rowInputNew(L('stt.mailingDate'),'mailingDate', 'mailingDate', 'text',  6, ['customDateTime'], ['required'], is_null($obj)?'':$obj['mailingDate']);
                                $content .= formLayout::rowInputNew(L('stt.mailingFrom'),'mailingFrom', 'mailingFrom', 'text',  6, [], ['required'], is_null($obj)?'':$obj['mailingFrom']);                
                            $content .= "</div>";

                            $content .= "<div class='row'>";  
                                $content .= formLayout::rowTextAreaNew(L('stt.mailingContent'), 'mailingContent', 'mailingContent',  12, [], [], is_null($obj)?'':$obj['mailingContent']);                                         
                            $content .= "</div>";

                        $content .= "</div>"; // end card body

                    $content .= "</div>"; // end card
                $content .= "</div>"; // end grad-12
            $content .= "</div>"; // end row

        $content .= "</form>"; // end form

		return new Data(['success'=>true, 'message'=>$content, 'mailingLogID'=>is_null($obj)?'1':$obj['id']]);
		
	} 

    public function mailingLogEdit($request) {
        
        if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);

		$sttMailingLogObj = self::getMailingLogDetail($request->get->id);
		if(is_null($sttMailingLogObj))
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyMailingLogID'), 'field'=>'notice']);        

        // form check
        if (!isset($request->post->mailingDate) || empty($request->post->mailingDate)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyClient'), 'field'=>'mailingDate']);

        if (!isset($request->post->mailingFrom) || empty($request->post->mailingFrom)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyClient'), 'field'=>'mailingFrom']);            

        /*        
        if (!isset($request->post->mailingContent) || empty($request->post->mailingContent)) 
			return new Data(['success'=>false, 'message'=>L('error.sttEmptyAddressDDLot'), 'field'=>'mailingContent');              
               
        */

        $editFields = [];
		$editValues = [];

        if (isset($request->post->mailingDate) && !empty($request->post->mailingDate)) {
			$editFields['mailingDate'] = "?";
			$editValues[] = $request->post->mailingDate;
		}	

		if (isset($request->post->mailingFrom) && !empty($request->post->mailingFrom)) {
			$editFields['mailingFrom'] = "?";
			$editValues[] = $request->post->mailingFrom;
		}		

		if (isset($request->post->mailingContent) && !empty($request->post->mailingContent)) {
			$editFields['mailingContent'] = "?";
			$editValues[] = $request->post->mailingContent;
		}	        
       
        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('sttMailingLog')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
            return new Data(['success'=>true, 'message'=>L('info.updated')]);		
        } else {
            return new Data(['success'=>false, 'message'=>L('error.unableUpdate'), 'field'=>'notice']);
        }

    }    

    public function mailingLogDelete($request) {	
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.sttMailingLogEmptyID')]);	
			
		$sql = Sql::delete('sttMailingLog')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {
			return new Data(['success'=>true, 'message'=>L('info.sttMailingLogDeleted')]);	
		} else {
			return new Data(['success'=>false, 'message'=>L('error.sttMailingLogFailed')]);	
		}					
	}        
}