(function(){
	var app = angular.module('owl', [])
	.config(function($interpolateProvider) {
		$interpolateProvider.startSymbol('[[').endSymbol(']]');
	});

	app.controller('TreeviewController',['$http', function($http){
		var tree = this;
		$http({method: 'GET', url: '/getInitialTree'}).
		success(function(data, status, headers, config) {
			alert('no error');
			//$scope.output = data[0] + data[1];
			tree.output = data[0] + data[1];
		}).
		error(function(data, status, headers, config) {
			alert('error');
			tree.output = data+status+headers+config;
		});
	}]);
	
})();
