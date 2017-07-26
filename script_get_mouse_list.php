<?php
	$NL = PHP_SAPI == "cli" ? "\n" : "<br>";

	$sunken_city_zones = json_decode(file_get_contents("sunken_city_zones.json"));
	$region_locations  = json_decode(file_get_contents("locations.json"));
	$regions           = array_keys((array) $region_locations);
	$locations         = array();
	foreach ($region_locations as $location)
		$locations = array_merge($locations, $location);

	$mouse_list = array();
	$dom        = new DOMDocument();
	$wiki       = "http://mhwiki.hitgrab.com/wiki/index.php/Mice";
	$wiki       = "mouse_list.html";
	$dom->loadHTMLFile($wiki);
	$xml        = simplexml_import_dom($dom);
	$table      = $xml->xpath("//table/tbody");
	$rows       = $table[0]->tr;

	unset($rows[0]);
	foreach ($rows as $key => $row) {
		$mouse_json                = array();
		$mouse_name                = substr(trim((string) $row->td[0]->b->a), -6) == " Mouse" ? substr_replace(trim((string) $row->td[0]->b->a), "", -6) : trim((string) $row->td[0]->b->a);
		$mouse_name                = substr($mouse_name, 0, 4) == "The " ? substr($mouse_name, 4) : $mouse_name;
		$mouse_json["id"]          = strtolower(str_replace(" ", "_", $mouse_name));
		$mouse_json["name"]        = $mouse_name;
		$mouse_json["description"] = "";

		$doc = new DOMDocument();
		$doc->loadHTML($row->td[1]->asXML());
		$location_text = $doc->textContent;
		if (preg_match("/all(.*)except/i", $location_text)) {
			$areas            = xml2array($row->td[1]->a)["a"];
			$except_regions   = array();
			$except_locations = array();
			foreach ($areas as $area) {
				$area_type = test_area($area);
				if ($area_type == "region")
					$except_regions[] = $area;
				elseif ($area_type == "location")
					$except_locations[] = $area;
			}
			$mouse_location_text = "All areas except: " . (count($except_regions) ? implode(" Region, ", $except_regions) . " Region" . (count($except_locations) ? ", " : "") : "") . "" . implode(", ", $except_locations);
			$mouse_locations     = clone $region_locations;
			foreach ($except_regions as $except_region)
				unset($mouse_locations->$except_region);
			foreach ($except_locations as $except_location) {
				$region  = get_region($except_location);
				$del_key = array_search($except_location, $mouse_locations->$region);
				unset($mouse_locations->{$region}[$del_key]);
				$mouse_locations->{$region} = array_values($mouse_locations->{$region});
			}
			foreach ($mouse_locations as $region => $location) {
				if (count($location) == 0)
					unset($mouse_locations->$location);
			}
		}
		elseif (preg_match("/many locations/i", $location_text)) {
			$mouse_location_text = "Many locations. See wiki page for details.";
			$mouse_locations     = array();
		}
		else {
			$areas           = xml2array($row->td[1]->a)["a"];
			$mouse_locations = array();
			$sunken_zones    = array();
			$sunken_id       = null;
			foreach ($areas as $i => $area) {
				$region = get_region($area);
				if (!isset($mouse_locations[$region]))
					$mouse_locations[$region] = array();
				if ($area == "Sunken City") {
					foreach ($sunken_city_zones->{$mouse_json["id"]} as $zone) {
						$mouse_locations[$region][] = $zone;
						$sunken_zones[]             = str_replace("Sunken City - ", "", $zone);
					}
					$sunken_id = $i;
				} else {
					$mouse_locations[$region][] = $area;
				}
			}

			if (!is_null($sunken_id)) {
				unset($areas[$sunken_id]);
				$areas = array_merge($areas, $sunken_zones);
			}
			$mouse_location_text = implode(", ", $areas);
		}
		$mouse_json["location"] = array("location_text" => $mouse_location_text, "areas" => $mouse_locations);
		$mouse_json["points"]   = trim((string) $row->td[2]);
		$mouse_json["gold"]     = trim((string) $row->td[3]);

		$doc = new DOMDocument();
		$doc->loadHTML($row->td[4]->asXML());
		$cheese_text = $doc->textContent;
		if (trim($cheese_text) == "")
			$mouse_json["cheese"] = "No preference";
		elseif (preg_match("/see current/i", $cheese_text))
			$mouse_json["cheese"] = "See current " . ((string) $row->td[4]->a) . " location";
		else {
			$cheese = xml2array($row->td[4]->a)["a"];
			$mouse_json["cheese"] = implode(", ", $cheese);
		}
		$mouse_json["group"]     = trim((string) $row->td[5]->a);
		$mouse_json["sub_group"] = isset($row->td[6]->a) ? ucwords(trim((string) $row->td[6]->a), ": ") : "";
		$mouse_json["image"]     = "img/e10adc3949ba59abbe56e057f20f883e.png";
		$mouse_json["thumb"]     = "img/e10adc3949ba59abbe56e057f20f883e.png";

		$mouse_list[$mouse_json["id"]] = $mouse_json;
	}

	file_put_contents("mouse_list.json", json_encode($mouse_list, JSON_PRETTY_PRINT));

	function xml2array($xml) {
		$arr = array();
		foreach ($xml as $element) {
			$tag      = $element->getName();
			$e_string = (string) $element;
			if ($element->count()) {
				$arr[$tag][] = xml2array($element);
			}
			else {
				$arr[$tag][] = (string) $element;
			}
		}

		return $arr;
	}

	function test_area($area) {
		if (in_array($area, $GLOBALS["regions"]))
			return "region";
		elseif (in_array($area, $GLOBALS["locations"]))
			return "location";
		return false;
	}

	function get_region($location) {
		foreach ($GLOBALS["region_locations"] as $region => $locations) {
			if (in_array($location, $locations))
				return $region;
		}
		return false;
	}
?>
