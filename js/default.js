angular.module('mh-treasure-hunt', []).controller('mhTreasureHuntCtrl', function($scope, $http) {
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
			if (obj.hasOwnProperty(i))
				return false;
		return true;
	};
}).filter('toArray', function () {
	return function (obj, addKey) {
		if (!(obj instanceof Object)) {
			return obj;
		}

		if ( addKey === false ) {
			return Object.values(obj);
		} else {
			return Object.keys(obj).map(function (key) {
				return Object.defineProperty(obj[key], '$key', { enumerable: false, value: key});
			});
		}
	};
}).filter('underscore', function() {
	return function(text) {
		return text.replace(/ /g, "_");
	};
});