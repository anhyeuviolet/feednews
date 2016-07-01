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

function _isCurl(){
    return function_exists('curl_version');
}
function _urlencode($url){
	$output="";
	for($i = 0; $i < strlen($url); $i++) 
	$output .= strpos("/:@&%=?.#", $url[$i]) === false ? urlencode($url[$i]) : $url[$i]; 
	return $output;
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