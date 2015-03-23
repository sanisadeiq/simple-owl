(function(){
	var app = angular.module('owl', [])
	.config(function($interpolateProvider) {
		$interpolateProvider.startSymbol('[[').endSymbol(']]');
	});

	app.controller('TreeviewController', function($scope, $http){
		$http({method: 'GET', url: 'getInitialTree'}).
		success(function(data, status, headers, config) {
			alert('no error');
			$scope.output = data;
		}).
		error(function(data, status, headers, config) {
			alert('error');
			$scope.output = data 
			+status  
			+headers   
			+config;
		});
	});

/*
	$scope.output = "asd";
	function TreeviewController($scope, $http){
		$http({method: 'GET', url: 'getInitialTree'}).
		success(function(data, status, headers, config) {
			$scope.output = data;
		}).
		error(function(data, status, headers, config) {
			alert("hello");
			$scope.output = data;
		});
	}
*/
})();
