<?php

/**
 * @Project FEEDNEWS ON NUKEVIET 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 07/30/2013 10:27
 */

global $module_name;
require_once ( NV_ROOTDIR . "/modules/" . $module_name . "/simple_html_dom.php" );
function nv_news_fix_blocks( $bid, $table_name )
{
	$bid = intval( $bid );
	if( $bid > 0 )
	{
		$query = "SELECT `id` FROM `" . NV_PREFIXLANG . "_" . $table_name . "_block` where `bid`='" . $bid . "' ORDER BY `weight` ASC";
		$result = $db->sql_query( $query );
		$weight = 0;
		while( $row = $db->sql_fetchrow( $result ) )
		{
			$weight++;
			if( $weight <= 100 )
			{
				$sql = "UPDATE `" . NV_PREFIXLANG . "_" . $table_name . "_block` SET `weight`=" . $weight . " WHERE `bid`='" . $bid . "' AND `id`=" . intval( $row['id'] );
			}
			else
			{
				$sql = "DELETE FROM `" . NV_PREFIXLANG . "_" . $table_name . "_block` WHERE `bid`='" . $bid . "' AND `id`=" . intval( $row['id'] );
			}
			$db->sql_query( $sql );
		}
		$db->sql_freeresult();
	}
	return true;
}

function save_data_auto( $catid, $blockcat, $title, $alias, $hometext, $bodytext, $sourceid, $publtime, $homefile, $homeimgthumb, $link, $array_block_cat_module )
{
	global $db, $module_config;
	$error = "";
	$module = $module_config['getnews']['module'];
	$mod_data = str_replace( '-', '_', $module );
	if( $hometext != "" ) $keywords = nv_get_keywords( $hometext );
	else  $keywords = nv_get_keywords( nv_fil_tag( $bodytext ) );
	$row = array( "id" => "", "author" => "" );
	$hitstotal = rand( 1, 5 );
	$query = "INSERT INTO `" . NV_PREFIXLANG . "_" . $mod_data . "_rows` 
		(`id`, `catid`, `listcatid`, `topicid`, `admin_id`, `author`, `sourceid`, `addtime`, `edittime`, `status`, `publtime`, `exptime`, `archive`, `title`, `alias`, `hometext`, `homeimgfile`, `homeimgalt`, `homeimgthumb`, `inhome`, `allowed_comm`, `allowed_rating`, `hitstotal`, `hitscm`, `total_rating`, `click_rating`, `keywords`) VALUES 
		(NULL, 
		" . intval( $catid ) . ",
		" . $db->dbescape_string( $catid ) . ",
		" . intval( 0 ) . ",
		" . intval( 1 ) . ",
		" . $db->dbescape_string( $row['author'] ) . ",
		" . intval( $sourceid ) . ",
		" . intval( $publtime ) . ",
		" . intval( $publtime ) . ",
		" . intval( $module_config['getnews']['active'] ) . ",
		" . intval( $publtime ) . ",
		" . intval( 0 ) . ", 
		" . intval( 2 ) . ",
		" . $db->dbescape_string( $title ) . ",
		" . $db->dbescape_string( $alias ) . ",
		" . $db->dbescape_string( $hometext ) . ",
		" . $db->dbescape_string( $homefile ) . ",
		" . $db->dbescape_string( $title ) . ",
		" . $db->dbescape_string( $homeimgthumb ) . ",
		" . intval( 1 ) . ",  
		" . intval( 2 ) . ", 
		" . intval( 1 ) . ", 
		" . intval( $hitstotal ) . ",  
		" . intval( 0 ) . ",  
		" . intval( 0 ) . ",  
		" . intval( 0 ) . ",  
		" . $db->dbescape_string( $keywords ) . ")";
	$row['id'] = $db->sql_query_insert_id( $query );
	if( $row['id'] > 0 )
	{
		$bodytext2 = nv_news_get_bodytext( nv_convert( $bodytext ) );
		$ct_query = array();
		$tbhtml = NV_PREFIXLANG . "_" . $mod_data . "_bodyhtml_" . ceil( $row['id'] / 2000 );
		$db->sql_query( "CREATE TABLE IF NOT EXISTS `" . $tbhtml . "` (`id` int(11) unsigned NOT NULL, `bodyhtml` longtext NOT NULL, `sourcetext` varchar(255) NOT NULL default '', `imgposition` tinyint(1) NOT NULL default '1', `copyright` tinyint(1) NOT NULL default '0', `allowed_send` tinyint(1) NOT NULL default '0', `allowed_print` tinyint(1) NOT NULL default '0', `allowed_save` tinyint(1) NOT NULL default '0', PRIMARY KEY  (`id`)) ENGINE=MyISAM" );
		$ct_query[] = ( int )$db->sql_query( "INSERT INTO `" . $tbhtml . "` VALUES 
		(
			" . $row['id'] . ", 
			" . $db->dbescape_string( nv_convert( $bodytext ) ) . ", 
			" . $db->dbescape_string( $link ) . ",
			" . intval( $module_config['getnews']['imgposition'] ) . ",
			" . intval( 0 ) . ",  
			" . intval( 1 ) . ",  
			" . intval( 1 ) . ",  
			" . intval( 1 ) . "					
		)" );

		$ct_query[] = ( int )$db->sql_query( "INSERT INTO `" . NV_PREFIXLANG . "_" . $mod_data . "_" . $catid . "` SELECT * FROM `" . NV_PREFIXLANG . "_" . $module . "_rows` WHERE `id`=" . $row['id'] . "" );
		$ct_query[] = ( int )$db->sql_query( "INSERT INTO `" . NV_PREFIXLANG . "_" . $mod_data . "_bodytext` VALUES (" . $row['id'] . ", " . $db->dbescape_string( $bodytext2 ) . ")" );
		if( array_sum( $ct_query ) != sizeof( $ct_query ) )
		{
			$error .= "Lỗi khi lưu 1";
		}
		unset( $ct_query );
	}
	else
	{
		$error .= $query;
	}

	$id_block_content = explode( "|", $blockcat );
	$id_block_content = array_unique( $id_block_content );
	$id_block_content = array_filter( $id_block_content );
	foreach( $id_block_content as $bid_i )
	{
		$db->sql_query( "INSERT INTO `" . NV_PREFIXLANG . "_" . $mod_data . "_block` (`bid`, `id`, `weight`) VALUES ('" . $bid_i . "', '" . $row['id'] . "', '0')" );
	}

	$id_block_content[] = 0;
	$db->sql_query( "DELETE FROM `" . NV_PREFIXLANG . "_" . $mod_data . "_block` WHERE `id` = " . $row['id'] . " AND `bid` NOT IN (" . implode( ",", $id_block_content ) . ")" );
	$id_block_content = array_keys( $array_block_cat_module );
	foreach( $id_block_content as $bid_i )
	{
		nv_news_fix_blocks( $bid_i, false );
	}
	$db->sql_freeresult();
	unset( $bodytext );
	return $error;
}

// chuyen doi bang ma latin sang utf-8
function nv_convert( $string )
{
	$strings = str_replace( array(
		'&#192;',
		'&#193;',
		'&#194;',
		'&#200;',
		'&#201;',
		'&#202;',
		'&#204;',
		'&#205;',
		'&#208;',
		'&#210;',
		'&#211;',
		'&#212;',
		'&#213;',
		'&#217;',
		'&#218;',
		'&#221;',
		'&#224;',
		'&#225;',
		'&#226;',
		'&#227;',
		'&#232;',
		'&#233;',
		'&#234;',
		'&#236;',
		'&#237;',
		'&#242;',
		'&#243;',
		'&#244;',
		'&#249;',
		'&#250;',
		'&#253;',
		'&Agrave;',
		'&Aacute;',
		'&Acirc;',
		'&Egrave;',
		'&Eacute;',
		'&Ecirc;',
		'&Igrave;',
		'&Iacute;',
		'&ETH;',
		'&Ograve;',
		'&Oacute;',
		'&Ocirc;',
		'&Otilde;',
		'&Ugrave;',
		'&Uacute;',
		'&Yacute;',
		'&agrave;',
		'&aacute;',
		'&acirc;',
		'&#7927;',
		'&eacute;',
		'&ecirc;',
		'&igrave;',
		'&iacute;',
		'&ograve;',
		'&oacute;',
		'&ocirc;',
		'&ugrave;',
		'&uacute;',
		'&yacute;',
		'&#195;',
		'\'',
		'&amp;',
		'&#160;',
		'&#7893;',
		'&#7875;',
		'&#7901;',
		'&#7897;',
		'&#7843;',
		'&#160;',
		'&quot;',
		'&#7871;',
		'&#7879;',
		'&#7915;',
		'&#7911;',
		'&#7921;',
		'&#7845;',
		'&#273;',
		'&#7841;',
		'&#259;',
		'&#7849;',
		'&#7899;',
		'&rdquo;',
		'&ldquo;',
		'&#7853;',
		'&#7877;',
		'&#7847;',
		'&#7861;',
		'&#7907;',
		'&#7919;',
		'&#7909;',
		'&#7889;',
		'&#7873;',
		'&#7857;',
		'&#417;',
		'&#7859;',
		'&#7863;',
		'&#7865;',
		'&#7881;',
		'&#7895;',
		'&#7917;',
		'&#272;',
		'&#7913;',
		'&#7903;',
		'&#7867;',
		'&#7883;',
		'&#7885;',
		'&#7855;',
		'&#7869;',
		'&#432;',
		'&#7891;',
		'&#361;',
		'&#7887;',
		'&#7929;',
		'&#297;',
		'&#431;',
		'&gt;',
		'&lt;',
		'&amp;',
		'&mdash;',
		'&hellip;',
		'&egrave;',
		'&#7842;',
		'&#7851;',
		'&#7905;',
		'&#7844;',
		'&nbsp;',
		'&#7923;',
		'&#7870;',
		'&#7840;',
		'&atilde;',
		'&Atilde;',
		'&Aring;',
		'&copy;',
		'&#169;',
		'&reg;',
		'&#174;',
		'&#40;',
		'&#41;',
		'&#42;',
		'&#43;',
		'&#44;',
		'&#45;',
		'&#46;',
		'&#47;',
		'&#32;',
		'&#33;',
		'&#34;',
		'&#35;',
		'&#36;',
		'&#37;',
		'&#38;',
		'&#39;',
		'&#171',
		'&laquo;',
		'&#187;',
		'&raquo;',
		'&#8220;',
		'&#8221;',
		'&#8230;',
		'&#124;',
		'&#131;',
		'&#136;',
		'&#139;' ), array(
		'À',
		'Á',
		'Â',
		'È',
		'É',
		'Ê',
		'Ì',
		'Í',
		'Ð',
		'Ò',
		'Ó',
		'Ô',
		'Õ',
		'Ù',
		'Ú',
		'Ý',
		'à',
		'á',
		'â',
		'ã',
		'è',
		'é',
		'ê',
		'ì',
		'í',
		'ò',
		'ó',
		'ô',
		'ù',
		'ú',
		'ý',
		'À',
		'Á',
		'Â',
		'È',
		'É',
		'Ê',
		'Ì',
		'Í',
		'Ð',
		'Ò',
		'Ó',
		'Ô',
		'Õ',
		'Ù',
		'Ú',
		'Ý',
		'à',
		'á',
		'â',
		'ỷ',
		'é',
		'ê',
		'ì',
		'í',
		'ò',
		'ó',
		'ô',
		'ù',
		'ú',
		'ý',
		'Ã',
		'"',
		' ',
		' ',
		"ổ",
		'ể',
		'ờ',
		'ộ',
		'ả',
		'ể',
		'"',
		'ế',
		'ệ',
		'ừ',
		'ủ',
		'ự',
		'ấ',
		'đ',
		'ạ',
		'ă',
		'ẩ',
		'ớ',
		'”',
		'“',
		'ậ',
		'ễ',
		'ầ',
		'ẵ',
		'ợ',
		'ữ',
		'ụ',
		'ố',
		'ề',
		'ắ',
		'ơ',
		'ẳ',
		'ặ',
		'ẹ',
		'ỉ',
		'ỗ',
		'ử',
		'Đ',
		'ứ',
		'ở',
		'ẻ',
		'ị',
		'ọ',
		'ắ',
		'ẽ',
		'ư',
		'ồ',
		'ũ',
		'ỏ',
		'ỹ',
		'ĩ',
		'Ư',
		'>',
		'<',
		'&',
		'—',
		'…',
		'è',
		'Ả',
		'ẫ',
		'ỡ',
		'Ấ',
		' ',
		'ỳ',
		'Ế',
		'Ạ',
		'ã',
		'Ã',
		'Â',
		'©',
		'©',
		'®',
		'®',
		'(',
		')',
		'*',
		'+',
		',',
		'-',
		'.',
		'/',
		' ',
		'!',
		'"',
		'#',
		'$',
		'%',
		'&',
		'\'',
		'«',
		'«',
		'»',
		'»',
		'“',
		'”',
		'…',
		'|',
		'ƒ',
		'ˆ',
		'‹' ), $string );
	return $strings;
}

/**
 * nv_news_get_bodytext()
 * 
 * @param mixed $bodytext
 * @return
 */
function nv_news_get_bodytext( $bodytext )
{
	// Get image tags
	if( preg_match_all( "/\<img[^\>]*src=\"([^\"]*)\"[^\>]*\>/is", $bodytext, $match ) )
	{
		foreach( $match[0] as $key => $_m )
		{
			$textimg = "";
			if( strpos( $match[1][$key], 'data:image/png;base64' ) === false )
			{
				$textimg = " " . $match[1][$key];
			}
			if( preg_match_all( "/\<img[^\>]*alt=\"([^\"]+)\"[^\>]*\>/is", $_m, $m_alt ) )
			{
				$textimg .= " " . $m_alt[1][0];
			}
			$bodytext = str_replace( $_m, $textimg, $bodytext );
		}
	}
	// Get link tags
	if( preg_match_all( "/\<a[^\>]*href=\"([^\"]+)\"[^\>]*\>(.*)\<\/a\>/isU", $bodytext, $match ) )
	{
		foreach( $match[0] as $key => $_m )
		{
			$bodytext = str_replace( $_m, $match[1][$key] . " " . $match[2][$key], $bodytext );
		}
	}

	$bodytext = nv_unhtmlspecialchars( strip_tags( $bodytext ) );
	$bodytext = str_replace( "&nbsp;", " ", $bodytext );
	return preg_replace( "/[ ]+/", " ", $bodytext );
}

function nv_filter_tag( $html )
{
	$partner = '@<([a-zA-Z0-9]*)[^>]*>(.*?)</\1>@siu';
	return preg_replace( $partner, " ", $html );
	// xoa het tag html giu lai chu ko co chua ma html
}
/////////////////////////////////////////////////////

function nv_fil_tag( $html )
{
	$partner = array( "'</?[a-zA-Z0-9]*[^<>]*>'i", "/(&nbsp;)|(%)|(-)|(»)|(\/)|[0-9]/" );
	return preg_replace( $partner, "", $html );
	// xoa het tag html giu lai chu ngay trong va ngoai the html
}

//////////////////////////////////////////////////////
function nv_conver_utf8( $string )
{
	$string = mb_convert_encoding( $string, 'UTF-8', 'HTML-ENTITIES' );
	return $string;
}

//$string= '&#224;';// http://www.w3schools.com/tags/ref_entities.asp
//echo nv_conver_utf8($string);
//////////////////////////
// get noi dung web
function nv_get_url( $url )
{

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13' );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
		"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
		"Accept-Language: en-us,en;q=0.5",
		"Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7" ) );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$t = curl_exec( $ch );
	curl_close( $ch );
	return $t;
}

function nv_time( $date )
{
	return preg_replace( "/[^0-9]/", "", $date );
}

function nv_get_content( $filter1, $filter2, $html )
{
	$content = '';
	$filter = '#(?<=' . $filter1 . ').*(?=' . $filter2 . ')#imsU';
	$a = preg_match( $filter, $html, $co_non );
	if( $a ) $content = $co_non[0];
	return $content;
}

function nv_get_box( $filter1, $filter2, $html )
{

	$filter = '#(?<=' . $filter1 . ').*(?=' . $filter2 . ')#imsU';
	preg_match_all( $filter, $html, $co_non );
	return $co_non;
	// echo $co_non[0][$i];
}

function nv_href( $string )
{

	$patterns = '/href="([^"]*)"/';
	preg_match_all( $patterns, $string, $matches );
	$href = str_replace( array( 'href="', '"' ), array( '', '' ), $matches[0][0] );
	return $href;
}

function nv_get_image( $string )
{
	$pattern = '/src=("|\')([^"\'>]*)/';
	preg_match_all( $pattern, $string, $img );
	$image = preg_replace( '/(src)(="|=\')(.*)/i', "$3", $img[0][0] );
	return $image;
}

function nv_get_images( $string )
{
	$preg = preg_match_all( '#<\s*img [^\>]*src\s*=\s*(["\'])(.*?)\1#im', $string, $matches );
	if( $preg ) return $matches[2][0];
}

function img_link( $string, $urlimg )
{
	$string = preg_replace( '#(href|src)="([^:"]*)(?:")#', '$1="' . $urlimg . '$2"', $string );
	return $string;
}

function url_link( $string, $urlimg )
{
	/*** make sure there is an http:// on all URLs ***/
	//$str = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2",$str);
	/*** make all URLs links ***/
	//$str = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<a target=\"_blank\" href=\"$1\">$1</a>",$str);`
	$string = preg_replace( '#(href|src)="([^:"]*)(?:")#', '$1="' . $urlimg . '$2"', $string );
	return $string;
}

function nv_filter( $hinhanh )
{
	if( $hinhanh )
	{
		global $folder, $module, $module_config;
		require_once ( NV_ROOTDIR . "/includes/class/image.class.php" );
		$basename = basename( $hinhanh );
		$image = new image( $hinhanh, NV_MAX_WIDTH, NV_MAX_HEIGHT );

		// Creat new folder
		if( ! file_exists( NV_UPLOADS_REAL_DIR . '/' . $module . "/" . $folder ) )
		{
			nv_mkdir( NV_UPLOADS_REAL_DIR . '/' . $module, $folder );
		}
		$thumb_w = $module_config[$module]['homewidth'];
		$thumb_h = $module_config[$module]['homeheight'];
		$block_w = $module_config[$module]['blockwidth'];
		$block_h = $module_config[$module]['blockheight'];
		$img_w = $module_config['getnews']['width'];
		$img_h = $module_config['getnews']['height'];

		$name = $basename;
		$thumb_name = "";
		$block_name = "";

		$i = 1;
		while( file_exists( NV_UPLOADS_REAL_DIR . '/' . $module . "/" . $folder . '/' . $name ) )
		{
			$name = preg_replace( '/(.*)(\.[a-zA-Z]+)$/', '\1_' . $i . '\2', $basename );
			$i++;
		}

		$image_info1 = $image->fileinfo;
		$image_info = array();
		$homeimg = "";
		$homeimgthumb = "";
		// if( ( $image_info1['width'] / $image_info1['height'] ) < ( $img_w / $img_h ) )
		// {
		// $image->resizeXY( $img_w, NV_MAX_HEIGHT );
		// }
		// else
		// {
		// $image->resizeXY( NV_MAX_WIDTH, $img_h );
		// }
		// $image->cropFromLeft( 0, 0, $img_w, $img_h );

		// $image->save( NV_UPLOADS_REAL_DIR . '/' . $module . "/" . $folder, $name );
		// $image_info = $image->create_Image_info;

		$homeimg = str_replace( NV_UPLOADS_REAL_DIR . '/' . $module . '/', '', $hinhanh );

		$name = $basename;
		$i = 1;
		while( file_exists( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module . '/thumb/' . $name ) )
		{
			$name = preg_replace( '/(.*)(\.[a-zA-Z]+)$/', '\1_' . $i . '\2', $basename );
			$i++;
		}

		if( ( $image_info1['width'] / $image_info1['height'] ) < ( $thumb_w / $thumb_h ) )
		{
			$image->resizeXY( $thumb_w, NV_MAX_HEIGHT );
		}
		else
		{
			$image->resizeXY( NV_MAX_WIDTH, $thumb_h );
		}
		$image->cropFromCenter( $thumb_w, $thumb_h );

		$image->save( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module . '/thumb', $name );
		$image_info = $image->create_Image_info;

		$thumb_name = str_replace( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module . '/', '', $image_info['src'] );

		$name = $basename;
		$i = 1;
		while( file_exists( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module . '/block/' . $name ) )
		{
			$name = preg_replace( '/(.*)(\.[a-zA-Z]+)$/', '\1_' . $i . '\2', $basename );
			$i++;
		}

		if( ( $image_info1['width'] / $image_info1['height'] ) < ( $block_w / $block_h ) )
		{
			$image->resizeXY( $block_w, NV_MAX_HEIGHT );
		}
		else
		{
			$image->resizeXY( NV_MAX_WIDTH, $block_h );
		}
		$image->cropFromCenter( $block_w, $block_h );

		$image->save( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module . '/block', $name );
		if( $image_info ) $image_info = $image->create_Image_info;
		else  $image_info['src'] = "";
		$block_name = str_replace( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module . '/', '', $image_info['src'] );

		$image->close();

		if( ! empty( $thumb_name ) and ! empty( $block_name ) )
		{
			$homeimgthumb = $thumb_name . "|" . $block_name;
		}
	}
	else
	{
		$homeimg = "";
		$homeimgthumb = "";
	}
	$arr_img = array( $homeimg, $homeimgthumb );
	return $arr_img;
}

function zesize_img( $hinhanh )
{
	global $folder, $module, $module_config, $module_name;

	if( $hinhanh )
	{
		require_once ( NV_ROOTDIR . "/includes/class/image.class.php" );
		$basename = basename( $hinhanh );
		$image = new image( $hinhanh, NV_MAX_WIDTH, NV_MAX_HEIGHT );

		// Creat new folder
		if( ! file_exists( NV_UPLOADS_REAL_DIR . '/' . $module . "/" . $folder ) )
		{
			nv_mkdir( NV_UPLOADS_REAL_DIR . '/' . $module, $folder );
		}

		$name = $basename;

		$i = 1;
		while( file_exists( NV_UPLOADS_REAL_DIR . '/' . $module . "/" . $folder . '/' . $name ) )
		{
			$name = preg_replace( '/(.*)(\.[a-zA-Z]+)$/', '\1_' . $i . '\2', $basename );
			$i++;
		}

		$image_info1 = $image->fileinfo;
		$img_w = $module_config[$module_name]['width'];
		$img_h = $module_config[$module_name]['height'];
		$image_info = array();
		$homeimg = "";
		if( ( $image_info1['width'] / $image_info1['height'] ) < ( $img_w / $img_h ) )
		{
			$image->resizeXY( $img_w, NV_MAX_HEIGHT );
		}
		else
		{
			$image->resizeXY( NV_MAX_WIDTH, $img_h );
		}
		$image->cropFromLeft( 0, 0, $img_w, $img_h );

		$image->save( NV_UPLOADS_REAL_DIR . '/' . $module . "/" . $folder, $name );
		$image_info = $image->create_Image_info;

		$homeimg = str_replace( NV_UPLOADS_REAL_DIR . '/' . $module . '/', '', $image_info['src'] );
		$image->close();
	}
	else
	{
		$homeimg = "";
	}

	return $homeimg;
}

function stripwhitespace( $bff )
{
	$pzcr = 0;
	$pzed = strlen( $bff ) - 1;
	$rst = "";
	while( $pzcr < $pzed )
	{
		$t_poz_start = stripos( $bff, "<textarea", $pzcr );
		if( $t_poz_start === false )
		{
			$bffstp = substr( $bff, $pzcr );
			$temp = stripBuffer( $bffstp );
			$rst .= $temp;
			$pzcr = $pzed;
		}
		else
		{
			$bffstp = substr( $bff, $pzcr, $t_poz_start - $pzcr );
			$temp = stripBuffer( $bffstp );
			$rst .= $temp;
			$t_poz_end = stripos( $bff, "</textarea>", $t_poz_start );
			$temp = substr( $bff, $t_poz_start, $t_poz_end - $t_poz_start );
			$rst .= $temp;
			$pzcr = $t_poz_end;
		}
	}
	return html_compress( $rst );
}

function stripBuffer( $bff )
{
	/* carriage returns, new lines */
	$bff = str_replace( array(
		"\r\r\r",
		"\r\r",
		"\r\n",
		"\n\r",
		"\n\n\n",
		"\n\n" ), "\n", $bff );
	/* tabs */
	$bff = str_replace( array(
		"\t\t\t",
		"\t\t",
		"\t\n",
		"\n\t" ), "\t", $bff );
	/* opening HTML tags */
	$bff = str_replace( array(
		">\r<a",
		">\r <a",
		">\r\r <a",
		"> \r<a",
		">\n<a",
		"> \n<a",
		"> \n<a",
		">\n\n <a" ), "><a", $bff );
	$bff = str_replace( array( ">\r<b", ">\n<b" ), "><b", $bff );
	$bff = str_replace( array(
		">\r<d",
		">\n<d",
		"> \n<d",
		">\n <d",
		">\r <d",
		">\n\n<d" ), "><d", $bff );
	$bff = str_replace( array(
		">\r<f",
		">\n<f",
		">\n <f" ), "><f", $bff );
	$bff = str_replace( array(
		">\r<h",
		">\n<h",
		">\t<h",
		"> \n\n<h" ), "><h", $bff );
	$bff = str_replace( array(
		">\r<i",
		">\n<i",
		">\n <i" ), "><i", $bff );
	$bff = str_replace( array( ">\r<i", ">\n<i" ), "><i", $bff );
	$bff = str_replace( array(
		">\r<l",
		"> \r<l",
		">\n<l",
		"> \n<l",
		">  \n<l",
		"/>\n<l",
		"/>\r<l" ), "><l", $bff );
	$bff = str_replace( array( ">\t<l", ">\t\t<l" ), "><l", $bff );
	$bff = str_replace( array( ">\r<m", ">\n<m" ), "><m", $bff );
	$bff = str_replace( array( ">\r<n", ">\n<n" ), "><n", $bff );
	$bff = str_replace( array(
		">\r<p",
		">\n<p",
		">\n\n<p",
		"> \n<p",
		"> \n <p" ), "><p", $bff );
	$bff = str_replace( array( ">\r<s", ">\n<s" ), "><s", $bff );
	$bff = str_replace( array( ">\r<t", ">\n<t" ), "><t", $bff );
	/* closing HTML tags */
	$bff = str_replace( array( ">\r</a", ">\n</a" ), "></a", $bff );
	$bff = str_replace( array( ">\r</b", ">\n</b" ), "></b", $bff );
	$bff = str_replace( array( ">\r</u", ">\n</u" ), "></u", $bff );
	$bff = str_replace( array(
		">\r</d",
		">\n</d",
		">\n </d" ), "></d", $bff );
	$bff = str_replace( array( ">\r</f", ">\n</f" ), "></f", $bff );
	$bff = str_replace( array( ">\r</l", ">\n</l" ), "></l", $bff );
	$bff = str_replace( array( ">\r</n", ">\n</n" ), "></n", $bff );
	$bff = str_replace( array( ">\r</p", ">\n</p" ), "></p", $bff );
	$bff = str_replace( array( ">\r</s", ">\n</s" ), "></s", $bff );
	/* other */
	$bff = str_replace( array( ">\r<!", ">\n<!" ), "><!", $bff );
	$bff = str_replace( array( "\n<div" ), " <div", $bff );
	$bff = str_replace( array( ">\r\r \r<" ), "><", $bff );
	$bff = str_replace( array( "> \n \n <" ), "><", $bff );
	$bff = str_replace( array( ">\r</h", ">\n</h" ), "></h", $bff );
	$bff = str_replace( array( "\r<u", "\n<u" ), "<u", $bff );
	$bff = str_replace( array(
		"/>\r",
		"/>\n",
		"/>\t" ), "/>", $bff );
	//$bff=ereg_replace(" {2,}",' ',$bff);
	//$bff=ereg_replace("  {3,}",'  ',$bff);
	$bff = str_replace( "> <", "><", $bff );
	$bff = str_replace( "  <", "<", $bff );
	/* non-breaking spaces */
	$bff = str_replace( " &nbsp;", "&nbsp;", $bff );
	$bff = str_replace( "&nbsp; ", "&nbsp;", $bff );
	/* Example of EXCEPTIONS where I want the space to remain
	between two form buttons at */
	/* <!-- http://websitetips.com/articles/copy/loremgenerator/ --> */
	/* name="select" /> <input */
	$bff = str_replace( array( "name=\"select\" /><input" ), "name=\"select\" /> <input", $bff );

	return $bff;
}
function html_compress( $html )
{
	preg_match_all( '!(<(?:code|pre).*>[^<]+</(?:code|pre)>)!', $html, $pre );
	$html = preg_replace( '!<(?:code|pre).*>[^<]+</(?:code|pre)>!', '#pre#', $html );
	$html = preg_replace( '#<!–[^\[].+–>#', "", $html );
	$html = preg_replace( '/[\r\n\t]+/', ' ', $html );
	$html = preg_replace( '/>[\s]+</', '><', $html );
	// remote empty tag
	//$html = preg_replace('/<[^\/>]*>([\s]?)*<\/[^>]*>/', ' ', $html);
	//$html = preg_replace('/<[^\/>]*>([\s&nbsp;]?)*<\/[^>]*>/', ' ', $html);
	$html = preg_replace( '/<!--(.*)-->/Uis', '', $html );
	if( ! empty( $pre[0] ) )
		foreach( $pre[0] as $tag ) $html = preg_replace( '!#pre#!', $tag, $html, 1 );
	return trim( $html );
}

function delemptytag( $html )
{
	$html = preg_replace( '/<[^\/>]*>([\s&nbsp;]?)*<\/[^>]*>/', ' ', $html );
	return $html;
}

function nv_filters( $urlink, $urlimages, $tieude, $linktd, $tomtat, $hinhanh, $chitiet, $thoigian, $e )
{
	global $folder, $module, $module_name, $module_config, $global_config;
	$title = $e->find( $tieude, 0 )->innertext;
	$title = trim( nv_unhtmlspecialchars( strip_tags( $title ) ) );
	$link = $urlink . $e->find( $linktd, 0 )->href;
	$alias = change_alias( $title );
	if( $e->find( $tomtat, 0 ) )
	{
		$hometext = $e->find( $tomtat, 0 )->innertext;
		$hometext = trim( $hometext );
	}
	else  $hometext = '';

	$getContent = new UrlGetContents( $global_config );
	$link_t = $getContent->get( $link );
	$html = str_get_html( $link_t );

	$date = $html->find( $thoigian, 0 )->plaintext;
	$number = preg_replace( "/[^0-9]/", "", $date );

	$image = array();
	$homeimgthumb = "";
	if( $html->find( $chitiet, 0 )->find( 'img' ) )
		foreach( $html->find( $chitiet, 0 )->find( 'img' ) as $img )
		{
			if( ! nv_is_url( $img ) ) $abc = $img->src = $urlimages . $img->src;
			else  $abc = $img->src;
			$image[] = $abc;
			if( $module_config[$module_name]['load_image'] == 1 )
			{
				$images = filter_images( $folder, $abc );
				$img->src = NV_BASE_SITEURL . NV_UPLOADS_DIR . "/" . $module . "/" . $images;
			}

		}
	if( $html->find( $chitiet, 0 )->find( 'a' ) )
		foreach( $html->find( $chitiet, 0 )->find( 'a' ) as $href )
		{
			if( substr( $href->href, 0, 1 ) == "/" ) $href->href = $urlink . $href->href;

		}
	if( $image )
	{
		$homeimg = filter_images( $folder, $image[0] );
		$homeimgfile = NV_UPLOADS_REAL_DIR . "/" . $module . "/" . $homeimg;

		if( file_exists( $homeimgfile ) )
		{
			require_once ( NV_ROOTDIR . "/includes/class/image.class.php" );

			$basename = basename( $homeimgfile );
			$image = new image( $homeimgfile, NV_MAX_WIDTH, NV_MAX_HEIGHT );

			$thumb_basename = $basename;
			$i = 1;
			while( file_exists( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module . '/thumb/' . $thumb_basename ) )
			{
				$thumb_basename = preg_replace( '/(.*)(\.[a-zA-Z]+)$/', '\1_' . $i . '\2', $basename );
				++$i;
			}

			$image->resizeXY( $module_config[$module]['homewidth'], $module_config[$module]['homeheight'] );
			$image->save( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module . '/thumb', $thumb_basename );
			$image_info = $image->create_Image_info;
			$thumb_name = str_replace( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module . '/', '', $image_info['src'] );

			$block_basename = $basename;
			$i = 1;
			while( file_exists( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module . '/block/' . $block_basename ) )
			{
				$block_basename = preg_replace( '/(.*)(\.[a-zA-Z]+)$/', '\1_' . $i . '\2', $basename );
				++$i;
			}
			$image->resizeXY( $module_config[$module]['blockwidth'], $module_config[$module]['blockheight'] );
			$image->save( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module . '/block', $block_basename );
			$image_info = $image->create_Image_info;
			$block_name = str_replace( NV_ROOTDIR . '/' . NV_FILES_DIR . '/' . $module . '/', '', $image_info['src'] );

			$image->close();
			$homeimgthumb = $thumb_name . "|" . $block_name;
		}
	}
	else
	{

		if( $e->find( $hinhanh, 0 ) )
		{

			$homeimg1 = $e->find( $hinhanh, 0 )->src;
			if( nv_is_url( $homeimg1 ) ) $home_img = $urlimages . $homeimg1;
			else  $home_img = $homeimg1;
			if( $module_config[$module_name]['load_image'] == 1 ) $homeimg = filter_images( $folder, $home_img );
			else  $homeimg = $home_img;
		}
		else
		{
			$homeimg = "";
		}
	}

	$bodyhtml = $html->find( $chitiet, 0 )->innertext;
	$html->clear();
	unset( $html );
	$bodyhtml = nv_unhtmlspecialchars( $bodyhtml );
	$content = array(
		$title,
		$link,
		$alias,
		$hometext,
		$homeimg,
		$homeimgthumb,
		$number,
		$bodyhtml );
	return $content;
}
function load_image( $bodytext, $urlink )
{
	global $folder, $module_config, $module;

	$bodytext = str_get_html( $bodytext );
	foreach( $bodytext->find( 'img' ) as $img )
	{
		if( ! file_exists( NV_UPLOADS_REAL_DIR . '/' . $module . "/" . $folder ) )
		{
			nv_mkdir( NV_UPLOADS_REAL_DIR . '/' . $module, $folder );
		}

		// if( ! nv_is_url( $img ) )
		// $abc = $img->src = $urlink . $img->src;
		// else  $abc = $img->src;

		$abc = str_replace( ' ', '%20', $img->src );

		$images = filter_images( $folder, $abc );
		$img->src = NV_BASE_SITEURL . NV_UPLOADS_DIR . "/" . $module . "/" . $images;
	}
	return $bodytext;
}

function resizeimg( $folder, $linkanh )
{
	global $global_config, $module, $module_name, $module_config;
	require_once ( NV_ROOTDIR . "/includes/class/image.class.php" );
	$basename = basename( $linkanh );
	$i = 1;
	while( file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_config[$module_name]['module'] . '/' . $folder . '/' . $basename ) )
	{
		$basename = preg_replace( '/(.*)(\.[a-zA-Z]+)$/', '\1_' . $i . '\2', $basename );
		$i++;
	}
	$image = new image( $linkanh );
	$image_info1 = $image->fileinfo;
	if( ( $image_info1['width'] > $module_config[$module_name]['bodyimg'] ) )
	{
		$image->resizeXY( $module_config[$module_name]['bodyimg'], NV_MAX_HEIGHT );
	}
	$thumb_basename = $basename;
	$image->save( NV_UPLOADS_REAL_DIR . '/' . $module_config[$module_name]['module'] . '/' . $folder, $thumb_basename );
	$image_info = $image->create_Image_info;
	$img = str_replace( NV_UPLOADS_REAL_DIR . '/' . $module_config[$module_name]['module'] . '/', '', $image_info['src'] );
	$image->close();
	return $img;
}

function filter_images( $folder, $linkanh )
{
	global $global_config, $module;
	if( ! file_exists( NV_UPLOADS_REAL_DIR . '/' . $module . "/" . $folder ) )
	{
		nv_mkdir( NV_UPLOADS_REAL_DIR . '/' . $module, $folder );
	}
	$path = NV_UPLOADS_DIR . "/" . $module . "/" . $folder . "";
	require_once ( NV_ROOTDIR . "/includes/class/upload.class.php" );
	$upload = new upload( array( 'images' ), $global_config['forbid_extensions'], $global_config['forbid_mimes'], NV_UPLOAD_MAX_FILESIZE, NV_MAX_WIDTH, NV_MAX_HEIGHT );
	$upload_info = $upload->save_urlfile( $linkanh, NV_ROOTDIR . '/' . $path, false );
	$home_file = $folder . "/" . $upload_info['basename'];
	sleep( 1 );
	return $home_file;
}

function optimize()
{
	global $db;
	$res = $db->sql_query( 'SHOW TABLE STATUS WHERE Data_free >0' );
	while( $row = $db->sql_fetch_assoc( $res ) )
	{
		$db->sql_query( 'OPTIMIZE TABLE ' . $row['Name'] );
	}
	return true;
}

function change( $text )
{
	$text = nv_unhtmlspecialchars( trim( nv_convert( strip_tags( $text ) ) ) );
	$text = str_replace( array(
		'"',
		'>',
		'<',
		'´',
		'`' ), array(
		'&quot;',
		'&gt;',
		'&lt;',
		'&#180;',
		'&#96;' ), $text );
	//if($strip==1)
	//{
	//$text = strip_tags($text);
	//}
	return $text;
}


function nv_remove_emptytag( $html )
{
	$html = preg_replace( '/<[^\/>]*>([\s&nbsp;]?)*<\/[^>]*>/', ' ', $html );
	return $html;
}

function curl($url) {
	$ch = @curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	$head[] = "Connection: keep-alive";
	$head[] = "Keep-Alive: 300";
	$head[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
	$head[] = "Accept-Language: en-us,en;q=0.5";
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36');
	curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
	$page = curl_exec($ch);
	curl_close($ch);
	return $page;
}

function check_link($url,$host='')
{
	if((nv_is_url($url)===false) and (preg_match_all('/^(http|https|ftp|gopher)\:\/\/(.*)\.([a-z]+)/',$host,$matches,PREG_SET_ORDER)))
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

function dom_html_file($url){
	global $module_upload;
	if( !empty($url)){
		$me = curl($url);
		$file = NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . md5($url) . '.html';
		$current = curl($url);
		file_put_contents($file, $current);
		$html = file_get_html($file);
		unlink($file);
		return $html;
	}
}

function nv_get_firstimage( $contents ){
	if( preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $contents, $img) ){
		return $img[1];
	}else{
		return '';
	}
}