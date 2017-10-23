var app = angular.module('schedules', ['angularUtils.directives.dirPagination','block-ui','bootstrap-modal','bootstrap-notify','account']);

app.factory('appService',function($http,$timeout,bootstrapNotify,bootstrapModal,blockUI) {
	
	function appService() {
		
		var self = this;
		
		this.controls = function(scope,opt) {
			
			scope.controls.schedule = {
				description: opt,
				morning_in: opt,
				morning_cutoff: opt,
				morning_out: opt,
				lunch_break_cutoff: opt,
				afternoon_in: opt,
				afternoon_cutoff: opt,
				afternoon_out: opt,
				saveBtn: opt,
				cancelBtn: opt,
				editBtn: opt,
				delBtn: opt
			};
			
		};
		
		this.start = function(scope) {

			$http({
			  method: 'POST',
			  url: 'controllers/schedules.php?r=start'
			}).then(function mySucces(response) {

				scope.schedules = response.data;
				
			}, function myError(response) {
				 
			  // error
				
			});
			
		};		
		
		this.required = [];
		
		this.onAdd = function(scope) {
			self.controls(scope,false);
			scope.controls.schedule.addBtn = true;
			scope.controls.schedule.editBtn = true;
			scope.controls.schedule.delBtn = true;
			scope.views.addUpdateTxt = 'Save';
			scope.views.cancelCloseTxt = 'Cancel';			
		};
		
		this.onUpdate = function(scope) {
			self.controls(scope,true);
			scope.controls.schedule.addBtn = false;
			scope.controls.schedule.editBtn = true;
			scope.controls.schedule.delBtn = true;		
		};
		
		this.onCancel = function(scope) {
			self.controls(scope,true);
			scope.controls.schedule.addBtn = false;
			scope.controls.schedule.editBtn = true;
			scope.controls.schedule.delBtn = true;	
		};
		
		this.onView = function(scope) {
			self.controls(scope,true);
			scope.controls.schedule.addBtn = false;
			scope.controls.schedule.editBtn = false;
			scope.controls.schedule.delBtn = false;
			scope.views.addUpdateTxt = 'Update';
			scope.views.cancelCloseTxt = 'Close';			
		};
		
		this.onEdit = function(scope) {
			self.controls(scope,false);
			scope.controls.schedule.addBtn = true;
			scope.controls.schedule.editBtn = true;
			scope.controls.schedule.delBtn = false;
		};
		
		this.onClose = function(scope) {
			self.controls(scope,true);
			scope.controls.schedule.addBtn = false;
			scope.controls.schedule.editBtn = false;
			scope.controls.schedule.delBtn = false;		
		};
		
		this.add = function(scope) {
			
			self.onAdd(scope);
			
			// insert new schedule
			
			$http({
			  method: 'POST',
			  url: 'controllers/schedules.php?r=new',
			  data: scope.schedule
			}).then(function mySucces(response) {
			
				angular.copy(scope.schedule_struct,scope.schedule);
				scope.schedule.id = response.data;			
				
			}, function myError(response) {
				 
			  // error
				
			});				
			
		};
		
		this.update = function(scope) {
			
			if (scope.frmHolder.schedule.$invalid) {
				scope.frmHolder.schedule.description.$touched = true;
				bootstrapNotify.show('danger','Please fill up description');			
				return;
			}
			
			self.onUpdate(scope);
			
			$http({
			  method: 'POST',
			  url: 'controllers/schedules.php?r=update',
			  data: scope.schedule
			}).then(function mySucces(response) {

				self.start(scope);
				if (scope.views.addUpdateTxt == 'Save') {
					angular.copy(scope.schedule_struct,scope.schedule);
					scope.frmHolder.schedule.description.$touched = false;							
				} else {
					scope.views.onEdit = false;
				}
				
			}, function myError(response) {
				 
			  // error
				
			});				
			
		};
		
		this.cancel = function(scope) {
			
			if (scope.views.cancelCloseTxt == 'Close') {
				self.onClose(scope);
				scope.views.onEdit = false;
				return;
			}
			
			$http({
			  method: 'POST',
			  url: 'controllers/schedules.php?r=cancel',
			  data: {id: [scope.schedule.id]}
			}).then(function mySucces(response) {
				
				angular.copy(scope.schedule_struct,scope.schedule);
				
				self.onCancel(scope);
				self.start(scope);
				scope.frmHolder.schedule.description.$touched = false;
				
			}, function myError(response) {
				 
			  // error
				
			});			
			
		};
		
		this.view = function(scope) {
			
			if (scope.views.onEdit) {
				bootstrapNotify.show('danger','Please close the schedule currently being edited to view another schedule');
				return;
			}
			
			blockUI.show();
			
			self.onView(scope);
			
			$http({
			  method: 'POST',
			  url: 'controllers/schedules.php?r=view',
			  data: {id: scope.schedule_row.id}
			}).then(function mySucces(response) {
				
				angular.forEach(response.data.details,function(item,i) {
					response.data.details[i]['morning_in'] = new Date("2000-01-01 "+item['morning_in']);
					response.data.details[i]['morning_cutoff'] = new Date("2000-01-01 "+item['morning_cutoff']);
					response.data.details[i]['morning_out'] = new Date("2000-01-01 "+item['morning_out']);
					response.data.details[i]['lunch_break_cutoff'] = new Date("2000-01-01 "+item['lunch_break_cutoff']);
					response.data.details[i]['afternoon_in'] = new Date("2000-01-01 "+item['afternoon_in']);
					response.data.details[i]['afternoon_cutoff'] = new Date("2000-01-01 "+item['afternoon_cutoff']);
					response.data.details[i]['afternoon_out'] = new Date("2000-01-01 "+item['afternoon_out']);
				});
						
				$timeout(function() {
					angular.copy(response.data, scope.schedule);
					blockUI.hide();					
				},500);				
				
			}, function myError(response) {
				 
			  // error
				
			});			
			
		};
		
		this.edit = function(scope) {
			
			self.onEdit(scope);
			scope.views.onEdit = true;
			
		};
		
		this.confirmDel = function(scope) {

			bootstrapModal.confirm(scope,'Are you sure want to delete this schedule?','appService.del(this)');
			
		};
		
		this.del = function(scope) {
			
			bootstrapModal.closeConfirm();
			
			$http({
			  method: 'POST',
			  url: 'controllers/schedules.php?r=cancel',
			  data: {id: [scope.schedule.id]}
			}).then(function mySucces(response) {
				
				self.onCancel(scope);
				self.start(scope);
				angular.copy(scope.schedule_struct,scope.schedule);				
				scope.frmHolder.schedule.description.$touched = false;			

			}, function myError(response) {
				 
			  // error
				
			});
			
		};

		this.clone = function(scope) {
			console.log(scope);
			scope.detail.morning_in = scope.schedule.details[0]['morning_in'];
			scope.detail.morning_cutoff = scope.schedule.details[0]['morning_cutoff']; 
			scope.detail.morning_out = scope.schedule.details[0]['morning_out']; 
			scope.detail.lunch_break_cutoff = scope.schedule.details[0]['lunch_break_cutoff']; 
			scope.detail.afternoon_in = scope.schedule.details[0]['afternoon_in']; 
			scope.detail.afternoon_cutoff = scope.schedule.details[0]['afternoon_cutoff']; 
			scope.detail.afternoon_out = scope.schedule.details[0]['afternoon_out']; 
			
		};
		
	};
	
	return new appService();
	
});

app.controller('schedulesCtrl',function($scope,appService) {
	
$scope.currentPage = 1;
$scope.pageSize = 10;

$scope.views = {};
$scope.frmHolder = {};

$scope.controls = {};
$scope.controls.schedule = {};

$scope.views.onEdit = false;

$scope.schedule_struct = {
	id: 0,
	description: "",
	details: [
		{id: 0, day: "Monday", morning_in: new Date("0"), morning_cutoff: new Date("0"), morning_out: new Date("0"), lunch_break_cutoff: new Date("0"), afternoon_in: new Date("0"), afternoon_cutoff: new Date("0"), afternoon_out: new Date("0")},
		{id: 0, day: "Tuesday", morning_in: new Date("0"), morning_cutoff: new Date("0"), morning_out: new Date("0"), lunch_break_cutoff: new Date("0"), afternoon_in: new Date("0"), afternoon_cutoff: new Date("0"), afternoon_out: new Date("0")},
		{id: 0, day: "Wednesday", morning_in: new Date("0"), morning_cutoff: new Date("0"), morning_out: new Date("0"), lunch_break_cutoff: new Date("0"), afternoon_in: new Date("0"), afternoon_cutoff: new Date("0"), afternoon_out: new Date("0")},
		{id: 0, day: "Thursday", morning_in: new Date("0"), morning_cutoff: new Date("0"), morning_out: new Date("0"), lunch_break_cutoff: new Date("0"), afternoon_in: new Date("0"), afternoon_cutoff: new Date("0"), afternoon_out: new Date("0")},
		{id: 0, day: "Friday", morning_in: new Date("0"), morning_cutoff: new Date("0"), morning_out: new Date("0"), lunch_break_cutoff: new Date("0"), afternoon_in: new Date("0"), afternoon_cutoff: new Date("0"), afternoon_out: new Date("0")},
		{id: 0, day: "Saturday", morning_in: new Date("0"), morning_cutoff: new Date("0"), morning_out: new Date("0"), lunch_break_cutoff: new Date("0"), afternoon_in: new Date("0"), afternoon_cutoff: new Date("0"), afternoon_out: new Date("0")},
		{id: 0, day: "Sunday", morning_in: new Date("0"), morning_cutoff: new Date("0"), morning_out: new Date("0"), lunch_break_cutoff: new Date("0"), afternoon_in: new Date("0"), afternoon_cutoff: new Date("0"), afternoon_out: new Date("0")}
	]
};

$scope.schedule = {};
angular.copy($scope.schedule_struct,$scope.schedule);

$scope.views.addUpdateTxt = "Save";
$scope.views.cancelCloseTxt = "Cancel";

$scope.controls.schedule.addBtn = false;

var appServiceL = appService;
$scope.appService = appService;

appServiceL.controls($scope,true);
appServiceL.start($scope);
	
});