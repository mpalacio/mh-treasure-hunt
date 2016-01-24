<?php
	$NL = PHP_SAPI == "cli" ? "\n" : "<br>";

	$groups = json_decode(file_get_contents("mice_stat.json"));
	$new_stats = preg_split("/\n/ ", file_get_contents("mice_stat_update.in"));

	foreach ($new_stats as $new_stat) {
		$group_stat = json_decode($new_stat);
		$group_stat_name = get_initialized_group($group_stat);
		$new_group = true;
		foreach ($groups as $key => $group) {
			$group_name = get_initialized_group($group);
			if($group_name == $group_stat_name) {
				$groups[$key]->page->tabs[2]->subtabs[0]->categories = $group_stat->page->tabs[2]->subtabs[0]->categories;
				$new_group = false;
				break;
			}
		}
		if($new_group)
			$groups[] = $group_stat;
	}

	file_put_contents("mice_stat.json", json_encode($groups));
	file_put_contents("mice_stat_update.in", "");

	function get_initialized_group($group_stat) {
		foreach ($group_stat->page->tabs[2]->subtabs[0]->categories as $key => $group) {
			if(!$group->initialized)
				continue;
			return $group->name;
		}
		return false;
	}
?>