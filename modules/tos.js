angular.module('tos-module',['block-ui','bootstrap-modal','bootstrap-notify']).factory('tos',function($http,$compile,$timeout,blockUI,bootstrapModal,bootstrapNotify) {

	function tos() {

		var self = this;

		self.data = function(scope) {

			scope.pagination.tos = {};
			scope.pagination.currentPage.tos = 1;
			
			scope.to = {};
			scope.to.id = {};
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
			

			
		};
		
		self.to = function(scope,row) {
			
			scope.to = {};
			scope.to.id = {};
			scope.to.dates = {};
			scope.to.dates.data = [];
			scope.to.dates.dels = [];
			
			switch (row) {
				
				case null:

				break;
				
				default:
				
				break;
				
			};
			
			bootstrapModal.box(scope,'Travel Order','views/to.html',function() { });
			
		};
		
	};

	return new tos();

});