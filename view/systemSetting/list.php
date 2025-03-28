<?php
include("view/layout/meta.php");
include("view/layout/head.php");
?>
<style>
.modal.fade .modal-dialog {
  transition: transform 0.3s ease-out, opacity 0.3s ease-out;
}
</style>
<div class="main-panel">
<?php
include("view/layout/headExt.php");
?>  			
   <div class="container">
      <div class=""></div>
         <div class="row">
            <div class="col-md-12">
               <div class="card">
               <div class="card-header">
                  <div class="d-flex align-items-center">
                      <h4 class="card-title"><?=L('menu.otherSetting');?></h4>
                      <button class="btn btn-primary btn-round ms-auto addZoningBtn">
                        <i class="fa fa-plus"></i>
                      </button>
                    </div>    
               </div>
               <div class="card-body">
                  <?php
                     $formName = "form-systemSetting";			

                     $content = "<form id='".$formName."' class='' autocomplete='off'>";
                     $content .= "<div class='row'><p class='col-md-12 col-lg-12 text-primary' id='notice'></p></div>";

                     $alertEmailTemplateID = Controller\systemSetting::findMetaValue("alertEmailTemplateID")->metaValue;
                     $autoEmailHour = Controller\systemSetting::findMetaValue("autoEmailHour")->metaValue;

                     $content .= "<div class='row'>";   

                           $option = [""=>""];
                           $stm = Database\Sql::select('emailTemplate')->where(['status', '=', 1])->prepare();
                           $stm->execute();                                          
                           foreach ($stm as $opt) {  
                              $option[$opt['id']] = $opt['name'];			  
                           }                                    
                           $content .= Controller\formLayout::rowSelectNew(L('systemSetting.alertEmailTemplate'), 'alertEmailTemplateID', 'alertEmailTemplateID', $option,  6, [], [], $alertEmailTemplateID );   


                           $option = ["00"=>"00", "01"=>"01", "02"=>"02", "03"=>"03", "04"=>"04", "05"=>"05", "06"=>"06", "07"=>"07", "08"=>"08", "09"=>"09", "10"=>"10", "11"=>"11", "12"=>"12", "13"=>"13", "14"=>"14", "15"=>"15", "16"=>"16", "17"=>"17", "18"=>"18", "19"=>"19", "20"=>"20", "21"=>"21", "22"=>"22", "23"=>"23"];
                           $content .= Controller\formLayout::rowSelectNew(L('systemSetting.alertEmailSendHour'), 'autoEmailHour', 'autoEmailHour', $option,  6, [], ['required'], $autoEmailHour);   

                     $content .= "</div>";	                    
                     
                     $content .= "</form>";

                     echo $content;
                  ?>                  
               </div>
                  <div class="card-action">
                    <button class="btn btn-md btn-success" id="systemSettingSaveBtn"><?=L("Save");?></button>
                  </div>               
               </div>
            </div>
         </div>
      </div>
      <?php include("view/layout/foot.php"); ?>   
   </div>
   
</div>
<?php
include("view/layout/js.php");
include("view/layout/endpage.php");
?>
<script>
      $(document).ready(function () {

         $('#systemSettingSaveBtn').click(function(e){
            e.preventDefault();

            if(document.getElementById("form-systemSetting")!==null){                        
                  var data = new FormData(document.getElementById("form-systemSetting"));   
                  ajaxFunc.apiCall("POST", "systemSetting/update", data, "multipart/form-data",  function (return_data) { 

                     if(return_data.content.success) { 
                        swal({
                           title: return_data.content.message,
                           text: return_data.content.message,
                           type: "warning",
                           buttons: {
                              confirm: {
                                 text: "<?=L('OK');?>",
                                 className: "btn btn-success",
                              }
                           },
                        }).then((willOK) => {
                           if (willOK) {
                              ;
                           } 
                        });    
                     } else {
                        $("#form-systemSetting").find(".form-group").removeClass("has-error");
                        $("#form-systemSetting").find(".form-group").find(".hintHelp").text("");
                        $("#form-systemSetting").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                        $("#form-systemSetting").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                        $("#form-systemSetting").find("#"+return_data.content.field).focus();
                     }

                  });
            }                                 

         });



        /*
        $('#zoningTable tbody').on('click', '.btnEdit', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            ajaxFunc.apiCall("GET", "zoning/formEdit/"+button.data('id'), null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Edit');?> <?=L('Record');?>");                  
                  if(form_data.content.success) {

                     modal.find('.modal-body').html(form_data.content.message);
                     
                     modal.find('#msgBoxBtnPri').off('click');
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        
                        if(document.getElementById("form-editZoning")!==null){    
                           var data = new FormData(document.getElementById("form-editZoning"));  
                           ajaxFunc.apiCall("POST", "zoning/"+button.data('id'), data, "multipart/form-data", function(return_data){
                              if(return_data.content.success) {
                                 $("#msgBox").modal("hide");    
                                 swal({
                                    title: return_data.content.message,
                                    text: return_data.content.message,
                                    type: "warning",
                                    buttons: {
                                       confirm: {
                                          text: "<?=L('OK');?>",
                                          className: "btn btn-success",
                                       }
                                    },
                                 }).then((willOK) => {
                                    if (willOK) {
                                       //location.reload();     
                                       zoningTable.ajax.reload(myCallback, false);
                                    } 
                                 });    
                              } else {
                                 $("#form-editZoning").find(".form-group").removeClass("has-error");
                                 $("#form-editZoning").find(".form-group").find(".hintHelp").text("");
                                 $("#form-editZoning").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-editZoning").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-editZoning").find("#"+return_data.content.field).focus();
                              }
                           });
                        } 
                        
                     }); 
                  } else {
                     modal.find('.modal-body').html(form_data.content.message);
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        $("#msgBox").modal("hide");   
                     });
                  }  
                               
               }).modal('show')
            });

        });
        */
        
        
         
      });


    </script>