var app = angular.module('dashboard', ['block-ui','bootstrap-modal','bootstrap-notify','account']);

app.factory('appService', function(consoleMsg,$http,$compile,$timeout,fileUpload,blockUI) {
	
	function appService() {
		
		var self = this;
		
		self.howToImport = function(scope) {

			scope.views.showPreUploadedOpt = false;
			scope.views.showUploadOpt = false;
		
			scope.views.importProgressDetail = '';
		
			if (scope.views.howToImport == 'preuploaded') {
			
				scope.views.showPreUploadedOpt = true;
				consoleMsg.show(300,'Import logs from pre-uploaded file selected','r');
				consoleMsg.show(300,'Please make sure that the latest log file(s) has been pre-uploaded','a');

			} else {

				scope.views.showUploadOpt = true;
				consoleMsg.show(300,'Upload log file selected','r');			
				
			}

		}

		self.start = function(scope) {
			
			/*
			** validate dates filter
			*/
			
			if ( (scope.filter.dateFrom == undefined) || (scope.filter.dateTo == undefined) ) {
				
				consoleMsg.show(400,'No Dates selected to be imported','r');
				return;
			
			}
			
			if (scope.filter.dateFrom.getTime() > scope.filter.dateTo.getTime()) {

				consoleMsg.show(400,"Invalid dates, 'To' must be greater than date 'From'",'r');			
				return;
				
			}
			
			scope.views.started = true;
			blockUI.show("Please wait...");
			
			switch (scope.views.howToImport) {
				
				case "preuploaded":
				
					if (scope.views.prefile == undefined) {
						consoleMsg.show(400,'No file selected','a');
						return;
					}
					
					scope.views.opt = scope.views.prefile;
					
					// check if file exists
					$http({
					  method: 'POST',
					  url: 'controllers/dashboard.php?r=check_log_files_existence',
					  data: {prefile: scope.views.prefile}
					}).then(function mySucces(response) {
						
						consoleMsg.show(response.data[0],response.data[1],response.data[2]);
						if (response.data[0] == 300) {
							self.collectLogs(scope);
						}
						
					}, function myError(response) {
						 
					  // error
						
					});			
					
				break;
				
				case "upload":

					if (scope.views.usePreviousFile) { // use latest uploaded file						
						
						if ((scope.views.pf == undefined) || (scope.views.pf == '')) {
							consoleMsg.show(400,'No previously added file exists','a');
							return;
						}
						
						scope.views.opt = scope.views.pf;
						
						consoleMsg.show(300,'Using previously added file ({{views.pf}})','a');					
						$compile($('.console')[0])(scope);

						// check file existence
						$http({
						  method: 'POST',
						  url: 'controllers/dashboard.php?r=check_log_file_existence',
						  data: {pf: scope.views.pf}
						}).then(function mySucces(response) {
							
							consoleMsg.show(response.data[0],response.data[1],response.data[2]);
							if (response.data[0] == 300) {
								self.collectLogs(scope);
							}
							
						}, function myError(response) {
							 
						  // error
							
						});
					
						
					} else {

						var file = scope.views.logFile;				
						if (file == undefined) {
							consoleMsg.show(400,'No file selected','a');
							return;
						}						
						
						if (scope.views.recursiveUpload) {
							consoleMsg.show(300,'Upload log file selected','r');
						}
						
						consoleMsg.show(300,'Uploading {{views.logFilename}} ({{views.progress}}%)','a');
						$compile($('.console')[0])(scope);

						var fn = file['name'];
						var en = fn.substring(fn.indexOf("."),fn.length);
						
						scope.views.logFilename = fn;
						
						scope.views.opt = fn;
						
						var uploadUrl = "controllers/dashboard.php?r=upload_log&fn="+fn;
						fileUpload.uploadFileToUrl(file, uploadUrl, scope);

					}
				
				break;
				
				default:
				
					consoleMsg.show(300,"Please select in 'Select how to import'",'r');
				
				break;
				
			}
			
		}
		
		self.chkPf = function(chk) {
			
			if (chk) consoleMsg.show(300,'Toggled use previously uploaded file on','r');
			else consoleMsg.show(300,'Toggled use previously uploaded file off','r');
			
		}
		
		self.collectLogs = function(scope) {
			
			blockUI.hide();
			
			$timeout(function() {

				consoleMsg.show(300,'Collecting employees logs...','a');
				
			},500);
			
			$http({
			  method: 'POST',
			  url: 'controllers/dashboard.php?r=collect_logs',
			  data: {how: scope.views.howToImport, opt: scope.views.opt, filter: scope.filter}
			}).then(function mySucces(response) { 

				if (response.data[0][0] == 400) {
					consoleMsg.show(response.data[0][0],response.data[0][1],response.data[0][2]);
				} else {
					consoleMsg.show(response.data[0][0],response.data[0][1],response.data[0][2]);					
					scope.views.importProgressDetail = '';
					self.putLogs(scope,response.data[1]['logs']);
				}
				
			}, function myError(response) {
				 
			  // error
				
			});

		}
		
		self.putLogs = function(scope,logs) {
			
			var i = 0;
			var logsCount = logs.length - 1;
			if (logsCount < 0) {
				consoleMsg.show(300,'No logs found','a');
				return;
			}
			putLog(logs[i]);
			
			function putLog(log) {
				
				$http({
				  method: 'POST',
				  url: 'controllers/dashboard.php?r=put_log',
				  data: log
				}).then(function mySucces(response) {
					
					if ((response.data[0] == 200) || (response.data[0] == 300)) {
						consoleMsg.show(response.data[0],response.data[1],response.data[2]);
						
						var logsLeft = logsCount - i;
						var progress = Math.round((i*100)/logsCount);						
						var eta = formatSeconds(logsLeft);
		
						scope.views.importProgressDetail = ' - Processed '+ i + ' of ' + logsCount + ' ('+progress+'%), estimated time remaining: '+eta;
						
						if (i == logsCount) {
							consoleMsg.show(300,'All logs were successfully imported','a');
							scope.views.started = false;
						}
						
						if (i < logsCount) {
							i++;
							putLog(logs[i]);
						}
					} else {
						consoleMsg.show(400,'Something went wrong, importing halted','a');
					}
				
				}, function myError(response) {
					 
				  // error
					
				});
				
			}
			
			function formatSeconds(seconds) {
				
				var date = new Date(1970,0,1);
				date.setSeconds(seconds);
				return date.toTimeString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, "$1");
				
			}			
			
		}

	};
	
	return new appService();	
	
});

app.directive('fileModel', ['$parse', function ($parse) {
	return {
	   restrict: 'A',
	   link: function(scope, element, attrs) {
		  var model = $parse(attrs.fileModel);
		  var modelSetter = model.assign;
		  
		  element.bind('change', function(){
			  
			scope.$apply(function(){
				modelSetter(scope, element[0].files[0]);
			});
			 
		  });

	   }
	};
}]);

app.service('fileUpload', function (consoleMsg) {
	
	this.uploadFileToUrl = function(file, uploadUrl, scope) {
		
	   var fd = new FormData();
	   fd.append('file', file);
	
        var xhr = new XMLHttpRequest();
        xhr.upload.addEventListener("progress", uploadProgress, false);
        xhr.addEventListener("load", uploadComplete, false);
        xhr.open("POST", uploadUrl)
        xhr.send(fd);

		// upload progress
		function uploadProgress(evt) {
			scope.$apply(function(){
				scope.views.progress = 0;				
				if (evt.lengthComputable) {
					scope.views.progress = Math.round(evt.loaded * 100 / evt.total);
				} else {
					scope.views.progress = 'unable to compute';
				}
			});
		}

		function uploadComplete(evt) {
			/* This event is raised when the server send back a response */
			scope.$apply(function(){
				consoleMsg.show(200,scope.views.logFilename+' successfully uploaded','a');
				scope.views.pf = scope.views.logFilename;			
			});			
			$('#logFile').val(null);
			scope.views.logFile = null;
			scope.views.recursiveUpload = true;
			localStorage.pf = scope.views.logFilename;
			scope.appService.collectLogs(scope);
		}

	}
	
});

app.service('consoleMsg', function($timeout) {
	
	this.show = function(code,msg,opt = 'a') {
		
		var codeClass = 'success-response';
		if (code == 300) codeClass = 'info-response';
		else if (code == 400) codeClass = 'error-response';
		
		if (opt == 'a') $('.console').append('<span class="'+codeClass+'">'+msg+'</span>');
		else $('.console').html('<span class="'+codeClass+'">'+msg+'</span>');
		
		$('.console').scrollTop(($('.console')[0]).scrollHeight);		
		
	}
	
});

app.controller('dashboardCtrl', function($scope,blockUI,bootstrapModal,bootstrapNotify,fileUpload,consoleMsg,appService) {

$scope.views = {};
$scope.frmHolder = {};
$scope.filter = {};

$scope.views.errorBox = false;
$scope.views.errorMsg = '';

$scope.views.progress = 0;

$scope.views.showPreUploadedOpt = false;
$scope.views.showUploadOpt = false;
$scope.views.usePreviousFile = false;
$scope.views.recursiveUpload = false;

$scope.views.importProgressDetail = '';

$scope.appService = appService;
$scope.views.started = false;

$scope.views.pf = '';
if (localStorage.pf !== undefined) $scope.views.pf = localStorage.pf;

// r = new line, a = append
// consoleMsg.show(200,'Lorem Ipsum...','a'); // success
// consoleMsg.show(300,'Lorem Ipsum...','a'); // info
// consoleMsg.show(400,'Lorem Ipsum...','a'); // error
	
});