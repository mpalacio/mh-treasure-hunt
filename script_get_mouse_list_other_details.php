<?php
	$NL = PHP_SAPI == "cli" ? "\n" : "<br>";

	$groups = json_decode(file_get_contents("mice_stat.json"));
	$mouse_list = json_decode(file_get_contents("mouse_list.json"));
	$mouse_locations = json_decode(file_get_contents("mouse_locations.json"));
	foreach ($groups as $group_tab) {
		foreach ($group_tab->page->tabs->{'2'}->subtabs[0]->categories as $group) {
			if (!$group->initialized)
				continue;
			foreach ($group->subgroups[0]->mice as $mouse) {
				$m_name = substr($mouse->name, -6) == " Mouse" ? substr_replace($mouse->name, "", -6) : $mouse->name;
				$m_name = substr($m_name, 0, 4) == "The " ? substr($m_name, 4) : $m_name;
				$id = strtolower(str_replace(" ", "_", $m_name));
				if (isset($mouse_list->$id)) {
					list($weakness_key) = count((array) $mouse->weaknesses) ? array_keys((array) $mouse->weaknesses) : array("0");

					$mouse_list->$id->description = $mouse->description;
					$mouse_list->$id->thumb = $mouse->thumb;
					$mouse_list->$id->image = $mouse->medium;
					$mouse_list->$id->weaknesses = ($weakness_key != "0" ? $mouse->weaknesses->{$weakness_key} : array());
				} else {
					$mouse_json = array(
						"id"          => $id,
						"name"        => $m_name,
						"description" => $mouse->description,
						"location"    => $mouse_locations->$id,
						"points"      => trim($mouse->points),
						"gold"        => trim($mouse->gold),
						"cheese"      => "See wiki page",
						"group"       => $group->name,
						"sub_group"   => "",
						"image"       => $mouse->medium,
						"thumb"       => $mouse->thumb
					);
					$mouse_list->$id = (object) $mouse_json;
				}
			}
			break;
		}
	}
	file_put_contents("mouse_list.json", json_encode($mouse_list, JSON_PRETTY_PRINT));
?>
