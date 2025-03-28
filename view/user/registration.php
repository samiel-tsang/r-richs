<?php

use Controller\category;
use Controller\period;
use Controller\defaultAvatar;
use Controller\team;
use Controller\eventSetting;

$cssPath = array("css/signin.css");
$bodyClass = "bg-light";
$end_date = empty(eventSetting::getSettingMeta("regEndDate"))?"2024-11-27":eventSetting::getSettingMeta("regEndDate")->metaValue;
include("view/layout/meta.php");

  function gen_member_row($i = 1, $required = "")
  {
    $class = "";

    if($i>1){
      $class = "memeberFieldCheck";
    }

    return '
    <div class="card" style="width: 100%; margin-top:20px" id="member_' . $i . '">
      <div class="card-body">
        <h5 class="card-title text-center">' . L('team.member') . ' ' . $i . ' ' . L('team.memberInfo') . '</h5>
        <div class="row">
          <div class="col-12 col-md-6">
            <label for="nameChi" class="form-label">' . L('team.memberChineseName') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="text" class="form-control '.$class.'" name="memberNameChi[]" id="memberNameChi_' . $i . '" placeholder="' . L('team.memberChineseName') . '" ' . $required . '>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberChineseName') . '</div>
          </div>
          <div class="col-12 col-md-6">
            <label for="nameEng" class="form-label">' . L('team.memberEnglishName') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="text" class="form-control '.$class.'" name="memberNameEng[]" id="memberNameEng_' . $i . '" placeholder="' . L('team.memberEnglishName') . '" ' . $required . '>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberEnglishName') . '</div>
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-6">
            <label for="age" class="form-label">' . L('team.memberAge') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="number" class="form-control memberAge '.$class.'" name="memberAge[]" id="memberAge_' . $i . '" placeholder="' . L('team.memberAge') . '" min="3" step="1" max="100" ' . $required . '>
            <div class="invalid-feedback memberAgeFeedback">' . L('error.teamEmptyMemberAge') . ' ('.L('info.teamMemberAgeReminder1').')</div>
          </div>
          <div class="col-12 col-md-6">
            <label for="gender" class="form-label">' . L('team.memberGender') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <select class="form-select genderField '.$class.'" name="memberGender[]" id="memberGender_' . $i . '" placeholder="' . L('team.memberGender') . '" ' . $required . '>
              <option value=""></option>
              <option value="M">' . L('gender.male') . '</option>
              <option value="F">' . L('gender.female') . '</option>

            </select>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberGender') . '</div>
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-6">
            <label for="mobile" class="form-label">' . L('team.memberMobile') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="tel" class="form-control '.$class.'" name="memberMobile[]" id="memberMobile_' . $i . '" placeholder="' . L('team.memberMobile') . '" pattern="[0-9]{8}" ' . $required . '>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberMobile') . '</div>
          </div>
          <div class="col-12 col-md-6">
            <label for="email" class="form-label">' . L('team.memberEmail') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="email" class="form-control '.$class.'" name="memberEmail[]" id="memberEmail_' . $i . '" placeholder="' . L('team.memberEmail') . '" pattern="[^@]+@[^@]+\.[a-zA-Z]{2,6}" ' . $required . '>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberEmail') . '</div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-6">
            <label for="emContactName" class="form-label">' . L('team.emContactName') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="text" class="form-control '.$class.'" name="memberEmContactName[]" id="memberEmContactName_' . $i . '" placeholder="' . L('team.emContactName') . '" ' . $required . '>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberEmContactName') . '</div>
          </div>
          <div class="col-12 col-md-6">
            <label for="emContactMobile" class="form-label">' . L('team.emContactMobile') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="tel" class="form-control '.$class.'" name="memberEmContactMobile[]" id="memberEmContactMobile_' . $i . '" placeholder="' . L('team.emContactMobile') . '" pattern="[0-9]{8}" ' . $required . '>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberEmContactMobile') . '</div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-6">
            <label for="gender" class="form-label">'. L('team.memberShirtSize') . ' ' . ($required == 'required' ? '*' : '') . '</label>

            <select class="form-select sizeField '.$class.'" name="memberShirtSize[]" id="memberShirtSize_' . $i . '" placeholder="' . L('team.memberShirtSize') . '" ' . $required . '>
              <option value=""></option>
              <option value="100">Kids 100</option>
              <option value="130">Kids 130</option>
              <option value="150">Kids 150</option>
              <option value="XS">XS</option>
              <option value="S">S</option>
              <option value="M">M</option>
              <option value="L">L</option>
              <option value="XL">XL</option>
            </select>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberShirtSize'). '</div>
          </div>
        </div>         


      </div>
    </div>';
  }  ?>
<!--<body style="background-image: url(images/background/background.jpg);background-position: 50% 0;background-size: cover;"?>-->
<form class="form-signin shadow-lg needs-validation" id="form-regUser" novalidate autocomplete="off">
  <span class="text-info"><i class="fas fa-language fa-lg"></i><?php foreach (Pages\Language::getAvailableLang() as $lang) : ?>
        <a href="<?php Utility\WebSystem::path("lang/" . $lang['langCode']); ?>" class="p-1 text-info"><?= $lang['langName']; ?></a>
  <?php endforeach ?></span>
  <div class="py-5 text-center">
    <!--<img class="d-block mx-auto mb-4" src="https://www.bizwave.hk/wp-content/uploads/2022/02/bizwave-logo.png" width="72">-->
    <h3><?= L('user.regForm'); ?></h3>
      <?php if(date("Y-m-d") > $end_date) { 
        echo "<div class='text-danger h4'>".L("info.registrationEnded")."</div>";
      } ?>    
    <?php if (!team::isJoinable() || (date("Y-m-d") > $end_date)) { ?><div class="text-danger h4"><?=L("info.enquiry");?></div><?php } ?>
  </div>  
  <div class="card" style="width: 100%">    
    <div class="card-body text-primary">
      <img src="<?php Utility\WebSystem::path("images/banner.jpg"); ?>" style="width:100%; margin-bottom:20px">
      <h5><?=L("info.groupInstructionHead");?></h5>
      <ul>
        <li><?=L("info.groupInstructionDetail1");?></li>
        <li><?=L("info.groupInstructionDetail2");?></li>
        <li><?=L("info.groupInstructionDetail3");?></li>
        <li><?=L("info.groupInstructionDetail4");?></li>
        <li><?=L("info.groupInstructionDetail5");?></li>
      </ul>
      <!--<h5><?=L("info.periodInstructionHead");?> (<?=L("info.periodInstructionDetail");?>)</h5>-->
      <h5><?=L("info.periodInstructionHead");?></h5>
      <ul>        
        <li>10:00 - 12:00</li>
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

      <h5><?=L("info.registrationDeadlineHead");?></h5>
      <ul>
        <li><?=L("info.registrationDeadlineDetail");?></li>
      </ul>

      <h5><?=L("info.sizeChartHead");?></h5>
      <img src="<?php Utility\WebSystem::path("images/size_chart.png"); ?>" style="width:100%"><br><br>

      <ul>
        <li><?=L("info.sizeChartDetail1");?><br>
            <table class='table' style='width:100%'>
              <thead>
                <tr>
                  <th></th>
                  <th>XS</th>
                  <th>S</th>
                  <th>M</th>
                  <th>L</th>
                  <th>XL</th>                  
                </tr>
                <tr>
                  <td><?=L("info.sizeChartLabel1");?></td>
                  <td>17.7</td>
                  <td>18.5</td>
                  <td>19.3</td>
                  <td>20.1</td>
                  <td>20.9</td>
                </tr>    
                <tr>
                  <td><?=L("info.sizeChartLabel2");?></td>
                  <td>23.2</td>
                  <td>24</td>
                  <td>24.8</td>
                  <td>25.6</td>
                  <td>26.4</td>
                </tr>                             
              </tead>
            </table>
        </li>
        <li><?=L("info.sizeChartDetail2");?><br>
        <table class='table' style='width:100%'>
              <thead>
                <tr>
                  <th></th>
                  <th>100</th>
                  <th>130</th>
                  <th>150</th>                 
                </tr>
                <tr>
                  <td><?=L("info.sizeChartLabel1");?></td>
                  <td>13</td>
                  <td>15.25</td>
                  <td>16.75</td>
                </tr>    
                <tr>
                  <td><?=L("info.sizeChartLabel2");?></td>
                  <td>15.25</td>
                  <td>18.25</td>
                  <td>20.25</td>
                </tr>                             
              </tead>
            </table>        
        </li>
      </ul>



      <h5><?=L("info.eventDetailTitle");?></h5>
      <ul>
        <!--<li><a target="_blank" href="<?=L("info.eventDetailLink");?>"><?=L("info.eventDetailLink");?></a></li>-->
        <li><a target="_blank" href="<?=cfg('event')['eventLink'];?>"><?=cfg('event')['eventLink'];?></a></li>
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

  <a href="<?=cfg('event')['emailLink'];?>" class="whatsapp-float" target="_blank"><i class="fa fa-envelope my-float"></i></a>
  <!--
  <a href="<?=cfg('event')['emailLink'];?>" class="email-float" target="_blank"><i class="fa fa-envelope my-float"></i></a>
  <a href="<?=cfg('event')['whatsappLink'];?>" class="whatsapp-float" target="_blank"><i class="fa fa-whatsapp my-float"></i></a>
  -->

  <?php if (team::isJoinable() && (date("Y-m-d") <= $end_date)) { ?>
    <div id="promptMsg"></div>
    <div class="card" style="width: 100%; margin-top:20px">
      <div class="card-body">
        <h5 class="card-title text-center"><?= L('team.info'); ?></h5>
        <div class="row">
          <div class="col-12 col-md-6">
            <label for="categoryID" class="form-label"><?= L('team.category'); ?> *</label>
            <select class="form-select" name="categoryID" id="categoryID" placeholder="<?= L('team.category'); ?>" required>
              <option value=""></option>
              <?php
              foreach (category::find_all() as $categoryObj) {
                echo "<option value='" . $categoryObj->id . "'>" . $categoryObj->name . "</option>";
              }
              ?>
            </select>
            <div class="invalid-feedback"><?= L('error.teamEmptyCategory'); ?></div>
          </div>
          <div class="col-12 col-md-6">
            <!--<label for="periodID" class="form-label"><?= L('team.period'); ?> *</label>
            <select class="form-select" name="periodID" id="periodID" placeholder="<?= L('team.period'); ?>" required>
              <option value=""></option>
              <?php
              foreach (period::find_all() as $periodObj) {
                $attr = "";
                $infoMessage = "";
                
                if (team::getParticipantCountByPeriod($periodObj->id) >= $periodObj->maxParticipantCount) {
                  $attr = "disabled";
                  $infoMessage = "(" . L('info.teamPeriodisFull') . ")";
                }

                echo "<option value='" . $periodObj->id . "' " . $attr . ">" . $periodObj->name . " " . $infoMessage . "</option>";
              }
              ?>
            </select>
            <div class="invalid-feedback"><?= L('error.teamEmptyPeriod'); ?></div>-->
            <label for="teamName" class="form-label"><?= L('team.name'); ?> *</label>
            <input type="text" class="form-control" name="teamName" id="teamName" placeholder="<?= L('team.name'); ?>" maxLength="20" required>
            <div class="invalid-feedback"><?= L('error.teamEmptyName'); ?></div>            
          </div>
        </div>        
        

      </div>
    </div>

    <div class="card" style="width: 100%; margin-top:20px">
      <div class="card-body">
        <h5 class="card-title text-center"><?= L('team.leaderInfo'); ?></h5>
        <div class="row">
          <div class="col-12 col-md-6">
            <label for="nameChi" class="form-label"><?= L('team.memberChineseName'); ?> *</label>
            <input type="text" class="form-control" name="leaderNameChi" id="leaderNameChi" placeholder="<?= L('team.memberChineseName'); ?>" required>
            <div class="invalid-feedback"><?= L('error.teamEmptyLeaderChineseName'); ?></div>
          </div>
          <div class="col-12 col-md-6">
            <label for="nameEng" class="form-label"><?= L('team.memberEnglishName'); ?> *</label>
            <input type="text" class="form-control" name="leaderNameEng" id="leaderNameEng" placeholder="<?= L('team.memberEnglishName'); ?>" required>
            <div class="invalid-feedback"><?= L('error.teamEmptyLeaderEnglishName'); ?></div>
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-6">
            <label for="age" class="form-label"><?= L('team.memberAge'); ?> *</label>
            <input type="number" class="form-control" name="leaderAge" id="leaderAge" placeholder="<?= L('team.memberAge'); ?>" required min="15" step="1" max="100">
            <div class="invalid-feedback"><?= L('error.teamEmptyLeaderAge'); ?> (<?= L('info.teamLeaderAgeReminder'); ?>)</div>
          </div>
          <div class="col-12 col-md-6">
            <label for="gender" class="form-label"><?= L('team.memberGender'); ?> *</label>
            <select class="form-select genderField" name="leaderGender" id="leaderGender" placeholder="<?= L('team.memberGender'); ?>" required>
              <option value=""></option>
              <option value="M"><?= L('gender.male'); ?></option>
              <option value="F"><?= L('gender.female'); ?></option>

            </select>
            <div class="invalid-feedback"><?= L('error.teamEmptyLeaderGender'); ?></div>
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-6">
            <label for="mobile" class="form-label"><?= L('team.memberMobile'); ?> *</label>
            <input type="tel" class="form-control" name="leaderMobile" id="leaderMobile" placeholder="<?= L('team.memberMobile'); ?>" pattern="[0-9]{8}" required>
            <div class="invalid-feedback"><?= L('error.teamEmptyLeaderMobile'); ?></div>
          </div>
          <div class="col-12 col-md-6">
            <label for="email" class="form-label"><?= L('team.memberEmail'); ?> *</label>
            <input type="email" class="form-control" name="leaderEmail" id="leaderEmail" placeholder="<?= L('team.memberEmail'); ?>" pattern="[^@]+@[^@]+\.[a-zA-Z]{2,6}" required>
            <div class="invalid-feedback"><?= L('error.teamEmptyLeaderEmail'); ?></div>
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-6">
            <label for="addr1" class="form-label"><?= L('team.memberAddress'); ?> *</label>
            <input type="text" class="form-control" name="leaderAddr1" id="leaderAddr1" placeholder="<?= L('team.memberAddress1'); ?>" required>
            <div class="invalid-feedback"><?= L('error.teamEmptyLeaderAddress'); ?></div>
          </div>
          <div class="col-12 col-md-6">
            <label for="addr2" class="form-label">&nbsp;</label>
            <input type="text" class="form-control" name="leaderAddr2" id="leaderAddr2" placeholder="<?= L('team.memberAddress2'); ?>">
            <div class="invalid-feedback"><?= L('error.teamEmptyLeaderAddress'); ?></div>
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-6">
            <label for="emContactName" class="form-label"><?= L('team.emContactName'); ?> *</label>
            <input type="text" class="form-control" name="leaderEmContactName" id="leaderEmContactName" placeholder="<?= L('team.emContactName'); ?>" required>
            <div class="invalid-feedback"><?= L('error.teamEmptyLeaderEmContactName'); ?></div>
          </div>
          <div class="col-12 col-md-6">
            <label for="emContactMobile" class="form-label"><?= L('team.emContactMobile'); ?> *</label>
            <input type="tel" class="form-control" name="leaderEmContactMobile" id="leaderEmContactMobile" placeholder="<?= L('team.emContactMobile'); ?>" pattern="[0-9]{8}" required>
            <div class="invalid-feedback"><?= L('error.teamEmptyLeaderEmContactMobile'); ?></div>
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-6">
            <label for="gender" class="form-label"><?= L('team.memberShirtSize'); ?> *</label>
            <select class="form-select " name="leaderShirtSize" id="leaderShirtSize" placeholder="<?= L('team.memberShirtSize'); ?>" required>
              <option value=""></option>
              <option value="XS">XS</option>
              <option value="S">S</option>
              <option value="M">M</option>
              <option value="L">L</option>
              <option value="XL">XL</option>
            </select>
            <div class="invalid-feedback"><?= L('error.teamEmptyLeaderShirtSize'); ?></div>
          </div>
        </div> 

      </div>
    </div>
    <div id="memeber_section">
      <?= gen_member_row(1, "required"); ?>
      <?= gen_member_row(2); ?>
      <?= gen_member_row(3); ?>
    </div>
    <!--<div class="card" style="width: 100%; margin-top:20px">
      <div class="card-body">
        <div class="row">
          <div class="col-12 col-md-6">
            <label for="categoryID" class="form-label"><?= L('promo.code'); ?></label>
            <input type="text" class="form-control" name="promoCode" id="promoCode" placeholder="<?= L('promo.code'); ?>">
          </div>
        </div>
      </div>
    </div>-->
    <div class="card" style="width: 100%; margin-top:20px">
      <div class="card-body text-primary">
        <h5><?=L("info.disclaimerHead");?></h5>
        <ul>
          <li><?=L("info.disclaimerDetail");?></li>          
        </ul>
        <h5><?=L("info.parentConsentHead");?></h5>
        <ul>
          <li><?=L("info.parentConsentDetail");?></li>
        </ul>
        <h5><?=L("info.sponsorshipHead");?></h5>
        <ul>
          <li><?=L("info.sponsorshipDetail");?></li>
        </ul>
        <h5><?=L("info.useOfDataHead");?></h5>
        <ul>
          <li><?=L("info.useOfDataDetail");?></li>
        </ul>
      </div>
    </div>    
    <div class="row d-grid gap-2">
      <div class="col-12 rowBtn"><button class="w-100 btn btn-lg btn-primary" type="submit"><?= L("team.submit"); ?></button></div>
    </div>
  <?php } // end check joinable ?> 

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
  // show/hide custom upload
  $("input[type='radio'][name='avatarID']").click(function() {
    if ($(this).val() == "5") {
      $("#image_upload").show();
    } else {
      $("#image_upload").hide();
      $("#customizeImage").val("");
      $("#imagePreview").html("");
    }
  });

  // for selected image instant preview
  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function(e) {
        $('#imagePreview').html("<img src='" + e.target.result + "' style='max-width:300px'>");
      }

      reader.readAsDataURL(input.files[0]);
    }
  }

  $("#customizeImage").change(function() {
    readURL(this);
  });

  $("#categoryID").change(function() {
    var categoryID = $(this).val();
    if(categoryID<=3){
      $(".memberAge").attr("min", 15);
      $(".memberAgeFeedback").text("<?=L('error.teamEmptyMemberAge');?> (<?=L('info.teamMemberAgeReminder1');?>)");
    } else {
      $(".memberAge").attr("min", 3);
      $(".memberAgeFeedback").text("<?=L('error.teamEmptyMemberAge');?> (<?=L('info.teamMemberAgeReminder2');?>)");
    }  

    $(".genderField").each(function(){

      var selectedValue = $(this).val();
      //console.log(selectedValue);

      var content = "<option value=''></option>";

      if(categoryID==1) {        
        content += "<option value='M' "+(selectedValue=='M'?'selected':'')+"><?= L('gender.male'); ?></option>";
      } else if(categoryID==2) {
        content += "<option value='F' "+(selectedValue=='F'?'selected':'')+"><?= L('gender.female'); ?></option>";
      } else {
        content += "<option value='M' "+(selectedValue=='M'?'selected':'')+"><?= L('gender.male'); ?></option>";
        content += "<option value='F' "+(selectedValue=='F'?'selected':'')+"><?= L('gender.female'); ?></option>";
      }      

      $(this).html(content);
      
    });


    $(".sizeField").each(function(){

      var selectedValue = $(this).val();
      //console.log(selectedValue);

      var content = "<option value=''></option>";

      if(categoryID>3) {        

        content += "<option value='100' "+(selectedValue=='100'?'selected':'')+">Kids 100</option>";
        content += "<option value='130' "+(selectedValue=='130'?'selected':'')+">Kids 130</option>";
        content += "<option value='150' "+(selectedValue=='150'?'selected':'')+">Kids 150</option>";

      }

      content += "<option value='XS' "+(selectedValue=='XS'?'selected':'')+">XS</option>";
      content += "<option value='S' "+(selectedValue=='S'?'selected':'')+">S</option>";
      content += "<option value='M' "+(selectedValue=='M'?'selected':'')+">M</option>"; 
      content += "<option value='L' "+(selectedValue=='L'?'selected':'')+">L</option>";
      content += "<option value='XL' "+(selectedValue=='XL'?'selected':'')+">XL</option>";            

      $(this).html(content);

    });

  });

  
  $(".memeberFieldCheck").on("keyup paste change", function() {
    
    var idx = $(this).attr('id').split("_")[1];

    var memberNameChiField = $(this).closest('.card-body').find("#memberNameChi_"+idx);
    var memberNameEngField = $(this).closest('.card-body').find("#memberNameEng_"+idx);
    var memberAgeField = $(this).closest('.card-body').find("#memberAge_"+idx);
    var memberGenderField = $(this).closest('.card-body').find("#memberGender_"+idx);
    var memberMobileField = $(this).closest('.card-body').find("#memberMobile_"+idx);
    var memberEmailField = $(this).closest('.card-body').find("#memberEmail_"+idx);
    var memberEmContactNameField = $(this).closest('.card-body').find("#memberEmContactName_"+idx);
    var memberEmContactMobileField = $(this).closest('.card-body').find("#memberEmContactMobile_"+idx);    
    var memberShirtSizeField = $(this).closest('.card-body').find("#memberShirtSize_"+idx);    

    if($(this).val().length){

      if(!memberNameChiField.val().length){
        memberNameChiField.prop('required',true);
      }

      if(!memberNameEngField.val().length){
        memberNameEngField.prop('required',true);
      }

      if(!memberAgeField.val().length){
        memberAgeField.prop('required',true);
      }

      if(!memberGenderField.val().length){
        memberGenderField.prop('required',true);
      }         

      if(!memberMobileField.val().length){
        memberMobileField.prop('required',true);
      } 

      if(!memberEmailField.val().length){
        memberEmailField.prop('required',true);
      }    

      if(!memberEmContactNameField.val().length){
        memberEmContactNameField.prop('required',true);
      }    

      if(!memberEmContactMobileField.val().length){
        memberEmContactMobileField.prop('required',true);
      }  

      if(!memberShirtSizeField.val().length){
        memberShirtSizeField.prop('required',true);
      }        
      
    }  else {
      
      var empty = true;

      if(memberNameChiField.val().length){
        empty = false;
      }

      if(memberNameEngField.val().length){
        empty = false;
      }

      if(memberAgeField.val().length){
        empty = false;
      }

      if(memberGenderField.val().length){
        empty = false;
      }         

      if(memberMobileField.val().length){
        empty = false;
      } 

      if(memberEmailField.val().length){
        empty = false;
      }    

      if(memberEmContactNameField.val().length){
        empty = false;
      }    

      if(memberEmContactMobileField.val().length){
        empty = false;
      } 
      
      if(memberShirtSizeField.val().length){
        empty = false;
      }        

      if(empty){

        memberNameChiField.prop('required',false);
        memberNameEngField.prop('required',false);
        memberAgeField.prop('required',false);
        memberGenderField.prop('required',false);
        memberMobileField.prop('required',false);
        memberEmailField.prop('required',false);
        memberEmContactNameField.prop('required',false);
        memberEmContactMobileField.prop('required',false);
        memberShirtSizeField.prop('required',false);

      } else {

        memberNameChiField.prop('required',true);
        memberNameEngField.prop('required',true);
        memberAgeField.prop('required',true);
        memberGenderField.prop('required',true);
        memberMobileField.prop('required',true);
        memberEmailField.prop('required',true);
        memberEmContactNameField.prop('required',true);
        memberEmContactMobileField.prop('required',true);
        memberShirtSizeField.prop('required',true);

      }
    } 

  });
  


  <?php 
  if (team::isJoinable() && (date("Y-m-d") <= $end_date)){
  ?>
  var checkoutXhr;
  $('#form-regUser').submit(function(e) {
    e.preventDefault();

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

    /*
    if (!$("input[name='avatarID']:checked").val()) {
      alert('<?= L('error.teamEmptyAvatar'); ?>');
      return;
    }

    if ($("input[name='avatarID']:checked").val() == 5) {
      if ($('#customizeImage').get(0).files.length === 0) {
        alert('<?= L('error.teamEmptyCustomizedAvatar'); ?>');
        return;
      }
    }
    */

    var data = new FormData(this);
    if (checkoutXhr !== undefined && checkoutXhr.readyState != 4) checkoutXhr.abort();
    checkoutXhr = ajaxFunc.apiCall("POST", "team/add", data, "multipart/form-data", ajaxFunc.responseHandle);
  });
  <?php 
  }
  ?>  
</script>
<?php
include("view/layout/endpage.php");
?>