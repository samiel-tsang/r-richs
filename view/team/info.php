<?php
include("view/layout/meta.php");
include("view/layout/head.php");
?>
<main class="content">
  <div class="funcBar d-flex text-white">
    <span class="funcTitle me-auto"><?= L('team.info'); ?> - <?= $obj->teamName; ?></span>
    <div class="funcMenu mx-3 py-2">
    <a class="btn" href="<?=$this->pageLink('page.teamList', ["pg"=>1]);?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?=L('ToList');?>"><i class="fas fa-1x fa-list-alt"></i></a>
    </div>
  </div>
  <div class="container">
    <div class="card my-3">
      <div class="card-top">
        <div class="row my-3 mx-1">
          <div class="col-md-6">
            <h5 class="card-title"><?= L('team.info'); ?></h5>
          </div>
          <div class="col-md-6 text-end">
            <a class="btn btn-md btn-success" href="<?= $this->pageLink('page.teamEdit', ["id" => $obj->id]); ?>" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= L('Edit'); ?>"><i class="fas fa-sm fa-edit"></i> <?= L('Edit'); ?></a>
            <?php if ($obj->userID==0 && $obj->status==1) { ?>
              <button class="btn btn-md btn-danger btnConfirm" type="button" data-toggle="tooltip" data-placement="bottom" title="<?= L('Confirm'); ?>" data-id="<?= $obj->id; ?>" data-name="<?= $obj->teamName; ?>" data-category="<?= $obj->teamCategory; ?>" data-period="<?= $obj->teamPeriod; ?>"> <i class="fa-solid fa-clipboard-check"></i> <?= L('Confirm'); ?></button>
            <?php } else if ($obj->userID==0 && $obj->status==2) { ?>
              <button class="btn btn-md btn-info btnRePay" type="button" data-toggle="tooltip" data-placement="bottom" title="<?= L('RepayEmail'); ?>" data-id="<?= $obj->id; ?>" data-name="<?= $obj->teamName; ?>" data-category="<?= $obj->teamCategory; ?>" data-period="<?= $obj->teamPeriod; ?>"> <i class="fa-solid fa-dollar-sign"></i> <?= L('RepayEmail'); ?></button>
            <?php } ?>

          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <!--<figure class="figure col-4 text-center border border-1"><img src="../<?= $obj->teamImage; ?>" alt="Team Image" class="align-middle w-100"></figure>-->
              <div class="col-12 d-flex align-items-center">
                <div>
                  <h3><?= $obj->teamName; ?>  </h3>
                  <div><span class="fw-bold"><?= L('team.category'); ?>: </span><span><?= $obj->teamCategory; ?></span></div>
                  <div><span class="fw-bold"><?= L('team.period'); ?>: </span><span><?= $obj->teamPeriod; ?></span></div>
                  <div><span class="fw-bold"><?= L('team.submissionFee'); ?>: </span><span><?= $obj->submissionFee==0?L('team.waive'):Utility\WebSystem::displayPrice($obj->submissionFee); ?></span></div>
                  <div><span class="fw-bold"><?= L('team.confirmStatus'); ?>: </span><span id='confirmStatus' class="text-<?= ($obj->userID==0?"danger":"primary"); ?>"><?= ($obj->userID==0?L('team.pending'):L('team.confirmed')); ?></span></div>
                </div>
              </div>
            </div>
          </div>


          <div class="col-md-6">
            <div class="card">
              <div class="card-header"><?= L('team.info'); ?></div>
              <div class="card-body">
                <div class="accordion" id="memberInfo">
                  <?php
                  $stmTM = Database\Sql::select('teamMember')->where(['teamID', '=', "?"])->prepare();
                  $stmTM->execute([$obj->id]);
                  foreach ($stmTM as $mb) {

                  ?>
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="memberID_<?= $mb['id']; ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#memberID_<?= $mb['id']; ?>-collapse" aria-expanded="false" aria-controls="memberID_<?= $mb['id']; ?>-collapse">
                          <span class="fw-bold me-5"><?= ($mb['roleID'] == 1) ? L('team.leaderInfo') : L('team.memberInformation'); ?>:</span> <?= $mb['nameChi']; ?>; <?= $mb['nameEng']; ?>
                        </button>
                      </h2>
                      <div id="memberID_<?= $mb['id']; ?>-collapse" class="accordion-collapse collapse" aria-labelledby="memberID_<?= $mb['id']; ?>">
                        <div class="accordion-body">
                          <div class="row">
                            <div class="col-sm-4 fw-bold"><?= L('team.memberChineseName'); ?></div>
                            <div class="col-sm-8"><?= $mb['nameChi']; ?></div>
                          </div>
                          <div class="row">
                            <div class="col-sm-4 fw-bold"><?= L('team.memberEnglishName'); ?></div>
                            <div class="col-sm-8"><?= $mb['nameEng']; ?></div>
                          </div>
                          <div class="row">
                            <div class="col-sm-4 fw-bold"><?= L('team.memberAge'); ?></div>
                            <div class="col-sm-8"><?= $mb['age']; ?></div>
                          </div>
                          <div class="row">
                            <div class="col-sm-4 fw-bold"><?= L('team.memberGender'); ?></div>
                            <div class="col-sm-8"><?= $mb['gender']; ?></div>
                          </div>
                          <div class="row">
                            <div class="col-sm-4 fw-bold"><?= L('team.memberMobile'); ?></div>
                            <div class="col-sm-8"><?= $mb['mobile']; ?></div>
                          </div>
                          <div class="row">
                            <div class="col-sm-4 fw-bold"><?= L('team.memberEmail'); ?></div>
                            <div class="col-sm-8"><?= $mb['email']; ?></div>
                          </div>
                          <?php if ($mb['roleID'] == 1) { ?>
                            <fieldset>
                              <legend><?= L('team.memberAddress'); ?></legend>
                              <div class="row">
                                <div class="col-sm-4 fw-bold"><?= L('team.memberAddress1'); ?></div>
                                <div class="col-sm-8"><?= $mb['addr1']; ?></div>
                              </div>
                              <div class="row">
                                <div class="col-sm-4 fw-bold"><?= L('team.memberAddress2'); ?></div>
                                <div class="col-sm-8"><?= $mb['addr2']; ?></div>
                              </div>
                            </fieldset>
                          <?php } ?>
                          <div class="row">
                            <div class="col-sm-4 fw-bold"><?= L('team.emContactName'); ?></div>
                            <div class="col-sm-8"><?= $mb['emContactName']; ?></div>
                          </div>
                          <div class="row">
                            <div class="col-sm-4 fw-bold"><?= L('team.emContactMobile'); ?></div>
                            <div class="col-sm-8"><?= $mb['emContactMobile']; ?></div>
                          </div>
                          <div class="row">
                            <div class="col-sm-4 fw-bold"><?= L('team.memberShirtSize'); ?></div>
                            <div class="col-sm-8"><?= $mb['shirtSize']; ?></div>
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
        </div>
        <div class='row'>
          <div class="col-md-6">
            <div class="card my-3">
              <div class="card-header"><?= L('menu.donate'); ?></div>
              <div class="card-body">
                <ul class="list-group">
                  <?php
                  $donatorAmt = 0;
                  $stmTD = Database\Sql::select('teamDonator')->where(['teamID', '=', "?"])->where(['status', '=', 1])->prepare();
                  $stmTD->execute([$obj->id]);
                  foreach ($stmTD as $donator) {
                    $donatorAmt += $donator['donatorAmount'];
                  ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center"><?= $donator['donatorName']; ?><span>$<?= $donator['donatorAmount']; ?></span></li>
                  <?php } ?>
                </ul>
              </div>
              <div class="card-footer"><span class="fw-bold"><?= L('team.donateAmount'); ?>:</span> <?=Utility\WebSystem::displayPrice($donatorAmt); ?></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card my-3">
              <div class="card-header"><?= L('menu.paypal'); ?></div>
              <div class="card-body">
                <ul class="list-group">
                  <?php
                  $paidAmt = 0;
                  $stmTxn = Database\Sql::select('transaction')->where(['teamID', '=', "?"])->where(['status', '=', 1])->prepare();
                  $stmTxn->execute([$obj->id]);
                  foreach ($stmTxn as $txn) {
                    $paidAmt += $txn['amount'];
                  ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center"><?= Utility\WebSystem::displayDate($txn['createDate'], 'Y.m.d H:i:s'); ?><span>$<?= $txn['amount']; ?></span></li>
                  <?php } ?>
                </ul>
              </div>
              <div class="card-footer"><span class="fw-bold"><?= L('amount'); ?>:</span> <?= Utility\WebSystem::displayPrice($paidAmt); ?></div>
            </div>
          </div>
        </div>


      </div>
    </div>


  </div>
</main>
<?php
include("view/layout/foot.php");
include("view/layout/js.php");
?>
<script>
  $(".btnConfirm").click(function(e) {

    e.preventDefault();

    $('#msgBox').one('show.bs.modal', function(ev) {

      var button = $(e.currentTarget);
      var modal = $(this);
      modal.find('#msgBoxLabel').text('<?=L('team.confirm');?>');

      modal.find('#msgBoxBtnPri').css('display', 'none');

      modal.find('.modal-body').html('<?=L('team.name');?>: ' + button.data('name') + '<br><?=L('team.category');?>: ' + button.data('category') + '<br><?=L('team.period');?>: ' + button.data('period'));
      modal.find('#msgBoxBtnPri').css('display', 'block');
      modal.find('#msgBoxBtnPri').one('click', function(event) {
        //$('#msgBox').modal('hide');        
        
        ajaxFunc.apiCall("POST", "team/confirm/<?=$obj->id;?>", null, null, function (data) {   
          modal.find('.modal-body').text(data.content.message);
          modal.find('#msgBoxBtnPri').css('display', 'none');
          $(".btnConfirm").hide();
          $("#confirmStatus").removeClass("text-danger");
          $("#confirmStatus").addClass("text-primary");
          $("#confirmStatus").text("<?=L('team.confirmed');?>");
        });
      });


    }).modal('show')

  });

  $(".btnRePay").click(function(e) {

    e.preventDefault();

    $('#msgBox').one('show.bs.modal', function(ev) {

      var button = $(e.currentTarget);
      var modal = $(this);
      modal.find('#msgBoxLabel').text('<?=L('team.repay');?>');

      modal.find('#msgBoxBtnPri').css('display', 'none');

      modal.find('.modal-body').html('<?=L('team.name');?>: ' + button.data('name') + '<br><?=L('team.category');?>: ' + button.data('category') + '<br><?=L('team.period');?>: ' + button.data('period'));
      modal.find('#msgBoxBtnPri').css('display', 'block');
      modal.find('#msgBoxBtnPri').one('click', function(event) {
        //$('#msgBox').modal('hide');        
        
        ajaxFunc.apiCall("GET", "team/repay/<?=$obj->id;?>", null, null, function (data) {   
          modal.find('.modal-body').text(data.msg);
          modal.find('#msgBoxBtnPri').css('display', 'none');
        });
      });


    }).modal('show')

  });
</script>

<?php
include("view/layout/endpage.php");
?>