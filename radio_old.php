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
require_once INCL_DIR.'html_functions.php';
dbconn(false);
loggedinorreturn();
//error_reporting(0);
$lang = array_merge( load_language('global') );
//$stdfoot = array(/** include js **/'js' => array('rollover'));
$HTMLOUT='';

$HTMLOUT .="<script type='text/javascript'>
/*<![CDATA[*/
function roll_over(img_name, img_src)
{
document[img_name].src=img_src;
}
/*]]>*/
</script>";

$HTMLOUT .= begin_main_frame();
//== Shoutcast server settings, needed to get the XML output from the DNAS
global $bgcolor2;
$index = 1;
$shout_server = "dsn-radio.no-ip.org";
$shout_port = "8000";
$shout_password = "golfaren";
//== Set this to whatever the bitrate you are streaming at is
$bitrate="128 kbps";
//Stream Name
$streamname="{$INSTALLER09['site_name']}";
$shout_socket = fsockopen ($shout_server, $shout_port, $errno, $errstr,30);
if (!$shout_socket)
{
$HTMLOUT .= "<div align='center'>
<font size='3' color='red'><img src='pic/off1.gif' alt='Off' title='Off' border='0' /><br />
<b>Radio - Offline</b></font><br />
Artist: <b>N/A</b><br />
Song: <b>N/A</b><br />
Bitrate: <b>N/A</b><br />
Listeners: <b>N/A</b><br />
Time: <b>".get_date(time(), 'LONG',1)."</b></div><br />";
}
else
{
$xml_load = "";
fputs ($shout_socket, "GET /admin.cgi?pass=".$shout_password."&mode=viewxml HTTP/1.1\nUser-Agent:Mozilla\n\n");
while (!feof($shout_socket)) {
$xml_load .= fgets ($shout_socket, 1000);
}
}

if ($shout_socket) {
$xml_load = strtr ($xml_load, '<', '[');
$xml_load = strtr ($xml_load, '>', ']');
$tag_separated = explode ("]", $xml_load);
foreach ($tag_separated as $key => $value) {
$tag_separated[$key] = $value."]\n";
if (substr_count($value, "Content-Type")) {$tag_separated[$key] = "";}
}
//== $titles array will hold the last 10 songs played
//== Note that $titles[0] will give you the currently playing song
//== The following are provided to let you know which stats are being grabbed by this script
$titles = array();
$currentlisteners=0;
$peaklisteners=0;
$maxlisteners=0;
$reportedlisteners=0;
$averagetime=0;
$servergenre="";
$serverurl="";
$servertitle="";
$page="";
$success="";
foreach ($tag_separated as $value) {
if (substr_count($value, "[/TITLE]")) {
$value = str_replace ("[/TITLE]","", $value);
array_push ($titles, $value);
}

if (substr_count ($value, "[/CURRENTLISTENERS]")) {
$value = str_replace ("[/CURRENTLISTENERS]","", $value);
$currentlisteners=$value;
}

if (substr_count ($value, "[/PEAKLISTENERS]")) {
$value = str_replace ("[/PEAKLISTENERS]","", $value);
$peaklisteners=$value;
}

if (substr_count ($value, "[/MAXLISTENERS]")) {
$value = str_replace("[/MAXLISTENERS]","", $value);
$maxlisteners=$value;
}

if (substr_count ($value, "[/REPORTEDLISTENERS]")) {
$value = str_replace("[/REPORTEDLISTENERS]","", $value);
$reportedlisteners=$value;
}

if (substr_count ($value, "[/AVERAGETIME]")) {
$value = str_replace("[/AVERAGETIME]","", $value);
$averagetime=$value;
$tmp=$averagetime / 60;
$averagesec=$averagetime % 60;
if ($averagesec < 10) {$averagesec = "0".$averagesec;}
$averagemin = sprintf ("%d",$tmp);
$averagehour = $averagemin / 60;
$averagemin = $averagemin % 60;
$averagehour = sprintf ("%d", $averagehour);
}

if (substr_count ($value, "[/SERVERGENRE]")) {
$value = str_replace("[/SERVERGENRE]","", $value);
$servergenre=$value;
}

if (substr_count ($value, "[/SERVERURL]")) {
$value = str_replace("[/SERVERURL]","", $value);
$serverurl=$value;
}

if (substr_count ($value, "[/SERVERTITLE]")) {
$value = str_replace("[/SERVERTITLE]","", $value);
$servertitle=$value;
if (substr_count ($servertitle, "N/A")) {$servertitle = "Radio is currently offline!";}
}

if (substr_count ($value, "[/STREAMHITS]")) {
$value = str_replace("[/STREAMHITS]","", $value);
$streamhits=$value;
}
}
//== $nowplaying[0] = currently playing artist
//== $nowplaying[1] = currently playing title
//== Obviously, use of this requires that titles be named like so:
//== Artist - Title
//== If not, just use $titles[0] for the current song
$temp = $titles[0];
$nowplaying = explode (" - ",$temp);
$fp = fsockopen("$shout_server", $shout_port, &$errno, &$errstr, 30);
if(!$fp) {
$success=2;
}
if($success!=2){ //if connection
fputs($fp,"GET /7.html HTTP/1.0\r\nUser-Agent: XML Getter (Mozilla Compatible)\r\n\r\n");
while(!feof($fp)) {
$page .= fgets($fp, 1000);
}
fclose($fp);
$page = preg_replace("#.*<body>#", "", $page); //extract data
$page = preg_replace("#</body>.*#", ",", $page); //extract data
$numbers = explode(",",$page);
$currentlisteners=$numbers[0];
$connected=$numbers[1];
if($connected==1)
$wordconnected="yes";
else
$wordconnected="no";
}
if($success!=2 && $connected==1){

$HTMLOUT .="<div align='center'>
<img src='pic/on1.gif' alt='On' title='On' border='0' /><br />
<b><font size='3' color='green'>{$INSTALLER09['site_name']} Radio</font></b><br />
<b>{$servertitle}</b><br />
Now playing: <b>{$titles[0]}</b><br />
Genre: <b>{$servergenre}</b><br />
Artist: <b>{$nowplaying[0]}</b><br />
Song: <b>{$nowplaying[1]}</b><br />
Bitrate: <b>{$bitrate}</b><br />
Listeners: <b>{$currentlisteners}/{$maxlisteners}</b><br />
Time: <b>".get_date(time(), 'LONG',1)."</b><br />
<br />
<a href=\"http://dsn-radio.no-ip.org:8000/listen.pls\" onmouseover=\"roll_over('winamp', 'pic/winamp_over.png')\" onmouseout=\"roll_over('winamp', 'pic/winamp.png')\" style=\"border:hidden;\" ><img src=\"pic/winamp.png\" name=\"winamp\" alt=\"click here to listen with Winamp\" title=\"click here to listen with Winamp\" style=\"border:hidden;\" /></a>
<a href=\"http://dsn-radio.no-ip.org:8000/listen.asx\" onmouseover=\"roll_over('wmp', 'pic/wmp_over.png')\" onmouseout=\"roll_over('wmp', 'pic/wmp.png')\" style=\"border:hidden;\" ><img src=\"pic/wmp.png\" name=\"wmp\" alt=\"click here to listen with Windows Media Player\" title=\"click here to listen with Windows Media Player\" style=\"border:hidden;\" /></a></div>";
}
else {
$HTMLOUT .="<div align='center'>
<font size='3' color='Red'><b>Radio - Offline</b></font><br />
Artist: <b>N/A</b><br />
Song: <b>N/A</b><br />
Bitrate: <b>N/A</b><br />
Listeners: <b>N/A</b><br />
Time: <b>".get_date(time(), 'LONG',1)."</b></div><br />";
}
}
$HTMLOUT .= end_main_frame();
print stdhead("{$INSTALLER09['site_name']} Radio") . $HTMLOUT . stdfoot(/*$stdfoot*/);
?>