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
                      <h4 class="card-title"><?=L('menu.systemLog');?></h4>
                      <!--
                      <button class="btn btn-primary btn-round ms-auto addSystemLogBtn">
                        <i class="fa fa-plus"></i>
                      </button>
                     -->
                    </div>    
               </div>
                  <div class="card-body"> 
                     <div class="table-responsive">
                        <table id="systemLogTable" class="display table table-striped table-hover dataTable">
                           <?=Controller\systemLog::genTableHeader();?>
                           <?=Controller\systemLog::genTableFooter();?>
                           <tbody>
                              <?php
                              $content = Controller\systemLog::genTableContentData();
                              foreach($content as $listObj) {
                                 echo Controller\systemLog::genTableBodyRow($listObj);                                    
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
         
        
        var systemLogTable = $("#systemLogTable").DataTable({
          pageLength: 25,
          processing: false,
          serverSide: true,
          serverMethod: 'post',
          ajax: '<?=$request->baseUrl();?>/script/systemLogList.php',
            "columns": [
            { data: 'column_systemLogID' },
            { data: 'column_userDisplayName' },
            { data: 'column_logTime' },
            { data: 'column_module' },
            { data: 'column_action' },
            { data: 'column_description' },
            { data: 'column_ip' },
            { data: 'column_function' },                   
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
                  }).on( 'click' , function (evt){
                     evt.stopPropagation();
                  });;

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
          order: [[0, 'desc']],
          rowId: 'column_systemLogID'
        });

        $('#systemLogTable tbody').on('click', '.btnView', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            show_log_detail(button.data('id'));
        });

        $('#systemLogTable').on('click', 'tbody tr td:not(:last-child)', function(e) {
            e.preventDefault();
            show_log_detail($(this).parent().attr('id'));                 
        }); 

        function show_log_detail(logID) {
            ajaxFunc.apiCall("GET", "systemLog/detail/"+logID, null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                  modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('menu.systemLog');?> <?=L('Record');?>");
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
                               
               }).modal('show')

               $('#msgBox').on('hidden.bs.modal', function (e) {
                  $(this).find('.modal-dialog').removeClass("modal-xl");
               })               
            });
        }

      });




    </script>