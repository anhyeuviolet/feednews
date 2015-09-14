<?php

/**
 * @Project FEEDNEWS ON NUKEVIET 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 07/30/2013 10:27
 */

if ( ! defined( 'NV_ADMIN' ) or ! defined( 'NV_MAINFILE' ) or ! defined( 'NV_IS_MODADMIN' ) ) die( 'Stop!!!' );
global $module_name;
require_once ( NV_ROOTDIR . "/modules/" . $module_name . "/global.function.php" );
require_once ( NV_ROOTDIR . "/modules/" . $module_name . "/simple_html_dom.php" );


$allow_func = array( 'main', 'site_structure', 'add_site_structure','edit_site_structure','copy_site_structure','temp_site_structure');

define( 'NV_IS_FILE_ADMIN', true );
function debug($array)
{
	echo '<pre>';
	print_r($array);
	echo '</pre>';
	exit();
}
function check_link($url,$host='')
{
	if((nv_is_url($url)===false) and (preg_match_all('/http:\/\/(.*)\.([a-z]+)\//',$host,$matches,PREG_SET_ORDER)))
	{
		while ($url{0}=='/'){
			$url=substr($url,1);
		}
		if($matches[0][0]{strlen($matches[0][0])-1}!='/'){
			$matches[0][0]=$matches[0][0].'/';
		}
		$url = $matches[0][0].$url;
	}
	return $url;
}

function _isCurl(){
    return function_exists('curl_version');
}
function _urlencode($url){
	$output="";
	for($i = 0; $i < strlen($url); $i++) 
	$output .= strpos("/:@&%=?.#", $url[$i]) === false ? urlencode($url[$i]) : $url[$i]; 
	return $output;
}
function file_get_contents_curl($url) {
	//$url=urlencode($url);
	//debug($url);
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

function html_no_comment($url) {
	$url=_urlencode($url);
    // create HTML DOM
	$check_curl=_isCurl();
	if(!$html=file_get_html($url)){
		if(!$html=str_get_html(file_get_contents_curl($url)) or !$check_curl){
			return false;
		}
	}
    // remove all comment elements
    foreach($html->find('comment') as $e)
        $e->outertext = '';

    $ret = $html->save();

    // clean up memory
    $html->clear();
    unset($html);
    return $ret;
}

/**
 * get_html_body()
 *
 * @param integer $html_detail
 * @param integer $value
 * @param integer $item
 * @return
 */

function get_html_body( $html_detail, $value, $item ) 
{
		//print_r($value);
	$element_delete = $value['element_delete'];
	//echo $element_delete.'<br>';
	if($detail_pattern = $value['extra'])
	{
		// Nếu mẫu cần lấy có dạng đối tượng con có thứ tự {nth} của một đối tượng, 
		if(preg_match("/{([^*]+)}/", $detail_pattern, $child))
		{
			$detail_pattern=substr($detail_pattern,0,strpos($detail_pattern,'{'));
			// Nếu có chỉ định đối tượng con cụ thể dạng childelement-nth
			if(strpos($child[1],'-'))
			{
				$el=explode('-',$child[1]);
				foreach($html_detail->find($detail_pattern) as $element)
				{
					if($element_delete)
					{
						$arr = explode(',',$element_delete);
						for($i=0;$i<count($arr);$i++)
						{
							if(preg_match("/{([^*]+)}/", $arr[$i], $matches))
							{
								// Nếu mẫu đối tượng cần xóa có dạng {nth} thì xóa đối tượng con có thứ tự là nth
								$element->find($el[0],$el[1])->children($matches[1])->outertext='';
							}
							else
							{
								foreach($element->find($arr[$i]) as $e)
								{
									$e->outertext='';
								}
							}
						}
					}
					// Thông tin lấy được
					if($value['field_name']=='bodyhtml')
					{
						$item[$value['field_name']] = stripwhitespace(nv_convert($element->find($el[0],$el[1])->innertext));
					}
					else
					{
						$item[$value['field_name']] = stripwhitespace(nv_convert(strip_tags($element->find($el[0],$el[1])->innertext)));
					}
					break;
				}
			// Chỉ có dạng {nth}, nghĩa là lấy đối tượng con gần nhất có chỉ số là nth
			}
			else
			{
				foreach($html_detail->find($detail_pattern) as $element)
				{
					// Xóa mẫu đối tượng đã khai báo
					if($element_delete)
					{
						$arr = explode(',',$element_delete);
						for($i=0;$i<count($arr);$i++)
						{
							if(preg_match("/{([^*]+)}/", $arr[$i], $matches))
							{
								// Nếu mẫu đối tượng cần xóa có dạng {nth} thì xóa đối tượng con có thứ tự là nth
								$element->children($child[1])->children($matches[1])->outertext='';
							}
							else
							{
								foreach($element->find($arr[$i]) as $e)
								{
									$e->outertext='';
								}
							}
						}
					}
					// Thông tin lấy được
					if($value['field_name']=='bodyhtml')
					{
						$item[$value['field_name']] = stripwhitespace(nv_convert($element->children($child[1])->innertext));
					}
					else
					{
						$item[$value['field_name']] = stripwhitespace(nv_convert(strip_tags($element->children($child[1])->innertext)));
					}
					break;
				}
			}
		}
		else
		{
			foreach($html_detail->find($detail_pattern) as $element)
			{
				// Xóa mẫu đối tượng đã khai báo
				if($element_delete){
					$arr = explode(',',$element_delete);
					for($i=0;$i<count($arr);$i++)
					{
						if(preg_match("/{([^*]+)}/", $arr[$i], $matches))
						{
							// Nếu mẫu đối tượng cần xóa có dạng {nth} thì xóa đối tượng con có thứ tự là nth
							$element->children($matches[1])->outertext='';
						}
						else
						{
							foreach($element->find($arr[$i]) as $e)
							{
								$e->outertext='';
							}
						}
					}
				}
				// Thông tin lấy được
				if($value['field_name']=='bodyhtml')
				{
					$item[$value['field_name']] = stripwhitespace(nv_convert($element->innertext));
				}
				else
				{
					$item[$value['field_name']] = stripwhitespace(nv_convert(strip_tags($element->innertext)));
				}
				break;
			}
		}
	}
	// Xóa chuỗi ký tự đã khai báo
	if($string_delete = $value['string_delete'])
	{
		$arr_string_delete = explode(',',$string_delete);
		for($s=0;$s<count($arr_string_delete);$s++)
		{
			$item[$value['field_name']]=str_replace($arr_string_delete[$s],'',$item[$value['field_name']]);
		}
	}

	return $item;
}