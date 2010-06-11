--TEST--
HTTP_StaticMerger: URI compression
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$uri = getUri(array_fill(0, 50, 'slkufwqkcbwofiuabriwfsjbfiwufbskribasjdnksjfgsdjbcmshbdkwjnf.css'));
echo $uri . "\n";
echo strlen($uri) < 300? "length is ok\n" : "bad: $uri\n";
?>

--EXPECTF--
%s!!!!%s/%s.css
length is ok
