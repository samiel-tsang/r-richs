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
                      <h4 class="card-title"><?=L('menu.client');?></h4>
                      <button class="btn btn-primary btn-round ms-auto addClientBtn">
                        <i class="fa fa-plus"></i>
                      </button>
                    </div>    
               </div>
               <div class="card-body">
                  <div class="table-responsive">
                     <table id="clientTable" class="display table table-striped table-hover dataTable">
                     <?=Controller\client::genTableHeader();?>
                        <?=Controller\client::genTableFooter();?>
                        <tbody>
                           <?php
                           $content = Controller\client::genTableContentData(0);
                           foreach($content as $listObj) {
                              echo Controller\client::genTableBodyRow($listObj);                                    
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

        var clientTable = $("#clientTable").DataTable({
            pageLength: 10,
            processing: false,
            serverSide: true,
            serverMethod: 'post',
            ajax: '<?=$request->baseUrl();?>/script/clientList.php',
            "columns": [
               { data: 'column_clientID' },
               { data: 'column_clientType' },
               { data: 'column_clientTitle' },
               { data: 'column_clientContactPerson' },
               { data: 'column_clientPosition' },
               { data: 'column_clientPhone' },
               { data: 'column_clientEmail' },
               { data: 'column_clientStatus' },
               { data: 'column_function' },                   
            ],  
            initComplete: function () {
            this.api()
              .columns([0,1,2,3,4,5,6,7])
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

        
        $(".addClientBtn").click(function(e){
            var button = $(e.currentTarget);
            e.preventDefault();
            ajaxFunc.apiCall("GET", "client/formAdd", null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Add');?> <?=L('Record');?>");
                  if(form_data.content.success) {
                     modal.find('.modal-body').html(form_data.content.message);
                     $(".companyInfo").closest(".form-group").hide();
                     $("#sameAddress").click(function(){
                        if($("#sameAddress").prop("checked")){                           
                           $("#companyAddress").val($("#address").val());
                           $("#companyAddress").closest(".form-group").hide();
                        } else {
                           $("#companyAddress").val("");
                           $("#companyAddress").closest(".form-group").show();
                        }
                     });
                     
                     modal.find('#msgBoxBtnPri').off('click');
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        if(document.getElementById("form-addClient")!==null){    
                           var data = new FormData(document.getElementById("form-addClient"));  
                           ajaxFunc.apiCall("POST", "client", data, "multipart/form-data", function(return_data){
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
                                        clientTable.ajax.reload(myCallback, false);   
                                    } 
                                 });    
                              } else {
                                 $("#form-addClient").find(".form-group").removeClass("has-error");
                                 $("#form-addClient").find(".form-group").find(".hintHelp").text("");
                                 $("#form-addClient").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-addClient").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-addClient").find("#"+return_data.content.field).focus();
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
                  
                  $(".clientTypeSelect").click(function(){
                     if($(this).val()==2){
                        $(".companyInfo").closest(".form-group").show();
                     } else {
                        $(".companyInfo").closest(".form-group").hide();
                     }
                  })

               }).modal('show')
            });
        });
        
        $('#clientTable tbody').on('click', '.btnView', function (e) {

            e.preventDefault();
            var button = $(e.currentTarget);
            ajaxFunc.apiCall("GET", "client/detail/"+button.data('id'), null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                  modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('tpb.client');?> <?=L('Record');?>");
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

        $('#clientTable tbody').on('click', '.btnEdit', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            ajaxFunc.apiCall("GET", "client/formEdit/"+button.data('id'), null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Edit');?> <?=L('Record');?>");                  
                  if(form_data.content.success) {

                     modal.find('.modal-body').html(form_data.content.message);

                     if(form_data.content.clientTypeID=='1')
                        $(".companyInfo").closest(".form-group").hide();
                     else
                        $(".companyInfo").closest(".form-group").show();

                     $("#sameAddress").click(function(){
                        if($("#sameAddress").prop("checked")){                           
                           $("#companyAddress").val($("#address").val());
                           $("#companyAddress").closest(".form-group").hide();
                        } else {
                           $("#companyAddress").val("");
                           $("#companyAddress").closest(".form-group").show();
                        }
                     });
                     removeDoc();
                     downloadDoc();                     
                     modal.find('#msgBoxBtnPri').off('click');
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        
                        if(document.getElementById("form-editClient")!==null){    
                           var data = new FormData(document.getElementById("form-editClient"));  
                           ajaxFunc.apiCall("POST", "client/"+button.data('id'), data, "multipart/form-data", function(return_data){
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
                                        clientTable.ajax.reload(myCallback, false);      
                                    } 
                                 });    
                              } else {
                                 $("#form-editClient").find(".form-group").removeClass("has-error");
                                 $("#form-editClient").find(".form-group").find(".hintHelp").text("");
                                 $("#form-editClient").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-editClient").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-editClient").find("#"+return_data.content.field).focus();
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

                  $(".clientTypeSelect").click(function(){
                     if($(this).val()==2){
                        $(".companyInfo").closest(".form-group").show();
                     } else {
                        $(".companyInfo").closest(".form-group").hide();
                     }
                  })
                               
               }).modal('show')
            });

        });
        
         $('#clientTable tbody').on('click', '.btnDel', function (e) {

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

                  ajaxFunc.apiCall("DELETE", "client/"+button.data('id'), null, null, function(return_data){
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
                              clientTable.ajax.reload(myCallback, false);   
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
                     
                     ajaxFunc.apiCall("POST", "client/removeDoc/"+button.data('id'), {"clientID":button.data('client'), "docType":button.data('doc')}, null, function(return_data){
                        
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