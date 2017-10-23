angular.module('block-ui',[]).service('blockUI',function($timeout,$compile,$http) {
	
	this.show = function(msg = 'Please wait...') {

		$.blockUI({
			message: '<span style="font-size: 12px;">'+msg+'</span>',
			css: {
			border: 'none',
			padding: '15px',
			backgroundColor: '#000',
			'-webkit-border-radius': '10px',
			'-moz-border-radius': '10px',
			opacity: .5,
			color: '#fff'
			}
		});

	}

	this.hide = function() {

		$.unblockUI();

	}
	
});