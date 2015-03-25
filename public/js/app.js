(function(){
	var app = angular.module('owl', [])
	.config(function($interpolateProvider) {
		$interpolateProvider.startSymbol('[[').endSymbol(']]');
	});

	app.directive('collection', function () {
		return {
			restrict: "E",
			replace: true,
			scope: {
				collection: '='
			},
			template: "<ul><member ng-repeat='member in collection' member='member'></member></ul>"
		}
	})

	app.directive('member', function ($compile) {
		return {
			restrict: "E",
			replace: true,
			scope: {
				member: '='
			},
			template: "<li>[[member.title]]</li>",
			link: function (scope, element, attrs) {
				if (angular.isArray(scope.member.children)) {
					element.append("<collection collection='member.children'></collection>"); 
					$compile(element.contents())(scope)
				}
			}
		}
	})


	app.controller('TreeviewController',['$http', function($http){
		var tree = this;
		//$scope.array = [];
		$http({method: 'GET', url: '/getInitialTree'}).
		success(function(data, status, headers, config) {
			alert('no error');
			tree.array = data;
			//$scope.array = data;
		}).
		error(function(data, status, headers, config) {
			alert('error');
			tree.array = data+status+headers+config;
			//$scope.array = data+status+headers+config;
		});
	}]);
	
})();
