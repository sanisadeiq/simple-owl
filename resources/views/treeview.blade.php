<html ng-app="owl">
<head>
	<title>OWL Query Helper</title>
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href={{ URL::asset('css/style.css') }}>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js"></script>
	<script type="text/javascript" src="js/app.js"></script>

	<script type="text/javascript" src={{ URL::asset('js/bootstrap-multiselect.js') }}></script>
	<link rel="stylesheet" href={{ URL::asset('css/bootstrap-multiselect.css') }} type="text/css"/>

	<script type="text/javascript" src={{ URL::asset('js/queryBuilder.js') }}></script>
</head>

<body>

	<div id="query-area">

		<h1>OWL Query Helper</h1>

		<form id="upload-form" action="">
			<input id="upload-button" type="submit" value="">
		</form>

		<form id="query-form" action="">
			<input id="query-button" type="submit" value="">
		</form>

		<input id="undo-button" type="submit" value="" onclick="rebuildQuery()">

		<div id="query-titles">
			<div class="query-title">CLASSES</div>
			<div class="query-title">INDIVIDUALS</div>
			<div class="query-title">RELATIONSHIPS</div>
			<div class="query-title">OPERATORS</div>
		</div>

		<div id="query-boxes">

				<!-- Receive lists from OWL API instead of hardcoded values -->
				<select id="classes">
					<option value="class1">Class A</option>
					<option value="class2">Class B</option>
					<option value="class3">Class C</option>
					<option value="class4">Class D</option>
				</select>

				<select id="individuals">
					<option value="individual1">Individual A</option>
					<option value="individual2">Individual B</option>
					<option value="individual3">Individual C</option>
					<option value="individual4">Individual D</option>
				</select>

				<select id="relationships">
					<option value="relationship1">Relationship A</option>
					<option value="relationship2">Relationship B</option>
					<option value="relationship3">Relationship C</option>
					<option value="relationship4">Relationship D</option>
				</select>

				<select id="operators">
				</select>
		</div>

		<p id="swrl-query">
			<span id="query-title">SWRL QUERY: </span>
			<span id="query-main"></span>
		</p>

	</div>

	<div class="tree well" ng-controller="TreeviewController as treeviewCtrl">
  		<collection collection='treeviewCtrl.array'></collection>
 	</div>
 	
</body>
</html>
