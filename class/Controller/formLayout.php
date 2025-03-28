<?php
namespace Controller;

use Responses\Message, Responses\Action, Responses\Data;
use Database\Sql;
//use Pages\Page, Pages\ListPage, Pages\FormPage;
use Routing\Route;
use Utility\WebSystem, Utility\Security;

class formLayout { 

	public static function rowInputNew(string $label, string $name, string $id, string $type, string $gridDivisorSize, Array $additonClass=[], Array $prop = [], string $displayValue="") {		
        $content = "";
		$content .= "<div class='col-md-".$gridDivisorSize." col-lg-".$gridDivisorSize."'>";
			$content .= "<div class='form-group'>";
				$content .= "<label for='".$id."'>".$label;
                if(in_array("required", $prop))
                    $content .= "*";
                $content .="</label>";   
 
                $class = "";
                foreach($additonClass as $cla) {
                    $class .= $cla." ";                    
                }
                
				$content .= "<input type='".$type."' class='form-control ".$class."' id='".$id."' name='".$name."' value='".($type=="file"?"":$displayValue)."' ";
                
                $attributes = "";
                foreach($prop as $attr) {
                    $attributes .= $attr." ";                    
                }
                $content .= $attributes; 

                $content .= ">";

                if($type=="file") 
                    if(!empty($displayValue))
                        $content .= $displayValue;

                $content .= "<small id='".$id."Help' class='form-text text-muted hintHelp'></small>";
			$content .= "</div>";
		$content .= "</div>";
        return $content;
	}

	public static function rowSelectNew(string $label, string $name, string $id, Array $options = [], string $gridDivisorSize, Array $additonClass=[], Array $prop = [], string $selectedValue="") {
        $content = "";
		$content .= "<div class='col-md-".$gridDivisorSize." col-lg-".$gridDivisorSize."'>";
			$content .= "<div class='form-group'>";
                $content .= "<label for='".$id."'>".$label;
                if(in_array("required", $prop))
                    $content .= "*";
                $content .="</label>";   
                
                $class = "";
                foreach($additonClass as $cla) {
                    $class .= $cla." ";                    
                }                

                $content .= "<select class='form-control form-select ".$class."' id='".$id."' name='".$name."' ";
                $attributes = "";
                foreach($prop as $attr) {
                    $attributes .= $attr." ";                    
                }
                $content .= $attributes;  
                $content .= ">";
                    foreach ($options as $value => $display) { 		
                        $content .= "<option value='".$value."' ";
                        if($selectedValue==$value)
                            $content .= "selected";
                        $content .=">".$display."</option>";		
                    }
                $content .= "</select>";
                $content .= "<small id='".$id."Help' class='form-text text-muted hintHelp'></small>";
			$content .= "</div>";
		$content .= "</div>";
        return $content;
	}


	public static function rowMultiSelectNew(string $label, string $name, string $id, Array $options = [], string $gridDivisorSize, Array $additonClass=[], Array $prop = [], Array $selectedValue=[]) {
        $content = "";
		$content .= "<div class='col-md-".$gridDivisorSize." col-lg-".$gridDivisorSize."'>";
			$content .= "<div class='form-group'>";
                $content .= "<label for='".$id."'>".$label;
                if(in_array("required", $prop))
                    $content .= "*";
                $content .="</label>";   
                
                $class = "";
                foreach($additonClass as $cla) {
                    $class .= $cla." ";                    
                }                

                $content .= "<select class='form-control form-select ".$class."' id='".$id."' name='".$name."' multiple ";
                $attributes = "";
                foreach($prop as $attr) {
                    $attributes .= $attr." ";                    
                }
                $content .= $attributes;  
                $content .= ">";
                    foreach ($options as $value => $display) { 		
                        $content .= "<option value='".$value."' ";
                        if(in_array($value, $selectedValue))
                            $content .= "selected";
                        $content .=">".$display."</option>";		
                    }
                $content .= "</select>";
                $content .= "<small id='".$id."Help' class='form-text text-muted hintHelp'></small>";
			$content .= "</div>";
		$content .= "</div>";
        return $content;
	}

    public static function rowRadioNew(string $label, string $name, string $id, Array $options = [], string $gridDivisorSize, Array $additonClass=[], Array $prop = [], string $selectedValue=""){
        $content = "";
		$content .= "<div class='col-md-".$gridDivisorSize." col-lg-".$gridDivisorSize."'>";  
            $content .= "<div class='form-group'>";
                $content .= "<label class='form-label'>".$label;
                if(in_array("required", $prop))
                    $content .= "*";                
                $content .= "</label>";

                $class = "";
                foreach($additonClass as $cla) {
                    $class .= $cla." ";                    
                }      

                $content .= "<div class='selectgroup w-100' id='".$id."'>";
                foreach ($options as $value => $display) { 	
                    $content .= "<label class='selectgroup-item'>";
                        $content .= "<input type='radio' name='".$name."' value='".$value."' class='selectgroup-input ".$class."' ";
                        if($selectedValue==$value)
                            $content .= "checked";
                        $content .=">";
                        $content .= "<span class='selectgroup-button'>".$display."</span>";
                    $content .= "</label>";
                }                    
                    
                $content .= "</div>";
                $content .= "<small id='".$id."Help' class='form-text text-muted hintHelp'></small>";
            $content .= "</div>";
        $content .= "</div>";
        return $content;            
    }

    public static function rowTextAreaNew(string $label, string $name, string $id, string $gridDivisorSize, Array $additonClass=[], Array $prop = [], string $displayValue="") {		
        $content = "";
		$content .= "<div class='col-md-".$gridDivisorSize." col-lg-".$gridDivisorSize."'>";
			$content .= "<div class='form-group'>";
				$content .= "<label for='".$id."'>".$label;
                if(in_array("required", $prop))
                    $content .= "*";
                $content .="</label>";    
                
                $class = "";
                foreach($additonClass as $cla) {
                    $class .= $cla." ";                    
                }     

                $content .= "<textarea class='form-control ".$class."' id='".$id."' name='".$name."' rows='5' ";
                $attributes = "";
                foreach($prop as $attr) {
                    $attributes .= $attr." ";                    
                }
                $content .= $attributes;                 
                $content .= ">".$displayValue;

                $content .= "</textarea>";

                $content .= "<small id='".$id."Help' class='form-text text-muted hintHelp'></small>";
			$content .= "</div>";
		$content .= "</div>";
        return $content;
	}

    public static function rowCheckBoxNew(string $label, string $name, string $id, string $gridDivisorSize, Array $additonClass=[], Array $prop = [], string $displayValue="") {
        $content = "";
		$content .= "<div class='col-md-".$gridDivisorSize." col-lg-".$gridDivisorSize."'>";
			$content .= "<div class='form-group'>";
                $content .= "<div class='form-check'>";
                    $class = "";
                    foreach($additonClass as $cla) {
                        $class .= $cla." ";                    
                    }                    
                    $content .= "<input class='form-check-input ".$class."' type='checkbox' value='On' id='".$id."' name='".$name."'>";
                    $content .= "<label class='form-check-label' for='".$id."'>";
                    if(in_array("required", $prop))
                        $content .= "*";
                    $content .= $label;
                    $content .= "</label>";
                $content .= "</div>";
                $content .= "<small id='".$id."Help' class='form-text text-muted hintHelp'></small>";
			$content .= "</div>";
		$content .= "</div>";  
        return $content;                 
    }


    public static function rowSeparatorLineNew($gridDivisorSize){
        $content = "";
		$content .= "<div class='col-md-".$gridDivisorSize." col-lg-".$gridDivisorSize."'>";  
            $content .= "<hr>";
        $content .= "</div>";
        return $content;         
    }

    public static function rowDisplayLineNew(string $label, string $displayValue, int $gridDivisorSize=12){

        $labelSize = "2";
        $valuieSize = "10";

        if($gridDivisorSize==6) {
            $labelSize = "2";
            $valuieSize = "4";
        } 

        $content = "";
		//$content .= "<div class='row'>";  
            $content .= "<div class='col-md-".$labelSize." col-lg-".$labelSize." my-3'>".$label.":</div>";
            $content .= "<div class='col-md-".$valuieSize." col-lg-".$valuieSize." my-3' style='border-bottom: 1px solid grey'>".$displayValue."</div>";
        //$content .= "</div>";
        return $content;         
    }

    public static function rowDisplayClearLineNew(string $label, int $gridDivisorSize=12){

        $content = "";
		//$content .= "<div class='row'>";  
            $content .= "<div class='col-md-".$gridDivisorSize." col-lg-".$gridDivisorSize." my-3'>".$label."</div>";
        //$content .= "</div>";
        return $content;         
    }    

    public static function rowDisplayClearGroupLineNew(string $label, int $gridDivisorSize=12){

        $content = "";
		
            $content .= "<div class='col-md-".$gridDivisorSize." col-lg-".$gridDivisorSize." my-3'>";
                $content .= "<div class='form-group'>";
                $content .= "<label></label>";
                $content .= $label;
                $content .= "</div>";
            $content .= "</div>";        

        return $content;         
    }   



}