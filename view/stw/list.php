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
                      <h4 class="card-title"><?=L('menu.stw');?></h4>
                      <button class="btn btn-primary btn-round ms-auto addStwBtn">
                        <i class="fa fa-plus"></i>
                      </button>
                    </div>    
               </div>
               <div class="card-body">
                  <div class="table-responsive">
                     <table id="stwTable" class="display table table-striped table-hover dataTable">
                     <?=Controller\stw::genTableHeader();?>
                        <?=Controller\stw::genTableFooter();?>
                        <tbody>
                           <?php
                           $content = Controller\stw::genTableContentData();
                           foreach($content as $listObj) {
                              echo Controller\stw::genTableBodyRow($listObj);                                    
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

         function autoTab(tabID) {
            $(".stwAddMenuA").removeClass('active');
            $(".stwAddMenuDiv").removeClass('show active');
            $("#"+tabID+"-tab").addClass('active');
            $("#"+tabID).addClass('show active');
         }

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

        var stwTable = $("#stwTable").DataTable({
            pageLength: 10,
            processing: false,
            serverSide: true,
            serverMethod: 'post',
            ajax: '<?=$request->baseUrl();?>/script/stwList.php?mode=edit',
            "columns": [
               { data: 'column_stwID' },
               { data: 'column_refNo' },
               { data: 'column_tpbNo' },
               { data: 'column_client' }, 
               { data: 'column_addressDDLot' }, 
               { data: 'column_submissionDate' }, 
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
          },rowId: 'column_stwID'
        });

        
        $(".addStwBtn").click(function(e){
            var button = $(e.currentTarget);
            e.preventDefault();
            ajaxFunc.apiCall("GET", "stw/formAdd", null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Add');?> <?=L('Record');?>");
                  if(form_data.content.success) {
                     modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                     modal.find('.modal-body').html(form_data.content.message);                       
                    
                     addMultiSelect();
                     addCalendar();

                     $("#addMailingLogRow").click(function(e){
                        addMailingLogRow();
                        removeMailingLogRow();
                     });

                     removeMailingLogRow();                     

                     modal.find('#msgBoxBtnPri').off('click');
                     
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        if(document.getElementById("form-addStw")!==null){                               
                           var data = new FormData(document.getElementById("form-addStw"));  
                           ajaxFunc.apiCall("POST", "stw", data, "multipart/form-data", function(return_data){
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
                                        stwTable.ajax.reload(myCallback, false);   
                                    } 
                                 });    
                              } else {
                                 autoTab(return_data.content.tab);
                                 $("#form-addStw").find(".form-group").removeClass("has-error");
                                 $("#form-addStw").find(".form-group").find(".hintHelp").text("");
                                 $("#form-addStw").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-addStw").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-addStw").find("#"+return_data.content.field).focus();
                              }
                           });
                        }      
                     });  
                     

                     $("#clientID").change(function(e){
                        if($(this).val()=="Add") {
                           e.preventDefault();

                           ajaxFunc.apiCall("GET", "client/formAdd", null, null,  function (form_data) { 
                              $('#msgBox2').one('show.bs.modal', function (ev) {
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
                                                $("#msgBox2").modal("hide");      
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
                                                      $("#msgBox2").modal("hide");                                                        
                                                      $("#msgBox").find("#clientID").append($('<option>').val(return_data.content.id).text(return_data.content.name));                                                     
                                                      $("#clientID option[value='"+return_data.content.id+"']").prop('selected',true);   

                                                      ajaxFunc.apiCall("GET", "client/detail/"+return_data.content.id, null, null,  function (return_data) {                               
                                                         if(return_data.content.success) {
                                                            $("#stwSelectedClentDetail").html(return_data.content.message);
                                                            downloadDoc();                                                 
                                                         } else {
                                                            $("#stwSelectedClentDetail").html(return_data.content.message);
                                                         }     
                                                      });
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
                                       $("#msgBox2").modal("hide");   
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

                        } else if($(this).val()=="") {
                           $("#stwSelectedClentDetail").html("");
                        } else {
                           ajaxFunc.apiCall("GET", "client/detail/"+$(this).val(), null, null,  function (return_data) {                               
                              if(return_data.content.success) {                                 
                                 $("#stwSelectedClentDetail").html(return_data.content.message);
                                 downloadDoc();
                              } else {
                                 $("#stwSelectedClentDetail").html(return_data.content.message);
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
               /* remove xl modal on modal close*/
               $('#msgBox').on('hidden.bs.modal', function (e) {
                  $(this).find('.modal-dialog').removeClass("modal-xl");
               })
            });
        });

        $('#stwTable tbody').on('click', '.btnView', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            show_stw_detail(button.data('id'));

        });   

        $('#stwTable').on('click', 'tbody tr td:not(:last-child)', function(e) {
            e.preventDefault();
            show_stw_detail($(this).parent().attr('id'));                 
        });            
        
        function show_stw_detail(stwID) {
            ajaxFunc.apiCall("GET", "stw/detail/"+stwID, null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                  modal.find('#msgBoxLabel').html("<?=L('View');?> STW <?=L('Record');?>");
                  if(form_data.content.success) {
                     modal.find('.modal-body').html(form_data.content.message);                      

                     ajaxFunc.apiCall("GET", "client/detail/"+$("#clientID").val(), null, null,  function (return_data) {                               
                        if(return_data.content.success) {
                           $("#stwSelectedClentDetail").html(return_data.content.message);
                           downloadDoc();                                                 
                        } else {
                           $("#stwSelectedClentDetail").html(return_data.content.message);
                        }     
                     });                     
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        $("#msgBox").modal("hide");   
                     });  
                     
                        var mailingLogTable = $("#mailingLogTable").DataTable({
                           pageLength: 10,
                           processing: false,
                           serverSide: true,
                           serverMethod: 'post',
                           autoWidth: false,
                           ajax: '<?=$request->baseUrl();?>/script/stwMailingLogList.php?mode=view&stwID='+stwID,
                           "columns": [
                              { data: 'column_mailingLogID' },
                              { data: 'column_date' },
                              { data: 'column_from' },
                              { data: 'column_content' }, 
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
                           }                     
                        }); 

                        $('#mailingLogTable tbody').on('click', '.btnView', function (e) {
                              e.preventDefault();
                              var button = $(e.currentTarget);
                              ajaxFunc.apiCall("GET", "stw/mailingLog/detail/"+button.data('id'), null, null,  function (form_data) { 
                                 $('#msgBox2').one('show.bs.modal', function (ev) {                 
                                    var modal = $(this);
                                    //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                    modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('stw.mailingDetail');?> <?=L('Record');?>");
                                    if(form_data.content.success) {
                                       modal.find('.modal-body').html(form_data.content.message);                      
                                       modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                          $("#msgBox2").modal("hide");   
                                       });     
                                    } else {
                                       modal.find('.modal-body').html(form_data.content.message);
                                       modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                          $("#msgBox2").modal("hide");   
                                       });                    
                                    }
                                    downloadDoc();  
                                                
                                 }).modal('show')
                                 /*
                                 $('#msgBox2').on('hidden.bs.modal', function (e) {
                                    $(this).find('.modal-dialog').removeClass("modal-xl");
                                 })
                                 */               
                              });

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
        
        $('#stwTable tbody').on('click', '.btnEdit', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            ajaxFunc.apiCall("GET", "stw/formEdit/"+button.data('id'), null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Edit');?> <?=L('Record');?>");                  
                  if(form_data.content.success) {
                     modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */

                     modal.find('.modal-body').html(form_data.content.message);
                     addMultiSelect();
                     addCalendar();
                     removeDoc(); 
                     downloadDoc();  

                     $("#clientID").change(function(e){
                        if($(this).val()=="Add") {
                           e.preventDefault();

                           ajaxFunc.apiCall("GET", "client/formAdd", null, null,  function (form_data) { 
                              $('#msgBox2').one('show.bs.modal', function (ev) {
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
                                                $("#msgBox2").modal("hide");      
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
                                                      $("#msgBox2").modal("hide");                                                        
                                                      $("#msgBox").find("#clientID").append($('<option>').val(return_data.content.id).text(return_data.content.name));                                                     
                                                      $("#clientID option[value='"+return_data.content.id+"']").prop('selected',true);   

                                                      ajaxFunc.apiCall("GET", "client/detail/"+return_data.content.id, null, null,  function (return_data) {                               
                                                         if(return_data.content.success) {
                                                            $("#stwSelectedClentDetail").html(return_data.content.message);
                                                            downloadDoc();                                                 
                                                         } else {
                                                            $("#stwSelectedClentDetail").html(return_data.content.message);
                                                         }     
                                                      });
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
                                       $("#msgBox2").modal("hide");   
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

                        } else if($(this).val()=="") {
                           $("#stwSelectedClentDetail").html("");
                        } else {
                           ajaxFunc.apiCall("GET", "client/detail/"+$(this).val(), null, null,  function (return_data) {                               
                              if(return_data.content.success) {                                 
                                 $("#stwSelectedClentDetail").html(return_data.content.message);
                                 downloadDoc();
                              } else {
                                 $("#stwSelectedClentDetail").html(return_data.content.message);
                              }     
                           });

                           
                        }
                     });                        
                 
                     $("#addMailingLogRow").click(function(e){
                        addMailingLogRow();
                        removeMailingLogRow();
                     });

                     removeMailingLogRow();    
                     
                     modal.find('#msgBoxBtnPri').off('click');
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        tinymce.triggerSave();
                        if(document.getElementById("form-editStw")!==null){    
                           var data = new FormData(document.getElementById("form-editStw"));  
                           ajaxFunc.apiCall("POST", "stw/"+button.data('id'), data, "multipart/form-data", function(return_data){
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
                                        stwTable.ajax.reload(myCallback, false);      
                                    } 
                                 });    
                              } else {
                                 autoTab(return_data.content.tab);
                                 $("#form-editStw").find(".form-group").removeClass("has-error");
                                 $("#form-editStw").find(".form-group").find(".hintHelp").text("");
                                 $("#form-editStw").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-editStw").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-editStw").find("#"+return_data.content.field).focus();
                              }
                           });
                        } 
                        
                     }); 

                     var mailingLogTable = $("#mailingLogTable").DataTable({
                        pageLength: 10,
                        processing: false,
                        serverSide: true,
                        serverMethod: 'post',
                        autoWidth: false,
                        ajax: '<?=$request->baseUrl();?>/script/stwMailingLogList.php?mode=edit&stwID='+button.data('id'),
                        "columns": [
                           { data: 'column_mailingLogID' },
                           { data: 'column_date' },
                           { data: 'column_from' },
                           { data: 'column_content' }, 
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
                        }                     
                     }); 

                     // add mailingLog 
                     $(".addMailingLogBtn").click(function(e){
                        var btn = $(e.currentTarget);
                        e.preventDefault();
                        /* call html form */
                        ajaxFunc.apiCall("GET", "stw/mailingLogFormAdd/"+btn.data('id'), null, null,  function (form_data) { 
                           $('#msgBox2').one('show.bs.modal', function (ev) {
                              var modal = $(this);
                              modal.find('#msgBoxLabel').html("<?=L('Add');?> <?=L('stw.mailingDetail');?> <?=L('Record');?>");
                              if(form_data.content.success) {
                                 //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                 modal.find('.modal-body').html(form_data.content.message);   
                                 
                                 /* init show/hide for land owner fields */                                         
                                 addCalendar();         

                                 /* form submit */
                                 modal.find('#msgBoxBtnPri').off('click');
                                 modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                    if(document.getElementById("form-addMailingLog")!==null){    
                                       var data = new FormData(document.getElementById("form-addMailingLog"));  
                                       modal.find('#msgBoxBtnPri').html('<i class="fa fa-spinner fa-spin"></i>');
                                       ajaxFunc.apiCall("POST", "stw/mailingLog", data, "multipart/form-data", function(return_data){                              
                                          if(return_data.content.success) {
                                             /* successfully added */
                                             $("#msgBox2").modal("hide");    
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
                                                   mailingLogTable.ajax.reload(myCallback, false);
                                                } 
                                             });    
                                          } else {
                                             /* unsuccessfully added, focuse to error field */
                                             $("#form-addMailingLog").find(".form-group").removeClass("has-error");
                                             $("#form-addMailingLog").find(".form-group").find(".hintHelp").text("");                                 
                                             $("#form-addMailingLog").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                             $("#form-addMailingLog").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                             $("#form-addMailingLog").find("#"+return_data.content.field).focus();
                                          }
                                          modal.find('#msgBoxBtnPri').html('<?=L("OK");?>');
                                       });
                                    }      
                                 });  

                              } else {
                                 modal.find('.modal-body').html(form_data.content.message);
                                 modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                    $("#msgBox2").modal("hide");   
                                 });
                              }                   

                           }).modal('show')

                           /* remove xl modal on modal close*/
                           /*
                           $('#msgBox2').on('hidden.bs.modal', function (e) {
                              $(this).find('.modal-dialog').removeClass("modal-xl");
                           })
                              */
                           
                        });


                     });                        
                     
                     /* delete mailingLog record */
                     $('.mailingLogTable tbody').on('click', '.btnDel', function (e) {

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

                              ajaxFunc.apiCall("DELETE", "stw/mailingLog/"+button.data('id'), null, null, function(return_data){
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
                                          mailingLogTable.ajax.reload(myCallback, false);
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
                     
                     /* edit mailingLog record */
                     $('.mailingLogTable tbody').on('click', '.btnEdit', function (e) {
                           
                           e.preventDefault();
                           var button = $(e.currentTarget);
                           ajaxFunc.apiCall("GET", "stw/mailingLogFormEdit/"+button.data('id'), {"stwID":button.data('stwid')}, null,  function (form_data) { 
                              $('#msgBox2').one('show.bs.modal', function (ev) {                 
                                 var modal = $(this);
                                 modal.find('#msgBoxLabel').html("<?=L('stw.mailingDetail');?>");
                                 if(form_data.content.success) {
                                    //modal.find('.modal-dialog').addClass("modal-lg");
                                    modal.find('.modal-body').html(form_data.content.message);                                     

                                    modal.find('#msgBoxBtnPri').off('click');
                                    modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                       
                                       if(document.getElementById("form-editMailingLog")!==null){    
                                          var data = new FormData(document.getElementById("form-editMailingLog"));  
                                          ajaxFunc.apiCall("POST", "stw/mailingLog/"+button.data('id'), data, "multipart/form-data", function(return_data){
                                             if(return_data.content.success) {
                                                $("#msgBox2").modal("hide");    
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
                                                      mailingLogTable.ajax.reload(myCallback, false); 
                                                   } 
                                                });    
                                             } else {
                                                $("#form-editMailingLog").find(".form-group").removeClass("has-error");
                                                $("#form-editMailingLog").find(".form-group").find(".hintHelp").text("");
                                                $("#form-editMailingLog").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                                $("#form-editMailingLog").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                                $("#form-editMailingLog").find("#"+return_data.content.field).focus();
                                             }
                                          });
                                       } 
                                       
                                    }); 

                                    addCalendar();                                     
                                                                  
                                 
                                 } else {
                                    modal.find('.modal-body').html(form_data.content.message);
                                    modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                       $("#msgBox2").modal("hide");   
                                    });
                                 }  
                                             
                              }).modal('show')

                           });
                         
                         
                     });                       

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
        
        $('#stwTable tbody').on('click', '.btnDel', function (e) {

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

                  ajaxFunc.apiCall("DELETE", "stw/"+button.data('id'), null, null, function(return_data){
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
                              stwTable.ajax.reload(myCallback, false);   
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

         /* add mailing log row */
         function addMailingLogRow() {
            var idx = $("#mailingLogArea").find(".mailingLogRow").length;
            var content = "";
            content += '<div class="col-md-12 col-lg-12 mailingLogRow" id="mailingLogRow_'+idx+'">';
                content += '<div class="form-group">';                   
                        content += '<div class="input-group">';
                            content += '<input type="hidden" class="form-control" placeholder="Line Status" id="lineStatus_'+idx+'" name="lineStatus[]" value="0" >';
                            content += '<input type="text" class="form-control customDateTime w-20" placeholder="Date" id="no_'+idx+'" name="date[]" value="" >';
                            content += '<input type="text" class="form-control w-20" placeholder="From" id="from_'+idx+'" name="from[]" value="" >';
                            content += '<input type="text" class="form-control w-50" placeholder="Content" id="content_'+idx+'" name="content[]" value="" >';
                            content += '<button type="button" class="btn btn-sm btn-danger removeMailingLogRow"><i class="fas fa-trash"></i></button>';
                        content += '</div>';                
                    content += '<small id="mailingLog_'+idx+'Help" class="form-text text-muted hintHelp"></small>';
                content += '</div>';
            content += '</div>';              
            
            $("#mailingLogArea").append(content);
            addCalendar();
         }

        /* remove mailing log row */ 
         function removeMailingLogRow() {
            $(".removeMailingLogRow").click(function(e){
               $(this).closest(".mailingLogRow").remove();
            });
         }

         function removeDoc(){
            $(".removeDoc").off('click').on( "click" , function(e) {
               
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
                     
                     ajaxFunc.apiCall("POST", "stw/removeDoc/"+btn.attr('data-id'), {"stwID":button.data('stw'), "docType":button.data('doc')}, null, function(return_data){
                        
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
                                 btn.closest(".btnGrp").attr("style","display: none !important");
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