<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');

    $lang = array_merge( load_language('global'), load_language('confirmemail') );
    
    if ( !isset($_GET['uid']) OR !isset($_GET['key']) OR !isset($_GET['email']) )
      stderr("{$lang['confirmmail_user_error']}", "{$lang['confirmmail_idiot']}");

    if (! preg_match( "/^(?:[\d\w]){32}$/", $_GET['key'] ) )
		{
			stderr( "{$lang['confirmmail_user_error']}", "{$lang['confirmmail_no_key']}" );
		}
		
		if (! preg_match( "/^(?:\d){1,}$/", $_GET['uid'] ) )
		{
			stderr( "{$lang['confirmmail_user-error']}", "{$lang['confirmmail_no_id']}" );
		}

    $id = intval($_GET['uid']);
    $md5 = $_GET['key'];
    $email = urldecode($_GET['email']);
    
    if( !validemail($email) )
      stderr("{$lang['confirmmail_user_error']}", "{$lang['confirmmail_false_email']}");

dbconn();


    $res = mysql_query("SELECT editsecret FROM users WHERE id = $id");
    $row = mysql_fetch_assoc($res);

    if (!$row)
      stderr("{$lang['confirmmail_user_error']}", "{$lang['confirmmail_not_complete']}");

    $sec = $row['editsecret'];
    if (preg_match('/^ *$/s', $sec))
      stderr("{$lang['confirmmail_user_error']}", "{$lang['confirmmail_not_complete']}");
      
    if ($md5 != md5($sec . $email . $sec))
      stderr("{$lang['confirmmail_user_error']}", "{$lang['confirmmail_not_complete']}");

   @mysql_query("UPDATE users SET editsecret='', email=" . sqlesc($email) . " WHERE id=$id AND editsecret=" . sqlesc($row["editsecret"]));

    if (!mysql_affected_rows())
      stderr("{$lang['confirmmail_user_error']}", "{$lang['confirmmail_not_complete']}");

    header("Refresh: 0; url={$INSTALLER09['baseurl']}/usercp.php?emailch=1");


?>