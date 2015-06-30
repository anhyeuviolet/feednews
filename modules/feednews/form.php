<?php
$item=$_REQUEST;
if(isset($item['ajax']) and $item['ajax']==1 and $url=$item['url']){
	require_once 'simple_html_dom.php';
	if($html=html_no_comment($url)){
		$html=str_get_html($html);
		$script=$html->find("script");
		foreach($script as $sc)
		{
			$sc->outertext='';
		}
		$a=$html->find("body",0)->childNodes();
		$noidung="";
		foreach($a as $child)
		{
			$noidung.=$child->outertext();
		}
		$html->clear(); 
		unset($html);
		echo $noidung; exit();
	}
}
function _isCurl(){
    return function_exists('curl_version');
}
function file_get_contents_curl($url) {
	//$url=urlencode($url);
	//debug($url);
    $ch = curl_init();

    //curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    //curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    $ret = curl_exec($ch);
    return $ret;
}
function html_no_comment($url) {
    // create HTML DOM
	$check_curl=_isCurl();
	if($check_curl and !$html=str_get_html(file_get_contents_curl($url))){
		return false;
	}else
	if(!$html=file_get_html($url)){
		return false;
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
