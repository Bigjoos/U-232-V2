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
dbconn(false);
loggedinorreturn();
sql_query("UPDATE users SET override_class='255' WHERE id = " . $CURUSER['id']);
$mc1->begin_transaction('MyUser_'.$CURUSER['id']);
$mc1->update_row(false, array('override_class' => 255));
$mc1->commit_transaction(300);
$mc1->begin_transaction('user'.$CURUSER['id']);
$mc1->update_row(false, array('override_class' => 255));
$mc1->commit_transaction(900);
header("Location: {$INSTALLER09['baseurl']}/index.php");
die();

?>