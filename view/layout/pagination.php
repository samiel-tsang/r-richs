<?php
   $pageRoute = Routing\Route::getRouteByName(Routing\Route::$currRouteName);
?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <li class="page-item<?=(!$isFirst)?"":" disabled";?>">
            <a class="page-link" id="link-first" href="<?=(!$isFirst)?$this->pageNameLink($pageRoute, 1):"#";?>" tabindex="-1"><?=L("page.first");?></a>
        </li>
        <li class="page-item<?=($havePrev)?"":" disabled";?>">
            <a class="page-link" id="link-prev" href="<?=($havePrev)?$this->pageNameLink($pageRoute, $this->pageNo-1):"#";?>" tabindex="-1"><?=L("page.previous");?></a>
        </li>
        <?php
    for ($i = $start; $i <= $end; $i++) {
        $isCurrPage = ($i == $this->pageNo);
        echo '<li class="page-item'.(($isCurrPage)?" active":"").'"><'.(($isCurrPage)?"span":"a").' class="page-link" href="'.(($isCurrPage)?"#":$this->pageNameLink($pageRoute, $i)).'">'.$i.'</'.(($isCurrPage)?"span":"a").'></li>';
    }
        ?>
        <li class="page-item<?=($haveNext)?"":" disabled";?>">
            <a class="page-link" id="link-next" href="<?=($haveNext)?$this->pageNameLink($pageRoute, $this->pageNo+1):"#";?>"><?=L("page.next");?></a>
        </li>
        <li class="page-item<?=(!$isLast)?"":" disabled";?>">
            <a class="page-link" id="link-last" href="<?=(!$isLast)?$this->pageNameLink($pageRoute, $this->pageTotal):"#";?>"><?=L("page.last");?></a>
        </li>
    </ul>
</nav>
