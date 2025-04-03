<?php
include("view/layout/meta.php");
include("view/layout/headExt.php");
?>
<main class="content">

  <div class="funcBar d-flex text-white">
    <span class="funcTitle me-auto"><?=$teamObj->teamName; ?></span>
  </div>
  <div class="container-fiuld">
    <div class="row">
      <div class="col-md-6">
        <div class="card">
            <div class="card-header"><?= L('donator.createNew'); ?></div>
            <div class="card-body">
              <div class="row">                
                <div class="col-12 d-flex align-items-center">
                  <div>
                    <h3><?= $teamObj->teamName; ?></h3>
                    <div><span class="fw-bold"><?= L('team.category'); ?>: </span><span><?= $teamObj->teamCategory->name; ?></span></div>
                    <div><span class="fw-bold"><?= L('team.period'); ?>: </span><span><?= $teamObj->teamPeriod->name; ?></span></div>
                    <div><span class="fw-bold"><?= L('team.submissionFee'); ?>: </span><span><?= $teamObj->submissionFee==0?L('team.waive'):Utility\WebSystem::displayPrice($teamObj->submissionFee); ?></span></div>                                       
                  </div>
                </div>
              </div>
            </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><?= L('donator.createNew'); ?></div>
          <div class="card-body">
            <form id="donatorForm">
              <input type="hidden" name="teamID" value="<?= $teamObj->id; ?>">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="donatorName" name="donatorName" placeholder="Name">
                <label for="donatorName"><?= L('donator.name'); ?></label>
              </div>
              <div class="form-floating">
                <input type="text" class="form-control" id="donatorAmount" name="donatorAmount" placeholder="Amount">
                <label for="donatorAmount"><?= L('amount'); ?></label>
              </div>
              <div class="row">
                <p><?= L('info.personalDataCollectionTitle'); ?></p>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="donatorRecvComm" value="0" id="chkRecvComm">
                <label class="form-check-label" for="chkRecvComm"><?= L('info.saInfoReceiveTitle'); ?></label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="donatorPermitPromo" value="0" id="chkPermitPromo">
                <label class="form-check-label" for="chkPermitPromo"><?= L('info.saImageUseTitle'); ?></label>
              </div>
              <button type="submit" class="btn btn-primary my-3"><?= L('team.submit'); ?></button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><?= L('team.info'); ?></div>
          <div class="card-body">
            <div class="accordion" id="memberInfo">
              <?php
              foreach ($stmTM as $mb) {
              ?>
                <div class="accordion-item">
                  <h2 class="accordion-header" id="memberID_<?= $mb->id; ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#memberID_<?= $mb->id; ?>-collapse" aria-expanded="false" aria-controls="memberID_<?= $mb->id; ?>-collapse">
                      <span class="fw-bold me-5"><?= ($mb->roleID == 1) ? L('team.leaderInfo') : L('team.memberInformation'); ?>:</span> <?= $mb->nameChi; ?>; <?= $mb->nameEng; ?>
                    </button>
                  </h2>
                  <div id="memberID_<?= $mb->id; ?>-collapse" class="accordion-collapse collapse" aria-labelledby="memberID_<?= $mb->id; ?>">
                    <div class="accordion-body">
                      <div class="row">
                        <div class="col-sm-4 fw-bold"><?= L('team.memberChineseName'); ?></div>
                        <div class="col-sm-8"><?= $mb->nameChi; ?></div>
                      </div>
                      <div class="row">
                        <div class="col-sm-4 fw-bold"><?= L('team.memberEnglishName'); ?></div>
                        <div class="col-sm-8"><?= $mb->nameEng; ?></div>
                      </div>
                      <div class="row">
                        <div class="col-sm-4 fw-bold"><?= L('team.memberAge'); ?></div>
                        <div class="col-sm-8"><?= $mb->age; ?></div>
                      </div>
                      <div class="row">
                        <div class="col-sm-4 fw-bold"><?= L('team.memberGender'); ?></div>
                        <div class="col-sm-8"><?= $mb->gender; ?></div>
                      </div>
                      <div class="row">
                        <div class="col-sm-4 fw-bold"><?= L('team.memberMobile'); ?></div>
                        <div class="col-sm-8"><?= $mb->mobile; ?></div>
                      </div>
                      <div class="row">
                        <div class="col-sm-4 fw-bold"><?= L('team.memberEmail'); ?></div>
                        <div class="col-sm-8"><?= $mb->email; ?></div>
                      </div>
                      <?php if ($mb->roleID == 1) { ?>
                        <fieldset>
                          <legend><?= L('team.memberAddress'); ?></legend>
                          <div class="row">
                            <div class="col-sm-4 fw-bold"><?= L('team.memberAddress1'); ?></div>
                            <div class="col-sm-8"><?= $mb->addr1; ?></div>
                          </div>
                          <div class="row">
                            <div class="col-sm-4 fw-bold"><?= L('team.memberAddress2'); ?></div>
                            <div class="col-sm-8"><?= $mb->addr2; ?></div>
                          </div>
                        </fieldset>
                      <?php } ?>
                      <div class="row">
                        <div class="col-sm-4 fw-bold"><?= L('team.emContactName'); ?></div>
                        <div class="col-sm-8"><?= $mb->emContactName; ?></div>
                      </div>
                      <div class="row">
                        <div class="col-sm-4 fw-bold"><?= L('team.emContactMobile'); ?></div>
                        <div class="col-sm-8"><?= $mb->emContactMobile; ?></div>
                      </div>
                    </div>
                  </div>
                </div>
              <?
              }
              ?>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card my-3 my-md-0">
          <div class="card-header"><?= L('menu.donate'); ?></div>
          <div class="card-body">
            <ul class="list-group">
              <?php
              $donatorAmt = 0;
              foreach ($stmTD as $donator) {
                $donatorAmt += $donator->donatorAmount;
              ?>
                <li class="list-group-item d-flex justify-content-between align-items-center"><?= $donator->donatorName; ?><span>$<?= $donator->donatorAmount; ?></span></li>
              <?php } ?>
            </ul>
          </div>
          <div class="card-footer"><span class="fw-bold"><?= L('team.donateAmount'); ?>:</span> <?= Utility\WebSystem::displayPrice($donatorAmt); ?></div>
        </div>
        <div class="card my-3">
          <div class="card-header"><?= L('menu.paypal'); ?></div>
          <div class="card-body">
            <ul class="list-group">
              <?php
              $paidAmt = 0;
              foreach ($stmTxn as $txn) {
                $paidAmt += $txn->amount;
              ?>
                <li class="list-group-item d-flex justify-content-between align-items-center"><?= Utility\WebSystem::displayDate($txn->createDate, 'Y.m.d H:i:s'); ?><span>$<?= $txn->amount; ?></span></li>
              <?php } ?>
            </ul>
          </div>
          <div class="card-footer"><span class="fw-bold"><?= L('amount'); ?>:</span> <?= Utility\WebSystem::displayPrice($paidAmt); ?></div>
        </div>
      </div>
    </div>
  </div>
  <a href="<?=cfg('event')['emailLink'];?>" class="whatsapp-float" target="_blank"><i class="fa fa-envelope my-float"></i></a>
  <!--
  <a href="<?=cfg('event')['emailLink'];?>" class="email-float" target="_blank"><i class="fa fa-envelope my-float"></i></a>
  <a href="<?=cfg('event')['whatsappLink'];?>" class="whatsapp-float" target="_blank"><i class="fa fa-whatsapp my-float"></i></a>  
  -->
</main>
<?php
include("view/layout/footExt.php");
include("view/layout/js.php");

$formHtml = '
<form class="form-changePass needs-validation" id="form-changePass" novalidate><div id="message" class="h5 text-primary text-center"></div>
      <div class="row">
        <div class="col-12 col-md-12">
        <label for="oldPassword" class="form-label">' . L('user.oldPassword') . ' *</label>
          <input type="password" class="form-control" name="oldPassword" id="oldPassword" placeholder="' . L('user.oldPassword') . '" required>
          <div class="invalid-feedback">' . L('error.userInvalidOldPassword') . '</div>
        </div>
      </div>
      <div class="row">
        <div class="col-12 col-md-12">
         <label for="password" class="form-label">' . L('user.password') . ' *</label>
          <input type="password" class="form-control" name="password" id="password" placeholder="' . L('user.password') . '" pattern="(?=.*\\\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
          <div class="invalid-feedback">' . L('error.userInvalidPasswordFormat') . '</div>
        </div>
      </div>
      <div class="row">  
        <div class="col-12 col-md-12">
        <label for="cfmPassword" class="form-label">' . L('user.confirmPassword') . ' *</label>
          <input type="password" class="form-control" name="cfmPassword" id="cfmPassword" placeholder="' . L('user.confirmPassword') . '" pattern="(?=.*\\\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
          <div class="invalid-feedback">' . L('error.userInvalidPasswordFormat') . '</div>          
        </div>
      </div>
      <div class="row">  
         <div class="col-12 rowBtn"><button class="w-100 btn btn-lg btn-primary" type="submit">'.L("team.submit").'</button></div>
      </div>
</form>';

?>
<script>
  $('#uploadImage').submit(function(e) {
    var form = $(this);
    var formData = new FormData(this);
    ajaxFunc.apiCall("POST", "team/uploadAvatar/<?=$teamObj->id;?>", formData, "multipart/form-data", function (data) {      
      
      if(data.content.success==true){
        $("#avatarImage").attr('src',data.content.imagePath);
      } 
      
      $('#msgBox').one('show.bs.modal', function(ev) {
      var button = $(e.currentTarget);
      var modal = $(this);
      modal.find('#msgBoxLabel').text('');     
      modal.find('.modal-body').html(data.content.message);
      modal.find('#msgBoxBtnPri').css('display', 'none');

    }).modal('show')      

      
    });    
    e.preventDefault();
  });

  $('#donatorForm').submit(function(e) {
    var form = $(this);
    var data = new FormData(this);
    ajaxFunc.apiCall("POST", "donator/", data, "multipart/form-data", ajaxFunc.responseHandle);
    e.preventDefault();
  });


  var contentHtml = '<?=trim(preg_replace('/\s+/', ' ', $formHtml));?>';
  
  $("#profileMenu").click(function(e) {
    e.preventDefault();
    $('#msgBox').one('show.bs.modal', function(ev) {
      var button = $(e.currentTarget);
      var modal = $(this);
      modal.find('#msgBoxLabel').text('<?= L('login.changePassword'); ?>');     
      modal.find('.modal-body').html(contentHtml);
      modal.find('#msgBoxBtnPri').css('display', 'none');

      $('#form-changePass').submit(function(e) {
        e.preventDefault();

        this.classList.add('was-validated');
        if (!this.checkValidity()) return;
        
        var formData = new FormData(this);
        ajaxFunc.apiCall("POST", "user/resetpassword/<?=$teamObj->userID;?>", formData, "multipart/form-data", function (data) {           
            if(data.type=="alert"){
                $("#message").html(data.msg);
            } else if(data.type=="info") {
                modal.find('.modal-body').html(data.msg);
            }
        });        
      });
    }).modal('show')
  });



</script>
<?php
include("view/layout/endpage.php");
?>