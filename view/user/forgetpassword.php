<?php
use Responses\Message, Responses\Action, Responses\Data;
use Controller\category;
use Controller\period;
use Controller\defaultAvatar;

$cssPath = array("css/signin.css");
$bodyClass = "bg-light";
include("view/layout/meta.php");
?>
<form class="form-signin shadow-lg needs-validation" id="form-regUser" novalidate autocomplete="off">
  <div class="py-5 text-center">    
    <h2><?= L('login.forgetPassword'); ?></h2>
  </div>
  <div id="promptMsg"></div>
  <div class="card" style="width: 100%;">
    <div class="card-body">      
      <div class="row">
        <div class="col-12 col-md-6">
         <label for="email" class="form-label"><?= L('user.email'); ?> *</label>
          
         <div class="input-group mb-3">
          <input type="email" class="form-control" name="email" id="email" placeholder="<?= L('user.email'); ?>" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required>
          
          <div class="input-group-append">
          <button class="btn btn-primary" type="submit"><?= L("team.submit"); ?></button>
          </div>
          <div class="invalid-feedback"><?= L('error.userEmptyEmail'); ?></div>
        </div>
        
         
      <!--   <input type="email" class="form-control" name="email" id="email" placeholder="<?= L('user.email'); ?>" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required>
          <div class="invalid-feedback"><?= L('error.userEmptyEmail'); ?></div>          -->
        </div>
        <div class="col-12 col-md-6">          
          <label for="email" class="form-label">&nbsp;</label>
          <span id='message' class='text-primary' style="display:block"></span>          
        </div>
      </div>
    </div>
  </div>

<!--
  <div class="row g-2">
    <div class="col-6 rowBtn"><a class="w-100 btn btn-lg btn-secondary" href="<?php Utility\WebSystem::path($request->referer(Requests\Request::REFERER_QUERY), true, false); ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?= L('Back'); ?>"><i class="fas fa-1x fa-angle-left"></i><span class="d-none d-md-inline"> <?= L('Back'); ?></span></a></div>
    <div class="col-12 rowBtn"><button class="w-100 btn btn-lg btn-primary" type="submit"><?= L("team.submit"); ?></button></div>
  </div>
-->
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
    $("#message").html('<i class="fas fa-spinner fa-spin"></i>'); 
    this.classList.add('was-validated');
    if (!this.checkValidity()) {     
      $("#message").html('');
      return;
    }
   
    var formData = new FormData(this);

    ajaxFunc.apiCall("POST", "user/forget", formData, "multipart/form-data", function (data) {   
      $("#message").text(data.content.message);      
    });

  });
</script>
<?php
include("view/layout/endpage.php");
?>