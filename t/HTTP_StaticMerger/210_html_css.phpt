--TEST--
HTTP_StaticMerger: build tags for CSS list
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

echo $mergerUncompr->getHtml('/merger', array('a.css', 'b.css')) . "\n\n";
echo $mergerUncompr->getHtml('/merger', array('a.css', 'b.css'), true);

?>

--EXPECTF--
<link rel="stylesheet" type="text/css" href="/merger!!%s!!%d!!a.css!!b.css" />

<link rel="stylesheet" type="text/css" href="/a.css?%d" />
<link rel="stylesheet" type="text/css" href="/b.css?%d" />
