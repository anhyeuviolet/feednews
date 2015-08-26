<?php

/**
 * @Project FEEDNEWS ON NUKEVIET 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 08/25/2015 10:27
 */

if( ! defined( 'NV_ADMIN' ) ) die( 'Stop!!!' );

// Dump data
$db->query( "INSERT INTO ".$db_config['prefix']."_".$lang."_".$module_data."_site_structure (id, site_id, field_name, extra, element_delete, string_delete) VALUES
(1, 1, 'title', 'h1', '', ''),
(2, 1, 'hometext', '.short_intro', '', ''),
(3, 1, 'bodyhtml', '.fck_detail', '', ''),
(4, 1, 'homeimgalt', 'h1', '', '')");

$db->query( "INSERT INTO ".$db_config['prefix']."_".$lang."_".$module_data."_site (
id, name, host, url, extra, count, table_name, get_image, image_pattern, image_content_left, image_content_right, pattern_bound, catid, status, page_num, image_dir, sourceid, begin, end, bid, cat_title) 
VALUES
(1, 'Số hoá - VNEXPRESS', 'http://sohoa.vnexpress.net/', 'http://sohoa.vnexpress.net/tin-tuc/san-pham',
 'a.txt_link', '5', 'news', 1, '.thumb img', '', '', '.block_image_news', 2, 1, '', '', 1, '.block_image_news width_common',
 'news', '2', 'Sản phẩm')");