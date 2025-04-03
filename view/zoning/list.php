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
                      <h4 class="card-title"><?=L('menu.zoning');?></h4>
                      <button class="btn btn-primary btn-round ms-auto addZoningBtn">
                        <i class="fa fa-plus"></i>
                      </button>
                    </div>    
               </div>
               <div class="card-body">
                  <div class="table-responsive">
                     <table id="zoningTable" class="display table table-striped table-hover dataTable">
                        <?=Controller\zoning::genTableHeader();?>
                        <?=Controller\zoning::genTableFooter();?>
                        <tbody>
                           <?php
                           $content = Controller\zoning::genTableContentData(0);
                           foreach($content as $listObj) {
                              echo Controller\zoning::genTableBodyRow($listObj);                                    
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

        var zoningTable = $("#zoningTable").DataTable({
          pageLength: 10,
          processing: false,
          serverSide: true,
          serverMethod: 'post',
          ajax: '<?=$request->baseUrl();?>/script/zoningList.php',
            "columns": [
            { data: 'column_zoningID' },
            { data: 'column_zoningName' },
            { data: 'column_zoningStatus' },
            { data: 'column_function' },                   
         ],           
          initComplete: function () {
            this.api()
              .columns([0,1,2])
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
                  }).on( 'click' , function (evt){
                     evt.stopPropagation();
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
          },rowId: 'column_zoningID'
        });

        
        $(".addZoningBtn").click(function(e){
            var button = $(e.currentTarget);
            e.preventDefault();
            ajaxFunc.apiCall("GET", "zoning/formAdd", null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Add');?> <?=L('Record');?>");
                  if(form_data.content.success) {
                     modal.find('.modal-body').html(form_data.content.message);                     
                     modal.find('#msgBoxBtnPri').off('click');
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        if(document.getElementById("form-addZoning")!==null){    
                           var data = new FormData(document.getElementById("form-addZoning"));  
                           ajaxFunc.apiCall("POST", "zoning", data, "multipart/form-data", function(return_data){
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
                                 $("#form-addZoning").find(".form-group").removeClass("has-error");
                                 $("#form-addZoning").find(".form-group").find(".hintHelp").text("");
                                 $("#form-addZoning").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-addZoning").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-addZoning").find("#"+return_data.content.field).focus();
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

        $('#zoningTable tbody').on('click', '.btnView', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            show_zoning_detail(button.data('id'));
        });

        $('#zoningTable').on('click', 'tbody tr td:not(:last-child)', function(e) {
            e.preventDefault();
            show_zoning_detail($(this).parent().attr('id'));                 
        }); 

        function show_zoning_detail(zoningID) {
            ajaxFunc.apiCall("GET", "zoning/detail/"+zoningID, null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('tpb.zoning');?> <?=L('Record');?>");
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
        
         $('#zoningTable tbody').on('click', '.btnDel', function (e) {
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

                  ajaxFunc.apiCall("DELETE", "zoning/"+button.data('id'), null, null, function(return_data){
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
                              zoningTable.ajax.reload(myCallback, false);
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