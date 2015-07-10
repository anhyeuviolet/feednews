<?php

/**
 * @Project FEEDNEWS ON NUKEVIET 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 07/30/2013 10:27
 */
 
if ( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

$sql_drop_module = array();
$sql_drop_module[] = "DROP TABLE IF EXISTS ".$db_config['prefix']."_".$lang."_".$module_data."_site";

$sql_drop_module[] = "DROP TABLE IF EXISTS ".$db_config['prefix']."_".$lang."_".$module_data."_site_structure";


$sql_create_module = $sql_drop_module;
$sql_create_module[] = "CREATE TABLE ".$db_config['prefix']."_".$lang."_".$module_data."_site (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(250) NOT NULL,
  host tinytext NOT NULL,
  url varchar(200) NOT NULL,
  extra varchar(250) NOT NULL,
  count tinytext NOT NULL,
  table_name varchar(250) NOT NULL,
  get_image tinyint(4) NOT NULL DEFAULT '1',
  image_pattern varchar(250) NOT NULL,
  image_content_left varchar(255) NOT NULL,
  image_content_right varchar(255) NOT NULL,
  pattern_bound varchar(255) NOT NULL,
  catid int(11) NOT NULL,
  status tinyint(4) NOT NULL DEFAULT '1',
  page_num varchar(255) NOT NULL,
  image_dir varchar(255) NOT NULL,
  sourceid int(11) NOT NULL,
  begin varchar(255) NOT NULL,
  end varchar(255) NOT NULL,
  bid varchar(255) NOT NULL,
  cat_title varchar(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE ".$db_config['prefix']."_".$lang."_".$module_data."_site_structure (
  id int(11) NOT NULL AUTO_INCREMENT,
  site_id int(11) NOT NULL,
  field_name varchar(200) NOT NULL,
  extra varchar(250) NOT NULL,
  element_delete varchar(255) NOT NULL,
  string_delete tinytext NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM;";

// Dump data
$sql_create_module[] = "INSERT INTO ".$db_config['prefix']."_".$lang."_".$module_data."_site_structure (id, site_id, field_name, extra, element_delete, string_delete) VALUES
(1, 1, 'title', 'h1', '', ''),
(2, 1, 'hometext', 'short_intro txt_666', '', ''),
(3, 1, 'bodyhtml', '.fck_detail', '', ''),
(4, 1, 'homeimgalt', 'h1', '', '')";

$sql_create_module[] = "INSERT INTO ".$db_config['prefix']."_".$lang."_".$module_data."_site (
id, name, host, url, extra, count, table_name, get_image, image_pattern, image_content_left, image_content_right, pattern_bound, catid, status, page_num, image_dir, sourceid, begin, end, bid, cat_title) 
VALUES
(1, 'Số hoá - VNEXPRESS', 'http://sohoa.vnexpress.net/', 'http://sohoa.vnexpress.net/tin-tuc/san-pham',
 'a.txt_link', '5', 'news', 1, '.thumb img', '', '', '.block_image_news', 2, 1, '', '', 1, '.block_image_news width_common',
 'news', '2', 'Sản phẩm')";