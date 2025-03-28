<?php

use Controller\user, Controller\team;
use Database\Sql;

$cssPath = array("css/signin.css");
$bodyClass = "bg-light";
include("view/layout/meta.php");

$hash = $_GET['hashCode'];
$valid = true;
$errorMessage = "";

if(empty($hash)){
    $errorMessage = L('error.invalidLink');
    $valid = false;
} else {
    $sql = Sql::select("forgetPassword")->where(['hashCode', '=',"'".$hash."'"])->order("id", "DESC")->limit(1);
    $stm = $sql->prepare();
    $stm->execute();
    $obj = $stm->fetch($fetchMode);
    if ($obj === false) {
        $errorMessage = L('error.invalidLink');
        $valid = false;
    } else {       
        if($obj['status']==2) {
            $errorMessage = L('error.invalidLink');
            $valid = false;
        } elseif (time()>strtotime($obj['expiry_date'])) {
            $errorMessage = L('error.expiredLink');
            $valid = false;
        } else {            
            $userObj = user::find($obj['userID']);           
        }        
    }
}

?>
<form class="form-signin shadow-lg needs-validation" id="form-regUser" novalidate autocomplete="off">
  <div class="py-5 text-center">    
    <h2><?= L('login.changePassword'); ?></h2>
  </div>
  <div id="message" class='h5 text-primary text-center' style="display:<?=$valid?'none':'block';?>"><?=$errorMessage;?></div>
  <div class="card" style="width: 100%; display:<?=$valid?'block':'none';?>">
    <div class="card-body">      
      
      <div class="row">
        <div class="col-12 col-md-6">
         <label for="userName" class="form-label"><?= L('user.userName'); ?></label>        
         <input type="text" class="form-control" name="userName" id="userName" placeholder="<?= L('user.userName'); ?>" value="<?=$userObj->username;?>" readonly> 
        </div>
        <div class="col-12 col-md-6">
         <label for="userEmail" class="form-label"><?= L('user.email'); ?></label>        
         <input type="text" class="form-control" name="userEmail" id="userEmail" placeholder="<?= L('user.email'); ?>" value="<?=$userObj->email;?>"readonly> 
        </div>        
      </div>
    
      <div class="row">
        <div class="col-12 col-md-6">
         <label for="password" class="form-label"><?= L('user.password'); ?> *</label>
          <input type="password" class="form-control" name="password" id="password" placeholder="<?= L('user.password'); ?>" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
          <div class="invalid-feedback"><?= L('error.userInvalidPasswordFormat'); ?></div>
        </div>
        <div class="col-12 col-md-6">
        <label for="cnpassword" class="form-label"><?= L('user.confirmPassword'); ?> *</label>
          <input type="password" class="form-control" name="cfmPassword" id="cfmPassword" placeholder="<?= L('user.confirmPassword'); ?>" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
          <div class="invalid-feedback"><?= L('error.userInvalidPasswordFormat'); ?></div>          
        </div>
      </div>
    </div>
  </div>

  

  <div class="row g-2" id="btnSubmitArea" style="display:<?=$valid?'block':'none';?>">   
    <div class="col-12 rowBtn"><button class="w-100 btn btn-lg btn-primary" id="btnSubmit" type="submit" <?=$valid?'':'disabled';?>><?= L("team.submit"); ?></button></div>
  </div>

  <footer class="footer text-muted text-center text-small" style="margin-top:20px;">
    <p class="">&copy; <?=date("Y");?> Supertainment</p>
    <span class="text-info"><i class="fas fa-language fa-lg"></i><?php foreach (Pages\Language::getAvailableLang() as $lang) : ?>
        <a href="<?php Utility\WebSystem::path("lang/" . $lang['langCode']); ?>" class="p-1 text-info"><?= $lang['langName']; ?></a>
      <?php endforeach ?></span>
  </footer>

</form>

<?php
include("view/layout/js.php");
?>
<script>    

  $('#form-regUser').submit(function(e) {
    e.preventDefault();

    this.classList.add('was-validated');
    if (!this.checkValidity()) return;
    
    var formData = new FormData(this);
    ajaxFunc.apiCall("POST", "user/resetpassword/<?=$userObj->id;?>", formData, "multipart/form-data", function (data) {           
        if(data.type=="info") {
            $(".card").hide();
            $("#btnSubmitArea").hide();
        }
        $("#message").html(data.msg);
        $("#message").show();        

    });
    
  });
</script>
<?php
include("view/layout/endpage.php");
?>