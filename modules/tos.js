angular.module('tos-module',['block-ui','bootstrap-modal','bootstrap-notify']).factory('tos',function($http,$compile,$timeout,blockUI,bootstrapModal,bootstrapNotify) {

	function tos() {

		var self = this;

		function validate(scope,form) {
			
			var controls = scope.frmHolder[form].$$controls;

			angular.forEach(controls,function(elem,i) {
				
				if (elem.$$attr.$attr.required) scope.$apply(function() { elem.$touched = elem.$invalid; });
									
			});

			return scope.frmHolder[form].$invalid;
			
		};		
		
		self.data = function(scope) {

			scope.pagination.tos = {};
			scope.pagination.currentPage.tos = 1;
			
			scope.to = {};
			scope.to.id = 0;
			scope.to.dates = {};
			scope.to.dates.data = [];
			scope.to.dates.dels = [];
			
			scope.tos = [];
			
			scope.filters.tos = {};
			scope.filters.tos.year = (new Date()).getFullYear();

		};
		
		self.list = function(scope) {

			scope.pagination.tos.currentPage = scope.pagination.currentPage.tos;
			scope.pagination.tos.pageSize = 10;
			scope.pagination.tos.maxSize = 3;
			
			$http({
			  method: 'POST',
			  url: 'handlers/travel-orders-list.php',
			  data: {employee_id: scope.generate.id, filter: scope.filters.tos}
			}).then(function mySucces(response) {

				angular.copy(response.data, scope.tos);
				scope.pagination.tos.filterData = scope.tos;

			}, function myError(response) {
				 
			  // error
				
			});	
			
		};
		
		self.to = function(scope,row) {			
			
			switch (row) {
				
				case null:
					
					scope.to = {};
					scope.to.id = 0;
					scope.to.dates = {};
					scope.to.dates.data = [];
					scope.to.dates.dels = [];					
					
					scope.to.employee_id = scope.generate.id;
					
				break;
				
				default:
				
					$http({
					  method: 'POST',
					  url: 'handlers/travel-order-view.php',
					  data: {id: row.id}
					}).then(function mySucces(response) {

						scope.to = angular.copy(response.data);
						angular.forEach(scope.to.dates.data, function(item,i) {
							
							scope.to.dates.data[i].to_date = new Date(item.to_date);
							
						});
						
					}, function myError(response) {
						 
					  // error
						
					});					
				
				break;
				
			};
			
			bootstrapModal.box(scope,'Travel Order','views/to.html',save);
			
		};
		
		function save(scope) {

			if (validate(scope,'to')) return false;
			
			$http({
			  method: 'POST',
			  url: 'handlers/travel-order-save.php',
			  data: scope.to
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
				  url: 'handlers/travel-order-delete.php',
				  data: {id: [row.id]}
				}).then(function mySucces(response) {

					self.list(scope);
					
				}, function myError(response) {
					 
				  // error
					
				});

			};

			bootstrapModal.confirm(scope,'Confirmation','Are you sure you want to delete this travel order?',onOk,function() {});

		};
		
		self.to_dates = {
			
			add: function(scope) {
				
				scope.to.dates.data.push({id:0, to_date: new Date(), to_duration: "Wholeday", disabled: false});				
				
			},
			
			edit: function(scope,to_date) {

				var index = scope.to.dates.data.indexOf(to_date);
				scope.to.dates.data[index].disabled = !scope.to.dates.data[index].disabled;
				
			},
			
			del: function(scope,to_date) {

				if (to_date.id > 0) {
					scope.to.dates.dels.push(to_date.id);
				};

				var to_dates = scope.to.dates.data;
				var index = scope.to.dates.data.indexOf(to_date);
				scope.to.dates.data = [];

				angular.forEach(to_dates, function(d,i) {
					
					if (index != i) {
						
						delete d['$$hashKey'];
						scope.to.dates.data.push(d);
						
					};
					
				});		

			}
			
		};
		
	};

	return new tos();

});