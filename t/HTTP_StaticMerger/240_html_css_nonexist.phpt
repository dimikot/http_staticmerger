--TEST--
HTTP_StaticMerger: build tags for CSS list of non-existed files
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

echo $mergerUncompr->getHtml('/merger', array('xxxx.js', 'yyyy.js')) . "\n\n";
echo $mergerUncompr->getHtml('/merger', array('xxxx.js', 'yyyy.js'), true) . "\n\n";

?>

--EXPECTF--
<script type="text/javascript" src="/merger!!%s!!%d!!xxxx.js!!yyyy.js">&#160;</script>

<script type="text/javascript" src="/xxxx.js?">&#160;</script>
<script type="text/javascript" src="/yyyy.js?">&#160;</script>
