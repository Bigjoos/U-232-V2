<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
if ( ! defined( 'IN_INSTALLER09_ADMIN' ) )
{
	$HTMLOUT='';
	$HTMLOUT .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<title>Error!</title>
		</head>
		<body>
	<div style='font-size:33px;color:white;background-color:red;text-align:center;'>Incorrect access<br />You cannot access this file directly.</div>
	</body></html>";
	echo $HTMLOUT;
	exit();
}

require_once(INCL_DIR.'user_functions.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_SYSOP);

$lang = array_merge($lang, load_language('ad_index') );
$HTMLOUT='';
 
    //==Windows Server Load
    $HTMLOUT .="
    <div class='roundedCorners' style='text-align:left;width:80%;border:1px solid black;padding:5px;'>
    <div style='background:transparent;height:25px;'><span style='font-weight:bold;font-size:12pt;'>{$lang['index_serverload']}</span></div>
    <br />
    <table width='100%' border='1' cellspacing='0' cellpadding='1'>
		<tr><td align='center'>
		<table class='main' border='0' width='402'>
    <tr><td style='padding: 0px; background-image: url({$INSTALLER09['pic_base_url']}loadbarbg.gif); background-repeat: repeat-x'>";
    $perc = get_server_load();
    $percent = min(100, $perc);
    if ($percent <= 70) $pic = "loadbargreen.gif";
    elseif ($percent <= 90) $pic = "loadbaryellow.gif";
    else $pic = "loadbarred.gif";
    $width = $percent * 4;
    $HTMLOUT .="<img height='15' width='$width' src=\"{$INSTALLER09['pic_base_url']}{$pic}\" alt='$percent&#37;' /><br />Currently {$percent}&#37; CPU usage.<br /></td></tr></table></td></tr></table></div><br />";
    //==End
    
    /*
    //==Server Load linux
    $HTMLOUT .="
    <div class='roundedCorners' style='text-align:left;width:80%;border:1px solid black;padding:5px;'>
    <div style='background:transparent;height:25px;'><span style='font-weight:bold;font-size:12pt;'>{$lang['index_serverload']}</span></div>
    <br />
    <table width='100%' border='1' cellspacing='0' cellpadding='1'>
			<tr><td align='center'>
		    <table class='main' border='0' width='402'>
    			<tr><td style='padding: 0px; background-image: url({$INSTALLER09['pic_base_url']}loadbarbg.gif); background-repeat: repeat-x'>";
    $percent = min(100, round(exec('ps ax | grep -c apache') / 256 * 100));
    if ($percent <= 70) $pic = "loadbargreen.gif";
    elseif ($percent <= 90) $pic = "loadbaryellow.gif";
    else $pic = "loadbarred.gif";
    $width = $percent * 4;
    $HTMLOUT .="<img height='15' width='$width' src=\"{$INSTALLER09['pic_base_url']}{$pic}\" alt='$percent&#37;' /><br />Currently {$percent}&#37; CPU usage.<br /></td></tr></table></td></tr></table></div><br />";
    //==End
    */

 
echo stdhead("Server Load") . $HTMLOUT . stdfoot();
?>