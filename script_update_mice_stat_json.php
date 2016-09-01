<?php
	$NL = PHP_SAPI == "cli" ? "\n" : "<br>";

	$groups = json_decode(file_get_contents("mice_stat.json"));
	$new_stats = preg_split("/\n/ ", file_get_contents("mice_stat_update.in"));

	foreach ($new_stats as $new_stat) {
		if ($new_stat == "")
			continue;
		$group_stat = json_decode($new_stat);

		// remove uninitialized categories
		foreach ($group_stat->page->tabs[2]->subtabs[0]->categories as $h => $_group) {
			if (!$_group->initialized) {
				unset($group_stat->page->tabs[2]->subtabs[0]->categories[$h]);
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

		if ($new_group)
			$groups[] = $group_stat;
	}

	foreach ($groups as $key => $group) {
		unset($groups[$key]->page->tabs[2]->type);
		unset($groups[$key]->page->tabs[2]->name);
		unset($groups[$key]->page->tabs[2]->css_class);
		unset($groups[$key]->page->tabs[2]->show_subtabs);
		foreach ($groups[$key]->page->tabs[2]->subtabs as $h => $subtab) {
			unset($groups[$key]->page->tabs[2]->subtabs[$h]->name);
			unset($groups[$key]->page->tabs[2]->subtabs[$h]->css_class);
			$json = json_encode($groups[$key]->page->tabs[2]->subtabs[$h]->categories);
			$groups[$key]->page->tabs[2]->subtabs[$h]->categories = json_decode($json, TRUE);
			foreach ($groups[$key]->page->tabs[2]->subtabs[$h]->categories as $i => $category) {
				unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["type"]);
				unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["status"]);
				unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["has_weaknesses"]);
				unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["weaknesses"]);
				unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["total"]);
				unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["caught"]);
				unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["display_order"]);
				unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["show_image"]);
				unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["show_stats"]);
				unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["is_complete"]);
				foreach ($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["subgroups"] as $j => $subgroup) {
					unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["subgroups"][$j]["display_order"]);
					foreach ($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["subgroups"][$j]["mice"] as $k => $mouse) {
						unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["subgroups"][$j]["mice"][$k]["display_order"]);
						unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["subgroups"][$j]["mice"][$k]["num_catches"]);
						unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["subgroups"][$j]["mice"][$k]["num_misses"]);
						unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["subgroups"][$j]["mice"][$k]["avg_weight"]);
						unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["subgroups"][$j]["mice"][$k]["heaviest_catch"]);
						unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["subgroups"][$j]["mice"][$k]["global_num_catches"]);
						unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["subgroups"][$j]["mice"][$k]["global_avg_weight"]);
						unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["subgroups"][$j]["mice"][$k]["global_heaviest_catch"]);
						unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["subgroups"][$j]["mice"][$k]["css_class"]);
						unset($groups[$key]->page->tabs[2]->subtabs[$h]->categories[$i]["subgroups"][$j]["mice"][$k]["crown"]);
					}
				}
			}
		}
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
