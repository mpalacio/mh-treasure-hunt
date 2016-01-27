<?php
	$data = json_decode(file_get_contents("php://input"));
	$map = $data->map;
	$json = file_get_contents("map_mouse_json/$map.json");
	$list = file_get_contents("map_mouse_list/$map.in");
	$hunters = file_get_contents("map_mouse_hunters/$map.json");
	if(unlink("map_mouse_json/$map.json") == false | unlink("map_mouse_list/$map.in") == false | unlink("map_mouse_hunters/$map.json") == false) {
		echo (json_encode(array("success" => false)));
		file_put_contents("map_mouse_json/$map.json", $json);
		file_put_contents("map_mouse_list/$map.in", $list);
		file_put_contents("map_mouse_hunters/$map.json", $hunters);
  }
  else {
  	echo (json_encode(array("success" => true)));
  }
?>