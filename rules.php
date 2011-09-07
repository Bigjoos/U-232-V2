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
loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('rules') );

    $HTMLOUT = '';
    
    $HTMLOUT .= begin_main_frame();

   $HTMLOUT .= begin_frame("{$lang['rules_general_header']}");
   $HTMLOUT .= "{$lang['rules_general_body']}";
    $HTMLOUT .= end_frame();
    
    $HTMLOUT .= begin_frame("{$lang['rules_downloading_header']}");
    $HTMLOUT .= "{$lang['rules_downloading_body']}";
    $HTMLOUT .= end_frame();
    
    $HTMLOUT .= begin_frame("{$lang['rules_forum_header']}");
    $HTMLOUT .= "{$lang['rules_forum_body']}";
    $HTMLOUT .= end_frame();
    
    $HTMLOUT .= begin_frame("{$lang['rules_avatar_header']}");
    $HTMLOUT .= "{$lang['rules_avatar_body']}";
    $HTMLOUT .= end_frame();

    if (isset($CURUSER) AND $CURUSER['class'] >= UC_UPLOADER) 
    {

      $HTMLOUT .= begin_frame("{$lang['rules_uploading_header']}");
      $HTMLOUT .= "{$lang['rules_uploading_body']}";

      $HTMLOUT .= end_frame();

    }
    
    if (isset($CURUSER) AND $CURUSER['class'] >= UC_MODERATOR) 
    {

     $HTMLOUT .= begin_frame("{$lang['rules_moderating_header']}");
     $HTMLOUT .= "<br />
      <table border='0' cellspacing='3' cellpadding='0'>
      {$lang['rules_moderating_body']}
      </table>
      <br />";

      $HTMLOUT .= end_frame();
      $HTMLOUT .= begin_frame("{$lang['rules_mod_rules_header']}");

      $HTMLOUT .= "{$lang['rules_mod_rules_body']}";


      $HTMLOUT .= end_frame();
      $HTMLOUT .= begin_frame("{$lang['rules_mod_options_header']}");

      $HTMLOUT .= "{$lang['rules_mod_options_body']}";

      $HTMLOUT .= end_frame(); 
    }
    
    $HTMLOUT .= end_main_frame();
    
    print stdhead("{$lang['rules_rules']}") . $HTMLOUT . stdfoot();
?>