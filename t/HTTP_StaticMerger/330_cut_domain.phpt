--TEST--
HTTP_StaticMerger: domain name is not included in signature
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

$merger = new HTTP_StaticMerger('some-secret-code');
$html = $merger->getHtml('http://example.com/', array('a.css', 'b.css'));
if (!preg_match('/(?:href|src)="(.*?)"/s', $html, $m)) die("bug!");
$url = $m[1];

$_SERVER['REQUEST_URI'] = parse_url($url, PHP_URL_PATH);
$merger->main();
?>

--EXPECT--

/**** a.css ****/
content_of_a {}



/**** b.css ****/
content_of_b {}
