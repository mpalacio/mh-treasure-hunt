<?php
	$data = json_decode(file_get_contents("php://input"));
	$map = $data->map;

	$mouse_default = array();
	$mouse_group = array();
	$mouse_location = array();
	$hunters = array();

	/*Get mouse list*/
	if($map != "none"){
		$input = file_get_contents("map_mouse_list/$map.in");
		$input_arr = preg_split("/\n/ ", $input);

		$hunters = json_decode(file_get_contents("map_mouse_hunters/$map.json"), TRUE);

		$mouse_list = json_decode(file_get_contents("mouse_list.json"), TRUE);

		$mouse_default = file_exists("map_mouse_json/$map.json") ? json_decode(file_get_contents("map_mouse_json/$map.json"), TRUE) : array();

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

			$mouse_default[$mouse_key] = $mouse_list[$mouse_key] + $mouse_default[$mouse_key];

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

		file_put_contents("map_mouse_json/$map.json", json_encode($mouse_default, JSON_PRETTY_PRINT));
		file_put_contents("unrecorded.json", json_encode($unrecorded_mouse, JSON_PRETTY_PRINT));
	}

	/*Set current selected map*/
	file_put_contents("current_map.in", $map);

	/*Response*/
	echo json_encode(array("map_found" => ($map != "none"), "default" => $mouse_default, "group" => $mouse_group, "location" => $mouse_location, "hunters" => $hunters), JSON_PRETTY_PRINT);
?>