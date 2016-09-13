<?php
	$NL = PHP_SAPI == "cli" ? "\n" : "<br>";

	$groups = json_decode(file_get_contents("mice_stat.json"));
	$mouse_list = json_decode(file_get_contents("mouse_list.json"));
	foreach ($groups as $group_tab) {
		foreach ($group_tab->page->tabs[2]->subtabs[0]->categories as $group) {
			if(!$group->initialized)
				continue;
			foreach ($group->subgroups[0]->mice as $mouse) {
				$m_name = substr($mouse->name, -6) == " Mouse" ? substr_replace($mouse->name, "", -6) : $mouse->name;
				$id = strtolower(str_replace(" ", "_", $m_name));
				$mouse_json = $mouse_list->$id;
				list($weakness_key) = count((array) $mouse->weaknesses) ? array_keys((array) $mouse->weaknesses) : array('0');

				$mouse_json->description = $mouse->description;
				$mouse_json->thumb = $mouse->thumb;
				$mouse_json->image = $mouse->medium;
				$mouse_json->weaknesses = ($weakness_key != '0' ? $mouse->weaknesses->{$weakness_key} : array());
			}
			break;
		}
	}
	file_put_contents("mouse_list.json", json_encode($mouse_list, JSON_PRETTY_PRINT));
?>
