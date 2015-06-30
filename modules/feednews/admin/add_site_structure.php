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
	$image_dir = '';
	$page_num = '';
	
	if(!$name or !$host or !$url or !$extra or !$pattern_bound){
		$error .= "Hãy nhập đầy đủ các thông tin cần thiết";
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
			$error .= "Không thể lưu dữ liệu được";
		}
	}
}

if( $error )
{
	$contents .= "<div class=\"alert alert-danger\">" . $error . "</div>";
}
$contents .= '<form name="EditDeclarationSite" id="EditDeclarationSite" method="post">
        <button type="submit" class="btn btn-success">Ghi lại</button>
		<a href="'.NV_BASE_ADMINURL.'index.php?'.NV_NAME_VARIABLE.'='.$module_name.'" style="color:#000;"><button type="button" class="btn btn-primary">Danh sách</button></a>
		<span style="float:right; font-weight:700;">Mục có dấu (<span class="require">*</span>) là bắt buộc</span>
		<div class="table-responsive" style="margin-top: 10px">
		<table class="table table-striped table-bordered table-hover">
		<tr><td style="padding:2px;"><label>Module lưu tin</label></td><td>
		<select class="form-control w200" name="module" id="module" onchange="window.location=\''.NV_BASE_ADMINURL .'index.php?'. NV_NAME_VARIABLE . '=' . $module_name.'&'.NV_OP_VARIABLE.'=add_site_structure&table_name=\'+this.value">
		';
		$sql = "SELECT * FROM " . NV_PREFIXLANG . "_modules WHERE module_file='news'";
		$result = $db->query( $sql );
		while( $rows = $result->fetch() )
		{
			$select1="";
			if($table_name==$rows['module_data']){
				$select1 = " selected=\"selected\"";
			}
			$contents .= "<option " . $select1 . " value=\"" . $rows['module_data'] . "\">" . $rows['title'] . "</option>";
		}

	$contents .= '</select></td></tr><tr>
                <td style="padding:2px;"><label>Tên mẫu (<span class="require">*</span>)</label></td>
                <td style="padding:2px;"><input name="name" type="text" id="name" style="width:60%;" class="form-control" /></td>
            </tr>
            <tr>
                <td style="padding:2px;"><label>Host (<span class="require">*</span>)</label></td>
                <td style="padding:2px;"><input name="host" type="text" id="host" style="width:60%;" class="form-control" /></td>
            </tr>
            <tr>
                <td style="padding:2px;"><label>Url (<span class="require">*</span>)</label></td>
                <td style="padding:2px;"><input name="url" type="text" id="url" style="width:60%;" class="form-control" /></td>
            </tr>
            <tr>
                <td style="padding:2px;"><label>Mẫu bao ngoài một đối tượng (<span class="require">*</span>)</label></td>
                <td style="padding:2px;"><input name="pattern_bound" type="text" id="pattern_bound" style="width:60%;" class="form-control" /></td>
            </tr>
            <tr>
                <td style="padding:2px;"><label>Mẫu liên kết một tin (<span class="require">*</span>)</label></td>
                <td style="padding:2px;"><input name="extra" type="text" id="extra" style="width:60%;" class="form-control" /></td>
            </tr>
            <tr>
                <td style="padding:2px;"><label>Số tin lấy</label></td>
                <td style="padding:2px;"><input name="count" type="text" id="count" style="width:60%;" class="form-control" /></td>
            </tr>
            <tr>
                <td style="padding:2px;"><label>Chèn vào danh mục</label></td>
                <td style="padding:2px;"><select name="catid" id="catid" class="form-control w200">
				';
				while( $cat = $cat_id->fetch() )
				{
					$xtitle_i = "";
					if( $cat['lev'] > 0 )
					{
						for( $i = 1; $i <= $cat['lev']; $i++ )
						{
							$xtitle_i .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						}
					}
					$contents .= '<option value="'.$cat['catid'].'">'.$xtitle_i . $cat['title'].'</option>';
				}

                $contents .= '</select></td>
            </tr>
			<tr>
				<td style="padding:2px;"><label>Trạng thái tin bài</label></td>
				<td style="padding:2px;"><select name="status" id="status" class="form-control w200">
				<option value="0">Chờ duyệt</option>
				<option value="1" selected="selected">Đăng bài ngay</option>
				</select></td>
			</tr>
			<tr>
				<td style="padding:2px;"><label>Lấy ảnh đại diện về host</label></td>
				<td style="padding:2px;"><select name="get_image" id="get_image" class="form-control w200">
				<option value="1" selected>Có</option>
				<option value="0">Không</option>
				</select></td>
			</tr>
            <tr>
                <td style="padding:2px;"><label>Mẫu ảnh đại diện</label></td>
                <td style="padding:2px;"><input name="image_pattern" type="text" id="image_pattern" style="width:60%;" class="form-control" /></td>
            </tr>
            <tr>
                <td style="padding:2px;"><label>Thay thế đường dẫn ảnh trong nội dung</label></td>
                <td style="padding:2px;">
                     <input name="image_content_left" type="text" id="image_content_left" style="width:35%;" class="form-control pull-left" /> <span class="pull-left text-middle">==></span> 
                     <input name="image_content_right" type="text" id="image_content_right" style="width:35%;" class="form-control pull-left" />
                </td>
           </tr>
			<tr>
				<td style="padding:2px;"><label>Nguồn tin</label></td>
				<td style="padding:2px;"><select name="sourceid" id="sourceid" class="form-control w200">';
				while( $source = $source_id->fetch() )
				{
					$contents .= '<option value="'.$source['sourceid'].'">'.$source['title'].'</option>';
				}

			$contents .= '</select></td>
			</tr>
			<tr>
					<td style="padding:2px;"><label>Nhóm tin</label></td>
					<td style="padding:2px;">
					';
					if( $bid )
					{
						while($b = $bid->fetch() )
						{
							$contents .= '<input name="bid['.$b['bid'].']" type="checkbox" id="bid['.$b['bid'].']" value="'.$b['bid'].'" /> <label for="bid['.$b['bid'].']">'.$b['title'].'</label> ';
						}
					}

					$contents .= '</td>
					</tr>';
					
			$contents.='
        </table>
        </div>
        <input type="hidden" name="cmd" value="1" id="cmd" />
	</form>';


include ( NV_ROOTDIR . "/includes/header.php" );
echo nv_admin_theme( $contents );
include ( NV_ROOTDIR . "/includes/footer.php" );

?>