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

dbconn();

loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('delete') );

    if (!mkglobal("id"))
      stderr("{$lang['delete_failed']}", "{$lang['delete_missing_data']}");

    $id = 0 + $id;
    if (!is_valid_id($id))
      stderr("{$lang['delete_failed']}", "{$lang['delete_missing_data']}");
      
function deletetorrent($id) {
    global $INSTALLER09, $mc1, $CURUSER, $lang;
    sql_query("DELETE FROM torrents WHERE id = $id");
    sql_query("DELETE FROM coins WHERE torrentid = $id");
    sql_query("DELETE FROM bookmarks WHERE torrentid = $id");
    sql_query("DELETE FROM snatched WHERE torrentid = $id");
    foreach(explode(".","peers.files.comments.ratings") as $x)
    @mysql_query("DELETE FROM $x WHERE torrent = $id");
    unlink("{$INSTALLER09['torrent_dir']}/$id.torrent");
    $mc1->delete_value('MyPeers_'.$CURUSER['id']);
    }

$res = sql_query("SELECT name,owner,seeders FROM torrents WHERE id = $id");
$row = mysql_fetch_assoc($res);
if (!$row)
	stderr("{$lang['delete_failed']}", "{$lang['delete_not_exist']}");

if ($CURUSER["id"] != $row["owner"] && $CURUSER["class"] < UC_MODERATOR)
	stderr("{$lang['delete_failed']}", "{$lang['delete_not_owner']}\n");

$rt = 0 + $_POST["reasontype"];

if (!is_int($rt) || $rt < 1 || $rt > 5)
	bark("{$lang['delete_invalid']}");

//$r = $_POST["r"]; // whats this
$reason = $_POST["reason"];

if ($rt == 1)
	$reasonstr = "{$lang['delete_dead']}";
elseif ($rt == 2)
	$reasonstr = "{$lang['delete_dupe']}" . ($reason[0] ? (": " . trim($reason[0])) : "!");
elseif ($rt == 3)
	$reasonstr = "{$lang['delete_nuked']}" . ($reason[1] ? (": " . trim($reason[1])) : "!");
elseif ($rt == 4)
{
	if (!$reason[2])
		stderr("{$lang['delete_failed']}", "{$lang['delete_violated']}");
  $reasonstr = $INSTALLER09['site_name']."{$lang['delete_rules']}" . trim($reason[2]);
}
else
{
	if (!$reason[3])
		stderr("{$lang['delete_failed']}", "{$lang['delete_reason']}");
  $reasonstr = trim($reason[3]);
}

    deletetorrent($id);
    //$mc1->delete_value('lastest_tor_');
    $mc1->delete_value('top5_tor_');
    $mc1->delete_value('last5_tor_');
    write_log("{$lang['delete_torrent']} $id ({$row['name']}){$lang['delete_deleted_by']}{$CURUSER['username']} ($reasonstr)\n");
    //===remove karma 
    sql_query("UPDATE users SET seedbonus = seedbonus-15.0 WHERE id = ".sqlesc($row["owner"])."") or sqlerr(__FILE__, __LINE__);
    //===end
    if ($CURUSER["id"] != $row["owner"] AND $CURUSER['pm_on_delete'] == 'yes')  
    {  
    $added = time();     
    $pm_on = $row["owner"];  
    $message = "Torrent $id ($row[name]) has been deleted.\n  Reason: $reasonstr";    
    sql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES(0, $pm_on,".sqlesc($message).", $added)") or sqlerr(__FILE__, __LINE__);  
    $mc1->delete_value('inbox_new_'.$pm_on);   
    $mc1->delete_value('inbox_new_sb_'.$pm_on);
    }

    if (isset($_POST["returnto"]))
      $ret = "<a href='" . htmlspecialchars($_POST["returnto"]) . "'>{$lang['delete_go_back']}</a>";
    else
      $ret = "<a href='{$INSTALLER09['baseurl']}/browse.php'>{$lang['delete_back_browse']}</a>";

    $HTMLOUT = '';
    $HTMLOUT .= "<h2>{$lang['delete_deleted']}</h2>
    <p>$ret</p>";


    print stdhead("{$lang['delete_deleted']}") . $HTMLOUT . stdfoot();

?>