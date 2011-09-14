<?php
/**
 *   http://btdev.net:1337/svn/test/Installer09_Beta
 *   Licence Info: GPL
 *   Copyright (C) 2010 BTDev Installer v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn.
 **/
error_reporting(0);
////////////////// GLOBAL VARIABLES ////////////////////////////	
$finished = $finished1 = '';
$INSTALLER09['baseurl'] = '#baseurl';
$INSTALLER09['announce_interval'] = 60 * 30;
$INSTALLER09['connectable_check'] = 1;
$INSTALLER09['max_slots'] = 1; //1=On 0=Off
$INSTALLER09['user_slots'] = 20;
$INSTALLER09['p_user_slots'] = 30;
$INSTALLER09['user_ratio1_slots'] = 2;
$INSTALLER09['user_ratio2_slots'] = 3;
$INSTALLER09['user_ratio3_slots'] = 5;
$INSTALLER09['user_ratio4_slots'] = 10;
define('TIME_NOW', time());
define ('UC_USER', 0);
define ('UC_POWER_USER', 1);
define ('UC_VIP', 2);
define ('UC_UPLOADER', 3);
define ('UC_MODERATOR', 4);
define ('UC_ADMINISTRATOR', 5);
define ('UC_SYSOP', 6);
// DB setup
$INSTALLER09['mysql_host'] = "#mysql_host";
$INSTALLER09['mysql_user'] = "#mysql_user";
$INSTALLER09['mysql_pass'] = "#mysql_pass";
$INSTALLER09['mysql_db']   = "#mysql_db";
require_once("include/class/class_cache.php");
$mc1 = NEW CACHE();
$INSTALLER09['expires']['user_passkey'] = 900; // 900 = 15 min
$INSTALLER09['cache'] = dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR;
////////////////// GLOBAL VARIABLES ////////////////////////////
// DO NOT EDIT BELOW UNLESS YOU KNOW WHAT YOU'RE DOING!!
   //=== bad stuff let's just kill this right off
   $agent = $_SERVER["HTTP_USER_AGENT"];
   $detectedclient = $_SERVER["HTTP_USER_AGENT"];
   if (isset($_SERVER['HTTP_COOKIE']) || isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) || isset($_SERVER['HTTP_ACCEPT_CHARSET']))
   exit('It takes 46 muscles to frown but only 4 to flip \'em the bird.');
   /////////////////////// FUNCTION DEFS ///////////////////////////////////
   
   function crazyhour_announce() {
   global $INSTALLER09;
   $crazyhour_filename = $INSTALLER09['cache'].'crazy_hour.txt';
   $crazyhour_cache = fopen($crazyhour_filename,'r+');
   $crazyhour_var = fread($crazyhour_cache, filesize($crazyhour_filename));
   fclose($crazyhour_cache);
   
   if ($crazyhour_var >= TIME_NOW && $crazyhour_var < TIME_NOW + 3600) // is crazyhour
       return true;
       
    elseif ($crazyhour_var + 3600 < TIME_NOW) {   // crazyhour over
        $crazyhour['crazyhour_new']       = mktime(23, 59, 59, date('m'), date('d'), date('y'));
        $crazyhour['crazyhour']['var']    = mt_rand($crazyhour['crazyhour_new'], ($crazyhour['crazyhour_new'] + 86400));
        $fp = fopen($crazyhour_filename, 'w');
        fwrite($fp, $crazyhour['crazyhour']['var']);
        fclose($fp); 
        /** log, shoutbot **/
        $text = 'Next Crazyhour is at '.$crazyhour['crazyhour'];
        mysql_query('INSERT INTO sitelog (added, txt) VALUES('.TIME_NOW.', '.sqlesc($text).')') or err("Crazyhour Err");     
        mysql_query('INSERT INTO shoutbox (userid, date, text, text_parsed) VALUES (2, '.TIME_NOW.', '.sqlesc($text).', '.sqlesc($text).')') or err("Crazyhour Err 1");
        return false;
        }
        else
        return false;
        }
	      // crazyhour end



function auto_enter_cheater($userid, $rate, $upthis, $diff, $torrentid, $client, $ip, $last_up)
{
mysql_query("INSERT INTO cheaters (added, userid, client, rate, beforeup, upthis, timediff, userip, torrentid) VALUES(" . sqlesc(TIME_NOW) . ", " . sqlesc($userid) . ", " . sqlesc($client) . ", " . sqlesc($rate) . ", " . sqlesc($last_up) . ", " . sqlesc($upthis) . ", " . sqlesc($diff) . ", " . sqlesc($ip) . ", " . sqlesc($torrentid) . ")") or err("Cheater Err");
}

function err($msg)
{
	benc_resp(array('failure reason' => array('type' => 'string', 'value' => $msg)));
	
	exit();
}

function benc_resp($d)
{
	benc_resp_raw(benc(array('type' => 'dictionary', 'value' => $d)));
}

function benc_resp_raw($x)
{
    header( "Content-Type: text/plain" );
    header( "Pragma: no-cache" );

    if ( $_SERVER['HTTP_ACCEPT_ENCODING'] == 'gzip' )
    {
        header( "Content-Encoding: gzip" );
        echo gzencode( $x, 9, FORCE_GZIP );
    }
    else
        echo $x ;
}

function benc($obj) {
	if (!is_array($obj) || !isset($obj["type"]) || !isset($obj["value"]))
		return;
	$c = $obj["value"];
	switch ($obj["type"]) {
		case "string":
			return benc_str($c);
		case "integer":
			return benc_int($c);
		case "list":
			return benc_list($c);
		case "dictionary":
			return benc_dict($c);
		default:
			return;
	}
}

function benc_str($s) {
	return strlen($s) . ":$s";
}

function benc_int($i) {
	return "i" . $i . "e";
}

function benc_list($a) {
	$s = "l";
	foreach ($a as $e) {
		$s .= benc($e);
	}
	$s .= "e";
	return $s;
}

function benc_dict($d) {
	$s = "d";
	$keys = array_keys($d);
	sort($keys);
	foreach ($keys as $k) {
		$v = $d[$k];
		$s .= benc_str($k);
		$s .= benc($v);
	}
	$s .= "e";
	return $s;
}

function hash_where($name, $hash) {
    $shhash = preg_replace('/ *$/s', "", $hash);
    return "($name = " . sqlesc($hash) . " OR $name = " . sqlesc($shhash) . ")";
}

function sqlesc($x) {
    return "'".mysql_real_escape_string($x)."'";
}

function portblacklisted($port)
{
    //=== new portblacklisted ....... ==> direct connect 411 ot 413,  bittorrent 6881 to 6889, kazaa 1214, gnutella 6346 to 6347, emule 4662, winmx 6699, IRC bot based trojans 65535
    $portblacklisted = array(411, 412, 413, 6881 ,6882, 6883, 6884, 6885, 6886, 6887, 6889, 1214, 6346, 6347, 4662, 6699, 65535);
        if (in_array($port, $portblacklisted)) return true;

    return false;
}
/////////////////////// FUNCTION DEFS END ///////////////////////////////
$parts = array();
if( !isset($_GET['passkey']) OR !preg_match('/^[0-9a-fA-F]{32}$/i', $_GET['passkey'], $parts) ) 
err("Invalid Passkey");
else
$GLOBALS['passkey'] = $parts[0];
		
foreach (array("info_hash","peer_id","event","ip","localip") as $x) 
{
if(isset($_GET["$x"]))
$GLOBALS[$x] = "" . $_GET[$x];
}

foreach (array("port","downloaded","uploaded","left") as $x)
{
$GLOBALS[$x] = 0 + $_GET[$x];
}

foreach (array("passkey","info_hash","peer_id","port","downloaded","uploaded","left") as $x)

if (!isset($x)) err("Missing key: $x");

foreach (array("info_hash","peer_id") as $x)

if (strlen($GLOBALS[$x]) != 20) err("Invalid $x (" . strlen($GLOBALS[$x]) . " - " . urlencode($GLOBALS[$x]) . ")");

unset($x);

$info_hash = bin2hex($info_hash);

$ip = $_SERVER['REMOTE_ADDR'];

$port = 0 + $port;
$downloaded = 0 + $downloaded;
$uploaded = 0 + $uploaded;
$left = 0 + $left;
$rsize = 50;
foreach(array("num want", "numwant", "num_want") as $k)
{
if (isset($_GET[$k]))
{
$rsize = 0 + $_GET[$k];
break;
}
}

if (!$port || $port > 0xffff)
err("invalid port");

if (!isset($event))
$event = "";

$seeder = ($left == 0) ? "yes" : "no";

if (!($db = @mysql_connect ($INSTALLER09['mysql_host'], $INSTALLER09['mysql_user'], $INSTALLER09['mysql_pass']) AND $select = @mysql_select_db($INSTALLER09['mysql_db'], $db)))
err('Please call back later');

$user = $mc1->get_value('u_passkey_'.$passkey);
if ($user === false) {
$user_query = mysql_query("SELECT id, uploaded, downloaded, class, downloadpos, parked, free_switch, highspeed, enabled FROM users WHERE passkey=".sqlesc($passkey)) or err("Tracker error 2");
if (mysql_num_rows($user_query) != 1)
err("Unknown passkey. Please redownload the torrent from {$INSTALLER09['baseurl']}.");
$user = mysql_fetch_assoc($user_query);
        $user['id']         = (int)$user['id'];
        $user['uploaded']   = (float)$user['uploaded'];
        $user['downloaded'] = (float)$user['downloaded'];
        $user['class']      = (int)$user['class'];
        $user['downloadpos']      = (int)$user['downloadpos'];
        $user['free_switch']  = (int)$user['free_switch'];
        $mc1->cache_value('u_passkey_'.$passkey, $user, $INSTALLER09['expires']['user_passkey']);
}

if($user['enabled'] == 'no')
err('Permission denied, you\'re not enabled');
	
////Start IP logger ////
        $userid = (int)$user['id'];
        $res = mysql_query("SELECT * FROM ips WHERE ip = '$ip' AND userid = '$userid'") or die(mysql_error());
        if (mysql_num_rows($res) == 0 ) {
        mysql_query("INSERT INTO ips (userid, ip, lastannounce, type) VALUES ('$userid', '$ip' ,'".time()."','announce')") or err("Ip fail");
        }
        else
        {
        mysql_query("UPDATE ips SET lastannounce = ".time()." WHERE ip = '$ip' AND userid = '$userid'") or err("Ip insert fail");
        } //// End Ip logger /////

$res = mysql_query("SELECT torrents.id, torrents.banned, torrents.free, torrents.vip, torrents.seeders + torrents.leechers AS numpeers, torrents.added AS ts, freeslots.free AS freeslot, freeslots.double AS doubleslot FROM torrents LEFT JOIN freeslots ON (torrents.id=freeslots.tid AND freeslots.uid=".sqlesc($user['id']).") WHERE info_hash = ".sqlesc($info_hash));//" . hash_where("info_hash", $info_hash));
$torrent = mysql_fetch_assoc($res);
if (!$torrent)
	err("torrent not registered with this tracker CODE 2");

$torrentid = $torrent["id"];

$fields = 'seeder, peer_id, ip, port, uploaded, downloaded, userid, ('.TIME_NOW.' - last_action) AS announcetime, last_action AS ts';

$numpeers = $torrent["numpeers"];
$limit = "";
if ($numpeers > $rsize)
$limit = "ORDER BY RAND() LIMIT $rsize";
// If user is a seeder, then only supply leechers.
// This helps with the zero upload cheat, as it doesn't supply anyone who has
// a full copy.
$wantseeds = "";
if ( $seeder == 'yes' )
$wantseeds = "AND seeder = 'no'";
$res = mysql_query( "SELECT $fields FROM peers WHERE torrent = $torrentid AND connectable = 'yes' $wantseeds $limit" ) or err( 'peers query failure' );
//////////////////// START NEW COMPACT MODE/////////////////////////////
if($_GET['compact'] != 1)
{
$resp = "d" . benc_str("interval") . "i" . $INSTALLER09['announce_interval'] . "e" . benc_str("private") . 'i1e' . benc_str("peers") . "l";
}
else
{
$resp = "d" . benc_str("interval") . "i" . $INSTALLER09['announce_interval'] ."e" . benc_str("private") . 'i1e'. benc_str("min interval") . "i" . 300 ."e5:"."peers" ;
}

$peer = array();
$peer_num = 0;
while ($row = mysql_fetch_assoc($res))
{
if($_GET['compact'] != 1)
{
$row["peer_id"] = str_pad($row["peer_id"], 20);
if ($row["peer_id"] === $peer_id)
{
$self = $row;
continue;
}
$resp .= "d" .
benc_str("ip") . benc_str($row["ip"]);
if (!$_GET['no_peer_id']) {
$resp .= benc_str("peer id") . benc_str($row["peer_id"]);
}
$resp .= benc_str("port") . "i" . $row["port"] . "e" . "e";
}
else
{
$peer_ip = explode('.', $row["ip"]);
$peer_ip = pack("C*", $peer_ip[0], $peer_ip[1], $peer_ip[2], $peer_ip[3]);
$peer_port = pack("n*", (int)$row["port"]);
$time = intval((TIME_NOW % 7680) / 60);
if($_GET['left'] == 0)
{
$time += 128;
}
$time = pack("C", $time);
$peer[] = $time . $peer_ip . $peer_port;
$peer_num++;
}
}
if ($_GET['compact']!=1)
$resp .= "ee";
else
{
$o = "";
for($i=0;$i<$peer_num;$i++)
{
$o .= substr($peer[$i], 1, 6);
}
$resp .= strlen($o) . ':' . $o . 'e';
}
$selfwhere = "torrent = $torrentid AND " . hash_where("peer_id", $peer_id);
///////////////////////////// END NEW COMPACT MODE////////////////////////////////
if (!isset($self))
{
	$res = mysql_query("SELECT $fields FROM peers WHERE $selfwhere");
	$row = mysql_fetch_assoc($res);
	if ($row)
	{
		$userid = $row["userid"];
		$self = $row;
	}
}
//// Up/down stats ////////////////////////////////////////////////////////////
if ($torrent['vip'] == '1' && $user['class'] < UC_VIP)
err('VIP Access Required, You must be a VIP In order to view details or download this torrent! You may become a Vip By Donating to our site. Donating ensures we stay online to provide you more Vip-Only Torrents!');

if (!isset($self))
{
$valid = @mysql_fetch_row(@mysql_query("SELECT COUNT(*) FROM peers WHERE torrent=$torrentid AND passkey=" . sqlesc($passkey)));
if ($valid[0] >= 1 && $seeder == 'no') err("Connection limit exceeded! You may only leech from one location at a time.");
if ($valid[0] >= 3 && $seeder == 'yes') err("Connection limit exceeded!");
}
else
{
	$upthis = max(0, $uploaded - $self["uploaded"]);
	$downthis = max(0, $downloaded - $self["downloaded"]);
  $upspeed = ($upthis > 0 ? $upthis / $self["announcetime"] : 0);
  $downspeed = ($downthis > 0 ? $downthis / $self["announcetime"] : 0);
  $announcetime = ($self["seeder"] == "yes" ? "seedtime = seedtime + $self[announcetime]" : "leechtime = leechtime + $self[announcetime]");
  //==sitepot
  $Pot_query = mysql_query("SELECT value_i FROM avps WHERE arg = 'sitepot'") or err("Sitepot Err");
  $SitePot = mysql_fetch_assoc($Pot_query);
  ///////////////////happyhour by putyn
  $happy = mysql_query( "SELECT id, multiplier from happyhour where userid=" . sqlesc( $userid ) . " AND torrentid=" . sqlesc( $torrentid ) . " " );
  $happyhour = mysql_num_rows( $happy ) == 0 ? false : true;
  $happy_multi = mysql_fetch_row( $happy );
  $multiplier = $happy_multi["multiplier"];
  if ( $happyhour ) {
  $upthis = $upthis * $multiplier;
  $downthis = 0;
  }

   //==freeleech/doubleupload system by ezero - recoded block by putyn
   $q = mysql_query("SELECT * FROM events ORDER BY startTime DESC LIMIT 1") or err("Events Err");
	 $a = mysql_fetch_assoc($q);
	 if($a["startTime"] < TIME_NOW && $a["endTime"] > TIME_NOW)
	 {
	 if($a['freeleechEnabled'] == 1)
	 $downthis = 0;
	 if($a['duploadEnabled'] == 1){
	 $upthis *=2;
	 $downthis = 0;
	 }
	 if($a['hdownEnabled'] == 1){
	 $downthis = $downthis / 2;
	 }
	 }
   
   if ($upthis > 0 || $downthis > 0)
   {
   $isfree =   '';
   $isdouble = '';
   include("cache/free_cache.php");
   if (isset($free))
   {
   foreach ($free as $fl) {
   $isfree =   ($fl['modifier'] == 1 || $fl['modifier'] == 3) && $fl['expires'] > TIME_NOW;
   $isdouble = ($fl['modifier'] == 2 || $fl['modifier'] == 3) && $fl['expires'] > TIME_NOW;
   }
   }
   
   $crazyhour = crazyhour_announce();
   if (!($crazyhour || $user['free_switch'] != 0 || $isfree || $torrent['free'] != 0 || $SitePot['value_i'] >= 10000 || $torrent['vip'] != 0 || ($torrent['freeslot'] != 0)))
   $updq[0] = "downloaded = downloaded + $downthis";
   if ($crazyhour) // crazyhour
   $updq[1]="uploaded = uploaded + ($upthis*3)";
   else
   $updq[1] = "uploaded = uploaded + ".(($torrent['doubleslot'] != 0 || $isdouble) ? ($upthis*2) : $upthis);
   $udq=implode(',',$updq);
   mysql_query("UPDATE users SET $udq WHERE id=".$user['id']) or err('Tracker error 3');
   $mc1->delete_value('MyUser_'.$user['id']);
   $mc1->delete_value('user'.$user['id']);
   }

      //=== abnormal upload detection
			if ($user['highspeed'] == 'no' && $upthis > 103872)
			{
      //=== Work out difference
      $diff = (TIME_NOW - $self['ts']);
      $rate = ($upthis / ($diff + 1));
      $last_up = $user['uploaded'];
      //=== about 1 MB/s
      if ($rate > 103872) 
      {
		  auto_enter_cheater($user['id'], $rate, $upthis, $diff, $torrentid, $agent, $ip, $last_up );
      }
			} //=== end abnormal upload detection
      }

///////////////////////////////////////////////////////////////////////////////
    if (portblacklisted($port))
		{
	  err("Port $port is blacklisted.");
		}
		elseif ( $INSTALLER09['connectable_check'] )
		{
		//== connectable checking - pdq
    $connkey = 'conn:'.$ip.':'.$port;
    $connectable = $mc1->get_value($connkey);
    if ($connectable === false) {
    $sockres = @fsockopen($ip, $port, $errno, $errstr, 5);
    if (!$sockres) {
        $connectable = 'no';
        $conn_ttl = 15;
    }
    else {
        $connectable = 'yes';
        $conn_ttl = 900;
        @fclose($sockres);
    }
    $mc1->cache_value($connkey, $connectable, $conn_ttl);
    }
		}
 
 
 $updateset = array();
 
 //== NoReport sends event=stopped in combination with numwant greater 0 everytime
 if ($_GET['event'] == 'stopped' && $_GET['numwant'] != 0) {
 mysql_query("INSERT INTO cheatdetect (userid, torrentid, detectedclient, suspicion, time, data) VALUES ({$user['id']}, {$torrent['id']}, ".sqlesc($detectedclient).", 'No Report Client Used', ".TIME_NOW.", ".sqlesc($_GET['left']).")") or err("Zero report Detect Err");
 mysql_query("UPDATE users SET enabled = 'no' WHERE id =".sqlesc($user["id"])."") or err("Zero report Disable Err");
 $mc1->delete_value('MyUser_'.$user['id']);
 err("WTF - NO Report client");
 return;
 }
 
 if (isset($self) && $event == "stopped") {
 mysql_query("DELETE FROM peers WHERE $selfwhere") or err("Delete Err");

 //===09 sir_snuggles hit and run
 $res_snatch = mysql_query("SELECT seedtime, uploaded, downloaded, finished, start_date AS start_snatch FROM snatched WHERE torrentid = $torrentid AND userid = {$user['id']}") or err('Snatch Error 1');
 $a = mysql_fetch_array($res_snatch);
 //=== only run the function if the ratio is below 1
 if( ($a['uploaded'] + $upthis) < ($a['downloaded'] + $downthis) && $a['finished'] == 'yes')
 {
 $HnR_time_seeded = ($a['seedtime'] + $self['announcetime']);
 //=== get times per class
 switch (true)
 { 
 //=== user
 case ($user['class'] < UC_POWER_USER):
 $days_3 = 2*86400; //== 2 days
 $days_14 = 2*86400; //== 2 days
 $days_over_14 = 86400; //== 1 day
 break;
 //=== poweruser
 case ($user['class'] == UC_POWER_USER):
 $days_3 = 129600; //== 36 hours
 $days_14 = 129600; //== 36 hours
 $days_over_14 = 64800; //== 18 hours
 break;
 //=== vip / donor?
 case ($user['class'] == UC_VIP):
 $days_3 = 129600; //== 36 hours
 $days_14 = 86400; //== 24 hours
 $days_over_14 = 43200; //== 12 hours
 break;
 //=== uploader / staff and above (we don't need this for uploaders now do we?
 case ($user['class'] >= UC_UPLOADER):
 $days_3 = 43200; //== 12 hours
 $days_14 = 43200; //== 12 hours
 $days_over_14 = 43200; //== 12 hours
 break;
 }

 switch(true) 
 {
 case (($a['start_snatch'] - $torrent['ts']) < 7*86400):
 $minus_ratio = ($days_3 - $HnR_time_seeded);
 break;
 case (($a['start_snatch'] - $torrent['ts']) < 21*86400):
 $minus_ratio = ($days_14 - $HnR_time_seeded);
 break;
 case (($a['start_snatch'] - $torrent['ts']) >= 21*86400):
 $minus_ratio = ($days_over_14 - $HnR_time_seeded);
 break;
 }
 $hit_and_run = (($minus_ratio > 0 && ($a['uploaded'] + $upthis) < ($a['downloaded'] + $downthis)) ? ", seeder='no', hit_and_run= '".TIME_NOW."'" : ", hit_and_run = '0'");
 } //=== end if not 1:1 ratio
 else
 $hit_and_run = ", hit_and_run = '0'";
 //=== end hit and run
 
 if (mysql_affected_rows()) {
 $updateset[] = ($self["seeder"] == "yes" ? "seeders = seeders - 1" : "leechers = leechers - 1");
 mysql_query("UPDATE snatched SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', uploaded = uploaded + $upthis, downloaded = downloaded + $downthis, to_go = $left, upspeed = $upspeed, downspeed = $downspeed, $announcetime, last_action = ".TIME_NOW.", seeder = '$seeder', agent = ".sqlesc($agent)." $hit_and_run WHERE torrentid = $torrentid AND userid = {$user['id']}") or err("SL Err 1");
 }
 } elseif (isset($self)) {

 if ($event == "completed") {
 $updateset[] = "times_completed = times_completed + 1";
 $finished = ", finishedat = ".TIME_NOW."";
 $finished1 = ", complete_date = ".TIME_NOW.", finished = 'yes'";
 }

 mysql_query("UPDATE peers SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', uploaded = $uploaded, downloaded = $downloaded, to_go = $left, last_action = ".TIME_NOW.", seeder = '$seeder', agent = ".sqlesc($agent)." $finished WHERE $selfwhere") or err("PL Err 1");

 if (mysql_affected_rows()) {
 if ($seeder <> $self["seeder"])
 $updateset[] = ($seeder == "yes" ? "seeders = seeders + 1, leechers = leechers - 1" : "seeders = seeders - 1, leechers = leechers + 1");
 $anntime = "timesann = timesann + 1";
 mysql_query("UPDATE snatched SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', uploaded = uploaded + $upthis, downloaded = downloaded + $downthis, to_go = $left, upspeed = $upspeed, downspeed = $downspeed, $announcetime, last_action = ".TIME_NOW.", seeder = '$seeder', agent = ".sqlesc($agent)." $finished1, $anntime WHERE torrentid = $torrentid AND userid = {$user['id']}") or err("SL Err 2");
 }
 } else {
 if ($user["parked"] == "yes")
 err("Your account is parked! (Read the FAQ)");
 elseif ($user["downloadpos"] == 0 OR $user["downloadpos"] > 1 )
 err("Your downloading priviledges have been disabled! (Read the rules)");

 mysql_query("INSERT INTO peers (torrent, userid, peer_id, ip, port, connectable, uploaded, downloaded, to_go, started, last_action, seeder, agent, downloadoffset, uploadoffset, passkey) VALUES ($torrentid, {$user['id']}, ".sqlesc($peer_id).", ".sqlesc($ip).", $port, '$connectable', $uploaded, $downloaded, $left, ".TIME_NOW.", ".TIME_NOW.", '$seeder', ".sqlesc($agent).", $downloaded, $uploaded, ".sqlesc($passkey).")") or err("PL Err 2");

 if (mysql_affected_rows()) {
 $updateset[] = ($seeder == "yes" ? "seeders = seeders + 1" : "leechers = leechers + 1");
 $anntime = "timesann = timesann + 1";
 mysql_query("UPDATE snatched SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', to_go = $left, last_action = ".TIME_NOW.", seeder = '$seeder', agent = ".sqlesc($agent).", $anntime, hit_and_run = '0', mark_of_cain = 'no' WHERE torrentid = $torrentid AND userid = {$user['id']}") or err("SL Err 3");
 if (!mysql_affected_rows() && $seeder == "no")
 mysql_query("INSERT INTO snatched (torrentid, userid, peer_id, ip, port, connectable, uploaded, downloaded, to_go, start_date, last_action, seeder, agent) VALUES ($torrentid, {$user['id']}, ".sqlesc($peer_id).", ".sqlesc($ip).", $port, '$connectable', $uploaded, $downloaded, $left, ".TIME_NOW.", ".TIME_NOW.", '$seeder', ".sqlesc($agent).")") or err("SL Err 4");
 }
 }

if ($seeder == "yes")
{
	if ($torrent["banned"] != "yes")
	$updateset[] = "visible = 'yes'";
	$updateset[] = "last_action = ".TIME_NOW;
}

if (count($updateset))
	mysql_query("UPDATE torrents SET ".join(",", $updateset)." WHERE id = $torrentid");
	
benc_resp_raw($resp);
?>
