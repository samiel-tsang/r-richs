<?php
namespace Responses;

class MultipleResponse implements iResponse {
   private $time;
   private $actArray;

   public function __construct($actArray) {
//      $this->actArray = array_merge(array(), $actArray);
      $this->actArray = $actArray;
      $this->time = date(DATE_RSS);
   }

   public function getObject($index) { return $this->actArray[$index]; }

   public function display() {
      echo "(".$this->time.") Multiple Responses: ".PHP_EOL;
      foreach ($this->actArray as $action) {
         $action->display().PHP_EOL;
      }
   }

   public function objArr() {
      $actArr = array();
      foreach ($this->actArray as $action) {
         $actArr[] = $action->objArr();
      }
      return array('objectType'=>'multiResp', 'actions'=>$actArr, 'time'=>$this->time);
   }

   public function json() {
      return json_encode($this->objArr());
   }
}