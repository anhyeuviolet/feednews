<?php

/**
 * @Project FEEDNEWS ON NUKEVIET 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 07/30/2013 10:27
 */

if ( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$xtpl = new XTemplate( $op . ".tpl", NV_ROOTDIR . "/themes/" . $global_config['module_theme'] . "/modules/" . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );

$xtpl->assign( 'BUTTON', array(
	'add' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=add_site_structure',
	'copy' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=copy_site_structure',
	'edit' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=edit_site_structure',
	'temp' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=temp_site_structure',
) );

$__site=NV_PREFIXLANG . "_" . $module_name . "_site";
$__site_structure=NV_PREFIXLANG . "_" . $module_name . "_site_structure";

$id=$nv_Request ->get_int('id','post,get');

$query = "SELECT * FROM ".$__site." WHERE id=".intval($id);
$edit_id=$db->query( $query );
$item=$edit_id->fetch();

$page_title = $lang_module['temp_site_structure']." - ".$item['name'];
$error="";

if($item){
	$cmd = $nv_Request->isset_request( 'cmd', 'post' );
	if($cmd){
		// submit
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
		Header( "Location: " . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=temp_site_structure&id='.$item['id'] );
		die();
	}

	$query = "SELECT * FROM ".$__site_structure." WHERE site_id=".$item['id'];
	$temp_id=$db->query( $query );
	$temp=array();
	while($row = $temp_id->fetch())
	{
		$temp[$row['field_name']]=$row;
	}

	$field=array('title','hometext','bodyhtml','homeimgalt','author');
	for($i=0;$i<sizeof($field);$i++){
		$xtpl->assign( 'TEMP', array(
			'extra' => ((isset($temp[$field[$i]]['extra']) and $temp[$field[$i]]['extra'])?$temp[$field[$i]]['extra']:''),
			'element_delete' => ((isset($temp[$field[$i]]['element_delete']) and $temp[$field[$i]]['element_delete'])?$temp[$field[$i]]['element_delete']:''),
			'string_delete' => ((isset($temp[$field[$i]]['string_delete']) and $temp[$field[$i]]['string_delete'])?$temp[$field[$i]]['string_delete']:''),
		) );
		$xtpl->assign('FIELD',$field[$i]);
		$xtpl->parse( 'main.field_list' );
	}
	$xtpl->assign('ITEM',$item);
}
else
{
	$contents = $error;
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );
include ( NV_ROOTDIR . "/includes/header.php" );
echo nv_admin_theme( $contents );
include ( NV_ROOTDIR . "/includes/footer.php" );