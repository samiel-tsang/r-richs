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
                      <h4 class="card-title"><?=L('menu.dbm');?></h4>
                      <button class="btn btn-primary btn-round ms-auto addDbmBtn">
                        <i class="fa fa-plus"></i>
                      </button>
                    </div>    
               </div>
               <div class="card-body">
                  <ul class="nav nav-pills nav-secondary" id="pills-tab" role="tablist">
                     <li class="nav-item submenu tpbMenu" role="presentation">
                        <a class="nav-link active" id="pills-all-tab" data-bs-toggle="pill" href="#pills-all" role="tab" aria-controls="pills-all" aria-selected="false" tabindex="-1"><?=L("listView");?></a>
                     </li>                     
                     <li class="nav-item submenu tpbMenu" role="presentation">
                        <a class="nav-link" id="pills-draft-tab" data-bs-toggle="pill" href="#pills-draft" role="tab" aria-controls="pills-draft" aria-selected="false" tabindex="-1"><?=L("calendarView");?></a>
                     </li>                                                                                                   
                  </ul>       
                  <div class="tab-content mt-2 mb-3" id="pills-tabContent">
                     <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab">           
                        <div class="table-responsive">
                           <table id="dbmTable" class="display table table-striped table-hover">
                           <?=Controller\dbm::genTableHeader();?>
                           <?=Controller\dbm::genTableFooter();?>
                           <tbody>
                              <?php
                              $content = Controller\dbm::genTableContentData();
                              foreach($content as $listObj) {
                                 echo Controller\dbm::genTableBodyRow($listObj);                                    
                              } ?>   
                           </tbody>
                           </table>
                        </div>
                     </div>
                     <div class="tab-pane fade" id="pills-draft" role="tabpanel" aria-labelledby="pills-draft-tab">
                        <?php
                           $selected_month = (isset($_GET['month']) && $_GET['month']!="")?$_GET['month']:date("Y-m");
                           $pre_month = date("Y-m", strtotime('-1 month', strtotime($selected_month)));        
                           $next_month = date("Y-m", strtotime('+1 month', strtotime($selected_month)));                
                        ?>
                        <div class='row'>
                           <div class='col col-sm-12 col-md-3, col-lg-3'>
                              <input type="text" class="form-control flatpickr-input" id="selected_month" name="selected_month" value="<?=$selected_month;?>" readonly>
                           </div>                    
                        </div>                           
                        <div id='calendar_area' class='text-center mt-2'>
                           <i class="fa fa-spinner fa-spin" style="font-size:24px"></i>               
                        </div>
                     </div>
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

         var selected_month = "<?=$selected_month;?>";            
         
         $("#selected_month").flatpickr({
            plugins: [
               new monthSelectPlugin({
                  shorthand: true, //defaults to false
                  dateFormat: "Y-m", //defaults to "F Y"
                  altFormat: "F Y", //defaults to "F Y"
                  theme: "dark" // defaults to "light"
               })
            ], 
            disableMobile: "true",
            locale: lang
         });         

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

         var dbmTable = $("#dbmTable").DataTable({
            pageLength: 10,
            processing: false,
            serverSide: true,
            serverMethod: 'post',
            ajax: '<?=$request->baseUrl();?>/script/dbmList.php',
               "columns": [
               { data: 'column_dbmID' },
               { data: 'column_dbmScheduleDate' },
               { data: 'column_dbmStatus' },
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
          },rowId: 'column_dbmID'
        });

        
        $(".addDbmBtn").click(function(e){
            var button = $(e.currentTarget);
            e.preventDefault();
            ajaxFunc.apiCall("GET", "dbm/formAdd", null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Add');?> <?=L('Record');?>");
                  if(form_data.content.success) {
                     modal.find('.modal-body').html(form_data.content.message);                     
                     modal.find('#msgBoxBtnPri').off('click');
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        if(document.getElementById("form-addDbm")!==null){    
                           var data = new FormData(document.getElementById("form-addDbm"));  
                           ajaxFunc.apiCall("POST", "dbm", data, "multipart/form-data", function(return_data){
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
                                       dbmTable.ajax.reload(myCallback, false);    
                                    } 
                                 });    
                              } else {
                                 $("#form-addDbm").find(".form-group").removeClass("has-error");
                                 $("#form-addDbm").find(".form-group").find(".hintHelp").text("");
                                 $("#form-addDbm").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-addDbm").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-addDbm").find("#"+return_data.content.field).focus();
                              }
                           });
                        }      
                     });  

                     addCalendar();
                  } else {
                     modal.find('.modal-body').html(form_data.content.message);
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        $("#msgBox").modal("hide");   
                     });
                  }                    

               }).modal('show')
            });
        });

        $(document, '#dbmTable tbody').on('click', '.btnView', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            show_dbm_detail(button.data('id'));
        });

        $('#dbmTable').on('click', 'tbody tr td:not(:last-child)', function(e) {
            e.preventDefault();
            show_dbm_detail($(this).parent().attr('id'));                 
        });  

        function show_dbm_detail(dbmID) {
            ajaxFunc.apiCall("GET", "dbm/detail/"+dbmID, null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('.modal-dialog').addClass("modal-xl"); /*  extend xl modal */
                  modal.find('#msgBoxLabel').html("<?=L('View');?> DBM <?=L('Record');?>");
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
        }

        $('#dbmTable tbody').on('click', '.btnEdit', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            ajaxFunc.apiCall("GET", "dbm/formEdit/"+button.data('id'), null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Edit');?> <?=L('Record');?>");                  
                  if(form_data.content.success) {

                     modal.find('.modal-body').html(form_data.content.message);
                     
                     modal.find('#msgBoxBtnPri').off('click');
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        
                        if(document.getElementById("form-editDbm")!==null){    
                           var data = new FormData(document.getElementById("form-editDbm"));  
                           ajaxFunc.apiCall("POST", "dbm/"+button.data('id'), data, "multipart/form-data", function(return_data){
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
                                       dbmTable.ajax.reload(myCallback, false);     
                                    } 
                                 });    
                              } else {
                                 $("#form-editDbm").find(".form-group").removeClass("has-error");
                                 $("#form-editDbm").find(".form-group").find(".hintHelp").text("");
                                 $("#form-editDbm").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-editDbm").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-editDbm").find("#"+return_data.content.field).focus();
                              }
                           });
                        } 
                        
                     }); 

                     addCalendar();
                  } else {
                     modal.find('.modal-body').html(form_data.content.message);
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        $("#msgBox").modal("hide");   
                     });
                  }  
                               
               }).modal('show')
            });

        });
        
         $('#dbmTable tbody').on('click', '.btnDel', function (e) {

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

                  ajaxFunc.apiCall("DELETE", "dbm/"+button.data('id'), null, null, function(return_data){
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
                              dbmTable.ajax.reload(myCallback, false);   
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

         function showCalendar() {
            ajaxFunc.apiCall("GET", "dbm/getMeetingDateByMonth/"+selected_month, null, null, function (return_data) {                    
                  if(return_data.content.success){                      
                     $("#calendar_area").html(return_data.content.message);
                  }
            });
         }      
         
         showCalendar();

         $("#selected_month").change(function(e){
            selected_month = $(this).val();
            showCalendar();
         });
         
      });


    </script>