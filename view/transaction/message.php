<?php
$cssPath = array("css/signin.css");
$bodyClass = "bg-light";
include("view/layout/meta.php");

?>
<form class="form-signin shadow-lg">
  <div class="py-5 text-center">    
    <h2><?=$header; ?></h2>
  </div>
  <div class="card text-center" style="width: 100%;">
    <div class="card-body">      
      <p><?=$message; ?></p>
    </div>
  </div>

  

  <div class="row g-2" id="btnSubmitArea">   
    <div class="col-12 rowBtn"><a class="w-100 btn btn-lg btn-primary" id="btnAction" type="button" href="<?=$returnUrl;?>"><?= L("Back"); ?></a></div>
  </div>

  <footer class="footer text-muted text-center text-small" style="margin-top:20px;">
    <p class="">&copy; <?=date("Y");?> Supertainment</p>
    <span class="text-info"><i class="fas fa-language fa-lg"></i><?php foreach (Pages\Language::getAvailableLang() as $lang) : ?>
        <a href="<?php Utility\WebSystem::path("lang/" . $lang['langCode']); ?>" class="p-1 text-info"><?= $lang['langName']; ?></a>
      <?php endforeach ?></span>
  </footer>

</form>

<?php
include("view/layout/js.php");
?>
<script>
  $(function() {
    window.history.replaceState( null , '', '<?=$returnUrl;?>' );
  });
</script>
<?php
include("view/layout/endpage.php");
?>