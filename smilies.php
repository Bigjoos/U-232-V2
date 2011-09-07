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
require_once(INCL_DIR.'emoticons.php');
require_once(INCL_DIR.'html_functions.php');
dbconn(false);
loggedinorreturn();

    $lang = load_language('global');
    
    $HTMLOUT = stdhead();
    $HTMLOUT .= begin_main_frame();
    $HTMLOUT .= insert_smilies_frame();
    $HTMLOUT .= end_main_frame();
    $HTMLOUT .= stdfoot();
    print $HTMLOUT ;
?>