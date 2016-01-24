<?php
	$data = json_decode(file_get_contents("php://input"));
	$map = $data->map;
	$json = file_get_contents("map_mouse_json/$map.json");
	$list = file_get_contents("map_mouse_list/$map.in");
	if(!unlink("map_mouse_json/$map.json") | !unlink("map_mouse_list/$map.in")) {
		echo ("false");
		file_put_contents("map_mouse_json/$map.json", $json);
		file_put_contents("map_mouse_list/$map.in", $list);
  }
  else {
  	echo ("true");
  }
?>
