<?php
include("view/layout/meta.php");
include("view/layout/head.php");
?>
<div class="main-panel">
<?php
include("view/layout/headExt.php");
$tpbData = Controller\tpb::getStatusStatistic();

?>  			
   <div class="container">
      <div class=""></div>
         <div class="row">
            <div class="col-md-12">
               <div class="card">
                  <div class="card-header">
                     <h4 class="card-title"><?=L('Dashboard');?></h4>
                  </div>
                  <div class="card-body">                  
                    <div class="row">
                      <div class="col-sm-6 col-md-3">
                        <a href="<?=$this->pageLink('page.userList');?>">
                          <div class="card card-stats card-info card-round">
                            <div class="card-body">
                              <div class="row">
                                <div class="col-5">
                                  <div class="icon-big text-center">                                
                                    <i class="fas fa-users text-warning"></i>
                                  </div>
                                </div>
                                <div class="col-7 col-stats">
                                  <div class="numbers">
                                  <p class="card-category"><?=L("menu.user");?></p>
                                  <h4 class="card-title"><?=Controller\user::findAll()->rowCount();?></h4>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>

                      <div class="col-sm-6 col-md-3">
                        <a href="<?=$this->pageLink('page.clientList');?>">
                          <div class="card card-stats card-success card-round">
                            <div class="card-body">
                              <div class="row">
                                <div class="col-5">
                                  <div class="icon-big text-center">
                                    <i class="fa fa-address-card text-success"></i>
                                  </div>
                                </div>
                                <div class="col-7 col-stats">
                                  <div class="numbers">
                                  <p class="card-category"><?=L("menu.client");?></p>
                                  <h4 class="card-title"><?=Controller\client::findAll()->rowCount();?></h4>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>

                      <div class="col-sm-6 col-md-3">
                        <a href="<?=$this->pageLink('page.tpbList');?>">
                          <div class="card card-stats card-secondary card-round">
                            <div class="card-body">
                              <div class="row">
                                <div class="col-5">
                                  <div class="icon-big text-center">
                                    <i class="fa fa-clipboard-check text-danger"></i>
                                  </div>
                                </div>
                                <div class="col-7 col-stats">
                                  <div class="numbers">
                                  <p class="card-category"><?=L("tpb.processing");?></p>
                                  <h4 class="card-title"><?=Controller\tpb::findAll(1)->rowCount()+Controller\tpb::findAll(2)->rowCount()+Controller\tpb::findAll(3)->rowCount()+Controller\tpb::findAll(4)->rowCount()+Controller\tpb::findAll(5)->rowCount();?></h4>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>        
                      
                      
                      <div class="col-sm-6 col-md-3">
                        <a href="<?=$this->pageLink('page.tpbList');?>">
                          <div class="card card-stats card-secondary card-round">
                            <div class="card-body">
                              <div class="row">
                                <div class="col-5">
                                  <div class="icon-big text-center">
                                    <i class="fa fa-clipboard-check text-danger"></i>
                                  </div>
                                </div>
                                <div class="col-7 col-stats">
                                  <div class="numbers">
                                  <p class="card-category"><?=L("tpb.completed");?></p>
                                  <h4 class="card-title"><?=Controller\tpb::findAll(6)->rowCount()+Controller\tpb::findAll(7)->rowCount()+Controller\tpb::findAll(8)->rowCount();?></h4>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>          

                      <div class="col-sm-6 col-md-3">
                        <a href="<?=$this->pageLink('page.taskList');?>">
                          <div class="card card-stats card-warning card-round">
                            <div class="card-body">
                              <div class="row">
                                <div class="col-5">
                                  <div class="icon-big text-center">
                                    <i class="fa fa-clipboard-check text-danger"></i>
                                  </div>
                                </div>
                                <div class="col-7 col-stats">
                                  <div class="numbers">
                                  <p class="card-category"><?=L("task.processing");?></p>
                                  <h4 class="card-title"><?=Controller\task::findAll(1)->rowCount();?></h4>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>      
                      
                      <div class="col-sm-6 col-md-3">
                        <a href="<?=$this->pageLink('page.taskList');?>">
                          <div class="card card-stats card-warning card-round">
                            <div class="card-body">
                              <div class="row">
                                <div class="col-5">
                                  <div class="icon-big text-center">
                                    <i class="fa fa-clipboard-check text-danger"></i>
                                  </div>
                                </div>
                                <div class="col-7 col-stats">
                                  <div class="numbers">
                                  <p class="card-category"><?=L("task.completed");?></p>
                                  <h4 class="card-title"><?=Controller\task::findAll(2)->rowCount();?></h4>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>                 
                      
                      <div class="col-sm-6 col-md-3">
                        <a href="<?=$this->pageLink('page.sttList');?>">
                          <div class="card card-stats card-danger card-round">
                            <div class="card-body">
                              <div class="row">
                                <div class="col-5">
                                  <div class="icon-big text-center">
                                    <i class="fa fa-clipboard-check text-danger"></i>
                                  </div>
                                </div>
                                <div class="col-7 col-stats">
                                  <div class="numbers">
                                  <p class="card-category"><?=L("stt.processing");?></p>
                                  <h4 class="card-title"><?=Controller\stt::findAll(1)->rowCount();?></h4>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>      
                      
                      <div class="col-sm-6 col-md-3">
                        <a href="<?=$this->pageLink('page.sttList');?>">
                          <div class="card card-stats card-danger card-round">
                            <div class="card-body">
                              <div class="row">
                                <div class="col-5">
                                  <div class="icon-big text-center">
                                    <i class="fa fa-clipboard-check text-danger"></i>
                                  </div>
                                </div>
                                <div class="col-7 col-stats">
                                  <div class="numbers">
                                  <p class="card-category"><?=L("stt.completed");?></p>
                                  <h4 class="card-title"><?=Controller\stt::findAll(2)->rowCount();?></h4>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>                     
                      
                      <div class="col-sm-6 col-md-3">
                        <a href="<?=$this->pageLink('page.stwList');?>">
                          <div class="card card-stats card-danger card-round">
                            <div class="card-body">
                              <div class="row">
                                <div class="col-5">
                                  <div class="icon-big text-center">
                                    <i class="fa fa-clipboard-check text-danger"></i>
                                  </div>
                                </div>
                                <div class="col-7 col-stats">
                                  <div class="numbers">
                                  <p class="card-category"><?=L("stw.processing");?></p>
                                  <h4 class="card-title"><?=Controller\stw::findAll(1)->rowCount();?></h4>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>      
                      
                      <div class="col-sm-6 col-md-3">
                        <a href="<?=$this->pageLink('page.stwList');?>">
                          <div class="card card-stats card-danger card-round">
                            <div class="card-body">
                              <div class="row">
                                <div class="col-5">
                                  <div class="icon-big text-center">
                                    <i class="fa fa-clipboard-check text-danger"></i>
                                  </div>
                                </div>
                                <div class="col-7 col-stats">
                                  <div class="numbers">
                                  <p class="card-category"><?=L("stw.completed");?></p>
                                  <h4 class="card-title"><?=Controller\stw::findAll(2)->rowCount();?></h4>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>                            
                      
                                    
                    </div>   
                  </div>
               </div>
            </div>
         </div>   
         
         <div class="row">
            <div class="col-md-4">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                      <div class="col-sm-12 col-md-12">                          
                              <div class="card card-stats card-round">
                                <div class="card-body">
                                  <div class="d-flex align-items-center">
                                    <h5 class="card-title"><?=L('task.notExpired');?></h5>
                                    <button class="btn btn-sm btn-primary btn-round ms-auto addTaskBtn">
                                      <i class="fa fa-plus"></i>
                                    </button>
                                  </div>   
                                  <div class="table-responsive">
                                    <table id="taskTable0" class="display table table-striped table-hover dataTable taskTable">
                                    <?=Controller\mainPage::genTableHeader();?>
                                        <!--<?=Controller\mainPage::genTableFooter();?>-->
                                        <tbody>
                                          <?php
                                          $content = Controller\mainPage::genTableContentData($mode=1);
                                          foreach($content as $listObj) {
                                              echo Controller\mainPage::genTableBodyRow($listObj);                                    
                                          } ?>
                                        </tbody>
                                    </table>
                                  </div>
                                </div>
                              </div>                        
                          </div>     
                          <div class="col-sm-12 col-md-12">                          
                              <div class="card card-stats card-round">                                    
                                <div class="card-body">

                                  <div class="d-flex align-items-center">
                                    <h5 class="card-title"><?=L('task.expired');?></h5>
                                    <button class="btn btn-sm btn-primary btn-round ms-auto addTaskBtn">
                                      <i class="fa fa-plus"></i>
                                    </button>
                                  </div>   

                                  <table id="taskTable1" class="display table table-striped table-hover dataTable taskTable">
                                    <?=Controller\mainPage::genTableHeader();?>
                                        <!--<?=Controller\mainPage::genTableFooter();?>-->
                                        <tbody>
                                          <?php
                                          $content = Controller\mainPage::genTableContentData($mode=2);
                                          foreach($content as $listObj) {
                                              echo Controller\mainPage::genTableBodyRow($listObj);                                    
                                          } ?>
                                        </tbody>
                                  </table>
                                </div>
                              </div>                        
                          </div>                    
                  </div>  
                </div>
              </div>
            </div>

            <div class="col-md-8">
              <div class="card">
                <div class="card-body">
                  <div class="row">                         
                      <div class="col-sm-12 col-md-12">                          
                          <div class="card card-stats card-round">
                            <div class="card-body">
                              
                              <?php
                                $selected_month = (isset($_GET['month']) && $_GET['month']!="")?$_GET['month']:date("Y-m");
                              ?>
                              <div class='row'>
                                <div class='col col-sm-12 col-md-3, col-lg-3'>
                                    <input type="text" class="form-control flatpickr-input" id="selected_month" name="selected_month" value="<?=$selected_month;?>" readonly>
                                </div>                    
                              </div>                                  
                              <div id='mainPageCalendar'>
                                <i class='fa fa-spinner fa-spin'></i>
                              </div>
                             
                            </div>
                          </div>                        
                      </div>               
                  </div>  
                </div>
              </div>
            </div>            
          </div>


          <div class="row">
            <div class="col-md-12">
            <div class="card">
                  <div class="card-header">
                     <div class="d-flex align-items-center">
                        <h4 class="card-title"><?=L('menu.tpb');?></h4>
                        <button class="btn btn-primary btn-round ms-auto addTpbBtn">
                           <i class="fa fa-plus"></i>
                        </button>
                     </div>    
                  </div>
                  <div class="card-body">  
                     <!-- nav menu -->                    
                     <ul class="nav nav-pills nav-secondary" id="pills-tab" role="tablist">
                        <li class="nav-item submenu tpbMenu" role="presentation">
                           <a class="nav-link active" id="pills-all-tab" data-bs-toggle="pill" href="#pills-all" role="tab" aria-controls="pills-all" aria-selected="false" tabindex="-1">All (<span id='count_0'><?=Controller\tpb::findAll()->rowCount();?></span>)</a>
                        </li>                     
                        <li class="nav-item submenu tpbMenu" role="presentation">
                           <a class="nav-link" id="pills-draft-tab" data-bs-toggle="pill" href="#pills-draft" role="tab" aria-controls="pills-draft" aria-selected="false" tabindex="-1">Draft (<span id='count_1'><?=Controller\tpb::findAll(1)->rowCount();?></span>)</a>
                        </li>
                        <li class="nav-item submenu tpbMenu" role="presentation">
                           <a class="nav-link" id="pills-followup-tab" data-bs-toggle="pill" href="#pills-followup" role="tab" aria-controls="pills-followup" aria-selected="false" tabindex="-1">Follow Up (<span id='count_2'><?=Controller\tpb::findAll(2)->rowCount();?></span>)</a>
                        </li>
                        <li class="nav-item submenu tpbMenu" role="presentation">
                           <a class="nav-link" id="pills-submitted-tab" data-bs-toggle="pill" href="#pills-submitted" role="tab" aria-controls="pills-submitted" aria-selected="true">Submitted (<span id='count_3'><?=Controller\tpb::findAll(3)->rowCount();?></span>)</a>
                        </li>
                        <li class="nav-item submenu tpbMenu" role="presentation">
                           <a class="nav-link" id="pills-received-tab" data-bs-toggle="pill" href="#pills-received" role="tab" aria-controls="pills-received" aria-selected="true">TPB Recevied (<span id='count_4'><?=Controller\tpb::findAll(4)->rowCount();?></span>)</a>
                        </li>     
                        <li class="nav-item submenu tpbMenu" role="presentation">
                           <a class="nav-link" id="pills-approved-tab" data-bs-toggle="pill" href="#pills-approved" role="tab" aria-controls="pills-approved" aria-selected="true">Approved (<span id='count_5'><?=Controller\tpb::findAll(5)->rowCount();?></span>)</a>
                        </li> 
                        <li class="nav-item submenu tpbMenu" role="presentation">
                           <a class="nav-link" id="pills-rejected-tab" data-bs-toggle="pill" href="#pills-rejected" role="tab" aria-controls="pills-rejected" aria-selected="true">Rejected (<span id='count_6'><?=Controller\tpb::findAll(6)->rowCount();?></span>)</a>
                        </li>   
                        <li class="nav-item submenu tpbMenu" role="presentation">
                           <a class="nav-link" id="pills-completed-tab" data-bs-toggle="pill" href="#pills-completed" role="tab" aria-controls="pills-completed" aria-selected="true">Completed (<span id='count_7'><?=Controller\tpb::findAll(7)->rowCount();?></span>)</a>
                        </li>  
                        <li class="nav-item submenu tpbMenu" role="presentation">
                           <a class="nav-link" id="pills-withdrawn-tab" data-bs-toggle="pill" href="#pills-withdrawn" role="tab" aria-controls="pills-withdrawn" aria-selected="true">Withdrawn (<span id='count_8'><?=Controller\tpb::findAll(8)->rowCount();?></span>)</a>
                        </li>                                                                                                    
                     </ul>                     
                     <!-- tab -->
                     <div class="tab-content mt-2 mb-3" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab">
                           <div class="table-responsive">
                              <table id="tpbTable0" class="display table table-striped table-hover tpbTable">
                                 <?=Controller\tpb::genTableHeader();?>
                                 <?=Controller\tpb::genTableFooter();?>
                                 <tbody>
                                 <?php
                                 $content = Controller\tpb::genTableContentData(0);
                                 foreach($content as $listObj) {
                                    echo Controller\tpb::genTableBodyRow($listObj);                                    
                                 } ?>   
                                 </tbody>
                              </table>
                           </div>
                        </div>                     
                        <div class="tab-pane fade" id="pills-draft" role="tabpanel" aria-labelledby="pills-draft-tab">
                           <div class="table-responsive">
                              <table id="tpbTable1" class="display table table-striped table-hover tpbTable">
                                 <?=Controller\tpb::genTableHeader();?>
                                 <?=Controller\tpb::genTableFooter();?>
                                 <tbody>
                                 <?php
                                 $content = Controller\tpb::genTableContentData(1);
                                 foreach($content as $listObj) {
                                    echo Controller\tpb::genTableBodyRow($listObj);                                    
                                 } ?>   
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="pills-followup" role="tabpanel" aria-labelledby="pills-followup-tab">
                           <div class="table-responsive">
                              <table id="tpbTable2" class="display table table-striped table-hover tpbTable">
                                 <?=Controller\tpb::genTableHeader();?>
                                 <?=Controller\tpb::genTableFooter();?>
                                 <tbody>
                                 <?php
                                 $content = Controller\tpb::genTableContentData(2);
                                 foreach($content as $listObj) {
                                    echo Controller\tpb::genTableBodyRow($listObj);                                    
                                 } ?>   
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="pills-submitted" role="tabpanel" aria-labelledby="pills-submitted-tab">
                           <div class="table-responsive">
                              <table id="tpbTable3" class="display table table-striped table-hover tpbTable">
                                 <?=Controller\tpb::genTableHeader();?>
                                 <?=Controller\tpb::genTableFooter();?>
                                 <tbody>
                                 <?php
                                 $content = Controller\tpb::genTableContentData(3);
                                 foreach($content as $listObj) {
                                    echo Controller\tpb::genTableBodyRow($listObj);                                    
                                 } ?>   
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="pills-received" role="tabpanel" aria-labelledby="pills-received-tab">
                           <div class="table-responsive">
                              <table id="tpbTable4" class="display table table-striped table-hover tpbTable">
                                 <?=Controller\tpb::genTableHeader();?>
                                 <?=Controller\tpb::genTableFooter();?>
                                 <tbody>
                                 <?php
                                 $content = Controller\tpb::genTableContentData(4);
                                 foreach($content as $listObj) {
                                    echo Controller\tpb::genTableBodyRow($listObj);                                    
                                 } ?>   
                                 </tbody>
                              </table>
                           </div>
                        </div> 
                        <div class="tab-pane fade" id="pills-approved" role="tabpanel" aria-labelledby="pills-approved-tab">
                           <div class="table-responsive">
                              <table id="tpbTable5" class="display table table-striped table-hover tpbTable">
                                 <?=Controller\tpb::genTableHeader();?>
                                 <?=Controller\tpb::genTableFooter();?>
                                 <tbody>
                                 <?php
                                 $content = Controller\tpb::genTableContentData(5);
                                 foreach($content as $listObj) {
                                    echo Controller\tpb::genTableBodyRow($listObj);                                    
                                 } ?>   
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="pills-rejected" role="tabpanel" aria-labelledby="pills-rejected-tab">
                           <div class="table-responsive">
                              <table id="tpbTable6" class="display table table-striped table-hover tpbTable">
                                 <?=Controller\tpb::genTableHeader();?>
                                 <?=Controller\tpb::genTableFooter();?>
                                 <tbody>
                                 <?php
                                 $content = Controller\tpb::genTableContentData(6);
                                 foreach($content as $listObj) {
                                    echo Controller\tpb::genTableBodyRow($listObj);                                    
                                 } ?>   
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="pills-completed" role="tabpanel" aria-labelledby="pills-completed-tab">
                           <div class="table-responsive">
                              <table id="tpbTable7" class="display table table-striped table-hover tpbTable">
                                 <?=Controller\tpb::genTableHeader();?>
                                 <?=Controller\tpb::genTableFooter();?>
                                 <tbody>
                                 <?php
                                 $content = Controller\tpb::genTableContentData(7);
                                 foreach($content as $listObj) {
                                    echo Controller\tpb::genTableBodyRow($listObj);                                    
                                 } ?>   
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="pills-withdrawn" role="tabpanel" aria-labelledby="pills-withdrawn-tab">
                           <div class="table-responsive">
                              <table id="tpbTable8" class="display table table-striped table-hover tpbTable">
                                 <?=Controller\tpb::genTableHeader();?>
                                 <?=Controller\tpb::genTableFooter();?>
                                 <tbody>
                                 <?php
                                 $content = Controller\tpb::genTableContentData(8);
                                 foreach($content as $listObj) {
                                    echo Controller\tpb::genTableBodyRow($listObj);                                    
                                 } ?>   
                                 </tbody>
                              </table>
                           </div>
                        </div>                                                                                                        
                     </div>                     
                  </div>
               </div>
            </div>
          </div>


          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header">
                  <div class="card-title"><?=L('ApplicationCount');?></div>
                </div>
                <div class="card-body">
                  <div class="chart-container"><div class="chartjs-size-monitor" style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
                    <canvas id="barChart" width="739" height="300" style="display: block; width: 739px; height: 300px;" class="chartjs-render-monitor"></canvas>
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
         },rowId: 'column_tpbID'
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
         },rowId: 'column_tpbID'
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
         },rowId: 'column_tpbID'
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
         },rowId: 'column_tpbID'
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
         },rowId: 'column_tpbID'
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
         },rowId: 'column_tpbID'
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
         },rowId: 'column_tpbID'
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
         },rowId: 'column_tpbID'
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
         },rowId: 'column_tpbID'
      }); 

      var labelArr1 = new Array();
      var dataArr1 = new Array();
      <?php foreach($tpbData as $key => $val){ ?>
      labelArr1.push('<?php echo $key; ?>');
      dataArr1.push('<?php echo $val; ?>');
      <?php } ?>
         

      var myBarChart = new Chart(barChart, {
         type: "bar",
         data: {
         labels: labelArr1,
         datasets: [
            {
               label: "<?=L('ApplicationCount');?>",
               backgroundColor: "#1572e8",
               borderColor: "#1572e8",
               data: dataArr1,
            },
         ],
         },
         options: {
         responsive: true,
         maintainAspectRatio: false,
         scales: {
            yAxes: [
               {
               ticks: {
                  beginAtZero: true,
               },
               },
            ],
         },
         },
      });    
      
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

      function showCalendar() {
        ajaxFunc.apiCall("GET", "mainPage/getCalendarDateByMonth/"+selected_month, null, null, function (return_data) {                    
              if(return_data.content.success){                      
                  $("#mainPageCalendar").html(return_data.content.message);
                  calendarCellClick();
              }
        });
      }      
      
      showCalendar();

      $("#selected_month").change(function(e){
        selected_month = $(this).val();
        showCalendar();
      });

      var taskTable0 = $("#taskTable0").DataTable({
          pageLength: 5,
          processing: false,
          serverSide: true,
          serverMethod: 'post',
          "dom": 'frtip',
          ajax: '<?=$request->baseUrl();?>/script/mainPageTaskList.php?mode=1',
          "columns": [
              { data: 'column_officer' },
              { data: 'column_description' }, 
              { data: 'column_deadline' }, 
              { data: 'column_function' }            
          ],              
      });

      var taskTable1 = $("#taskTable1").DataTable({
          pageLength: 5,
          processing: false,
          serverSide: true,
          serverMethod: 'post',
          "dom": 'frtip',
          ajax: '<?=$request->baseUrl();?>/script/mainPageTaskList.php?mode=2',
          "columns": [
              { data: 'column_officer' },
              { data: 'column_description' }, 
              { data: 'column_deadline' }, 
              { data: 'column_function' }            
          ],              
      });      

      $('.taskTable tbody').on('click', '.taskDone', function (e) {
          e.preventDefault();
          var checkbox = $(e.currentTarget);

          swal({
              title: "<?=L('MarkDoneAlertTitle');?>",
              text: "<?=L('MarkDoneAlertMessage');?>",
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

                ajaxFunc.apiCall("POST", "task/done/"+checkbox.data('id'), null, null, function(return_data){
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
                            taskTable0.ajax.reload();   
                            taskTable1.ajax.reload();   
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
                                       taskTable0.ajax.reload(myCallback, false);   
                                       taskTable1.ajax.reload(myCallback, false);   
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

      var myCallback = function () { 

         var table = $('.tpbTable').DataTable(); // Initialize your DataTable
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

      /* init datatables */
      function eotTable() {
         var eotTable = $("#eotTable").DataTable({
            pageLength: 10,
            autoWidth: false,
            processing: false,
            serverSide: true,
            serverMethod: 'post',
            ajax: '<?=$request->baseUrl();?>/script/eotList.php',
               "columns": [
               { data: 'column_eotID' },
               { data: 'column_conditionNo' },
               { data: 'column_extendMonth' },
               { data: 'column_reason' },
               { data: 'column_submissionDate' },
               { data: 'column_submissionMode' },
               { data: 'column_status' },
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



      }
           
      /* reload datatables */
      function reloadAllTable() {
         tpbTable0.ajax.reload(myCallback, false);           
         tpbTable1.ajax.reload(myCallback, false); 
         tpbTable2.ajax.reload(myCallback, false);   
         tpbTable3.ajax.reload(myCallback, false);   
         tpbTable4.ajax.reload(myCallback, false);   
         tpbTable5.ajax.reload(myCallback, false);   
         tpbTable6.ajax.reload(myCallback, false);   
         tpbTable7.ajax.reload(myCallback, false);   
         tpbTable8.ajax.reload(myCallback, false);     
         
         reloadStatistic();

      }

      function reloadStatistic() {
         ajaxFunc.apiCall("GET", "tpb/getStatusCount", null, null,  function (return_data) { 
               
            json = JSON.parse(return_data.content.message);
            Object.keys(json).forEach(function(key) {                   
               $("#count_"+key).html(json[key].count);
            });
         });
      }

      /* auto tap tab */
      function autoTab(tabID) {
         console.log("autoTab");
         $(".tpbAddMenuA").removeClass('active');
         $(".tpbAddMenuDiv").removeClass('show active');
         $("#"+tabID+"-tab").addClass('active');
         $("#"+tabID).addClass('show active');
      }

         $(".addTpbBtn").click(function(e){
            var button = $(e.currentTarget);
            e.preventDefault();
            /* call html form */
            ajaxFunc.apiCall("GET", "tpb/formAdd", null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Add');?> TPB <?=L('Record');?>");
                  if(form_data.content.success) {
                     modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                     modal.find('.modal-body').html(form_data.content.message);   
                     
                     /* init show/hide for land owner fields */
                     $(".landOwnerSection").show();
                     $(".notLandOwnerSection").hide();            

                     addMultiSelect();
                     addCalendar();

                     $("#addConditionRow").click(function(e){
                        addConditionRow();
                        removeConditionRow();
                     });

                     removeConditionRow();
                     
                     /* form submit */
                     modal.find('#msgBoxBtnPri').off('click');
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        if(document.getElementById("form-addTpb")!==null){    
                           var data = new FormData(document.getElementById("form-addTpb"));  
                           modal.find('#msgBoxBtnPri').html('<i class="fa fa-spinner fa-spin"></i>');
                           ajaxFunc.apiCall("POST", "tpb", data, "multipart/form-data", function(return_data){                              
                              if(return_data.content.success) {
                                 /* successfully added */
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
                                       reloadAllTable();
                                    } 
                                 });    
                              } else {
                                 /* unsuccessfully added, focuse to error field */
                                 
                                 autoTab(return_data.content.tab);
                                 $("#form-addTpb").find(".form-group").removeClass("has-error");
                                 $("#form-addTpb").find(".form-group").find(".hintHelp").text("");                                 
                                 $("#form-addTpb").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-addTpb").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-addTpb").find("#"+return_data.content.field).focus();
                              }
                              modal.find('#msgBoxBtnPri').html('<?=L("OK");?>');
                           });
                        }      
                     });  
                     
                     /* init multiselect field */
                     /*
                     $('#userID, #zoningID').multiselect({
                        enableHTML: true
                     });
                     */

                     $(".isLandOwnerSelect").click(function(){                        
                        if($(this).val()=="Y"){
                           $(".landOwnerSection").show();
                           $(".notLandOwnerSection").hide();
                        } else {
                           $(".landOwnerSection").hide();
                           $(".notLandOwnerSection").show();
                        }
                     })   

                     $("#next_pills-submission").click(function(e){                        
                        autoTab("pills-submission");
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
                                                            $("#tpbSelectedClentDetail").html(return_data.content.message);
                                                            downloadDoc();
                                                            var content = "<div class='row'>";
                                                                  content += "<div class='col-md-12 col-lg-12 text-end'>";
                                                                     content += "<button type='button' class='btn btn-md btn-success' id='next_pills-application'><?=L('Next');?></button>";
                                                                  content += "</div>";                 
                                                            content == "</div>"; 

                                                            $("#tpbSelectedClentDetail").append(content);

                                                            
                                                            $("#next_pills-application").click(function(e){
                                                               autoTab("pills-application");
                                                            });                                                            
                                                         } else {
                                                            $("#tpbSelectedClentDetail").html(return_data.content.message);
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
                           $("#tpbSelectedClentDetail").html("");
                        } else {
                           ajaxFunc.apiCall("GET", "client/detail/"+$(this).val(), null, null,  function (return_data) {                               
                              if(return_data.content.success) {                                 
                                 
                                 $("#tpbSelectedClentDetail").html(return_data.content.message);
                                 downloadDoc();
                                 var content = "<div class='row'>";
                                       content += "<div class='col-md-12 col-lg-12 text-end'>";
                                          content += "<button type='button' class='btn btn-md btn-success' id='next_pills-application'><?=L('Next');?></button>";
                                       content += "</div>";                 
                                 content == "</div>"; 

                                 $("#tpbSelectedClentDetail").append(content);

                                 
                                 $("#next_pills-application").click(function(e){
                                    autoTab("pills-application");
                                 });
                              } else {
                                 $("#tpbSelectedClentDetail").html(return_data.content.message);
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
        
        /* edit record */
        $('.tpbTable tbody').on('click', '.btnEdit', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            var tpbID = button.data('id');
            ajaxFunc.apiCall("GET", "tpb/formEdit/"+button.data('id'), null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Edit');?> TPB <?=L('Record');?>");   
                  modal.find('.modal-body').html("");    
                  modal.find('#msgBoxBtnPri').hide();   
                  if(form_data.content.success) {
                     modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                     modal.find('.modal-body').html(form_data.content.message);
                     addMultiSelect();
                     addCalendar();   
                     removeDoc(); 
                     downloadDoc();   
                                          
                     $("#addConditionRow").click(function(e){
                        addConditionRowWithStatus();
                        removeConditionRow();
                        addCalendar();
                     });

                     removeConditionRow();
                     showConditionCalendar(button.data('id'));      

                     $("#selected_condition_month").change(function(e){
                        selected_condition_month = $(this).val();
                        showConditionCalendar(button.data('id'));
                     });            

                     // add eot 
                     $(".addEotBtn").click(function(e){
                        var btn = $(e.currentTarget);
                        e.preventDefault();
                        /* call html form */
                        ajaxFunc.apiCall("GET", "eot/formAdd/"+btn.data('id'), null, null,  function (form_data) { 
                           $('#msgBox2').one('show.bs.modal', function (ev) {
                              var modal = $(this);
                              modal.find('#msgBoxLabel').html("<?=L('Add');?> EOT <?=L('Record');?>");
                              if(form_data.content.success) {
                                 //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                 modal.find('.modal-body').html(form_data.content.message);   
                                 
                                 /* init show/hide for land owner fields */                                         
                                 addCalendar();
                     
                                 /* init multiselect field */
                                 $('#conditionID').multiselect({
                                    enableHTML: false
                                 });                                 

                                 /* form submit */
                                 modal.find('#msgBoxBtnPri').off('click');
                                 modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                    if(document.getElementById("form-addEOT")!==null){    
                                       var data = new FormData(document.getElementById("form-addEOT"));  
                                       modal.find('#msgBoxBtnPri').html('<i class="fa fa-spinner fa-spin"></i>');
                                       ajaxFunc.apiCall("POST", "eot", data, "multipart/form-data", function(return_data){                              
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
                                                   eotTable.ajax.reload(myCallback, false);
                                                } 
                                             });    
                                          } else {
                                             /* unsuccessfully added, focuse to error field */
                                             $("#form-addEOT").find(".form-group").removeClass("has-error");
                                             $("#form-addEOT").find(".form-group").find(".hintHelp").text("");                                 
                                             $("#form-addEOT").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                             $("#form-addEOT").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                             $("#form-addEOT").find("#"+return_data.content.field).focus();
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
                     
                     // add condition 
                     $(".addConditionBtn").click(function(e){
                        var btn = $(e.currentTarget);
                        e.preventDefault();
                        /* call html form */
                        ajaxFunc.apiCall("GET", "condition/formAdd/"+btn.data('id'), null, null,  function (form_data) { 
                           $('#msgBox2').one('show.bs.modal', function (ev) {
                              var modal = $(this);
                              modal.find('#msgBoxLabel').html("<?=L('Add');?> Condition <?=L('Record');?>");
                              if(form_data.content.success) {
                                 //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                 modal.find('.modal-body').html(form_data.content.message);   
                                 
                                 /* init show/hide for land owner fields */                                         
                                 addCalendar();
                     
                                 /* init multiselect field */
                                 $('#conditionID').multiselect({
                                    enableHTML: false
                                 });                                 

                                 /* form submit */
                                 modal.find('#msgBoxBtnPri').off('click');
                                 modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                    if(document.getElementById("form-addCondition")!==null){    
                                       var data = new FormData(document.getElementById("form-addCondition"));  
                                       modal.find('#msgBoxBtnPri').html('<i class="fa fa-spinner fa-spin"></i>');
                                       ajaxFunc.apiCall("POST", "condition", data, "multipart/form-data", function(return_data){                              
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
                                                   conditionTable.ajax.reload(myCallback, false);
                                                   showConditionCalendar(button.data('id'));
                                                } 
                                             });    
                                          } else {
                                             /* unsuccessfully added, focuse to error field */
                                             $("#form-addCondition").find(".form-group").removeClass("has-error");
                                             $("#form-addCondition").find(".form-group").find(".hintHelp").text("");                                 
                                             $("#form-addCondition").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                             $("#form-addCondition").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                             $("#form-addCondition").find("#"+return_data.content.field).focus();
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
                     
                     /* add stt record */
                     $(".addSttBtn").click(function(e){
                        
                        var button = $(e.currentTarget);
                        e.preventDefault();
                        ajaxFunc.apiCall("POST", "stt/formAdd", {'tpbID':tpbID}, null,  function (form_data) { 
                           $('#msgBox2').one('show.bs.modal', function (ev) {
                              var modal = $(this);
                              modal.find('#msgBoxLabel').html("<?=L('Add');?> <?=L('Record');?>");
                              if(form_data.content.success) {
                                 modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                 modal.find('.modal-body').html(form_data.content.message);
                              
                                 addCalendar();    
                                 
                                 $("#addMailingLogRow").click(function(e){
                                    addMailingLogRow();
                                    removeMailingLogRow();
                                 });

                                 removeMailingLogRow();  
                                 
                                 ajaxFunc.apiCall("GET", "client/detail/"+$("#clientID").val(), null, null,  function (return_data) {                               
                                    if(return_data.content.success) {
                                       $("#sttSelectedClentDetail").html(return_data.content.message);
                                       downloadDoc();                                                 
                                    } else {
                                       $("#sttSelectedClentDetail").html(return_data.content.message);
                                    }     
                                 });                                 

                                 modal.find('#msgBoxBtnPri').off('click');                                 
                                 modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                    if(document.getElementById("form-addStt")!==null){                               
                                       var data = new FormData(document.getElementById("form-addStt"));  
                                       ajaxFunc.apiCall("POST", "stt", data, "multipart/form-data", function(return_data){
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
                                                   //location.reload();  
                                                   sttTable.ajax.reload(myCallback, false);   
                                                } 
                                             });    
                                          } else {
                                             autoTab(return_data.content.tab);
                                             $("#form-addStt").find(".form-group").removeClass("has-error");
                                             $("#form-addStt").find(".form-group").find(".hintHelp").text("");
                                             $("#form-addStt").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                             $("#form-addStt").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                             $("#form-addStt").find("#"+return_data.content.field).focus();
                                          }
                                       });
                                    }      
                                 });  
                                 
                                 /*
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
                                                                        $("#tpbSelectedClentDetail").html(return_data.content.message);
                                                                        downloadDoc();                                                 
                                                                     } else {
                                                                        $("#tpbSelectedClentDetail").html(return_data.content.message);
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
                                       $("#tpbSelectedClentDetail").html("");
                                    } else {
                                       ajaxFunc.apiCall("GET", "client/detail/"+$(this).val(), null, null,  function (return_data) {                               
                                          if(return_data.content.success) {                                 
                                             $("#tpbSelectedClentDetail").html(return_data.content.message);
                                             downloadDoc();
                                          } else {
                                             $("#tpbSelectedClentDetail").html(return_data.content.message);
                                          }     
                                       });

                                       
                                    }
                                 });                     
                                 */
                              } else {
                                 modal.find('.modal-body').html(form_data.content.message);
                                 modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                    $("#msgBox2").modal("hide");   
                                 });
                              }                    

                           }).modal('show')
                           /* remove xl modal on modal close */
                           $('#msgBox2').on('hidden.bs.modal', function (e) {
                              $(this).find('.modal-dialog').removeClass("modal-xl");
                           }) 
                        });
                     });                     
                     
                     /* add stw record */
                     $(".addStwBtn").click(function(e){
                        
                        var button = $(e.currentTarget);
                        e.preventDefault();
                        ajaxFunc.apiCall("POST", "stw/formAdd", {'tpbID':tpbID}, null,  function (form_data) { 
                           $('#msgBox2').one('show.bs.modal', function (ev) {
                              var modal = $(this);
                              modal.find('#msgBoxLabel').html("<?=L('Add');?> <?=L('Record');?>");
                              if(form_data.content.success) {
                                 modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                 modal.find('.modal-body').html(form_data.content.message);
                              
                                 addCalendar();    
                                 
                                 $("#addMailingLogRow").click(function(e){
                                    addMailingLogRow();
                                    removeMailingLogRow();
                                 });

                                 removeMailingLogRow();  
                                 
                                 ajaxFunc.apiCall("GET", "client/detail/"+$("#clientID").val(), null, null,  function (return_data) {                               
                                    if(return_data.content.success) {
                                       $("#stwSelectedClentDetail").html(return_data.content.message);
                                       downloadDoc();                                                 
                                    } else {
                                       $("#stwSelectedClentDetail").html(return_data.content.message);
                                    }     
                                 });                                 

                                 modal.find('#msgBoxBtnPri').off('click');                                 
                                 modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                    if(document.getElementById("form-addStw")!==null){                               
                                       var data = new FormData(document.getElementById("form-addStw"));  
                                       ajaxFunc.apiCall("POST", "stw", data, "multipart/form-data", function(return_data){
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
                                 
                                 /*
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
                                                                        $("#tpbSelectedClentDetail").html(return_data.content.message);
                                                                        downloadDoc();                                                 
                                                                     } else {
                                                                        $("#tpbSelectedClentDetail").html(return_data.content.message);
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
                                       $("#tpbSelectedClentDetail").html("");
                                    } else {
                                       ajaxFunc.apiCall("GET", "client/detail/"+$(this).val(), null, null,  function (return_data) {                               
                                          if(return_data.content.success) {                                 
                                             $("#tpbSelectedClentDetail").html(return_data.content.message);
                                             downloadDoc();
                                          } else {
                                             $("#tpbSelectedClentDetail").html(return_data.content.message);
                                          }     
                                       });

                                       
                                    }
                                 });                     
                                 */
                              } else {
                                 modal.find('.modal-body').html(form_data.content.message);
                                 modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                    $("#msgBox2").modal("hide");   
                                 });
                              }                    

                           }).modal('show')
                           /* remove xl modal on modal close */
                           $('#msgBox2').on('hidden.bs.modal', function (e) {
                              $(this).find('.modal-dialog').removeClass("modal-xl");
                           }) 
                        });
                     });   

                     modal.find('#save_pills-applicant').click(function(e){
                        if(document.getElementById("form-editTPB-applicant")!==null){    
                           var data = new FormData(document.getElementById("form-editTPB-applicant"));  
                           modal.find('#msgBoxBtnPri').html('<i class="fa fa-spinner fa-spin"></i>');
                           ajaxFunc.apiCall("POST", "tpb/applicantEdit/"+button.data('id'), data, "multipart/form-data", function(return_data){                              
                              if(return_data.content.success) {
                                 /* successfully added */
                                 //$("#msgBox").modal("hide");    
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
                                    $("#form-editTPB-applicant").find(".form-group").removeClass("has-error");                                    
                                    
                                    if (willOK) {                                       
                                       reloadAllTable();
                                    } 
                                    
                                 });    
                              } else {
                                 /* unsuccessfully added, focuse to error field */
                                 autoTab(return_data.content.tab);
                                 $("#form-editTPB-applicant").find(".form-group").removeClass("has-error");
                                 $("#form-editTPB-applicant").find(".form-group").find(".hintHelp").text("");                                 
                                 $("#form-editTPB-applicant").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-editTPB-applicant").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-editTPB-applicant").find("#"+return_data.content.field).focus();
                              }
                              modal.find('#msgBoxBtnPri').html('<?=L("OK");?>');
                           });
                        }  
                     });
                     
                     modal.find('#save_pills-submission').click(function(e){
                        if(document.getElementById("form-editTPB-submission")!==null){    
                           var data = new FormData(document.getElementById("form-editTPB-submission"));  
                           modal.find('#msgBoxBtnPri').html('<i class="fa fa-spinner fa-spin"></i>');
                           ajaxFunc.apiCall("POST", "tpb/submissionEdit/"+button.data('id'), data, "multipart/form-data", function(return_data){                              
                              if(return_data.content.success) {
                                 /* successfully added */
                                 //$("#msgBox").modal("hide");    
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
                                    $("#form-editTPB-submission").find(".form-group").removeClass("has-error");
                                    $("#form-editTPB-submission").find(".removeDoc").attr("data-id", return_data.content.submissionDocID);
                                    $("#form-editTPB-submission").find(".downloadDoc").attr("data-id", return_data.content.submissionDocID);
                                    
                                    if(return_data.content.submissionDocID>0) {
                                       $("#form-editTPB-submission").find(".btnGrp").show();     
                                    }
                                    /*
                                    if (willOK) {                                       
                                       reloadAllTable();
                                    } 
                                    */
                                 });    
                              } else {
                                 /* unsuccessfully added, focuse to error field */
                                 if(return_data.content.field!='notice'){
                                    autoTab(return_data.content.tab);
                                    $("#form-editTPB-submission").find(".form-group").removeClass("has-error");
                                    $("#form-editTPB-submission").find(".form-group").find(".hintHelp").text("");                                 
                                    $("#form-editTPB-submission").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                    $("#form-editTPB-submission").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                    $("#form-editTPB-submission").find("#"+return_data.content.field).focus();
                                 } else {
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
                                    });
                                 }
                              }
                              modal.find('#msgBoxBtnPri').html('<?=L("OK");?>');
                           });
                        }  
                     });    
                     
                     modal.find('#save_pills-application').click(function(e){
                        if(document.getElementById("form-editTPB-application")!==null){    
                           var data = new FormData(document.getElementById("form-editTPB-application"));  
                           modal.find('#msgBoxBtnPri').html('<i class="fa fa-spinner fa-spin"></i>');
                           ajaxFunc.apiCall("POST", "tpb/applicationEdit/"+button.data('id'), data, "multipart/form-data", function(return_data){                              
                              if(return_data.content.success) {
                                 /* successfully added */
                                 //$("#msgBox").modal("hide");    
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
                                    $("#form-editTPB-application").find(".form-group").removeClass("has-error");

                                    $("#form-editTPB-application").find(".authorizationLetterRemove").attr("data-id", return_data.content.authorizationLetterDocID);
                                    $("#form-editTPB-application").find(".authorizationLetterDownload").attr("data-id", return_data.content.authorizationLetterDocID);                                    
                                    if(return_data.content.authorizationLetterDocID>0) {
                                       $("#form-editTPB-application").find(".authorizationLetterDownload").closest(".btnGrp").show();     
                                    }

                                    $("#form-editTPB-application").find(".landRegistryRemove").attr("data-id", return_data.content.landRegistryDocID);
                                    $("#form-editTPB-application").find(".landRegistryDownload").attr("data-id", return_data.content.landRegistryDocID);                                    
                                    if(return_data.content.landRegistryDocID>0) {
                                       $("#form-editTPB-application").find(".landRegistryDownload").closest(".btnGrp").show();     
                                    }
                                    
                                    $("#form-editTPB-application").find(".siteNoticeRemove").attr("data-id", return_data.content.siteNoticeDocID);
                                    $("#form-editTPB-application").find(".siteNoticeDownload").attr("data-id", return_data.content.siteNoticeDocID);                                    
                                    if(return_data.content.siteNoticeDocID>0) {
                                       $("#form-editTPB-application").find(".siteNoticeDownload").closest(".btnGrp").show();     
                                    }
                                    
                                    $("#form-editTPB-application").find(".letterToRCRemove").attr("data-id", return_data.content.letterToRCDocID);
                                    $("#form-editTPB-application").find(".letterToRCDownload").attr("data-id", return_data.content.letterToRCDocID);                                    
                                    if(return_data.content.letterToRCDocID>0) {
                                       $("#form-editTPB-application").find(".letterToRCDownload").closest(".btnGrp").show();     
                                    }                                    


                                    /*
                                    if (willOK) {                                       
                                       reloadAllTable();
                                    } 
                                    */
                                 });    
                              } else {
                                 /* unsuccessfully added, focuse to error field */
                                 autoTab(return_data.content.tab);
                                 $("#form-editTPB-application").find(".form-group").removeClass("has-error");
                                 $("#form-editTPB-application").find(".form-group").find(".hintHelp").text("");                                 
                                 $("#form-editTPB-application").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-editTPB-application").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-editTPB-application").find("#"+return_data.content.field).focus();
                              }
                              modal.find('#msgBoxBtnPri').html('<?=L("OK");?>');
                           });
                        }  
                     });      
                     
                     modal.find('#save_pills-receive').click(function(e){
                        if(document.getElementById("form-editTPB-receive")!==null){    
                           var data = new FormData(document.getElementById("form-editTPB-receive"));  
                           modal.find('#msgBoxBtnPri').html('<i class="fa fa-spinner fa-spin"></i>');
                           ajaxFunc.apiCall("POST", "tpb/receiveEdit/"+button.data('id'), data, "multipart/form-data", function(return_data){                              
                              if(return_data.content.success) {
                                 /* successfully added */
                                 //$("#msgBox").modal("hide");    
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
                                    $("#form-editTPB-receive").find(".form-group").removeClass("has-error");                                    
                                    /*
                                    if (willOK) {                                       
                                       reloadAllTable();
                                    } 
                                    */
                                 });    
                              } else {
                                 /* unsuccessfully added, focuse to error field */
                                 if(return_data.content.field!='notice'){
                                    autoTab(return_data.content.tab);
                                    $("#form-editTPB-receive").find(".form-group").removeClass("has-error");
                                    $("#form-editTPB-receive").find(".form-group").find(".hintHelp").text("");                                 
                                    $("#form-editTPB-receive").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                    $("#form-editTPB-receive").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                    $("#form-editTPB-receive").find("#"+return_data.content.field).focus();
                                 } else {
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
                                    });
                                 }
                              }
                              modal.find('#msgBoxBtnPri').html('<?=L("OK");?>');
                           });
                        }  
                     });             
                     
                     modal.find('#save_pills-decision').click(function(e){
                        if(document.getElementById("form-editTPB-decision")!==null){    
                           var data = new FormData(document.getElementById("form-editTPB-decision"));  
                           modal.find('#msgBoxBtnPri').html('<i class="fa fa-spinner fa-spin"></i>');
                           ajaxFunc.apiCall("POST", "tpb/decisionEdit/"+button.data('id'), data, "multipart/form-data", function(return_data){                              
                              if(return_data.content.success) {
                                 /* successfully added */
                                 //$("#msgBox").modal("hide");    
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
                                    $("#form-editTPB-decision").find(".form-group").removeClass("has-error");                                    
                                    
                                    if (willOK) {                                       
                                       reloadAllTable();
                                    } 
                                    
                                 });    
                              } else {
                                 /* unsuccessfully added, focuse to error field */
                                 if(return_data.content.field!='notice'){
                                    autoTab(return_data.content.tab);
                                    $("#form-editTPB-decision").find(".form-group").removeClass("has-error");
                                    $("#form-editTPB-decision").find(".form-group").find(".hintHelp").text("");                                 
                                    $("#form-editTPB-decision").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                    $("#form-editTPB-decision").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                    $("#form-editTPB-decision").find("#"+return_data.content.field).focus();
                                 } else {
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
                                    });
                                 }
                              }
                              modal.find('#msgBoxBtnPri').html('<?=L("OK");?>');
                           });
                        }  
                     });           
                     
                     /*
                     modal.find('#save_pills-condition').click(function(e){
                        if(document.getElementById("form-editTPB-condition")!==null){    
                           var data = new FormData(document.getElementById("form-editTPB-condition"));  
                           modal.find('#msgBoxBtnPri').html('<i class="fa fa-spinner fa-spin"></i>');
                           ajaxFunc.apiCall("POST", "tpb/conditionEdit/"+button.data('id'), data, "multipart/form-data", function(return_data){                              
                              if(return_data.content.success) {                                 
                                 //$("#msgBox").modal("hide");    
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
                                    $("#form-editTPB-condition").find(".form-group").removeClass("has-error");                                    
                                    
                                    if (willOK) {                                       
                                       reloadAllTable();
                                    } 
                                    
                                 });    
                              } else {
                                 // unsuccessfully added, focuse to error field 
                                 autoTab(return_data.content.tab);
                                 $("#form-editTPB-condition").find(".form-group").removeClass("has-error");
                                 $("#form-editTPB-condition").find(".form-group").find(".hintHelp").text("");                                 
                                 $("#form-editTPB-condition").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-editTPB-condition").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-editTPB-condition").find("#"+return_data.content.field).focus();
                              }
                              modal.find('#msgBoxBtnPri').html('<?=L("OK");?>');
                           });
                        }  
                     }); */
                     
                     /* init multiselect field */
                     /*
                     $('#userID, #zoningID').multiselect({
                        enableHTML: true
                     });
                     */

                     $(".isLandOwnerSelect").click(function(){                        
                        if($(this).val()=="Y"){
                           $(".landOwnerSection").show();
                           $(".notLandOwnerSection").hide();
                        } else {
                           $(".landOwnerSection").hide();
                           $(".notLandOwnerSection").show();
                        }
                     })   

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
                                                            $("#tpbSelectedClentDetail").html(return_data.content.message);
                                                            downloadDoc();
                                                         } else {
                                                            $("#tpbSelectedClentDetail").html(return_data.content.message);
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
                           $("#tpbSelectedClentDetail").html("");
                        } else {
                           ajaxFunc.apiCall("GET", "client/detail/"+$(this).val(), null, null,  function (return_data) {                               
                              if(return_data.content.success) {
                                 $("#tpbSelectedClentDetail").html(return_data.content.message);   
                                 downloadDoc();                             
                              } else {
                                 $("#tpbSelectedClentDetail").html(return_data.content.message);
                              }     
                           });                           
                        }
                     });  
                     
       
                     var eotTable = $("#eotTable").DataTable({
                        pageLength: 10,
                        autoWidth: false,
                        processing: false,
                        serverSide: true,
                        serverMethod: 'post',
                        ajax: '<?=$request->baseUrl();?>/script/eotList.php?mode=edit&tpbID='+button.data('id'),
                           "columns": [
                           { data: 'column_eotID' },
                           { data: 'column_extendMonth' },
                           { data: 'column_reason' },
                           { data: 'column_submissionDate' },
                           { data: 'column_submissionMode' },
                           { data: 'column_status' },
                           { data: 'column_function' },                   
                        ], 
                        initComplete: function () {
                           this.api()
                           .columns([0,1,2,3,4,5])
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

                     $('.eotTable tbody').on('click', '.btnView', function (e) {
                           e.preventDefault();
                           var button = $(e.currentTarget);
                           ajaxFunc.apiCall("GET", "eot/detail/"+button.data('id'), null, null,  function (form_data) { 
                              $('#msgBox2').one('show.bs.modal', function (ev) {                 
                                 var modal = $(this);
                                 //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                 modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('tpb.EOTDetail');?> <?=L('Record');?>");
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
                              $('#msgBox').on('hidden.bs.modal', function (e) {
                                 $(this).find('.modal-dialog').removeClass("modal-xl");
                              })
                              */               
                           });

                     });  

                     /* delete eot record */
                     $('.eotTable tbody').on('click', '.btnDel', function (e) {

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

                              ajaxFunc.apiCall("DELETE", "eot/"+button.data('id'), null, null, function(return_data){
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
                                          eotTable.ajax.reload(myCallback, false);
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

                     /* edit eot record */
                     $('.eotTable tbody').on('click', '.btnEdit', function (e) {
                           
                           e.preventDefault();
                           var button = $(e.currentTarget);
                           ajaxFunc.apiCall("GET", "eot/formEdit/"+button.data('id'), {"tpbID":button.data('tpbid')}, null,  function (form_data) { 
                              $('#msgBox2').one('show.bs.modal', function (ev) {                 
                                 var modal = $(this);
                                 modal.find('#msgBoxLabel').html("<?=L('tpb.EOTDetail');?>");
                                 if(form_data.content.success) {
                                    //modal.find('.modal-dialog').addClass("modal-lg");
                                    modal.find('.modal-body').html(form_data.content.message);                    

                                    $('#conditionID').multiselect({
                                       enableHTML: false
                                    });                                           

                                    modal.find('#msgBoxBtnPri').off('click');
                                    modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                       
                                       if(document.getElementById("form-editEOT")!==null){    
                                          var data = new FormData(document.getElementById("form-editEOT"));  
                                          ajaxFunc.apiCall("POST", "eot/"+button.data('id'), data, "multipart/form-data", function(return_data){
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
                                                      $("#form-editEOT").find(".removeDoc").attr("data-id", return_data.content.submissionDocID);
                                                      $("#form-editEOT").find(".downloadDoc").attr("data-id", return_data.content.submissionDocID);
                                                      eotTable.ajax.reload(myCallback, false); 
                                                   } 
                                                });    
                                             } else {
                                                $("#form-editEOT").find(".form-group").removeClass("has-error");
                                                $("#form-editEOT").find(".form-group").find(".hintHelp").text("");
                                                $("#form-editEOT").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                                $("#form-editEOT").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                                $("#form-editEOT").find("#"+return_data.content.field).focus();
                                             }
                                          });
                                       } 
                                       
                                    }); 

                                    addCalendar();    
                                    removeEOTDoc(); 
                                    downloadDoc();                                      
                                                                  
                                 
                                 } else {
                                    modal.find('.modal-body').html(form_data.content.message);
                                    modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                       $("#msgBox2").modal("hide");   
                                    });
                                 }  
                                             
                              }).modal('show')

                           });
                         
                         
                     });       
 
                     // conditionTable();
                     var conditionTable = $("#conditionTable").DataTable({
                        pageLength: 10,
                        autoWidth: false,
                        processing: false,
                        serverSide: true,
                        serverMethod: 'post',
                        ajax: '<?=$request->baseUrl();?>/script/conditionList.php?mode=edit&tpbID='+button.data('id'),
                           "columns": [
                           { data: 'column_conditionID' },
                           { data: 'column_conditionNo' },
                           { data: 'column_description' },
                           { data: 'column_deadline' },
                           { data: 'column_status' },
                           { data: 'column_function' },                   
                        ], 
                        initComplete: function () {
                           this.api()
                           .columns([0,1,2,3,4])
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

                     $('.conditionTable tbody').on('click', '.btnView', function (e) {
                           e.preventDefault();
                           var button = $(e.currentTarget);
                           ajaxFunc.apiCall("GET", "condition/detail/"+button.data('id'), null, null,  function (form_data) { 
                              $('#msgBox2').one('show.bs.modal', function (ev) {                 
                                 var modal = $(this);
                                 //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                 modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('tpb.conditionDetail');?> <?=L('Record');?>");
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
                              $('#msgBox').on('hidden.bs.modal', function (e) {
                                 $(this).find('.modal-dialog').removeClass("modal-xl");
                              })
                              */               
                           });

                     });

                     /* delete condition record */
                     $('.conditionTable tbody').on('click', '.btnDel', function (e) {

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

                              ajaxFunc.apiCall("DELETE", "condition/"+button.data('id'), null, null, function(return_data){
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
                                          conditionTable.ajax.reload(myCallback, false);
                                          showConditionCalendar(tpbID);
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
                     
                     /* edit condition record */
                     $('.conditionTable tbody').on('click', '.btnEdit', function (e) {
                           
                           e.preventDefault();
                           var button = $(e.currentTarget);
                           ajaxFunc.apiCall("GET", "condition/formEdit/"+button.data('id'), {"tpbID":button.data('tpbid')}, null,  function (form_data) { 
                              $('#msgBox2').one('show.bs.modal', function (ev) {                 
                                 var modal = $(this);
                                 modal.find('#msgBoxLabel').html("<?=L('tpb.conditionDetail');?>");
                                 if(form_data.content.success) {
                                    //modal.find('.modal-dialog').addClass("modal-lg");
                                    modal.find('.modal-body').html(form_data.content.message);                                     

                                    modal.find('#msgBoxBtnPri').off('click');
                                    modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                       
                                       if(document.getElementById("form-editCondition")!==null){    
                                          var data = new FormData(document.getElementById("form-editCondition"));  
                                          ajaxFunc.apiCall("POST", "condition/"+button.data('id'), data, "multipart/form-data", function(return_data){
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
                                                      conditionTable.ajax.reload(myCallback, false); 
                                                      showConditionCalendar(tpbID);
                                                   } 
                                                });    
                                             } else {
                                                $("#form-editCondition").find(".form-group").removeClass("has-error");
                                                $("#form-editCondition").find(".form-group").find(".hintHelp").text("");
                                                $("#form-editCondition").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                                $("#form-editCondition").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                                $("#form-editCondition").find("#"+return_data.content.field).focus();
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
                     
                     /* stt table */
                     var sttTable = $("#sttTable").DataTable({
                           pageLength: 10,
                           processing: false,
                           serverSide: true,
                           serverMethod: 'post',
                           autoWidth: false,
                           ajax: '<?=$request->baseUrl();?>/script/sttList.php?mode=edit&tpbID='+tpbID,
                           "columns": [
                              { data: 'column_sttID' },
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
                        },
                     });
           
                     /* delete stt record */
                     $('#sttTable tbody').on('click', '.btnDel', function (e) {

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

                              ajaxFunc.apiCall("DELETE", "stt/"+button.data('id'), null, null, function(return_data){
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
                                          sttTable.ajax.reload(myCallback, false);   
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

                     $('#sttTable tbody').on('click', '.btnView', function (e) {
                        e.preventDefault();
                        var button = $(e.currentTarget);
                        ajaxFunc.apiCall("GET", "stt/detail/"+button.data('id'), null, null,  function (form_data) { 
                           $('#msgBox2').one('show.bs.modal', function (ev) {                 
                              var modal = $(this);
                              modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                              modal.find('#msgBoxLabel').html("<?=L('View');?> STT <?=L('Record');?>");
                              if(form_data.content.success) {
                                 modal.find('.modal-body').html(form_data.content.message);                      

                                 ajaxFunc.apiCall("GET", "client/detail/"+$("#clientID").val(), null, null,  function (return_data) {                               
                                    if(return_data.content.success) {
                                       $("#sttSelectedClentDetail").html(return_data.content.message);
                                       downloadDoc();                                                 
                                    } else {
                                       $("#sttSelectedClentDetail").html(return_data.content.message);
                                    }     
                                 });                     
                                 modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                    $("#msgBox2").modal("hide");   
                                 });  
                                 
                                    var mailingLogTable = $("#mailingLogTable").DataTable({
                                       pageLength: 10,
                                       processing: false,
                                       serverSide: true,
                                       serverMethod: 'post',
                                       autoWidth: false,
                                       ajax: '<?=$request->baseUrl();?>/script/sttMailingLogList.php?mode=view&sttID='+button.data('id'),
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
                                          ajaxFunc.apiCall("GET", "stt/mailingLog/detail/"+button.data('id'), null, null,  function (form_data) { 
                                             $('#msgBox3').one('show.bs.modal', function (ev) {                 
                                                var modal = $(this);
                                                //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                                modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('stt.mailingDetail');?> <?=L('Record');?>");
                                                if(form_data.content.success) {
                                                   modal.find('.modal-body').html(form_data.content.message);                      
                                                   modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                                      $("#msgBox3").modal("hide");   
                                                   });     
                                                } else {
                                                   modal.find('.modal-body').html(form_data.content.message);
                                                   modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                                      $("#msgBox3").modal("hide");   
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
                                    $("#msgBox2").modal("hide");   
                                 });                    
                              }
                              downloadDoc();  
                                          
                           }).modal('show')

                           $('#msgBox2').on('hidden.bs.modal', function (e) {
                              $(this).find('.modal-dialog').removeClass("modal-xl");
                           })               
                        });

                     });   

                     /* edit stt record */
                     $('#sttTable tbody').on('click', '.btnEdit', function (e) {
                           e.preventDefault();
                           var button = $(e.currentTarget);
                           ajaxFunc.apiCall("POST", "stt/formEdit/"+button.data('id'), {'tpbID':tpbID}, null,  function (form_data) { 
                              $('#msgBox2').one('show.bs.modal', function (ev) {                 
                                 var modal = $(this);
                                 modal.find('#msgBoxLabel').html("<?=L('Edit');?> <?=L('Record');?>");                  
                                 if(form_data.content.success) {
                                    modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */

                                    modal.find('.modal-body').html(form_data.content.message);
                                    addCalendar();
                                    removeDoc(); 
                                    downloadDoc();  

                                    ajaxFunc.apiCall("GET", "client/detail/"+$("#clientID").val(), null, null,  function (return_data) {                               
                                       if(return_data.content.success) {
                                          $("#sttSelectedClentDetail").html(return_data.content.message);
                                          downloadDoc();                                                 
                                       } else {
                                          $("#sttSelectedClentDetail").html(return_data.content.message);
                                       }     
                                    });                                     
                                    /*
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
                                                                           $("#tpbSelectedClentDetail").html(return_data.content.message);
                                                                           downloadDoc();                                                 
                                                                        } else {
                                                                           $("#tpbSelectedClentDetail").html(return_data.content.message);
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
                                          $("#tpbSelectedClentDetail").html("");
                                       } else {
                                          ajaxFunc.apiCall("GET", "client/detail/"+$(this).val(), null, null,  function (return_data) {                               
                                             if(return_data.content.success) {                                 
                                                $("#tpbSelectedClentDetail").html(return_data.content.message);
                                                downloadDoc();
                                             } else {
                                                $("#tpbSelectedClentDetail").html(return_data.content.message);
                                             }     
                                          });

                                          
                                       }
                                    });                        
                                    */

                                    $("#addMailingLogRow").click(function(e){
                                       addMailingLogRow();
                                       removeMailingLogRow();
                                    });

                                    removeMailingLogRow();    
                                    
                                    modal.find('#msgBoxBtnPri').off('click');
                                    modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                       tinymce.triggerSave();
                                       if(document.getElementById("form-editStt")!==null){    
                                          var data = new FormData(document.getElementById("form-editStt"));  
                                          ajaxFunc.apiCall("POST", "stt/"+button.data('id'), data, "multipart/form-data", function(return_data){
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
                                                      //location.reload();  
                                                      sttTable.ajax.reload(myCallback, false);      
                                                   } 
                                                });    
                                             } else {
                                                autoTab(return_data.content.tab);
                                                $("#form-editStt").find(".form-group").removeClass("has-error");
                                                $("#form-editStt").find(".form-group").find(".hintHelp").text("");
                                                $("#form-editStt").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                                $("#form-editStt").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                                $("#form-editStt").find("#"+return_data.content.field).focus();
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
                                       ajax: '<?=$request->baseUrl();?>/script/sttMailingLogList.php?mode=edit&sttID='+button.data('id'),
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
                                       ajaxFunc.apiCall("GET", "stt/mailingLogFormAdd/"+btn.data('id'), null, null,  function (form_data) { 
                                          $('#msgBox3').one('show.bs.modal', function (ev) {
                                             var modal = $(this);
                                             modal.find('#msgBoxLabel').html("<?=L('Add');?> <?=L('stt.mailingDetail');?> <?=L('Record');?>");
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
                                                      ajaxFunc.apiCall("POST", "stt/mailingLog", data, "multipart/form-data", function(return_data){                              
                                                         if(return_data.content.success) {
                                                            /* successfully added */
                                                            $("#msgBox3").modal("hide");    
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
                                                   $("#msgBox3").modal("hide");   
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

                                             ajaxFunc.apiCall("DELETE", "stt/mailingLog/"+button.data('id'), null, null, function(return_data){
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

                                    $('#mailingLogTable tbody').on('click', '.btnView', function (e) {
                                          e.preventDefault();
                                          var button = $(e.currentTarget);
                                          ajaxFunc.apiCall("GET", "stt/mailingLog/detail/"+button.data('id'), null, null,  function (form_data) { 
                                             $('#msgBox3').one('show.bs.modal', function (ev) {                 
                                                var modal = $(this);
                                                //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                                modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('stt.mailingDetail');?> <?=L('Record');?>");
                                                if(form_data.content.success) {
                                                   modal.find('.modal-body').html(form_data.content.message);                      
                                                   modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                                      $("#msgBox3").modal("hide");   
                                                   });     
                                                } else {
                                                   modal.find('.modal-body').html(form_data.content.message);
                                                   modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                                      $("#msgBox3").modal("hide");   
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
                                    
                                    /* edit mailingLog record */
                                    $('.mailingLogTable tbody').on('click', '.btnEdit', function (e) {
                                          
                                          e.preventDefault();
                                          var button = $(e.currentTarget);
                                          ajaxFunc.apiCall("GET", "stt/mailingLogFormEdit/"+button.data('id'), {"sttID":button.data('sttid')}, null,  function (form_data) { 
                                             $('#msgBox3').one('show.bs.modal', function (ev) {                 
                                                var modal = $(this);
                                                modal.find('#msgBoxLabel').html("<?=L('stt.mailingDetail');?>");
                                                if(form_data.content.success) {
                                                   //modal.find('.modal-dialog').addClass("modal-lg");
                                                   modal.find('.modal-body').html(form_data.content.message);                                     

                                                   modal.find('#msgBoxBtnPri').off('click');
                                                   modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                                      
                                                      if(document.getElementById("form-editMailingLog")!==null){    
                                                         var data = new FormData(document.getElementById("form-editMailingLog"));  
                                                         ajaxFunc.apiCall("POST", "stt/mailingLog/"+button.data('id'), data, "multipart/form-data", function(return_data){
                                                            if(return_data.content.success) {
                                                               $("#msgBox3").modal("hide");    
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
                                                      $("#msgBox3").modal("hide");   
                                                   });
                                                }  
                                                            
                                             }).modal('show')

                                          });
                                       
                                       
                                    });                       

                                 } else {
                                    modal.find('.modal-body').html(form_data.content.message);
                                    modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                       $("#msgBox3").modal("hide");   
                                    });
                                 }  

                              }).modal('show')
                              /* remove xl modal on modal close*/
                              $('#msgBox3').on('hidden.bs.modal', function (e) {
                                 $(this).find('.modal-dialog').removeClass("modal-xl");
                              })
                           });

                     });
                     
                     /* stw table */
                     var stwTable = $("#stwTable").DataTable({
                           pageLength: 10,
                           processing: false,
                           serverSide: true,
                           serverMethod: 'post',
                           autoWidth: false,
                           ajax: '<?=$request->baseUrl();?>/script/stwList.php?mode=edit&tpbID='+tpbID,
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
                        },
                     });

                     /* delete stw record */
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

                     $('#stwTable tbody').on('click', '.btnView', function (e) {
                        e.preventDefault();
                        var button = $(e.currentTarget);
                        ajaxFunc.apiCall("GET", "stw/detail/"+button.data('id'), null, null,  function (form_data) { 
                           $('#msgBox2').one('show.bs.modal', function (ev) {                 
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
                                    $("#msgBox2").modal("hide");   
                                 });  
                                 
                                    var mailingLogTable = $("#mailingLogTable").DataTable({
                                       pageLength: 10,
                                       processing: false,
                                       serverSide: true,
                                       serverMethod: 'post',
                                       autoWidth: false,
                                       ajax: '<?=$request->baseUrl();?>/script/stwMailingLogList.php?mode=view&stwID='+button.data('id'),
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
                                             $('#msgBox3').one('show.bs.modal', function (ev) {                 
                                                var modal = $(this);
                                                //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                                modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('stw.mailingDetail');?> <?=L('Record');?>");
                                                if(form_data.content.success) {
                                                   modal.find('.modal-body').html(form_data.content.message);                      
                                                   modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                                      $("#msgBox3").modal("hide");   
                                                   });     
                                                } else {
                                                   modal.find('.modal-body').html(form_data.content.message);
                                                   modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                                      $("#msgBox3").modal("hide");   
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
                                    $("#msgBox2").modal("hide");   
                                 });                    
                              }
                              downloadDoc();  
                                          
                           }).modal('show')

                           $('#msgBox2').on('hidden.bs.modal', function (e) {
                              $(this).find('.modal-dialog').removeClass("modal-xl");
                           })               
                        });

                     });  

                     /* edit stw record */
                     $('#stwTable tbody').on('click', '.btnEdit', function (e) {
                           e.preventDefault();
                           var button = $(e.currentTarget);
                           ajaxFunc.apiCall("POST", "stw/formEdit/"+button.data('id'), {'tpbID':tpbID}, null,  function (form_data) { 
                              $('#msgBox2').one('show.bs.modal', function (ev) {                 
                                 var modal = $(this);
                                 modal.find('#msgBoxLabel').html("<?=L('Edit');?> <?=L('Record');?>");                  
                                 if(form_data.content.success) {
                                    modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */

                                    modal.find('.modal-body').html(form_data.content.message);
                                    addCalendar();
                                    removeDoc(); 
                                    downloadDoc();  

                                    ajaxFunc.apiCall("GET", "client/detail/"+$("#clientID").val(), null, null,  function (return_data) {                               
                                       if(return_data.content.success) {
                                          $("#stwSelectedClentDetail").html(return_data.content.message);
                                          downloadDoc();                                                 
                                       } else {
                                          $("#stwSelectedClentDetail").html(return_data.content.message);
                                       }     
                                    });                                     
                                    /*
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
                                                                           $("#tpbSelectedClentDetail").html(return_data.content.message);
                                                                           downloadDoc();                                                 
                                                                        } else {
                                                                           $("#tpbSelectedClentDetail").html(return_data.content.message);
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
                                          $("#tpbSelectedClentDetail").html("");
                                       } else {
                                          ajaxFunc.apiCall("GET", "client/detail/"+$(this).val(), null, null,  function (return_data) {                               
                                             if(return_data.content.success) {                                 
                                                $("#tpbSelectedClentDetail").html(return_data.content.message);
                                                downloadDoc();
                                             } else {
                                                $("#tpbSelectedClentDetail").html(return_data.content.message);
                                             }     
                                          });

                                          
                                       }
                                    });                        
                                    */

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
                                                      //location.reload();  
                                                      sttTable.ajax.reload(myCallback, false);      
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
                                       ajax: '<?=$request->baseUrl();?>/script/stwMailingLogList.php?stwID='+button.data('id'),
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
                                          $('#msgBox3').one('show.bs.modal', function (ev) {
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
                                                            $("#msgBox3").modal("hide");    
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
                                                   $("#msgBox3").modal("hide");   
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
                                    
                                    $('#mailingLogTable tbody').on('click', '.btnView', function (e) {
                                          e.preventDefault();
                                          var button = $(e.currentTarget);
                                          ajaxFunc.apiCall("GET", "stw/mailingLog/detail/"+button.data('id'), null, null,  function (form_data) { 
                                             $('#msgBox3').one('show.bs.modal', function (ev) {                 
                                                var modal = $(this);
                                                //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                                modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('stw.mailingDetail');?> <?=L('Record');?>");
                                                if(form_data.content.success) {
                                                   modal.find('.modal-body').html(form_data.content.message);                      
                                                   modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                                      $("#msgBox3").modal("hide");   
                                                   });     
                                                } else {
                                                   modal.find('.modal-body').html(form_data.content.message);
                                                   modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                                      $("#msgBox3").modal("hide");   
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
                                             $('#msgBox3').one('show.bs.modal', function (ev) {                 
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
                                                               $("#msgBox3").modal("hide");    
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
                                                      $("#msgBox3").modal("hide");   
                                                   });
                                                }  
                                                            
                                             }).modal('show')

                                          });
                                       
                                       
                                    });                       

                                 } else {
                                    modal.find('.modal-body').html(form_data.content.message);
                                    modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                       $("#msgBox3").modal("hide");   
                                    });
                                 }  

                              }).modal('show')
                              /* remove xl modal on modal close*/
                              $('#msgBox3').on('hidden.bs.modal', function (e) {
                                 $(this).find('.modal-dialog').removeClass("modal-xl");
                              })
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
                  $(this).find('#msgBoxBtnPri').show();   
                  $(this).find('.modal-dialog').removeClass("modal-xl");
                  reloadAllTable();
               })               
            });

        });        

        /* delete record */
        $('.tpbTable tbody').on('click', '.btnDel', function (e) {

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

                  ajaxFunc.apiCall("DELETE", "tpb/"+button.data('id'), null, null, function(return_data){
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
                              reloadAllTable();
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


         $('.tpbTable tbody').on('click', '.btnFollowUp', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            ajaxFunc.apiCall("GET", "tpb/followUpFormEdit/"+button.data('id'), null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('FollowUp');?>");
                  if(form_data.content.success) {
                     //modal.find('.modal-dialog').addClass("modal-lg");
                     modal.find('.modal-body').html(form_data.content.message);                    
                     modal.find('#msgBoxBtnPri').off('click');
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        
                        if(document.getElementById("form-editTpbFollowUp")!==null){    
                           var data = new FormData(document.getElementById("form-editTpbFollowUp"));  
                           ajaxFunc.apiCall("POST", "tpb/followUp/"+button.data('id'), data, "multipart/form-data", function(return_data){
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
                                       reloadAllTable();   
                                    } 
                                 });    
                              } else {
                                 $("#form-editTpbFollowUp").find(".form-group").removeClass("has-error");
                                 $("#form-editTpbFollowUp").find(".form-group").find(".hintHelp").text("");
                                 $("#form-editTpbFollowUp").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-editTpbFollowUp").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-editTpbFollowUp").find("#"+return_data.content.field).focus();
                              }
                           });
                        } 
                        
                     }); 

                     /*
                     $('#zoningID').multiselect({
                        enableHTML: true
                     });
                     */

                     addCalendar();
                    
                  } else {
                     modal.find('.modal-body').html(form_data.content.message);
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        $("#msgBox").modal("hide");   
                     });
                  }  
                               
               }).modal('show')
               /*
               $('#msgBox').on('hidden.bs.modal', function (e) {
                  $(this).find('.modal-dialog').removeClass("modal-lg");
               })   */
            });

        });

        /* receive record */
        $('.tpbTable tbody').on('click', '.btnReceive', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            ajaxFunc.apiCall("GET", "tpb/receiveFormEdit/"+button.data('id'), null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Receive');?>");
                  if(form_data.content.success) {
                     //modal.find('.modal-dialog').addClass("modal-lg");
                     modal.find('.modal-body').html(form_data.content.message);                    
                     modal.find('#msgBoxBtnPri').off('click');
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        
                        if(document.getElementById("form-editTpbReceive")!==null){    
                           var data = new FormData(document.getElementById("form-editTpbReceive"));  
                           ajaxFunc.apiCall("POST", "tpb/receive/"+button.data('id'), data, "multipart/form-data", function(return_data){
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
                                       reloadAllTable();    
                                    } 
                                 });    
                              } else {
                                 $("#form-editTpbReceive").find(".form-group").removeClass("has-error");
                                 $("#form-editTpbReceive").find(".form-group").find(".hintHelp").text("");
                                 $("#form-editTpbReceive").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-editTpbReceive").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-editTpbReceive").find("#"+return_data.content.field).focus();
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
        
        /* decision record */
        $('.tpbTable tbody').on('click', '.btnDecision', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            ajaxFunc.apiCall("GET", "tpb/decisionFormEdit/"+button.data('id'), null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('#msgBoxLabel').html("<?=L('Decision');?>");
                  if(form_data.content.success) {
                     //modal.find('.modal-dialog').addClass("modal-lg");
                     modal.find('.modal-body').html(form_data.content.message);                    
                     modal.find('#msgBoxBtnPri').off('click');
                     modal.find('#msgBoxBtnPri').on('click', function (event) { 
                        if(document.getElementById("form-editTpbDecision")!==null){
                           var data = new FormData(document.getElementById("form-editTpbDecision"));  
                           ajaxFunc.apiCall("POST", "tpb/decision/"+button.data('id'), data, "multipart/form-data", function(return_data){
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
                                       reloadAllTable()    
                                    } 
                                 });    
                              } else {
                                 $("#form-editTpbDecision").find(".form-group").removeClass("has-error");
                                 $("#form-editTpbDecision").find(".form-group").find(".hintHelp").text("");
                                 $("#form-editTpbDecision").find("#"+return_data.content.field).closest(".form-group").addClass("has-error");
                                 $("#form-editTpbDecision").find("#"+return_data.content.field+"Help").text(return_data.content.message);
                                 $("#form-editTpbDecision").find("#"+return_data.content.field).focus();
                              }
                           });
                        }                         
                     });   
                     
                     $("#addConditionRow").click(function(e){
                        addConditionRowWithStatus();
                        removeConditionRow();
                        addCalendar();
                     });

                     removeConditionRow();
                     addCalendar();
                    
                  } else {
                     modal.find('.modal-body').html(form_data.content.message);
                     modal.find('#msgBoxBtnPri').on('click', function (event) {  
                        $("#msgBox").modal("hide");   
                     });
                  }  
                               
               }).modal('show')
               /*
               $('#msgBox').on('hidden.bs.modal', function (e) {
                  $(this).find('.modal-dialog').removeClass("modal-lg");
               })
                  */   
            });

        });    
                
        /* view record */
        $('.tpbTable tbody').on('click', '.btnView', function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            var tpbID = button.data('id');
            show_tpb_detail(tpbID);

        });            

        $('.tpbTable').on('click', 'tbody tr td:not(:last-child)', function(e) {
            e.preventDefault();
            show_tpb_detail($(this).parent().attr('id'));                 
        });      


        function show_tpb_detail(tpbID) {
            ajaxFunc.apiCall("GET", "tpb/detail/"+tpbID, null, null,  function (form_data) { 
               $('#msgBox').one('show.bs.modal', function (ev) {                 
                  var modal = $(this);
                  modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                  modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('Record');?>");
                  if(form_data.content.success) {
                     modal.find('.modal-body').html(form_data.content.message); 
                     
                     ajaxFunc.apiCall("GET", "client/detail/"+$("#clientID").val(), null, null,  function (return_data) {                               
                        if(return_data.content.success) {
                           $("#tpbSelectedClentDetail").html(return_data.content.message);
                           downloadDoc();                                                 
                        } else {
                           $("#tpbSelectedClentDetail").html(return_data.content.message);
                        }     
                     });   
                     
                     showConditionCalendar(tpbID);
                     
                     $("#selected_condition_month").change(function(e){
                        selected_condition_month = $(this).val();
                        showConditionCalendar(tpbID);
                     });

                     // conditionTable();
                     var conditionTable = $("#conditionTable").DataTable({
                        pageLength: 10,
                        autoWidth: false,
                        processing: false,
                        serverSide: true,
                        serverMethod: 'post',
                        ajax: '<?=$request->baseUrl();?>/script/conditionList.php?mode=view&tpbID='+tpbID,
                           "columns": [
                           { data: 'column_conditionID' },
                           { data: 'column_conditionNo' },
                           { data: 'column_description' },
                           { data: 'column_deadline' },
                           { data: 'column_status' },
                           { data: 'column_function' },                   
                        ], 
                        initComplete: function () {
                           this.api()
                           .columns([0,1,2,3,4])
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

                     $('.conditionTable tbody').on('click', '.btnView', function (e) {
                           e.preventDefault();
                           var button = $(e.currentTarget);
                           ajaxFunc.apiCall("GET", "condition/detail/"+tpbID, null, null,  function (form_data) { 
                              $('#msgBox2').one('show.bs.modal', function (ev) {                 
                                 var modal = $(this);
                                 //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                 modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('tpb.conditionDetail');?> <?=L('Record');?>");
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
                              $('#msgBox').on('hidden.bs.modal', function (e) {
                                 $(this).find('.modal-dialog').removeClass("modal-xl");
                              })
                              */               
                           });

                     });


                     var eotTable = $("#eotTable").DataTable({
                        pageLength: 10,
                        autoWidth: false,
                        processing: false,
                        serverSide: true,
                        serverMethod: 'post',
                        ajax: '<?=$request->baseUrl();?>/script/eotList.php?mode=view&tpbID='+tpbID,
                           "columns": [
                           { data: 'column_eotID' },
                           { data: 'column_extendMonth' },
                           { data: 'column_reason' },
                           { data: 'column_submissionDate' },
                           { data: 'column_submissionMode' },
                           { data: 'column_status' },
                           { data: 'column_function' },                   
                        ], 
                        initComplete: function () {
                           this.api()
                           .columns([0,1,2,3,4,5])
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

                     $('.eotTable tbody').on('click', '.btnView', function (e) {
                           e.preventDefault();
                           var button = $(e.currentTarget);
                           ajaxFunc.apiCall("GET", "eot/detail/"+tpbID, null, null,  function (form_data) { 
                              $('#msgBox2').one('show.bs.modal', function (ev) {                 
                                 var modal = $(this);
                                 //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                 modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('tpb.EOTDetail');?> <?=L('Record');?>");
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
                              $('#msgBox').on('hidden.bs.modal', function (e) {
                                 $(this).find('.modal-dialog').removeClass("modal-xl");
                              })
                              */               
                           });

                     });  
                    
                     /* stt table */
                     var sttTable = $("#sttTable").DataTable({
                           pageLength: 10,
                           processing: false,
                           serverSide: true,
                           serverMethod: 'post',
                           autoWidth: false,
                           ajax: '<?=$request->baseUrl();?>/script/sttList.php?mode=view&tpbID='+tpbID,
                           "columns": [
                              { data: 'column_sttID' },
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
                        },
                     });

                     $('#sttTable tbody').on('click', '.btnView', function (e) {
                        e.preventDefault();
                        var button = $(e.currentTarget);
                        ajaxFunc.apiCall("GET", "stt/detail/"+tpbID, null, null,  function (form_data) { 
                           $('#msgBox2').one('show.bs.modal', function (ev) {                 
                              var modal = $(this);
                              modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                              modal.find('#msgBoxLabel').html("<?=L('View');?> STT <?=L('Record');?>");
                              if(form_data.content.success) {
                                 modal.find('.modal-body').html(form_data.content.message);                      

                                 ajaxFunc.apiCall("GET", "client/detail/"+$("#clientID").val(), null, null,  function (return_data) {                               
                                    if(return_data.content.success) {
                                       $("#sttSelectedClentDetail").html(return_data.content.message);
                                       downloadDoc();                                                 
                                    } else {
                                       $("#sttSelectedClentDetail").html(return_data.content.message);
                                    }     
                                 });                     
                                 modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                    $("#msgBox2").modal("hide");   
                                 });  
                                 
                                    var mailingLogTable = $("#mailingLogTable").DataTable({
                                       pageLength: 10,
                                       processing: false,
                                       serverSide: true,
                                       serverMethod: 'post',
                                       autoWidth: false,
                                       ajax: '<?=$request->baseUrl();?>/script/sttMailingLogList.php?mode=view&sttID='+tpbID,
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
                                          ajaxFunc.apiCall("GET", "stt/mailingLog/detail/"+tpbID, null, null,  function (form_data) { 
                                             $('#msgBox3').one('show.bs.modal', function (ev) {                 
                                                var modal = $(this);
                                                //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                                modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('stt.mailingDetail');?> <?=L('Record');?>");
                                                if(form_data.content.success) {
                                                   modal.find('.modal-body').html(form_data.content.message);                      
                                                   modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                                      $("#msgBox3").modal("hide");   
                                                   });     
                                                } else {
                                                   modal.find('.modal-body').html(form_data.content.message);
                                                   modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                                      $("#msgBox3").modal("hide");   
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
                                    $("#msgBox2").modal("hide");   
                                 });                    
                              }
                              downloadDoc();  
                                          
                           }).modal('show')

                           $('#msgBox2').on('hidden.bs.modal', function (e) {
                              $(this).find('.modal-dialog').removeClass("modal-xl");
                           })               
                        });

                     });   
                     
                     /* stw table */
                     var stwTable = $("#stwTable").DataTable({
                           pageLength: 10,
                           processing: false,
                           serverSide: true,
                           serverMethod: 'post',
                           autoWidth: false,
                           ajax: '<?=$request->baseUrl();?>/script/stwList.php?mode=view&tpbID='+tpbID,
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
                        },
                     });

                     $('#stwTable tbody').on('click', '.btnView', function (e) {
                        e.preventDefault();
                        var button = $(e.currentTarget);
                        ajaxFunc.apiCall("GET", "stw/detail/"+tpbID, null, null,  function (form_data) { 
                           $('#msgBox2').one('show.bs.modal', function (ev) {                 
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
                                    $("#msgBox2").modal("hide");   
                                 });  
                                 
                                    var mailingLogTable = $("#mailingLogTable").DataTable({
                                       pageLength: 10,
                                       processing: false,
                                       serverSide: true,
                                       serverMethod: 'post',
                                       autoWidth: false,
                                       ajax: '<?=$request->baseUrl();?>/script/stwMailingLogList.php?mode=view&stwID='+tpbID,
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
                                          ajaxFunc.apiCall("GET", "stw/mailingLog/detail/"+tpbID, null, null,  function (form_data) { 
                                             $('#msgBox3').one('show.bs.modal', function (ev) {                 
                                                var modal = $(this);
                                                //modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                                                modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('stw.mailingDetail');?> <?=L('Record');?>");
                                                if(form_data.content.success) {
                                                   modal.find('.modal-body').html(form_data.content.message);                      
                                                   modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                                      $("#msgBox3").modal("hide");   
                                                   });     
                                                } else {
                                                   modal.find('.modal-body').html(form_data.content.message);
                                                   modal.find('#msgBoxBtnPri').on('click', function (event) {  
                                                      $("#msgBox3").modal("hide");   
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
                                    $("#msgBox2").modal("hide");   
                                 });                    
                              }
                              downloadDoc();  
                                          
                           }).modal('show')

                           $('#msgBox2').on('hidden.bs.modal', function (e) {
                              $(this).find('.modal-dialog').removeClass("modal-xl");
                           })               
                        });

                     });  

                     modal.find('#msgBoxBtnPri').off('click');
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

        /* add condition row */
         function addConditionRow() {
            var idx = $("#conditionArea").find(".conditionRow").length;
            var content = "";
            content += '<div class="col-md-12 col-lg-12 conditionRow" id="conditionRow_'+idx+'">';
                  content += '<div class="form-group">';                   
                        content += '<div class="input-group">';
                              content += '<input type="hidden" class="form-control" placeholder="Line Status" id="lineStatus_'+idx+'" name="lineStatus[]" value="0" >';
                              content += '<input type="text" class="form-control w-20" placeholder="Number" id="no_'+idx+'" name="number[]" value="" >';
                              content += '<input type="text" class="form-control w-50" placeholder="Description" id="description_'+idx+'" name="description[]" value="" >';
                              content += '<input type="text" class="form-control customDateTime w-20" placeholder="Deadline" id="deadline_'+idx+'" name="deadline[]" value="" >';
                              content += '<button type="button" class="btn btn-sm btn-danger removeConditionRow"><i class="fas fa-trash"></i></button>';
                        content += '</div>';                
                     content += '<small id="condition_'+idx+'Help" class="form-text text-muted hintHelp"></small>';
                  content += '</div>';
            content += '</div>';              
            
            $("#conditionArea").append(content);
            addCalendar();
         }

        /* add condition row */
         function addConditionRowWithStatus() {
       
            var idx = $("#conditionArea").find(".conditionRow").length;
            var content = "";
            content += '<div class="col-md-12 col-lg-12 conditionRow" id="conditionRow_'+idx+'">';
                content += '<div class="form-group">';                   
                        content += '<div class="input-group">';
                            content += '<input type="hidden" class="form-control" placeholder="Line Status" id="lineStatus_'+idx+'" name="lineStatus[]" value="0" >';
                            content += '<input type="text" class="form-control w-15" placeholder="Number" id="no_'+idx+'" name="number[]" value="" >';
                            content += '<input type="text" class="form-control w-50" placeholder="Description" id="description_'+idx+'" name="description[]" value="" >';
                            content += '<input type="text" class="form-control customDateTime w-15" placeholder="Deadline" id="deadline_'+idx+'" name="deadline[]" value="" >';
                            content += '<select class="form-control form-select w-15" id="generalStatus_'+idx+'" name="generalStatus[]">';
                            content += '<?=$statusSelectionStr;?>';
                            content += '</select>';
                            content += '<button type="button" class="btn btn-sm btn-danger removeConditionRow"><i class="fas fa-trash"></i></button>';
                        content += '</div>';                
                    content += '<small id="condition_'+idx+'Help" class="form-text text-muted hintHelp"></small>';
                content += '</div>';
            content += '</div>';              
            
            $("#conditionArea").append(content);
            addCalendar();
         }         

        /* remove condition row */ 
         function removeConditionRow() {
            $(".removeConditionRow").click(function(e){
               $(this).closest(".conditionRow").remove();
            });
         }

         /* tab nav menu to reload all datatables */
         $(".tpbMenu").click(function(e){
            reloadAllTable();
         });

         /* remove tpb document */
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
                     
                     ajaxFunc.apiCall("POST", "tpb/removeDoc/"+btn.attr('data-id'), {"tpbID":button.data('tpb'), "docType":button.data('doc')}, null, function(return_data){
                        
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

         /* remove eot document */
         function removeEOTDoc(){
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
                     
                     ajaxFunc.apiCall("POST", "eot/removeDoc/"+btn.attr('data-id'), {"eotID":button.data('eot'), "docType":button.data('doc')}, null, function(return_data){
                        
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

         var selected_condition_month = "<?=$selected_condition_month;?>";            

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

         function calendarCellClick () {
            $(".btnCalendarEdit").click(function(e){
               e.preventDefault();
               var button = $(e.currentTarget);
               type = button.data('type');
               id = button.data('id');

               ajaxFunc.apiCall("GET", type+"/detail/"+id, null, null,  function (form_data) { 
                  $('#msgBox').one('show.bs.modal', function (ev) {                 
                     var modal = $(this);
                     modal.find('.modal-dialog').addClass("modal-xl"); /* extend xl modal */
                     modal.find('#msgBoxLabel').html("<?=L('View');?> <?=L('Record');?>");
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
         }


   });
</script>