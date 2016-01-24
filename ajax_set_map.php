<?php
	$data = json_decode(file_get_contents("php://input"));
	$map = $data->map;
	$mice_list = $data->mice_list;
	file_put_contents("map_mouse_list/$map.in", $mice_list);
?>