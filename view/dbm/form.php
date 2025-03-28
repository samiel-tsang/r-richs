<?php
include("view/layout/meta.php");
include("view/layout/head.php");
?>
<div class="main-panel">
<?php
include("view/layout/headExt.php");
?>  		
  <div class="container">
    <div class="">            
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <div class="d-flex align-items-center">
                <h4 class="card-title"><?=(isset($this->obj))?L('Edit'):L('Add'); ?> <?=L('Record');?></h4>
              </div>                    
            </div>
            <form id="form-user" class="">
            <input type="hidden" name="id" value="<?=(isset($this->obj))?$this->obj['id']:''; ?>">                  
            <div class="card-body">
              <div class="row">
                  <?php $this->halfRowInputNew(L('user.userName'), 'username', 'userName', 'text', (isset($this->obj))?' disabled':'');?>
                  <?php
                      $stm = Database\Sql::select('status')->prepare();
                      $stm->execute();
                      $options = [];
                      foreach ($stm as $opt) { $options[$opt['id']] = L($opt['name']); }
                      $this->halfRowSelectNew(L('Status'), 'status', 'userStatus', $options, ((isset($this->obj) && in_array($this->obj['id'], Controller\user::AdminUserID)) || $user->roleID != 1)?' disabled':'');           
                  ?>
              </div>
              <div class="row">
                  <?php
                      $this->halfRowPasswordNew(L('user.password'), 'password', 'userPW', 'password');
                      $this->halfRowPasswordNew(L('user.confirmPassword'), 'cfmPassword', 'userCfmPW', 'password');
                  ?>
              </div>
              <div class="row">
                  <?php $this->halfRowInputNew(L('user.email'), 'email', 'userEmail', 'email'); ?>
                  <?php
                      $stm = Database\Sql::select('role')->where(['status', '=', 1])->prepare();
                      $stm->execute();
                      $options = [];
                      foreach ($stm as $opt) { $options[$opt['id']] = $opt['name']; }
                      $this->halfRowSelectNew(L('Role'), 'roleID', 'roleID', $options, ($user->roleID != 1)?' disabled':''); 
                  ?>   
              </div>                    
            </div>
            
            <div class="card-action">
              <button class="btn btn-success mx-3" id="alert_demo_8"><?=L('Save');?></button>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php include("view/layout/foot.php"); ?>   
  </div>        
</div>
<?php
include("view/layout/js.php");
?>
<script>
    $('#form-user').submit(function (e) {
        e.preventDefault();
        
        swal({
              title: "<?=L('SaveAlertTitle');?>",
              text: "<?=L('SaveAlertMessage');?>",
              type: "warning",
              buttons: {
                confirm: {
                  text: "<?=L('Y');?>",
                  className: "btn btn-success",
                },                
                cancel: {
                  visible: true,
                  text: "<?=L('N');?>",
                  className: "btn btn-danger",
                },
              },
            }).then((willDelete) => {
              if (willDelete) {

                var data = new FormData(this);
                ajaxFunc.apiCall("POST", "user/<?=(isset($this->obj))?$this->obj['id']:'';?>", data, "multipart/form-data", function(return_data){
                  if(return_data.content.success) {
                    swal(return_data.content.message, {
                      icon: "success",
                      buttons: {
                        confirm: {
                          className: "btn btn-success",
                        },
                      },
                    });
                  } else {
                    swal(return_data.content.message, {
                      icon: "error",
                      buttons: {
                        confirm: {
                          className: "btn btn-danger",
                        },
                      },
                    });                    
                  }
                });
              } 
            });
    });

    </script>

<?php
include("view/layout/endpage.php");
?>