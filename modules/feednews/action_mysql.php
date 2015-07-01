<?php

/**
 * @Project FEEDNEWS FOR NUKEVIET 4
 * @Author FORUM.NUKEVIET.VN

 * @Created Wed, 01 Jul 2015 18:00:00 GMT
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

