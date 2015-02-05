$(document).ready(function() {
	/*输入框*/
	$(function(){
		$('input').focus(function(){
			$(this).css('border','1px solid #F89406	');
		});
		$('input').blur(function(){
			$(this).css('border','1px solid #84BFEA');
		});
	});

});