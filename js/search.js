$(function(){
	$("#searchBarMainInput").val("");

	$("#searchBarMainButton").click(function(evt){
		jQuery.ajax({
			type: "GET",
			url: "http://192.241.169.33/APISearch/inc/processRequest.php?q="+$("#searchBarMainInput").val(),
			success: function(data){
				$("#status").html(data);
				console.log(data);
			}
		});
	});
});