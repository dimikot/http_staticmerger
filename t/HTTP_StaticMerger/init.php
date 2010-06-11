<?php
header("Content-type: text/plain");
chdir(dirname(__FILE__));
include_once "../../lib/config.php";
include_once "HTTP/StaticMerger.php";

class HTTP_StaticMerger_Stub extends HTTP_StaticMerger
{
    protected function _header($s)
    {
    	$s = preg_replace('/^((Last-Modified|Etag):\s*).*/is', '$1***', $s);
    	$s = preg_replace('/^(Expires|Cache-Control):.*/is', '', $s);
        echo "$s\n";
    }
}

$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__) . '/fixture';
$merger = new HTTP_StaticMerger_Stub('some-secret-code');
$mergerUncompr = new HTTP_StaticMerger_Stub('some-secret-code', null, '/', true);


function getUri($list)
{
	global $merger;
	$html = $merger->getHtml('/merger/', $list);
	if (!preg_match('/(?:href|src)="(.*?)"/s', $html, $m)) return null;
	return $m[1];
}