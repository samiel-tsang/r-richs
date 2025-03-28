<?php
include("view/layout/meta.php");
include("view/layout/head.php");

$userObj = unserialize($_SESSION['user']);

$this->setHasContainer(true);
?>
<main class="content">
<div class="funcBar d-flex text-white">
    <span class="funcTitle me-auto"><?=L('menu.promoCode');?> <?=(isset($this->obj))?L('Edit')." - ".$this->obj['name']:L('Add'); ?></span>
    <div class="funcMenu mx-3 py-2">
        <a class="btn" href="<?php Utility\WebSystem::path($request->referer(Requests\Request::REFERER_QUERY), true, false);?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?=L('Back');?>"><i class="fas fa-1x fa-list-alt"></i></a>
    </div>
</div>
<form id="form-user" class="container my-3">
    <input type="hidden" name="id" value="<?=(isset($this->obj))?$this->obj['id']:''; ?>">

<div class="card my-3">
    <div class="card-top">
      <div class="row my-3 mx-1">
        <div class="col-md-6">
          <h5 class="card-title"><?=(isset($this->obj))?L('Edit')." ".$this->obj['name']:L('Add'); ?> <?=L('Record');?></h5>
        </div>      
        <div class="col-md-6 text-end"></div>
      </div>
    </div>
    <div class="card-body">
        <div class="form-group row mx-0">
            <?php 
		$this->halfRowInput(L('promo.name'), 'name', 'promoName', 'text');
		
                $stm = Database\Sql::select('status')->prepare();
                $stm->execute();
                $options = [];
                foreach ($stm as $opt) { $options[$opt['id']] = L($opt['name']); }
                $this->halfRowSelect(L('Status'), 'status', 'promoStatus', $options);
            ?>
        </div>  
        <div class="form-group row mx-0">
            <?php 
                $this->halfRowInput(L('promo.code'), 'code', 'promoCode', 'text');
                $this->halfRowInput(L('amount'), 'amount', 'promoAmount', 'text');
            ?>
        </div>       
        <?php $this->rowSubmit((isset($this->obj))?L('Edit'):L('Add')); ?>
    </div>
</div>
</form>
</main>
<?php
include("view/layout/foot.php");
include("view/layout/js.php");
?>
<script>
    $('#form-user').submit(function (e) {
        var data = new FormData(this);
        ajaxFunc.apiCall("POST", "promocode/<?=(isset($this->obj))?$this->obj['id']:'';?>", data, "multipart/form-data", ajaxFunc.responseHandle);
        e.preventDefault();
    });
</script>
<?php
include("view/layout/endpage.php");
?>