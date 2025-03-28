<html>

<body>
<?php
var_dump(preg_match('/(\W+|\s+)/', 'stanley@@@#!@#$#%$ho'));
?>
<textarea id="txtContent"></textarea>
<button id="btnConvert">Convert</button>
<textarea id="txtConverted"></textarea>
</body>

<script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js"></script>
<script>
$('#btnConvert').click(function (e) {
   $('#txtConverted').val(encodeURIComponent($('#txtContent').val()));
});
</script>

</html>