<?php
namespace Responses;

interface iResponse {
   public function display();
   public function objArr();
   public function json();
}