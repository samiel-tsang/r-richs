<?php
include("view/layout/meta.php");
if(empty($_SESSION['lang'])) 
  $_SESSION['lang'] = 'hk';
?>
<style>
   #forgetPasswordBtn {
      text-decoration: none;
      margin: 0px 15px;
   }
</style>
<body class="login bg-primary">
    <form class="form-signin shadow-lg">
    <div class="wrapper wrapper-login">
      <div class="container container-login animated fadeIn">
      <div class="text-info text-end text-info text-end my-2 mx-3"><?php foreach (Pages\Language::getAvailableLang() as $lang): ?>
		<a href="<?php Utility\WebSystem::path("lang/".$lang['langCode']);?>" class="p-1 text-info"><?=$lang['langName'];?></a>
<?php endforeach ?></div>
        <div class="text-center mx-3"><img class="my-4" src="images/logo/logo_main.jpg" alt="Logo" style="max-height: 150px;"></div>
        <h3 class="text-center"><?=L("login.signIn");?></h3>
        <div class="login-form">
          <div class="form-sub">
            <div class="form-floating form-floating-custom mb-3">
              <input
                id="inputUsername"
                name="user"
                type="text"
                class="form-control"
                placeholder="Username"
                required
              />
              <label for="inputUsername"><?=L("login.userName");?></label>

            </div>
            <div class="form-floating form-floating-custom mb-3">
              <input
                id="inputPassword"
                name="pass"
                type="password"
                class="form-control"
                placeholder="Password"
                required
              />
              <label for="inputPassword"><?=L("login.password");?></label>
              <div class="show-password">
                <i class="icon-eye"></i>
              </div>
            </div>
          </div>
          <div class="row m-0">
            <div class="d-flex form-sub">
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember_me" value="remember-me" />
                <label class="form-check-label" for="remember_me"><?=L("login.rememberMe");?></label>
              </div>

              <!--<a href="#" class="link float-end">Forget Password ?</a>-->
            </div>
          </div>
          <div class="form-action mb-3">
            <button class="btn btn-primary w-100 btn-login" text="submit"><?=L("login.signIn");?></button>
          </div>
          <!--<div class="login-account">
            <span class="msg">Don't have an account yet ?</span>
            <a href="#" id="show-signup" class="link">Sign Up</a>
          </div>
      -->
          <div><p class="mt-3 mb-3 text-muted mx-3">&copy; <?=date("Y");?> <?=L('title');?></p></div>
        </div>
      </div>
      <!--
      <div class="container container-signup animated fadeIn">
        <h3 class="text-center">Sign Up</h3>
        <div class="login-form">
          <div class="form-sub">
            <div class="form-floating form-floating-custom mb-3">
              <input
                id="fullname"
                name="fullname"
                type="text"
                class="form-control"
				placeholder="fullname"
                required
              />
              <label for="fullname">Fullname</label>
            </div>
            <div class="form-floating form-floating-custom mb-3">
              <input
                id="email"
                name="email"
                type="email"
                class="form-control"
				placeholder="email"
                required
              />
              <label for="email">Email</label>
            </div>
            <div class="form-floating form-floating-custom mb-3">
              <input
                id="passwordsignin"
                name="passwordsignin"
                type="password"
                class="form-control"
				placeholder="passwordsignin"
                required
              />
              <label for="passwordsignin">Password</label>
              <div class="show-password">
                <i class="icon-eye"></i>
              </div>
            </div>
            <div class="form-floating form-floating-custom mb-3">
              <input
                id="confirmpassword"
                name="confirmpassword"
                type="password"
                class="form-control"
				placeholder="confirmpassword"
                required
              />
              <label for="confirmpassword">Confirm Password</label>
              <div class="show-password">
                <i class="icon-eye"></i>
              </div>
            </div>
          </div>
          <div class="row form-sub m-0">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" name="agree" id="agree" />
              <label class="form-check-label" for="agree"
                >I Agree the terms and conditions.</label
              >
            </div>
          </div>
          <div class="form-action">
            <a href="#" id="show-signin" class="btn btn-danger btn-link btn-login me-3">Cancel</a>
            <a href="#" class="btn btn-primary btn-login">Sign Up</a>
          </div>
        </div>
      </div>-->
    </div>
    </form>


<?php
include("view/layout/js.php");
?>
<script>
$('.form-signin').submit(function (e) {
   var data = new FormData(this);
   ajaxFunc.apiCall("POST", "user/login", data, "multipart/form-data", function (data) {
      if (data.objectType == 'action' && data.action == 'redirect' && $('#remember_me').is(':checked')) {
         localStorage.admin_user = $('#inputUsername').val();
         localStorage.admin_pw = $('#inputPassword').val();
         localStorage.admin_chkbx = $('#remember_me').val();
      }
      /*
      ajaxFunc.responseHandle(data);
      if (data.objectType == 'message') {
         $('#inputUsername').focus();
      }
      */
      if(data.objectType == 'action') {
         ajaxFunc.responseHandle(data);
      } else {

         if(!data.content.success) { 
            swal({
               title: data.content.message,
               text: data.content.message,
               type: "warning",
               buttons: {
                  confirm: {
                     text: "<?=L('OK');?>",
                     className: "btn btn-success",
                  }
               },
            }).then((willOK) => {
               if (!willOK) {
                  $('#inputUsername').focus();
               } 
            });    
         }      
      }

   });
   e.preventDefault();
});
$(function() {
   if (localStorage.admin_chkbx && localStorage.admin_chkbx != '') {
      $('#remember_me').attr('checked', 'checked');
      $('#inputUsername').val(localStorage.admin_user);
      $('#inputPassword').val(localStorage.admin_pw);
   } else {
      $('#remember_me').removeAttr('checked');
      $('#inputUsername').val('');
      $('#inputPassword').val('');
   }

   $("#remember_me").click(function (event){
      if ($('#remember_me').is(':checked')) {
         localStorage.admin_user = $('#inputUsername').val();
         localStorage.admin_pw = $('#inputPassword').val();
         localStorage.admin_chkbx = $('#remember_me').val();
      } else {
         localStorage.admin_user = '';
         localStorage.admin_pw = '';
         localStorage.admin_chkbx = '';
      }
   });

   localStorage.setItem("hideSideBar", "open");
});

</script>
<?php
include("view/layout/endpage.php");
?>