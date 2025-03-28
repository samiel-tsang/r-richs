<?php
$user = unserialize($_SESSION['user']);
				  
$currentLocation = $request->url;

$lastSlashPos = strrpos($currentLocation, '/');
$questionMarkPos = strpos($currentLocation, '?');

// Check if both positions are found and valid
if ($lastSlashPos !== false && $questionMarkPos !== false && $lastSlashPos < $questionMarkPos) {
	// Extract the substring between the last "/" and "?"
	$currentPage = substr($currentLocation, $lastSlashPos + 1, $questionMarkPos - $lastSlashPos - 1);
} else {
	$currentPage = "";
}				 
				 
?>
	<div class="wrapper">
		<!-- Sidebar -->
		<div class="sidebar" data-background-color="dark">
			<div class="sidebar-logo">
				<!-- Logo Header -->
				<div class="logo-header" data-background-color="white" style="color:black">
               <!--<?=L('title');?>-->
					<a href="<?=$this->pageLink('page.dashboard');?>" class="logo">
						<img src="<?php Utility\WebSystem::path("images/logo/logo2.jpg");?>" alt="<?=L('title');?>" class="navbar-brand" height="80%">
					</a>
					<div class="nav-toggle menuToggle">
						<button class="btn btn-toggle toggle-sidebar">
							<i class="gg-menu-right"></i>
						</button>
						<button class="btn btn-toggle sidenav-toggler">
							<i class="gg-menu-left"></i>
						</button>
					</div>
					<button class="topbar-toggler more">
						<i class="gg-more-vertical-alt"></i>
					</button>
				</div>
				<!-- End Logo Header -->	
			</div>	<?php $permissionList = Controller\role::findPermission($user->roleID); $arrPermission = []; foreach($permissionList as $permission) { $arrPermission[]=$permission['navItemID']; } ?>
			
			<div class="sidebar-wrapper scrollbar scrollbar-inner">
				<div class="sidebar-content">
					<ul class="nav nav-secondary">
				  		<li class="nav-item">
							<a href="<?=$this->pageLink('page.dashboard');?>">
								<i class="fas fa-home"></i>
								<p><?=L('Dashboard');?></p>								
							</a>
						</li>
                  <?php
                  $stmNavItemsMain = Database\Sql::select('navItems')->where(['status', '=', 1])->where(['itemType', '=', 1])->order('itemOrder')->prepare();
                  $stmNavItemsMain->execute();
                  foreach ($stmNavItemsMain as $navItemsMain) {

					$stmNavItemsSub = Database\Sql::select('navItems')->where(['status', '=', 1])->where(['itemType', '=', 0])->where(['subMenuOf', '=', $navItemsMain['id']])->order('itemOrder')->prepare();			
					$stmNavItemsSub->execute();
					if($stmNavItemsSub->rowCount()>0) {				

                  ?>
				  	<!--sub menu-->
				  		<li class="nav-item submenu <?=in_array($currentPage,json_decode($navItemsMain['pageKey']))?"active":"";?>">
							<a data-bs-toggle="collapse" href="#base_<?=$navItemsMain['id'];?>">
								<i class="fas <?=$navItemsMain['itemIcon'];?>"></i> 
								<p><?=L($navItemsMain['itemName']);?></p>
								<span class="caret"></span>
							</a>
							<div class="collapse <?=in_array($currentPage,json_decode($navItemsMain['pageKey']))?"show":"";?>" id="base_<?=$navItemsMain['id'];?>">
								<ul class="nav nav-collapse">
									<?php 
										foreach ($stmNavItemsSub as $navItemsSub) {
									?>
									<?php if(in_array($navItemsSub['id'], $arrPermission)) { ?>
									<li class="<?=$currentPage==$navItemsSub['pageKey']?"active":"";?>">
										<a href="<?=$this->pageLink($navItemsSub['pageName'], ['pg'=>1]);?>">
											<span class="sub-item"><?=L($navItemsSub['itemName']);?></span>
										</a>
									</li>
									<?php } ?>	
									<?php } ?>									
								</ul>
							</div>
						</li>	
					<?php } else { ?>			  
					 <!--single main menu--> 
					 <?php if(in_array($navItemsMain['id'], $arrPermission)) { ?>
                     <li class="nav-item <?=$currentPage==$navItemsMain['pageKey']?"active":"";?>">
                        <a href="<?=$this->pageLink($navItemsMain['pageName'], ['pg'=>1]);?>">
                           <i class="fas <?=$navItemsMain['itemIcon'];?>"></i>                           
                           <p><?=L($navItemsMain['itemName']);?></p>
                           <!--<span class="badge badge-success">0</span>-->
                        </a>
                     </li>                     
					 <?php } ?>
                  <?php                     
                  	}
				  }
                  ?>
                     
				  

































                  <!--
						<li class="nav-item">
							<a href="../widgets.html">
								<i class="fas fa-desktop"></i>
								<p>Widgets</p>
								<span class="badge badge-success">4</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="../../../documentation/index.html">
							  <i class="fas fa-file"></i>
							  <p>Documentation</p>
							  <span class="badge badge-secondary">1</span>
							</a>
						</li>
						<li class="nav-item">
							<a data-bs-toggle="collapse" href="#submenu">
								<i class="fas fa-bars"></i>
								<p>Menu Levels</p>
								<span class="caret"></span>
							</a>
							<div class="collapse" id="submenu">
								<ul class="nav nav-collapse">
									<li>
										<a data-bs-toggle="collapse" href="#subnav1">
											<span class="sub-item">Level 1</span>
											<span class="caret"></span>
										</a>
										<div class="collapse" id="subnav1">
											<ul class="nav nav-collapse subnav">
												<li>
													<a href="#">
														<span class="sub-item">Level 2</span>
													</a>
												</li>
												<li>
													<a href="#">
														<span class="sub-item">Level 2</span>
													</a>
												</li>
											</ul>
										</div>
									</li>
									<li>
										<a data-bs-toggle="collapse" href="#subnav2">
											<span class="sub-item">Level 1</span>
											<span class="caret"></span>
										</a>
										<div class="collapse" id="subnav2">
											<ul class="nav nav-collapse subnav">
												<li>
													<a href="#">
														<span class="sub-item">Level 2</span>
													</a>
												</li>
											</ul>
										</div>
									</li>
									<li>
										<a href="#">
											<span class="sub-item">Level 1</span>
										</a>
									</li>
								</ul>
							</div>
						</li>-->
					</ul>
				</div>
			</div>
		</div>
		<!-- End Sidebar -->