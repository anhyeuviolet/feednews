<?php

/**
 * @Project FEEDNEWS 3.2.01
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
	$query = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_name. "_site (`id`, `name`, `host`, `url`, `extra`, `count`, `table_name`, `get_image`, `image_pattern`, `image_content_left`, `image_content_right`, `pattern_bound`, `catid`, `status`, `page_num`, `image_dir`,`sourceid`, `begin`, `end`, `bid`, `cat_title`) VALUES
	(NULL,'".$site['name']."','".$site['host']."','".$site['url']."','".$site['extra']."','".$site['count']."','".$site['table_name']."','".$site['get_image']."','".$site['image_pattern']."','".$site['image_content_left']."','".$site['image_content_right']."','".$site['pattern_bound']."','".$site['catid']."','".$site['status']."','','".$site['image_dir']."','".$site['sourceid']."','','','".$site['bid']."','".$site['cat_title']."')";
	if( $new_id=$db->query( $query ) )
	{
		// chèn dữ liệu vào bảng _site_structure
		$query="select ".$__site_structure.".* from ".$__site_structure." where site_id=".$id;
		if($query_id=$db->query( $query )){
			while($row = $query_id->fetch())
			{
				$query="INSERT INTO ".$__site_structure." (id, site_id, field_name, extra, count, element_delete, string_delete) VALUES (NULL, '".$new_id."','".$row['field_name']."','".$row['extra']."','".$row['element_delete']."','".$row['string_delete']."')";
				$db->query( $query );
			}
		}
		Header( "Location: " . NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . "=" . $module_name );
		die();
	}else{
		$error .= "Nhân bản lỗi, không lưu được dữ liệu";
	}
}else{
	$error .= "Không tồn tại mẫu nguồn để nhân bản";
}
if($error){
	$contents .= "   
<div class=\"quote\" style=\"width: 780px;\">        
	<blockquote class=\"error\"><span style=\"font-size:16px\">" . $error . "</span>        
	</blockquote></div><div class=\"clear\">
</div>";
}
$contents.='<a href="'.NV_BASE_ADMINURL.'index.php?'.NV_NAME_VARIABLE.'='.$module_name.'" style="color:#000;"><button type="button">Danh sách</button></a>';

include ( NV_ROOTDIR . "/includes/header.php" );
echo nv_admin_theme( $contents );
include ( NV_ROOTDIR . "/includes/footer.php" );

?>