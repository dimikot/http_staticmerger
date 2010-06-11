--TEST--
HTTP_StaticMerger: signature includes slashes positions too
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$merger = new HTTP_StaticMerger('some-secret-code');
$html = $merger->getHtml('/merger/', array('slkufwqkcbwofiuabriwfsjbfiwufbskribasjdnksjfgsdjbcmshbdkwjnf.css'));
if (!preg_match('/(?:href|src)="(.*?)"/s', $html, $m)) die("bug!");
$url = $m[1];

$_SERVER['REQUEST_URI'] = parse_url($url, PHP_URL_PATH);
$merger->main();

$url = preg_replace('{/([^/]+$)}s', '//$1', $url);

$_SERVER['REQUEST_URI'] = parse_url($url, PHP_URL_PATH);
@$merger->main();
?>

--EXPECT--
/**** slkufwqkcbwofiuabriwfsjbfiwufbskribasjdnksjfgsdjbcmshbdkwjnf.css ****/
.content_of_long_file {}


Malformed merged URL!
