<?php
use Controller\user;
use Controller\role;
include("view/layout/meta.php");
include("view/layout/head.php");
?>
<main class="content">
<div class="funcBar d-flex text-white">
    <span class="funcTitle me-auto"><?=L('menu.user');?> - <?=$obj->username;?></span>
    <div class="funcMenu mx-3 py-2">     
<?php
if ($user->roleID == 1) {
?>
        <a class="btn" href="<?=$this->pageLink('page.userList', ["pg"=>1]);?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?=L('ToList');?>"><i class="fas fa-1x fa-list-alt"></i></a>
<?php 
}
?>
    </div>
</div>
<div class="container">
  <div class="card my-3">
      <div class="card-top">
        <div class="row my-3 mx-1">
            <div class="col-md-6">
              <h5 class="card-title"><?=L('user.info');?></h5>
            </div>      
            <div class="col-md-6 text-end">
              <a class="btn btn-primary" href="<?=$this->pageLink('page.userEdit', ["id"=>$obj->id]);?>" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=L('Edit');?>"><i class="fas fa-sm fa-edit"></i> <?=L('Edit');?></a>
<?php
if ($user->roleID == 1) {
?>
              <button class="btn btn-danger btnDel" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=L('Suspend');?>" data-id="<?=$obj->id;?>" data-username="<?=$obj->username;?>"><i class="fas fa-sm fa-trash-alt"></i> <?=L('Suspend');?></button>
<?php 
}
?>
            </div>
        </div>
      </div>

      <div class="card-body">
        <div class="row my-3">
            <div class="col-md-3 font-weight-bold"><?=L('ID');?></div>
            <div class="col-md-3"><?=$obj->id;?></div>
          <div class="col-md-3 font-weight-bold"><?=L('Status');?></div>
          <div class="col-md-3"><?=L($obj->statusName);?></div>
        </div>  

        <div class="row my-3">
            <div class="col-md-3 font-weight-bold"><?=L('user.userName');?></div>
            <div class="col-md-3"><?=$obj->username;?></div>            
            <div class="col-md-3 font-weight-bold"><?=L('Role');?></div>
            <div class="col-md-3"><?=role::find($obj->roleID)->name;?></div>
        </div>    

        <div class="row my-3">
            <div class="col-md-3 font-weight-bold"><?=L('user.email');?></div>
            <div class="col-md-3"><?=$obj->email;?></div>            
        </div>       

        <div class="row my-3">
          <div class="col-md-3 font-weight-bold"><?=L('user.lastLoginIP');?></div>
          <div class="col-md-3"><?=$obj->loginIPAddr;?></div>
          <div class="col-md-3 font-weight-bold"><?=L('user.lastLoginTime');?></div>
          <div class="col-md-3"><?=Utility\WebSystem::displayDate($obj->lastLogin, 'm/d/Y H:i:s');?></div>
        </div>  

        <div class="row my-3">
          <div class="col-md-3 font-weight-bold"><?=L('createTime');?></div>
          <div class="col-md-3"><?=Utility\WebSystem::displayDate($obj->createDate, 'm/d/Y');?></div>
          <div class="col-md-3 font-weight-bold"><?=L('createBy');?></div>
          <div class="col-md-3"><?=user::find($obj->createBy)->username;?></div>
        </div>        

        <div class="row my-3">
          <div class="col-md-3 font-weight-bold"><?=L('modifyTime');?></div>
          <div class="col-md-3"><?=Utility\WebSystem::displayDate($obj->modifyDate, 'm/d/Y');?></div>
          <div class="col-md-3 font-weight-bold"><?=L('modifyBy');?></div>
          <div class="col-md-3"><?=user::find($obj->modifyBy)->username;?></div>
        </div>                      

      </div>
  </div>
<?php
/*
if ($obj->roleID != 1 && $user->roleID == 1) {
?>
  <div class="card my-3">
      <div class="card-top">
        <div class="row my-3 mx-1">
            <div class="col-md-6">
              <h5 class="card-title"><?=L('card.info');?></h5>
            </div>      
            <div class="col-md-6 text-end">
              <a class="btn btn-primary" href="<?=$this->pageLink('page.cardAdd', ['userID'=>$obj->id]);?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?=L('Add');?>"><i class="fas fa-1x fa-plus-square"></i></a>
            </div>
        </div>
      </div>

      <div class="card-body">
<div class="table-responsive">
<table class="table table-hover">
    <thead>
        <tr>
            <th scope="col"><?=L('card.name');?></th>
            <th scope="col"><?=L('card.url');?></th>
            <th scope="col"><?=L('card.firebaseUrl');?></th>
            <th scope="col"><?=L('createTime');?></th>
            <th scope="col"><?=L('modifyTime');?></th>
            <th scope="col"><?=L('Actions');?></th>
        </tr>
    </thead>
    <tbody>
        <?php
    $sql = Database\Sql::select('card')->where(['userID', '=', $obj->id]);
    $stm = $sql->prepare();
    $stm->setFetchMode(\PDO::FETCH_OBJ);
    $stm->execute();
    foreach ($stm as $listObj) {
        ?>
        <tr>
            <td scope="row"><?=$listObj->cardName;?></td>
            <td class="text-truncate" style="max-width: 150px;"><?=$listObj->cardUrl;?></td>
            <td><?=$listObj->firebaseUrl;?></td>
            <td><?=$listObj->createDate;?></td>
            <td><?=$listObj->modifyDate;?></td>
            <td>
                <a class="btn btn-md btn-primary" href="<?=$this->pageLink('page.cardInfo', ["id"=>$listObj->id]);?>" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=L('Information');?>"><i class="fas fa-sm fa-info-circle"></i></a>
                <a class="btn btn-md btn-success" href="<?=$this->pageLink('page.cardEdit', ["id"=>$listObj->id]);?>" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=L('Edit');?>"><i class="fas fa-sm fa-edit"></i></a>
            </td>
        </tr>
        <?php
    }
        ?>
    </tbody>
</table>
</div>
      </div>
  </div>
<?php
}
*/
?>
</div>
</main>
<?php
include("view/layout/foot.php");
include("view/layout/js.php");
?>
<script>
$(".btnDel").click(function (e) {
  e.preventDefault();
  
  $('#msgBox').one('show.bs.modal', function (ev) {
     var button = $(e.currentTarget);
     var modal = $(this);
     modal.find('#msgBoxLabel').text('Are you sure to Suspend?');
     modal.find('.modal-body').text('Are you sure to suspend user - '+button.data('username')+'?');
     
     modal.find('#msgBoxBtnPri').one('click', function (event) {
        $('#msgBox').modal('hide');
        
        ajaxFunc.apiCall("DELETE", "user/"+button.data('id'), null, null, ajaxFunc.responseHandle);
     });
  }).modal('show')
});
</script>
<?php
include("view/layout/endpage.php");
?>