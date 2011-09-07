<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');

dbconn();
loggedinorreturn();
$lang = array_merge( load_language('global'), load_language('takerate') );


if (!isset($CURUSER))
	stderr("Error","{$lang['rate_login']}");

if (!mkglobal("rating:id"))
	stderr("Error","{$lang['rate_miss_form_data']}");

$id = 0 + $id;
if (!$id)
	stderr("Error","{$lang['rate_invalid_id']}");

$rating = 0 + $rating;
if ($rating <= 0 || $rating > 5)
	stderr("Error","{$lang['rate_invalid']}");

$res = sql_query("SELECT owner FROM torrents WHERE id = $id");
$row = mysql_fetch_assoc($res);
if (!$row)
	stderr("Error","{$lang['rate_torrent_not_found']}");

$time_now = time();
$res = sql_query("INSERT INTO ratings (torrent, user, rating, added) VALUES ($id, " . $CURUSER["id"] . ", $rating, $time_now)");
if (!$res) {
	if (mysql_errno() == 1062)
		stderr("Error","{$lang['rate_already_voted']}");
	else
		mysql_error();
}

sql_query("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");
//===add karma 
sql_query("UPDATE users SET seedbonus = seedbonus+5.0 WHERE id = ".sqlesc($CURUSER['id'])."") or sqlerr(__FILE__, __LINE__);
//===end
header("Refresh: 0; url=details.php?id=$id&rated=1");

?>