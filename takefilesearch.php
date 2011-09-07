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

dbconn(false);

loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('takefilesearch') );

    if(isset($_POST['search']) && !empty($_POST['search'])) {
      
      $cleansearchstr = sqlesc($_POST['search']);
      //print $cleansearchstr;
      }
      else
      stderr($lang['tfilesearch_oops'], $lang['tfilesearch_nuffin']);


    $query = sql_query("SELECT id, filename, MATCH (filename)
                AGAINST ($cleansearchstr IN BOOLEAN MODE) AS score
                FROM files WHERE MATCH (filename) AGAINST ($cleansearchstr IN BOOLEAN MODE)
                ORDER BY score DESC");

    if(mysql_num_rows($query) == 0)
      stderr($lang['tfilesearch_error'], $lang['tfilesearch_nothing']);

    $HTMLOUT = '';
  	
    $HTMLOUT .= begin_table();

    $HTMLOUT .= "<tr>
    <td class='colhead'>{$lang['tID']}</td>
    <td class='colhead' align='left'>{$lang['tfilename']}</td>
    <td class='colhead' align='left'>{$lang['tscore']}</td>";
    
    while($row = mysql_fetch_assoc($query)) 
    {
      $HTMLOUT .= "<tr><td>{$row['id']}</td><td>".htmlspecialchars($row['filename'])."</td><td>{$row['score']}</td></tr>";
    }
    
    $HTMLOUT .= end_table();
    
    print stdhead($lang['tstdhead']) . $HTMLOUT . stdfoot();
?>