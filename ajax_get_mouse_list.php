<?php
	$map = "map_1";
	$input = file_get_contents("map_mouse_list/$map.in");
	$input_arr = preg_split("/\n/ ", $input);

	$mouse_list = json_decode(file_get_contents("mouse_list.json"), TRUE);

	$mouse_default = file_exists("map_mouse_json/$map.json") ? json_decode(file_get_contents("map_mouse_json/$map.json"), TRUE) : array();
	$mouse_group = array();
	$mouse_location = array();

	$unrecorded_mouse = file_exists("unrecorded.json") ? json_decode(file_get_contents("unrecorded.json"), TRUE) : array();

	foreach($input_arr as $key => $mouse) {
		$mouse_key = strtolower(str_replace(" ", "_", trim($mouse)));

		if(!isset($mouse_default[$mouse_key])) {
			if(!isset($mouse_list[$mouse_key])) {
				$unrecorded_mouse[] = $mouse;
				continue;
			}
			$mouse_default[$mouse_key] = $mouse_list[$mouse_key];
			$mouse_default[$mouse_key]['caught'] = false;
		}

		foreach($mouse_default[$mouse_key]['location']['areas'] as $region => $locations) {
			if(!isset($mouse_location[$region]))
				$mouse_location[$region] = array();
			foreach($locations as $location) {
				if(!isset($mouse_location[$region][$location]))
					$mouse_location[$region][$location] = array();
				$mouse_location[$region][$location][] = $mouse_key;
			}
		}

		$group = $mouse_default[$mouse_key]['group'].($mouse_default[$mouse_key]['sub_group'] != "" ? ": {$mouse_default[$mouse_key]['sub_group']}" : "");
		if(!isset($mouse_group[$group]))
			$mouse_group[$group] = array();
		$mouse_group[$group][] = $mouse_key;
	}
	ksort($mouse_default);
	ksort($mouse_group);
	ksort($mouse_location);
	foreach ($mouse_location as $region => $locations) {
		ksort($mouse_location[$region]);
		foreach ($locations as $location => $values) {
			sort($mouse_location[$region][$location]);
		}
	}

	$myfile = fopen("map_mouse_json/$map.json", "w");
	fwrite($myfile, json_encode($mouse_default));
	fclose($myfile);
	file_put_contents("unrecorded.json", json_encode($unrecorded_mouse));
	echo json_encode(array("default" => $mouse_default, "group" => $mouse_group, "location" => $mouse_location));
?>