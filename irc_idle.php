<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
//irc idle thingy using php
$key = 'VGhlIE1vemlsbGEgZmFtaWx5IGFwcG';
$vars = array('ircidle'=>'','username'=>'','key'=>'','do'=>'');
foreach($vars as $k=>$v)
	$vars[$k] = isset($_GET[$k]) ? $_GET[$k] : '';
if($key !== $vars['key'] || empty($vars['username']))
	die('hmm something looks odd');
	
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
dbconn();

switch($vars['do']) {
	case 'check':
		$q = sql_query('SELECT id FROM users WHERE username = '.sqlesc($vars['username']));
		print(mysql_num_rows($q));
	break;
	case 'idle':
		sql_query("UPDATE users SET onirc = ".sqlesc(!$vars['ircidle'] ? 'no':'yes')." where username = ".sqlesc($vars['username']));
		print(mysql_affected_rows());
	break;
	default:
		die('hmm something looks odd again');
}
die;
?>