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
                      <h4 class="card-title"><?=L('menu.emailTemplate');?></h4>
                      <button class="btn btn-primary btn-round ms-auto addEmailTemplateBtn">
                        <i class="fa fa-plus"></i>
                      </button>
                    </div>    
               </div>
               <div class="card-body">
                  <div class="table-responsive">
                     <table id="emailTemplateTable" class="display table table-striped table-hover dataTable">
                     <?=Controller\emailTemplate::genTableHeader();?>
                        <?=Controller\emailTemplate::genTableFooter();?>
                        <tbody>
                           <?php
                           $content = Controller\emailTemplate::genTableContentData();
                           foreach($content as $listObj) {
                              echo Controller\emailTemplate::genTableBodyRow($listObj);                                    
                           } ?>
                        </tbody>
                     </table>
                  </div>
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

         var myCallback = function () { 

            var table = $('.dataTable').DataTable(); // Initialize your DataTable
            var lastColumnIndex = table.columns().count() - 1; // Get the last column index
            table.columns().every(function() {
               var column = this;

               if (column.index() === lastColumnIndex) {
                     return; // exits the function, being the last (and desired) column
               }
               
               var select = $('<select class="form-select"><option value=""></option></select>')
                  .appendTo($(column.footer()).empty())
                  .on('change', function() {

                  var val = $.fn.dataTable.util.escapeRegex(
                     $(this).val()
                  );
                  column
                     .search(val ? '^' + val + '$' : '', true, false)
                     .draw();
                  });

               column.data().unique().sort().each(function(d, j) {
                  select.append('<option value="' + d + '">' + d + '</option>')
               });
            });            
         };                   

        var emailTemplateTable = $("#emailTemplateTable").DataTable({
            pageLength: 10,
            processing: false,
            serverSide: true,
            serverMethod: 'post',
            ajax: '<?=$request->baseUrl();?>/script/emailTemplateList.php',
            "columns": [
               { data: 'column_emailTemplateID' },
               { data: 'column_emailTemplateName' },
               { data: 'column_emailTemplateSubject' },
               { data: 'column_emailTemplateStatus' },  
               { data: 'column_function' }            
            ],  
            initComplete: function () {
            this.api()
              .columns([0,1,2,3])
              .every(function () {
                var column = this;
                var select = $(
                  '<select class="form-select"><option value=""></option></select>'
                )
                  .appendTo($(column.footer()).empty())
                  .on("change", function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                    column
                      .search(val ? "^" + val + "$" : "", true, false)
                      .draw();
                  });

                column
                  .data()
                  .unique()
                  .sort()
                  .each(function (d, j) {
                    select.append(
                      '<option value="' + d + '">' + d + "</option>"
                    );
                  });
              });
          },
        });

        
        $(".addEmailTemplateBtn").click(function(e){
            var button = $(e.currentTarget);
            e.preventDefault();
            ajaxFunc.apiCall("GET", "emailTemplate/formAdd", null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Add');?> <?=L('Record');?>");
                  if(form_data.content.success) {
                     modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                     modal.find('.modal-body').html(form_data.content.message);    
                    
                    
                     ajaxFunc.apiCall("GET", "emailTemplate/variableList", null, null, function(return_variable_data){
                        if(return_variable_data.content.success) {
                           return_variable_data.content.message.each
                           $("#variableList").html(return_variable_data.content.message);
                        }
                     });
                    


                     modal.find('#msgBoxBtnPri').off('click');
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        tinymce.triggerSave();
                        if(document.getElementById("form-addEmailTemplate")!==null){                               
                           var data = new FormData(document.getElementById("form-addEmailTemplate"));  
                           ajaxFunc.apiCall("POST", "emailTemplate", data, "multipart/form-data", function(return_data){
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
                                        emailTemplateTable.ajax.reload(myCallback, false);   
                                    } 
                                 });    
                              } else {
                                 $("#form-addEmailTemplate").find(".form-group").removeClass("has-error");
                                 $("#form-addEmailTemplate").find(".form-group").find(".hintHelp").text("");
                                 $("#form-addEmailTemplate").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-addEmailTemplate").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-addEmailTemplate").find("#"+return_data.content.field).focus();
                              }
                           });
                        }      
                     });  

                     buildTinymce();

                  } else {
                     modal.find('.modal-body').html(form_data.content.message);
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        $("#msgBox").modal("hide");   
                     });
                  }                    

               }).modal('show')


               /* remove xl modal on modal close*/
               $('#msgBox').on('hidden.bs.modal', function (e) {
                  $(this).find('.modal-dialog').removeClass("modal-xl");
               })               
            });
        });

        $('#emailTemplateTable tbody').on('click', '.btnView', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            ajaxFunc.apiCall("GET", "emailTemplate/detail/"+button.data('id'), null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                  modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('emailTemplate.info');?> <?=L('Record');?>");
                  if(form_data.content.success) {
                     modal.find('.modal-body').html(form_data.content.message);                      
                     ajaxFunc.apiCall("GET", "emailTemplate/variableList", null, null, function(return_variable_data){
                        if(return_variable_data.content.success) {
                           return_variable_data.content.message.each
                           $("#variableList").html(return_variable_data.content.message);
                        }
                     });   
                     buildTinymce();
                     tinymce.activeEditor.mode.set('readonly');          
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        $("#msgBox").modal("hide");   
                     });     
                  } else {
                     modal.find('.modal-body').html(form_data.content.message);
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        $("#msgBox").modal("hide");   
                     });                    
                  }
                  downloadDoc();  
                               
               }).modal('show')

               $('#msgBox').on('hidden.bs.modal', function (e) {
                  $(this).find('.modal-dialog').removeClass("modal-xl");
               })               
            });

        });        

        
        $('#emailTemplateTable tbody').on('click', '.btnEdit', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            ajaxFunc.apiCall("GET", "emailTemplate/formEdit/"+button.data('id'), null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Edit');?> <?=L('Record');?>");                  
                  if(form_data.content.success) {
                     modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */

                     modal.find('.modal-body').html(form_data.content.message);
                     ajaxFunc.apiCall("GET", "emailTemplate/variableList", null, null, function(return_variable_data){
                        if(return_variable_data.content.success) {
                           return_variable_data.content.message.each
                           $("#variableList").html(return_variable_data.content.message);
                        }
                     });                     
                     modal.find('#msgBoxBtnPri').off('click');
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        tinymce.triggerSave();
                        if(document.getElementById("form-editEmailTemplate")!==null){    
                           var data = new FormData(document.getElementById("form-editEmailTemplate"));  
                           ajaxFunc.apiCall("POST", "emailTemplate/"+button.data('id'), data, "multipart/form-data", function(return_data){
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
                                        emailTemplateTable.ajax.reload(myCallback, false);      
                                    } 
                                 });    
                              } else {
                                 $("#form-editEmailTemplate").find(".form-group").removeClass("has-error");
                                 $("#form-editEmailTemplate").find(".form-group").find(".hintHelp").text("");
                                 $("#form-editEmailTemplate").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-editEmailTemplate").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-editEmailTemplate").find("#"+return_data.content.field).focus();
                              }
                           });
                        } 
                        
                     }); 

                     buildTinymce();

                  } else {
                     modal.find('.modal-body').html(form_data.content.message);
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        $("#msgBox").modal("hide");   
                     });
                  }  

               }).modal('show')

               /* remove xl modal on modal close*/
               $('#msgBox').on('hidden.bs.modal', function (e) {
                  $(this).find('.modal-dialog').removeClass("modal-xl");
               }) 
            });

        });
        
         $('#emailTemplateTable tbody').on('click', '.btnDel', function (e) {

            e.preventDefault();

            var button = $(e.currentTarget);

            swal({
               title: "<?=L('DeleteAlertTitle');?>",
               text: "<?=L('DeleteAlertMessage');?>",
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

                  ajaxFunc.apiCall("DELETE", "emailTemplate/"+button.data('id'), null, null, function(return_data){
                     if(return_data.content.success) {
                        swal(return_data.content.message, {
                           icon: "success",
                           buttons: {
                              confirm: {
                                 className: "btn btn-success",
                              },
                           },
                        }).then((willReload) => {
                           if (willReload) {
                              //location.reload();  
                              emailTemplateTable.ajax.reload(myCallback, false);   
                           }
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


      
      });


    </script>