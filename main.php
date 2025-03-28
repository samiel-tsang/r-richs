<?php
include("view/layout/meta.php");
include("view/layout/head.php");
?>
<main class="content">
<div class="funcBar d-flex text-white">
    <span class="funcTitle mr-auto"><?=L('Dashboard');?></span>
</div>
<style>
.dashboardicon {
   border-color: #6c757d;
   width: 150px;
   height: 150px;
}
</style>
<div class="container">
   <div class="row flex-sm-row flex-column">
      <div class="col">
         <div class="card text-center">
            <div class="card-header"><?=L('team.donateAmount');?> ($<?=$donateAmt;?>)</div>
            <div class="card-body">
            
               <table class="table">
                     <tr><td></td>
                     <?php
                        foreach (Controller\category::find_all() as $categoryObj) {
                           echo "<td>".$categoryObj->name."</td>";
                        }
                     ?>                    
                     </tr>

                     <?php
                     foreach (Controller\period::find_all() as $periodObj) {
                        echo "<tr>";
                           echo "<td>".$periodObj->name."</td>";
                           foreach (Controller\category::find_all() as $categoryObj) {
                              echo "<td>".Controller\team::getDonaterAmountByPeriodAndCategory($periodObj->id, $categoryObj->id)."</td>";
                           }
                        echo "</tr>";
                     }                     
                     ?>            
               </table>
            
            </div>

         </div>
      </div>
      <div class="col">
         <div class="card text-center">
            <div class="card-header"><?=L('team.totalCount');?> (<?=$teamTotal;?>)</div>
            <div class="card-body">
               <table class="table">
                     <tr><td></td>
                     <?php
                        foreach (Controller\category::find_all() as $categoryObj) {
                           echo "<td>".$categoryObj->name."</td>";
                        }
                     ?>                    
                     </tr>

                     <?php
                     foreach (Controller\period::find_all() as $periodObj) {
                        echo "<tr>";
                           echo "<td>".$periodObj->name."</td>";
                           foreach (Controller\category::find_all() as $categoryObj) {
                              echo "<td>".Controller\team::getTeamCountByPeriodAndCategory($periodObj->id, $categoryObj->id)."</td>";
                           }
                        echo "</tr>";
                     }                     
                     ?>            
               </table>
            </div>
         </div>
      </div>
      <div class="col">
         <div class="card text-center">
            <div class="card-header"><?=L('team.totalHeadCount');?> (<?=$teamHeadTotal;?>)</div>
            <div class="card-body">
               <table class="table">
                     <tr><td></td>
                     <?php
                        foreach (Controller\category::find_all() as $categoryObj) {
                           echo "<td>".$categoryObj->name."</td>";
                        }
                     ?>                    
                     </tr>

                     <?php
                     foreach (Controller\period::find_all() as $periodObj) {
                        echo "<tr>";
                           echo "<td>".$periodObj->name."</td>";
                           foreach (Controller\category::find_all() as $categoryObj) {
                              echo "<td>".Controller\team::getParticipantCountByPeriodAndCategory($periodObj->id, $categoryObj->id)."</td>";
                           }
                        echo "</tr>";
                     }                     
                     ?>            
               </table>
            
            </div>
         </div>
      </div>
   </div>
   <div class="row my-3">
<?php
   $stmNavItems = Database\Sql::select('navItems')->where(['status', '=', 1])
	->where(['itemType', '=', 0])->order('itemOrder')->prepare();
   $stmNavItems->execute();
   foreach ($stmNavItems as $navItems) {

?>
      
      <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-3 text-center"><a href="<?=$this->pageLink($navItems['pageName'], ["pg"=>1]);?>" class="btn dashboardicon shadow-lg" style="position: relative;">
         <div class="row justify-content-center"><i class="fas <?=$navItems['itemIcon'];?> fa-5x"></i></div> 
         <?php if ($count>0) { ?>
         <div style='position: absolute;right: 5px;top: 5px;'><span class="badge badge-pill badge-danger" style="background: #DC3545;
    border-radius: 50rem !important;"><?=$count;?></span></div>        
         <?php } ?>
         <div class="row justify-content-center p-1"><?=L($navItems['itemName']);?></div>
      </a></div>
<?php
   }
?>

   </div>
</div>
</main>
<?php
include("view/layout/foot.php");
include("view/layout/js.php");

include("view/layout/endpage.php");
?>