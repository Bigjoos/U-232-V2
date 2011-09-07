<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
/*
+------------------------------------------------
|   $Date$ 181010
|   $Revision$ 2.0
|   $Author$ laffin-stonebreath
|   $update09 Bigjoos
|   $URL$
|   $qlogin
|   
+------------------------------------------------
*/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
dbconn();
loggedinorreturn();
$lang = array_merge( load_language('global') );

if ($CURUSER['class'] < UC_MODERATOR)
stderr("No Permision", "system file");
$id = 0 + $_GET['id'];
if (!is_valid_id($id))
die();
$action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';
$res = sql_query("SELECT hash1, username, passhash FROM users WHERE id = ".sqlesc($id)." AND class >= ".UC_MODERATOR) or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_assoc($res);
   $hash1 = md5($arr['username'].time().$arr['passhash']);
   $hash2 = md5($hash1.time().$arr['username']);
   $hash3 = md5($hash1.$hash2.$arr['passhash']);
   $hash1.=$hash2.$hash3;
if ($action == 'reset') {
$sure = isset($_GET['sure']) ? (int)($_GET['sure']) : 0;
if ($sure != '1')
stderr("Sanity check...", "You are about to reset your login link: Click <a href='createlink.php?action=reset&amp;id=$id&amp;sure=1'>here</a> if you are sure.");
sql_query("UPDATE users SET hash1 = ".sqlesc($hash1)." WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$mc1->delete_value('user'.$id);
$mc1->delete_value('hash1_'.$id);
header("Refresh: 1; url={$INSTALLER09['baseurl']}/userdetails.php?id=$id");
stderr("Success", "Your login link reset successfully.");
} else {
if ($arr['hash1'] === '') {
sql_query("UPDATE users SET hash1 = ".sqlesc($hash1)." WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
header("Refresh: 2; url={$INSTALLER09['baseurl']}/userdetails.php?id=$id");
$mc1->delete_value('user'.$id);
$mc1->delete_value('hash1_'.$id);
stderr('Success', "Your login link was created successfully.");
} else {
header("Refresh: 2; url={$INSTALLER09['baseurl']}/userdetails.php?id=$id");
stderr('Error', "You have already created your login link.");
}
}
?>