<?php

/**
 * @Project FEEDNEWS 3.2.01
 * @Author FORUM.NUKEVIET.VN

 * @Created Wed, 01 Jul 2015 18:00:00 GMT
 */

if ( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$xtpl = new XTemplate( $op . ".tpl", NV_ROOTDIR . "/themes/" . $global_config['module_theme'] . "/modules/" . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );

$page_title = $lang_module['copy_site_structure'];
$__site=NV_PREFIXLANG . "_" . $module_name . "_site";
$__site_structure=NV_PREFIXLANG . "_" . $module_name . "_site_structure";
$id=$_REQUEST['id'];
$query="select ".$__site.".* from ".$__site." where id=".$id;
$query_id=$db->query( $query );
$site=$query_id->fetch();

$error="";
if($id and $site){
		// chèn dữ liệu vào bảng _site
	$stmt = $db->prepare( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data. '_site SET
		name=:name,
		host=:host,
		url=:url,
		extra=:extra,
		count=:count,
		table_name=:table_name,
		pattern_bound=:pattern_bound,
		
		get_image=:get_image,
		image_pattern=:image_pattern,
		image_content_left=:image_content_left,
		image_content_right=:image_content_right,
		page_num=:page_num, 
		sourceid=:sourceid, 
		begin=:begin,
		end=:end,
		bid=:bid,
		
		catid=:catid,
		status=:status,
		image_dir=:image_dir,
		cat_title=:cat_title' );

		$stmt->bindParam( ':name', $site['name'], PDO::PARAM_STR );
		$stmt->bindParam( ':host', $site['host'], PDO::PARAM_STR );
		$stmt->bindParam( ':url', $site['url'], PDO::PARAM_STR );
		$stmt->bindParam( ':extra', $site['extra'], PDO::PARAM_STR );
		$stmt->bindParam( ':count', $site['count'], PDO::PARAM_STR );
		$stmt->bindParam( ':table_name', $site['table_name'], PDO::PARAM_STR );
		$stmt->bindParam( ':pattern_bound', $site['pattern_bound'], PDO::PARAM_STR );
		
		
		$stmt->bindParam( ':get_image', $site['get_image'], PDO::PARAM_STR );
		$stmt->bindParam( ':image_pattern', $site['image_pattern'], PDO::PARAM_STR );
		$stmt->bindParam( ':image_content_left', $site['image_content_left'], PDO::PARAM_STR );
		$stmt->bindParam( ':image_content_right', $site['image_content_right'], PDO::PARAM_STR );
		$stmt->bindParam( ':page_num', $site['page_num'], PDO::PARAM_STR );
		$stmt->bindParam( ':sourceid', $site['sourceid'], PDO::PARAM_STR );
		$stmt->bindParam( ':begin', $site['begin'], PDO::PARAM_STR );
		$stmt->bindParam( ':end', $site['end'], PDO::PARAM_STR );
		$stmt->bindParam( ':bid', $site['bid'], PDO::PARAM_STR );
		
		$stmt->bindParam( ':catid', $site['catid'], PDO::PARAM_STR );
		$stmt->bindParam( ':status', $site['status'], PDO::PARAM_STR );
		
		$stmt->bindParam( ':image_dir', $site['image_dir'], PDO::PARAM_STR );
		$stmt->bindParam( ':cat_title', $site['cat_title'], PDO::PARAM_STR );
		$stmt->execute();
		
		
		
	if( $new_id = $db->lastInsertId() )
	{
		// Lay du lieu tu table goc
		$query="select ".$__site_structure.".* from ".$__site_structure." where site_id=".$id;
		if($query_id=$db->query( $query )){
			while($row = $query_id->fetch())
			{
				// Chen du lieu vao _site_structure
				$stmt = $db->prepare( 'INSERT INTO '.$__site_structure. ' SET
				site_id=:site_id,
				field_name=:field_name,
				extra=:extra,
				element_delete=:element_delete,
				string_delete=:string_delete');
				
				$stmt->bindParam( ':site_id', $new_id, PDO::PARAM_STR );
				$stmt->bindParam( ':field_name', $row['field_name'], PDO::PARAM_STR );
				$stmt->bindParam( ':extra', $row['extra'], PDO::PARAM_STR );
				$stmt->bindParam( ':element_delete', $row['element_delete'], PDO::PARAM_STR );
				$stmt->bindParam( ':string_delete', $row['string_delete'], PDO::PARAM_STR );
				$stmt->execute();
			}
		}
		Header( "Location: " . NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . "=" . $module_name .'&op=edit_site_structure&id='.$new_id);
		die();
	}else{
		$error = "Nhân bản lỗi, không lưu được dữ liệu";
	}
}else{
	$error = "Không tồn tại mẫu nguồn để nhân bản";
}
if($error){
	$contents = $error;
	$xtpl->assign( 'ERROR',$error );
}
$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

include ( NV_ROOTDIR . "/includes/header.php" );
echo nv_admin_theme( $contents );
include ( NV_ROOTDIR . "/includes/footer.php" );
