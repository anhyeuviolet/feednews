<?php

/**
 * @Project FEEDNEWS 3.3.01
 * @Author MINHTC.NET (hunters49@gmail.com)
 * @Copyright (C) 2013 MINHTC.NET All rights reserved
 * @Createdate Sun, 28 Jul 2013 00:57:11 GMT
 */

if ( ! defined( 'NV_IS_FILE_SITEINFO' ) ) die( 'Stop!!!' );


$lang_siteinfo = nv_get_lang_module( $mod );
/*
// Tong so bai viet 
list( $number ) = $db->sql_fetchrow( $db->sql_query( "SELECT COUNT(*) as number FROM `" . NV_PREFIXLANG . "_" . $mod_data . "_rows` where `status`= 1 AND `publtime` < " . NV_CURRENTTIME . " AND (`exptime`=0 OR `exptime`>" . NV_CURRENTTIME . ")" ) );
if ( $number > 0 )
{
    $siteinfo[] = array( 
        'key' => $lang_siteinfo['siteinfo_publtime'], 'value' => $number 
    );
}
*/
?>