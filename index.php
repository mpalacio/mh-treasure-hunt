<!DOCTYPE html>
<html lang="en">
<head>
	<title>Mousehunt Treasure Map</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/default.css">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/angular.min.js"></script>
	<script src="js/default.js"></script>
</head>
<body ng-app="mh-treasure-hunt" ng-controller="mhTreasureHuntCtrl">
	<div class="container">
		<div class="row mh-container">
			<div class="col-md-3 col-sm-7 col-xs-7 section-left">
				<form name="add_map_form" novalidate>
					<div class="form-inline">
						<label>Map: </label>
						<select class="form-control input-sm" ng-model="current_map" ng-change="get_map()" ng-disabled="edit_map_flag == true">
							<option value="none">Add New Map</option>
							<option ng-repeat="map in maps" value="{{map}}">{{map}}</option>
						</select>
					</div>
					<div class="form-inline new-map" ng-show="current_map == 'none' || edit_map_flag == true">
						<label>Map Name: </label>
						<input type="text" class="form-control input-sm" name="new_map" ng-model="input_map" required>
					</div>
					<label ng-show="current_map == 'none' || edit_map_flag == true">Mousehunters:</label>
					<textarea class="form-control" ng-model="input_hunters" ng-show="current_map == 'none' || edit_map_flag == true" style="height: 75px" data-limit-rows="true" required></textarea>
					<div class="form-inline" ng-hide="current_map == 'none' || edit_map_flag == true">
						<label>Hunter: </label>
						<select class="form-control input-sm hunters" ng-model="current_hunter">
							<option ng-repeat="hunter in mouse_hunters" value="{{hunter}}">{{hunter}}</option>
						</select>
					</div>
					<label>List Mouse Names:</label>
					<textarea class="form-control mouse-list" ng-model="input_mice_list" style="height: calc(100% - {{(current_map == 'none' || edit_map_flag == true) ? '245px' : '145px'}})" required></textarea>
					<center>
						<button class="btn btn-xs btn-primary" ng-hide="current_map != 'none'" ng-click="add_map(add_map_form.$valid)">Add Map</button>
						<button class="btn btn-xs btn-success" ng-show="edit_map_flag == true" ng-click="update_map(add_map_form.$valid)">Update Map</button>
						<button class="btn btn-xs btn-primary" ng-hide="current_map == 'none' || edit_map_flag == true" ng-click="catch_mice(input_mice_list)">Catch Mice</button>
						<button class="btn btn-xs btn-info" ng-hide="current_map == 'none' || edit_map_flag == true" ng-click="edit_map()">Edit Map</button>
						<button class="btn btn-xs btn-primary" ng-show="edit_map_flag == true" ng-click="cancel_edit_map()">Cancel</button>
						<button class="btn btn-xs btn-danger" ng-hide="current_map == 'none'" ng-click="delete_map()">Delete Map</button>
					</center>
				</form>
			</div>
			<div class="col-md-9 col-sm-12 col-xs-12">
				<div class="section-top">
					<div class="col-md-12 col-sm-7 col-xs-7">
						<div class="form-inline group-by">
							<label class="custom-label">Group By: </label>
							<label class="radio-inline">
								<input type="radio" name="group_by" ng-model="group_by" value="default" ng-click="show_mouse(null, true)"> Catcher
							</label>
							<label class="radio-inline">
								<input type="radio" name="group_by" ng-model="group_by" value="group" ng-click="show_mouse(null, true)"> Mouse Group
							</label>
							<label class="radio-inline">
								<input type="radio" name="group_by" ng-model="group_by" value="location" ng-click="show_mouse(null, true)"> Location
							</label>
						</div>
						<div class="form-inline group-by">
							<label class="custom-label">Search: </label>
							<input type="text" class="form-control input-sm" ng-model="search">
							<label class="custom-label">Show: </label>
							<select class="form-control input-sm" ng-model="show">
								<option value="all">All</option>
								<option value="true">Caught</option>
								<option value="false">Uncaught</option>
							</select>
						</div>
					</div>
				</div>
				<div class="section-map">
					<div class="col-md-7 col-sm-7 col-xs-7">
						<div class="row section-body">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<div class="row mouse-list-container" ng-show="group_by == 'default' && current_map != 'none'">
									<div class="loading" ng-show="isEmpty(mouse_default)">Loading...</div>
									<div class="col-md-12 col-sm-12 col-xs-12 group-container" ng-hide="isEmpty(mouse_default)" ng-show="(show == 'all') || (show == 'false')">
										<label class="group-name">Uncaught Mice <i ng-hide="current_map == 'none'">({{count_mouse(mouse_default, false)}}/{{(mouse_default | toArray).length}} Mice)</i></label>
										<div class="col-md-4 col-sm-6 col-xs-6" ng-repeat="mouse in mouse_default | toArray | filter:{name:search} | filter:{caught:false}">
											<div class="mouse-name uncaught" ng-class="{active: locked_mouse==mouse}" ng-style="{'background-image': 'url(' + mouse.thumb + ')'}" ng-click="show_mouse(mouse, true)" ng-mouseover="show_mouse(mouse, false)" ng-mouseleave="show_mouse(null, false)">
												{{mouse.name.replace(" Mouse", "")}}
											</div>
										</div>
									</div>
									<div class="col-md-12 col-sm-12 col-xs-12 group-container" ng-hide="isEmpty(mouse_default)" ng-show="(show == 'all') || (show == 'true')" ng-repeat="(hunter_name, group) in mouse_by_hunters | filterEmptyRegion:'filterMiceByKey':{key:'name', val:search, mice:mouse_default}">
										<label class="group-name">{{hunter_name}} caught these mice: <i>({{group.length}} Mice)</i></label>
										<div class="col-md-4 col-sm-6 col-xs-6" ng-repeat="mouse in group | filterMiceByKey:'name':search:mouse_default | filterMiceByKey:'caught':true:mouse_default">
											<div class="mouse-name caught" ng-class="{active: locked_mouse==mouse_default[mouse]}" ng-style="{'background-image': 'url(' + mouse_default[mouse].thumb + ')'}" ng-click="show_mouse(mouse_default[mouse], true)" ng-mouseover="show_mouse(mouse_default[mouse], false)" ng-mouseleave="show_mouse(null, false)">
												{{mouse_default[mouse].name.replace(" Mouse", "")}}
											</div>
										</div>
									</div>
								</div>
								<div class="row mouse-list-container" ng-show="group_by == 'group' && current_map != 'none'">
									<div class="loading" ng-show="isEmpty(mouse_group)">Loading...</div>
									<div class="col-md-12 col-sm-12 col-xs-12 group-container" ng-hide="isEmpty(mouse_group)" ng-repeat="(group_name, group) in mouse_group | filterEmptyGroup:'caught':show:mouse_default | searchGroup:search">
										<label class="group-name">{{group_name}} <i>({{(group | filterMiceByKey:'caught':true:mouse_default | jqueryToArray).length}}/{{group.length}} Mice caught)</i></label>
										<div class="col-md-4 col-sm-6 col-xs-6" ng-show="(show == 'all') || (show == 'false')" ng-repeat="mouse in group | filterMiceByKey:'caught':false:mouse_default">
											<div class="mouse-name uncaught" ng-class="{active: locked_mouse==mouse_default[mouse]}" ng-style="{'background-image': 'url(' + mouse_default[mouse].thumb + ')'}" ng-click="show_mouse(mouse_default[mouse], true)" ng-mouseover="show_mouse(mouse_default[mouse], false)" ng-mouseleave="show_mouse(null, false)">
												{{mouse_default[mouse].name.replace(" Mouse", "")}}
											</div>
										</div>
										<div class="col-md-4 col-sm-6 col-xs-6" ng-show="(show == 'all') || (show == 'true')" ng-repeat="mouse in group | filterMiceByKey:'caught':true:mouse_default">
											<div class="mouse-name caught" ng-class="{active: locked_mouse==mouse_default[mouse]}" ng-style="{'background-image': 'url(' + mouse_default[mouse].thumb + ')'}" ng-click="show_mouse(mouse_default[mouse], true)" ng-mouseover="show_mouse(mouse_default[mouse], false)" ng-mouseleave="show_mouse(null, false)">
												{{mouse_default[mouse].name.replace(" Mouse", "")}}
											</div>
										</div>
									</div>
								</div>
								<div class="row mouse-list-container" ng-show="group_by == 'location' && current_map != 'none'">
									<div class="loading" ng-show="isEmpty(mouse_location)">Loading...</div>
									<div class="col-md-12 col-sm-12 col-xs-12 group-container" ng-hide="isEmpty(mouse_location)" ng-repeat="(region_name, region) in mouse_location | filterEmptyRegion:'filterEmptyGroup':{key:'caught', val:show, mice:mouse_default} | filterEmptyRegion:'searchGroup':{search:search}">
										<label class="location-name">{{region_name}}</label>
										<div class="col-md-12 col-sm-12 col-xs-12 group-container" ng-repeat="(location_name, location) in region | filterEmptyGroup:'caught':show:mouse_default | searchGroup:search">
											<label class="location-name"><a href="http://mhwiki.hitgrab.com/wiki/index.php/{{location_name | underscore}}" target="_blank">{{location_name}}</a> <i>({{(location | filterMiceByKey:'caught':true:mouse_default | jqueryToArray).length}}/{{location.length}} Mice caught)</i></label>
											<div class="col-md-4 col-sm-6 col-xs-6" ng-show="(show == 'all') || (show == 'false')" ng-repeat="mouse in location | filterMiceByKey:'caught':false:mouse_default">
												<div class="mouse-name uncaught" ng-class="{active: locked_mouse==mouse_default[mouse]}" ng-style="{'background-image': 'url(' + mouse_default[mouse].thumb + ')'}" ng-click="show_mouse(mouse_default[mouse], true)" ng-mouseover="show_mouse(mouse_default[mouse], false)" ng-mouseleave="show_mouse(null, false)">
													{{mouse_default[mouse].name.replace(" Mouse", "")}}
												</div>
											</div>
											<div class="col-md-4 col-sm-6 col-xs-6" ng-show="(show == 'all') || (show == 'true')" ng-repeat="mouse in location | filterMiceByKey:'caught':true:mouse_default">
												<div class="mouse-name caught" ng-class="{active: locked_mouse==mouse_default[mouse]}" ng-style="{'background-image': 'url(' + mouse_default[mouse].thumb + ')'}" ng-click="show_mouse(mouse_default[mouse], true)" ng-mouseover="show_mouse(mouse_default[mouse], false)" ng-mouseleave="show_mouse(null, false)">
													{{mouse_default[mouse].name.replace(" Mouse", "")}}
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-5 col-sm-5 col-xs-5">
						<div class="row section-mouse-detail">
							<div class="col-md-12 col-sm-12 col-xs-12 mouse-preview" ng-show="current_mouse != null">
								<div class="arrow"></div>
								<div class="preview-container">
									<a title="Close" class="preview-close" ng-click="show_mouse(null, true)">X</a>
									<div class="col-md-12 col-sm-12 col-xs-12 preview-name"><a ng-href="http://mhwiki.hitgrab.com/wiki/index.php/{{current_mouse.name | underscore}}_Mouse" target="_blank">{{current_mouse.name}}</a></div>
									<div class="col-md-12 col-sm-12 col-xs-12 preview-group">{{current_mouse.group}} {{current_mouse.sub_group != "" ? "(" + current_mouse.sub_group + ")" : ""}}</div>
									<div class="col-md-12 col-sm-12 col-xs-12 preview-gold-points">{{current_mouse.gold}} gold / {{current_mouse.points}} points</div>
									<div class="col-md-12 col-sm-12 col-xs-12 preview-image">
										<div class="img">
											<img src="{{current_mouse.image}}">
											<span class="catcher" ng-show="current_mouse.caught">{{current_mouse.hunter}}</span>
											<span class="catcher catch-mouse" ng-show="!current_mouse.caught" ng-click="catch_mouse(current_mouse)">Catch Mouse</span>
										</div>
									</div>
									<div class="col-md-12 col-sm-12 col-xs-12 preview-label">Weaknesses:</div>
									<div class="col-md-12 col-sm-12 col-xs-12 preview-weaknesses"><img ng-repeat="weakness in current_mouse.weaknesses" ng-src="{{weakness.icon}}" title="{{weakness.name}}"></div>
									<div class="col-md-12 col-sm-12 col-xs-12 preview-label">Cheese Preference:</div>
									<div class="col-md-12 col-sm-12 col-xs-12 preview-cheese">{{current_mouse.cheese}}</div>
									<div class="col-md-12 col-sm-12 col-xs-12 preview-label">Location:</div>
									<div class="col-md-12 col-sm-12 col-xs-12 preview-location">{{current_mouse.location.location_text}}</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	$(document).ready(function () {
		$('textarea[data-limit-rows=true]').on('keypress', function (event) {
			var textarea = $(this),
				numberOfLines = (textarea.val().match(/\n/g) || []).length + 1,
				maxRows = 5;

			if (event.which === 13 && numberOfLines === maxRows ) {
				return false;
			}
		});
	});
</script>
</html>
