--TEST--
HTTP_StaticMerger: build tags for CSS list (different BASE)
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$mergerUncompr = new HTTP_StaticMerger_Stub('some-secret-code', $_SERVER['DOCUMENT_ROOT'], '/dir', true);

// Relative path to "merger" must stay relative.
echo $mergerUncompr->getHtml('merger', array('a.css', 'b.css')) . "\n\n";

echo "As /dir/a.css does not exist, its timestamp must be NULL.\n";
echo $mergerUncompr->getHtml('merger', array('a.css', 'b.css'), true);
?>

--EXPECTF--
<link rel="stylesheet" type="text/css" href="merger!!%s!!%d!!a.css!!b.css" />

As /dir/a.css does not exist, its timestamp must be NULL.
<link rel="stylesheet" type="text/css" href="/dir/a.css?" />
<link rel="stylesheet" type="text/css" href="/dir/b.css?" />
