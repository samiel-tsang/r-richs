<?php
include("view/layout/meta.php");
?>
<h1 class="d-flex text-white bg-danger p-3">
    Error/Exception: <?=get_class($ex);?>
</h1>
<main>
<div class="container-fiuld m-3">
<div class="row">
	<div class="col-12 col-md-2 font-weight-bold">Message</div>
	<div class="col-12 col-md-10 text-wrap"><?=$ex->getMessage();?></div>
</div>
<div class="row">
	<div class="col-12 col-md-2 font-weight-bold">Error Code</div>
	<div class="col-12 col-md-10 text-wrap"><?=$ex->getCode();?></div>
</div>
<div class="row">
	<div class="col-12 col-md-2 font-weight-bold">File</div>
	<div class="col-12 col-md-10 text-wrap"><?=$ex->getFile();?></div>
</div>
<div class="row">
	<div class="col-12 col-md-2 font-weight-bold">Line</div>
	<div class="col-12 col-md-10 text-wrap"><?=$ex->getLine();?></div>
</div>
</div>
<h3 class="p-3 bg-info">Back Trace:</h3>
<div class="container-fiuld m-3">
<?php foreach ($ex->getTrace() as $idx => $row): ?>
<div class="card border-info">
	<div class="card-header">[<?=$idx;?>] <?=$row['file']??'';?> (<?=$row['line']??''; ?>)  :  <?=$row['class']??'';?><?=$row['type']??'';?><?=$row['function']??''; ?>()</div>
<?php if (isset($row['args']) && count($row['args'])): ?>
	<div class="card-body">
		<h5 class="card-title">Function Arguments</h5>
<?php foreach ($row['args'] as $idx => $args): ?>
		<div class="card-text text-wrap"><?=$idx;?>: <?php print_r($args); ?></div>
<?php endforeach; ?>
	</div>
<?php endif; ?>
</div>
<?php endforeach; ?>
</div>
</main>
<?php
include("view/layout/js.php");
include("view/layout/endpage.php");
?>