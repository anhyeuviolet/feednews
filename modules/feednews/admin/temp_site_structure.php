<?php

/**
 * @Project FEEDNEWS 3.0.01
 * @Author MINHTC.NET (hunters49@gmail.com)
 * @Copyright (C) 2013 MINHTC.NET All rights reserved
 * @Createdate Sun, 28 Jul 2013 00:57:11 GMT
 */

if ( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$xtpl = new XTemplate( $op . ".tpl", NV_ROOTDIR . "/themes/" . $global_config['module_theme'] . "/modules/" . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );
$xtpl->parse( 'main' );

$__site=NV_PREFIXLANG . "_" . $module_name . "_site";
$__site_structure=NV_PREFIXLANG . "_" . $module_name . "_site_structure";

$id=$nv_Request ->get_int('id','post,get');

$query = "SELECT * FROM ".$__site." WHERE id=".$id;
$edit_id=$db->query( $query );
$item=$edit_id->fetch();

$page_title = $lang_module['temp_site_structure']." - ".$item['name'];
$error="";

if($item){
	//$site_structure=$db->sql_fetch_assoc ( $temp_id );
	//debug($site_structure);
	$cmd = $nv_Request->isset_request( 'cmd', 'post' );
	if($cmd){
		// submit
		//debug($_REQUEST);
		if($structure=$_REQUEST['field']){
			// xóa toàn bộ site_structure có site_id=$edit_id
			$query="DELETE FROM ".$__site_structure." WHERE site_id=".$item['id'];
			$db->query( $query );
			// lưu dữ liệu
			foreach($structure as $key=>$value){
				if($value['extra']){
					$query="INSERT INTO ".$__site_structure." (id, site_id, field_name, extra, element_delete, string_delete) VALUES (NULL, '".$item['id']."','".$key."','".$value['extra']."','".$value['element_delete']."','".$value['string_delete']."')";
					$db->query( $query );
				}
			}
		}
		Header( "Location: " . NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . "=" . $module_name );
		die();
	}
	
	$query = "SELECT * FROM ".$__site_structure." WHERE site_id=".$item['id'];
	$temp_id=$db->query( $query );
	$temp=array();
	while($row = $temp_id->fetch())
	{
		$temp[$row['field_name']]=$row;
	}

	//debug($temp);
	$field=array('title','hometext','bodyhtml','homeimgalt','author');
	
	$contents .= '<form name="EditDeclarationSite" id="EditDeclarationSite" method="post">
		<button type="submit" class="btn btn-success">Ghi lại</button>
		<a href="'.NV_BASE_ADMINURL.'index.php?'.NV_NAME_VARIABLE.'='.$module_name.'&'.NV_OP_VARIABLE.'=edit_site_structure&id='.$item['id'].'" style="color:#000;"><button type="button" class="btn btn-primary">Sửa mẫu</button></a>
		<a href="'.NV_BASE_ADMINURL.'index.php?'.NV_NAME_VARIABLE.'='.$module_name.'" style="color:#000;"><button type="button" class="btn btn-primary">Danh sách</button></a>
		<a href="'.NV_BASE_ADMINURL.'index.php?'.NV_NAME_VARIABLE.'='.$module_name.'&'.NV_OP_VARIABLE.'=add_site_structure" style="color:#000;"><button type="button" class="btn btn-primary">Thêm mẫu</button></a>
	<div class="table-responsive" style="margin-top: 10px">
		<table class="table table-striped table-bordered table-hover">
            <tr bgcolor="#f2f2f2">
                <th style="padding:5px;">Trường dữ liệu</th>
                <th style="padding:5px; width:30%;">Mẫu cần lấy</th>
                <th style="padding:5px; width:30%;">Mẫu đối tượng cần xóa (cách nhau bởi dấu phẩy ",")</th>
                <th style="padding:5px; width:30%;">Chuỗi ký tự cần xóa (cách nhau bởi dấu phẩy ",")</th>
            </tr>';
	for($i=0;$i<sizeof($field);$i++){
		$contents .= '<tr>
				<td style="padding:2px;">'.$field[$i].'</td>
				<td style="padding:2px;"><input name="field['.$field[$i].'][extra]" type="text" id="field['.$field[$i].'][extra]" style="width:99%;" value=\''.((isset($temp[$field[$i]]['extra']) and $temp[$field[$i]]['extra'])?$temp[$field[$i]]['extra']:'').'\' /></td>
				<td style="padding:2px;"><input name="field['.$field[$i].'][element_delete]" type="text" id="field['.$field[$i].'][element_delete]" style="width:99%;" value=\''.((isset($temp[$field[$i]]['element_delete']) and $temp[$field[$i]]['element_delete'])?$temp[$field[$i]]['element_delete']:'').'\' /></td>
				<td style="padding:2px;"><input name="field['.$field[$i].'][string_delete]" type="text" id="field['.$field[$i].'][string_delete]" style="width:99%;" value=\''.((isset($temp[$field[$i]]['string_delete']) and $temp[$field[$i]]['string_delete'])?$temp[$field[$i]]['string_delete']:'').'\' /></td>
			</tr>';
	}
	$contents .= '</table></div>
	<input type="hidden" name="cmd" id="cmd" value="1" /></form>';
}else{
	$contents .= "   
		<div class=\"quote\" style=\"width: 780px;\">        
			<blockquote class=\"error\"><span style=\"font-size:16px\">Không tồn tại mẫu này</span>        
			</blockquote></div><div class=\"clear\">
		</div>";
}


include ( NV_ROOTDIR . "/includes/header.php" );
echo nv_admin_theme( $contents );
include ( NV_ROOTDIR . "/includes/footer.php" );

?>