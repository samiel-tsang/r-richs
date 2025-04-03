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
                      <h4 class="card-title"><?=L('menu.user');?></h4>
                      <button class="btn btn-primary btn-round ms-auto addUserBtn">
                        <i class="fa fa-plus"></i>
                      </button>
                    </div>    
               </div>
               <div class="card-body">
                  <div class="table-responsive">
                     <table id="userTable" class="display table table-striped table-hover dataTable">
                     <?=Controller\user::genTableHeader();?>
                        <?=Controller\user::genTableFooter();?>
                        <tbody>
                           <?php
                           $content = Controller\user::genTableContentData(0);
                           foreach($content as $listObj) {
                              echo Controller\user::genTableBodyRow($listObj);                                    
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

        var userTable = $("#userTable").DataTable({
          pageLength: 10,
          processing: false,
          serverSide: true,
          serverMethod: 'post',
          ajax: '<?=$request->baseUrl();?>/script/userList.php',
            "columns": [
            { data: 'column_userID' },
            { data: 'column_userName' },
            { data: 'column_userDisplayName' },
            { data: 'column_userEmail' },
            { data: 'column_userPhone' },
            { data: 'column_userPosition' },
            { data: 'column_userRole' },
            { data: 'column_userStatus' },
            { data: 'column_function' },                   
         ],   
          initComplete: function () {
            this.api()
              .columns([0,1,2,3,4,5, 6, 7])
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
          rowId: 'column_userID'
        });

        $(".addUserBtn").click(function(e){
            var button = $(e.currentTarget);
            e.preventDefault();
            ajaxFunc.apiCall("GET", "user/formAdd", null, null,  function (form_data) { 
               if(form_data.content.success) {
                  $('#msgBox').one('show.bs.modal', function (ev) {
                     var modal = $(this);
                     modal.find('#msgBoxLabel').html("<?=L('Add');?> <?=L('Record');?>");                     
                     modal.find('.modal-body').html(form_data.content.message);
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        if(document.getElementById("form-addUser")!==null){    
                           var data = new FormData(document.getElementById("form-addUser"));  
                           ajaxFunc.apiCall("POST", "user", data, "multipart/form-data", function(return_data){
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
                                       userTable.ajax.reload(myCallback, false);   
                                    } 
                                 });    
                              } else {                                 
                                 $("#form-addUser").find(".form-group").removeClass("has-error");
                                 $("#form-addUser").find(".form-group").find(".hintHelp").text("");
                                 $("#form-addUser").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-addUser").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-addUser").find("#"+return_data.content.field).focus();
                              }
                           });
                        }      
                     });  
                  }).modal('show')
               } else {
                  if(form_data.content.note=='signIn'){ 
                     showLoginNotice(form_data.content.message);
                  }                         
               }                                 
            });
        });

        $('#userTable tbody').on('click', '.btnView', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);   
            show_user_detail(button.data('id'));         
        });

        $('#userTable').on('click', 'tbody tr td:not(:last-child)', function(e) {
            e.preventDefault();
            show_user_detail($(this).parent().attr('id'));                 
        });        

        function show_user_detail(id) {
            ajaxFunc.apiCall("GET", "user/detail/"+id, null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('user.info');?> <?=L('Record');?>");
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
            });
        }

        $('#userTable tbody').on('click', '.btnEdit', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            ajaxFunc.apiCall("GET", "user/formEdit/"+button.data('id'), null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Edit');?> <?=L('Record');?>");
                  if(form_data.content.success) {
                     modal.find('.modal-body').html(form_data.content.message); 
                     removeDoc();
                     downloadDoc();  
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        
                        if(document.getElementById("form-editUser")!==null){    
                           var data = new FormData(document.getElementById("form-editUser"));  
                           ajaxFunc.apiCall("POST", "user/"+button.data('id'), data, "multipart/form-data", function(return_data){
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
                                       userTable.ajax.reload(myCallback, false);   
                                    } 
                                 });    
                              } else {
                                 $("#form-editUser").find(".form-group").removeClass("has-error");
                                 $("#form-editUser").find(".form-group").find(".hintHelp").text("");
                                 $("#form-editUser").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-editUser").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-editUser").find("#"+return_data.content.field).focus();
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

         $('#userTable tbody').on('click', '.btnDel', function (e) {

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

                  ajaxFunc.apiCall("DELETE", "user/"+button.data('id'), null, null, function(return_data){
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
                              userTable.ajax.reload(myCallback, false);    
                           }
                        });                          
                     } else {
                        if(return_data.content.note=="signIn") {
                           console.log("check");
                           showLoginNotice(return_data.content.message);
                        }      
                     }
                  });
               } 
            });
            
         });

         function removeDoc(){
            
            $(".removeDoc").click(function(e){
                           
               var btn = $(this);
               e.preventDefault();
               var button = $(e.currentTarget);

               
               swal({
                  title: "<?=L('DeleteDocumentAlertTitle');?>",
                  text: "<?=L('DeleteDocumentAlertMessage');?>",
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
                     
                     ajaxFunc.apiCall("POST", "user/removeDoc/"+button.data('id'), {"userID":button.data('user'), "docType":button.data('doc')}, null, function(return_data){
                        
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
                                 btn.closest(".btnGrp").remove();
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
         }         

      });
    </script>