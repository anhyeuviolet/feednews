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

$page_title = $lang_module['site_structure'];
$__site=NV_PREFIXLANG . "_" . $module_name . "_site";
$__site_structure=NV_PREFIXLANG . "_" . $module_name . "_site_structure";
$__cat=NV_PREFIXLANG . "_news_cat";
$query = "SELECT " .$__site.".*,".$__cat.".title as category_title FROM ".$__site." LEFT OUTER JOIN ".$__cat." ON ".$__site.".catid=".$__cat.".catid ORDER BY ".$__site.".name";
$site_id=$db->query( $query );
$status=array(0=>'Chờ duyệt',1=>'Đăng bài ngay');
$cmd = $nv_Request->isset_request( 'cmd', 'post' );
if($cmd and $temps = $nv_Request->get_typed_array( 'selected_ids', 'post', '' )){
	// submit
	foreach($temps as $id){
		$query = "DELETE FROM ".$__site." WHERE id=".$id;
		if($db->query( $query )){
			$query = "DELETE FROM ".$__site_structure." WHERE site_id=".$id;
			$db->query( $query );
		}
	}
	Header( "Location: " . NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . "=" . $module_name."&".NV_OP_VARIABLE."=site_structure" );
	die();
}
$contents .= '<form name="feedForm" method="post">
	<div>
        <a href="'.NV_BASE_ADMINURL.'index.php?'.NV_NAME_VARIABLE.'='.$module_name.'&'.NV_OP_VARIABLE.'=add_site_structure" style="color:#000;"><button type="button" class="btn btn-primary">Thêm mới</button></a>
        <button class="btn btn-danger" type="submit" onclick="if(!confirm(\'Bạn muốn xóa không?\')) return false;">Xóa</button>
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover">
        	<tr bgcolor="#f2f2f2">
				<th style="padding:5px;" width="1%" align="center">#</th>
				<th style="padding:5px;" align="left" nowrap>Tên mẫu</th>
				<th style="padding:5px;" width="27%" align="left" nowrap>Đường dẫn</th>                    
				<th style="padding:5px;" width="1%" align="left" nowrap>Danh mục</th>                    
				<th style="padding:5px;" width="1%" align="left" nowrap>Trạng thái</th>                    
				<th style="padding:5px;" width="10%" align="left">Mẫu ảnh đại diện</th>
				<th style="padding:5px;" nowrap width="1%" align="center">Hành động</th>
            </tr>';
			while( $row = $site_id->fetch() ){
				$contents.='<tr valign="middle">
					<td style="padding:5px;" width="1%" align="center">
						<input name="selected_ids[]" type="checkbox" id="selected_ids['.$row['id'].']" value="'.$row['id'].'" />
					</td>
					<td style="padding:5px;" align="left" nowrap><label for="selected_ids['.$row['id'].']"><strong>'.$row['name'].'</strong></label></td>
					<td style="padding:5px;" align="left" nowrap><div style="word-wrap:break-word; width:250px; overflow:hidden;"><a href="'.$row['url'].'" target="_blank" title="'.$row['url'].'">'.$row['url'].'</a></div></td>
					<td style="padding:5px;" align="left" nowrap>'.$row['category_title'].'</td>
					<td style="padding:5px;" align="left" nowrap>'.$status[$row['status']].'</td>
					<td style="padding:5px;" align="left" nowrap>'.$row['image_pattern'].'</td>
					<td style="padding:5px;" align="center" nowrap width="1%">
						<a href="'.NV_BASE_ADMINURL .'index.php?'.NV_NAME_VARIABLE.'='.$module_name.'&'.NV_OP_VARIABLE.'=edit_site_structure&id='.$row['id'].'">Sửa</a> |
						<a href="'.NV_BASE_ADMINURL .'index.php?'.NV_NAME_VARIABLE.'='.$module_name.'&'.NV_OP_VARIABLE.'=temp_site_structure&id='.$row['id'].'">Cấu trúc</a>
					</td>
				</tr>';
			}
$contents .= '
        </table></div>
	</div>
    <input type="hidden" name="cmd" value="1" id="cmd" />
    </form>';


include ( NV_ROOTDIR . "/includes/header.php" );
echo nv_admin_theme( $contents );
include ( NV_ROOTDIR . "/includes/footer.php" );

?>