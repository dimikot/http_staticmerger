<?php
require_once "../../lib/HTTP/StaticMerger.php";
$merger = new HTTP_StaticMerger("a-secret-choose-your-own", dirname(__FILE__));
$merger->main('windows-1251');
