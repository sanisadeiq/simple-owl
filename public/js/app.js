(function(){
	var app = angular.module('owl', [])
	.config(function($interpolateProvider) {
		$interpolateProvider.startSymbol('[[').endSymbol(']]');
	});

/*myApp.directive('multiselectDropdown', [function() {
    return function(scope, element, attributes) {
        
        element = $(element[0]); // Get the element as a jQuery element
        
        // Below setup the dropdown:
        
        element.multiselect({
            buttonClass : 'btn btn-small',
            buttonWidth : '200px',
            buttonContainer : '<div class="btn-group" />',
            maxHeight : 200,
            enableFiltering : true,
            enableCaseInsensitiveFiltering: true,
            buttonText : function(options) {
                if (options.length == 0) {
                    return element.data()['placeholder'] + ' <b class="caret"></b>';
                } else if (options.length > 1) {
                    return _.first(options).text 
                    + ' + ' + (options.length - 1)
                    + ' more selected <b class="caret"></b>';
                } else {
                    return _.first(options).text
                    + ' <b class="caret"></b>';
                }
            },
            // Replicate the native functionality on the elements so
            // that angular can handle the changes for us.
            onChange: function (optionElement, checked) {
                optionElement.removeAttr('selected');
                if (checked) {
                    optionElement.attr('selected', 'selected');
                }
                element.change();
            }
            
        });
        // Watch for any changes to the length of our select element
        scope.$watch(function () {
            return element[0].length;
        }, function () {
            element.multiselect('rebuild');
        });
        
        // Watch for any changes from outside the directive and refresh
        scope.$watch(attributes.ngModel, function () {
            element.multiselect('refresh');
        });
        
        // Below maybe some additional setup
    }
}]);*/



	app.directive('multiselect', function () {
    return function (scope, element, attrs) {

    	/*element = $(element[0]); // Get the element as a jQuery element*/

            $('#classes, #individuals, #relationships, #operators').multiselect({
                checkboxName: 'multiselect[]',
				disableIfEmpty: true,
				maxHeight: 250,
				enableFiltering: true,
				enableCaseInsensitiveFiltering: true,
				buttonWidth: '200px',
				nonSelectedText: '(none selected)',
				allSelectedText: '(all selected)',
				onChange: function (optionElement, checked) {
                optionElement.removeAttr('selected');
                if (checked) {
                    optionElement.attr('selected', 'selected');
                }
                element.change();
            }
            });

            scope.$watch(scope.arrayClasses, function() {
            	var array = scope.arrayClasses;
            	var array2 = [];
            	for (var i = 0; i < array.length ; i++) {
            		array2.push("{label: 'inverse', title: 'inverse', value: 'inverse'}");
            	}

            	$('#classes').multiselect('dataprovider', array2);
            });

            // Watch for any changes to the length of our select element
            scope.$watch(function () {
                return element[0].length;
            }, function () {
                element.multiselect('rebuild');
            });

            // Watch for any changes from outside the directive and refresh
            scope.$watch(attrs.ngModel, function () {
                element.multiselect('refresh');
            });

        }

    };
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
                    //alert(String(member.class));
                    for (var i = member.subs.length - 1; i >= 0; i--) {
                        member.subs[i].active = !member.subs[i].active;
                    };
                };
            }
            
        }
    })


app.controller('TreeviewController',['$scope', '$http', function($scope, $http){
  var tree = this;
		$http({method: 'GET', url: '/getInitialTree'}).
		success(function(data, status, headers, config) {
			alert('no error');
			tree.array = data['tree'];
			$scope.arrayClasses = data['classes'];
            console.log(data);
		}).
		error(function(data, status, headers, config) {
			alert('error: ' + JSON.stringify(config));
			//tree.array = data+status+headers+config;
			//console.log(data + status + headers + config);
			//$scope.array = data+status+headers+config;
		});
	}]);




})();