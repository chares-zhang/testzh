var mobliledetailViewModel = function() {
	var self = this;
};

$.get(
	baseUrl+'/tools/mobiledetail/getTbItems',
	function (data) {
		alert(data);
	}
			
);