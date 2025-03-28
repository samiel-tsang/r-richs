<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Pages\Page;
use Database\Sql;
use Routing\Route;
use Utility\WebSystem, Utility\PayPal, Utility\Email; 
use Controller\team, Controller\template;

class payment {
    const clientID = 'AQlBoHqgT8_-aQbo0gcZcGPTH842GFl6ncBROL6mZnursDSaNkR-6fa5y435E2GSjJNWyLT7VcwIaY2E';
    const clientSecret = 'EPIA9i72G1hslANB-5mio5TN_H5JzIUsLdNxt5A2Xd94jH63fxBTNi-lq4LZP6M7CLTynYNAJ26uCnTB';
    const env = PayPal::ENV_SANDBOX;

    public static function setPayment() {
        $conf = cfg('paypal');

        $clientID = $conf['clientID_SANDBOX'];
        $clientSecret = $conf['clientSecret_SANDBOX'];
        $env = PayPal::ENV_SANDBOX;

        if ($conf['currentEnvironment'] == PayPal::ENV_LIVE) {
            $clientID = $conf['clientID_LIVE'];
            $clientSecret = $conf['clientSecret_LIVE'];
            $env = PayPal::ENV_LIVE;
        }

        $paypal = new PayPal($clientID, $clientSecret, $env);
        $paypal->grantAccess();
        return $paypal;
    }
    // type : register / donate
    public static function createOrder($teamID, $amount, $remark, $type, $donatorID = 0) {
        $paypal = self::setPayment();

        $purchase = [[
            "reference_id" => "Team-".$teamID,
            "custom_id" => $teamID,
            "description" => "Donation",
            "soft_descriptor" => "Donation",
            "amount" => [
                "currency_code"=> "HKD",
                "value"=> number_format($amount, 2, ".", "")
            ]
        ]];
        $donate = [];
        if ($donatorID)
            $donate['donate'] = $donatorID;

        $baseUrl = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'];
        $returnUrl = $baseUrl.WebSystem::path(Route::getRouteByName('page.paymentMessage')->path(array_merge(['msgName'=>'success', 'type'=>$type], $donate)), false, false);
        $cancelUrl = $baseUrl.WebSystem::path(Route::getRouteByName('page.paymentMessage')->path(array_merge(['msgName'=>'cancel', 'type'=>$type], $donate)), false, false);

        $orderObj = $paypal->createOrder($purchase, PayPal::INTENT_CAPTURE, $returnUrl, $cancelUrl);

        $addFields = ['teamID' => "?", 'txnOrder'=>"?", 'amount'=>"?", 'remark'=>"?"];
		$addValues = [$teamID, $orderObj->id, $amount, $remark];

        $sql = Sql::insert('transaction')->setFieldValue($addFields);

        $id = 0;
		if ($sql->prepare()->execute($addValues)) {
			$id = db()->lastInsertId();
        } else 
            throw new \Exception('Database Error: Insert Transaction Failed! PayPal OrderID: '.$orderObj->id);
        
        $ret = '';
        foreach ($orderObj->links as $link) {
            if ($link->rel == 'approve' && $link->method == 'GET')
                $ret = $link->href;
        }
        return $ret;
    }

    public static function paymentSuccess($txnObj, string $payerID, string $type, $donatorID = 0) {
        $paypal = $paypal = self::setPayment();

        $capRet = $paypal->captureOrder($txnObj->txnOrder);
        $captureIDList = [];

        foreach ($capRet->purchase_units as $captureUnit) {
            $captureIDList[] = $captureUnit->payments->captures[0]->id;
        }
        $captureID = "";
        if (count($captureIDList))
            $captureID = implode(";",$captureIDList);

        $editFields = ['txnPayerID' => "?", 'txnCode' => "?", 'status' => 1, 'captureDate'=>"NOW()"];
		$editValues = [$payerID, $captureID];
        $sql = Sql::update('transaction')->setFieldValue($editFields)->where(['id', '=', $txnObj->id]);
        if (!$sql->prepare()->execute($editValues)) 
                throw new \Exception('Database Error: Update Transaction Failed! PayPal OrderID: '.$txnObj->txnOrder);

        if ($type == 'register') {
            $sql = Sql::update('team')->setFieldValue(['status' => 1])->where(['id', '=', $txnObj->teamID]);
            if (!$sql->prepare()->execute()) 
                throw new \Exception('Database Error: Update Team Failed! '.$txnObj->teamID.' PayPal OrderID: '.$txnObj->txnOrder);
    
            $teamObj = team::find($txnObj->teamID);

            // Send Welcome email
            $stmTM = Sql::select("teamMember")->where(['teamID', '=', $txnObj->teamID])->where(['status', '=', 1])->prepare();
            $stmTM->execute();  
            foreach($stmTM as $idx=>$member){  
                
                if (filter_var($member['email'], FILTER_VALIDATE_EMAIL)) {
                    try {
                        $tpl = template::findName('welcomeEmail');
                        $var = ['team_name' => $teamObj->teamName, 'member_name' => $member['nameChi']];
                        $content = template::replaceVar($tpl, $var);
                        $mail = new Email($tpl->subject);
                        $mail->addAddress($member['email'], 'Email');
                        $mail->setHTMLBody($content);
                        $mail->send();
                    } catch (Exception $e) {
                        throw new \Exception('Email Error: Send Email Failed! '.$txnObj->teamID.'-'.$member['id'].' PayPal OrderID: '.$txnObj->txnOrder);
                    } 
                }
            }

        } else if ($type == 'donate') {
            $sql = Sql::update('teamDonator')->setFieldValue(['status' => 1])->where(['id', '=', $donatorID]);
            if (!$sql->prepare()->execute()) 
                throw new \Exception('Database Error: Update Team Donator Failed! '.$donatorID.' PayPal OrderID: '.$txnObj->txnOrder);
        }
    }

    public static function paymentCancel($txnObj, string $type, $donatorID = 0) {
        $sql = Sql::delete('transaction')->where(['id', '=', $txnObj->id]);
        if (!$sql->prepare()->execute()) 
            throw new \Exception('Database Error: Delete Transaction Failed! PayPal OrderID: '.$txnObj->txnOrder);
        if ($type == 'register') {
            $sql = Sql::delete('team')->where(['id', '=', $txnObj->teamID]);
            if (!$sql->prepare()->execute()) 
                throw new \Exception('Database Error: Delete Team Failed! '.$txnObj->teamID.' PayPal OrderID: '.$txnObj->txnOrder);
            $sql = Sql::delete('teamMember')->where(['teamID', '=', $txnObj->teamID]);
            if (!$sql->prepare()->execute()) 
                throw new \Exception('Database Error: Delete Team Member Failed! '.$txnObj->teamID.' PayPal OrderID: '.$txnObj->txnOrder);
            $sql = Sql::delete('teamDonator')->where(['teamID', '=', $txnObj->teamID]);
            if (!$sql->prepare()->execute()) 
                throw new \Exception('Database Error: Delete Team Donator Failed! '.$txnObj->teamID.' PayPal OrderID: '.$txnObj->txnOrder);

        } else if ($type == 'donate') {
            $sql = Sql::delete('teamDonator')->where(['id', '=', $donatorID]);
            if (!$sql->prepare()->execute()) 
                throw new \Exception('Database Error: Delete Team Donator Failed! '.$donatorID.' PayPal OrderID: '.$txnObj->txnOrder);

        }
    }

    public function message($request) {
        if (!isset($request->get->token) || empty($request->get->token)) 
			return new Message('alert', L('error.paypalEmptyToken'));
        if (!isset($request->get->type) || empty($request->get->type)) 
			return new Message('alert', L('error.msgType'));

        $donatorID = $request->get->donate ?? 0;
        $returnUrl = '';
        if ($request->get->type == 'register') {
            $returnUrl = WebSystem::path(Route::getRouteByName('page.userRegistration')->path(), false, false);
        } else if ($request->get->type == 'donate') {
            $returnUrl = WebSystem::path(Route::getRouteByName('page.dashboard')->path(), false, false);
        }

        $txnObj = transaction::findByOrderID($request->get->token);
        if (is_null($txnObj))
            throw new \Exception('Database Error: Transaction not found! PayPal OrderID: '.$request->get->token);
        if ($txnObj->status == 1)
            $request->get->msgName = ''; // transaction is completed

        switch ($request->get->msgName) {
            case 'success':
                $header = 'txnMsg.headerSuccess';
                $msg = 'txnMsg.msgSuccess';
                self::paymentSuccess($txnObj, $request->get->PayerID, $request->get->type, $donatorID);
                break;
            case 'cancel':
                $header = 'txnMsg.headerCancel';
                $msg = 'txnMsg.msgCancel';
                self::paymentCancel($txnObj, $request->get->type, $donatorID);
                break;
            default:
                $header = 'txnMsg.headerFail';
                $msg = 'txnMsg.msgFail';
        }
        return new Page('transaction/message', ['header'=>L($header), 'message'=>L($msg), 'returnUrl'=>$returnUrl]);
    }

    public function test($request) {
        $clientID = 'AQlBoHqgT8_-aQbo0gcZcGPTH842GFl6ncBROL6mZnursDSaNkR-6fa5y435E2GSjJNWyLT7VcwIaY2E';
        $clientSecret = 'EPIA9i72G1hslANB-5mio5TN_H5JzIUsLdNxt5A2Xd94jH63fxBTNi-lq4LZP6M7CLTynYNAJ26uCnTB';
        $env = PayPal::ENV_SANDBOX;
        $paypal = new PayPal($clientID, $clientSecret, $env);

        $paypal->grantAccess();
        var_dump($paypal->isGranted());

        $purchase = [
            [
                "reference_id"=> "PUHF",
                "amount"=> [
                    "currency_code"=> "USD",
                    "value"=> "100.00"
                ]
            ],
/*
            [
                "reference_id"=> "PUHF1",
                "amount"=> [
                    "currency_code"=> "USD",
                    "value"=> "100.00"
                ]
            ]
*/
        ];
        echo "<pre>";

        $baseUrl = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'];
        $returnUrl = $baseUrl.WebSystem::path(Route::getRouteByName('page.paymentTester')->path(), false, false);
        $cancelUrl = $baseUrl.WebSystem::path(Route::getRouteByName('page.login')->path(), false, false);
//        print_r($paypal->createOrder($purchase, PayPal::INTENT_CAPTURE, $returnUrl, $cancelUrl));

        
//        print_r($paypal->showOrderDetails('5ND77200237229628'));
//        print_r($paypal->showOrderDetails('1F349572FA976444W'));
//        print_r($paypal->showOrderDetails('5LF63288S9164284P'));

        //print_r($paypal->updateOrder('5LF63288S9164284P', 'add', "/purchase_units/@reference_id=='PUHF'/description", 'Donation'));
        // can't re-capture Order, ORDER_ALREADY_CAPTURED Error
     
        $capRet = $paypal->captureOrder($request->get->token);
        $captureIDList = [];

        print_r($capRet->purchase_units);

        foreach ($capRet->purchase_units as $captureUnit) {
            $captureIDList[] = $captureUnit->payments->captures[0]->id;
        }

        print_r($captureIDList);

        echo ($captureIDList[0]);
        //print_r($paypal->captureOrder('5R912407N5405980K')); 

//        print_r($paypal->showOrderDetails('5R912407N5405980K'));
        echo "</pre>";
    }

}