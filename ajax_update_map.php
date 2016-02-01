<?php
	include_once dirname(__FILE__) . '/config.php';
	/*Save map*/
	$data = json_decode(file_get_contents("php://input"));
	$map = trim($data->old_map);
	$new_map = trim($data->new_map);
	$mice_list = $data->mice_list;
	$hunters = preg_split("/\n/ ", $data->hunters);
	$hunters = array_values(array_filter($hunters, function($var) {
		return (trim($var) != "");
	}));
	file_put_contents("map_mouse_list/$map.in", $mice_list);
	file_put_contents("map_mouse_hunters/$map.json", json_encode($hunters));
	if($map != $new_map) {
		rename("map_mouse_list/$map.in", "map_mouse_list/$new_map.in");
		rename("map_mouse_hunters/$map.json", "map_mouse_hunters/$new_map.json");
		rename("map_mouse_json/$map.json", "map_mouse_json/$new_map.json");
	}
?>
