<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
//made by putyn @tbdev.net
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
dbconn();
loggedinorreturn();
global $INSTALLER09;
$pm_what = isset($_POST["pm_what"]) && $_POST["pm_what"] =="last10" ? "last10" : "owner";
$reseedid = 0 + $_POST["reseedid"];
$uploader = 0 + $_POST["uploader"];
$use_subject = true;
$subject = "Request reseed!";
$pm_msg = "User " . $CURUSER["username"] . " asked for a reseed on torrent ".$INSTALLER09['baseurl']."/details.php?id=" . $reseedid . " !\nThank You!";

$pms = array();
if ($pm_what == "last10" ) {
	$res = sql_query("SELECT snatched.userid, snatched.torrentid FROM snatched  where snatched.torrentid =$reseedid AND snatched.seeder='yes' LIMIT 10") or sqlerr(__FILE__, __LINE__);
	while($row = mysql_fetch_assoc($res))
		$pms[] = "(0,".$row["userid"].",".sqlesc(time()).",".sqlesc($pm_msg).($use_subject ? ",".sqlesc($subject) : "").")";
}
elseif($pm_what == "owner")
		$pms[] = "(0,$uploader,".sqlesc(time()).",".sqlesc($pm_msg).($use_subject ? ",".sqlesc($subject) : "").")";
		
if(count($pms) > 0)		
sql_query("INSERT INTO messages (sender, receiver, added, msg ".($use_subject ? ", subject" : "")." ) VALUES ".join(",",$pms)) or sqlerr(__FILE__, __LINE__);
sql_query("UPDATE torrents set last_reseed=".sqlesc(time())." WHERE id= $reseedid ") or sqlerr(__FILE__, __LINE__);
//===remove karma 
@sql_query("UPDATE users SET seedbonus = seedbonus-10.0 WHERE id = ".sqlesc($CURUSER["id"])."") or sqlerr(__FILE__, __LINE__);
//===end
header("Refresh: 0; url=./details.php?id=$reseedid&reseed=1");
?>