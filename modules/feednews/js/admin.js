/**
 * @Project FEEDNEWS 3.2.01
 * @Author MINHTC.NET (hunters49@gmail.com)
 * @Copyright (C) 2013 MINHTC.NET All rights reserved
 * @Createdate Sun, 28 Jul 2013 00:57:11 GMT
 */
function feedNews(){
	var check=false;
	$('.selected_ids').each(function(){
		if($(this).is(':checked')) check=true;
	});
	if(check){
		document.feedForm.submit();
	}else{
		alert('Hãy chọn ít nhất một mẫu cần lấy tin!');
	}
}
function deletePattern(){
	var check=false;
	$('.selected_ids').each(function(){
		if($(this).is(':checked')) check=true;
	});
	if(check){
		if(confirm('Bạn muốn xóa không?')){
			$('#cmd').val('delete');
			document.feedForm.submit();
		}
	}else{
		alert('Hãy chọn ít nhất một mẫu cần xóa!');
	}
}