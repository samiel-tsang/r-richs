<footer class="footer text-muted text-center text-small">
  <p class="">&copy; <?=date("Y");?> Supertainment</p>
</footer>

<!-- Modal -->
<div class="modal fade" id="langSwitcher" tabindex="-1" role="dialog" aria-labelledby="langSwitcherLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="langSwitcherLabel"><?=L("Languages");?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body"><ul>
<?php foreach (Pages\Language::getAvailableLang() as $lang): ?>
		<li style='margin-top: 10px;'><a href="<?php Utility\WebSystem::path("lang/".$lang['langCode']);?>" class="btn btn-info"><?=$lang['langName'];?></a></li>
<?php endforeach ?>
      </ul></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=L("Close");?></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="msgBox" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="msgBoxLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="msgBoxLabel"><?=L("Title");?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" id="msgBoxBtnSec" class="btn btn-secondary" data-bs-dismiss="modal"><?=L("Close");?></button>
        <button type="button" id="msgBoxBtnPri" class="btn btn-primary"><?=L("OK");?></button>
      </div>
    </div>
  </div>
</div>

<div id="promptMsg"></div>