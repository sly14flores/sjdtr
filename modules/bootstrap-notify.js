angular.module('bootstrap-notify',[]).service('bootstrapNotify', function() {

	this.show = function(type,msg) {
		
		$.notify({
			message: msg
		},{
			type: type
		});
		
	}

});