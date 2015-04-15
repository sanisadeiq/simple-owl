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
			template: "<li class='parent_li' ng-show='member.active'><span class='[[member.type]]' ng-click='onClick(member)'>[[member.title]]</span></li>",
			link: function (scope, element, attrs) {
				//if (angular.isArray(scope.member.subs)) {
					//element.append("<collection collection='member.subs'></collection>"); 
					//$compile(element.contents())(scope)
					$compile('<collection collection="member.subs"></collection>')(scope, function(cloned, scope){
						element.append(cloned); 
					});
				//}
                scope.onClick  = function (member) {
                    //alert('asdasd');
                    //console.log(member.subs);
                    for (var i = member.subs.length - 1; i >= 0; i--) {
                        member.subs[i].active = !member.subs[i].active;
                    };
                };
            }
        }
    })


app.controller('TreeviewController',['$http', function($http){
  var tree = this;
		//$scope.array = [];
		$http({method: 'GET', url: '/getInitialTree'}).
		success(function(data, status, headers, config) {
			alert('no error');
			tree.array = data['tree'];
            console.log(data);
			//$scope.array = data;
		}).
		error(function(data, status, headers, config) {
			alert('error: ' + JSON.stringify(config));
			//tree.array = data+status+headers+config;
			//console.log(data + status + headers + config);
			//$scope.array = data+status+headers+config;
		});
	}]);

})();