--TEST--
HTTP_StaticMerger: build tags for CSS empty list
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

echo $mergerUncompr->getHtml('/merger', array()) . "\n\n";
echo $mergerUncompr->getHtml('/merger', array(), true) . "\n\n";

?>

--EXPECTF--
<script type="text/javascript" src="/merger!!%s!!%d">&#160;</script>

