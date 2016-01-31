<?php
	include_once dirname(__FILE__) . '/config.php';
	/*Get all maps*/
	$maps = glob('map_mouse_list/*.*');

	usort($maps, function($a, $b) {
		return filemtime($a) > filemtime($b);
	});

	array_walk($maps, function(&$item, $key) {
		$item = str_replace(".in", "", str_replace("map_mouse_list/", "", $item));
	});

	/*Get current map*/
	$current_map = file_get_contents("current_map.in") != false ? file_get_contents("current_map.in") : 'none';

	/*Response*/
	echo json_encode(array('maps' => $maps, 'current_map' => $current_map), JSON_PRETTY_PRINT);
?>