<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
require_once("include/config.php");

    if (!@mysql_connect($INSTALLER09['mysql_host'], $INSTALLER09['mysql_user'], $INSTALLER09['mysql_pass']))
    {
      exit();
    }
      
    @mysql_select_db($INSTALLER09['mysql_db']) or exit();

    if ( !isset($_GET['info_hash']) OR (strlen($_GET['info_hash']) != 20) )
      error('Invalid hash');

    $res = @mysql_query( "SELECT info_hash, seeders, leechers, times_completed FROM torrents WHERE " . hash_where( $_GET['info_hash']) );
    
    if( !mysql_num_rows($res) )
      error('No torrent with that hash found');
    
    $benc = 'd5:files';

    while ($row = mysql_fetch_assoc($res))
    {
    $benc .= 'd20:'.pack('H*', $row['info_hash'])."d8:completei{$row['seeders']}e10:downloadedi{$row['times_completed']}e10:incompletei{$row['leechers']}eeee";
    }

    
    header('Content-Type: text/plain; charset=UTF-8');
    header('Pragma: no-cache');
    print($benc);


function error($err){

    header('Content-Type: text/plain; charset=UTF-8');
    header('Pragma: no-cache');
    exit("d14:failure reason".strlen($err).":{$err}ed5:flagsd20:min_request_intervali1800eeee");
    
}

function hash_where($hash) {

    return "info_hash = '" . mysql_real_escape_string( bin2hex($hash) ) . "'";

}

?>