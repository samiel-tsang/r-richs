<?php
use Controller\role;
include("view/layout/meta.php");
include("view/layout/head.php");
?>
<main class="content">
<div class="funcBar d-flex text-white">
    <span class="funcTitle me-auto"><?=L('menu.paypal');?></span>
    <div class="funcMenu mx-3 py-2">
<?php /*		<a class="btn" href="<?=$this->pageLink('page.cardSearch', isset($request->get->q)?['q'=>$request->get->q]:[]);?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?=L('Search');?>"><i class="fas fa-1x fa-search"></i></a> 
        <a class="btn" href="<?=$this->pageLink('page.cardAdd');?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?=L('Add');?>"><i class="fas fa-1x fa-plus-square"></i></a> */ ?>
    </div>
</div>
<div>
<?php
if (isset($request->get->q)) {
	echo '<div class="d-flex mx-3 my-1"><div class="me-auto mt-2">'.L("search.criteria").' <i class="fas fa-filter"></i>: ';
	
	$hash = $request->get->q;

	echo '</div><div><a class="btn btn-dark" href="'.$this->pageLink('page.txnList', ['pg'=>1]).'">'.L("search.clear").'</a></div></div>';
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
            <th scope="col"><?=L('team.name');?></th>
            <th scope="col"><?=L('txn.txnOrder');?></th>
            <th scope="col"><?=L('txn.txnPayerID');?></th>
            <th scope="col"><?=L('txn.txncode');?></th>
            <th scope="col"><?=L('amount');?></th>
            <th scope="col"><?=L('remark');?></th>
            <th scope="col"><?=L('Status');?></th>
            <th scope="col"><?=L('createTime');?></th>
        </tr>
    </thead>
    <tbody>
        <?php
    foreach ($this->ListItem() as $listObj) {
        ?>
        <tr>
            <td scope="row"><?=$listObj->id;?></td>
            <td><?=$listObj->teamName;?></td>
            <td><?=$listObj->txnOrder;?></td>
            <td><?=$listObj->txnPayerID;?></td>
            <td><?=$listObj->txnCode;?></td>
            <td><?=$listObj->amount;?></td>
            <td><?=$listObj->remark;?></td>
            <td><?=L($listObj->statusName);?></td>
            <td><?=$listObj->createDate;?></td>
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
include("view/layout/endpage.php");
?>