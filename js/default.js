var app = angular.module('mh-treasure-hunt', []).controller('mhTreasureHuntCtrl', function($scope, $http) {
	$scope.group_by = "default";
	$scope.mouse_default = [];
	$scope.mouse_group = [];
	$scope.mouse_location = [];
	$scope.search = "";
	$scope.current_mouse = null;
	$scope.mouse_over = false;
	$scope.maps = [];
	$scope.current_map = 'none';

	$http.get("http://localhost/mh-treasure-hunt/ajax_get_maps.php").then(function(response) {
		$scope.maps = response.data;
	});

	$http.get("http://localhost/mh-treasure-hunt/ajax_get_current_map.php").then(function(response) {
		$scope.current_map = response.data == "" ? "none" : response.data;
		$scope.get_map();
	});

	$scope.show_mouse = function(mouse, mouse_over) {
		$scope.current_mouse = mouse;
		$scope.mouse_over = mouse_over;
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
		$scope.show_mouse(mouse, false);

		if(send_post == true)
			$http.post("http://localhost/mh-treasure-hunt/ajax_catch_mice.php", {'map': $scope.current_map, 'mouse_ids': [mouse.id]});
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
		$scope.mice_list = "";
		if(mouse_ids.length > 0)
			$http.post("http://localhost/mh-treasure-hunt/ajax_catch_mice.php", {'map': $scope.current_map, 'mouse_ids': mouse_ids});
	}

	$scope.add_map = function(isValid) {
		if(!isValid)
			return
		else {
			if($.inArray($scope.new_map, $scope.maps) < 0) {
				$http.post("http://localhost/mh-treasure-hunt/ajax_set_map.php", {'map': $scope.new_map, 'mice_list': $scope.mice_list}).then(function(response) {
					$scope.maps.push($scope.new_map);
					$scope.current_map = $scope.new_map;
					$scope.new_map = $scope.mice_list = "";
					$scope.get_map();
				});
			}
			else
				console.log("Map exists");
		}
	}

	$scope.get_map = function() {
		$scope.mouse_default = [];
		$scope.mouse_group = [];
		$scope.mouse_location = [];
		if($scope.current_map != "none") {
			$http.post("http://localhost/mh-treasure-hunt/ajax_get_mouse_list.php", {'map': $scope.current_map}).then(function(response) {
				$scope.mouse_default = response.data.default;
				$scope.mouse_group = response.data.group;
				$scope.mouse_location = response.data.location;
			});
		}
		$http.post("http://localhost/mh-treasure-hunt/ajax_set_current_map.php", {'map': $scope.current_map});
	}

	$scope.delete_map = function() {
		$http.post("http://localhost/mh-treasure-hunt/ajax_delete_map.php", {'map': $scope.current_map}).then(function(response) {
			if(response.data == 'true') {
				$scope.current_map = "none";
				$http.get("http://localhost/mh-treasure-hunt/ajax_get_maps.php").then(function(response) {
					$scope.maps = response.data;
				});
				$scope.get_map();
			}
		});
	}
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
		for(var i in items){
			if(mice[items[i]][key] == value)
				filtered[i] = items[i];
		}
		return filtered;
	};
}).filter('filterEmptyGroup', function() {
	return function(items, caught, mice) {
		var filtered = {};
		for(var i in items){
			for(var j in items[i])
				if(mice[items[i][j]].caught == caught)
					filtered[i] = items[i];
		}
		return filtered;
	};
}).filter('filterEmptyRegion', function(filterEmptyGroupFilter, searchGroupFilter, toArrayFilter) {
	return function(items, filter, params) {
		var filtered = {};
		if(filter == "filterEmptyGroup") {
			for(var i in items){
				var locations = toArrayFilter(filterEmptyGroupFilter(items[i], params.caught, params.mice));
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
});