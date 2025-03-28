<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem, Utility\Excel, Utility\Email; 
use Controller\promoCode, Controller\period, Controller\defaultAvatar, Controller\template, Controller\payment;

class team implements Listable {
	private $stmStatus = null;
	private $stmCategory = null;
	private $stmPeriod = null;
	//public $stmTeamDonatorAmt = null;
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("team")->where(['id', '=', $id]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

	public static function findByUserID($userID,  $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("team")->where(['userID', '=', '?']);		
		$stm = $sql->prepare();
		$stm->execute([$userID]);
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

	public static function findLeader($id,  $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("teamMember")->where(['teamID', '=', $id])->where(['roleID', '=', 1]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

	/* Page Function */
	public function list($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false)); 
		
		$sql = Sql::select(['team', 't'])->leftJoin(['teamDonator', 'td'], "td.teamID = t.id")->setFieldValue('t.*, COALESCE(sum(td.donatorAmount), 0) teamDonateAmt');
	
		if (isset($request->get->q)) {
			$hash = $request->get->q;
			if (!empty($_SESSION['search'][$hash]['teamName']))
				$sql->where(['t.teamName', 'LIKE', dbes("%".$_SESSION['search'][$hash]['teamName']."%")]);
			if (!empty($_SESSION['search'][$hash]['categoryID']))
				$sql->where(['t.categoryID', '=', "'".$_SESSION['search'][$hash]['categoryID']."'"]);;				
			if (!empty($_SESSION['search'][$hash]['periodID']))
				$sql->where(['t.periodID', '=', "'".$_SESSION['search'][$hash]['periodID']."'"]);				
			if (!empty($_SESSION['search'][$hash]['status']))
				$sql->where(['t.status', '=', "'".$_SESSION['search'][$hash]['status']."'"]);
			if (!empty($_SESSION['search'][$hash]['sql_order_field']))
				$sql->order($_SESSION['search'][$hash]['sql_order_field'], (!empty($_SESSION['search'][$hash]['sql_order_seq']))?$_SESSION['search'][$hash]['sql_order_seq']:'ASC');
		}
		$sql->whereOp('(td.status = 1 OR td.status is null) GROUP BY t.id');

		$listPage = new ListPage('team/list', $sql);
		$listPage->setLister($this);
		return $listPage;
	}

	public function extraProcess($listObj) {
		if (is_null($this->stmCategory))
			$this->stmCategory = Sql::select('category')->where(['id', '=', "?"])->prepare();
			
		$this->stmCategory->execute([$listObj->categoryID]);
		$objCategory = $this->stmCategory->fetch();
		$listObj->teamCategory = $objCategory['name'];

		if (is_null($this->stmPeriod))
			$this->stmPeriod = Sql::select('period')->where(['id', '=', "?"])->prepare();
			
		$this->stmPeriod->execute([$listObj->periodID]);
		$objPeriod = $this->stmPeriod->fetch();
		$listObj->teamPeriod = $objPeriod['name'];

		if (is_null($this->stmStatus))
			$this->stmStatus = Sql::select('status')->where(['id', '=', "?"])->prepare();
			
		$this->stmStatus->execute([$listObj->status]);
		$objStatus = $this->stmStatus->fetch();
		$listObj->statusName = $objStatus['name'];
		
		return $listObj;
	}

	public function search($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false)); 
		
		$hash = '';
		if ($request->method == 'POST') {
			unset($_SESSION['search']);
			$hash = sha1("search_".time());
			$_SESSION['search'][$hash]['teamName'] = $request->post->teamName;
			$_SESSION['search'][$hash]['categoryID'] = $request->post->categoryID;
			$_SESSION['search'][$hash]['periodID'] = $request->post->periodID;
			$_SESSION['search'][$hash]['status'] = $request->post->status;
		}
		if ($request->method == 'GET') {
			$hash = (isset($request->get->q))?$request->get->q:sha1("order_".time());
			$_SESSION['search'][$hash]['sql_order_field'] = $request->get->field;
			$_SESSION['search'][$hash]['sql_order_seq'] = $request->get->order;
		}

		$param = ['pg'=>1];
		if (!empty($hash)) $param['q'] = $hash;
		return new Action('redirect', WebSystem::path(Route::getRouteByName('page.teamList')->path($param), false, false));
	}
	
	/* Page Function */
	public function form($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);
		
		return new FormPage('team/form', $obj);
	}
	
	/* Page Function */
	public function info($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		
		$obj = $this->extraProcess(self::find($request->get->id));
		return new Page('team/info', ['obj'=>$obj]);
	}

	/* Page Function */
	public function searchform($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		
		$obj = (isset($request->get->q))?$_SESSION['search'][$request->get->q]:null;
		return new FormPage('team/search', $obj);
	}
	
	
	public function add($request) {		
		$totalFee = 0;
		$submissionFee = 0;
		$donateFee = 0;

		// team basic info check //
		if (!isset($request->post->categoryID) || empty($request->post->categoryID)) {
			return new Message('alert', L('error.teamEmptyCategory'));
		} else {
			$category_id = $request->post->categoryID;
		}

		/* removed since 2024
		if (!isset($request->post->periodID) || empty($request->post->periodID)) {
			return new Message('alert', L('error.teamEmptyPeriod'));
		} else {

			$periodObj = period::find($request->post->periodID);

			//if(self::getTeamCountByPeriod($request->post->periodID)>=cfg('event')['maxTeamCount']){
			if(self::getParticipantCountByPeriod($request->post->periodID) >= $periodObj->maxParticipantCount){
				if (!isset($request->post->isBackend) || empty($request->post->isBackend)) {
					return new Message('alert', L('info.teamPeriodisFull'));
				}
			}
		}
		*/
		
		if (!isset($request->post->teamName) || empty($request->post->teamName)) {
			return new Message('alert', L('error.teamEmptyName'));			
		} else {
			if(!is_null(self::find_by_name($request->post->teamName))){
				return new Message('alert', L('error.teamDuplicateName'));		
			}
		}

		if (!isset($request->post->status) || empty($request->post->status)) {
			$status = 2;
		} else {
			$status = $request->post->status;
		}

		// team leader check
		if (!isset($request->post->leaderNameChi) || empty($request->post->leaderNameChi)) 
			return new Message('alert', L('error.teamEmptyLeaderChineseName'));		

		if (!isset($request->post->leaderNameEng) || empty($request->post->leaderNameEng)) 
			return new Message('alert', L('error.teamEmptyLeaderEnglishName'));			
			
		if (!isset($request->post->leaderAge) || empty($request->post->leaderAge)) {
			return new Message('alert', L('error.teamEmptyLeaderAge'));		
		} else {
			if($category_id<=3){
				if($request->post->leaderAge<15) {
					return new Message('alert', L('error.teamLeaderInvalidAge'));		
				} else {
					$submissionFee += 50;
					$donateFee += 300;
				}
			} else {
				//if(($request->post->leaderAge>=15 && $request->post->leaderAge<18) || $request->post->leaderAge<3) {
				if($request->post->leaderAge<15) {
					return new Message('alert', L('error.teamLeaderInvalidAge'));		
				} else {
					if($request->post->leaderAge>=15) {
						$submissionFee += 50;
						$donateFee += 300;
					} if(($request->post->leaderAge>=3 && $request->post->leaderAge<=14)) {
						$submissionFee += 50;
						$donateFee += 130;
					}
				}
			}
		}

		if (!isset($request->post->leaderGender) || empty($request->post->leaderGender)) 
			return new Message('alert', L('error.teamEmptyLeaderGender'));	

		if (!isset($request->post->leaderMobile) || empty($request->post->leaderMobile)) 
			return new Message('alert', L('error.teamEmptyLeaderMobile'));	
		
		if (filter_var($request->post->leaderEmail, FILTER_VALIDATE_EMAIL) === FALSE)
			return new Message('alert', L('error.teamEmptyLeaderEmail'));	

		if (!isset($request->post->leaderAddr1) || empty($request->post->leaderAddr1)) 
			return new Message('alert', L('error.teamEmptyLeaderAddress'));	

		if (!isset($request->post->leaderEmContactName) || empty($request->post->leaderEmContactName)) 
			return new Message('alert', L('error.teamEmptyLeaderEmContactName'));	

		if (!isset($request->post->leaderEmContactMobile) || empty($request->post->leaderEmContactMobile)) 
			return new Message('alert', L('error.teamEmptyLeaderEmContactMobile'));	

		if (!isset($request->post->leaderShirtSize) || empty($request->post->leaderShirtSize)) 
			return new Message('alert', L('error.teamEmptyLeaderShirtSize'));				

		// team member check
		foreach($request->post->memberNameEng as $idx=>$nameEng){
			if(trim($nameEng)!="" || trim($request->post->memberNameChi[$idx])!="" || trim($request->post->memberAge[$idx])!="" || trim($request->post->memberGender[$idx])!="" 
			|| trim($request->post->memberMobile[$idx])!="" || trim($request->post->memberEmail[$idx])!="" || trim($request->post->memberEmContactName[$idx])!="" || trim($request->post->memberEmContactMobile[$idx])!="") {
				if($request->post->memberNameChi[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberChineseName'));		
				}

				if($request->post->memberAge[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberAge'));		
				} else {					
					if($category_id<=3){
						if($request->post->memberAge[$idx]<15) {
							return new Message('alert', L('error.teamMemberInvalidAge'));		
						} else {
							$submissionFee += 50;
							$donateFee += 300;
						}
					} else {
						//if(($request->post->memberAge[$idx]>=15 && $request->post->memberAge[$idx]<18) || $request->post->memberAge[$idx]<3) {
						if($request->post->memberAge[$idx]<3) {
							return new Message('alert', L('error.teamMemberInvalidAge'));		
						} else {
							if($request->post->memberAge[$idx]>=15) {
								$submissionFee += 50;
								$donateFee += 300;
							} if(($request->post->memberAge[$idx]>=3 && $request->post->memberAge[$idx]<=14)) {
								$submissionFee += 50;
								$donateFee += 130;
							}
						}
					}
				}
				
				if($request->post->memberGender[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberGender'));		
				}
				
				if($request->post->memberMobile[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberMobile'));		
				}	
				
				if($request->post->memberEmail[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberEmail'));		
				}	
				
				if($request->post->memberEmContactName[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberEmContactName'));		
				}	
				
				if($request->post->memberEmContactMobile[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberEmContactMobile'));		
				}
				
				if($request->post->memberShirtSize[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberShirtSize'));		
				}					
			}
		}

		// promo code check
		if (isset($request->post->promoCode) && !empty($request->post->promoCode)) {
			if(is_null(promoCode::findCode($request->post->promoCode))){
				return new Message('alert', L('error.promoCodeInvalid'));		
			} else {
				$submissionFee = 0;
				$totalFee = $donateFee;
			}
		} else {
			$totalFee = $submissionFee + $donateFee;
		}

		// image check
		// removed since 2023
		/*
		if (!isset($request->post->avatarID) || empty($request->post->avatarID)) {
			return new Message('alert', L('error.teamEmptyAvatar'));	
		} else {
			if($request->post->avatarID==5){
				if (!isset($request->files->customizeImage) || empty($request->files->customizeImage)) {
					return new Message('alert', L('error.teamEmptyCustomizedAvatar'));	
				} else {

					$target_dir = "upload/";
					$target_file = $target_dir.time()."_".basename($request->files->customizeImage['name']);

					$copyImage = imagecreatefromstring(file_get_contents($request->files->customizeImage['tmp_name']));
					$imgWidth = imagesx($copyImage);
					$imgHeight = imagesy($copyImage);
					$imgWidthHeightRation = $imgWidth / $imgHeight;

					$scaleWidth = 170;
					$scaleHeight = 170;
					$scaleX = 0;
					$scaleY = 0;
					if ($imgWidthHeightRation < 1) {
						$scaleWidth = floor($imgWidthHeightRation * 170);
						$scaleX = (170 - $scaleWidth) / 2;
					} else if ($imgWidthHeightRation > 1) {
						$scaleHeight = floor(170 / $imgWidthHeightRation);
						$scaleY = (170 - $scaleHeight) / 2;
					}

					$imgResized = imagecreatetruecolor(170, 170);
					imagecopyresampled($imgResized, $copyImage, $scaleX, $scaleY, 0, 0, 
						$scaleWidth, $scaleHeight, $imgWidth, $imgHeight);

					if(imagejpeg($imgResized, $target_file)){
						$teamImage = $target_file;
					} else {
						return new Message('alert', L('error.teamCustomizedAvatarUploadFail'));	
					}					
				}
				
			} else {
				$teamImage = defaultAvatar::find($request->post->avatarID)->url;
			}
		}
		*/

		//echo $submissionFee;
		$teamImage = "";
		$sql = Sql::insert('team')->setFieldValue(['categoryID' => "?", 'periodID' => "?", 'teamImage' => "?", 'teamName' => "?", 
		'promoCode'=>"?", 'submissionFee'=>"?", 'donateFee'=>"?",  'totalFee'=>"?", 'status'=>"?"]);

		if ($sql->prepare()->execute([$request->post->categoryID, 1, $teamImage, $request->post->teamName,
		$request->post->promoCode??"",
		$submissionFee, $donateFee, $totalFee, $status
		])) {
			$teamID = db()->lastInsertId();

			// insert leader //
			$sql = Sql::insert('teamMember')->setFieldValue(['teamID' => "?", 'roleID' => "?", 'nameChi' => "?", 'nameEng' => "?", 
			'age'=>"?", 'gender'=>"?", 'mobile'=>"?",  'email'=>"?", 'addr1'=>"?",'addr2'=>"?",'emContactName'=>"?",'emContactMobile'=>"?",'shirtSize'=>"?",'status'=>"?"]);			
			
			if ($sql->prepare()->execute([$teamID, 1, $request->post->leaderNameChi, 
				$request->post->leaderNameEng, $request->post->leaderAge, $request->post->leaderGender,
				$request->post->leaderMobile, $request->post->leaderEmail, 
				$request->post->leaderAddr1, $request->post->leaderAddr2, 
				$request->post->leaderEmContactName, $request->post->leaderEmContactMobile, $request->post->leaderShirtSize, 1
			])) {
				// send welcome email. this should be processed after sucessful paypal payment 
				/*
				try {
					$tpl = template::findName('welcomeEmail');
					$var = ['team_name' => $request->post->teamName, 'member_name' => $request->post->leaderNameChi];
					$content = template::replaceVar($tpl, $var);
					$mail = new Email($tpl->subject);
					$mail->addAddress($request->post->leaderEmail, 'Email');
					$mail->setHTMLBody($content);
					$mail->send();
				} catch (Exception $e) {
					return new Data(['success'=>false, 'message'=>'email sent fail']);
				} 
				*/

				foreach($request->post->memberNameEng as $idx=>$nameEng){
					if(trim($nameEng)!="" || trim($request->post->memberNameChi[$idx])!="" || trim($request->post->memberAge[$idx])!="" || trim($request->post->memberGender[$idx])!="" 
			|| trim($request->post->memberMobile[$idx])!="" || trim($request->post->memberEmail[$idx])!="" || trim($request->post->memberEmContactName[$idx])!="" || trim($request->post->memberEmContactMobile[$idx])!="" || trim($request->post->memberShirtSize[$idx])!="") {
						$sql = Sql::insert('teamMember')->setFieldValue(['teamID' => "?", 'roleID' => "?", 'nameChi' => "?", 'nameEng' => "?", 
						'age'=>"?", 'gender'=>"?", 'mobile'=>"?",  'email'=>"?", 'addr1'=>"''",'addr2'=>"''",'emContactName'=>"?",'emContactMobile'=>"?",'shirtSize'=>"?",'status'=>"?"]);			
						
						if($sql->prepare()->execute([$teamID, 2, $request->post->memberNameChi[$idx], 
							$nameEng, $request->post->memberAge[$idx], $request->post->memberGender[$idx],
							$request->post->memberMobile[$idx], $request->post->memberEmail[$idx], 
							$request->post->memberEmContactName[$idx], $request->post->memberEmContactMobile[$idx], $request->post->memberShirtSize[$idx], 1
						])){
							// send welcome email. this should be processed after sucessful paypal payment 
							/* 
							try {
								$tpl = template::findName('welcomeEmail');
								$var = ['team_name' => $request->post->teamName, 'member_name' => $request->post->memberNameChi[$idx]];
								$content = template::replaceVar($tpl, $var);
								$mail = new Email($tpl->subject);
								$mail->addAddress($request->post->memberEmail[$idx], 'Email');
								$mail->setHTMLBody($content);
								$mail->send();
							} catch (Exception $e) {
								return new Data(['success'=>false, 'message'=>'email sent fail']);
							} 
							*/
						}



					}
				}
			}

			if (isset($request->post->isBackend) && !empty($request->post->isBackend)) {
				return new Action('redirect', WebSystem::path(Route::getRouteByName('page.teamInfo')->path(['id'=>$teamID]), false, false));
			} else {
				$remark = "Submission Fee $".$submissionFee.". Donate Fee $".$donateFee;
				$type = "register";
				//return payment::createOrder($teamID, $totalFee, $remark, $type);
				return new Action('redirect', payment::createOrder($teamID, $totalFee, $remark, $type));
				//return new Message('alert', 'added');
			}
		}
		
	}
	
	public function edit($request) {	

		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		$userObj = unserialize($_SESSION['user']);
		
		$totalFee = 0;
		$submissionFee = 0;
		$donateFee = 0;
		$editFields = [];
		$editValues = [];		

		if (!isset($request->get->id) || empty($request->get->id))
			return new Message('alert', L('error.teamEmptyID'));

		$teamObj = self::find($request->get->id);

		// team basic info check //
		if (!isset($request->post->categoryID) || empty($request->post->categoryID)) {
			return new Message('alert', L('error.teamEmptyCategory'));
		} else {
			$category_id = $request->post->categoryID;
			$editFields['categoryID'] = "?";
			$editValues[] = $request->post->categoryID;
		}

		// removed since 2024
		/*
		if (!isset($request->post->periodID) || empty($request->post->periodID)) {
			return new Message('alert', L('error.teamEmptyPeriod'));			
		} else {			
			$editFields['periodID'] = "?";
			$editValues[] = $request->post->periodID;
		}
		*/

		// removed since 2023
		/*
		if (!isset($request->post->avatarID) || empty($request->post->avatarID)) {
			return new Message('alert', L('error.teamEmptyAvatar'));	
		} else {
			if($request->post->avatarID==5){
				if (isset($request->files->customizeImage) && !empty($request->files->customizeImage['name'])) {
				//	return new Message('alert', L('error.teamEmptyCustomizedAvatar'));	
				//} else {

					$target_dir = "upload/";
					$target_file = $target_dir.time()."_".basename($request->files->customizeImage['name']);

					$copyImage = imagecreatefromstring(file_get_contents($request->files->customizeImage['tmp_name']));
					$imgWidth = imagesx($copyImage);
					$imgHeight = imagesy($copyImage);
					$imgWidthHeightRation = $imgWidth / $imgHeight;

					$scaleWidth = 170;
					$scaleHeight = 170;
					$scaleX = 0;
					$scaleY = 0;
					if ($imgWidthHeightRation < 1) {
						$scaleWidth = floor($imgWidthHeightRation * 170);
						$scaleX = (170 - $scaleWidth) / 2;
					} else if ($imgWidthHeightRation > 1) {
						$scaleHeight = floor(170 / $imgWidthHeightRation);
						$scaleY = (170 - $scaleHeight) / 2;
					}

					$imgResized = imagecreatetruecolor(170, 170);
					imagecopyresampled($imgResized, $copyImage, $scaleX, $scaleY, 0, 0, 
						$scaleWidth, $scaleHeight, $imgWidth, $imgHeight);

					if(imagejpeg($imgResized, $target_file)){
						$teamImage = $target_file;
					} else {
						return new Message('alert', L('error.teamCustomizedAvatarUploadFail'));	
					}					
				} else {
					$teamImage = $teamObj->teamImage;
				}
				
			} else {
				$teamImage = defaultAvatar::find($request->post->avatarID)->url;
			}
				
			$editFields['teamImage'] = "?";
			$editValues[] = $teamImage;

		}
		*/
		
		if (!isset($request->post->teamName) || empty($request->post->teamName)) {
			return new Message('alert', L('error.teamEmptyName'));			
		} else {
			if($teamObj->teamName!=$request->post->teamName){
				if(!is_null(self::find_by_name($request->post->teamName))){
					return new Message('alert', L('error.teamDuplicateName'));		
				}
			}
						
			$editFields['teamName'] = "?";
			$editValues[] = $request->post->teamName;
						
		}

		if (!isset($request->post->status) || empty($request->post->status)) {
			$status = 2;
		} else {
			$status = $request->post->status;
		}		

		$editFields['status'] = $status;

		// team leader check		
		if (!isset($request->post->leaderNameChi) || empty($request->post->leaderNameChi)) 
			return new Message('alert', L('error.teamEmptyLeaderChineseName'));		

		if (!isset($request->post->leaderNameEng) || empty($request->post->leaderNameEng)) 
			return new Message('alert', L('error.teamEmptyLeaderEnglishName'));			
			
		if (!isset($request->post->leaderAge) || empty($request->post->leaderAge)) {
			return new Message('alert', L('error.teamEmptyLeaderAge'));		
		} else {			
			if($category_id<=3){
				if($request->post->leaderAge<15) {					
					return new Message('alert', L('error.teamLeaderInvalidAge'));		
				} else {					
					$submissionFee += 50;
					$donateFee += 300;
				}
			} else {
				//if(($request->post->leaderAge>=15 && $request->post->leaderAge<18) || $request->post->leaderAge<3) {
				if($request->post->leaderAge<15) {
					return new Message('alert', L('error.teamLeaderInvalidAge'));		
				} else {
					if($request->post->leaderAge>=15) {
						$submissionFee += 50;
						$donateFee += 300;
					} if(($request->post->leaderAge>=3 && $request->post->leaderAge<=14)) {
						$submissionFee += 50;
						$donateFee += 130;
					}
				}
			}
		}

		if (!isset($request->post->leaderGender) || empty($request->post->leaderGender)) 
			return new Message('alert', L('error.teamEmptyLeaderGender'));	

		if (!isset($request->post->leaderMobile) || empty($request->post->leaderMobile)) 
			return new Message('alert', L('error.teamEmptyLeaderMobile'));	
		
		if (filter_var($request->post->leaderEmail, FILTER_VALIDATE_EMAIL) === FALSE)
			return new Message('alert', L('error.teamEmptyLeaderEmail'));	

		if (!isset($request->post->leaderAddr1) || empty($request->post->leaderAddr1)) 
			return new Message('alert', L('error.teamEmptyLeaderAddress'));	

		if (!isset($request->post->leaderEmContactName) || empty($request->post->leaderEmContactName)) 
			return new Message('alert', L('error.teamEmptyLeaderEmContactName'));	

		if (!isset($request->post->leaderEmContactMobile) || empty($request->post->leaderEmContactMobile)) 
			return new Message('alert', L('error.teamEmptyLeaderEmContactMobile'));	

		if (!isset($request->post->leaderShirtSize) || empty($request->post->leaderShirtSize)) 
			return new Message('alert', L('error.teamEmptyLeaderShirtSize'));				

		// team member check
		foreach($request->post->memberNameEng as $idx=>$nameEng){
			if(trim($nameEng)!="" || trim($request->post->memberNameChi[$idx])!="" || trim($request->post->memberAge[$idx])!="" || trim($request->post->memberGender[$idx])!="" 
			|| trim($request->post->memberMobile[$idx])!="" || trim($request->post->memberEmail[$idx])!="" || trim($request->post->memberEmContactName[$idx])!="" || trim($request->post->memberEmContactMobile[$idx])!="" || trim($request->post->memberShirtSize[$idx])!="") {
				if($request->post->memberNameChi[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberChineseName'));		
				}

				if($request->post->memberAge[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberAge'));		
				} else {					
					if($category_id<=3){
						if($request->post->memberAge[$idx]<15) {
							return new Message('alert', L('error.teamMemberInvalidAge'));		
						} else {
							$submissionFee += 50;
							$donateFee += 300;
						}
					} else {
						//if(($request->post->memberAge[$idx]>=15 && $request->post->memberAge[$idx]<18) || $request->post->memberAge[$idx]<3) {
						if($request->post->memberAge[$idx]<3) {
							return new Message('alert', L('error.teamMemberInvalidAge'));		
						} else {
							if($request->post->memberAge[$idx]>=15) {
								$submissionFee += 50;
								$donateFee += 300;
							} if(($request->post->memberAge[$idx]>=3 && $request->post->memberAge[$idx]<=14)) {
								$submissionFee += 50;
								$donateFee += 130;
							}
						}
					}
				}
				
				if($request->post->memberGender[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberGender'));		
				}
				
				if($request->post->memberMobile[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberMobile'));		
				}	
				
				if($request->post->memberEmail[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberEmail'));		
				}	
				
				if($request->post->memberEmContactName[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberEmContactName'));		
				}	
				
				if($request->post->memberEmContactMobile[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberEmContactMobile'));		
				}			

				if($request->post->memberShirtSize[$idx]==""){
					return new Message('alert', L('error.teamEmptyMemberShirtSize'));		
				}					
			}
		}

		// promo code check
		if (isset($request->post->promoCode) && !empty($request->post->promoCode)) {
			if(is_null(promoCode::findCode($request->post->promoCode))){
				return new Message('alert', L('error.promoCodeInvalid'));		
			} else {
				$submissionFee = 0;
				$totalFee = $donateFee;
				$editFields['promoCode'] = "?";
				$editValues[] = $request->post->promoCode;
			}
		} else {
			$totalFee = $submissionFee + $donateFee;
		}
		
		$editFields['submissionFee'] = $submissionFee;
		$editFields['donateFee'] = $donateFee;
		$editFields['totalFee'] = $totalFee;

		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $userObj->id;
		}

		$sql = Sql::update('team')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute($editValues)) {


			$stmDelMember = Sql::delete('teamMember')->where(['teamID', '=', "'".$request->get->id."'"]);
			if($stmDelMember->execute()){

				// insert leader //
				$sql = Sql::insert('teamMember')->setFieldValue(['teamID' => "?", 'roleID' => "?", 'nameChi' => "?", 'nameEng' => "?", 
				'age'=>"?", 'gender'=>"?", 'mobile'=>"?",  'email'=>"?", 'addr1'=>"?",'addr2'=>"?",'emContactName'=>"?",'emContactMobile'=>"?",'shirtSize'=>"?",'status'=>"?"]);			

				if ($sql->prepare()->execute([$request->get->id, 1, $request->post->leaderNameChi, 
					$request->post->leaderNameEng, $request->post->leaderAge, $request->post->leaderGender,
					$request->post->leaderMobile, $request->post->leaderEmail, 
					$request->post->leaderAddr1, $request->post->leaderAddr2, 
					$request->post->leaderEmContactName, $request->post->leaderEmContactMobile, $request->post->leaderShirtSize, 1
				])) {
					// insert member
					foreach($request->post->memberNameEng as $idx=>$nameEng){
						if(trim($nameEng)!="" || trim($request->post->memberNameChi[$idx])!="" || trim($request->post->memberAge[$idx])!="" || trim($request->post->memberGender[$idx])!="" 
				|| trim($request->post->memberMobile[$idx])!="" || trim($request->post->memberEmail[$idx])!="" || trim($request->post->memberEmContactName[$idx])!="" || trim($request->post->memberEmContactMobile[$idx])!="" || trim($request->post->memberShirtSize[$idx])!="") {
							$sql = Sql::insert('teamMember')->setFieldValue(['teamID' => "?", 'roleID' => "?", 'nameChi' => "?", 'nameEng' => "?", 
							'age'=>"?", 'gender'=>"?", 'mobile'=>"?",  'email'=>"?", 'addr1'=>"''",'addr2'=>"''",'emContactName'=>"?",'emContactMobile'=>"?",'shirtSize'=>"?",'status'=>"?"]);			
							
							$sql->prepare()->execute([$request->get->id, 2, $request->post->memberNameChi[$idx], 
								$nameEng, $request->post->memberAge[$idx], $request->post->memberGender[$idx],
								$request->post->memberMobile[$idx], $request->post->memberEmail[$idx], 
								$request->post->memberEmContactName[$idx], $request->post->memberEmContactMobile[$idx], $request->post->memberShirtSize[$idx], 1
							]);	
						}
					}
				}	
				//return new Action('redirect', WebSystem::path(Route::getRouteByName('page.teamList')->path(['pg'=>1]), false, false));
				return new Action('redirect', WebSystem::path(Route::getRouteByName('page.teamInfo')->path(['id'=>$request->get->id]), false, false));

			} else {
				return new Message('alert', L('error.unableUpdate'));
			}	
			
			//return new Action('redirect', WebSystem::path(Route::getRouteByName('page.promoCodeList')->path(['pg'=>1]), false, false));
		} else {
			return new Message('alert', L('error.unableUpdate'));
		}		
	}

	public function delete($request) {	
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
			
		if (!isset($request->get->id) || empty($request->get->id))
			return new Message('alert', L('error.promoEmptyID'));	
			
		$sql = Sql::delete('promoCode')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {
			return new Message('info', L('info.promoCodeDeleted'));
		} else {
			return new Message('alert', L('error.promoCodeDeleteFailed'));
		}
	}

	public static function find_by_name($name, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("team")->where(['teamName', '=', "?"]);
		$stm = $sql->prepare();
		$stm->execute([$name]);
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}

	public function export($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));

		$xls = new Excel("w");
		$xls->wsObj->setTitle("TeamList");

		$headerStyle = [
			'font' => [ 'bold' =>true],
			'borders' => [ 'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] ],
			'alignment' => [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER ],
		];

		$headerArr = ['SystemID', 'CategoryID', 'Category', 'PeriodID', 'Period', 'TeamName', 'PromoCode', 'Confirmed?', 'Status',
				'Leader-NameChi', 'Leader-NameEng', 'Leader-Age', 'Leader-Gender', 'Leader-Mobile', 'Leader-Email', 'Leader-Addr1', 'Leader-Addr2', 'Leader-EmContactName', 'Leader-EmContactMobile', 'Leader-EventTeeSize',
				'Member-NameChi', 'Member-NameEng', 'Member-Age', 'Member-Gender', 'Member-Mobile', 'Member-Email', 'Member-EmContactName', 'Member-EmContactMobile', 'Member-EventTeeSize',
				'Member-NameChi', 'Member-NameEng', 'Member-Age', 'Member-Gender', 'Member-Mobile', 'Member-Email', 'Member-EmContactName', 'Member-EmContactMobile', 'Member-EventTeeSize',
				'Member-NameChi', 'Member-NameEng', 'Member-Age', 'Member-Gender', 'Member-Mobile', 'Member-Email', 'Member-EmContactName', 'Member-EmContactMobile', 'Member-EventTeeSize'
				];
		$xls->setSheetHeader($headerArr, 0, $headerStyle);

		$sql = Sql::select('team');
		$stm = $sql->prepare();
		$stm->setFetchMode(\PDO::FETCH_OBJ);
		$stm->execute();

		// get teamMember
		$sqlTeamMember = Sql::select('teamMember')->where(['teamID', '=', '?'])->order('roleID');
		$stmTeamMember = $sqlTeamMember->prepare();
		$stmTeamMember->setFetchMode(\PDO::FETCH_OBJ);

		foreach ($stm as $row) {
			$obj = $this->extraProcess($row);

			$stmTeamMember->execute([$obj->id]);

			$dataLine = [
	        	['value'=>$obj->id, 'type'=>'s'], 
				['value'=>$obj->categoryID, 'type'=>'n'], 
	        	['value'=>$obj->teamCategory, 'type'=>'s'], 
				['value'=>$obj->periodID, 'type'=>'n'], 
	        	['value'=>$obj->teamPeriod, 'type'=>'s'], 
	        	['value'=>$obj->teamName, 'type'=>'s'], 
	        	['value'=>$obj->promoCode, 'type'=>'s'], 
	        	['value'=>($obj->userID == 0)?'Pending':'Confirmed', 'type'=>'s'],
				['value'=>$obj->statusName, 'type'=>'s'],
        		];
			foreach ($stmTeamMember as $tm) {
				$tmDL_Basic = [
					['value'=>$tm->nameChi, 'type'=>'s'], 
					['value'=>$tm->nameEng, 'type'=>'s'], 
					['value'=>$tm->age, 'type'=>'n'], 
					['value'=>$tm->gender, 'type'=>'s'], 
					['value'=>$tm->mobile, 'type'=>'s'], 
					['value'=>$tm->email, 'type'=>'s'], 
				];
				$tmDL_Addr = [
					['value'=>$tm->addr1, 'type'=>'s'], 
					['value'=>$tm->addr2, 'type'=>'s'], 
				];
				$tmDL_Emergency = [
					['value'=>$tm->emContactName, 'type'=>'s'], 
					['value'=>$tm->emContactMobile, 'type'=>'s'], 
				];
				$tmDL_Extra = [
					['value'=>$tm->shirtSize, 'type'=>'s']
				];				
				if ($tm->roleID == 1)
					$dataLine = array_merge($dataLine, $tmDL_Basic, $tmDL_Addr, $tmDL_Emergency, $tmDL_Extra);
				else
					$dataLine = array_merge($dataLine, $tmDL_Basic, $tmDL_Emergency, $tmDL_Extra);
			}
			$xls->writeRowData($dataLine);
		}

		$xls->downloadFile("teamList");
		return null;
	}

	// confirm a team
	public function confirm($request) {	

		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		
		$userObj = unserialize($_SESSION['user']);	
		$teamObj = self::find($request->get->id);
		$leaderObj = self::findLeader($request->get->id);

		if (!isset($request->get->id) || empty($request->get->id))
			return new Message('alert', L('error.teamEmptyID'));
			
		$userName = "sateam".WebSystem::generateStringHash(4, "LETTER", "L");
		$passcode = WebSystem::generateStringHash(8);
		$roleID = 2;
		$status = 1;
		
		$sql = Sql::insert('user')->setFieldValue(['username' => "?", 'email' => "?", 'roleID' => "?", 'password' => "?", 
		'status'=>"?", 'createBy'=>$userObj->id, 'modifyBy'=>$userObj->id])->prepare();

		if($sql->execute([$userName, $leaderObj->email, $roleID, password_hash($passcode, PASSWORD_BCRYPT),$status])){
			$teamUserID = db()->lastInsertId();
			$editFields = ['userID' => "?", 'createBy' => "?", 'modifyBy' => "?"];
			$editValues = [$teamUserID, $userObj->id, $userObj->id];		
			
			$sql = Sql::update('team')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);
			if ($sql->prepare()->execute($editValues)) {				
				if (filter_var($leaderObj->email, FILTER_VALIDATE_EMAIL)) {
					try {
						$tpl = template::findName('confirmationEmail');
						$var = ['team_name' => $teamObj->teamName, 'leader_name'=>$leaderObj->nameChi, 'user_name'=>$userName, 'password'=>$passcode];
						$content = template::replaceVar($tpl, $var);
						$mail = new Email($tpl->subject);
						$mail->addAddress($leaderObj->email, 'Email');
						$mail->setHTMLBody($content);
						$mail->send();
					} catch (Exception $e) {
						return new Data(['success'=>false, 'message'=>'email sent fail']);
					}
				}
				
				return new Data(['success'=>true, 'message'=>L('info.teamConfirmSuccess'), 'id'=>$request->get->id, 'userID'=>$teamUserID]);
			}

		}
	
	}

	public static function getTeamCountByPeriod($periodID) {	
		$periodObj = period::find($periodID);
		if(is_null($periodObj)){
			return 0;
		}

		$sql = Sql::select('team')->where(['periodID', '=', "'".$periodID."'"])->where(['userID', '!=', 0])->where(['status', '=', "'1'"]);
		$stm = $sql->prepare();
		$stm->execute();			
		return $stm->rowCount();
	}


	public static function getTeamCountByPeriodAndCategory($periodID, $categoryID) {	
		$periodObj = period::find($periodID);
		if(is_null($periodObj)){
			return 0;
		}

		$sql = Sql::select('team')->where(['periodID', '=', "'".$periodID."'"])->where(['userID', '!=', 0])->where(['categoryID', '=', "'".$categoryID."'"])->where(['status', '=', "'1'"]);
		$stm = $sql->prepare();
		$stm->execute();			
		return $stm->rowCount();
	}	

	public static function getParticipantCountByPeriod($periodID) {	
		$periodObj = period::find($periodID);
		if(is_null($periodObj)){
			return 0;
		}

		$stmTeamHeadTotal = Sql::select(['team', 't'])->setFieldValue('count(tm.id) teamHeadTotal')
		->leftJoin(['teamMember', 'tm'], 't.id = tm.teamID')
		->where(['t.status', '=', 1])->where(['t.userID', '!=', 0])
		->where(['tm.status', '=', 1])->where(['t.periodID', '=', $periodID])
		->prepare();

		$stmTeamHeadTotal->execute();
		$objTeamHeadTotal = $stmTeamHeadTotal->fetch();
		
		return $objTeamHeadTotal['teamHeadTotal'];
	}	

	public static function getParticipantCountByPeriodAndCategory($periodID, $categoryID) {	
		$periodObj = period::find($periodID);
		if(is_null($periodObj)){
			return 0;
		}

		$stmTeamHeadTotal = Sql::select(['team', 't'])->setFieldValue('count(tm.id) teamHeadTotal')
		->leftJoin(['teamMember', 'tm'], 't.id = tm.teamID')
		->where(['t.status', '=', 1])->where(['t.userID', '!=', 0])
		->where(['tm.status', '=', 1])->where(['t.periodID', '=', $periodID])->where(['t.categoryID', '=', $categoryID])
		->prepare();

		$stmTeamHeadTotal->execute();
		$objTeamHeadTotal = $stmTeamHeadTotal->fetch();
		
		return $objTeamHeadTotal['teamHeadTotal'];
	}		


	public static function getDonaterAmountByPeriodAndCategory($periodID, $categoryID) {	
		$periodObj = period::find($periodID);
		if(is_null($periodObj)){
			return 0;
		}	

		$stmDonatorAmt = Sql::select(['team', 't'])->setFieldValue('COALESCE(SUM(td.donatorAmount), 0) donateAmt')
		->leftJoin(['teamDonator', 'td'], 't.id = td.teamID')
		->where(['t.status', '=', 1])->where(['t.userID', '!=', 0])
		->where(['td.status', '=', 1])->where(['t.periodID', '=', $periodID])->where(['t.categoryID', '=', $categoryID])
		->prepare();

		$stmDonatorAmt->execute();
		$objDonatorAmt = $stmDonatorAmt->fetch();
		return $objDonatorAmt['donateAmt'];
	}			

	/*
	public static function isJoinable() {	

		$periods = period::find_all();
		foreach($periods as $periodObj){
			if (self::getTeamCountByPeriod($periodObj->id)<cfg('event')['maxTeamCount']){
				return true;
			}
		}
		return false;

	}
	*/
	public static function isJoinable() {	

		$periods = period::find_all();

		foreach($periods as $periodObj){

			if (self::getParticipantCountByPeriod($periodObj->id)<$periodObj->maxParticipantCount){
				return true;
			}
		}

		return false;
	}

	// confirm a team
	public function uploadAvatar($request) {	

		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		
		$userObj = unserialize($_SESSION['user']);			

		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.teamEmptyID')]);

		$teamObj = self::find($request->get->id);
		$teamImage = "";
		if (!isset($request->files->customizeImage) || empty($request->files->customizeImage)) {
			return new Data(['success'=>false, 'message'=>L('error.teamEmptyCustomizedAvatar')]);
		} else {

			$target_dir = "upload/";
			$target_file = $target_dir.time()."_".basename($request->files->customizeImage['name']);

			$copyImage = imagecreatefromstring(file_get_contents($request->files->customizeImage['tmp_name']));
			$imgWidth = imagesx($copyImage);
			$imgHeight = imagesy($copyImage);
			$imgWidthHeightRation = $imgWidth / $imgHeight;

			$scaleWidth = 170;
			$scaleHeight = 170;
			$scaleX = 0;
			$scaleY = 0;
			if ($imgWidthHeightRation < 1) {
				$scaleWidth = floor($imgWidthHeightRation * 170);
				$scaleX = (170 - $scaleWidth) / 2;
			} else if ($imgWidthHeightRation > 1) {
				$scaleHeight = floor(170 / $imgWidthHeightRation);
				$scaleY = (170 - $scaleHeight) / 2;
			}

			$imgResized = imagecreatetruecolor(170, 170);
			imagecopyresampled($imgResized, $copyImage, $scaleX, $scaleY, 0, 0, 
				$scaleWidth, $scaleHeight, $imgWidth, $imgHeight);

			if(imagejpeg($imgResized, $target_file)){
				$teamImage = $target_file;
				$editFields['teamImage'] = "'".$teamImage."'";
				$editFields['modifyDate'] = "NOW()";
				$editFields['modifyBy'] = $userObj->id;
				$sql = Sql::update('team')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);
				if ($sql->prepare()->execute()) {
					if($teamObj->teamImage!="upload/enroll_default_v1_01.jpg") {
						unlink($teamObj->teamImage);
					}
					//return new Data(['success'=>false, 'message'=>L('error.teamCustomizedAvatarUploadFail')]);
					return new Data(['success'=>true, 'message'=>L('info.avatarUploadSuccess'), 'imagePath'=>$teamImage]);
				} else {	
					return new Data(['success'=>false, 'message'=>L('error.teamCustomizedAvatarUploadFail')]);
				}

			} else {
				return new Data(['success'=>false, 'message'=>L('error.teamCustomizedAvatarUploadFail')]);
			}					
		}


	
	}
	
	public function sendRePayEmail($request) {
		if (!user::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));

		$userObj = unserialize($_SESSION['user']);			

		if (!isset($request->get->id) || empty($request->get->id))
			return new Message('alert', L('error.teamEmptyID'));

		$teamObj = self::find($request->get->id);
		$leaderObj = self::findLeader($request->get->id);

		$baseUrl = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'];
		$url_link = $baseUrl.WebSystem::path(Route::getRouteByName('page.teamRepay')->path(['hash'=>base64_encode(json_encode(['teamID' => $teamObj->id]))]), false, false);
		
		if (filter_var($leaderObj->email, FILTER_VALIDATE_EMAIL)) {
			try {
				$tpl = template::findName('rePayEmail');
				$var = ['leader_name' => $leaderObj->nameChi, 'url_link'=>$url_link];
				$content = template::replaceVar($tpl, $var);
				$mail = new Email($tpl->subject);
				$mail->addAddress($leaderObj->email, 'Email');
				$mail->setHTMLBody($content);
				$mail->send();	
				//return new Message('info', L('info.forgetPasswordEmailSent'));
				return new Message('info', L('info.emailSendSuccess'));
			} catch (Exception $e) {
				return new Message('alert', L('info.emailSendFail'));
			} 
		}
	}

	/* Page Function */
	public function repay($request) {
		
		$errMsg = "";

		if (!isset($request->get->hash) || empty($request->get->hash))
			return new Page('team/repay', ['obj'=>null, 'err'=>L('error.invalidLink')]);
			//return new Message('alert', L('error.invalidLink'));
			//return new Data(['success'=>false, 'message'=>L('error.invalidLink')]);

		$param_json =  base64_decode($request->get->hash);

		if(!is_array(json_decode($param_json, true)))
			return new Page('team/repay', ['obj'=>null, 'err'=>L('error.invalidLink')]);
			//return new Message('alert', L('error.invalidLink'));	
			//return new Data(['success'=>false, 'message'=>L('error.invalidLink')]);
		

		$param = json_decode($param_json);		

		if(!isset($param->teamID) || empty($param->teamID))
			return new Page('team/repay', ['obj'=>null, 'err'=>L('error.invalidLink')]);
			//return new Message('alert', L('error.invalidLink'));	
			//return new Data(['success'=>false, 'message'=>L('error.invalidLink')]);

		$obj = $this->extraProcess(self::find($param->teamID));
		if(is_null($obj))
			return new Page('team/repay', ['obj'=>null, 'err'=>L('error.invalidLink')]);
			//return new Message('alert', L('error.invalidLink'));
			//return new Data(['success'=>false, 'message'=>L('error.invalidLink')]);

		return new Page('team/repay', ['obj'=>$obj, 'err'=>'']);
	}

	public function rePayment($request) {
		if (!isset($request->get->id) || empty($request->get->id))
			return new Message('alert', L('error.teamEmptyID'));

		$teamObj = self::find($request->get->id);

		$remark = "[repay] Submission Fee $".$teamObj->submissionFee.". Donate Fee $".$teamObj->donateFee;
		$type = "register";
		//return payment::createOrder($teamID, $totalFee, $remark, $type);
		return new Action('redirect', payment::createOrder($teamObj->id, $teamObj->totalFee, $remark, $type));
	}
	
}