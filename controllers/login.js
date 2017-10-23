var app = angular.module('login', []);

app.service('loginService', function($http, $window) {
	
	this.login = function(scope) {
		
		scope.views.incorrect = false;
		
		$http({
		  method: 'POST',
		  url: 'controllers/account.php?r=login',
		  data: scope.account,
		  headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function mySucces(response) {

			if (response.data['id'] == "0") {
				scope.views.incorrect = true;
			} else {					
				scope.views.incorrect = false;
				$window.location.href = 'index.php';
			}
			
		},
		function myError(response) {

		});
		
	}
	
});

app.controller('loginCtrl', function($scope, $http, $window, loginService) {

	$scope.views = {};
	
	$scope.account = {
		username: '', password: ''
	}		

	$scope.views.incorrect = false;
	
	$scope.login = function() {

		loginService.login($scope);
		
	}
	
});