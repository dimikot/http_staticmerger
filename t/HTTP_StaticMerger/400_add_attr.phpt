--TEST--
HTTP_StaticMerger: build tags for JS list
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

echo $mergerUncompr->getHtml('/merger', array('dir/a.js', '/dir/b.js'), false, 'language="javascript"') . "\n\n";
echo $mergerUncompr->getHtml('/merger', array('dir/a.css', '/dir/b.css'), false, 'media="print"') . "\n\n";

?>

--EXPECTF--
<script type="text/javascript" src="/merger!!%s!!%d!!dir/a.js!!/dir/b.js" language="javascript">&#160;</script>

<link rel="stylesheet" type="text/css" href="/merger!!%s!!%d!!dir/a.css!!/dir/b.css" media="print" />
