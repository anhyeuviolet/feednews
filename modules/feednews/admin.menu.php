<?php

/**
 * @Project FEEDNEWS ON NUKEVIET 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 07/30/2013 10:27
 */

if( ! defined( 'NV_ADMIN' ) ) die( 'Stop!!!' );


$allow_func = array( 'main', 'add_site_structure', 'copy_site_structure', 'edit_site_structure', 'temp_site_structure', );

$submenu['main'] = $lang_module['main'];
$submenu['add_site_structure'] = $lang_module['add_site_structure'];


$allow_func[] = 'main';

$allow_func[] = 'add_site_structure';
$allow_func[] = 'copy_site_structure';
$allow_func[] = 'edit_site_structure';
$allow_func[] = 'temp_site_structure';