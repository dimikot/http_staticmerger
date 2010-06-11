--TEST--
HTTP_StaticMerger: Do not merge absolute URLs
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

echo preg_replace(
	'/(?<=!!).*?(?=!!)/s', '***', 
	$mergerUncompr->getHtml('/merger', array('a.css', 'http://www.ru/x.css', 'b.css')),
	2
) . "\n\n";

?>

--EXPECT--
<link rel="stylesheet" type="text/css" href="/merger!!***!!***!!a.css!!b.css" />
<link rel="stylesheet" type="text/css" href="http://www.ru/x.css" />
