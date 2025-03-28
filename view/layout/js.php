<script src="<?php Utility\WebSystem::path("js/kaiadmin/core/jquery-3.7.1.min.js");?>"></script>
<script src="<?php Utility\WebSystem::path("js/kaiadmin/core/popper.min.js");?>"></script>
<script src="<?php Utility\WebSystem::path("js/kaiadmin/core/bootstrap.min.js");?>"></script>

<!-- jQuery Scrollbar -->
<script src="<?php Utility\WebSystem::path("js/kaiadmin/plugin/jquery-scrollbar/jquery.scrollbar.min.js");?>"></script>
<!-- Moment JS -->
<script src="<?php Utility\WebSystem::path("js/kaiadmin/plugin/moment/moment.min.js");?>"></script>

<!-- Chart JS -->
<script src="<?php Utility\WebSystem::path("js/kaiadmin/plugin/chart.js/chart.min.js");?>"></script>

<!-- jQuery Sparkline -->
<script src="<?php Utility\WebSystem::path("js/kaiadmin/plugin/jquery.sparkline/jquery.sparkline.min.js");?>"></script>

<!-- Chart Circle -->
<script src="<?php Utility\WebSystem::path("js/kaiadmin/plugin/chart-circle/circles.min.js");?>"></script>

<!-- Datatables -->
<script src="<?php Utility\WebSystem::path("js/kaiadmin/plugin/datatables/datatables.min.js");?>"></script>

<!-- Bootstrap Notify -->
<script src="<?php Utility\WebSystem::path("js/kaiadmin/plugin/bootstrap-notify/bootstrap-notify.min.js");?>"></script>

<!-- jQuery Vector Maps -->
<script src="<?php Utility\WebSystem::path("js/kaiadmin/plugin/jsvectormap/jsvectormap.min.js");?>"></script>
<script src="<?php Utility\WebSystem::path("js/kaiadmin/plugin/jsvectormap/world.js");?>"></script>

<!-- Sweet Alert -->
<script src="<?php Utility\WebSystem::path("js/kaiadmin/plugin/sweetalert/sweetalert.min.js");?>"></script>

<!-- Kaiadmin JS -->
<script src="<?php Utility\WebSystem::path("js/kaiadmin/kaiadmin.min.js");?>"></script>

<!-- Multiselect JS -->
<script src="<?php Utility\WebSystem::path("js/kaiadmin/bootstrap.bundle-4.5.2.min.js");?>"></script>
<script src="<?php Utility\WebSystem::path("js/kaiadmin/bootstrap-multiselect.js");?>"></script>
<script src="<?php Utility\WebSystem::path("js/kaiadmin/prettify.min.js");?>"></script>

<!-- Flatpickr JS -->
<script src="<?php Utility\WebSystem::path("js/kaiadmin/flatpickr.js");?>"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/zh.js"></script>

<!-- Tinymce JS -->
<script type="text/javascript" src="<?php Utility\WebSystem::path("node_modules/tinymce/tinymce.min.js");?>"></script>

<script>
var BASE_URL = '<?php Utility\WebSystem::path('');?>';

var lang = "<?=$_SESSION['lang']=='hk'?'zh':'en';?>";

function buildTinymce() {
  tinymce.remove();
  tinymce.init({ selector: '.tinymce', height: 500, width: '100%', menubar: false, plugins: 'lists code link fullscreen table image',
        toolbar: 'undo redo | fontselect fontsizeselect | styleselect | bold italic | forecolor backcolor | numlist bullist | alignleft aligncenter alignright alignjustify outdent indent | link image code | table tabledelete | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | fullscreen',
        convert_urls : false, 
        /*
        relative_urls : false,
        remove_script_host : false,
        */
  }); 
}

function addCalendar() {
  $(".customDateTime").flatpickr({
      enableTime: true,
      dateFormat: "Y-m-d H:i",
      locale: lang
  });            
}

function downloadDoc(){
  $(".downloadDoc").off('click').on( "click" , function(e) {
      var btn = $(this);
      e.preventDefault();
      var button = $(e.currentTarget);                     
      ajaxFunc.apiCall("GET", "document/detail/"+btn.attr('data-id'), null, null, function(return_data){                        
        if(return_data.content.success) {
            myArr = JSON.parse(return_data.content.message);         

            if(myArr.fileType=='application/pdf') {
              window.open(myArr.downloadPath);
            } else {
              $('#msgBoxImage').one('show.bs.modal', function (ev) {                 
                var modal = $(this);
                modal.find('#msgBoxLabel').hide();              
                modal.find('.modal-body').html("<img src='"+myArr.downloadPath+"' style='width:100%'>");
              }).modal('show')
            }

        } 
      });                                      
  });
}         


$(".toggle-sidebar").click(function(e){
  
  currentSetting = localStorage.getItem("hideSideBar");
  console.log("current:"+currentSetting);
  
  if(currentSetting==="close"){
    localStorage.setItem("hideSideBar", "open");
  } else {
    localStorage.setItem("hideSideBar", "close");
  }

   console.log("afterClick:"+localStorage.getItem("hideSideBar")); 
  
});

if(localStorage.getItem("hideSideBar")=="close"){
  $(".wrapper").addClass("sidebar_minimize");
} else {
  $(".wrapper").removeClass("sidebar_minimize");
}


$(function () {
  $('[data-bs-toggle="tooltip"]').tooltip()

  var elem = document.documentElement;

  function openFullscreen() {
    if (elem.requestFullscreen) {
      elem.requestFullscreen();
    } else if (elem.webkitRequestFullscreen) { 
      elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) { 
      elem.msRequestFullscreen();
    }
  }

  function closeFullscreen() {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) { 
      document.msExitFullscreen();
    }
  }

  $("#expandBtn").click(function(e){
    openFullscreen();
    $("#expandBtn").hide();
    $("#compressBtn").show();
    //localStorage.setItem("fullScreen", "open");
  });
  
  $("#compressBtn").click(function(e){
    closeFullscreen();
    $("#compressBtn").hide();
    $("#expandBtn").show();
    //localStorage.setItem("fullScreen", "close");
  });

  /*
  if(localStorage.getItem("fullScreen")=="open"){
    openFullscreen();
  } else {
    closeFullscreen();
  }
  */


  var tpbTable0 = $("#tpbTable0").DataTable({
         pageLength: 10,
         autoWidth: false,
         processing: false,
         serverSide: true,
         serverMethod: 'post',
         ajax: '<?=$request->baseUrl();?>/script/tpbList.php?type=0',
            "columns": [
            { data: 'column_tpbID' },
            { data: 'column_tpbRefNo' },
            { data: 'column_tpbClient' },
            { data: 'column_tpbOfficer' },
            { data: 'column_tpbSubmissionDate' },
            { data: 'column_tpbLastUpdateDate' },
            { data: 'column_tpbNo' },
            { data: 'column_function' },                   
         ], 
         initComplete: function () {
            this.api()
            .columns([0,1,2,4,5,6])
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

      var tpbTable1 = $("#tpbTable1").DataTable({
         pageLength: 10,
         autoWidth: false,
         processing: false,
         serverSide: true,
         serverMethod: 'post',
         ajax: '<?=$request->baseUrl();?>/script/tpbList.php?type=1',
            "columns": [
            { data: 'column_tpbID' },
            { data: 'column_tpbRefNo' },
            { data: 'column_tpbClient' },
            { data: 'column_tpbOfficer' },
            { data: 'column_tpbSubmissionDate' },
            { data: 'column_tpbLastUpdateDate' },
            { data: 'column_tpbNo' },
            { data: 'column_function' },                   
         ], 
         initComplete: function () {
            this.api()
            .columns([0,1,2,4,5,6])
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

      var tpbTable2 = $("#tpbTable2").DataTable({
         pageLength: 10,
         autoWidth: false,
         processing: false,
         serverSide: true,
         serverMethod: 'post',
         ajax: '<?=$request->baseUrl();?>/script/tpbList.php?type=2',
            "columns": [
            { data: 'column_tpbID' },
            { data: 'column_tpbRefNo' },
            { data: 'column_tpbClient' },
            { data: 'column_tpbOfficer' },
            { data: 'column_tpbSubmissionDate' },
            { data: 'column_tpbLastUpdateDate' },
            { data: 'column_tpbNo' },
            { data: 'column_function' },                   
         ], 
         initComplete: function () {
            this.api()
            .columns([0,1,2,4,5,6])
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

      var tpbTable3 = $("#tpbTable3").DataTable({
         pageLength: 10,
         autoWidth: false,
         processing: false,
         serverSide: true,
         serverMethod: 'post',
         ajax: '<?=$request->baseUrl();?>/script/tpbList.php?type=3',
            "columns": [
            { data: 'column_tpbID' },
            { data: 'column_tpbRefNo' },
            { data: 'column_tpbClient' },
            { data: 'column_tpbOfficer' },
            { data: 'column_tpbSubmissionDate' },
            { data: 'column_tpbLastUpdateDate' },
            { data: 'column_tpbNo' },
            { data: 'column_function' },                   
         ], 
         initComplete: function () {
            this.api()
            .columns([0,1,2,4,5,6])
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

      var tpbTable4 = $("#tpbTable4").DataTable({
         pageLength: 10,
         autoWidth: false,
         processing: false,
         serverSide: true,
         serverMethod: 'post',
         ajax: '<?=$request->baseUrl();?>/script/tpbList.php?type=4',
            "columns": [
            { data: 'column_tpbID' },
            { data: 'column_tpbRefNo' },
            { data: 'column_tpbClient' },
            { data: 'column_tpbOfficer' },
            { data: 'column_tpbSubmissionDate' },
            { data: 'column_tpbLastUpdateDate' },
            { data: 'column_tpbNo' },
            { data: 'column_function' },                   
         ], 
         initComplete: function () {
            this.api()
            .columns([0,1,2,4,5,6])
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

      var tpbTable5 = $("#tpbTable5").DataTable({
         pageLength: 10,
         autoWidth: false,
         processing: false,
         serverSide: true,
         serverMethod: 'post',
         ajax: '<?=$request->baseUrl();?>/script/tpbList.php?type=5',
            "columns": [
            { data: 'column_tpbID' },
            { data: 'column_tpbRefNo' },
            { data: 'column_tpbClient' },
            { data: 'column_tpbOfficer' },
            { data: 'column_tpbSubmissionDate' },
            { data: 'column_tpbLastUpdateDate' },
            { data: 'column_tpbNo' },
            { data: 'column_function' },                   
         ], 
         initComplete: function () {
            this.api()
            .columns([0,1,2,4,5,6])
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

      var tpbTable6 = $("#tpbTable6").DataTable({
         pageLength: 10,
         autoWidth: false,
         processing: false,
         serverSide: true,
         serverMethod: 'post',
         ajax: '<?=$request->baseUrl();?>/script/tpbList.php?type=6',
            "columns": [
            { data: 'column_tpbID' },
            { data: 'column_tpbRefNo' },
            { data: 'column_tpbClient' },
            { data: 'column_tpbOfficer' },
            { data: 'column_tpbSubmissionDate' },
            { data: 'column_tpbLastUpdateDate' },
            { data: 'column_tpbNo' },
            { data: 'column_function' },                   
         ], 
         initComplete: function () {
            this.api()
            .columns([0,1,2,4,5,6])
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

      var tpbTable7 = $("#tpbTable7").DataTable({
         pageLength: 10,
         autoWidth: false,
         processing: false,
         serverSide: true,
         serverMethod: 'post',
         ajax: '<?=$request->baseUrl();?>/script/tpbList.php?type=7',
            "columns": [
            { data: 'column_tpbID' },
            { data: 'column_tpbRefNo' },
            { data: 'column_tpbClient' },
            { data: 'column_tpbOfficer' },
            { data: 'column_tpbSubmissionDate' },
            { data: 'column_tpbLastUpdateDate' },
            { data: 'column_tpbNo' },
            { data: 'column_function' },                   
         ], 
         initComplete: function () {
            this.api()
            .columns([0,1,2,4,5,6])
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

      var tpbTable8 = $("#tpbTable8").DataTable({
         pageLength: 10,
         autoWidth: false,
         processing: false,
         serverSide: true,
         serverMethod: 'post',
         ajax: '<?=$request->baseUrl();?>/script/tpbList.php?type=8',
            "columns": [
            { data: 'column_tpbID' },
            { data: 'column_tpbRefNo' },
            { data: 'column_tpbClient' },
            { data: 'column_tpbOfficer' },
            { data: 'column_tpbSubmissionDate' },
            { data: 'column_tpbLastUpdateDate' },
            { data: 'column_tpbNo' },
            { data: 'column_function' },                   
         ], 
         initComplete: function () {
            this.api()
            .columns([0,1,2,4,5,6])
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

      function showConditionCalendar(tpbID) {

        $("#selected_condition_month").flatpickr({
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

        ajaxFunc.apiCall("POST", "tpb/getConditionDateByMonth/"+$("#selected_condition_month").val(), {"tpbID":tpbID}, null, function (return_data) {                    
              if(return_data.content.success){                      
                    $("#conditionCalendar").html(return_data.content.message);
              }
        });
      }               
      

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

})
</script>
<script type="text/javascript" src="<?php Utility\WebSystem::path("js/prompt.js");?>"></script>
<script type="text/javascript" src="<?php Utility\WebSystem::path("js/ajaxFunc.js");?>"></script>

