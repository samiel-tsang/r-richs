<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem, Utility\Excel, Utility\Email; 
use Controller\documentHelper, Controller\formLayout;

class client implements Listable {
	private $stmStatus = null;
    private $stmClientType = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("client")->where(['id', '=', $id]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

    public static function findAll($fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("client")->where(['status', '=', "1"]);
		$stm = $sql->prepare();
		$stm->execute();
		return $stm;
	}    

	public function extraProcess($listObj) {

		if (is_null($this->stmClientType))
			$this->stmClientType = Sql::select('clientType')->where(['id', '=', "?"])->prepare();
			
		$this->stmClientType->execute([$listObj->categoryID]);
		$objClientType = $this->stmClientType->fetch();
		$listObj->clientType = $objClientType['name'];

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
		return new FormPage('client/list', $obj);
	}

    public function delete($request) {	
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyID')]);	
			
		$sql = Sql::delete('client')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {
			return new Data(['success'=>true, 'message'=>L('info.clientDeleted')]);	
		} else {
			return new Data(['success'=>false, 'message'=>L('error.clientDeleteFailed')]);	
		}					
	}    

    public function clientForm($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

		$formName = "form-addClient";

		if(!is_null($obj)) {
			$formName = "form-editClient";
		}				

		$content = "<form id='".$formName."' class='' autocomplete='off'>";
		$content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.clientAddHelperMessage')."</p></div>";
        $content .= "<div class='row'>";
            $option = [];
            $stm = Sql::select('clientType')->where(['status', '=', 1])->prepare();
            $stm->execute();                                          
            foreach ($stm as $opt) {  
                $option[$opt['id']] = $opt['name'];			  
            }
            
            $content .= formLayout::rowRadioNew(L('client.type'), 'clientTypeID', 'clientTypeID', $option,  12, ['clientTypeSelect'], ['required'], is_null($obj)?'1':$obj['clientTypeID']);
            $content .= formLayout::rowSeparatorLineNew(12);

            $option = ["Mr"=>"Mr", "Mrs"=>"Mrs", "Miss"=>"Miss", "Ms"=>"Ms"];               
            $content .= formLayout::rowRadioNew(L('client.title'), 'title', 'title', $option,  12, [], ['required'], is_null($obj)?'Mr':$obj['title']);        

            $content .= formLayout::rowInputNew(L('client.contactPerson'),'contactPerson', 'contactPerson', 'text',  6, [], ['required'], is_null($obj)?'':$obj['contactPerson']);
            $content .= formLayout::rowInputNew(L('client.position'),'position', 'position', 'text',  6, [], [], is_null($obj)?'':$obj['position']);
            $content .= formLayout::rowInputNew(L('client.phone'),'phone', 'phone', 'tel',  6, [], [], is_null($obj)?'':$obj['phone']);
            $content .= formLayout::rowInputNew(L('client.email'),'email', 'email', 'email',  6, [], [], is_null($obj)?'':$obj['email']);        

            $content .= formLayout::rowTextAreaNew(L('client.address'), 'address', 'address',  12, [], [], is_null($obj)?'':$obj['address']);
            $content .= formLayout::rowInputNew(L('client.idCardNo'),'idCardNo', 'idCardNo', 'text',  6, [], [], is_null($obj)?'':$obj['idCardNo']);  
            $content .= formLayout::rowInputNew(L('client.idCardDoc'),'idCardDoc', 'idCardDoc', 'file',  6, [], ['accept="image/*, application/pdf"'], 
                (is_null($obj) || empty($obj['idCardDocID']))?
                '':
                '<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-sm mt-2" data-id="'.$obj['idCardDocID'].'"><i class="fas fa-download"></i></button>
                <button type="button" class="btn btn-danger removeDoc btn-sm mt-2" data-id="'.$obj['idCardDocID'].'" data-client="'.$obj['id'].'" data-doc="idCardDocID"><i class="fas fa-trash"></i></button></div>'
            );  

            $option = [""=>""];
            $stm = Sql::select('user')->where(['status', '=', 1])->prepare();
            $stm->execute();                                          
            foreach ($stm as $opt) {  
                $option[$opt['id']] = $opt['displayName'];			  
            }
            $content .= formLayout::rowSelectNew(L('client.whoseClient'), 'userID', 'userID', $option,  6, [], [], is_null($obj)?'':$obj['userID']);     
            
            $content .= formLayout::rowSeparatorLineNew(12);
            $content .= formLayout::rowInputNew(L('client.companyEnglishName'),'companyEnglishName', 'companyEnglishName', 'text',  6, ['companyInfo'], [], is_null($obj)?'':$obj['companyEnglishName']);  
            $content .= formLayout::rowInputNew(L('client.companyChineseName'),'companyChineseName', 'companyChineseName', 'text',  6, ['companyInfo'], [], is_null($obj)?'':$obj['companyChineseName']);  

            $content .= formLayout::rowCheckBoxNew(L('CompanyAddressSameAsAbove'), 'sameAddress', 'sameAddress', 6, ['companyInfo'], []);

            $content .= formLayout::rowTextAreaNew(L('client.companyAddress'), 'companyAddress', 'companyAddress',  12, ['companyInfo'], [], is_null($obj)?'':$obj['companyAddress']); 
            $content .= formLayout::rowInputNew(L('client.CINo'),'CINo', 'CINo', 'text',  6, ['companyInfo'], [], is_null($obj)?'':$obj['CINo']);  
            $content .= formLayout::rowInputNew(L('client.CIDoc'),'CIDoc', 'CIDoc', 'file',  6, ['companyInfo'], ['accept="image/*, application/pdf"'], 
                (is_null($obj) || empty($obj['CIDocID']))?
                '':
                '<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-sm mt-2" data-id="'.$obj['CIDocID'].'"><i class="fas fa-download"></i></button>
                <button type="button" class="btn btn-danger removeDoc btn-sm mt-2" data-id="'.$obj['CIDocID'].'" data-client="'.$obj['id'].'" data-doc="CIDocID"><i class="fas fa-trash"></i></button></div>'
            ); 
            $content .= formLayout::rowInputNew(L('client.BRNo'),'BRNo', 'BRNo', 'text',  6, ['companyInfo'], [], is_null($obj)?'':$obj['BRNo']);  
            $content .= formLayout::rowInputNew(L('client.BRDoc'),'BRDoc', 'BRDoc', 'file',  6, ['companyInfo'], ['accept="image/*, application/pdf"'], 
                (is_null($obj) || empty($obj['BRDocID']))?
                '':
                '<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-sm mt-2" data-id="'.$obj['BRDocID'].'"><i class="fas fa-download"></i></button>
                <button type="button" class="btn btn-danger removeDoc btn-sm mt-2" data-id="'.$obj['BRDocID'].'" data-client="'.$obj['id'].'" data-doc="BRDocID"><i class="fas fa-trash"></i></button></div>'
            ); 
        
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

		return new Data(['success'=>true, 'message'=>$content, 'clientTypeID'=>is_null($obj)?'1':$obj['clientTypeID']]);
		
	}
	
    public function add($request) {	
      
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);

        // form check
		if (!isset($request->post->clientTypeID) || empty($request->post->clientTypeID)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyType'), 'field'=>'clientTypeID']);

        if (!isset($request->post->title) || empty($request->post->title)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyTitle'), 'field'=>'title']);

        if (!isset($request->post->contactPerson) || empty($request->post->contactPerson)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyContactPerson'), 'field'=>'contactPerson']);
        /*
		if (!isset($request->post->position) || empty($request->post->position)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyPosition'), 'field'=>'position']);

        if (!isset($request->post->phone) || empty($request->post->phone)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyPhone'), 'field'=>'phone']);            

        if (!isset($request->post->email) || empty($request->post->email)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyEmail'), 'field'=>'email']);

		if (filter_var($request->post->email, FILTER_VALIDATE_EMAIL) === FALSE)
			return new Data(['success'=>false, 'message'=>L('error.userEmailInvalid'), 'field'=>'email']);		

        if (!isset($request->post->address) || empty($request->post->address)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyAddress'), 'field'=>'address']);  
        
        if (!isset($request->post->idCardNo) || empty($request->post->idCardNo)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyIDCardNo'), 'field'=>'idCardNo']); 
        
        if (!isset($request->files->idCardDoc) || empty($request->files->idCardDoc['tmp_name'])) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyIDCardDoc'), 'field'=>'idCardDoc']);             
        
        if (!isset($request->post->userID) || empty($request->post->userID)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyWhoseClient'), 'field'=>'userID']);              

        if ($request->post->clientTypeID==2) {

            if (!isset($request->post->companyEnglishName) || empty($request->post->companyEnglishName)) 
			    return new Data(['success'=>false, 'message'=>L('error.clientEmptyCompanyEnglishName'), 'field'=>'companyEnglishName']);

            if (!isset($request->post->companyChineseName) || empty($request->post->companyChineseName)) 
			    return new Data(['success'=>false, 'message'=>L('error.userEmptycompanyChineseName'), 'field'=>'companyChineseName']);

            if (!isset($request->post->companyAddress) || empty($request->post->companyAddress)) 
			    return new Data(['success'=>false, 'message'=>L('error.clientEmptyCompanyAddress'), 'field'=>'companyAddress']);
            
            if (!isset($request->post->CINo) || empty($request->post->CINo)) 
                return new Data(['success'=>false, 'message'=>L('error.clientEmptyCINo'), 'field'=>'CINo']); 
            
            if (!isset($request->files->CIDoc) || empty($request->files->CIDoc['tmp_name'])) 
                return new Data(['success'=>false, 'message'=>L('error.clientEmptyCIDoc'), 'field'=>'CIDoc']);  

            if (!isset($request->post->BRNo) || empty($request->post->BRNo)) 
                return new Data(['success'=>false, 'message'=>L('error.clientEmptyBRNo'), 'field'=>'BRNo']); 
            
            if (!isset($request->files->BRDoc) || empty($request->files->BRDoc['tmp_name'])) 
                return new Data(['success'=>false, 'message'=>L('error.clientEmptyBRDoc'), 'field'=>'BRDoc']);                  
        }
        */
        // file upload
        $idCardDocID = 0;
        $CIDocID = 0;
        $BRDocID = 0;

        if (isset($request->files->idCardDoc) && !empty($request->files->idCardDoc)) {
            $idCardDocID = documentHelper::upload($request->files->idCardDoc, "HKID");
        }

        if ($request->post->clientTypeID==2) {
            if (isset($request->files->CIDoc) && !empty($request->files->CIDoc)) {
                $CIDocID = documentHelper::upload($request->files->CIDoc, "CI");
            }
            
            if (isset($request->files->BRDoc) && !empty($request->files->BRDoc)) {
                $BRDocID = documentHelper::upload($request->files->BRDoc, "BR");
            }        
        }        
		
        // insert database
		$sql = Sql::insert('client')->setFieldValue([
            'clientTypeID' => "?", 
            'title' => "?", 
            'contactPerson' => "?", 
            'position' => "?", 
            'phone'=>"?", 
            'email'=>"?", 
            'address'=>"?", 
            'idCardNo'=>"?", 
            'idCardDocID'=>"?", 
            'userID'=>"?", 
            'companyEnglishName'=>"?", 
            'companyChineseName'=>"?", 
            'companyAddress'=>"?", 
            'CINo'=>"?", 
            'CIDocID'=>"?", 
            'BRNo'=>"?", 
            'BRDocID'=>"?",             
            'createBy'=>$currentUserObj->id, 
            'modifyBy'=>$currentUserObj->id
        ]);

		if ($sql->prepare()->execute([
                strip_tags($request->post->clientTypeID),
                strip_tags($request->post->title),
                strip_tags($request->post->contactPerson), 
                strip_tags($request->post->position),
                strip_tags($request->post->phone), 
                strip_tags($request->post->email), 
                strip_tags($request->post->address),
                strip_tags($request->post->idCardNo),
                strip_tags($idCardDocID),
                strip_tags($request->post->userID),
                strip_tags($request->post->companyEnglishName), 
                strip_tags($request->post->companyChineseName), 
                strip_tags($request->post->sameAddress=="On"?$request->post->address:$request->post->companyAddress), 
                strip_tags($request->post->CINo),
                strip_tags($CIDocID), 
                strip_tags($request->post->BRNo),
                strip_tags($BRDocID), 
         ])) {
			
            $id = db()->lastInsertId();

			return new Data(['success'=>true, 'message'=>L('info.saved'), 'id'=>$id, 'name'=>$request->post->contactPerson]);
			
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableInsert'), 'field'=>'notice']);
		}	

	}

    public function edit($request) {	
      
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);

		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyID'), 'field'=>'notice']);

		$clientObj = self::find($request->get->id);
		if(is_null($clientObj))
			return new Data(['success'=>false, 'message'=>L('error.clientNotFound'), 'field'=>'notice']);

        // form check
		if (!isset($request->post->clientTypeID) || empty($request->post->clientTypeID)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyType'), 'field'=>'clientTypeID']);

        if (!isset($request->post->title) || empty($request->post->title)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyTitle'), 'field'=>'title']);

        if (!isset($request->post->contactPerson) || empty($request->post->contactPerson)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyContactPerson'), 'field'=>'contactPerson']);
        /*
		if (!isset($request->post->position) || empty($request->post->position)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyPosition'), 'field'=>'position']);

        if (!isset($request->post->phone) || empty($request->post->phone)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyPhone'), 'field'=>'phone']);            

        if (!isset($request->post->email) || empty($request->post->email)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyEmail'), 'field'=>'email']);

		if (filter_var($request->post->email, FILTER_VALIDATE_EMAIL) === FALSE)
			return new Data(['success'=>false, 'message'=>L('error.userEmailInvalid'), 'field'=>'email']);		

        if (!isset($request->post->address) || empty($request->post->address)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyAddress'), 'field'=>'address']);  
        
        if (!isset($request->post->idCardNo) || empty($request->post->idCardNo)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyIDCardNo'), 'field'=>'idCardNo']); 
                
        if (!isset($request->files->idCardDoc) || empty($request->files->idCardDoc['tmp_name'])) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyIDCardDoc'), 'field'=>'idCardDoc']);                     
        
        if (!isset($request->post->userID) || empty($request->post->userID)) 
			return new Data(['success'=>false, 'message'=>L('error.clientEmptyWhoseClient'), 'field'=>'userID']);              

        if ($request->post->clientTypeID==2) {

            if (!isset($request->post->companyEnglishName) || empty($request->post->companyEnglishName)) 
			    return new Data(['success'=>false, 'message'=>L('error.clientEmptyCompanyEnglishName'), 'field'=>'companyEnglishName']);

            if (!isset($request->post->companyChineseName) || empty($request->post->companyChineseName)) 
			    return new Data(['success'=>false, 'message'=>L('error.userEmptycompanyChineseName'), 'field'=>'companyChineseName']);

            if (!isset($request->post->companyAddress) || empty($request->post->companyAddress)) 
			    return new Data(['success'=>false, 'message'=>L('error.clientEmptyCompanyAddress'), 'field'=>'companyAddress']);
            
            if (!isset($request->post->CINo) || empty($request->post->CINo)) 
                return new Data(['success'=>false, 'message'=>L('error.clientEmptyCINo'), 'field'=>'CINo']); 
                        
            if (!isset($request->files->CIDoc) || empty($request->files->CIDoc['tmp_name'])) 
                return new Data(['success'=>false, 'message'=>L('error.clientEmptyCIDoc'), 'field'=>'CIDoc']);  
            
            if (!isset($request->post->BRNo) || empty($request->post->BRNo)) 
                return new Data(['success'=>false, 'message'=>L('error.clientEmptyBRNo'), 'field'=>'BRNo']); 
            
            if (!isset($request->files->BRDoc) || empty($request->files->BRDoc['tmp_name'])) 
                return new Data(['success'=>false, 'message'=>L('error.clientEmptyBRDoc'), 'field'=>'BRDoc']);                              
        }
        */
        $editFields = [];
		$editValues = [];

		if (isset($request->post->clientTypeID) && !empty($request->post->clientTypeID)) {
			$editFields['clientTypeID'] = "?";
			$editValues[] = $request->post->clientTypeID;
		}		

		if (isset($request->post->title) && !empty($request->post->title)) {
			$editFields['title'] = "?";
			$editValues[] = $request->post->title;
		}		
        
		if (isset($request->post->contactPerson) && !empty($request->post->contactPerson)) {
			$editFields['contactPerson'] = "?";
			$editValues[] = $request->post->contactPerson;
		}		
        
		if (isset($request->post->position) && !empty($request->post->position)) {
			$editFields['position'] = "?";
			$editValues[] = $request->post->position;
		}		
        
		if (isset($request->post->phone) && !empty($request->post->phone)) {
			$editFields['phone'] = "?";
			$editValues[] = $request->post->phone;
		}		
        
		if (isset($request->post->email) && !empty($request->post->email)) {
			$editFields['email'] = "?";
			$editValues[] = $request->post->email;
		}		
        
		if (isset($request->post->address) && !empty($request->post->address)) {
			$editFields['address'] = "?";
			$editValues[] = $request->post->address;
		}		
        
		if (isset($request->post->idCardNo) && !empty($request->post->idCardNo)) {
			$editFields['idCardNo'] = "?";
			$editValues[] = $request->post->idCardNo;
		}	

		if (isset($request->post->userID) && !empty($request->post->userID)) {
			$editFields['userID'] = "?";
			$editValues[] = $request->post->userID;
		}	
        
		if (isset($request->post->companyEnglishName) && !empty($request->post->companyEnglishName)) {
			$editFields['companyEnglishName'] = "?";
			$editValues[] = $request->post->companyEnglishName;
		}	

		if (isset($request->post->companyChineseName) && !empty($request->post->companyChineseName)) {
			$editFields['companyChineseName'] = "?";
			$editValues[] = $request->post->companyChineseName;
		}	        
        
		if (isset($request->post->companyAddress) && !empty($request->post->companyAddress)) {
			$editFields['companyAddress'] = "?";
			$editValues[] = $request->post->companyAddress;
		}	
        
		if (isset($request->post->CINo) && !empty($request->post->CINo)) {
			$editFields['CINo'] = "?";
			$editValues[] = $request->post->CINo;
		}	

		if (isset($request->post->BRNo) && !empty($request->post->BRNo)) {
			$editFields['BRNo'] = "?";
			$editValues[] = $request->post->BRNo;
		}	

        $idCardDocID = $clientObj->idCardDocID;
        $CIDocID = $clientObj->CIDocID;
        $BRDocID = $clientObj->BRDocID;

        if (isset($request->files->idCardDoc) && !empty($request->files->idCardDoc)) {
            $idCardDocID = documentHelper::upload($request->files->idCardDoc, "HKID");
            if($idCardDocID>0) {
                $editFields['idCardDocID'] = "?";
                $editValues[] = $idCardDocID;  
            }
        }

        if ($request->post->clientTypeID==2) {
            if (isset($request->files->CIDoc) && !empty($request->files->CIDoc)) {
                $CIDocID = documentHelper::upload($request->files->CIDoc, "CI");
                if($CIDocID>0) {
                    $editFields['CIDocID'] = "?";
                    $editValues[] = $CIDocID;
                }
            }
            
            if (isset($request->files->BRDoc) && !empty($request->files->BRDoc)) {
                $BRDocID = documentHelper::upload($request->files->BRDoc, "BR");
                if($BRDocID>0) {
                    $editFields['BRDocID'] = "?";
                    $editValues[] = $BRDocID;
                }                
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
		
		$sql = Sql::update('client')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
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
        
        if (!isset($request->post->clientID) || empty($request->post->clientID))
		    return new Data(['success'=>false, 'message'=>L('error.clientEmptyID')]);	
        
        $clientObj = self::find($request->post->clientID);

        if (is_null($clientObj))
            return new Data(['success'=>false, 'message'=>L('error.clientNotFound')]);	
	
        if(documentHelper::delete($docObj->id)) {

            $sql = Sql::update('client')->setFieldValue([$request->post->docType => '0'])->where(['id', '=', $request->post->clientID]);
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
                $htmlContent .= "<th>".L('client.type')."</th>";
				$htmlContent .= "<th>".L('client.title')."</th>";
				$htmlContent .= "<th>".L('client.contactPerson')."</th>";
				$htmlContent .= "<th>".L('client.position')."</th>";
                $htmlContent .= "<th>".L('client.phone')."</th>";
                $htmlContent .= "<th>".L('client.email')."</th>";
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
                $htmlContent .= "<th>".L('client.type')."</th>";
                $htmlContent .= "<th>".L('client.title')."</th>";
                $htmlContent .= "<th>".L('client.contactPerson')."</th>";
                $htmlContent .= "<th>".L('client.position')."</th>";
                $htmlContent .= "<th>".L('client.phone')."</th>";
                $htmlContent .= "<th>".L('client.email')."</th>";
                $htmlContent .= "<th>".L('Status')."</th>";                              
                $htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }	

	public static function genTableContentData() {
        $sql = Sql::select(['client', 'client'])->leftJoin(['status', 'status'], "client.status = status.id");
        $sql->setFieldValue('
           client.id id, 
           client.clientTypeID clientTypeID, 
           client.title title, 
           client.contactPerson contactPerson, 
           client.position position, 
           client.phone phone, 
           client.email email, 
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
			$htmlContent .= "<td>".clientType::find($listObj['clientTypeID'])->name."</td>";
			$htmlContent .= "<td>".$listObj['title']."</td>";
			$htmlContent .= "<td>".$listObj['contactPerson']."</td>";
            $htmlContent .= "<td>".$listObj['position']."</td>";
            $htmlContent .= "<td>".$listObj['phone']."</td>";
            $htmlContent .= "<td>".$listObj['email']."</td>";            
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

    public static function detail($request) {

        $clientObj = self::find($request->get->id);

		if(is_null($clientObj))
			return new Data(['success'=>false, 'message'=>L('error.clientNotFound')]);

        $content = "";
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
            $content .= formLayout::rowDisplayLineNew(L('client.idCardNo'),$clientObj->idCardNo, 6);    
            if($clientObj->idCardDocID>0) {
                $content .= formLayout::rowDisplayClearLineNew('<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs" data-id="'.$clientObj->idCardDocID.'"><i class="fas fa-download"></i></button></div>', 6);           
            }
        $content .= "</div>";        

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.address'),$clientObj->address, 12);            
        $content .= "</div>";            

        $content .= formLayout::rowSeparatorLineNew(12);        

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.companyEnglishName'),$clientObj->companyEnglishName, 6);
            $content .= formLayout::rowDisplayLineNew(L('client.companyChineseName'),$clientObj->companyChineseName, 6);
        $content .= "</div>";
 
        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.companyAddress'),$clientObj->companyAddress, 12);            
        $content .= "</div>";  
        
        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.CINo'),$clientObj->CINo, 6);
            if($clientObj->CIDocID>0) {
                $content .= formLayout::rowDisplayClearLineNew('<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs" data-id="'.$clientObj->CIDocID.'"><i class="fas fa-download"></i></button></div>', 6);           
            }       
        $content .= "</div>";  

        $content .= "<div class='row'>";
            $content .= formLayout::rowDisplayLineNew(L('client.BRNo'),$clientObj->BRNo, 6);
            if($clientObj->BRDocID>0) {
                $content .= formLayout::rowDisplayClearLineNew('<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-xs" data-id="'.$clientObj->BRDocID.'"><i class="fas fa-download"></i></button></div>', 6);           
            }  
        $content .= "</div>";         
/*        
        $content .= formLayout::rowSeparatorLineNew(12);        

        $content .= "<div class='row'>";
            $content .= "<div class='col-md-12 col-lg-12 text-end'>";
                $content .= "<button type='button' class='btn btn-md btn-success' id='next_pills-application'>".L('Next')."</button>";
            $content .= "</div>";                 
        $content .= "</div>"; 
*/
        return new Data(['success'=>true, 'message'=>$content]);


    }
}