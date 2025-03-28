<?php
http_response_code(404);
include("view/layout/meta.php");
?>
<h1 class="d-flex text-white bg-danger p-3">
    404: File Not Found!
</h1>
<main class="text-center">
<div style="color: #944; font-size: 25vw;">404</div>
<div style="color: #446; font-size: 6vw; font-family: 'Bilbo Swash Caps', cursive;">The Requested Url Was Not Found !...</div>
<div style="color: #444;">Sorry! Evidently the document you were looking for has either been moved or no longer exist.</div>
</main>
<?php
include("view/layout/js.php");
include("view/layout/endpage.php");
?>