<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem, Utility\Excel, Utility\Email; 
use Controller\formLayout;

class rntpc implements Listable {
	private $stmStatus = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("rntpc")->where(['id', '=', $id]);
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
		return new FormPage('rntpc/list', $obj);
	}

    public function delete($request) {	
		if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);	
		
		$currentUserObj = unserialize($_SESSION['user']);	

		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.rntpcEmptyID')]);
		
		$rntpcObj = self::find($request->get->id);		
	
		if(is_null($rntpcObj))
			return new Data(['success'=>false, 'message'=>L('error.rntpcNotFound'), 'field'=>'notice']);				
			
		$sql = Sql::delete('rntpc')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "RNTPC";
			$logData['referenceID'] = $request->get->id;
			$logData['action'] = "Delete";
			$logData['description'] = "Delete RNTPC [".$rntpcObj->meetingDate."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $request->get->id;
			$logData['changes'] = [];
			systemLog::add($logData);

			return new Data(['success'=>true, 'message'=>L('info.rntpcDeleted')]);	
		} else {
			return new Data(['success'=>false, 'message'=>L('error.rntpcDeleteFailed')]);	
		}					
	}    

    public function rntpcForm($request) {

		if (!user::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);

		$formName = "form-addRntpc";

		if(!is_null($obj)) {
			$formName = "form-editRntpc";
		}				

		$content = "<form id='".$formName."' class='' autocomplete='off'>";
		$content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.rntpcAddHelperMessage')."</p></div>";

		$content .= "<div class='row'>";     
			$content .= formLayout::rowInputNew(L('rntpc.meetingNo'),'meetingNo', 'meetingNo', 'text',  6, [], ['required'], is_null($obj)?'':$obj['meetingNo']);
			$content .= formLayout::rowInputNew(L('rntpc.meetingDate'),'meetingDate', 'meetingDate', 'text',  6, ['customDateTime'], ['required'], is_null($obj)?'':$obj['meetingDate']);

			if(!is_null($obj)) {

				$content .= formLayout::rowInputNew(L('rntpc.agendaTC'),'agendaTC', 'agendaTC', 'text',  12, [''], [''], is_null($obj)?'':$obj['agendaTC']);
				$content .= formLayout::rowInputNew(L('rntpc.agendaEN'),'agendaEN', 'agendaEN', 'text',  12, [''], [''], is_null($obj)?'':$obj['agendaEN']);
				$content .= formLayout::rowInputNew(L('rntpc.minutesTC'),'minutesTC', 'minutesTC', 'text',  12, [''], [''], is_null($obj)?'':$obj['minutesTC']);
				$content .= formLayout::rowInputNew(L('rntpc.minutesEN'),'minutesEN', 'minutesEN', 'text',  12, [''], [''], is_null($obj)?'':$obj['minutesEN']);
				$content .= formLayout::rowInputNew(L('rntpc.audioRecordingTC'),'audioRecordingTC', 'audioRecordingTC', 'text',  12, [''], [''], is_null($obj)?'':$obj['audioRecordingTC']);
				$content .= formLayout::rowInputNew(L('rntpc.audioRecordingEN'),'audioRecordingEN', 'audioRecordingEN', 'text',  12, [''], [''], is_null($obj)?'':$obj['audioRecordingEN']);
							
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
		if (!isset($request->post->meetingNo) || empty($request->post->meetingNo)) 
			return new Data(['success'=>false, 'message'=>L('error.rntpcEmptyMeetingNo'), 'field'=>'meetingNo']);

		if (!isset($request->post->meetingDate) || empty($request->post->meetingDate)) 
			return new Data(['success'=>false, 'message'=>L('error.rntpcEmptyMeetingDate'), 'field'=>'meetingDate']);

		$agendaTC = "https://www.tpb.gov.hk/tc/meetings/RNTPC/Agenda/".$request->post->meetingNo."_rnt_agenda.html";
		$agendaEN = "https://www.tpb.gov.hk/en/meetings/RNTPC/Agenda/".$request->post->meetingNo."_rnt_agenda.html";
		$minutesTC = "https://www.tpb.gov.hk/tc/meetings/RNTPC/Minutes/m".$request->post->meetingNo."rnt_e.pdf";
		$minutesEN = "https://www.tpb.gov.hk/en/meetings/RNTPC/Minutes/m".$request->post->meetingNo."rnt_e.pdf";
		$audioRecordingTC = "https://www.tpb.gov.hk/tc/meetings/RNTPC/Audio_Clips/".$request->post->meetingNo."_rntpc_audio.html";
		$audioRecordingEN = "https://www.tpb.gov.hk/en/meetings/RNTPC/Audio_Clips/".$request->post->meetingNo."_rntpc_audio.html";

        // insert database
		$sql = Sql::insert('rntpc')->setFieldValue([
			'meetingNo' => "?",
            'meetingDate' => "?",
			'agendaTC' => "?",
			'agendaEN' => "?",
			'minutesTC' => "?",
			'minutesEN' => "?",
			'audioRecordingTC' => "?",
			'audioRecordingEN' => "?",
            'createBy'=>$currentUserObj->id, 
            'modifyBy'=>$currentUserObj->id
        ]);

		$addValues = [
			strip_tags($request->post->meetingNo),      
			strip_tags($request->post->meetingDate),  
			strip_tags($agendaTC),  
			strip_tags($agendaEN),  
			strip_tags($minutesTC),  
			strip_tags($minutesEN),  
			strip_tags($audioRecordingTC),  
			strip_tags($audioRecordingEN),               
		];

		if ($sql->prepare()->execute($addValues)) {
			
            $id = db()->lastInsertId();

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "RNTPC";
			$logData['referenceID'] = $id;
			$logData['action'] = "Insert";
			$logData['description'] = "Create New RNTPC [".$request->post->meetingDate."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $addValues;
			$logData['changes'] = 
				[
					[
						"key"=>"meetingNo", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->meetingNo)
					],[
						"key"=>"meetingDate", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->meetingDate)
					],[
						"key"=>"agendaTC", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($agendaTC)
					],[
						"key"=>"agendaEN", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($agendaEN)
					],[
						"key"=>"minutesTC", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($minutesTC)
					],[
						"key"=>"minutesEN", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($minutesEN)
					],[
						"key"=>"audioRecordingTC", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($audioRecordingTC)
					],[
						"key"=>"audioRecordingEN", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($audioRecordingEN)
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
			return new Data(['success'=>false, 'message'=>L('error.rntpcEmptyID'), 'field'=>'notice']);

		$rntpcObj = self::find($request->get->id);
		if(is_null($rntpcObj))
			return new Data(['success'=>false, 'message'=>L('error.rntpcNotFound'), 'field'=>'notice']);

        // form check
        if (!isset($request->post->meetingNo) || empty($request->post->meetingNo)) 
            return new Data(['success'=>false, 'message'=>L('error.rntpcEmptyMeetingNo'), 'field'=>'meetingNo']);

		if (!isset($request->post->meetingDate) || empty($request->post->meetingDate)) 
            return new Data(['success'=>false, 'message'=>L('error.rntpcEmptyMeetingDate'), 'field'=>'meetingDate']);			

		$editFields = [];
		$editValues = [];
		$logContent = [];

		if (isset($request->post->meetingNo) && !empty($request->post->meetingNo)) {

			$editFields['meetingNo'] = "?";
			$editValues[] = $request->post->meetingNo;

			if($request->post->meetingNo!=$rntpcObj->meetingNo) {
				$logContent[] = [
					"key"=>"meetingNo", 
					"valueFrom"=>$rntpcObj->meetingNo, 
					"valueTo"=>strip_tags($request->post->meetingNo)					
				];	
			}			
		}	

		if (isset($request->post->meetingDate) && !empty($request->post->meetingDate)) {

			$editFields['meetingDate'] = "?";
			$editValues[] = $request->post->meetingDate;

			if($request->post->meetingDate.":00"!=$rntpcObj->meetingDate) {
				$logContent[] = [
					"key"=>"meetingDate", 
					"valueFrom"=>$rntpcObj->meetingDate, 
					"valueTo"=>strip_tags($request->post->meetingDate)					
				];	
			}			
		}	

		if (isset($request->post->agendaTC) && !empty($request->post->agendaTC)) {

			$editFields['agendaTC'] = "?";
			$editValues[] = $request->post->agendaTC;

			if($request->post->agendaTC!=$rntpcObj->agendaTC) {
				$logContent[] = [
					"key"=>"agendaTC", 
					"valueFrom"=>$rntpcObj->agendaTC, 
					"valueTo"=>strip_tags($request->post->agendaTC)					
				];	
			}			
		}		

		if (isset($request->post->agendaEN) && !empty($request->post->agendaEN)) {

			$editFields['agendaEN'] = "?";
			$editValues[] = $request->post->agendaEN;

			if($request->post->agendaEN!=$rntpcObj->agendaEN) {
				$logContent[] = [
					"key"=>"agendaEN", 
					"valueFrom"=>$rntpcObj->agendaEN, 
					"valueTo"=>strip_tags($request->post->agendaEN)					
				];	
			}			
		}		
		

		if (isset($request->post->agendaEN) && !empty($request->post->agendaEN)) {

			$editFields['agendaEN'] = "?";
			$editValues[] = $request->post->agendaEN;

			if($request->post->agendaEN!=$rntpcObj->agendaEN) {
				$logContent[] = [
					"key"=>"agendaEN", 
					"valueFrom"=>$rntpcObj->agendaEN, 
					"valueTo"=>strip_tags($request->post->agendaEN)					
				];	
			}			
		}	


		if (isset($request->post->minutesTC) && !empty($request->post->minutesTC)) {

			$editFields['minutesTC'] = "?";
			$editValues[] = $request->post->minutesTC;

			if($request->post->minutesTC!=$rntpcObj->minutesTC) {
				$logContent[] = [
					"key"=>"minutesTC", 
					"valueFrom"=>$rntpcObj->minutesTC, 
					"valueTo"=>strip_tags($request->post->minutesTC)					
				];	
			}			
		}	


		if (isset($request->post->minutesEN) && !empty($request->post->minutesEN)) {

			$editFields['minutesEN'] = "?";
			$editValues[] = $request->post->minutesEN;

			if($request->post->minutesEN!=$rntpcObj->minutesEN) {
				$logContent[] = [
					"key"=>"minutesEN", 
					"valueFrom"=>$rntpcObj->minutesEN, 
					"valueTo"=>strip_tags($request->post->minutesEN)					
				];	
			}			
		}	


		if (isset($request->post->audioRecordingTC) && !empty($request->post->audioRecordingTC)) {

			$editFields['audioRecordingTC'] = "?";
			$editValues[] = $request->post->audioRecordingTC;

			if($request->post->audioRecordingTC!=$rntpcObj->audioRecordingTC) {
				$logContent[] = [
					"key"=>"audioRecordingTC", 
					"valueFrom"=>$rntpcObj->audioRecordingTC, 
					"valueTo"=>strip_tags($request->post->audioRecordingTC)					
				];	
			}			
		}			


		if (isset($request->post->audioRecordingEN) && !empty($request->post->audioRecordingEN)) {

			$editFields['audioRecordingEN'] = "?";
			$editValues[] = $request->post->audioRecordingEN;

			if($request->post->audioRecordingEN!=$rntpcObj->audioRecordingEN) {
				$logContent[] = [
					"key"=>"audioRecordingEN", 
					"valueFrom"=>$rntpcObj->audioRecordingEN, 
					"valueTo"=>strip_tags($request->post->audioRecordingEN)					
				];	
			}			
		}		

		if (isset($request->post->status) && !empty($request->post->status)) {

			$editFields['status'] = "?";
			$editValues[] = $request->post->status;

			if($request->post->status!=$rntpcObj->status) {
				$logContent[] = [
					"key"=>"status", 
					"valueFrom"=>$rntpcObj->status, 
					"valueTo"=>strip_tags($request->post->status)					
				];	
			}			
		}					
        
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}

        if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('rntpc')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);

		if ($sql->prepare()->execute($editValues)) {
			if (count($logContent)) {
				$logData = [];
				$logData['userID']= $currentUserObj->id;
				$logData['module'] = "RNTPC";
				$logData['referenceID'] = $request->get->id;
				$logData['action'] = "Update";
				$logData['description'] = "Edit RNTPC [".$rntpcObj->meetingDate."]";
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
				$htmlContent .= "<th>".L('rntpc.meetingNo')."</th>";
                $htmlContent .= "<th>".L('rntpc.meetingDate')."</th>";
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
				$htmlContent .= "<th>".L('rntpc.meetingNo')."</th>";
				$htmlContent .= "<th>".L('rntpc.meetingDate')."</th>";
				$htmlContent .= "<th>".L('Status')."</th>";                             
                $htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }

	public static function genTableContentData() {
		$sql = Sql::select(['rntpc', 'rntpc'])->leftJoin(['status', 'status'], "rntpc.status = status.id");
		$sql->setFieldValue('
		   rntpc.id id, 
		   rntpc.meetingNo meetingNo, 
		   rntpc.meetingDate meetingDate, 
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
			$htmlContent .= "<td>".$listObj['meetingNo']."</td>";
            $htmlContent .= "<td>".$listObj['meetingDate']."</td>";
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

	public static function getMeetingDateByMonth($request) {
		
		//$firstDateOfMonth = $request->get->month."-01";
		//$lastDateOfMonth = date('Y-m-t', strtotime($request->get->month.'-01'));

		//$userObj = unserialize($_SESSION['user']);

		$calendar = new \donatj\SimpleCalendar($request->get->month);
		$calendar->setWeekDayNames([ L('Sun'), L('Mon'), L('Tue'), L('Wed'), L('Thu'), L('Fri'), L('Sat') ]);
		$calendar->setStartOfWeek('Sunday');   
		
		$sqlAll = Sql::select('rntpc')->where(['status', '=', 1]);
		$sqlAll->order('rntpc.meetingDate', 'asc');
		
		$stmAll = $sqlAll->prepare();
		$stmAll->execute();
		
		
		foreach($stmAll as $obj) {			
			//$content = "<div class='d-grid gap-2'><button type='button' class='btn btn-info btn-md btnEdit' data-id='".$obj['id']."'>#".$obj['meetingNo']."<br>".$obj['meetingDate']."</button></div>";
			$content = "<div class='d-grid gap-2'><button type='button' class='btn btn-info btn-md btnEdit' data-id='".$obj['id']."'>#".$obj['meetingNo']."</button></div>";
			$calendar->addDailyHtml($content, date('Y-m-d', strtotime($obj['meetingDate'])));	
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

			$content .= formLayout::rowDisplayLineNew(L('rntpc.meetingNo'), $obj['meetingNo'], 6);
			$content .= formLayout::rowDisplayLineNew(L('rntpc.meetingDate'), $obj['meetingDate'], 6);

			$content .= formLayout::rowDisplayLineNew(L('rntpc.agendaTC'), $obj['agendaTC'], 12);
			$content .= formLayout::rowDisplayLineNew(L('rntpc.agendaEN'), $obj['agendaEN'], 12);
			$content .= formLayout::rowDisplayLineNew(L('rntpc.minutesTC'), $obj['minutesTC'], 12);
			$content .= formLayout::rowDisplayLineNew(L('rntpc.minutesEN'), $obj['minutesEN'], 12);
			$content .= formLayout::rowDisplayLineNew(L('rntpc.audioRecordingTC'), $obj['audioRecordingTC'], 12);
			$content .= formLayout::rowDisplayLineNew(L('rntpc.audioRecordingEN'), $obj['audioRecordingEN'], 12);
			$content .= formLayout::rowDisplayLineNew(L('Status'), L(status::find($obj['status'])->name), 6);

		$content .= "</div>";

		return new Data(['success'=>true, 'message'=>$content]);
		
	}	
}