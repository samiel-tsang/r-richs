<?php
namespace Utility;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email {
   public $mailer;

   public function __construct($subject = '') {
      $this->mailer = new PHPMailer(true);

      $this->setupMailer();

      $this->Subject = $subject;
   }

   public function __call($name, $arguments) {
      return call_user_func_array([$this->mailer, $name], $arguments);
   }

   public function __set($name, $value) {
      $this->mailer->$name = $value;
   }

   public function setupMailer(array $config = []) {
      $defaultConfig = cfg('phpmailer');
      if (!empty($defaultConfig)) {
         if (isset($defaultConfig['server'])) {
            $this->setMailer($defaultConfig['server']);
         }
         if (isset($defaultConfig['recipients'])) {
            $this->setMailer($defaultConfig['recipients'], 'setMailerFunction');
         }
      }
      if (is_array($config) && count($config)) {
         $this->setMailer($config);
      }

   }

   private function setMailer(array $config, $function = 'setMailerSetting') {
      if (!is_array($config)) return false;
      foreach($config as $name => $value) {
         $this->$function($name, $value);
      }
      return true;
   }

   public function setMailerSetting($name, $value, $isFunction = false) {
      if ($name == 'Mailer') {
         switch ($value) {
            case 'smtp':
		$name = 'isSMTP'; $value = ''; $isFunction = true; break;
            case 'sendmail':
		$name = 'isSendmail'; $value = ''; $isFunction = true; break;
            case 'qmail':
		$name = 'isQmail'; $value = ''; $isFunction = true; break;
            case 'mail': 
            default:
		$name = 'isMail'; $value = ''; $isFunction = true;
         }
      }

      if ($isFunction)
         $this->$name($value);
      else
         $this->$name = $value;
   }

   public function setMailerFunction($name, array $arguments = []) {
      return call_user_func_array([$this->mailer, $name], array_values($arguments));
   }

   // This Function should be no use
   public function setUTF8Subject($subject) {
      if (strtolower($this->CharSet) == PHPMailer::CHARSET_UTF8) {
         $this->Subject = '=?utf-8?B?'.base64_encode($subject).'?=';
      }
   }

   public function setHTMLBody($bodyStr) {
      $this->isHTML(true);
      $this->msgHTML($bodyStr);
   }
}