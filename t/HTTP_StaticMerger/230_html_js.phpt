--TEST--
HTTP_StaticMerger: build tags for JS list
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

echo $mergerUncompr->getHtml('/merger', array('dir/a.js', '/dir/b.js')) . "\n\n";
echo $mergerUncompr->getHtml('/merger', array('dir/a.js', '/dir/b.js'), true) . "\n\n";

?>

--EXPECTF--
<script type="text/javascript" src="/merger!!%s!!%d!!dir/a.js!!/dir/b.js">&#160;</script>

<script type="text/javascript" src="/dir/a.js?%d">&#160;</script>
<script type="text/javascript" src="/dir/b.js?%d">&#160;</script>
