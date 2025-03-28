<?php
use Controller\user;
use Controller\role;
include("view/layout/meta.php");
include("view/layout/head.php");
?>
<main class="content">
<div class="funcBar d-flex text-white">
    <span class="funcTitle me-auto"><?=L('card.info');?> - <?=$obj->cardName;?></span>
    <div class="funcMenu mx-3 py-2">   
<?php if (!empty($obj->userID)) { ?>     
        <a class="btn" href="<?=$this->pageLink('page.userInfo', ["id"=>$obj->userID]);?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?=L('user.info');?>"><i class="fas fa-1x fa-list-alt"></i></a>
<?php } ?>
    </div>
</div>
<div class="container">
  <div class="card my-3">
      <div class="card-top">
        <div class="row my-3 mx-1">
            <div class="col-md-6">
              <h5 class="card-title"><?=L('card.info');?></h5>
            </div>      
            <div class="col-md-6 text-end">
<?php if (!empty($obj->userID)) { ?>
              <a class="btn btn-primary" href="<?=$this->pageLink('page.userInfo', ["id"=>$obj->userID]);?>" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=L('user.info');?>"><i class="fas fa-sm fa-edit"></i> <?=L('user.info');?></a>
<?php } ?>
              <a class="btn btn-md btn-success" href="<?=$this->pageLink('page.cardEdit', ["id"=>$obj->id]);?>" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=L('Edit');?>"><i class="fas fa-sm fa-edit"></i> <?=L('Edit');?></a>
            </div>
        </div>
      </div>

      <div class="card-body">
        <div class="row my-3">
            <div class="col-md-3 font-weight-bold"><?=L('ID');?></div>
            <div class="col-md-3"><?=$obj->id;?></div>
            <div class="col-md-3 font-weight-bold"><?=L('user.userName');?></div>
            <div class="col-md-3"><?=$obj->user->username;?></div>    
        </div>  

        <div class="row my-3">
            <div class="col-md-3 font-weight-bold"><?=L('card.name');?></div>
            <div class="col-md-3"><?=$obj->cardName;?></div>            
            <div class="col-md-3 font-weight-bold"><?=L('card.firebaseUrl');?></div>
            <div class="col-md-3"><?=$obj->firebaseUrl;?></div>
        </div>          

        <div class="row my-3">
          <div class="col-md-3 font-weight-bold"><?=L('card.url');?></div>
          <div class="col-md-9"><?=$obj->cardUrl;?></div>
        </div>  

        <div class="row my-3">
          <div class="col-md-3 font-weight-bold"><?=L('createTime');?></div>
          <div class="col-md-3"><?=Utility\WebSystem::displayDate($obj->createDate, 'm/d/Y');?></div>
          <div class="col-md-3 font-weight-bold"><?=L('modifyTime');?></div>
          <div class="col-md-3"><?=Utility\WebSystem::displayDate($obj->modifyDate, 'm/d/Y');?></div>
        </div>                      

      </div>
  </div>
<?php
$eventStats_7 = $obj->stat_7->eventStatistics();
$eventStats_14 = $obj->stat_14->eventStatistics();
$eventStats_30 = $obj->stat_30->eventStatistics();
?>
  <div class="card my-3">
      <div class="card-top">
        <div class="row my-3 mx-1">
            <div class="col-md-6">
              <h5 class="card-title"><?=L('card.stats');?></h5>
            </div>      
            <div class="col-md-6 text-end">

            </div>
        </div>
      </div>

      <div class="card-body">
        <div class="row my-3">
            <div class="col-md-3 font-weight-bold"><?=L('card.stats.clicks');?> (7 <?=L('days');?>)</div>
            <div class="col-md-3"><?=count($eventStats_7->clicks());?></div>
        </div>  
        <div class="row my-3">
            <div class="col-md-3 font-weight-bold"><?=L('card.stats.clicks');?> (14 <?=L('days');?>)</div>
            <div class="col-md-3"><?=count($eventStats_14->clicks());?></div>
        </div>  
        <div class="row my-3">
            <div class="col-md-3 font-weight-bold"><?=L('card.stats.clicks');?> (30 <?=L('days');?>)</div>
            <div class="col-md-3"><?=count($eventStats_30->clicks());?></div>
        </div>                      
      </div>
  </div>
</div>
</main>
<?php
include("view/layout/foot.php");
include("view/layout/js.php");
include("view/layout/endpage.php");
?>