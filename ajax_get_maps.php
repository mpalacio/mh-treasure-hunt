<?php
	/*Get all maps*/
	$maps = scandir('map_mouse_list');
	array_shift($maps);
	array_shift($maps);
	array_walk($maps, function(&$item, $key) {
		$item = str_replace(".in", "", $item);
	});

	/*Get current map*/
	$current_map = file_get_contents("current_map.in") != false ? file_get_contents("current_map.in") : 'none';

	/*Response*/
	echo json_encode(array('maps' => $maps, 'current_map' => $current_map), JSON_PRETTY_PRINT);
?>