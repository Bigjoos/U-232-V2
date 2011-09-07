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
require_once INCL_DIR.'pager_functions.php';
require_once INCL_DIR.'torrenttable_functions.php';
require_once INCL_DIR.'html_functions.php';
dbconn(false);

loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('mytorrents') , load_language( 'torrenttable_functions' )); 
    $stdfoot = array(/** include js **/'js' => array('java_klappe','wz_tooltip'));
    $HTMLOUT = '';

    if (isset($_GET['sort']) && isset($_GET['type'])) {
    $column = '';
    $ascdesc = '';

   $_valid_sort = array('id','name','numfiles','comments','added','size','times_completed','seeders','leechers','owner');
   $column = isset($_GET['sort']) && isset($_valid_sort[(int)$_GET['sort']]) ? $_valid_sort[(int)$_GET['sort']] : $_valid_sort[0];

    switch (htmlspecialchars($_GET['type'])) {
        case 'asc': $ascdesc = "ASC";
            $linkascdesc = "asc";
            break;
        case 'desc': $ascdesc = "DESC";
            $linkascdesc = "desc";
            break;
        default: $ascdesc = "DESC";
            $linkascdesc = "desc";
            break;
    }

    $orderby = "ORDER BY torrents." . $column . " " . $ascdesc;
    $pagerlink = "sort=" . intval($_GET['sort']) . "&amp;type=" . $linkascdesc . "&amp;";
    } else {
    $orderby = "ORDER BY torrents.sticky ASC, torrents.id DESC";
    $pagerlink = "";
    }

    $where = "WHERE owner = {$CURUSER["id"]} AND banned != 'yes'";
    $res = sql_query("SELECT COUNT(id) FROM torrents $where");
    $row = mysql_fetch_array($res,MYSQL_NUM);
    $count = $row[0];

    if (!$count) 
    {

      $HTMLOUT .= "{$lang['mytorrents_no_torrents']}";
      $HTMLOUT .= "{$lang['mytorrents_no_uploads']}";

    }
    else 
    {
      $pager = pager(20, $count, "mytorrents.php?{$pagerlink}");

      $res = sql_query("SELECT torrents.type, torrents.sticky, torrents.vip, torrents.descr, torrents.nuked, torrents.nukereason, torrents.release_group, torrents.free, torrents.comments, torrents.leechers, torrents.seeders, IF(torrents.numratings < {$INSTALLER09['minvotes']}, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.id, categories.name AS cat_name, categories.image AS cat_pic, torrents.name, save_as, numfiles, added, size, views, visible, hits, times_completed, category,  b.id as bookmark, freeslots.tid, freeslots.uid, freeslots.free AS freeslot, freeslots.double AS doubleup FROM torrents LEFT JOIN bookmarks as b ON torrents.id=b.torrentid AND b.userid={$CURUSER["id"]} LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN freeslots ON (torrents.id=freeslots.tid)$where $orderby ".$pager['limit']) or sqlerr(__FILE__, __LINE__);  

      $HTMLOUT .= $pager['pagertop'];

      $HTMLOUT .= torrenttable($res, "mytorrents");

      $HTMLOUT .= $pager['pagerbottom'];
    }

    echo stdhead($CURUSER["username"] . "'s torrents") . $HTMLOUT . stdfoot($stdfoot);

?>