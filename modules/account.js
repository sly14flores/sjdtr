angular.module('account',['bootstrap-modal']).directive('logout', function($window,bootstrapModal) {

	return {
	    restrict: 'A',
	    link: function(scope, element, attrs) {
		
			element.bind('click', function() {
					
				bootstrapModal.confirm(scope,'Are you sure you want to logout?','logout()');
					
			});

			scope.logout = function() {
				
				$window.location.href = 'modules/logout.php';
				
			};
		
	    }
	};
	
});