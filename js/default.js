var app = angular.module('mh-treasure-hunt', []).controller('mhTreasureHuntCtrl', function($scope, $http, $filter) {
	$scope.group_by = "default";
	$scope.mouse_default = [];
	$scope.mouse_group = [];
	$scope.mouse_location = [];
	$scope.search = "";
	$scope.current_mouse = null;
	$scope.mouse_over = false;

	$http.get("http://localhost/mh-treasure-hunt/ajax_get_mouse_list.php").then(function(response) {
		$scope.mouse_default = response.data.default;
		$scope.mouse_group = response.data.group;
		$scope.mouse_location = response.data.location;
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
			$http.post("http://localhost/mh-treasure-hunt/ajax_catch_mice.php", {'mouse_ids': [mouse.id]});
	}

	$scope.catch_mice = function(mouse_list) {
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
		if(mouse_ids.length > 0)
			$http.post("http://localhost/mh-treasure-hunt/ajax_catch_mice.php", {'mouse_ids': mouse_ids});
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
		var sorted = {};
		for(var i in items){
			if(mice[items[i]][key] == value)
				sorted[i] = items[i];
		}
		return sorted;
	};
}).filter('filterEmptyGroup', function() {
	return function(items, caught, mice) {
		var sorted = {};
		for(var i in items){
			for(var j in items[i])
				if(mice[items[i][j]].caught == caught)
					sorted[i] = items[i];
		}
		return sorted;
	};
}).filter('filterEmptyRegion', function(filterEmptyGroupFilter, toArrayFilter) {
	return function(items, caught, mice) {
		var sorted = {};
		for(var i in items){
			var locations = toArrayFilter(filterEmptyGroupFilter(items[i], caught, mice));
			if(locations.length > 0)
				sorted[i] = items[i];
		}
		return sorted;
	};
});