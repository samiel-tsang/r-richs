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
                      <h4 class="card-title"><?=L('menu.task');?></h4>
                      <button class="btn btn-primary btn-round ms-auto addTaskBtn">
                        <i class="fa fa-plus"></i>
                      </button>
                    </div>    
               </div>
               <div class="card-body">
                  <div class="table-responsive">
                     <table id="taskTable" class="display table table-striped table-hover dataTable">
                     <?=Controller\task::genTableHeader();?>
                        <?=Controller\task::genTableFooter();?>
                        <tbody>
                           <?php
                           $content = Controller\task::genTableContentData();
                           foreach($content as $listObj) {
                              echo Controller\task::genTableBodyRow($listObj);                                    
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

        var taskTable = $("#taskTable").DataTable({
            pageLength: 10,
            processing: false,
            serverSide: true,
            serverMethod: 'post',
            ajax: '<?=$request->baseUrl();?>/script/taskList.php',
            "columns": [
               { data: 'column_taskID' },
               { data: 'column_officer' },
               { data: 'column_tpbNo' },
               { data: 'column_conditionNo' }, 
               { data: 'column_description' }, 
               { data: 'column_deadline' }, 
               { data: 'column_status' },  
               { data: 'column_function' }            
            ],  
            initComplete: function () {
            this.api()
              .columns([0,1,2,3,4,5,6])
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

        
        $(".addTaskBtn").click(function(e){
            var button = $(e.currentTarget);
            e.preventDefault();
            ajaxFunc.apiCall("GET", "task/formAdd", null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Add');?> <?=L('Record');?>");
                  if(form_data.content.success) {
                     //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                     modal.find('.modal-body').html(form_data.content.message);    
                    
                    
                     ajaxFunc.apiCall("GET", "task/variableList", null, null, function(return_variable_data){
                        if(return_variable_data.content.success) {
                           return_variable_data.content.message.each
                           $("#variableList").html(return_variable_data.content.message);
                        }
                     });
                    
                     addCalendar();

                     modal.find('#msgBoxBtnPri').off('click');
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        tinymce.triggerSave();
                        if(document.getElementById("form-addTask")!==null){                               
                           var data = new FormData(document.getElementById("form-addTask"));  
                           ajaxFunc.apiCall("POST", "task", data, "multipart/form-data", function(return_data){
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
                                        taskTable.ajax.reload(myCallback, false);   
                                    } 
                                 });    
                              } else {
                                 $("#form-addTask").find(".form-group").removeClass("has-error");
                                 $("#form-addTask").find(".form-group").find(".hintHelp").text("");
                                 $("#form-addTask").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-addTask").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-addTask").find("#"+return_data.content.field).focus();
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

            });
        });

        $('#taskTable tbody').on('click', '.btnView', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            ajaxFunc.apiCall("GET", "task/detail/"+button.data('id'), null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                  modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('task.info');?> <?=L('Record');?>");
                  if(form_data.content.success) {
                     modal.find('.modal-body').html(form_data.content.message);                      
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
        
        $('#taskTable tbody').on('click', '.btnEdit', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            ajaxFunc.apiCall("GET", "task/formEdit/"+button.data('id'), null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Edit');?> <?=L('Record');?>");                  
                  if(form_data.content.success) {
                     //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */

                     modal.find('.modal-body').html(form_data.content.message);
                     addCalendar();
                     ajaxFunc.apiCall("GET", "task/variableList", null, null, function(return_variable_data){
                        if(return_variable_data.content.success) {
                           return_variable_data.content.message.each
                           $("#variableList").html(return_variable_data.content.message);
                        }
                     });                     
                     modal.find('#msgBoxBtnPri').off('click');
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        tinymce.triggerSave();
                        if(document.getElementById("form-editTask")!==null){    
                           var data = new FormData(document.getElementById("form-editTask"));  
                           ajaxFunc.apiCall("POST", "task/"+button.data('id'), data, "multipart/form-data", function(return_data){
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
                                        taskTable.ajax.reload(myCallback, false);      
                                    } 
                                 });    
                              } else {
                                 $("#form-editTask").find(".form-group").removeClass("has-error");
                                 $("#form-editTask").find(".form-group").find(".hintHelp").text("");
                                 $("#form-editTask").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-editTask").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-editTask").find("#"+return_data.content.field).focus();
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
 
            });

        });
        
         $('#taskTable tbody').on('click', '.btnDel', function (e) {

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

                  ajaxFunc.apiCall("DELETE", "task/"+button.data('id'), null, null, function(return_data){
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
                              taskTable.ajax.reload(myCallback, false);   
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