<?php

/**
 * @Project FEEDNEWS FOR NUKEVIET 4
 * @Author FORUM.NUKEVIET.VN

 * @Created Wed, 01 Jul 2015 18:00:00 GMT
 */

if ( ! defined( 'NV_IS_MOD_CRAWLER' ) ) die( 'Stop!!!' );

/**
 * nv_theme_crawler_main()
 * 
 * @param mixed $array_data
 * @return
 */
function nv_theme_crawler_main ( $array_data )
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op;

    $xtpl = new XTemplate( $op . ".tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
    $xtpl->assign( 'LANG', $lang_module );

    

    $xtpl->parse( 'main' );
    return $xtpl->text( 'main' );
}

/**
 * nv_theme_crawler_detail()
 * 
 * @param mixed $array_data
 * @return
 */
function nv_theme_crawler_detail ( $array_data )
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op;

    $xtpl = new XTemplate( $op . ".tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
    $xtpl->assign( 'LANG', $lang_module );

    

    $xtpl->parse( 'main' );
    return $xtpl->text( 'main' );
}

/**
 * nv_theme_crawler_search()
 * 
 * @param mixed $array_data
 * @return
 */
function nv_theme_crawler_search ( $array_data )
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op;

    $xtpl = new XTemplate( $op . ".tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
    $xtpl->assign( 'LANG', $lang_module );

    

    $xtpl->parse( 'main' );
    return $xtpl->text( 'main' );
}

