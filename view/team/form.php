<?php
use Controller\category;
use Controller\period;
use Controller\defaultAvatar;
use Controller\team;

include("view/layout/meta.php");
include("view/layout/head.php");

if (isset($this->obj)) {
    $stmTL = Database\Sql::select("teamMember")->where(['teamID', '=', $this->obj['id']])->where(['roleID', '=', 1])->prepare();
    $stmTL->execute();
    $leaderObj = $stmTL->fetch(\PDO::FETCH_OBJ);

    $stmTM = Database\Sql::select("teamMember")->where(['teamID', '=', $this->obj['id']])->where(['roleID', '=', 2])->prepare();
    $stmTM->execute();
    $memberCount = $stmTM->rowCount();

    $avatarObj = defaultAvatar::find_by_url($this->obj['teamImage']);
    if(!is_null($avatarObj)){
        $avatarID = $avatarObj->id;
    } else {
        $avatarID = 5;
    }    
}

$this->setHasContainer(true);
?>
<style>
    .btn-outline-dark {
        border: none;
    }
</style>
<main class="content">
    <div class="funcBar d-flex text-white">
        <span class="funcTitle me-auto"><?= L('team.info'); ?></span>
        <div class="funcMenu mx-3 py-2">
            <a class="btn" href="<?=$this->pageLink('page.teamList', ["pg"=>1]);?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?=L('ToList');?>"><i class="fas fa-1x fa-list-alt"></i></a>
        </div>
    </div>
    <form id="form-regUser" class="container my-3 needs-validation" novalidate autocomplete="off">
        <input type="hidden" name="id" value="<?= (isset($this->obj)) ? $this->obj['id'] : ''; ?>">
        <input type="hidden" name="isBackend" value="1">


        <div class="card my-3">
            <div class="card-top">
                <div class="row my-3 mx-1">
                    <div class="col-md-6">
                        <h5 class="card-title"><?= (isset($this->obj)) ? L('Edit') . " " . $this->obj['teamName'] : L('Add'); ?> <?= L('Record'); ?></h5>
                    </div>
                    <div class="col-md-6 text-end"></div>
                </div>
            </div>
            <div class="card" style="width: 100%">    
                <div class="card-body text-primary">
                <h5><?=L("info.groupInstructionHead");?></h5>
                <ul>
                    <li><?=L("info.groupInstructionDetail1");?></li>
                    <li><?=L("info.groupInstructionDetail2");?></li>
                </ul>
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
            <div class="card-body">
                <div class="card" style="width: 100%;">
                    <div class="card-body">
                        <h5 class="card-title text-center"><?= L('team.info'); ?></h5>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label for="categoryID" class="form-label"><?= L('team.category'); ?> *</label>
                                <select class="form-select" name="categoryID" id="categoryID" placeholder="<?= L('team.category'); ?>" required>
                                    <option value=""></option>
                                    <?php
                                    foreach (category::find_all() as $categoryObj) {
                                        echo "<option value='" . $categoryObj->id . "' ";
                                        if (isset($this->obj)) {
                                            if ($this->obj['categoryID'] == $categoryObj->id) {
                                                echo "selected ";
                                            }
                                        }
                                        echo ">" . $categoryObj->name . "</option>";
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback"><?= L('error.teamEmptyCategory'); ?></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <!--
                                <label for="periodID" class="form-label"><?= L('team.period'); ?> *</label>
                                <select class="form-select" name="periodID" id="periodID" placeholder="<?= L('team.period'); ?>" required>
                                    <option value=""></option>
                                    <?php
                                    foreach (period::find_all() as $periodObj) {
                                        $attr = "";
                                        $infoMessage = "";
                                        //if (team::getTeamCountByPeriod($periodObj->id) >= cfg('event')['maxTeamCount']) {
                                        if (team::getParticipantCountByPeriod($periodObj->id) >= $periodObj->maxParticipantCount) {                                            
                                          $attr = "disabled";
                                          $infoMessage = "(" . L('info.teamPeriodisFull') . ")";
                                        }                                        
                                        echo "<option value='" . $periodObj->id . "' ";
                                        if (isset($this->obj)) {
                                            if ($this->obj['periodID'] == $periodObj->id) {
                                                echo "selected ";
                                            }
                                        }
                                        echo ">" . $periodObj->name . " ". $infoMessage ."</option>";
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback"><?= L('error.teamEmptyPeriod'); ?></div>-->
                                
                                    <label for="teamName" class="form-label"><?= L('team.name'); ?> *</label>
                                    <input type="text" class="form-control" name="teamName" id="teamName" placeholder="<?= L('team.name'); ?>" maxLength="20" value="<?= $this->obj['teamName']; ?>" required>
                                    <div class="invalid-feedback"><?= L('error.teamEmptyName'); ?></div>
                                
                            </div>
                        </div>
                        <!--
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <label for="avatarID" class="form-label"><?= L('team.avatar'); ?> *</label>
                                <div class="row defaultImage">
                                    <?php
                                    foreach (defaultAvatar::find_all() as $avatarObj) {
                                    ?>
                                        <div class="col-xs-2 col-sm-2 col-md-2">
                                            <input class="btn-check form-check-input" type="radio" name="avatarID" id="avatarID_<?php echo $avatarObj->id; ?>" value="<?php echo $avatarObj->id; ?>" <?php if($avatarID==$avatarObj->id) echo "checked";?>>
                                            <label class="btn btn-outline-dark" for="avatarID_<?php echo $avatarObj->id; ?>">
                                                <img src="<?php echo isset($this->obj)?"../../":"../";?><?php echo $avatarObj->url; ?>" style="width:100%">
                                            </label>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="invalid-feedback"><?= L('error.teamEmptyAvatar'); ?></div>
                            </div>
                        </div>

                        <div class="row" id="image_upload" style="display:none">
                            <?php if(isset($this->obj)){ ?>                            
                            <div class="col-12 col-md-6">
                                <label for="avatarID" class="form-label"><?= L('team.uploadedAvatar'); ?></label>                                
                                <div id="originalImage"><img width='170' src='<?php echo isset($this->obj)?"../../":"../";?><?=$this->obj['teamImage'];?>'></div>                                
                            </div>                        
                            <?php } ?>
                            <div class="col-12 col-md-6">
                                <label for="avatarID" class="form-label"><?=isset($this->obj)?L('team.changeUploadedAvatar'):L('team.avatar'); ?> (自選上載)</label>
                                <input type="file" class="form-control" name="customizeImage" id="customizeImage" accept="image/*">
                                <div id="imagePreview"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label for="teamName" class="form-label"><?= L('team.name'); ?> *</label>
                                <input type="text" class="form-control" name="teamName" id="teamName" placeholder="<?= L('team.name'); ?>" maxLength="20" value="<?= $this->obj['teamName']; ?>" required>
                                <div class="invalid-feedback"><?= L('error.teamEmptyName'); ?></div>
                            </div>
                        </div>-->

                    </div>
                </div>

                <div class="card" style="width: 100%; margin-top:20px">
                    <div class="card-body">
                        <h5 class="card-title text-center"><?= L('team.leaderInfo'); ?></h5>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label for="nameChi" class="form-label"><?= L('team.memberChineseName'); ?> *</label>
                                <input type="text" class="form-control" name="leaderNameChi" id="leaderNameChi" placeholder="<?= L('team.memberChineseName'); ?>" value="<?= $leaderObj->nameChi; ?>" required>
                                <div class="invalid-feedback"><?= L('error.teamEmptyLeaderChineseName'); ?></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="nameEng" class="form-label"><?= L('team.memberEnglishName'); ?> *</label>
                                <input type="text" class="form-control" name="leaderNameEng" id="leaderNameEng" placeholder="<?= L('team.memberEnglishName'); ?>" value="<?= $leaderObj->nameEng; ?>" required>
                                <div class="invalid-feedback"><?= L('error.teamEmptyLeaderEnglishName'); ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label for="age" class="form-label"><?= L('team.memberAge'); ?> *</label>
                                <input type="number" class="form-control" name="leaderAge" id="leaderAge" placeholder="<?= L('team.memberAge'); ?>" min="15" step="1" max="100" value="<?= $leaderObj->age; ?>" required>
                                <div class="invalid-feedback"><?= L('error.teamEmptyLeaderAge'); ?> (<?= L('info.teamLeaderAgeReminder'); ?>)</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="gender" class="form-label"><?= L('team.memberGender'); ?> *</label>
                                <select class="form-select" name="leaderGender" id="leaderGender" placeholder="<?= L('team.memberGender'); ?>" required>
                                    <option value=""></option>
                                    <option value="M" <? if ($leaderObj->gender == "M") echo "selected"; ?>><?= L('gender.male'); ?></option>
                                    <option value="F" <? if ($leaderObj->gender == "F") echo "selected"; ?>><?= L('gender.female'); ?></option>

                                </select>
                                <div class="invalid-feedback"><?= L('error.teamEmptyLeaderGender'); ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label for="mobile" class="form-label"><?= L('team.memberMobile'); ?> *</label>
                                <input type="tel" class="form-control" name="leaderMobile" id="leaderMobile" placeholder="<?= L('team.memberMobile'); ?>" value="<?= $leaderObj->mobile; ?>" pattern="[0-9]{8}" required>
                                <div class="invalid-feedback"><?= L('error.teamEmptyLeaderMobile'); ?></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label"><?= L('team.memberEmail'); ?> *</label>
                                <input type="email" class="form-control" name="leaderEmail" id="leaderEmail" placeholder="<?= L('team.memberEmail'); ?>" pattern="[^@]+@[^@]+\.[a-zA-Z]{2,6}" value="<?= $leaderObj->email; ?>" required>
                                <div class="invalid-feedback"><?= L('error.teamEmptyLeaderEmail'); ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label for="addr1" class="form-label"><?= L('team.memberAddress'); ?> *</label>
                                <input type="text" class="form-control" name="leaderAddr1" id="leaderAddr1" placeholder="<?= L('team.memberAddress1'); ?>" value="<?= $leaderObj->addr1; ?>" required>
                                <div class="invalid-feedback"><?= L('error.teamEmptyLeaderAddress'); ?></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="addr2" class="form-label">&nbsp;</label>
                                <input type="text" class="form-control" name="leaderAddr2" id="leaderAddr2" placeholder="<?= L('team.memberAddress2'); ?>" value="<?= $leaderObj->addr2; ?>">
                                <div class="invalid-feedback"><?= L('error.teamEmptyLeaderAddress'); ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label for="emContactName" class="form-label"><?= L('team.emContactName'); ?> *</label>
                                <input type="text" class="form-control" name="leaderEmContactName" id="leaderEmContactName" placeholder="<?= L('team.emContactName'); ?>" value="<?= $leaderObj->emContactName; ?>" required>
                                <div class="invalid-feedback"><?= L('error.teamEmptyLeaderEmContactName'); ?></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="emContactMobile" class="form-label"><?= L('team.emContactMobile'); ?> *</label>
                                <input type="tel" class="form-control" name="leaderEmContactMobile" id="leaderEmContactMobile" placeholder="<?= L('team.emContactMobile'); ?>" value="<?= $leaderObj->emContactMobile; ?>" pattern="[0-9]{8}" required>
                                <div class="invalid-feedback"><?= L('error.teamEmptyLeaderEmContactMobile'); ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label for="gender" class="form-label"><?= L('team.memberShirtSize'); ?> *</label>
                                <select class="form-select" name="leaderShirtSize" id="leaderShirtSize" placeholder="<?= L('team.memberShirtSize'); ?>" required>
                                <option value=""></option>
                                <option value="XS" <?php if ($leaderObj->shirtSize=="XS") echo "selected";?>>XS</option>
                                <option value="S" <?php if ($leaderObj->shirtSize=="S") echo "selected";?>>S</option>
                                <option value="M" <?php if ($leaderObj->shirtSize=="M") echo "selected";?>>M</option>
                                <option value="L" <?php if ($leaderObj->shirtSize=="L") echo "selected";?>>L</option>
                                <option value="XL" <?php if ($leaderObj->shirtSize=="XL") echo "selected";?>>XL</option>
                                </select>
                                <div class="invalid-feedback"><?= L('error.teamEmptyLeaderShirtSize'); ?></div>
                            </div>
                        </div> 

                    </div>
                </div>
                <div id="memeber_section">

                    <?php

                        if (isset($this->obj)) {
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



                <?php
                function gen_member_row($i = 1, $memeberInfo = [], $required = "")
                {

                    return '
    <div class="card" style="width: 100%; margin-top:20px" id="member_' . $i . '">
      <div class="card-body">        
        <h5 class="card-title text-center">' . L('team.member') . ' ' . $i . ' ' . L('team.memberInfo') . '</h5>
        <div class="row">
          <div class="col-12 col-md-6">
            <label for="nameChi" class="form-label">' . L('team.memberChineseName') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="text" class="form-control" name="memberNameChi[]" id="memberNameChi_' . $i . '" placeholder="' . L('team.memberChineseName') . '" value="'.$memeberInfo['nameChi'].'" ' . $required . '>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberChineseName') . '</div>
          </div>
          <div class="col-12 col-md-6">
            <label for="nameEng" class="form-label">' . L('team.memberEnglishName') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="text" class="form-control" name="memberNameEng[]" id="memberNameEng_' . $i . '" placeholder="' . L('team.memberEnglishName') . '" value="'.$memeberInfo['nameEng'].'" ' . $required . '>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberEnglishName') . '</div>
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-6">
            <label for="age" class="form-label">' . L('team.memberAge') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="number" class="form-control" name="memberAge[]" id="memberAge_' . $i . '" placeholder="' . L('team.memberAge') . '" min="3" step="1" max="100" value="'.$memeberInfo['age'].'" ' . $required . '>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberAge') . '</div>
          </div>
          <div class="col-12 col-md-6">
            <label for="gender" class="form-label">' . L('team.memberGender') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <select class="form-select" name="memberGender[]" id="memberGender_' . $i . '" placeholder="' . L('team.memberGender') . '" ' . $required . '>
              <option value=""></option>
              <option value="M" '.($memeberInfo['gender']=="M"?"selected":"").' >' . L('gender.male') . '</option>
              <option value="F" '.($memeberInfo['gender']=="F"?"selected":"").' >' . L('gender.female') . '</option>

            </select>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberGender') . '</div>
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-6">
            <label for="mobile" class="form-label">' . L('team.memberMobile') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="tel" class="form-control" name="memberMobile[]" id="memberMobile_' . $i . '" placeholder="' . L('team.memberMobile') . '" value="'.$memeberInfo['mobile'].'" pattern="[0-9]{8}" ' . $required . '>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberMobile') . '</div>
          </div>
          <div class="col-12 col-md-6">
            <label for="email" class="form-label">' . L('team.memberEmail') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="email" class="form-control" name="memberEmail[]" id="memberEmail_' . $i . '" placeholder="' . L('team.memberEmail') . '" pattern="[^@]+@[^@]+\.[a-zA-Z]{2,6}" value="'.$memeberInfo['email'].'" ' . $required . '>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberEmail') . '</div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-6">
            <label for="emContactName" class="form-label">' . L('team.emContactName') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="text" class="form-control" name="memberEmContactName[]" id="memberEmContactName_' . $i . '" placeholder="' . L('team.emContactName') . '" value="'.$memeberInfo['emContactName'].'" ' . $required . '>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberEmContactName') . '</div>
          </div>
          <div class="col-12 col-md-6">
            <label for="emContactMobile" class="form-label">' . L('team.emContactMobile') . ' ' . ($required == 'required' ? '*' : '') . '</label>
            <input type="tel" class="form-control" name="memberEmContactMobile[]" id="memberEmContactMobile_' . $i . '" placeholder="' . L('team.emContactMobile') . '" value="'.$memeberInfo['emContactMobile'].'" pattern="[0-9]{8}" ' . $required . '>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberEmContactMobile') . '</div>
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-6">
            <label for="gender" class="form-label">'. L('team.memberShirtSize') . ' ' . ($required == 'required' ? '*' : '') . '</label>

            <select class="form-select" name="memberShirtSize[]" id="memberShirtSize_' . $i . '" placeholder="' . L('team.memberShirtSize') . '" ' . $required . '>
              <option value=""></option>
              <option value="100" '.($memeberInfo['shirtSize']=="100"?"selected":"").'>Kids 100</option>
              <option value="130" '.($memeberInfo['shirtSize']=="130"?"selected":"").'>Kids 130</option>
              <option value="150" '.($memeberInfo['shirtSize']=="150"?"selected":"").'>Kids 150</option>
              <option value="XS" '.($memeberInfo['shirtSize']=="XS"?"selected":"").'>XS</option>
              <option value="S" '.($memeberInfo['shirtSize']=="S"?"selected":"").'>S</option>
              <option value="M" '.($memeberInfo['shirtSize']=="M"?"selected":"").'>M</option>
              <option value="L" '.($memeberInfo['shirtSize']=="L"?"selected":"").'>L</option>
              <option value="XL" '.($memeberInfo['shirtSize']=="XL"?"selected":"").'>XL</option>
            </select>
            <div class="invalid-feedback">' . L('error.teamEmptyMemberShirtSize'). '</div>
          </div>

      </div>
    </div>';
                }  ?>


                <hr>
                <div class="card" style="width: 100%; margin-top:20px">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label for="categoryID" class="form-label"><?= L('promo.code'); ?></label>
                                <input type="text" class="form-control" name="promoCode" id="promoCode" placeholder="<?= L('promo.code'); ?>" value="<?=$this->obj['promoCode'] ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="status" class="form-label"><?= L('Status'); ?> *</label>
                                <select class="form-select" name="status" id="status" placeholder="<?= L('Status'); ?>" required>
                                    <option value="1" <?php if($this->obj['status'] == 1) echo "selected"; ?>><?=L('Enabled');?></option>
                                    <option value="2" <?php if($this->obj['status'] == 2) echo "selected"; ?>><?=L('Disabled');?></option>
                                </select>
                                <div class="invalid-feedback"><?= L('error.teamEmptyCategory'); ?></div>
                            </div>                            
                        </div>
                    </div>
                </div>
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

                <?php $this->rowSubmit((isset($this->obj)) ? L('Edit') : L('Add')); ?>
            </div>
        </div>
    </form>
</main>
<?php
include("view/layout/foot.php");
include("view/layout/js.php");
?>
<script>
    // show/hide custom upload
    <?php if (isset($this->obj)) {
            if($avatarID==5) { ?>
                $("#image_upload").show();
            
    <?php   } 
        } ?>

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

    $('#form-regUser').submit(function(e) {
        e.preventDefault();

        this.classList.add('was-validated');
        if (!this.checkValidity()) {
            var errorElements = document.querySelectorAll(
            "input.form-control:invalid, select.form-select:invalid");
            
            $('html, body').animate({
            scrollTop: $(errorElements[0]).offset().top-100
            }, 200);

            return;
        }

        /*
        if (!$("input[name='avatarID']:checked").val()) {
            alert('<?= L('error.teamEmptyAvatar'); ?>');
            return;
        }

        if ($("input[name='avatarID']:checked").val() == 5) {
            <?php if(!isset($this->obj)) { ?>
            if ($('#customizeImage').get(0).files.length === 0) {
                alert('<?= L('error.teamEmptyCustomizedAvatar'); ?>');
                return;
            }
            <?php } ?>
        }
        */
        var data = new FormData(this);
        ajaxFunc.apiCall("POST", "team/<?=(isset($this->obj))?$this->obj['id']:'add';?>", data, "multipart/form-data", ajaxFunc.responseHandle);
    });
</script>
<?php
include("view/layout/endpage.php");
?>