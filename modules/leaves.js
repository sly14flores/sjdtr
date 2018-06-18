angular.module('leaves-module',['block-ui','bootstrap-modal','bootstrap-notify']).factory('leaves',function($http,$compile,$timeout,blockUI,bootstrapModal,bootstrapNotify) {

	function leaves() {

		var self = this;

		function validate(scope,form) {
			
			var controls = scope.frmHolder[form].$$controls;

			angular.forEach(controls,function(elem,i) {
				
				if (elem.$$attr.$attr.required) scope.$apply(function() { elem.$touched = elem.$invalid; });
									
			});

			return scope.frmHolder[form].$invalid;
			
		};		
		
		self.data = function(scope) {

			scope.pagination.leaves = {};
			scope.pagination.currentPage.leaves = 1;
			
			scope.leave = {};
			scope.leave.id = 0;
			scope.leave.dates = {};
			scope.leave.dates.data = [];
			scope.leave.dates.dels = [];
			
			scope.leaves = [];
			
			scope.filters.leaves = {};
			scope.filters.leaves.year = (new Date()).getFullYear();
			
			$http({
			  method: 'GET',
			  url: 'handlers/leave-types.php'
			}).then(function mySucces(response) {

				scope.leave_types = response.data;

			}, function myError(response) {
				 
			  // error
				
			});			

		};
		
		self.list = function(scope) {

			scope.pagination.leaves.currentPage = scope.pagination.currentPage.leaves;
			scope.pagination.leaves.pageSize = 10;
			scope.pagination.leaves.maxSize = 3;
			
			$http({
			  method: 'POST',
			  url: 'handlers/leaves-list.php',
			  data: {employee_id: scope.generate.id, filter: scope.filters.leaves}
			}).then(function mySucces(response) {

				angular.copy(response.data, scope.leaves);
				scope.pagination.leaves.filterData = scope.leaves;

			}, function myError(response) {
				 
			  // error
				
			});	
			
		};
		
		self.leave = function(scope,row) {			
			
			switch (row) {
				
				case null:
					
					scope.leave = {};
					scope.leave.id = 0;
					scope.leave.dates = {};
					scope.leave.dates.data = [];
					scope.leave.dates.dels = [];					
					
					scope.leave.employee_id = scope.generate.id;
					
				break;
				
				default:
				
					$http({
					  method: 'POST',
					  url: 'handlers/leave-view.php',
					  data: {id: row.id}
					}).then(function mySucces(response) {

						scope.leave = angular.copy(response.data);
						angular.forEach(scope.leave.dates.data, function(item,i) {
							
							scope.leave.dates.data[i].leave_date = new Date(item.leave_date);
							
						});
						
					}, function myError(response) {
						 
					  // error
						
					});					
				
				break;
				
			};
			
			bootstrapModal.box(scope,'Leave','views/leave.html',save);
			
		};
		
		function save(scope) {

			if (validate(scope,'leave')) return false;
			
			$http({
			  method: 'POST',
			  url: 'handlers/leave-save.php',
			  data: scope.leave
			}).then(function mySucces(response) {

				self.list(scope);
				
			}, function myError(response) {
				 
			  // error
				
			});
			
			return true;			
			
		};
		
		self.del = function(scope,row) {
			
			var onOk = function() {		
				
				$http({
				  method: 'POST',
				  url: 'handlers/leave-delete.php',
				  data: {id: [row.id]}
				}).then(function mySucces(response) {

					self.list(scope);
					
				}, function myError(response) {
					 
				  // error
					
				});

			};

			bootstrapModal.confirm(scope,'Confirmation','Are you sure you want to delete this leave?',onOk,function() {});

		};
		
		self.leave_dates = {
			
			add: function(scope) {
				
				scope.leave.dates.data.push({id:0, leave_date: new Date(), leave_duration: "Wholeday", disabled: false});				
				
			},
			
			edit: function(scope,leave_date) {

				var index = scope.leave.dates.data.indexOf(leave_date);
				scope.leave.dates.data[index].disabled = !scope.leave.dates.data[index].disabled;
				
			},
			
			del: function(scope,leave_date) {

				if (leave_date.id > 0) {
					scope.leave.dates.dels.push(leave_date.id);
				};

				var leave_dates = scope.leave.dates.data;
				var index = scope.leave.dates.data.indexOf(leave_date);
				scope.leave.dates.data = [];

				angular.forEach(leave_dates, function(d,i) {
					
					if (index != i) {
						
						delete d['$$hashKey'];
						scope.leave.dates.data.push(d);
						
					};
					
				});		

			}
			
		};
		
	};

	return new leaves();

});