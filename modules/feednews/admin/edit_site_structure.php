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

$edit_id=$nv_Request ->get_int('id','post,get');
$query = "SELECT * FROM ".$__site." WHERE id=".$edit_id;
$edit_id=$db->query( $query );
$error="";

if($item=$edit_id->fetch()){
	
	//debug($item);
	if(isset($_REQUEST['table_name']) and $_REQUEST['table_name']){
		$table_name=$_REQUEST['table_name'];
	}else
	if($item['table_name']){
		$table_name=$item['table_name'];
	}else{
		$table_name="news";
	}
	//debug($table_name);
	$__cat=NV_PREFIXLANG . "_".$table_name."_cat";
	$__block_cat=NV_PREFIXLANG . "_".$table_name."_block_cat";
	$__source=NV_PREFIXLANG . "_".$table_name."_sources";

	// category
	$cat_sql="select ".$__cat.".* from ".$__cat."";
	$cat_id=$db->query( $cat_sql );
	
	// block
	$bsql="select ".$__block_cat.".* from ".$__block_cat;
	$bid=$db->query( $bsql );
	$block=array();
	if($item['bid']){
		$block=array_flip(explode(',',$item['bid']));
	}
	
	// source
	$source_sql="select * from ".$__source." order by weight ASC";
	$source_id=$db->query( $source_sql );
	
	$page_title = $lang_module['edit_site_structure']." - ".$item['name'];
	$cmd = $nv_Request->isset_request( 'cmd', 'post' );
	if($cmd){
		$name = $nv_Request->get_string( 'name', 'post' );
		$host = $nv_Request->get_string( 'host', 'post' );
		$url = $nv_Request->get_string( 'url', 'post' );
		$extra = $nv_Request->get_string( 'extra', 'post' );
		$count = $nv_Request->get_string( 'count', 'post' );
		$get_image = $nv_Request->get_int( 'get_image', 'post' );
		$image_pattern = $nv_Request->get_string( 'image_pattern', 'post' );
		$image_content_left = $nv_Request->get_string( 'image_content_left', 'post' );
		$image_content_right = $nv_Request->get_string( 'image_content_right', 'post' );
		$pattern_bound = $nv_Request->get_string( 'pattern_bound', 'post' );
		$catid = $nv_Request->get_int( 'catid', 'post' );
		$status = $nv_Request->get_int( 'status', 'post' );
		$sourceid = $nv_Request->get_int( 'sourceid', 'post' );
		$bid=implode(',',$nv_Request->get_array( 'bid', 'post' ));
		
		if($name=='' or $host=='' or $url=='' or $extra=='' or $pattern_bound=='' or $count ==''){
			$error = "Hãy nhập đầy đủ các thông tin cần thiết";
		}else{
			// lấy danh mục tin đã chọn
			$query="select ".$__cat.".* from ".$__cat." where catid=".$catid;
			$query_id=$db->query( $query );
			$catinfo=$query_id->fetch();
			$query = "UPDATE " . $__site . " SET 
				name=".$db->quote($name)."
				,host=".$db->quote($host)."
				,url=".$db->quote($url)."
				,extra=".$db->quote($extra)."
				,count=".$count."
				,table_name=".$db->quote($table_name)."
				,get_image=".$db->quote($get_image)."
				,image_pattern=".$db->quote($image_pattern)."
				,image_content_left=".$db->quote($image_content_left)."
				,image_content_right=".$db->quote($image_content_right)."
				,pattern_bound=".$db->quote($pattern_bound)."
				,catid=".$db->quote($catid)."
				,sourceid=".$db->quote($sourceid)."
				,status=".$db->quote($status)."
				,bid=".$db->quote($bid)."
				,cat_title=".$db->quote($catinfo['title'])."
			WHERE id=".$item['id'];
			//die($query);
			if($db->query( $query )){
				Header( "Location: " . NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . "=" . $module_name );
				die();
			}else{
				$error .= "Không thể lưu dữ liệu được";
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
	if ($cat['catid'] == $item['catid']){
		$cat['selected'] = "selected";
	}
	$xtpl->assign( 'CAT', $cat );
	$xtpl->parse( 'main.list_cat' );
}
	while( $source = $source_id->fetch() )
	{
		if ($source['sourceid'] == $item['sourceid'])
		{
		$source['selected'] = "selected";
		}
		$xtpl->assign( 'SOURCE', $source );
		$xtpl->parse( 'main.list_source' );
	}

	if( $bid )
	{
		while($b = $bid->fetch() )
		{
		if (isset($block[$b['bid']]))
		{
			$b['checked'] = "checked";
		}
			$xtpl->assign( 'BID', $b );
			$xtpl->parse( 'main.list_bid' );
		}
	}
	if ($item['status']==0)
	{
		$item['status0']= "selected";
	}else if ($item['status']==1)
	{
		$item['status1']= "selected";
	}
	
	if ($item['get_image']==0)
	{
		$item['get_image0']= "selected";
	}else if ($item['get_image']==1)
	{
		$item['get_image1']= "selected";
	}
	
	$xtpl->assign( 'ITEM', $item );
} else {
	$error = "Không tồn tại mẫu này!";
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
