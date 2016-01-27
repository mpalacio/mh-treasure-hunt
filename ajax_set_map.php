<?php
	/*Save map*/
	$data = json_decode(file_get_contents("php://input"));
	$map = $data->map;
	$mice_list = $data->mice_list;
	$hunters = preg_split("/\n/ ", $data->hunters);
	$hunters = array_values(array_filter($hunters, function($var) {
		return (trim($var) != "");
	}));
	file_put_contents("map_mouse_list/$map.in", $mice_list);
	file_put_contents("map_mouse_hunters/$map.json", json_encode($hunters));
?>