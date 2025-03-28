<?php
namespace Responses;

class Message implements iResponse {
   private $time;
   private $type;
   private $msg;

   public function __construct($type, $msg) {
      $this->type = $type;
      $this->msg = $msg;
      $this->time = date(DATE_RSS);
   }

   public function getMessage() { return $this->msg; }
   public function getType() { return $this->type; }

   public function display() {
      echo "(".$this->time.") Message[".$this->type."]: ".$this->msg;
   }

   public function objArr() {
      return array('objectType'=>'message', 'msg'=>$this->msg, 'type'=>$this->type, 'time'=>$this->time);
   }

   public function json() {
      return json_encode($this->objArr());
   }
}