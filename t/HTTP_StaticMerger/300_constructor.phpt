--TEST--
HTTP_StaticMerger: since 2.00 constructor signature is the first parameter 
--FILE--
<?php
require dirname(__FILE__) . '/init.php';

try {
	$merger = new HTTP_StaticMerger('/a/b');
} catch (Exception $e) {
	echo $e->getMessage() . "\n";
}

try {
	$merger = new HTTP_StaticMerger('\a\b');
} catch (Exception $e) {
	echo $e->getMessage() . "\n";
}
?>

--EXPECT--
HTTP_StaticMerger::__construct(): till 2.0 the first parameter is a digital signature secret. It should be larger than 4 characters and contan no "/" and "\" within.
HTTP_StaticMerger::__construct(): till 2.0 the first parameter is a digital signature secret. It should be larger than 4 characters and contan no "/" and "\" within.

