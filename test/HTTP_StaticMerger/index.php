<?php
require_once "../../lib/HTTP/StaticMerger.php";
$merger = new HTTP_StaticMerger("a-secret-choose-your-own", dirname(__FILE__));
$script = dirname($_SERVER['SCRIPT_NAME']) . '/merge.php/';
?>
<html>
<body>

<h2>Automatic merged URL generation</h2>
<xmp><?
echo $merger->getHtml($script, array("static/a.js", "static/b.js"), false, 'a="b"');
?>
</xmp>	

<h2>Automatic merged URL generation (debug mode)</h2>
<xmp><?
echo $merger->getHtml($script, array("static/a.js", "static/b.js"), true);
?>
</xmp>

</body>
</html>

<hr>
<?show_source(__FILE__)?>