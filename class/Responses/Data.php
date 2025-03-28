<?php
namespace Responses;

class Data implements iResponse {
   private $content;
   private $time;

   public function __construct($data) {
      $this->content = $data;
      $this->time = date(DATE_RSS);
   }

   public function display() {
      echo "(".$this->time.") Data: ".$this->json();
   }

   public function objArr() {
      return array('objectType'=>'data', 'content'=>$this->content);
   }

   public function json() {
      return json_encode($this->objArr());
   }
}