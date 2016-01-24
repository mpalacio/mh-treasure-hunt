<?php
	$maps = scandir('map_mouse_list');
	array_shift($maps);
	array_shift($maps);
	array_walk($maps, function(&$item, $key) {
		$item = str_replace(".in", "", $item);
	});
	echo json_encode($maps, JSON_PRETTY_PRINT);
?>