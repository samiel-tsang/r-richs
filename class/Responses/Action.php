<?php
namespace Responses;

class Action implements iResponse {
   private $time;
   private $action;
   private $script;

   public function __construct($action, $script) {
      $this->script = $script;
      $this->action = $action;
      $this->time = date(DATE_RSS);
   }

   public function getAction() { return $this->action; }
   public function getScript() { return $this->script; }

   public function display() {
      echo "(".$this->time.") Action[".$this->action."]: ";
      if (is_array($this->script)) echo json_encode($this->script);
      else echo $this->script;
   }

   public function objArr() {
      return array('objectType'=>'action', 'action'=>$this->action, 'script'=>$this->script, 'time'=>$this->time);
   }

   public function json() {
      return json_encode($this->objArr());
   }
}