angular.module('bootstrap-modal',[]).service('bootstrapModal', function($compile,$timeout) {

	this.confirm = function(scope,title,content,onOk,onCancel) {
		
		var dialog = bootbox.confirm({
			title: title,
			message: content,
			callback: function (result) {
				if (result) {
					onOk(scope);
				} else {
					onCancel();
				}
			}
		});		
		
	};
	
	this.notify = function(scope,content,onOk) {

		var dialog = bootbox.alert({
			title: 'Notification',
			message: content,
			callback: function () {
				onOk();
			}
		});		
	
	};
	
	this.box = function(scope,title,content,onOk) {

		var dialog = bootbox.confirm({
			title: title,
			message: 'Loading content...',
			buttons: {
				confirm: {
					label: 'Save',
					className: 'btn-success'
				},				
				cancel: {
					label: 'Cancel',
					className: 'btn-danger'
				}
			},			
			callback: function (result) {
				if (result) {
					return onOk(scope);
				}
			}
		});

		$timeout(function() { dialog.find('.bootbox-body').load(content, function() {
			$compile($('.bootbox-body')[0])(scope);
		}); }, 1000);
	
	};
	
	this.box2 = function(scope,title,content,onOk) {

		var dialog = bootbox.alert({
			title: title,
			message: 'Loading...',
			buttons: {			
				ok: {
					label: 'Close',
					className: 'btn-default'
				}
			},			
			callback: function (result) {

				if (result) {				

				} else {
					return onOk(scope);					
				};

			}
		});

		dialog.init(function() {
			dialog.find('.bootbox-body').load(content);
			$('.modal').css({"width": "60%","left": "40%"});
			$timeout(function() {
				$compile($('.bootbox-body')[0])(scope);
			}, 500);
		});

	};	

});