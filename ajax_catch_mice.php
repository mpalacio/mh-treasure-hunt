<?php
	$data = json_decode(file_get_contents("php://input"));
	$map = $data->map;
	$mouse_ids = $data->mouse_ids;
	$hunter = $data->hunter;

	$mouse_default = json_decode(file_get_contents("map_mouse_json/$map.json"));
	foreach($mouse_default as $mouse_id => $mouse)
		if(in_array($mouse_id, $mouse_ids)) {
			$mouse->caught = true;
			$mouse->hunter = $hunter;
		}

	file_put_contents("map_mouse_json/$map.json", json_encode($mouse_default, JSON_PRETTY_PRINT));
?>