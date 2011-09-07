<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
require_once(INCL_DIR.'html_functions.php');


dbconn();
    
    $lang = array_merge( load_language('global'), load_language('useragreement') );
    
    $HTMLOUT = '';
    
    $HTMLOUT .= begin_main_frame();
    $HTMLOUT .= begin_frame($INSTALLER09['site_name']." {$lang['frame_usragrmnt']}");

    $HTMLOUT .= "<p></p> {$lang['text_usragrmnt']}"; 

    $HTMLOUT .= end_frame();
    $HTMLOUT .= end_main_frame();
    print stdhead("{$lang['stdhead_usragrmnt']}") . $HTMLOUT . stdfoot();
?>