<?php

/**
 * @Project FEEDNEWS ON NUKEVIET 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 08/25/2015 10:27
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



$page_title = $lang_module['main'];
$__site=NV_PREFIXLANG . "_" . $module_name . "_site";
$__site_structure=NV_PREFIXLANG . "_" . $module_name . "_site_structure";

$query = "SELECT " .$__site.".* FROM ".$__site." ORDER BY ".$__site.".name";
$site_id=$db->query( $query );
$status=array(0=>'Chờ duyệt',1=>'Đăng bài ngay');
$cmd = $nv_Request->get_string( 'cmd', 'post' );
$imgext=array('jpg'=>'jpg','jpeg'=>'jpeg','gif'=>'gif','png'=>'png','bmp'=>'bmp');
$error="";
$total=0;

if($cmd=='delete' and $temps = $nv_Request->get_typed_array( 'temps', 'post', '' )){
	foreach($temps as $id){
		$query = "DELETE FROM ".$__site." WHERE id=".$id;
		if($db->query( $query )){
			$query = "DELETE FROM ".$__site_structure." WHERE site_id=".$id;
			$db->query( $query );
		}
	}
	Header( "Location: " . NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . "=" . $module_name );
	die();
}
else
	
if($cmd=='feed' and $temps = $nv_Request->get_typed_array( 'temps', 'post', '' )){
	set_time_limit(0);
	$data_result=array();
	$temps = implode(',',$temps);
	$sql="SELECT " .$__site.".* FROM ".$__site." WHERE ".$__site.".id IN (".$temps.") ORDER BY ".$__site.".name";
	if($site_crawler=$db->query( $sql )){
		while( $site = $site_crawler->fetch() )
		{
			$table_name=$site['table_name'];
			// get source text
			$query="SELECT * FROM ".NV_PREFIXLANG."_".$table_name."_sources WHERE sourceid='".$site['sourceid']."'";
			$query_id=$db->query( $query );
			$sourcesite = $query_id->fetch();
			$sourcetext_c=$sourcesite['title'];
			
			$pattern_sql="SELECT * FROM ".$__site_structure." WHERE site_id=".$site['id']." ORDER BY id";
			$pattern_id=$db->query( $pattern_sql );
			while($row = $pattern_id->fetch() )
			{
				$pattern[$row['id']] = $row;
			}

			$html=html_no_comment($site['url']);
			if($html){
				$html=str_get_html($html);
				$host = $site['host'];
				$pattern_bound = $site['pattern_bound'];
				$pattern_link = $site['extra'];
				$pattern_img = $site['image_pattern'];
				$table_upload = change_alias($table_name);
				$folder = NV_ROOTDIR .'/'. NV_FILES_DIR .'/'. $table_upload."/".date('Y_m'); // Thư mục chứa ảnh thumb
				if(!is_dir($folder)) @mkdir($folder,0755,true);
				
				$folder_upload=NV_ROOTDIR .'/'. NV_UPLOADS_DIR .'/'. $table_upload."/".date('Y_m'); // Thư mục chứa ảnh upload trên server
				if(!is_dir($folder_upload)) @mkdir($folder_upload,0755,true);
				
				if($pattern_bound){
					//debug($pattern_bound);
					$arr_pattern_bound = explode(',',$pattern_bound);
					$arr_pattern_link = explode(',',$pattern_link);
					$arr_pattern_img = explode(',',$pattern_img);
					
					for($j=0;$j<count($arr_pattern_bound);$j++){
						$pattern_bound=$arr_pattern_bound[$j];
						$pattern_link=(isset($arr_pattern_link[$j]) and $arr_pattern_link[$j])?$arr_pattern_link[$j]:$arr_pattern_link[0];
						$pattern_img=(isset($arr_pattern_img[$j]) and $arr_pattern_img[$j])?$arr_pattern_img[$j]:$arr_pattern_img[0];
						//debug($pattern_bound);
						$max_item=$site['count']; $num=0;
						foreach($html->find($pattern_bound) as $bound)
						{
							if($num==$max_item) break; $num++;
							foreach($bound->find($pattern_link) as $link){
								$link = check_link($link->getAttribute('href'),$host);
							}
							//echo $link.'<br>';
							// parse row
							$html_detail=html_no_comment($link);
							if($html_detail){
								$html_detail=str_get_html($html_detail);
								$item = array();
								if($pattern)
								{
									foreach($pattern as $key=>$value)
									{
										//echo '<pre>';
										//print_r($value);
										$element_delete = $value['element_delete'];
										//echo $element_delete.'<br>';
										if($detail_pattern = $value['extra']){
											// Nếu mẫu cần lấy có dạng đối tượng con có thứ tự {nth} của một đối tượng, 
											if(preg_match("/{([^*]+)}/", $detail_pattern, $child)){
												$detail_pattern=substr($detail_pattern,0,strpos($detail_pattern,'{'));
												// Nếu có chỉ định đối tượng con cụ thể dạng childelement-nth
												if(strpos($child[1],'-')){
													$el=explode('-',$child[1]);
													foreach($html_detail->find($detail_pattern) as $element)
													{
														if($element_delete){
															$arr = explode(',',$element_delete);
															for($i=0;$i<count($arr);$i++){
																if(preg_match("/{([^*]+)}/", $arr[$i], $matches)){
																	// Nếu mẫu đối tượng cần xóa có dạng {nth} thì xóa đối tượng con có thứ tự là nth
																	$element->find($el[0],$el[1])->children($matches[1])->outertext='';
																}else{
																	foreach($element->find($arr[$i]) as $e){
																		$e->outertext='';
																	}
																}
															}
														}
														// Thông tin lấy được
														if($value['field_name']=='bodyhtml'){
															$item[$value['field_name']] = stripwhitespace(nv_convert($element->find($el[0],$el[1])->innertext));
														}else{
															$item[$value['field_name']] = stripwhitespace(nv_convert(strip_tags($element->find($el[0],$el[1])->innertext)));
														}
														break;
													}
												// Chỉ có dạng {nth}, nghĩa là lấy đối tượng con gần nhất có chỉ số là nth
												}else{
													foreach($html_detail->find($detail_pattern) as $element)
													{
														// Xóa mẫu đối tượng đã khai báo
														if($element_delete){
															$arr = explode(',',$element_delete);
															for($i=0;$i<count($arr);$i++){
																if(preg_match("/{([^*]+)}/", $arr[$i], $matches)){
																	// Nếu mẫu đối tượng cần xóa có dạng {nth} thì xóa đối tượng con có thứ tự là nth
																	$element->children($child[1])->children($matches[1])->outertext='';
																}else{
																	foreach($element->find($arr[$i]) as $e){
																		$e->outertext='';
																	}
																}
															}
														}
														// Thông tin lấy được
														if($value['field_name']=='bodyhtml'){
															$item[$value['field_name']] = stripwhitespace(nv_convert($element->children($child[1])->innertext));
														}else{
															$item[$value['field_name']] = stripwhitespace(nv_convert(strip_tags($element->children($child[1])->innertext)));
														}
														break;
													}
												}
											}else{
												foreach($html_detail->find($detail_pattern) as $element)
												{
													// Xóa mẫu đối tượng đã khai báo
													if($element_delete){
														$arr = explode(',',$element_delete);
														for($i=0;$i<count($arr);$i++){
															if(preg_match("/{([^*]+)}/", $arr[$i], $matches)){
																// Nếu mẫu đối tượng cần xóa có dạng {nth} thì xóa đối tượng con có thứ tự là nth
																$element->children($matches[1])->outertext='';
															}else{
																foreach($element->find($arr[$i]) as $e){
																	$e->outertext='';
																}
															}
														}
													}
													// Thông tin lấy được
													if($value['field_name']=='bodyhtml'){
														$item[$value['field_name']] = stripwhitespace(nv_convert($element->innertext));
													}else{
														$item[$value['field_name']] = stripwhitespace(nv_convert(strip_tags($element->innertext)));
													}
													break;
												}
											}
										}
										// Xóa chuỗi ký tự đã khai báo
										if($string_delete = $value['string_delete']){
											$arr_string_delete = explode(',',$string_delete);
											for($s=0;$s<count($arr_string_delete);$s++){
												$item[$value['field_name']]=str_replace($arr_string_delete[$s],'',$item[$value['field_name']]);
											}
										}
									}
									//debug($item);
									if(isset($item['title']) and $item['title'])
									{
										$duplicate=false;
										$item['title']=nv_htmlspecialchars(str_replace('\'','"',strip_tags($item['title'])));
										$item['alias']=change_alias($item['title']);
										// Kiểm tra trùng tin bài
										$query="SELECT id FROM ".NV_PREFIXLANG."_".$table_name."_rows WHERE alias='".$item['alias']."'";
										$query_id=$db->query( $query );
										if( $query_id->fetch( 3 ) ){ $duplicate=true; }
										if(isset($data_result) and $data_result){
											foreach($data_result as $data){
												if($data['alias']==$item['alias']){
													$duplicate=true;
												}
											}
										}
										if($duplicate){
											$num = $num - 1;
										}
										else if(!$duplicate){
											// Lấy ảnh đại diện
											$items = $bound->find($pattern_img);
											if($items and count($items)){
												foreach($items as $img){
													$image_url=$img->src;
												}
												$source = check_link($image_url,$site['host']);
												if($site['get_image']){
													$basename = basename($source);
													foreach($imgext as $ext){
														if($pos=strpos($basename,$ext)){
															$basename=substr($basename,0,$pos+strlen($ext));
															break;
														}
													}
													// Thư mục chứa ảnh thumb
													if(file_exists($folder.'/'.$basename)){
														$dest = $folder.'/'.time().'_'.$basename;
													}else{
														$dest = $folder.'/'.$basename;
													}

													if(file_put_contents($dest, file_get_contents($source)))
													{
														$site['image_url'] = $dest;
														// copy ảnh sang thư mục upload trên server
														if(file_exists($folder_upload.'/'.$basename)){
															$basename = time().'_'.$basename;
														}
														$copyto = $folder_upload.'/'.$basename;
														if(copy($dest, $copyto)){
															$site['homeimgfile'] = date('Y_m')."/".$basename;
														}
													}else{
														$site['image_url'] = '';
														$site['homeimgfile'] = '';
													}
													
													if(isset($site['image_url']) and file_exists($site['image_url'])) $item['image_url'] = $site['image_url'];
													if(isset($copyto) and file_exists($copyto)) $item['homeimgfile'] = $site['homeimgfile'];
												}else{
													$item['homeimgfile']=str_replace(' ','%20',$source);
												}
											}
											// Viết lại đường dẫn ảnh trong nội dung
											if(isset($item['bodyhtml']) and $item['bodyhtml'])
											{
												$item['bodyhtml'] = str_replace($site['image_content_left'],$site['image_content_right'],$item['bodyhtml']);
											}
											$item['catid']= $site['catid'];
											$item['bid'] = $site['bid'];
											$item['status']= $site['status'];
											$item['sourceid'] = $site['sourceid'];
											$item['sourcetext'] = $sourcetext_c;
											$data_result[] = $item;
										}
									}
								}else{
									$error.="<div>Không tìm thấy mẫu cấu trúc chi tiết <strong>".$link."</strong></div>";
								}
								$html_detail->clear(); 
								unset($html_detail);
							}else{
								$error.="<div>Không phân tích được cấu trúc HTML của trang chi tiết <strong>".$link."</strong></div>";
							}
						}
					}
				}else{
					$error.="<div>Không tìm thấy mẫu bao ngoài một đối tượng</div>";
				}
				$html->clear(); 
				unset($html);
			}else{
				$error.="<div>Không phân tích được cấu trúc HTML của trang nguồn <strong>".$site['url']."</strong></div>";
			}
		}
		//var_dump($data_result); die();
		//debug($data_result);
		if($data_result){
			$total=sizeof($data_result);
			foreach($data_result as $item){
				$addtime=NV_CURRENTTIME-mt_rand(60,1000);
				$hometext=(isset($item['hometext']) and $item['hometext'])?nv_htmlspecialchars(str_replace('\'','"',strip_tags($item['hometext']))):'';
				$item['bodyhtml']=defined( 'NV_EDITOR' ) ? nv_nl2br( $item['bodyhtml'], '' ) : nv_nl2br( nv_htmlspecialchars( strip_tags( $item['bodyhtml'] ) ), '<br />' );
				$bodyhtml=(isset($item['bodyhtml']) and $item['bodyhtml'])?str_replace('\'','"',$item['bodyhtml']):'';
				$bodytext=nv_news_get_bodytext($bodyhtml);
				$sourcetext=(isset($item['sourcetext']) and $item['sourcetext'])?nv_htmlspecialchars(str_replace('\'','"',strip_tags($item['sourcetext']))):'';
				$sourceid=(isset($item['sourceid']) and $item['sourceid'])?$item['sourceid']:0;
				
				$homeimgfile=(isset($item['homeimgfile']) and $item['homeimgfile'])?$item['homeimgfile']:'';
				
				if (!empty($homeimgfile)and nv_is_url($homeimgfile)){
					$homeimgthumb = 3;
				}
				elseif ( !empty($homeimgfile))
				{
					$homeimgthumb = 1;
				}
				else
				{
					$homeimgthumb = '';
				}
				
				$homeimgalt=(isset($item['homeimgalt']) and $item['homeimgalt'])?nv_htmlspecialchars(str_replace('\'','"',strip_tags($item['homeimgalt']))):'';
				$author=(isset($item['author']) and $item['author'])?nv_htmlspecialchars(str_replace('\'','"',strip_tags($item['author']))):'';
				
				$keywords="";
				if( $hometext != "" )
					$keywords = nv_get_keywords( $hometext );
				else
					$keywords = nv_get_keywords( nv_fil_tag( $bodyhtml ) );
				
				/* 	các bảng cần chèn dữ liệu gồm:
				**	NV_PREFIXLANG."_".$table_name."_rows"
				**	NV_PREFIXLANG."_".$table_name."_".$item['catid']
				**	NV_PREFIXLANG."_".$table_name."_bodyhtml_*"
				**	NV_PREFIXLANG."_".$table_name."_bodytext"
				**	NV_PREFIXLANG."_".$table_name."_block"
				*/
				
				$query="INSERT INTO ".NV_PREFIXLANG."_".$table_name."_rows (id, catid, listcatid, topicid, admin_id, author, sourceid, addtime, edittime, status, publtime, exptime, archive, title, alias, hometext, homeimgfile, homeimgalt, homeimgthumb, inhome, allowed_comm, allowed_rating, hitstotal, hitscm, total_rating, click_rating) VALUES 
				(NULL, ".$item['catid'].", '".$item['catid']."', 0, 1, '".$author."', ".$sourceid.", ".$addtime.", ".$addtime.", ".$item['status'].", ".$addtime.", 0, 2, '".$item['title']."', '".$item['alias']."', '".$hometext."', '".$homeimgfile."', '".$homeimgalt."', '".$homeimgthumb."', 1, 2, 1, 1, 0, 0, 0);";
				// Lưu vào NV_PREFIXLANG."_".$table_name."_rows"
				//if( $id=$db->query_insert_id( $query ) ){
				if( $id = $db->insert_id( $query, 'id', array() ) )
				{
					// Lưu vào NV_PREFIXLANG."_".$table_name."_".$item['catid']
					$query="INSERT INTO ".NV_PREFIXLANG."_".$table_name."_".$item['catid']." (id, catid, listcatid, topicid, admin_id, author, sourceid, addtime, edittime, status, publtime, exptime, archive, title, alias, hometext, homeimgfile, homeimgalt, homeimgthumb, inhome, allowed_comm, allowed_rating, hitstotal, hitscm, total_rating, click_rating) VALUES 
					(".$id.", ".$item['catid'].", '".$item['catid']."', 0, 1, '".$author."', ".$sourceid.", ".$addtime.", ".$addtime.", ".$item['status'].", ".$addtime.", 0, 2, '".$item['title']."', '".$item['alias']."', '".$hometext."', '".$homeimgfile."', '".$homeimgalt."', '".$homeimgthumb."', 1, 2, 1, 1, 0, 0, 0);";
					$db->query( $query );
					
					// Lưu vào NV_PREFIXLANG."_".$table_name."_bodyhtml_*"
					// check bodyhtml
					$bodyhtml_table=NV_PREFIXLANG."_".$table_name."_bodyhtml_".ceil($id/2000);
					$val = $db->query("select 1 from ".$bodyhtml_table."");
					
					if( empty($val) ){
						$query = "CREATE TABLE ".$bodyhtml_table." (
						  id int(11) unsigned NOT NULL,
						  bodyhtml longtext NOT NULL,
						  sourcetext varchar(255) NOT NULL DEFAULT '',
						  imgposition tinyint(1) NOT NULL DEFAULT '1',
						  copyright tinyint(1) NOT NULL DEFAULT '0',
						  allowed_send tinyint(1) NOT NULL DEFAULT '0',
						  allowed_print tinyint(1) NOT NULL DEFAULT '0',
						  allowed_save tinyint(1) NOT NULL DEFAULT '0',
						  PRIMARY KEY (id)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8";
						$db->query( $query );
					}
					
					$query="INSERT INTO ".$bodyhtml_table." (id, bodyhtml, sourcetext, imgposition, copyright, allowed_send, allowed_print, allowed_save) VALUES 
					(".$id.", '".$bodyhtml."', '".$sourcetext."', 1, 0, 1, 1, 1)";
					$db->query( $query );
					
					// Lưu vào NV_PREFIXLANG."_".$table_name."_bodytext"
					$query="INSERT INTO ".NV_PREFIXLANG."_".$table_name."_bodytext (id, bodytext) VALUES (".$id.",'".$bodytext."')";
					$db->query( $query );
					
					// Lưu vào NV_PREFIXLANG."_".$table_name."_block"
					if($item['bid'])
					{
						$block_ids = explode(',',$item['bid']);
						foreach($block_ids as $bid)
						{
							$db->query( "INSERT INTO " . NV_PREFIXLANG . "_" . $table_name . "_block (bid, id, weight) VALUES ('" . $bid . "', '" . $id . "', '0')" );
						}
					}
				}
			}
			// block
			$bsql="select * from " . NV_PREFIXLANG . "_" . $table_name . "_block_cat";
			$bquery_id=$db->query( $bsql );
			while( $bid_i = $bquery_id->fetch() ){
				$bid = intval( $bid_i['bid'] );
				if( $bid > 0 )
				{
					$query = "SELECT id FROM " . NV_PREFIXLANG . "_" . $table_name . "_block where bid='" . $bid . "' ORDER BY weight ASC";
					$result = $db->query( $query );
					$weight = 0;
					while( $row = $result->fetch() )
					{
						$weight++;
						if( $weight <= 100 )
						{
							$sql = "UPDATE " . NV_PREFIXLANG . "_" . $table_name . "_block SET weight=" . $weight . " WHERE bid='" . $bid . "' AND id=" . intval( $row['id'] );
						}
						else
						{
							$sql = "DELETE FROM " . NV_PREFIXLANG . "_" . $table_name . "_block WHERE bid='" . $bid . "' AND id=" . intval( $row['id'] );
						}
						$db->query( $sql );
					}
				}
			}
		}
		//Header( "Location: " . NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . "=" . $module_name );
		//die();
	}
}
if($total){
	$xtpl->assign( 'TOTAL', $total );
	$xtpl->parse( 'main.complete' );
}
if($error){
	$xtpl->assign( 'ERROR', $error );
	$xtpl->parse( 'main.error' );
}

	while( $row = $site_id->fetch() )
	{
		$row['status'] = $status[$row['status']];
		$xtpl->assign( 'ROW', $row );
		$xtpl->parse( 'main.list_pattern' );
	}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );
include ( NV_ROOTDIR . "/includes/header.php" );
echo nv_admin_theme( $contents );
include ( NV_ROOTDIR . "/includes/footer.php" );
