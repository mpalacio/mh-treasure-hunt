<?php
	$data = json_decode(file_get_contents("php://input"));
	$map = $data->map;
	file_put_contents("current_map.in", $map);
?>