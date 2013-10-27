//参加或者感感兴趣
function userDoWish(eventid,status){
	$.ajax({
		type: "POST",
		url: siteUrl+"index.php?app=event&ac=do&ts=dowish",
		data: "eventid="+eventid+"&status="+status,
		beforeSend:function(){
		},
		success:function(result){
			if(result == '2'){
				window.location.reload(); 
			}
		}
	});
}

//取消参加活动
function cancelEvent(eventid,userid){
	$.ajax({
		type: "POST",
		url: siteUrl+"index.php?app=event&ac=do&ts=cancel",
		data: "eventid="+eventid+"&userid="+userid,
		beforeSend:function(){
		},
		success:function(result){
			if(result == '1'){
				window.location.reload(); 
			}
		}
	});
}

//参加活动
function doEvent(eventid,userid){
	$.ajax({
		type: "POST",
		url: siteUrl+"index.php?app=event&ac=do&ts=do",
		data: "eventid="+eventid+"&userid="+userid,
		beforeSend:function(){
		},
		success:function(result){
			if(result == '1'){
				window.location.reload(); 
			}
		}
	});
}