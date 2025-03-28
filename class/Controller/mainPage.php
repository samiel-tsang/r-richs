<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Pages\Page;
use Database\Sql, Database\Listable;
use Routing\Route;
use Utility\WebSystem, Utility\QRCode;

class mainPage implements Listable {
	public function login($request) { return new Page('login'); }
	public function registration($request) { return new Page('user/registration'); }
	public function forgetpassword($request) { return new Page('user/forgetpassword'); }
	public function changepassword($request) { return new Page('user/changepassword'); }
	
	public function __call($name, $arguments) { 
		$request = $arguments[0];

		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		$page = str_replace('_', '/', $name);
		return new Page($page); 
	}

	public function extraProcess($listObj) {
		return $listObj;
	}

	public function main($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));

		$userObj = unserialize($_SESSION['user']);		
		
		return new Page('main', ['teamObj'=>$userObj]);
	}

	public function genQRCode($request) {
		if (!isset($request->get->code) || empty($request->get->code)) {
			return new Message('alert', 'Code is empty');
		}
		$enctype = $request->get->enctype ?? 'base64';

		$code = "";
		if ($enctype == 'base64') {
			$code = base64_decode($request->get->code);
		} else if ($enctype == 'text' || $enctype == 'string') {
			$code = $request->get->code;
		} else {
			return new Message('alert', 'No Encode Type');
		}

		$qrCode = new QRCode($code);

		header('Content-Type: image/png');
		header('Content-Length: '.strlen($qrCode));

		echo $qrCode;

		return null;
	}

	public static function getCalendarDateByMonth($request) {

		$calendar = new \donatj\SimpleCalendar($request->get->month);
		$calendar->setWeekDayNames([ L('Sun'), L('Mon'), L('Tue'), L('Wed'), L('Thu'), L('Fri'), L('Sat') ]);
		$calendar->setStartOfWeek('Sunday');   
		
		// tntpc
		$sqlAll = Sql::select('rntpc')->where(['status', '=', 1]);
		$sqlAll->order('rntpc.meetingDate', 'asc');
		
		$stmAll = $sqlAll->prepare();
		$stmAll->execute();		
		
		foreach($stmAll as $obj) {			
			$content = "<div class='d-grid gap-2'><button type='button' class='btn btn-primary btn-sm btnEdit' data-id='".$obj['id']."'>RNTPC Meeting# ".$obj['meetingNo']."<br>".$obj['meetingDate']."</button></div>";
			$calendar->addDailyHtml($content, date('Y-m-d', strtotime($obj['meetingDate'])));	
		}	

		// dbm
		$sqlAll = Sql::select('dbm')->where(['status', '=', 1]);
		$sqlAll->order('dbm.scheduleDate', 'asc');
		
		$stmAll = $sqlAll->prepare();
		$stmAll->execute();		
		
		foreach($stmAll as $obj) {			
			$content = "<div class='d-grid gap-2'><button type='button' class='btn btn-warning btn-sm btnEdit' data-id='".$obj['id']."'>DBM Schedule Date<br>".$obj['scheduleDate']."</button></div>";
			$calendar->addDailyHtml($content, date('Y-m-d', strtotime($obj['scheduleDate'])));	
		}	
		
		// task
		$sqlAll = Sql::select('task')->where(['status', '=', 1]);
		$sqlAll->order('task.deadline', 'asc');
		
		$stmAll = $sqlAll->prepare();
		$stmAll->execute();		
		
		foreach($stmAll as $obj) {			
			$content = "<div class='d-grid gap-2'><button type='button' class='btn btn-success btn-sm btnEdit' data-id='".$obj['id']."'>Task: ".$obj['description']."<br>".$obj['deadline']."</button></div>";
			$calendar->addDailyHtml($content, date('Y-m-d', strtotime($obj['deadline'])));	
		}	
		
		// tpb
		$sqlAll = Sql::select('tpb')->where(['tpbStatusID', '<', 6]);
		$sqlAll->order('tpb.approvalValidUntil', 'asc');
		
		$stmAll = $sqlAll->prepare();
		$stmAll->execute();		
		
		foreach($stmAll as $obj) {			
			$content = "<div class='d-grid gap-2'><button type='button' class='btn btn-info btn-sm btnEdit' data-id='".$obj['id']."'>TPB#".$obj['TPBNo']."<br>".$obj['approvalValidUntil']."</button></div>";
			$calendar->addDailyHtml($content, date('Y-m-d', strtotime($obj['approvalValidUntil'])));	
		}	

		return new Data(['success'=>true, 'message'=>$calendar->render()]);

	}

	public static function genTableHeader() {
        $htmlContent = "";

        $htmlContent .= "<thead>";
            $htmlContent .= "<tr>";
                $htmlContent .= "<th>".L('task.officer')."</th>";
                $htmlContent .= "<th>".L('task.description')."</th>";
                $htmlContent .= "<th>".L('task.deadline')."</th>";                   
                $htmlContent .= "<th>".L('MarkAsDone')."</th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</thead>";

        return $htmlContent;
    }

    public static function genTableFooter() {
        $htmlContent = "";

        $htmlContent .= "<tfoot>";
            $htmlContent .= "<tr>";
                $htmlContent .= "<th>".L('task.officer')."</th>";
                $htmlContent .= "<th>".L('task.description')."</th>";
                $htmlContent .= "<th>".L('task.deadline')."</th>";                     
                $htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }	

	public static function genTableContentData($mode='1') {

		$currentUserObj = unserialize($_SESSION['user']);

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

		$sql->where(['task.status', '!=', 2]);

		if($currentUserObj->roleID!=1) {
			$sql->where(['task.userID', '=', '"'.$currentUserObj->id.'"']);
		}

		if($mode==1){
			$sql->where(['task.deadline', '>=', 'NOW()']);
		} else {
			$sql->where(['task.deadline', '<', 'NOW()']);
		}

		$sql->order('task.deadline','asc');

        $stm = $sql->prepare();
        $stm->execute();
        return $stm;
    }

	public static function genTableBodyRow($listObj) {
        $htmlContent = "";
        $htmlContent .= "<tr>";
            $htmlContent .= "<td>".$listObj['officer']."</td>";
            $htmlContent .= "<td>".$listObj['description']."</td>";
            $htmlContent .= "<td>".$listObj['deadline']."</td>";
            $htmlContent .= "<td>";
                $htmlContent .= "<input type='checkbox' class='form-check-input taskDone' data-id='".$listObj['id']."' value='1'>";
            $htmlContent .= "</td>";
        $htmlContent .= "</tr>";

        return $htmlContent;
    }

}