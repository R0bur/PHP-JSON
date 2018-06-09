<?php
/*=============================*/
/* Демонстрация разбора JSON.  */
/*-----------------------------*/
/* JSON parsing demonstration. */
/*=============================*/
require 'iajson.php';
$fname = 'demo.json';
$json = file_get_contents ($fname);
if ($json !== FALSE):
	$a = iaJsonDecode ($json);
	echo 'JSON:' . PHP_EOL;
	echo $json . PHP_EOL;
	echo 'ARRAY:' . PHP_EOL;
	print_r ($a);
else:
	echo "ERROR: Can't read JSON from the file $fname." . PHP_EOL;
endif;
?>