var app = angular.module('mh-treasure-hunt', []).controller('mhTreasureHuntCtrl', function($scope, $http, $sce) {
	$scope.current_mouse = null;
	$scope.locked_mouse = null;

	$scope.group_by = "default";
	$scope.search = "";

	$scope.maps = [];
	$scope.current_map = "none";
	$scope.current_hunter = "";

	$scope.show = "all";

	$scope.init_vars = function() {
		$scope.mouse_default = [];
		$scope.mouse_group = [];
		$scope.mouse_location = [];
		$scope.mouse_by_hunters = [];
		$scope.mouse_hunters = [];
	}
	$scope.init_vars();

	$scope.clean_input_fields = function() {
		$scope.input_map = "";
		$scope.input_hunters = "";
		$scope.input_mice_list = "";
	}
	$scope.clean_input_fields();

	$scope.get_maps = function() {
		$http.get("http://localhost/mh-treasure-hunt/ajax_get_maps.php").then(function(response) {
			$scope.maps = response.data.maps;
			$scope.current_map = response.data.current_map;
			$scope.get_map();
		});
	}
	$scope.get_maps();

	$scope.add_map = function(isValid) {
		if(!isValid) {
			alert('Missing fields.');
			return
		}
		else {
			if($.inArray($scope.input_map, $scope.maps) < 0) {
				$http.post("http://localhost/mh-treasure-hunt/ajax_set_map.php", {'map': $scope.input_map, 'hunters': $scope.input_hunters, 'mice_list': $scope.input_mice_list}).then(function(response) {
					$scope.maps.push($scope.input_map);
					$scope.current_map = $scope.input_map;
					$scope.clean_input_fields();
					$scope.get_map();
				});
			}
			else
				alert("Map exists.");
		}
	}

	$scope.get_map = function() {
		$scope.init_vars();
		$scope.clean_input_fields();
		$scope.show_mouse(null, true);
		$http.post("http://localhost/mh-treasure-hunt/ajax_get_map.php", {'map': $scope.current_map}).then(function(response) {
			if(response.data.map_found) {
				$scope.mouse_default = response.data.default;
				$scope.mouse_group = response.data.group;
				$scope.mouse_location = response.data.location;
				$scope.mouse_by_hunters = response.data.mouse_by_hunters;
				$scope.mouse_hunters = response.data.hunters;
				$scope.current_hunter = $scope.mouse_hunters[0];
			}
		});
	}

	$scope.delete_map = function() {
		if(confirm("Delete map?") == true) {
			$http.post("http://localhost/mh-treasure-hunt/ajax_delete_map.php", {'map': $scope.current_map}).then(function(response) {
				if(response.data.success == true) {
					$scope.current_map = "none";
					$scope.get_map();
					$scope.get_maps();
				}
				else
					alert("Failed to delete map.");
			});
		}
	}

	$scope.show_mouse = function(mouse, locked) {
		$scope.current_mouse = mouse;
		if(locked)
			$scope.locked_mouse = mouse;
		else {
			if(mouse == null)
				$scope.current_mouse = $scope.locked_mouse;
		}
	}

	$scope.count_mouse = function(mice, caught) {
		count = 0;
		for(var i in mice)
			if(mice[i].caught == caught)
				count++;
		return count;
	}

	$scope.isEmpty = function(obj) {
		for (var i in obj)
			if(obj.hasOwnProperty(i))
				return false;
		return true;
	};

	$scope.catch_mouse = function(mouse, send_post) {
		if(typeof send_post === 'undefined')
			send_post = true;

		$scope.mouse_default[mouse.id].caught = true;
		$scope.mouse_default[mouse.id].hunter = $scope.current_hunter;
		if($scope.mouse_by_hunters[$scope.current_hunter] == null)
			$scope.mouse_by_hunters[$scope.current_hunter] = [];
		$scope.mouse_by_hunters[$scope.current_hunter].push(mouse.id);
		$scope.mouse_by_hunters[$scope.current_hunter].sort();

		if(send_post == true) {
			if($scope.group_by == 'default') {
				setTimeout(function() {
					var catcher = $(".mouse-list-container:not(.ng-hide) .mouse-name.active").parents(".group-container").position().top;
					$(".mouse-list-container:not(.ng-hide)").animate({scrollTop: catcher}, "fast");
				}, 100);
			}
			$http.post("http://localhost/mh-treasure-hunt/ajax_catch_mice.php", {'map': $scope.current_map, 'mouse_ids': [mouse.id], 'hunter': $scope.current_hunter});
		}
	}

	$scope.catch_mice = function(mouse_list) {
		if($.trim(mouse_list) == '')
			return;
		mouse_list = mouse_list.split("\n");
		mouse_ids = [];
		for(var i in mouse_list) {
			mouse_key = mouse_list[i].toLowerCase().replace(" mouse", "").replace(/ /g, "_");
			mouse = $scope.mouse_default[mouse_key];
			if(mouse != null) {
				if(mouse.caught == false) {
					$scope.catch_mouse(mouse, false);
					mouse_ids.push(mouse_key);
				}
			}
		}
		$scope.input_mice_list = "";
		if(mouse_ids.length > 0)
			$http.post("http://localhost/mh-treasure-hunt/ajax_catch_mice.php", {'map': $scope.current_map, 'mouse_ids': mouse_ids, 'hunter': $scope.current_hunter});
	}

	$scope.renderHtml = function(htmlCode) {
		return $sce.trustAsHtml(htmlCode);
	};
}).filter('toArray', function() {
	return function(obj, addKey) {
		if(!(obj instanceof Object)) {
			return obj;
		}

		if(addKey === false) {
			return Object.values(obj);
		}
		else {
			return Object.keys(obj).map(function(key) {
				return Object.defineProperty(obj[key], '$key', {enumerable: false, value: key});
			});
		}
	};
}).filter('underscore', function() {
	return function(text) {
		return text.replace(/ /g, "_");
	};
}).filter('filterMiceByKey', function() {
	return function(items, key, value, mice) {
		var filtered = {};
		if(typeof(value) === "boolean") {
			for(var i in items){
				if(mice[items[i]][key] == value)
					filtered[i] = items[i];
			}
		}
		else {
			for(var i in items){
				if(mice[items[i]][key].toLowerCase().indexOf(value.toLowerCase()) > -1)
					filtered[i] = items[i];
			}
		}
		return filtered;
	};
}).filter('filterEmptyGroup', function() {
	return function(items, key, val, mice) {
		if(key == "caught")
			if(val == "all")
				return items;
			else
				val = JSON.parse(val);
		var filtered = {};
		for(var i in items){
			for(var j in items[i])
				if(mice[items[i][j]][key] == val)
					filtered[i] = items[i];
		}
		return filtered;
	};
}).filter('filterEmptyRegion', function(filterEmptyGroupFilter, searchGroupFilter, filterMiceByKeyFilter, toArrayFilter, jqueryToArrayFilter) {
	return function(items, filter, params) {
		var filtered = {};
		if(filter == "filterEmptyGroup") {
			for(var i in items){
				var locations = toArrayFilter(filterEmptyGroupFilter(items[i], params.key, params.val, params.mice));
				if(locations.length > 0)
					filtered[i] = items[i];
			}
		}
		else if(filter == "searchGroup") {
			for(var i in items){
				var locations = toArrayFilter(searchGroupFilter(items[i], params.search));
				if(locations.length > 0)
					filtered[i] = items[i];
			}
		}
		else if(filter == "filterMiceByKey") {
			for(var i in items){
				var hunter_mice = jqueryToArrayFilter(filterMiceByKeyFilter(items[i], params.key, params.val, params.mice, true));
				if(hunter_mice.length > 0)
					filtered[i] = items[i];
			}
		}
		else
			filtered = items;
		return filtered;
	};
}).filter('searchGroup', function() {
	return function(items, search) {
		var filtered = {};
		var keys = Object.keys(items).sort();
		for(var i = 0; i < keys.length; i++){
			var k = keys[i];
			if(k.toLowerCase().indexOf(search.toLowerCase()) > -1)
				filtered[k] = items[k];
		}
		return filtered;
	};
}).filter('cleanHtml', function() {
	return function(text) {
		var texts;
		try{
			texts = text.split(/<br \/>|<br>/);
		}
		catch(err){
			texts = [];
		}
		filtered = [];
		for(var i in texts) {
			if($.trim(texts[i]) != "")
				filtered.push($.trim(texts[i]));
		}
		return "<p>" + filtered.join("</p><p>") + "</p>";
	};
}).filter('jqueryToArray', function() {
	return function(arr) {
		return $.map(arr, function(value, index) {
			return [value];
		});
	};
});