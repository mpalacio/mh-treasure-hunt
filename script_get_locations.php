<?php
	$NL = PHP_SAPI == "cli" ? "\n" : "<br>";

	$dom = new DOMDocument();
	$wiki = 'http://mhwiki.hitgrab.com/wiki/index.php/Location_Quick_Reference';
	$wiki = 'locations.html';
	$dom->loadHTMLFile($wiki);
	$xml = simplexml_import_dom($dom);
	$rows = $xml->xpath('//*[@id="bodyContent"]/div[4]/table/tr');

	$locations = array();
	unset($rows[0]);
	unset($rows[14]);
	foreach ($rows as $key => $row) {
		$locations[(string) $row->td[0]->b->a] = xml2array($row->td[1]->a)['a'];
	}

	file_put_contents("locations.json", json_encode($locations));

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
