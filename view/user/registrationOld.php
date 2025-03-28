<?php
$bodyClass= "bg-light";
include("view/layout/meta.php");
?>
<div class="container">
  <main>
    <div class="py-5 text-center">
      <img class="d-block mx-auto mb-4" src="https://www.bizwave.hk/wp-content/uploads/2022/02/bizwave-logo.png" width="72">
      <h2><?=L('user.regForm');?></h2>
    </div>

    <form class="form-regUser needs-validation" novalidate>
      <div id="promptMsg"></div>

    <div class="row">
            <div class="col-12 col-md-6">
              <label for="username" class="form-label"><?=L('user.userName');?></label>
              <input type="text" class="form-control" name="username" id="username" placeholder="<?=L('user.userName');?>" required>
              <div class="invalid-feedback"><?=L('error.userEmptyUserName');?></div>
            </div>
            <div class="col-12 col-md-6">
              <label for="email" class="form-label"><?=L('user.email');?></label>
              <input type="email" class="form-control" name="email" id="email" placeholder="you@example.com" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required>
              <div class="invalid-feedback"><?=L('error.userEmptyEmail');?></div>
            </div>
            <div class="col-12 col-md-6">
              <label for="password" class="form-label"><?=L('user.password');?></label>
              <input type="password" class="form-control" name="password" id="password" autocomplete required>
              <div class="invalid-feedback"><?=L('error.userEmptyPassword');?></div>
            </div>
            <div class="col-12 col-md-6">
              <label for="cfmPassword" class="form-label"><?=L('user.confirmPassword');?></label>
              <input type="password" class="form-control" name="cfmPassword" id="cfmPassword" autocomplete required>
              <div class="invalid-feedback"><?=L('error.userEmptyConfirmPassword');?></div>
            </div>
    </div>
    <div class="row g-2">
       <div class="col-2"><a class="w-100 btn btn-lg btn-secondary" href="<?php Utility\WebSystem::path($request->referer(Requests\Request::REFERER_QUERY), true, false);?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?=L('Back');?>"><i class="fas fa-1x fa-angle-left"></i><span class="d-none d-md-inline"> <?=L('Back');?></span></a></div>
       <div class="col-10"><button class="w-100 btn btn-lg btn-primary" type="submit"><?=L("login.register");?></button></div>
    </div>
    </form>
<footer class="footer text-muted text-center text-small">
  <p class="">&copy; 2022 Supertainment</p>
   <span class="text-info"><i class="fas fa-language fa-lg"></i><?php foreach (Pages\Language::getAvailableLang() as $lang): ?>
		<a href="<?php Utility\WebSystem::path("lang/".$lang['langCode']);?>" class="p-1 text-info"><?=$lang['langName'];?></a>
  <?php endforeach ?></span> 
</footer>
<?php
include("view/layout/js.php");
?>
<script>
$('.form-regUser').submit(function (e) {
   e.preventDefault();
   this.classList.add('was-validated');
   if (!this.checkValidity()) return;

   var data = new FormData(this);
   ajaxFunc.apiCall("POST", "user/register", data, "multipart/form-data", ajaxFunc.responseHandle);
});
$(function() {

});
</script>
<?php
include("view/layout/endpage.php");
?>