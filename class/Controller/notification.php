<?php
namespace Controller;
use Responses\Message, Responses\Action, Responses\Data;
use Pages\Page, Pages\ListPage, Pages\FormPage;
use Database\Sql; 
use Utility\WebSystem, Utility\Excel, Utility\Email; 
use Controller\tpb;

class notification {

	public static function sendEmail($data){	
		
		try {

			$var = [						
				'tpb_no' => $data->TPBNo??"",
				'tpb_submission_date' => $data->submissionDate??"",
				'tpb_website' => $data->TPBWebsite??"",
				'tpb_receive_date' => $data->TPBReceiveDate??"",
				'tpb_addressDDLot' => $data->addressDDLot??"",
				'tpb_OZP_Name' => $data->OZPName??"",
				'tpb_OZP_No' => $data->OZPNo??"",
				'tpb_remarks' => $data->remarks??"",
				'condition_no' => $data->conditionNo??"",
				'condition_description' => $data->conditionDescription??"",
				'condition_deadline' => $data->conditionDeadline??"",
				'officer_displayName' => $data->officerDisplayName??"Officer",
				'officer_email' => $data->officerEmail??""
			];
			
			$subject = emailTemplate::replaceVar(emailTemplate::find($data->emailTemplateID)->subject, $var);
			$content = emailTemplate::replaceVar(emailTemplate::find($data->emailTemplateID)->content, $var);                    

			$smtpHost = 'smtp.mailgun.org'; 
			$smtpUserName = 'assistant@ecbq.hk';  // SMTP username
			$smtpPassword = '92acf8cca04dadad33a5fbb925585b9e-69a6bd85-c839b56d';   
			$smtpSecure = 'tls';  
			$senderEmail = 'assistant@ecbq.hk';  
			$senderName = $senderEmail;

			/*
			$smtpDefaultObj = organization::getOrganizationMeta($organizationObj->id, "smtpDefault");
			if(!is_null($smtpDefaultObj) && $smtpDefaultObj->meta_value!="Y"){

				$smtpServerObj = organization::getOrganizationMeta($organizationObj->id, "smtpServer");
				$smtpUserNameObj = organization::getOrganizationMeta($organizationObj->id, "smtpUserName");
				$smtpPasswordObj = organization::getOrganizationMeta($organizationObj->id, "smtpPassword");
				$smtpEncrptionTypeObj = organization::getOrganizationMeta($organizationObj->id, "smtpEncrptionType");

				$smtpHost = $smtpServerObj->meta_value;
				$smtpUserName = $smtpUserNameObj->meta_value;
				$smtpPassword = $smtpPasswordObj->meta_value;
				$smtpSecure = $smtpEncrptionTypeObj->meta_value;
			}

			if($eventObj->notificationSenderDisplayName!="") {
				$senderName = $eventObj->notificationSenderDisplayName;
			}

			if($eventObj->notificationSenderEmail!="") {
				$senderEmail = $eventObj->notificationSenderEmail;
			}		
			*/

			$mail = new Email($subject);
			$mail->isSMTP();    
			$mail->SMTPDebug = false;                                  // Set mailer to use SMTP
			$mail->Host = $smtpHost;                     // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = $smtpUserName;  // SMTP username
			$mail->Password = $smtpPassword;               // SMTP password
			$mail->SMTPSecure = $smtpSecure;                            // Enable encryption, only 'tls' is accepted			
			$mail->From = $senderEmail;
			$mail->FromName = $senderName;				
			$mail->addAddress($data->recipientEmail, $data->recipientName);	
			$mail->setHTMLBody($content);			
			$mail->SetFrom($senderEmail, $senderName);
			$mail->AddReplyTo($senderEmail, $senderName);	

			$result = $mail->send();
						
			/*
			$addFields = [
				'eventID' => "?",
				'subEventID' => "?",
				'guestID' => "?",	
				'eventGuestID' => "?",			
				'subject' => "?",
				'receipient' => "?",
				'email' => "?",
				'content' => "?",
				'notificationTemplateID' => "?",
				'type' => "?"
			];
	
			$addValues = [
				strip_tags($eventObj->id),
				strip_tags($guestObj->subEventID),
				strip_tags($guestObj->guestID),
				strip_tags($guestObj->id),
				strip_tags($subject), 
				strip_tags($guestObj->name), 
				strip_tags($guestObj->email), 
				$content, 
				$notificationTemplateID, 
				$typeString
			];	
						
			$sql = Sql::insert('emailLog')->setFieldValue($addFields);			
			$sql->prepare()->execute($addValues);
			*/
			return $result;

		} catch (\PHPMailer\PHPMailer\Exception $e) {	
			return false;
			//return new Data(['success'=>false, 'message'=>L('error.unableSendEmail')]);	
		}

	}

}