<?php
    $NL = PHP_SAPI == "cli" ? "\n" : "<br>";

    $mouse_locations = json_decode(file_get_contents("mouse_locations.json"));
    $new_stats = preg_split("/\n/ ", file_get_contents("mouse_locations_update.in"));
    $regions = json_decode(file_get_contents("locations.json"));

    $location_texts = array(
        "high_roller"  => "All locations except: Meadow",
        "leprechaun"   => "All locations except: Meadow",
        "mobster"      => "All locations except: Meadow",
        "snooty"       => "All locations except: Meadow",
        "treasurer"    => "All locations except: Meadow",
        "treasurer"    => "All locations except: Meadow",
        "black_widow"  => "Many locations. See wiki page for details.",
        "glitchpaw"    => "Many locations. See wiki page for details.",
        "lucky"        => "Many locations. See wiki page for details.",
        "relic_hunter" => "Many locations. See wiki page for details."
    );

    foreach ($new_stats as $new_stat) {
        if ($new_stat == "")
            continue;
        $location = json_decode($new_stat);

        // remove uninitialized categories
        foreach ($location->page->tabs[2]->subtabs[1]->categories as $key => $_location) {
            if ($_location->initialized) {
                $location_name = $_location->name;
                $region = get_region($location_name, $regions);
                if ($region != "") {
                    $mice = $_location->subgroups[0]->mice;
                    foreach ($mice as $mouse) {
                        $m_name = substr($mouse->name, -6) == " Mouse" ? substr_replace($mouse->name, "", -6) : $mouse->name;
                        $m_name = substr($m_name, 0, 4) == "The " ? substr($m_name, 4) : $m_name;
                        $id     = strtolower(str_replace(" ", "_", $m_name));
                        if (!isset($mouse_locations->$id)) {
                            $mouse_locations->$id = (object) array(
                                "location_text" => "",
                                "areas"         => (object) array()
                            );
                        }

                        if (!isset($mouse_locations->$id->areas->{$region})) {
                            $mouse_locations->$id->areas->{$region} = array();
                        }
                        if (!in_array($location_name, $mouse_locations->$id->areas->{$region})) {
                            array_push($mouse_locations->$id->areas->{$region}, $location_name);
                        }
                    }
                }
            }
        }
    }

    foreach ($mouse_locations as $id => &$mouse) {
        if (in_array($id, array_keys($location_texts))) {
            $mouse->location_text = $location_texts[$id];
        } else {
            $locations = array();
            foreach ($mouse->areas as $area_locations) {
                $locations = array_merge($locations, $area_locations);
            }
            $mouse->location_text = implode(", ", $locations);
        }
    }

    file_put_contents("mouse_locations.json", json_encode($mouse_locations, JSON_PRETTY_PRINT));

    function get_region($location, $regions) {
        foreach ($regions as $region => $locations) {
            if (in_array($location, $locations))
                return $region;
        }
    }
?>
