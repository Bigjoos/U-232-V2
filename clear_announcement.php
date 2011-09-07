<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
dbconn(false);
loggedinorreturn();

$query1 = sprintf('UPDATE users SET curr_ann_id = 0, curr_ann_last_check = \'0\' '.
 	 'WHERE id = %s AND curr_ann_id != 0',
 		 sqlesc($CURUSER['id']));
$mc1->delete_value('MyUser_'.$CURUSER['id']);
sql_query($query1);

header("Location: {$INSTALLER09['baseurl']}/index.php");
?>