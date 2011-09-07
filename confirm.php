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

    $lang = array_merge( load_language('global'), load_language('confirm') );
    
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $md5 = isset($_GET['secret']) ? $_GET['secret'] : '';

    if (!is_valid_id($id))
      stderr("{$lang['confirm_user_error']}", "{$lang['confirm_invalid_id']}");
    
    if (! preg_match( "/^(?:[\d\w]){32}$/", $md5 ) )
		{
			stderr("{$lang['confirm_user_error']}", "{$lang['confirm_invalid_key']}");
		}
		
    dbconn();


    $res = @mysql_query("SELECT passhash, editsecret, status FROM users WHERE id = $id");
    $row = @mysql_fetch_assoc($res);

    if (!$row)
      stderr("{$lang['confirm_user_error']}", "{$lang['confirm_invalid_id']}");

    if ($row['status'] != 'pending') 
    {
      header("Refresh: 0; url={$INSTALLER09['baseurl']}/ok.php?type=confirmed");
      exit();
    }

    $sec = $row['editsecret'];
    if ($md5 != $sec)
      stderr("{$lang['confirm_user_error']}", "{$lang['confirm_cannot_confirm']}");

    @mysql_query("UPDATE users SET status='confirmed', editsecret='' WHERE id=$id AND status='pending'");

    if (!mysql_affected_rows())
      stderr("{$lang['confirm_user_error']}", "{$lang['confirm_cannot_confirm']}");
    
    $passh = md5($row["passhash"].$_SERVER["REMOTE_ADDR"]);
    logincookie($id, $passh);
    
    header("Refresh: 0; url={$INSTALLER09['baseurl']}/ok.php?type=confirm");

?>