<?php
include("view/layout/meta.php");
include("view/layout/head.php");
$this->setHasContainer(true);
?>
<main class="content">
<div class="funcBar d-flex text-white">
    <span class="funcTitle me-auto"><?=L('menu.team');?> <?=L('Search');?></span>
    <div class="funcMenu mx-3 py-2">
        <a class="btn" href="<?php Utility\WebSystem::path($request->referer(Requests\Request::REFERER_QUERY), true, false);?>" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?=L('Back');?>"><i class="fas fa-1x fa-list-alt"></i></a>
    </div>
</div>
<form id="form-team" class="container my-3">


<div class="card my-3">
    <div class="card-top">
      <div class="row my-3 mx-1">
        <div class="col-md-6">
          <h5 class="card-title"><?=L('search.record');?></h5>
        </div>      
        <div class="col-md-6 text-end"></div>
      </div>
    </div>
    <div class="card-body">
        <div class="form-group row mx-0">
            <?php 
                $this->halfRowInput(L('team.name'), 'teamName', 'teamName', 'text');

                $stm = Database\Sql::select('status')->prepare();
                $stm->execute();
                $options = [''=>L('All')];
                foreach ($stm as $opt) { $options[$opt['id']] = L($opt['name']); }
                $this->halfRowSelect(L('Status'), 'status', 'status', $options); 
            ?>
        </div>
        <div class="form-group row mx-0">
            <?php 
                $stm = Database\Sql::select('category')->prepare();
                $stm->execute();
                $options = [''=>L('All')];
                foreach ($stm as $opt) { $options[$opt['id']] = $opt['name']; }
                $this->halfRowSelect(L('team.category'), 'categoryID', 'categoryID', $options); 

                $stm = Database\Sql::select('period')->prepare();
                $stm->execute();
                $options = [''=>L('All')];
                foreach ($stm as $opt) { $options[$opt['id']] = $opt['name']; }
                $this->halfRowSelect(L('team.period'), 'periodID', 'periodID', $options); 

           ?>
        </div>    
        <?php $this->rowSubmit('search'); ?>
    </div>
</div>


</form>
</main>
<?php
include("view/layout/foot.php");
include("view/layout/js.php");
?>
<script>
    $('#form-team').submit(function (e) {
        var data = new FormData(this);
        ajaxFunc.apiCall("POST", "team/search", data, "multipart/form-data", ajaxFunc.responseHandle);
        e.preventDefault();
    });
</script>
<?php
include("view/layout/endpage.php");
?>