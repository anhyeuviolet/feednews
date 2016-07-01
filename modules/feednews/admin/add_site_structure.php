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

$page_title = $lang_module['add_site_structure'];

$__site=NV_PREFIXLANG . "_" . $module_name . "_site";
$__site_structure=NV_PREFIXLANG . "_" . $module_name . "_site_structure";

if(isset($_REQUEST['table_name']) and $_REQUEST['table_name']){
	$table_name=$_REQUEST['table_name'];
}else{
	$table_name="news";
}

$__cat=NV_PREFIXLANG . "_".$table_name."_cat";
$__block_cat=NV_PREFIXLANG . "_".$table_name."_block_cat";
$__source=NV_PREFIXLANG . "_".$table_name."_sources";

// category
$cat_sql="select ".$__cat.".* from ".$__cat."";
$cat_id=$db->query( $cat_sql );

// block
$bsql="select ".$__block_cat.".* from ".$__block_cat;
$bid=$db->query( $bsql );
// source
$source_sql="select * from ".$__source." order by weight ASC";
$source_id=$db->query( $source_sql );

$error="";
$cmd = $nv_Request->isset_request( 'cmd', 'post' );
if($cmd){
	$name = $nv_Request->get_string( 'name', 'post' );
	$host = $nv_Request->get_string( 'host', 'post' );
	$url = $nv_Request->get_string( 'url', 'post' );
	$extra = $nv_Request->get_string( 'extra', 'post' );
	$count = $nv_Request->get_int( 'count', 'post', 1 );
	if( $count == 0){
		$count = 1;
	}
	$get_image = $nv_Request->get_int( 'get_image', 'post' );
	$image_pattern = $nv_Request->get_string( 'image_pattern', 'post' );
	$image_content_left = $nv_Request->get_string( 'image_content_left', 'post' );
	$image_content_right = $nv_Request->get_string( 'image_content_right', 'post' );
	$pattern_bound = $nv_Request->get_string( 'pattern_bound', 'post' );
	$catid = $nv_Request->get_int( 'catid', 'post' );
	$status = $nv_Request->get_int( 'status', 'post' );
	$sourceid = $nv_Request->get_int( 'sourceid', 'post' );
	$bid=implode(',',$nv_Request->get_array( 'bid', 'post' ));
	$image_dir = '';
	$page_num = '';
	
	if(!$name or !$host or !$url or !$extra or !$pattern_bound or $count ==''){
		$error = $lang_module['lack_data'];
	}else{
		// lấy danh mục tin đã chọn
		$query="select ".$__cat.".* from ".$__cat." where catid=".$catid;
		$query_id=$db->query( $query );
		$catinfo=$query_id->fetch();
		
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
		
		catid='.intval( $catid ).',
		status='. intval( $status ) .',
		image_dir=:image_dir,
		cat_title=:cat_title' );

		$stmt->bindParam( ':name', $name, PDO::PARAM_STR );
		$stmt->bindParam( ':host', $host, PDO::PARAM_STR );
		$stmt->bindParam( ':url', $url, PDO::PARAM_STR );
		$stmt->bindParam( ':extra', $extra, PDO::PARAM_STR );
		$stmt->bindParam( ':count', $count, PDO::PARAM_STR );
		$stmt->bindParam( ':table_name', $table_name, PDO::PARAM_STR );
		$stmt->bindParam( ':pattern_bound', $pattern_bound, PDO::PARAM_STR );
		
		
		$stmt->bindParam( ':get_image', $get_image, PDO::PARAM_STR );
		$stmt->bindParam( ':image_pattern', $image_pattern, PDO::PARAM_STR );
		$stmt->bindParam( ':image_content_left', $image_content_left, PDO::PARAM_STR );
		$stmt->bindParam( ':image_content_right', $image_content_right, PDO::PARAM_STR );
		$stmt->bindParam( ':page_num', $page_num, PDO::PARAM_STR );
		$stmt->bindParam( ':sourceid', $sourceid, PDO::PARAM_STR );
		$stmt->bindParam( ':begin', $pattern_bound, PDO::PARAM_STR );
		$stmt->bindParam( ':end', $table_name, PDO::PARAM_STR );
		$stmt->bindParam( ':bid', $bid, PDO::PARAM_STR );
		
		$stmt->bindParam( ':image_dir', $image_dir, PDO::PARAM_STR );
		$stmt->bindParam( ':cat_title', $catinfo['title'], PDO::PARAM_STR );
		$stmt->execute();
		if( $id = $db->lastInsertId() )
		{
			Header( "Location: " . NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . "=" . $module_name."&".NV_OP_VARIABLE."=temp_site_structure&id=".$id."" );
			die();
		}else{
			$error = $lang_module['error_save'];
		}
	}
}

$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_modules WHERE module_file="news"';
$result = $db->query( $sql );
while( $row = $result->fetch() )
{
	if($table_name==$row['module_data'])
	{
		$row['selected'] ="selected";
	}
	$xtpl->assign( 'ROW', $row );
	$xtpl->parse( 'main.list_module' );
}

while( $cat = $cat_id->fetch() )
{
	$xtitle_i = "";
	if( $cat['lev'] > 0 )
	{
		for( $i = 1; $i <= $cat['lev']; $i++ )
		{
			$xtitle_i = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			$xtpl->assign( 'LEVEL', $xtitle_i );
			$xtpl->parse( 'main.list_cat.level' );
		}
	}
	$xtpl->assign( 'CAT', $cat );
	$xtpl->parse( 'main.list_cat' );
}

while( $source = $source_id->fetch() )
{
	$xtpl->assign( 'SOURCE', $source );
	$xtpl->parse( 'main.list_source' );
}

if( $bid )
{
	while($b = $bid->fetch() )
	{
		$xtpl->assign( 'BID', $b );
		$xtpl->parse( 'main.list_bid' );
	}
}
if($error){
	$xtpl->assign( 'ERROR', $error );
	$xtpl->parse( 'main.error' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );
include ( NV_ROOTDIR . "/includes/header.php" );
echo nv_admin_theme( $contents );
include ( NV_ROOTDIR . "/includes/footer.php" );
