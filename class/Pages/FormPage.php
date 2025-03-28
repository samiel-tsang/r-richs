<?php
namespace Pages;

use Requests\Request;
use Database\Sql, Database\Listable;
use Routing\Route;
use Utility\WebSystem;

class FormPage extends Page {
	
	protected $obj;
	protected $hasContainer;
	
	public function __construct(String $view, $obj, Array $vals = []) {
		parent::__construct($view, $vals);
		$this->obj = $obj;
		$hasContainer = false;
	}
	
	public function setHasContainer($hasContainer = false) { $this->hasContainer = $hasContainer; }
	
	public function input(string $label, string $name, string $id, string $type, string $prop="", string $value="") {
		$fieldValue = '';
		if (!empty($value)) {
			$fieldValue = $value;
		} else if (isset($this->obj) && isset($this->obj[$name])) {
			$fieldValue = $this->obj[$name];
		}
?>
	<!--<div class="form-group row mx-0">-->
        <label for="<?=$id;?>" class="col-md-2 col-form-label"><?=$label;?></label>
<?php
if ($this->hasContainer) {
?>
        <div class="col-md-10 p-0"><input type="<?=$type;?>" class="form-control" id="<?=$id;?>" name="<?=$name;?>" value="<?=$fieldValue;?>" <?=$prop;?>></div>
<?php
} else { ?>
        <input type="<?=$type;?>" class="form-control col-md-8" id="<?=$id;?>" name="<?=$name;?>" value="<?=$fieldValue;?>" <?=$prop;?>>
<?php } ?>
	<!--</div>-->
<?php		
	}

	/* added from 16/9/2021 */
	public function halfRowInput(string $label, string $name, string $id, string $type, string $prop="", string $value="") {
		$fieldValue = '';
		if (!empty($value)) {
			$fieldValue = $value;
		} else if (isset($this->obj) && isset($this->obj[$name])) {
			$fieldValue = $this->obj[$name];
		}
?>
	<!--<div class="form-group row mx-0">-->
        <label for="<?=$id;?>" class="col-md-2 col-form-label"><?=$label;?></label>
<?php
if ($this->hasContainer) {
?>
        <div class="col-md-4 p-0"><input type="<?=$type;?>" class="form-control" id="<?=$id;?>" name="<?=$name;?>" value="<?=$fieldValue;?>" <?=$prop;?>></div>
<?php
} else { ?>
        <input type="<?=$type;?>" class="form-control col-md-4" id="<?=$id;?>" name="<?=$name;?>" value="<?=$fieldValue;?>" <?=$prop;?>>
<?php } ?>
	<!--</div>-->
<?php		
	}


	/* added from 18/2/2025 for new layout */	
	public function password(string $label, string $name, string $id, string $value, string $prop="") {
?>
	<div class="form-group row mx-0">
        <label for="<?=$id;?>" class="col-md-4 p-0 px-md-3"><?=$label;?></label>
<?php
if ($this->hasContainer) {
?>
        <div class="col-md-8 p-0"><input type="password" class="form-control" id="<?=$id;?>" name="<?=$name;?>" value="" <?=$prop;?>></div>
<?php
} else { ?>
		<input type="password" class="form-control col-md-8" id="<?=$id;?>" name="<?=$name;?>" value="" <?=$prop;?>>
<?php } ?>
	</div>
<?php		
	}

	/* added from 16/9/2021 */
	public function halfRowpassword(string $label, string $name, string $id, string $value, string $prop="") {
		?>
			<!--<div class="form-group row mx-0">-->
				<label for="<?=$id;?>" class="col-md-2 col-form-label"><?=$label;?></label>
		<?php
		if ($this->hasContainer) {
		?>
				<div class="col-md-4 p-0"><input type="password" class="form-control" id="<?=$id;?>" name="<?=$name;?>" value="" <?=$prop;?>></div>
		<?php
		} else { ?>
				<input type="password" class="form-control col-md-4" id="<?=$id;?>" name="<?=$name;?>" value="" <?=$prop;?>>
		<?php } ?>
			<!--</div>-->
		<?php		
	}
	
	public function select(string $label, string $name, string $id, Array $options = [], string $prop="", Array $selected = []) {
?>
    <div class="form-group row mx-0">
        <label for="<?=$id;?>" class="col-md-4 p-0 px-md-3"><?=$label;?></label>
<?php
if ($this->hasContainer) {
?>
        <div class="col-md-8 p-0"><select class="form-control" id="<?=$id;?>" name="<?=$name;?>" <?=$prop;?>>
<?php
} else { ?>
		<select class="form-control col-md-8" id="<?=$id;?>" name="<?=$name;?>" <?=$prop;?>>
<?php } ?>
<?php
		foreach ($options as $value => $display) { 
			$hasSelected = false;
			if (in_array($value, $selected)) $hasSelected = true;
			else if (isset($this->obj) && isset($this->obj[$name]) && $this->obj[$name] == $value) $hasSelected = true;
?>
            <option value="<?=$value;?>"<?=($hasSelected)?" selected":""; ?>><?=$display;?></option>
<?php 	} ?>
        </select><?=($this->hasContainer)?'</div>':'';?>
    </div>
<?php		
	}

	/* added from 16/9/2021 */
	public function halfRowSelect(string $label, string $name, string $id, Array $options = [], string $prop="", Array $selected = []) {
		?>
			<!--<div class="form-group row mx-0">-->
				<label for="<?=$id;?>" class="col-md-2 col-form-label"><?=$label;?></label>
		<?php
		if ($this->hasContainer) {
		?>
				<div class="col-md-4 p-0"><select class="form-control" id="<?=$id;?>" name="<?=$name;?>" <?=$prop;?>>
		<?php
		} else { ?>
				<select class="form-control col-md-4" id="<?=$id;?>" name="<?=$name;?>" <?=$prop;?>>
		<?php } ?>
		<?php
				foreach ($options as $value => $display) { 
					$hasSelected = false;
					if (in_array($value, $selected)) $hasSelected = true;
					else if (isset($this->obj) && isset($this->obj[$name]) && $this->obj[$name] == $value) $hasSelected = true;
		?>
					<option value="<?=$value;?>"<?=($hasSelected)?" selected":""; ?>><?=$display;?></option>
		<?php 	} ?>
				</select><?=($this->hasContainer)?'</div>':'';?>
			<!--</div>-->
		<?php		
	}



// need change for support (radio, inline display, no checkbox, etc)
	public function checkbox(string $label, string $name, string $id, string $type = "checkbox", Array $options = [], string $prop="", bool $isInline = false, Array $checked=[]) {
?>
    <div class="form-group row mx-0">
        <label for="<?=$id;?>" class="col-md-4 p-0 px-md-3"><?=$label;?></label>
        <div class="col-md-8">
<?php	
	$i = 1;
	foreach ($options as $value => $display) { 
		$isChecked = false;
		if ((count($checked) && in_array($value, $checked)) ||
			(isset($this->obj) && isset($this->obj[$name]) && $this->obj[$name] == $value)) $isChecked = true;
?>
	<div class="form-check<?=($isInline)?' form-check-inline':''; ?>">
            <input class="form-check-input" type="<?=$type;?>" id="<?=$id;?>_opt<?=$i;?>" name="<?=$name;?>" <?=$prop;?> value="<?=$value;?>"<?=($isChecked)?" checked":""; ?>>
            <label class="form-check-label" for="<?=$id;?>_opt<?=$i;?>"><?=$display;?></label>
	</div>
<?php
		$i++;
 	} 
?>
        </div>
    </div>
<?php		
	}

	/* added from 19/10/2021 Stanley */
	public function halfRowCheckBox(string $label, string $name, string $id, string $type = "checkbox", Array $options = [], string $prop="", bool $isInline = false, Array $checked=[]) {
		?>
			<!--<div class="form-group row mx-0">-->
				<label for="<?=$id;?>" class="col-md-2 p-0 px-md-3"><?=$label;?></label>
				<div class="col-md-4">
		<?php	
			$i = 1;
			foreach ($options as $value => $display) { 
				$isChecked = false;
				if ((count($checked) && in_array($value, $checked)) ||
					(isset($this->obj) && isset($this->obj[$name]) && $this->obj[$name] == $value)) $isChecked = true;
		?>
			<div class="form-check<?=($isInline)?' form-check-inline':''; ?>">
					<input class="form-check-input" type="<?=$type;?>" id="<?=$id;?>_opt<?=$i;?>" name="<?=$name;?>" <?=$prop;?> value="<?=$value;?>"<?=($isChecked)?" checked":""; ?>>
					<label class="form-check-label" for="<?=$id;?>_opt<?=$i;?>"><?=$display;?></label>
			</div>
		<?php
				$i++;
			 } 
		?>
				</div>
			<!--</div>-->
		<?php		
			}
	
	public function fileselect(string $label, string $name, string $id, string $prop="") {
?>
    <div class="form-group row mx-0">
        <label for="<?=$id;?>Label" class="col-md-4 p-0 px-md-3"><?=$label;?></label>
        <div class="custom-file col-md-8 px-0">
			<input type="file" class="form-control" id="<?=$id;?>" name="<?=$name;?>" <?=$prop;?>>
			<label class="custom-file-label text-left" for="<?=$id;?>">Choose file</label>
		</div>
    </div>
<?php		
	}
	
	/* added from 16/9/2021 */
	public function halfRowFileSelect(string $label, string $name, string $id, string $prop="") {
		?>
			<!--<div class="form-group row mx-0">-->
				<label for="<?=$id;?>Label" class="col-md-2 col-form-label"><?=$label;?></label>
				<div class="custom-file col-md-4 px-0">
					<input type="file" class="form-control" id="<?=$id;?>" name="<?=$name;?>" <?=$prop;?>>
					<!--<label class="custom-file-label text-left" for="<?=$id;?>">Choose file</label>-->
				</div>
			<!--</div>-->
		<?php		
	}


	public function textarea(string $label, string $name, string $id, string $prop="", string $value="") {
?>
	<!--<div class="form-group row mx-0">-->
		<label for="<?=$id;?>Label" class="col-md-2 col-form-label"><?=$label;?></label>
		
		<?php
if ($this->hasContainer) {
?>
        <div class="col-md-10 p-0"><textarea class="form-control" id="<?=$id;?>" name="<?=$name;?>" <?=$prop;?>><?=$value;?></textarea></div>
<?php
} else { ?>
        <textarea class="form-control" id="<?=$id;?>" <?=$prop;?>><?=$value;?></textarea>
<?php } ?>
	<!--</div>-->
<?php		
	}

			
	/* added from 7/5/2022 */
	public function halfRowTextArea(string $label, string $name, string $id, string $prop="", string $value="") {
		?>
			<!--<div class="form-group row mx-0">-->
				<label for="<?=$id;?>Label" class="col-md-2 col-form-label"><?=$label;?></label>
				<div class="col-md-4 px-0">
					<textarea class="form-control" id="<?=$id;?>" name="<?=$name;?>" <?=$prop;?>><?=$value;?></textarea>	
				</div>
			<!--</div>-->
		<?php		
	}


	public function submit(string $lblSubmit="Submit", string $lblReset="") {
?>
    <div class="form-group row mx-0">
        <div class="offset-md-4">
            <button type="submit" class="btn btn-primary"><?=$lblSubmit;?></button>
<?php if (!empty($lblReset)) { ?>
            <button type="reset"  class="btn btn-secondary"><?=$lblReset;?></button>
<?php } ?>
        </div>
    </div>
<?php		
	}

	/* added from 16/9/2021 */
	public function rowSubmit(string $lblSubmit="Submit", string $lblReset="") {
		?>
			<div class="form-group row mx-0">
				<div class="col-md-12 text-center">
					<button type="submit" class="btn btn-primary"><?=$lblSubmit;?></button>
		<?php if (!empty($lblReset)) { ?>
					<button type="reset"  class="btn btn-secondary"><?=$lblReset;?></button>
		<?php } ?>
				</div>
			</div>
		<?php		
	}
}