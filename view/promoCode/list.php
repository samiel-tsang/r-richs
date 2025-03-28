<?php
use Controller\role;
include("view/layout/meta.php");
include("view/layout/head.php");
?>
<main class="content">
<div class="funcBar d-flex text-white">
    <span class="funcTitle me-auto"><?=L('menu.promoCode');?></span>
    <div class="funcMenu mx-3 py-2">
<?php /*		<a class="btn" href="<?=$this->pageLink('page.cardSearch', isset($request->get->q)?['q'=>$request->get->q]:[]);?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?=L('Search');?>"><i class="fas fa-1x fa-search"></i></a> */ ?>
        <a class="btn" href="<?=$this->pageLink('page.promoCodeAdd');?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?=L('Add');?>"><i class="fas fa-1x fa-plus-square"></i></a>
    </div>
</div>
<div>
<?php
if (isset($request->get->q)) {
	echo '<div class="d-flex mx-3 my-1"><div class="me-auto mt-2">'.L("search.criteria").' <i class="fas fa-filter"></i>: ';
	
	$hash = $request->get->q;

	echo '</div><div><a class="btn btn-dark" href="'.$this->pageLink('page.promoCodeList', ['pg'=>1]).'">'.L("search.clear").'</a></div></div>';
}
?>
</div>
<?php
if ($itemCount == 0) {
?>
<h4 class="text-center m-3"><?=L('search.noRecord');?></h4>
<?php
} else {
?>
<div class="table-responsive">
<table class="table table-hover">
    <thead>
        <tr>
            <th scope="col"><?=L('ID');?></th>
            <th scope="col"><?=L('promo.name');?></th>
            <th scope="col"><?=L('promo.code');?></th>
            <th scope="col"><?=L('amount');?></th>
            <th scope="col"><?=L('Status');?></th>
            <th scope="col"><?=L('Actions');?></th>
        </tr>
    </thead>
    <tbody>
        <?php
    foreach ($this->ListItem() as $listObj) {
        ?>
        <tr>
            <td scope="row"><?=$listObj->id;?></td>
            <td><?=$listObj->name;?></td>
            <td><?=$listObj->code;?></td>
            <td><?=$listObj->amount;?></td>
            <td><?=L($listObj->statusName);?></td>
            <td>
                <a class="btn btn-md btn-success" href="<?=$this->pageLink('page.promoCodeEdit', ["id"=>$listObj->id]);?>" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=L('Edit');?>"><i class="fas fa-sm fa-edit"></i></a>
                <button class="btn btn-md btn-danger btnDel" type="button" data-toggle="tooltip" data-placement="top" title="<?=L('Delete');?>" data-id="<?=$listObj->id;?>" data-name="<?=$listObj->name;?>"><i class="fas fa-sm fa-trash-alt"></i></button>
            </td>
        </tr>
        <?php
    }
        ?>
    </tbody>
</table>
</div>
<?php
   //include("view/layout/pagination.php");
   $this->pagination();
}
?>
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
     modal.find('#msgBoxLabel').text('Are you sure to Delete?');
     modal.find('.modal-body').text('Are you sure to delete promoCode - '+button.data('name')+'?');
     
     modal.find('#msgBoxBtnPri').one('click', function (event) {
        $('#msgBox').modal('hide');
        
        ajaxFunc.apiCall("DELETE", "promocode/"+button.data('id'), null, null, ajaxFunc.responseHandle);
     });
  }).modal('show')
});
</script>
<?
include("view/layout/endpage.php");
?>