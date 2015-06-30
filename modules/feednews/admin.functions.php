<?php

/**
 * @Project FEEDNEWS 3.3.01
 * @Author MINHTC.NET (hunters49@gmail.com)
 * @Copyright (C) 2013 MINHTC.NET All rights reserved
 * @Createdate Sun, 28 Jul 2013 00:57:11 GMT
 */

if ( ! defined( 'NV_ADMIN' ) or ! defined( 'NV_MAINFILE' ) or ! defined( 'NV_IS_MODADMIN' ) ) die( 'Stop!!!' );
global $module_name;
require_once ( NV_ROOTDIR . "/modules/" . $module_name . "/global.function.php" );
require_once ( NV_ROOTDIR . "/modules/" . $module_name . "/simple_html_dom.php" );

//$submenu['main'] = $lang_module['main'];
//$submenu['site_structure'] = $lang_module['site_structure'];

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
	if((strpos($url,'http://')===false) and (preg_match_all('/http:\/\/(.*)\.([a-z]+)\//',$host,$matches,PREG_SET_ORDER)))
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
?>