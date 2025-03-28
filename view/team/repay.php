<?php

use Controller\category;
use Controller\period;
//use Controller\defaultAvatar;
use Controller\team;
if($err!=""){
  echo "<span style='text-align:center'><h1>".$err."</h1></span>";
  exit();
}
$cssPath = array("css/signin.css");
$bodyClass = "bg-light";
include("view/layout/meta.php");

if (isset($obj)) {
  $stmTL = Database\Sql::select("teamMember")->where(['teamID', '=', $obj->id])->where(['roleID', '=', 1])->prepare();
  $stmTL->execute();
  $leaderObj = $stmTL->fetch(\PDO::FETCH_OBJ);

  $stmTM = Database\Sql::select("teamMember")->where(['teamID', '=', $obj->id])->where(['roleID', '=', 2])->prepare();
  $stmTM->execute();
  $memberCount = $stmTM->rowCount();
}


function gen_member_row($i = 1, $memeberInfo = [], $required = ""){

   return '
    <div class="card" style="width: 100%; margin-top:20px" id="member_' . $i . '">
      <div class="card-body">
      <h5 class="card-title text-center">' . L('team.member') . ' ' . $i . ' ' . L('team.memberInfo') . '</h5>
        <div class="row">
          <div class="col-12 col-md-6">
            <label for="nameChi" class="form-label">' . L('team.memberChineseName') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="text" class="form-control" name="memberNameChi[]" id="memberNameChi_' . $i . '" value="'.$memeberInfo['nameChi'].'" ' . $required . ' readonly>            
          </div>
          <div class="col-12 col-md-6">
            <label for="nameEng" class="form-label">' . L('team.memberEnglishName') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="text" class="form-control" name="memberNameEng[]" id="memberNameEng_' . $i . '" value="'.$memeberInfo['nameEng'].'" ' . $required . ' readonly>            
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-6">
            <label for="age" class="form-label">' . L('team.memberAge') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="number" class="form-control" name="memberAge[]" id="memberAge_' . $i . '" min="3" step="1" max="100" value="'.$memeberInfo['age'].'" ' . $required . ' readonly>
          </div>
          <div class="col-12 col-md-6">
            <label for="gender" class="form-label">' . L('team.memberGender') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <select class="form-select" name="memberGender[]" id="memberGender_' . $i . '" ' . $required . ' disabled>
              <option value=""></option>
              <option value="M" '.($memeberInfo['gender']=="M"?"selected":"").' >' . L('gender.male') . '</option>
              <option value="F" '.($memeberInfo['gender']=="F"?"selected":"").' >' . L('gender.female') . '</option>
            </select>            
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-6">
            <label for="mobile" class="form-label">' . L('team.memberMobile') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="tel" class="form-control" name="memberMobile[]" id="memberMobile_' . $i . '" value="'.$memeberInfo['mobile'].'" pattern="[0-9]{8}" ' . $required . ' readonly>            
          </div>
          <div class="col-12 col-md-6">
            <label for="email" class="form-label">' . L('team.memberEmail') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="email" class="form-control" name="memberEmail[]" id="memberEmail_' . $i . '" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" value="'.$memeberInfo['email'].'" ' . $required . ' readonly>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-6">
            <label for="emContactName" class="form-label">' . L('team.emContactName') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="text" class="form-control" name="memberEmContactName[]" id="memberEmContactName_' . $i . '" value="'.$memeberInfo['emContactName'].'" ' . $required . ' readonly>
          </div>
          <div class="col-12 col-md-6">
            <label for="emContactMobile" class="form-label">' . L('team.emContactMobile') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="tel" class="form-control" name="memberEmContactMobile[]" id="memberEmContactMobile_' . $i . '" value="'.$memeberInfo['emContactMobile'].'" pattern="[0-9]{8}" ' . $required . ' readonly>
          </div>
        </div>
      </div>
    </div>';
}

?>


<form class="form-signin shadow-lg" id="form-regUser" autocomplete="off">
  <span class="text-info"><i class="fas fa-language fa-lg"></i><?php foreach (Pages\Language::getAvailableLang() as $lang) : ?>
      <a href="<?php Utility\WebSystem::path("lang/" . $lang['langCode']); ?>" class="p-1 text-info"><?= $lang['langName']; ?></a>
    <?php endforeach ?></span>
  <div class="py-5 text-center">
    <h2><?= L('user.regForm'); ?></h2>
  </div>

  <div class="card" style="width: 100%">    
    <div class="card-body text-primary">
      <h5><?=L("info.groupInstructionHead");?></h5>
      <ul>
        <li><?=L("info.groupInstructionDetail1");?></li>
        <li><?=L("info.groupInstructionDetail2");?></li>
      </ul>
      <h5><?=L("info.periodInstructionHead");?> (<?=L("info.periodInstructionDetail");?>)</h5>
      <ul>        
        <li>10:00 - 12:00</li>
        <li>11:00 - 13:00</li>
      </ul>
      <h5><?=L("info.memberInstructionHead");?></h5>
      <ul>
        <li><?=L("info.memberInstructionDetail1");?></li>
        <li><?=L("info.memberInstructionDetail2");?></li>
      </ul>
      <h5><?=L("info.priceInstructionHead");?></h5>
      <ul>
        <li><?=L("info.priceInstructionDetail1");?></li>
        <li><?=L("info.priceInstructionDetail2");?></li>
        <li><?=L("info.priceInstructionDetail3");?></li>
      </ul>
      <h5><?=L("info.eventDetailTitle");?></h5>
      <ul>
        <li><a target="_blank" href="<?=L("info.eventDetailLink");?>"><?=L("info.eventDetailLink");?></a></li>
      </ul>
      <h5><?=L("info.eventFBHead");?></h5>
      <ul>
        <li><a target="_blank" href="<?=cfg('event')['facebookLink'];?>"><?=cfg('event')['facebookLink'];?></a></li>
      </ul>
      <h5><?=L("info.eventIGHead");?></h5>
      <ul>
        <li><a target="_blank" href="<?=cfg('event')['instagramLink'];?>"><?=cfg('event')['instagramLink'];?></a></li>
      </ul>      
    </div>
  </div>

  <div id="promptMsg"></div>
  <div class="card" style="width: 100%; margin-top:20px">
    <div class="card-body">
      <h5 class="card-title text-center"><?= L('team.info'); ?></h5>
      <div class="row">
        <div class="col-12 col-md-6">
          <label for="categoryID" class="form-label"><?= L('team.category'); ?> *</label>
          <select class="form-select" name="categoryID" id="categoryID" placeholder="<?= L('team.category'); ?>" disabled>
            <option value=""></option>
            <?php
            foreach (category::find_all() as $categoryObj) {
              echo "<option value='" . $categoryObj->id . "' ";
              if ($categoryObj->id == $obj->categoryID) echo "selected";
              echo ">" . $categoryObj->name . "</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-12 col-md-6">
          <label for="periodID" class="form-label"><?= L('team.period'); ?> *</label>
          <select class="form-select" name="periodID" id="periodID" placeholder="<?= L('team.period'); ?>" disabled>
            <option value=""></option>
            <?php
            foreach (period::find_all() as $periodObj) {
              echo "<option value='" . $periodObj->id . "' ";
              if ($periodObj->id == $obj->periodID) echo "selected";
              echo ">" . $periodObj->name . "</option>";
            }
            ?>
          </select>
        </div>
      </div>
<!--
      <div class="row">
        <div class="col-12 col-md-12">
          <label for="avatarID" class="form-label"><?= L('team.avatar'); ?> *</label>
          <div class="row defaultImage">
            <img src="<?php Utility\WebSystem::path($obj->teamImage); ?>" style="width:170px">
          </div>
        </div>
      </div>
-->
      <div class="row">
        <div class="col-12 col-md-6">
          <label for="teamName" class="form-label"><?= L('team.name'); ?> *</label>
          <input type="text" class="form-control" name="teamName" id="teamName" placeholder="<?= L('team.name'); ?>" maxLength="20" value="<?= $obj->teamName; ?>" readonly>
        </div>
      </div>

    </div>
  </div>

  <!-- leader info -->
  <div class="card" style="width: 100%; margin-top:20px">
    <div class="card-body">
      <h5 class="card-title text-center"><?= L('team.leaderInfo'); ?></h5>
      <div class="row">
        <div class="col-12 col-md-6">
          <label for="nameChi" class="form-label"><?= L('team.memberChineseName'); ?> *</label>
          <input type="text" class="form-control" name="leaderNameChi" id="leaderNameChi" placeholder="<?= L('team.memberChineseName'); ?>" value="<?= $leaderObj->nameChi; ?>" readonly>
        </div>
        <div class="col-12 col-md-6">
          <label for="nameEng" class="form-label"><?= L('team.memberEnglishName'); ?> *</label>
          <input type="text" class="form-control" name="leaderNameEng" id="leaderNameEng" placeholder="<?= L('team.memberEnglishName'); ?>" value="<?= $leaderObj->nameEng; ?>" readonly>
        </div>
      </div>

      <div class="row">
        <div class="col-12 col-md-6">
          <label for="age" class="form-label"><?= L('team.memberAge'); ?> *</label>
          <input type="number" class="form-control" name="leaderAge" id="leaderAge" placeholder="<?= L('team.memberAge'); ?>" min="15" step="1" max="100" value="<?= $leaderObj->age; ?>" readonly>
        </div>
        <div class="col-12 col-md-6">
          <label for="gender" class="form-label"><?= L('team.memberGender'); ?> *</label>
          <select class="form-select" name="leaderGender" id="leaderGender" placeholder="<?= L('team.memberGender'); ?>" disabled>
            <option value=""></option>
            <option value="M" <?php if ($leaderObj->gender == "M") echo "selected"; ?>><?= L('gender.male'); ?></option>
            <option value="F" <?php if ($leaderObj->gender == "F") echo "selected"; ?>><?= L('gender.female'); ?></option>
          </select>
        </div>
      </div>

      <div class="row">
        <div class="col-12 col-md-6">
          <label for="mobile" class="form-label"><?= L('team.memberMobile'); ?> *</label>
          <input type="tel" class="form-control" name="leaderMobile" id="leaderMobile" placeholder="<?= L('team.memberMobile'); ?>" pattern="[0-9]{8}" value="<?= $leaderObj->mobile; ?>" readonly>
        </div>
        <div class="col-12 col-md-6">
          <label for="email" class="form-label"><?= L('team.memberEmail'); ?> *</label>
          <input type="email" class="form-control" name="leaderEmail" id="leaderEmail" placeholder="<?= L('team.memberEmail'); ?>" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" value="<?= $leaderObj->email; ?>" readonly>
        </div>
      </div>

      <div class="row">
        <div class="col-12 col-md-6">
          <label for="addr1" class="form-label"><?= L('team.memberAddress'); ?> *</label>
          <input type="text" class="form-control" name="leaderAddr1" id="leaderAddr1" placeholder="<?= L('team.memberAddress1'); ?>" value="<?= $leaderObj->addr1; ?>" readonly>
        </div>
        <div class="col-12 col-md-6">
          <label for="addr2" class="form-label">&nbsp;</label>
          <input type="text" class="form-control" name="leaderAddr2" id="leaderAddr2" placeholder="<?= L('team.memberAddress2'); ?>" value="<?= $leaderObj->addr2; ?>" readonly>
        </div>
      </div>

      <div class="row">
        <div class="col-12 col-md-6">
          <label for="emContactName" class="form-label"><?= L('team.emContactName'); ?> *</label>
          <input type="text" class="form-control" name="leaderEmContactName" id="leaderEmContactName" placeholder="<?= L('team.emContactName'); ?>" value="<?= $leaderObj->emContactName; ?>" readonly>
        </div>
        <div class="col-12 col-md-6">
          <label for="emContactMobile" class="form-label"><?= L('team.emContactMobile'); ?> *</label>
          <input type="tel" class="form-control" name="leaderEmContactMobile" id="leaderEmContactMobile" placeholder="<?= L('team.emContactMobile'); ?>" pattern="[0-9]{8}" value="<?= $leaderObj->emContactMobile; ?>" readonly>
        </div>
      </div>
    </div>
  </div>
  <!-- end of leader info -->
  <div id="memeber_section">
    <?php
        if (isset($obj)) {
            foreach($stmTM as $idx=>$memberObj) {
                echo gen_member_row(++$idx, $memberObj, "required");
            }
            for($i=$memberCount; $i<3; $i++) {
                $idx = $i+1;
                if($i==0)
                    echo gen_member_row($idx, [], "required");
                else
                    echo gen_member_row($idx);
            }                            
        } else {
            for($i=$memberCount; $i<3; $i++) {
                $idx = $i+1;
                echo gen_member_row($idx);
            }
        }
    ?>

  </div>

  <div class="card" style="width: 100%; margin-top:20px">
    <div class="card-body">
      <div class="row">
        <div class="col-12 col-md-6">
          <label for="categoryID" class="form-label"><?= L('promo.code'); ?></label>
          <input type="text" class="form-control" name="promoCode" id="promoCode" value="<?=$obj->promoCode;?>" readonly>
        </div>
        <div class="col-12 col-md-6">
          <label for="categoryID" class="form-label"><?= L('team.fee'); ?></label>
          <input type="text" class="form-control" name="totalFee" id="totalFee" value="<?=$obj->totalFee;?>" readonly>
        </div>        
      </div>
    </div>
  </div>
  <div class="card" style="width: 100%; margin-top:20px">
    <div class="card-body text-primary">
      <h5><?= L("info.disclaimerHead"); ?></h5>
      <ul>
        <li><?= L("info.disclaimerDetail"); ?></li>
      </ul>
      <h5><?= L("info.parentConsentHead"); ?></h5>
      <ul>
        <li><?= L("info.parentConsentDetail"); ?></li>
      </ul>
      <h5><?= L("info.sponsorshipHead"); ?></h5>
      <ul>
        <li><?= L("info.sponsorshipDetail"); ?></li>
      </ul>
      <h5><?= L("info.useOfDataHead"); ?></h5>
      <ul>
        <li><?= L("info.useOfDataDetail"); ?></li>
      </ul>
    </div>
  </div>
  <div class="row d-grid gap-2">
    <div class="col-12 rowBtn"><button class="w-100 btn btn-lg btn-primary" type="submit"><?= L("team.submit"); ?></button></div>
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

  var checkoutXhr;
  $('#form-regUser').submit(function(e) {
    e.preventDefault();
/*
    this.classList.add('was-validated');
    //if (!this.checkValidity()) return;

    if (!this.checkValidity()) {
      var errorElements = document.querySelectorAll(
        "input.form-control:invalid, select.form-select:invalid");

      $('html, body').animate({
        scrollTop: $(errorElements[0]).offset().top - 50
      }, 200);

      return;
    }
*/


    //var data = new FormData(this);
    if (checkoutXhr !== undefined && checkoutXhr.readyState != 4) checkoutXhr.abort();
    checkoutXhr = ajaxFunc.apiCall("GET", "team/repayment/<?=$obj->id;?>", null, null, ajaxFunc.responseHandle);
  });

</script>
<?php
include("view/layout/endpage.php");
?>