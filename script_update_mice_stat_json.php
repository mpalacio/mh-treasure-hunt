<?php
	$NL = PHP_SAPI == "cli" ? "\n" : "<br>";

	$groups = json_decode(file_get_contents("mice_stat.json"));
	$new_stats = preg_split("/\n/ ", file_get_contents("mice_stat_update.in"));

	foreach ($new_stats as $new_stat) {
		if ($new_stat == "")
			continue;
		$group_stat = json_decode($new_stat);
		$group_stat_name = get_initialized_group($group_stat);
		$new_group = true;
		foreach ($groups as $key => $group) {
			$group_name = get_initialized_group($group);
			if ($group_name == $group_stat_name) {
				$groups[$key]->page->tabs[2]->subtabs[0]->categories = $group_stat->page->tabs[2]->subtabs[0]->categories;
				$new_group = false;
				break;
			}
		}

		// remove uninitialized categories
		foreach ($group_stat->page->tabs[2]->subtabs[0]->categories as $_key => $_group) {
			if (!$_group->initialized) {
				unset($group_stat->page->tabs[2]->subtabs[0]->categories[$_key]);
			}
		}

		unset($group_stat->user);
		unset($group_stat->messageData);
		unset($group_stat->page_title);
		unset($group_stat->page_banner);
		unset($group_stat->success);
		unset($group_stat->inventory);
		unset($group_stat->journal_markup);
		unset($group_stat->last_read_journal_entry_id);
		unset($group_stat->asset_package_hash);
		unset($group_stat->page->tabs[2]->subtabs[1]);
		$group_stat->page->tabs[0] = array();
		$group_stat->page->tabs[1] = array();

		if ($new_group)
			$groups[] = $group_stat;
	}

	file_put_contents("mice_stat.json", json_encode($groups, JSON_PRETTY_PRINT));
	file_put_contents("mice_stat_update.in", "");

	function get_initialized_group($group_stat) {
		foreach ($group_stat->page->tabs[2]->subtabs[0]->categories as $key => $group) {
			if (!$group->initialized)
				continue;
			return $group->name;
		}
		return false;
	}
?>
