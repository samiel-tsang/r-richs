<?php
use Controller\role;
include("view/layout/meta.php");
include("view/layout/head.php");
?>
<main class="content">
<div class="funcBar d-flex text-white">
    <span class="funcTitle me-auto"><?=L('menu.team');?></span>
    <div class="funcMenu mx-3 py-2">
        <a class="btn" href="<?=$this->pageLink('team.export');?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Export"><i class="fas fa-1x fa-file-download"></i></a>
		<a class="btn" href="<?=$this->pageLink('page.teamSearch', isset($request->get->q)?['q'=>$request->get->q]:[]);?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?=L('Search');?>"><i class="fas fa-1x fa-search"></i></a>
        <a class="btn" href="<?=$this->pageLink('page.teamAdd');?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?=L('Add');?>"><i class="fas fa-1x fa-plus-square"></i></a>
        <button class="btn btnConfirmAll" type="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?= L('ConfirmSelected'); ?>"> <i class="fa-solid fa-clipboard-check"></i></button>
    </div>
</div>
<div>
<?php
$this->setOrderFieldSeq('id', 'ASC');
$qArr = [];
if (isset($request->get->q)) {
    $qArr = ['q' => $request->get->q];
	echo '<div class="d-flex mx-3 my-1"><div class="me-auto mt-2">'.L("search.criteria").' <i class="fas fa-filter"></i>: ';
	
	$hash = $request->get->q;
    if (!empty($_SESSION['search'][$hash]['teamName']))
       echo '<span class="badge bg-dark ms-2">'.L("team.name").': <span class="font-italic">'.$_SESSION['search'][$hash]['teamName'].'</span></span>';

    if (!empty($_SESSION['search'][$hash]['categoryID'])) {
        $stm = Database\Sql::select('category')->where(['id', '=', $_SESSION['search'][$hash]['categoryID']])->prepare();
        $stm->execute();
        $obj = $stm->fetch();
        
        echo '<span class="badge bg-dark ms-2">'.L("team.category").': <span class="font-italic">'.$obj['name'].'</span></span>';
    }
    if (!empty($_SESSION['search'][$hash]['periodID'])) {
        $stm = Database\Sql::select('period')->where(['id', '=', $_SESSION['search'][$hash]['periodID']])->prepare();
        $stm->execute();
        $obj = $stm->fetch();
        
        echo '<span class="badge bg-dark ms-2">'.L("team.period").': <span class="font-italic">'.$obj['name'].'</span></span>';
    }
    if (!empty($_SESSION['search'][$hash]['status'])) {
		$stm = Database\Sql::select('status')->where(['id', '=', $_SESSION['search'][$hash]['status']])->prepare();
		$stm->execute();
		$obj = $stm->fetch();
		
		echo '<span class="badge bg-dark ms-2">'.L("Status").': <span class="font-italic">'.L($obj['name']).'</span></span>';
	}
    if (isset($_SESSION['search'][$hash]['sql_order_field']) || isset($_SESSION['search'][$hash]['sql_order_seq'])) {
        $this->setOrderFieldSeq($_SESSION['search'][$hash]['sql_order_field'] ?? 'id', $_SESSION['search'][$hash]['sql_order_seq'] ?? 'DESC');
        echo '<span class="badge bg-dark ms-2">'.L('Sorted').'</span></span>';
    }
	echo '</div><div><a class="btn btn-dark" href="'.$this->pageLink('page.teamList', ['pg'=>1]).'">'.L("search.clear").'</a></div></div>';
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
            <th scope="col"><input type='checkbox' id='checkAll' class="form-check-input"></th>
            <th scope="col"><?=L('ID');?></th>
            <th scope="col"><?=$this->sortableColumn('team.searchOrder', $qArr, L('team.name'), 'teamName', 'link-dark');?></th>
            <th scope="col"><?=$this->sortableColumn('team.searchOrder', $qArr, L('team.category'), 'categoryID', 'link-dark');?></th>
            <th scope="col"><?=$this->sortableColumn('team.searchOrder', $qArr, L('team.period'), 'periodID', 'link-dark');?></th>
            <th scope="col"><?=$this->sortableColumn('team.searchOrder', $qArr, L('team.donateAmount'), 'teamDonateAmt', 'link-dark');?></th>
            <th scope="col"><?=L('team.confirmStatus');?></th>
            <th scope="col"><?=L('Status');?></th>
            <th scope="col"><?=L('Actions');?></th>
        </tr>
    </thead>
    <tbody>
        <?php
    foreach ($this->ListItem() as $listObj) {
        ?>
        <tr>
            <td scope="row"><?php if ($listObj->userID==0 && $listObj->status == 1) { ?><input type='checkbox' class="form-check-input checkSelect" id="checkboxItem_<?=$listObj->id;?>" value="<?=$listObj->id;?>"><?php } ?></td>
            <td><?=$listObj->id;?></td>
            <td><?=$listObj->teamName;?></td>
            <td><?=$listObj->teamCategory;?></td>
            <td><?=$listObj->teamPeriod;?></td>
            <td><?=$listObj->teamDonateAmt;?></td>
            <td><?=L(($listObj->userID == 0)?'team.pending':'team.confirmed');?></td>
            <td><?=L($listObj->statusName);?></td>
            <td>
                <a class="btn btn-md btn-primary" href="<?=$this->pageLink('page.teamInfo', ["id"=>$listObj->id]);?>" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=L('Information');?>"><i class="fas fa-sm fa-info-circle"></i></a>
                <a class="btn btn-md btn-success" href="<?=$this->pageLink('page.teamEdit', ["id"=>$listObj->id]);?>" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=L('Edit');?>"><i class="fas fa-sm fa-edit"></i></a> 
                <?php if ($listObj->userID==0 && $listObj->status == 1) { ?>
                    <button class="btn btn-md btn-danger btnConfirm" type="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?= L('Confirm'); ?>" data-id="<?= $listObj->id; ?>" data-name="<?= $listObj->teamName; ?>" data-category="<?= $listObj->teamCategory; ?>" data-period="<?= $listObj->teamPeriod; ?>"> <i class="fa-solid fa-clipboard-check"></i></button>
                <?php } else if ($listObj->userID==0 && $listObj->status == 2) { ?>
                    <button class="btn btn-md btn-info btnRePay" type="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?= L('RepayEmail'); ?>" data-id="<?= $listObj->id; ?>" data-name="<?= $listObj->teamName; ?>" data-category="<?= $listObj->teamCategory; ?>" data-period="<?= $listObj->teamPeriod; ?>"> <i class="fa-solid fa-dollar-sign"></i></button>
                <?php } ?>
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
  
  $("#checkAll").click(function(e){
    
    if( $(this).is(':checked') ){
        $(".checkSelect").each(function(){
            $(this).prop('checked', true);
        });
    } else {
        $(".checkSelect").each(function(){
            $(this).prop('checked', false);
        });
    }
  });

  $(".btnConfirmAll").click(function(e) {
    e.preventDefault();
    $('#msgBox').one('show.bs.modal', function(ev) {

      var button = $(e.currentTarget);
      var modal = $(this);
      modal.find('#msgBoxLabel').text('<?=L('team.confirm');?>');
      modal.find('#msgBoxBtnPri').css('display', 'none');
      modal.find('.modal-body').html('<?=L('info.teamConfirmSelected');?>?');
      modal.find('#msgBoxBtnPri').css('display', 'block');
      modal.find('#msgBoxBtnPri').one('click', function(event) {
        if($('.checkSelect:checked').length==0) {
            modal.find('.modal-body').text("<?=L('error.teamNotSelected');?>");
            modal.find('#msgBoxBtnPri').css('display', 'none');  
        } else {
            $(".checkSelect").each(function(){
                if($(this).is(':checked')){
                    ajaxFunc.apiCall("POST", "team/confirm/"+$(this).val(), null, null, function (data) { 
                        var checkboxItem = $("#checkboxItem_"+data.content.id);
                        checkboxItem.hide();
                        checkboxItem.parent().next().next().next().next().next().next().text("<?=L('team.confirmed');?>");
                        //$("[data-id="+button.data('id')+"]").hide();
                        //$("[data-id="+button.data('id')+"]").parent().prev().prev().text("<?=L('team.confirmed');?>");
                        //$("[data-id="+button.data('id')+"]").parent().prev().prev().prev().prev().prev().prev().prev().prev().text("");               
                    });
                }        
            });   
            modal.find('.modal-body').text("<?=L('info.teamConfirmSuccess');?>");
            modal.find('#msgBoxBtnPri').css('display', 'none');            
        }   

        

      });
    }).modal('show')
    
  });


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
        
        ajaxFunc.apiCall("POST", "team/confirm/"+button.data('id'), null, null, function (data) {   
          modal.find('.modal-body').text(data.content.message);
          modal.find('#msgBoxBtnPri').css('display', 'none');
          $("[data-id="+button.data('id')+"]").hide();
          $("[data-id="+button.data('id')+"]").parent().prev().prev().text("<?=L('team.confirmed');?>");
          $("[data-id="+button.data('id')+"]").parent().prev().prev().prev().prev().prev().prev().prev().prev().text("");

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
        
        ajaxFunc.apiCall("GET", "team/repay/"+button.data('id'), null, null, function (data) {   
          modal.find('.modal-body').text(data.msg);
          modal.find('#msgBoxBtnPri').css('display', 'none');
/*
          $("[data-id="+button.data('id')+"]").hide();
          $("[data-id="+button.data('id')+"]").parent().prev().prev().text("");
          $("[data-id="+button.data('id')+"]").parent().prev().prev().prev().prev().prev().prev().prev().prev().text("");
*/
        });        
      });
    }).modal('show')
   
  });
</script>
<?php
include("view/layout/endpage.php");
?>