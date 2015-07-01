/**
 * @Project FEEDNEWS 3.2.01
 * @Author FORUM.NUKEVIET.VN

 * @Created Wed, 01 Jul 2015 18:00:00 GMT
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