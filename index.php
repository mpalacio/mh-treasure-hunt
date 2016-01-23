<!DOCTYPE html>
<html lang="en">
<head>
	<title>Mousehunt Treasure Map</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha256-7s5uDGW3AHqw6xtJmNNtr+OBRJUlgkNJEo78P4b0yRw= sha512-nNo+yCHEyn0smMxSswnf/OnX6/KwJuZTlNZBjauKhTK0c+zT+q5JOCx0UFhXQ6rJR9jg6Es8gPuD2uZcYDLqSw==" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/default.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha256-KXn5puMvxCw+dAYznun+drMdG1IFl3agK0p/pqT9KAo= sha512-2e8qq0ETcfWRI4HJBzQiA3UoyFk6tbNyG+qSaIBZLyW9Xf3sWZHN/lxe9fTh1U45DpPf07yj94KsUHHWe4Yk1A==" crossorigin="anonymous"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
</head>
<body ng-app="mh-treasure-hunt" ng-controller="mhTreasureHuntCtrl">
	<div class="container">
		<div class="row mh-container">
			<div class="col-md-3 section-left">
				<label>List Mouse Names</label>
				<textarea class="form-control" ng-model="mice_list"></textarea>
				<center>
					<button class="btn btn-primary" ng-click="add_mice(mice_list)">Add Mice</button>
					<button class="btn btn-primary" ng-click="catch_mice(mice_list)">Catch Mice</button>
					<button class="btn btn-primary" ng-click="mice_list = ''">Clear</button>
				</center>
			</div>
			<div class="col-md-9">
				<div class="row section-top">
					<div class="col-md-12">
						<div class="form-inline group-by">
							<label class="custom-label">Group By: </label>
							<label class="radio-inline">
								<input type="radio" name="group_by" ng-model="group_by" value="default"> None
							</label>
							<label class="radio-inline">
								<input type="radio" name="group_by" ng-model="group_by" value="group"> Mouse Group
							</label>
							<label class="radio-inline">
								<input type="radio" name="group_by" ng-model="group_by" value="location"> Location
							</label>
							<label class="custom-label">Search: </label>
							<input type="text" class="form-control input-sm" ng-model="search">
						</div>
					</div>
				</div>
				<div class="row section-body">
					<div class="col-md-6">
						<h3>Uncaught Mice <i>({{count_mouse(mouse_default, false)}}/{{(mouse_default | toArray).length}} Mice)</i></h3>
						<div class="row mouse-list-container" ng-show="group_by == 'default'">
							<div ng-show="isEmpty(mouse_default)">Loading...</div>
							<div class="col-md-12 group-container" ng-hide="isEmpty(mouse_default)">
								<div class="col-md-4" ng-repeat="mouse in mouse_default | toArray | filter:{name:search} | filter:{caught:false}" ng-click="catch_mouse(mouse)" ng-mouseover="show_mouse(mouse, true)" ng-mouseleave="show_mouse(mouse, false)">
									<div class="mouse-name uncaught" ng-style="{'background-image': 'url({{mouse.thumb}})'}">
										{{mouse.name.replace(" Mouse", "")}}
									</div>
								</div>
							</div>
						</div>
						<div class="row mouse-list-container" ng-show="group_by == 'group'">
							<div ng-show="isEmpty(mouse_group)">Loading...</div>
							<div class="col-md-12 group-container" ng-hide="isEmpty(mouse_group)" ng-repeat="(group_name, group) in mouse_group | filterEmptyGroup:false:mouse_default | searchGroup:search">
								<label class="group-name">{{group_name}}</label>
								<div class="col-md-4" ng-repeat="mouse in group | filterMiceByKey:'caught':false:mouse_default" ng-click="catch_mouse(mouse_default[mouse])" ng-mouseover="show_mouse(mouse_default[mouse], true)" ng-mouseleave="show_mouse(mouse_default[mouse], false)">
									<div class="mouse-name uncaught" ng-style="{'background-image': 'url({{mouse_default[mouse].thumb}})'}">
										{{mouse_default[mouse].name.replace(" Mouse", "")}}
									</div>
								</div>
							</div>
						</div>
						<div class="row mouse-list-container" ng-show="group_by == 'location'">
							<div ng-show="isEmpty(mouse_location)">Loading...</div>
							<div class="col-md-12 group-container" ng-hide="isEmpty(mouse_location)" ng-repeat="(region_name, region) in mouse_location | filterEmptyRegion:'filterEmptyGroup':{caught:false, mice:mouse_default} | filterEmptyRegion:'searchGroup':{search:search}">
								<label class="location-name">{{region_name}}</label>
								<div class="col-md-12 group-container" ng-repeat="(location_name, location) in region | filterEmptyGroup:false:mouse_default | searchGroup:search">
									<label class="location-name"><a href="http://mhwiki.hitgrab.com/wiki/index.php/{{location_name | underscore}}" target="_blank">{{location_name}}</a></label>
									<div class="col-md-4" ng-repeat="mouse in location | filterMiceByKey:'caught':false:mouse_default" ng-click="catch_mouse(mouse_default[mouse])" ng-mouseover="show_mouse(mouse_default[mouse], true)" ng-mouseleave="show_mouse(mouse_default[mouse], false)">
										<div class="mouse-name uncaught" ng-style="{'background-image': 'url({{mouse_default[mouse].thumb}})'}">
											{{mouse_default[mouse].name.replace(" Mouse", "")}}
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<h3>Caught Mice <i>({{count_mouse(mouse_default, true)}}/{{(mouse_default | toArray).length}} Mice)</i></h3>
						<div class="row mouse-list-container" ng-show="group_by == 'default'">
							<div ng-show="isEmpty(mouse_default)">Loading...</div>
							<div class="col-md-12 group-container" ng-hide="isEmpty(mouse_default)">
								<div class="col-md-4" ng-repeat="mouse in mouse_default | toArray | filter:{name:search} | filter:{caught:true}" ng-mouseover="show_mouse(mouse, true)" ng-mouseleave="show_mouse(mouse, false)">
									<div class="mouse-name" ng-style="{'background-image': 'url({{mouse.thumb}})'}">
										{{mouse.name.replace(" Mouse", "")}}
									</div>
								</div>
							</div>
						</div>
						<div class="row mouse-list-container" ng-show="group_by == 'group'">
							<div ng-show="isEmpty(mouse_group)">Loading...</div>
							<div class="col-md-12 group-container" ng-hide="isEmpty(mouse_group)" ng-repeat="(group_name, group) in mouse_group | filterEmptyGroup:true:mouse_default | searchGroup:search">
								<label class="group-name">{{group_name}}</label>
								<div class="col-md-4" ng-repeat="mouse in group | filterMiceByKey:'caught':true:mouse_default" ng-mouseover="show_mouse(mouse_default[mouse], true)" ng-mouseleave="show_mouse(mouse_default[mouse], false)">
									<div class="mouse-name" ng-style="{'background-image': 'url({{mouse_default[mouse].thumb}})'}">
										{{mouse_default[mouse].name.replace(" Mouse", "")}}
									</div>
								</div>
							</div>
						</div>
						<div class="row mouse-list-container" ng-show="group_by == 'location'">
							<div ng-show="isEmpty(mouse_location)">Loading...</div>
							<div class="col-md-12 group-container" ng-hide="isEmpty(mouse_location)" ng-repeat="(region_name, region) in mouse_location | filterEmptyRegion:'filterEmptyGroup':{caught:true, mice:mouse_default} | filterEmptyRegion:'searchGroup':{search:search}">
								<label class="location-name">{{region_name}}</label>
								<div class="col-md-12 group-container" ng-repeat="(location_name, location) in region | filterEmptyGroup:true:mouse_default | searchGroup:search">
									<label class="location-name"><a href="http://mhwiki.hitgrab.com/wiki/index.php/{{location_name | underscore}}" target="_blank">{{location_name}}</a></label>
									<div class="col-md-4" ng-repeat="mouse in location | filterMiceByKey:'caught':true:mouse_default" ng-mouseover="show_mouse(mouse_default[mouse], true)" ng-mouseleave="show_mouse(mouse_default[mouse], false)">
										<div class="mouse-name uncaught" ng-style="{'background-image': 'url({{mouse_default[mouse].thumb}})'}">
											{{mouse_default[mouse].name.replace(" Mouse", "")}}
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row section-bottom">
					<div class="col-md-12 mouse-preview" ng-show="mouse_over == true">
						<div class="arrow"></div>
						<div class="preview-container">
							<div class="col-md-12 preview-name">{{current_mouse.name}}</div>
							<div class="col-md-4 preview-group">Group: <strong>{{current_mouse.group}}</strong></div>
							<div class="col-md-8 preview-cheese">Cheese Preference: <strong>{{current_mouse.cheese}}</strong></div>
							<div class="col-md-4 preview-weaknesses">Weaknesses: <img ng-repeat="weakness in current_mouse.weaknesses" ng-src="{{weakness.icon}}" title="{{weakness.name}}"></div>
							<div class="col-md-8 preview-location">Location: <strong>{{current_mouse.location.location_text}}</strong></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<script src="js/default.js"></script>
</html>