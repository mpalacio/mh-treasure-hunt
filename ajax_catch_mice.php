<?php
	$map = "map_1";

  $data = json_decode(file_get_contents("php://input"));
  $mouse_ids = $data->mouse_ids;

	$mouse_default = json_decode(file_get_contents("map_mouse_json/$map.json"));
	foreach($mouse_default as $mouse_id => $mouse)
		if(in_array($mouse_id, $mouse_ids))
			$mouse->caught = true;

	file_put_contents("map_mouse_json/$map.json", json_encode($mouse_default));
?>