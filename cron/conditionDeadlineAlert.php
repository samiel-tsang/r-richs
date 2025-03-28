<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include_once('/home/superta1/public_html/pms/inc/global.php');
include_once("/home/superta1/public_html/pms/config/route.php");

$data = new \stdClass;

/*
$data->emailTemplateID = 4;
$data->recipientEmail = "samieltsang@hotmail.com";
$data->recipientName = "samiel tsang";
$data->TPBNo = "TEST11111111111111111".time();
*/

$currentHour = date("H", time()+8*3600);
$presetHour = Controller\systemSetting::findMetaValue("autoEmailHour")->metaValue??"00";

if($currentHour==$presetHour) {

    $emailTemplateID = Controller\systemSetting::findMetaValue("alertEmailTemplateID")->metaValue??1;

    $sql = Database\Sql::select(['tpbCondition', 'conditions'])->leftJoin(['tpb', 'tpb'], "conditions.tpbID = tpb.id");
    
    $sql->setFieldValue('
       conditions.id id, 
       conditions.tpbID tpbID, 
       tpb.TPBNo TPBNo,
       tpb.submissionDate submissionDate, 
       tpb.TPBWebsite TPBWebsite, 
       tpb.TPBReceiveDate TPBReceiveDate,
       tpb.addressDDLot addressDDLot,
       tpb.OZPName OZPName,
       tpb.OZPNo OZPNo,
       tpb.remarks remarks,
       conditions.conditionNo ,
       conditions.description conditionDescription,
       conditions.deadline conditionDeadline,       
       conditions.status conditionStatus           
    ');    

    $sql->where(['conditions.status', '=', 1]);
    $sql->where(['conditions.deadline', '=', 'DATE_ADD(CURDATE(), INTERVAL 2 MONTH)']);

    $stm = $sql->prepare();
    $stm->execute();
    
    foreach($stm as $condition) {
        $officerList = Controller\tpb::findOfficer($condition['tpbID']);
        
        foreach($officerList as $officer) {
            $offcerInfo = Controller\user::find($officer['userID']);
            
            $data = (object) [
                "TPBNo" => $condition['TPBNo'],
                "submissionDate"=>$condition['submissionDate'],
                "submissionDate"=>$condition['submissionDate'],
                "TPBWebsite"=>$condition['TPBWebsite'],
                "TPBReceiveDate"=>$condition['TPBReceiveDate'],
                "addressDDLot"=>$condition['addressDDLot'],
                "OZPName"=>$condition['OZPName'],
                "OZPNo"=>$condition['OZPNo'],
                "remarks"=>$condition['remarks'],
                "conditionNo"=>$condition['conditionNo'],
                "conditionDescription"=>$condition['conditionDescription'],
                "conditionDeadline"=>$condition['conditionDeadline'],
                "emailTemplateID"=>$emailTemplateID,
                "recipientEmail"=>$offcerInfo->email,
                "recipientName"=>$offcerInfo->displayName,
                "officerDisplayName"=>$offcerInfo->email,
                "officerEmail"=>$offcerInfo->displayName
            ];

            $result = Controller\notification::sendEmail($data);

            var_dump($result);

        }        
        
    }
    
}


?>