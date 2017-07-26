<?php
	$NL = PHP_SAPI == "cli" ? "\n" : "<br>";

	$dom = new DOMDocument();
	$wiki = 'http://mhwiki.hitgrab.com/wiki/index.php/Location';
	$wiki = 'locations.html';
	$dom->loadHTMLFile($wiki);
	$xml = simplexml_import_dom($dom);
	$rows = $xml->xpath('//table/tbody/tr');

	$locations = array();
	unset($rows[0]);
	$region = "";
	foreach ($rows as $key => $row) {
		if (isset($row->th)) {
			$region = (string) $row->th->a;
			$locations[$region] = array();
		}
		$locations[$region][] = xml2array($row->td[0]->a)["a"][0];
	}

	file_put_contents("locations.json", json_encode($locations, JSON_PRETTY_PRINT));

	function xml2array($xml) {
		$arr = array();
		foreach ($xml as $element) {
			$tag = $element->getName();
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
?>
