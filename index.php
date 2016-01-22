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
<body>
	<div class="container">
		<div class="row mh-container">
			<div class="col-md-3 section-left">
				<label>List Mouse Names</label>
				<textarea class="form-control"></textarea>
				<center>
					<button class="btn btn-primary">Add Mice</button>
					<button class="btn btn-primary">Catch Mice</button>
					<button class="btn btn-primary">Clear</button>
				</center>
			</div>
			<div class="col-md-9">
				<div class="row section-top">
					<div class="col-md-12">
						<div class="form-inline group-by">
							<label class="custom-label">Group By: </label>
							<label class="radio-inline">
							  <input type="radio" name="group_by"> None
							</label>
							<label class="radio-inline">
							  <input type="radio" name="group_by"> Mouse Group
							</label>
							<label class="radio-inline">
							  <input type="radio" name="group_by"> Location
							</label>
							<label class="custom-label">Search: </label>
							<input type="text" class="form-control input-sm">
						</div>
					</div>
				</div>
				<div class="row section-body">
					<div class="col-md-6">
						<h3>Uncaught Mice</h3>
						<div class="row mouse-list-container">
							<div class="col-md-12 group-container">
							</div>
						</div>
						<div class="row mouse-list-container">
							<div class="col-md-12 group-container">
							</div>
						</div>
						<div class="row mouse-list-container">
							<div class="col-md-12 group-container">
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<h3>Caught Mice</h3>
						<div class="row mouse-list-container">
							<div class="col-md-12 group-container">
							</div>
						</div>
						<div class="row mouse-list-container">
							<div class="col-md-12 group-container">
							</div>
						</div>
						<div class="row mouse-list-container">
							<div class="col-md-12 group-container">
							</div>
						</div>
					</div>
				</div>
				<div class="row section-bottom">
					<div class="col-md-12 mouse-preview">
						<div class="arrow"></div>
						<div class="preview-container">
							<div class="col-md-12 preview-name">Mouse Name: </div>
							<div class="col-md-4 preview-group">Group: <strong></strong></div>
							<div class="col-md-8 preview-cheese">Cheese Preference: <strong></strong></div>
							<div class="col-md-4 preview-weaknesses">Weaknesses: <strong></strong></div>
							<div class="col-md-8 preview-location">Location: <strong></strong></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>