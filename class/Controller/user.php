<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql, Database\Listable;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem, Utility\Email; 
use Controller\formLayout, Controller\team;

class user implements Listable {
	const AdminUserID = [1, 2, 3];
	
	private $stmStatus = null;
	private $validMinute = 30;
	
	public static function checklogin() { return (isset($_SESSION) && isset($_SESSION['user'])); }
	
	public static function find($id, $fetchMode=\PDO::FETCH_OBJ, $includePW=false) {
		$sql = Sql::select("user")->setFieldValue("*, INET_NTOA(loginIP) 'loginIPAddr'")->where(['id', '=', $id]);
		$stm = $sql->prepare();
		$stm->execute();
		$obj = $stm->fetch($fetchMode);
		if (!$includePW) {
			if (is_array($obj))
				unset($obj['password']);
			else
				unset($obj->password);
		}
		if ($obj === false) return null;
		return $obj;
	}

	public static function findAll($fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("user")->where(['status', '=', "1"]);
		$stm = $sql->prepare();
		$stm->execute();
		return $stm;
	}    
	
	public static function findByName($name, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("user")->where(['username', '=', "?"]);
		$stm = $sql->prepare();
		$stm->execute([$name]);
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}	

	public static function findByEmail($email, $fetchMode=\PDO::FETCH_OBJ) {
		$sql = Sql::select("user")->where(['email', '=', "?"]);
		$stm = $sql->prepare();
		$stm->execute([$email]);
		$obj = $stm->fetch($fetchMode);
		if ($obj === false) return null;
		return $obj;
	}	
	
	public function login($request) { 
		if (!$request->isValued('user')) return new Message('alert', L('error.userEmptyUserName'));
		$user = trim($request->existsIn('user'));
		if (!$request->isValued('pass')) return new Message('alert', L('error.userEmptyPassword'));      
		$pw = trim($request->existsIn('pass'));

		$sql = Sql::select("user")->where(['status', '=', '1']);
		$username = $user;

		$stm = $sql->where(['username', '=', '?'])->prepare();
		$stm->execute([$username]);
		
		if ($stm->rowCount() < 1) return new Message('alert', L('error.userNotFound'));
		$userObj = $stm->fetch(\PDO::FETCH_OBJ);

		if (password_verify($pw, $userObj->password)) {
			$_SESSION['user'] = serialize($userObj);
			
			$editFields = ["loginIP"=>"INET_ATON('".trim($_SERVER['REMOTE_ADDR'])."')", "lastLogin"=>"NOW()"];
			
			Sql::update('user')->setFieldValue($editFields)->where(['id', '=', $userObj->id])->execute();
			
			return new Action('redirect', WebSystem::path(Route::getRouteByName('page.dashboard')->path(), false, false));
			//return new Responses\Action('refresh', implode(';', [5, WebSystem::path(Route::getRouteByName('page.login')->path(), false, false).'/main']));
		} 

		//return new Message('alert', L('error.authienticationFailed'));
		return new Data(['success'=>false, 'message'=>L('error.authienticationFailed'), 'field'=>'notice']);
	}
   
	public function logout($request) {
		unset($_SESSION['user']);
		return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
	}
	
	public function isAdmin($id) {
		return in_array($id, self::AdminUserID);
	}
	
	/* Page Function */
	public function list($request) {
		if (!self::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false)); 
		
		/*
		$sql = Sql::select('user');
	
		if (isset($request->get->q)) {
			$hash = $request->get->q;
			if (!empty($_SESSION['search'][$hash]['username']))
				$sql->where(['username', 'LIKE', dbes("%".$_SESSION['search'][$hash]['username']."%")]);
			if (!empty($_SESSION['search'][$hash]['email']))
				$sql->where(['email', 'LIKE', dbes("%".$_SESSION['search'][$hash]['email']."%")]);;				
			if (!empty($_SESSION['search'][$hash]['roleID']))
				$sql->where(['roleID', '=', "'".$_SESSION['search'][$hash]['roleID']."'"]);				
			if (!empty($_SESSION['search'][$hash]['status']))
				$sql->where(['status', '=', "'".$_SESSION['search'][$hash]['status']."'"]);
			if (!empty($_SESSION['search'][$hash]['sql_order_field']))
				$sql->order($_SESSION['search'][$hash]['sql_order_field'], (!empty($_SESSION['search'][$hash]['sql_order_seq']))?$_SESSION['search'][$hash]['sql_order_seq']:'ASC');
		}
		
		$listPage = new FormPage('user/list', $sql);
		$listPage->setLister($this);
		return $listPage;
		*/
		$obj = null;
		return new FormPage('user/list', $obj);
	}
	
	public function extraProcess($listObj) {
		if (is_null($this->stmStatus))
			$this->stmStatus = Sql::select('status')->where(['id', '=', "?"])->prepare();
			
		$this->stmStatus->execute([$listObj->status]);
		$objStatus = $this->stmStatus->fetch();
		$listObj->statusName = $objStatus['name'];
		
		return $listObj;

	}
	
	public function search($request) {
		if (!self::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false)); 
		
		$hash = '';
		if ($request->method == 'POST') {
			unset($_SESSION['search']);
			$hash = sha1("search_".time());
			$_SESSION['search'][$hash]['username'] = $request->post->username;
			$_SESSION['search'][$hash]['email'] = $request->post->email;
			$_SESSION['search'][$hash]['roleID'] = $request->post->roleID;
			$_SESSION['search'][$hash]['status'] = $request->post->status;
		}
		if ($request->method == 'GET') {
			$hash = (isset($request->get->q))?$request->get->q:sha1("order_".time());
			$_SESSION['search'][$hash]['sql_order_field'] = $request->get->field;
			$_SESSION['search'][$hash]['sql_order_seq'] = $request->get->order;
		}

		$param = ['pg'=>1];
		if (!empty($hash)) $param['q'] = $hash;
		return new Action('redirect', WebSystem::path(Route::getRouteByName('page.userList')->path($param), false, false));
	}
		
	/* Page Function */
	public function form($request) {
		if (!self::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);
		
		return new FormPage('user/form', $obj);
	}
	
	/* Page Function */
	public function info($request) {
		if (!self::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		
		$id = $request->get->id;
		if ($request->get->id == 'curr') {
			$userObj = unserialize($_SESSION['user']);
			$id = $userObj->id;
		}
		return new Page('user/info', ['obj'=>$this->extraProcess(self::find($id))]);
	}
	
	/* Page Function */
	public function searchform($request) {
		if (!self::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
		
		$obj = (isset($request->get->q))?$_SESSION['search'][$request->get->q]:null;
		return new FormPage('user/search', $obj);
	}
	
	public function add($request) {

		if (!self::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);
		
		if (!isset($request->post->username) || empty($request->post->username)) 
			return new Data(['success'=>false, 'message'=>L('error.userEmptyUserName'), 'field'=>'userName']);

		$checkUserNameObject = self::findByName($request->post->username);	
		if (!is_null($checkUserNameObject)) 
			return new Data(['success'=>false, 'message'=>L('error.userNameOccupied'), 'field'=>'userName']);

		if (!isset($request->post->displayName) || empty($request->post->displayName)) 
			return new Data(['success'=>false, 'message'=>L('error.userEmptyDisplayName'), 'field'=>'displayName']);

		/*
		if (!isset($request->post->email) || empty($request->post->email)) 
			return new Data(['success'=>false, 'message'=>L('error.userEmptyEmail'), 'field'=>'userEmail']);
		
		if (filter_var($request->post->email, FILTER_VALIDATE_EMAIL) === FALSE)
			return new Data(['success'=>false, 'message'=>L('error.userEmailInvalid'), 'field'=>'userEmail']);		
			
		$checkUserEmailObject = self::findByEmail($request->post->email);	
		if (!is_null($checkUserEmailObject)) 
			return new Data(['success'=>false, 'message'=>L('error.userEmailOccupied'), 'field'=>'userEmail']);
		
		if (!isset($request->post->phone) || empty($request->post->phone)) 
			return new Data(['success'=>false, 'message'=>L('error.userEmptyPhone'), 'field'=>'userPhone']);	
		
		if (!isset($request->post->position) || empty($request->post->position)) 
			return new Data(['success'=>false, 'message'=>L('error.userEmptyPosition'), 'field'=>'position']);			
		*/

		if (!isset($request->post->roleID) || empty($request->post->roleID)) 
			return new Data(['success'=>false, 'message'=>L('error.userEmptyRole'), 'field'=>'roleID']);

		/*
        if (!isset($request->files->signatureDoc) || empty($request->files->signatureDoc['tmp_name'])) 
            return new Data(['success'=>false, 'message'=>L('error.userEmptySignatureDoc'), 'field'=>'signatureDoc']);  			

		if (!isset($request->post->password) || empty($request->post->password)) 
			return new Data(['success'=>false, 'message'=>L('error.userEmptyPassword'), 'field'=>'userPW']);

		if (!isset($request->post->cfmPassword) || empty($request->post->cfmPassword)) 
			return new Data(['success'=>false, 'message'=>L('error.userEmptyConfirmPassword'), 'field'=>'userCfmPW']);

		if ($request->post->password != $request->post->cfmPassword)
			return new Data(['success'=>false, 'message'=>L('error.userPasswordsNotMatch'), 'field'=>'userCfmPW']);
		*/
		$signatureDocID = 0;

		if (isset($request->files->signatureDoc) && !empty($request->files->signatureDoc)) {
			$signatureDocID = documentHelper::upload($request->files->signatureDoc, "SIGNATURE");
		}

		$sql = Sql::insert('user')->setFieldValue([
			'username' => "?", 
			'displayName' => "?", 
			'email' => "?", 
			'phone' => "?", 
			'position' => "?", 
			'roleID' => "?", 
			'signatureDocID' => "?", 
			'password' => "?", 
			'status'=>"?", 
			'createBy'=>$currentUserObj->id, 
			'modifyBy'=>$currentUserObj->id]
		);

		$addValues = [
			$request->post->username, 
			$request->post->displayName, 
			$request->post->email, 
			$request->post->phone, 
			$request->post->position, 
			$request->post->roleID, 
			$signatureDocID, 
			password_hash($request->post->password, PASSWORD_BCRYPT), 
			1
		];

		if ($sql->prepare()->execute($addValues)) {
			$id = db()->lastInsertId();

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "User";
			$logData['referenceID'] = $id;
			$logData['action'] = "Insert";
			$logData['description'] = "Create New User [".$request->post->username."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $addValues;
			$logData['changes'] = 
				[
					[
						"key"=>"username", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->username)
					],[
						"key"=>"displayName", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->displayName)
					]
					,[
						"key"=>"email", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->email)
					]
					,[
						"key"=>"phone", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->phone)
					]
					,[
						"key"=>"position", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->position)
					]
					,[
						"key"=>"roleID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($request->post->roleID)
					],[
						"key"=>"signatureDocID", 
						"valueFrom"=>"", 
						"valueTo"=>strip_tags($signatureDocID)
					]
				];

			systemLog::add($logData);			

			return new Data(['success'=>true, 'message'=>L('info.saved'), 'id'=>$id, 'name'=>$request->post->username]);
			//return new Action('redirect', WebSystem::path(Route::getRouteByName('page.userInfo')->path(['id'=>$id]), false, false));
		} else {
			return new Data(['success'=>false, 'message'=>L('error.unableInsert'), 'field'=>'notice']);
		}			

	}
	
	public function edit($request) {
		
		if (!self::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'field'=>'notice']);

		$currentUserObj = unserialize($_SESSION['user']);

		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.userEmptyID'), 'field'=>'notice']);

		$userObj = self::find($request->get->id);
		
		if(is_null($userObj))
			return new Data(['success'=>false, 'message'=>L('error.userNotFound'), 'field'=>'notice']);

		$editFields = [];
		$editValues = [];
		$logContent = [];
		
		if (!self::isAdmin($request->get->id)) {
			$editFields['status'] = "?";
			$editValues[] = $request->post->status;

			if($request->post->status!=$userObj->status) {
				$logContent[] = [
					"key"=>"status", 
					"valueFrom"=>$userObj->status, 
					"valueTo"=>strip_tags($request->post->status)					
				];
			}
		}
		
		if ((isset($request->post->password) && !empty($request->post->password)) || 
			isset($request->post->cfmPassword) && !empty($request->post->cfmPassword)) {
				
			if ($request->post->password != $request->post->cfmPassword)
				return new Data(['success'=>false, 'message'=>L('error.userPasswordsNotMatch'), 'field'=>'userCfmPW']);

			$editFields['password'] = "?";
			$editValues[] = password_hash($request->post->password, PASSWORD_BCRYPT);
		}

		if (!isset($request->post->displayName) || empty($request->post->displayName)) 
			return new Data(['success'=>false, 'message'=>L('error.userEmptyDisplayName'), 'field'=>'displayName']);		

		if (isset($request->post->displayName) && !empty($request->post->displayName)) {
			$editFields['displayName'] = "?";
			$editValues[] = $request->post->displayName;

			if($request->post->displayName!=$userObj->displayName) {
				$logContent[] = [
					"key"=>"displayName", 
					"valueFrom"=>$userObj->displayName, 
					"valueTo"=>strip_tags($request->post->displayName)					
				];	
			}		
		}		

		/*
		if (!isset($request->post->email) || empty($request->post->email)) 
			return new Data(['success'=>false, 'message'=>L('error.userEmptyEmail'), 'field'=>'userEmail']);
		*/

		if (isset($request->post->email) && !empty($request->post->email)) {
			if (filter_var($request->post->email, FILTER_VALIDATE_EMAIL) === FALSE)
				return new Data(['success'=>false, 'message'=>L('error.userEmailInvalid'), 'field'=>'userEmail']);

			if ($userObj->email != $request->post->email) {
				$checkUserEmailObject = self::findByEmail($request->post->email);	
				if (!is_null($checkUserEmailObject)) 
					return new Data(['success'=>false, 'message'=>L('error.userEmailOccupied'), 'field'=>'userEmail']);
			}

			$editFields['email'] = "?";
			$editValues[] = $request->post->email;

			if($request->post->email!=$userObj->email) {
				$logContent[] = [
					"key"=>"email", 
					"valueFrom"=>$userObj->email, 
					"valueTo"=>strip_tags($request->post->email)					
				];	
			}

		}	
		
		/*
		if (!isset($request->post->phone) || empty($request->post->phone)) 
			return new Data(['success'=>false, 'message'=>L('error.userEmptyPhone'), 'field'=>'userPhone']);
		*/

		if (isset($request->post->phone) && !empty($request->post->phone)) {

			$editFields['phone'] = "?";
			$editValues[] = $request->post->phone;

			if($request->post->phone!=$userObj->phone) {
				$logContent[] = [
					"key"=>"phone", 
					"valueFrom"=>$userObj->phone, 
					"valueTo"=>strip_tags($request->post->phone)					
				];	
			}			
		}					

		/*
		if (!isset($request->post->position) || empty($request->post->position)) 
			return new Data(['success'=>false, 'message'=>L('error.userEmptyPosition'), 'field'=>'position']);	
		*/

		if (isset($request->post->position) && !empty($request->post->position)) {

			$editFields['position'] = "?";
			$editValues[] = $request->post->position;

			if($request->post->position!=$userObj->position) {
				$logContent[] = [
					"key"=>"position", 
					"valueFrom"=>$userObj->position, 
					"valueTo"=>strip_tags($request->post->position)					
				];	
			}	

		}	
		
			
		if (!isset($request->post->roleID) || empty($request->post->roleID)) 
			return new Data(['success'=>false, 'message'=>L('error.userEmptyRole'), 'field'=>'roleID']);				

		if (isset($request->post->roleID) && !empty($request->post->roleID)) {

			$editFields['roleID'] = "?";
			$editValues[] = $request->post->roleID;

			if($request->post->roleID!=$userObj->roleID) {
				$logContent[] = [
					"key"=>"roleID", 
					"valueFrom"=>$userObj->roleID, 
					"valueTo"=>strip_tags($request->post->roleID)
				];	
			}	

		}			
		
		$signatureDocID = $userObj->signatureDocID;

        if (isset($request->files->signatureDoc) && !empty($request->files->signatureDoc)) {
            $signatureDocID = documentHelper::upload($request->files->signatureDoc, "SIGNATURE");
            if($signatureDocID>0) {

				$editFields['signatureDocID'] = "?";
				$editValues[] = $request->post->signatureDocID;

				if($request->post->signatureDocID!=$userObj->signatureDocID) {
					$logContent[] = [
						"key"=>"signatureDocID", 
						"valueFrom"=>$userObj->signatureDocID, 
						"valueTo"=>strip_tags($request->post->signatureDocID)					
					];	
				}

                $editFields['signatureDocID'] = "?";
                $editValues[] = $signatureDocID;  
            }
        }
		
		if (count($editFields)) {
			$editFields['modifyDate'] = "NOW()";
			$editFields['modifyBy'] = $currentUserObj->id;
		}
		
		if (count($editFields) == 0) return new Data(['success'=>false, 'message'=>L('error.nothingEdit'), 'field'=>'notice']);
		
		$sql = Sql::update('user')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute($editValues)) {
			if (count($logContent)) {
				$logData = [];
				$logData['userID']= $currentUserObj->id;
				$logData['module'] = "User";
				$logData['referenceID'] = $request->get->id;
				$logData['action'] = "Update";
				$logData['description'] = "Edit User [".$userObj->username."]";
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
	
	public function delete($request) {	
		if (!self::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'note'=>'signIn']);

		$currentUserObj = unserialize($_SESSION['user']);			
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.userEmptyID')]);	

		$userObj = self::find($request->get->id);			
			
		if (self::isAdmin($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.systemUserNotSusendable')]);	

		$sql = Sql::delete('user')->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {

			$logData = [];
			$logData['userID']= $currentUserObj->id;
			$logData['module'] = "User";
			$logData['referenceID'] = $request->get->id;
			$logData['action'] = "Delete";
			$logData['description'] = "Delete User [".$userObj->username."]";
			$logData['sqlStatement'] = $sql;
			$logData['sqlValue'] = $request->get->id;
			$logData['changes'] = [];
			systemLog::add($logData);

			return new Data(['success'=>true, 'message'=>L('info.userDeleted')]);	
		} else {
			return new Data(['success'=>false, 'message'=>L('error.userSuspendFailed')]);	
		}			

		/*
		if (!self::checklogin()) return new Action('redirect', WebSystem::path(Route::getRouteByName('page.login')->path(), false, false));
			
		if (!isset($request->get->id) || empty($request->get->id))
			return new Message('alert', L('error.userEmptyID'));	
			
		if (self::isAdmin($request->get->id))
			return new Message('alert', L('error.systemUserNotSusendable'));
		
		$sql = Sql::update('user')->setFieldValue(['status'=>2])->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {
			return new Message('info', L('info.userSuspended'));
		} else {
			return new Message('alert', L('error.userSuspendFailed'));
		}
		*/

	}

	public function forget($request) {

		if (!isset($request->post->email) || empty($request->post->email)) {
			return new Message('alert', L('error.userEmptyEmail'));
		}

		$userObj = self::findByEmail($request->post->email);
		if(!is_null($userObj)){
			
			$userID = $userObj->id;
			$teamObj = team::findByUserID($userID);

			if(!is_null($teamObj)){

				$leaderObj = team::findLeader($teamObj->id);
				$hashCode = WebSystem::generateStringHash(32);
				$expiry_date = date("Y-m-d H:i:s", strtotime("+".$this->validMinute." minutes"));


				$sql = Sql::update('forgetPassword')->setFieldValue(['status'=>2])->where(['userID', '=', $userID]);

				if ($sql->prepare()->execute()) {

					$sql = Sql::insert('forgetPassword')->setFieldValue(['userID' => "?", 'hashCode' => "?", 'expiry_date' => "?", 'status' => "?"]);

					if ($sql->prepare()->execute([$userID, $hashCode, $expiry_date, 1])) {

						$baseUrl = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'];
						$url_link = $baseUrl.WebSystem::path(Route::getRouteByName('page.userChangepassword')->path(), false, false)."?hashCode=".$hashCode;
						
						if (filter_var($request->post->email, FILTER_VALIDATE_EMAIL)) {
							try {
								$tpl = template::findName('forgetPasswordEmail');
								$var = ['leader_name' => $leaderObj->nameChi, 'minute'=>$this->validMinute, 'url_link'=>$url_link];
								$content = template::replaceVar($tpl, $var);
								$mail = new Email($tpl->subject);
								$mail->addAddress($request->post->email, 'Email');
								$mail->setHTMLBody($content);
								$mail->send();	
								//return new Message('info', L('info.forgetPasswordEmailSent'));
								return new Data(['success'=>true, 'message'=>L('info.forgetPasswordEmailSent')]);				
							} catch (Exception $e) {
								return new Data(['success'=>false, 'message'=>'email sent fail']);
							} 
						}

					} else {
						return new Data(['success'=>false, 'message'=>L('error.unableInsert')]);
					}
				} else {
					return new Data(['success'=>false, 'message'=>L('error.unableUpdate')]);
				}
			} else {
				return new Data(['success'=>false, 'message'=>L('error.userNotFound')." ".L('Or')." ".L('error.userEmailInvalid')]);	
			}
		} else {
			return new Data(['success'=>false, 'message'=>L('error.userNotFound')." ".L('Or')." ".L('error.userEmailInvalid')]);			
		}
		
		
	}

	public function resetpassword($request) {

		if (!isset($request->get->id) || empty($request->get->id))
			return new Message('alert', L('error.userEmptyID'));	

		$userObj = self::find($request->get->id, \PDO::FETCH_OBJ, true);		

		if (isset($request->post->oldPassword) && !empty($request->post->oldPassword)) {

			if(!password_verify($request->post->oldPassword, $userObj->password)){
				return new Message('alert', L('error.userInvalidOldPassword'));
			}
		}

		if (!isset($request->post->password) || empty($request->post->password)) 
			return new Message('alert', L('error.userEmptyPassword'));

		if (!isset($request->post->cfmPassword) || empty($request->post->cfmPassword)) 
			return new Message('alert', L('error.userEmptyConfirmPassword'));
	  
		if ($request->post->password != $request->post->cfmPassword)
			return new Message('alert', L('error.userPasswordsNotMatch'));

		$editFields = ["password"=>"'".password_hash($request->post->password, PASSWORD_BCRYPT)."'"];

		$sql = Sql::update('user')->setFieldValue($editFields)->where(['id', '=', $request->get->id]);
		if ($sql->prepare()->execute()) {
			return new Message('info', L('info.passwordUpdated'));			
		} else {
			return new Message('alert', L('info.unableUpdate'));
		}

	}

	public function userForm($request) {

		if (!self::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage'), 'note'=>'signIn']);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);
		
		$formName = "form-addUser";

		if(!is_null($obj)) {
			$formName = "form-editUser";
		}				

		$content = "<form id='".$formName."' class='' autocomplete='off'>";
		$content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'>".L('info.userAddHelperMessage')."</p></div>";

		$content .= "<div class='row'>";
		   $content .= formLayout::rowInputNew(L('user.userName'),'username', 'userName', 'text', 6, [], ['required', is_null($obj)?'':'disabled'], is_null($obj)?'':$obj['username']);
		   $content .= formLayout::rowInputNew(L('user.displayName'),'displayName', 'displayName', 'text', 6, [], ['required'], is_null($obj)?'':$obj['displayName']);
		   $content .= formLayout::rowInputNew(L('user.email'),'email', 'userEmail', 'text',  6, [], [], is_null($obj)?'':$obj['email']);
		   $content .= formLayout::rowInputNew(L('user.phone'),'phone', 'userPhone', 'text',  6, [], [], is_null($obj)?'':$obj['phone']);
		   $content .= formLayout::rowInputNew(L('user.position'),'position', 'position', 'text',  6, [], [], is_null($obj)?'':$obj['position']);
		  
		   $option = [""=>""];
		   $stm = Sql::select('role')->where(['status', '=', 1])->prepare();
		   $stm->execute();                                          
		   foreach ($stm as $opt) {  
				$option[$opt['id']] = $opt['name'];			  
		   }
		   $content .= formLayout::rowSelectNew(L('Role'), 'roleID', 'roleID', $option,  6, [], ['required', ($currentUserObj->roleID != 1)?' disabled':''], is_null($obj)?'':$obj['roleID']);

		   $content .= formLayout::rowInputNew(L('user.password'),'password', 'userPW', 'password',  6, [], ['required'], '');
		   $content .= formLayout::rowInputNew(L('user.confirmPassword'),'cfmPassword', 'userCfmPW', 'password',  6, [], ['required'], '');

		   $content .= formLayout::rowInputNew(L('user.signatureDoc'),'signatureDoc', 'signatureDoc', 'file',  6, [], ['accept="image/*, application/pdf"'], 
				(is_null($obj) || empty($obj['signatureDocID']))?
				'':
				'<div class="d-flex gap-2 btnGrp"><button type="button" class="btn btn-black downloadDoc btn-sm mt-2" data-id="'.$obj['signatureDocID'].'"><i class="fas fa-download"></i></button>
				<button type="button" class="btn btn-danger removeDoc btn-sm mt-2" data-id="'.$obj['signatureDocID'].'" data-user="'.$obj['id'].'" data-doc="signatureDocID"><i class="fas fa-trash"></i></button></div>'
			);  		  
	   
		   if(!is_null($obj)) {
				$option = [];
				$stm = Sql::select('status')->prepare();
				$stm->execute();                                          
				foreach ($stm as $opt) {  
					$option[$opt['id']] = L($opt['name']);
				}
				$content .= formLayout::rowSelectNew(L('Status'), 'status', 'userStatus', $option,  6, [], ['required', ((!is_null($obj) && in_array($obj['id'], self::AdminUserID)) || $currentUserObj->roleID != 1)?' disabled':''], is_null($obj)?'':$obj['status']);
		   }
		$content .= "</div>";
		$content .= "</form>";

		return new Data(['success'=>true, 'message'=>$content]);
		
	}

	public function detail($request) {

		if (!self::checklogin()) return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);

		$currentUserObj = unserialize($_SESSION['user']);
		
		$obj = null;
		if (isset($request->get->id)) 
			$obj = self::find($request->get->id, \PDO::FETCH_NAMED);
		
		$content = "<div class='row'>";

		   $content .= formLayout::rowDisplayLineNew(L('user.userName'), $obj['username'], 6);
		   $content .= formLayout::rowDisplayLineNew(L('user.displayName'), $obj['displayName'], 6);
		   $content .= formLayout::rowDisplayLineNew(L('user.email'), $obj['email'], 6);
		   $content .= formLayout::rowDisplayLineNew(L('user.phone'), $obj['phone'], 6);
		   $content .= formLayout::rowDisplayLineNew(L('user.position'), $obj['position'], 6);
		   $content .= formLayout::rowDisplayLineNew(L('Role'), role::find($obj['roleID'])->name, 6);
		   $content .= formLayout::rowDisplayLineNew(L('user.signatureDoc'),$obj['signatureDocID']>0?"<div class='d-flex gap-2 btnGrp'><button type='button' class='btn btn-black downloadDoc btn-xs' data-id=".$obj['signatureDocID']."><i class='fas fa-download'></i></button></div>":"", 6);    	   
		   $content .= formLayout::rowDisplayLineNew(L('Status'), L(status::find($obj['status'])->name), 6);

		$content .= "</div>";


		return new Data(['success'=>true, 'message'=>$content]);
		
	}	

	public static function removeDoc($request) {	
       
        if (!user::checklogin()) 
			return new Data(['success'=>false, 'message'=>L('login.signInMessage')]);
		
		if (!isset($request->get->id) || empty($request->get->id))
			return new Data(['success'=>false, 'message'=>L('error.documentEmptyID')]);	

        $docObj = documentHelper::find($request->get->id);

		if (is_null($docObj))
			return new Data(['success'=>false, 'message'=>L('error.documentNotFound')]);	        
        
        if (!isset($request->post->userID) || empty($request->post->userID))
		    return new Data(['success'=>false, 'message'=>L('error.userEmptyID')]);	
        
        $userObj = self::find($request->post->userID);

        if (is_null($userObj))
            return new Data(['success'=>false, 'message'=>L('error.userNotFound')]);	
		
        if(documentHelper::delete($docObj->id)) {

            $sql = Sql::update('user')->setFieldValue([$request->post->docType => '0'])->where(['id', '=', $request->post->userID]);
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
                $htmlContent .= "<th>".L('user.userName')."</th>";
				$htmlContent .= "<th>".L('user.displayName')."</th>";
				$htmlContent .= "<th>".L('user.email')."</th>";
				$htmlContent .= "<th>".L('user.phone')."</th>";
				$htmlContent .= "<th>".L('user.position')."</th>";
				$htmlContent .= "<th>".L('user.role')."</th>";
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
				$htmlContent .= "<th>".L('user.userName')."</th>";
				$htmlContent .= "<th>".L('user.displayName')."</th>";
				$htmlContent .= "<th>".L('user.email')."</th>";
				$htmlContent .= "<th>".L('user.phone')."</th>";
				$htmlContent .= "<th>".L('user.position')."</th>";
				$htmlContent .= "<th>".L('user.role')."</th>";
				$htmlContent .= "<th>".L('Status')."</th>";                              
                $htmlContent .= "<th></th>";
            $htmlContent .= "</tr>";
        $htmlContent .= "</tfoot>";

        return $htmlContent;
    }	

	public static function genTableContentData() {
		$sql = Sql::select(['user', 'user'])->leftJoin(['status', 'status'], "user.status = status.id");
		$sql->setFieldValue('
		   user.id id, 
		   user.username username, 
		   user.displayName displayName, 
		   user.email email, 
		   user.phone phone,
		   user.position position, 
		   user.roleID roleID, 
		   status.name statusName                         
		');
        $stm = $sql->prepare();
        $stm->execute();
        return $stm;
    }

	public static function genTableBodyRow($listObj) {
        $htmlContent = "";
        $htmlContent .= "<tr data-id='".$listObj['id']."'>";
            $htmlContent .= "<td>".$listObj['id']."</td>";
			$htmlContent .= "<td>".$listObj['username']."</td>";
			$htmlContent .= "<td>".$listObj['displayName']."</td>";
			$htmlContent .= "<td>".$listObj['email']."</td>";
			$htmlContent .= "<td>".$listObj['phone']."</td>";
			$htmlContent .= "<td>".$listObj['position']."</td>";
            $htmlContent .= "<td>".role::find($listObj['roleID'])->name."</td>";
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
}