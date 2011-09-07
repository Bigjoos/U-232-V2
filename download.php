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
$pkey = isset($_GET['passkey']) && strlen($_GET['passkey']) == 32 ? $_GET['passkey'] : '';
if(!empty($pkey)) {
	$q0 = mysql_query("SELECT * FROM users where passkey = ".sqlesc($pkey)) or sqlerr(__FILE__, __LINE__);
	if(mysql_num_rows($q0) == 0)
		die($lang['downlod_passkey']);
	else
		$CURUSER = mysql_fetch_assoc($q0);
}else
	loggedinorreturn();

  
  $lang =  array_merge( load_language('global'),load_language('download'));
  
  if (function_exists('parked'))
  parked();
  
  $id = isset($_GET['torrent']) ? intval($_GET['torrent']) : 0;
  $ssluse = isset($_GET['ssl']) && $_GET['ssl'] == 1 || $CURUSER['ssluse'] == 3 ? 1 : 0;
  if ( !is_valid_id($id) )
  stderr("{$lang['download_user_error']}", "{$lang['download_no_id']}");

  $res = sql_query("SELECT name, owner, vip, category, filename FROM torrents WHERE id = $id") or sqlerr(__FILE__, __LINE__);
  $row = mysql_fetch_assoc($res);

  $fn = "{$INSTALLER09['torrent_dir']}/$id.torrent";

  if (!$row || !is_file($fn) || !is_readable($fn))
    httperr();

  if ( happyHour( "check" ) && happyCheck( "checkid", $row["category"] ) ) {
  $multiplier = happyHour( "multiplier" );
  $time = time();
  happyLog( $CURUSER["id"], $id, $multiplier );
  sql_query( "INSERT INTO happyhour (userid, torrentid, multiplier ) VALUES (" . sqlesc( $CURUSER["id"] ) . " , " . sqlesc( $id ) . ", " . sqlesc( $multiplier ) . ")" );
  }
   
  if (!($CURUSER["id"] == $row["owner"])) {
  if ($CURUSER["downloadpos"] == 0 || $CURUSER["downloadpos"] > 1 || $CURUSER['suspended'] == 'yes')
  stderr("Error","Your download rights have been disabled.");
  }

  if ($row["vip"] == "1" && $CURUSER["class"] < UC_VIP)
  stderr("VIP Access Required", "You must be a VIP In order to view details or download this torrent! You may become a Vip By Donating to our site. Donating ensures we stay online to provide you more Vip-Only Torrents!");
 
  sql_query("UPDATE torrents SET hits = hits + 1 WHERE id = $id");
  /** free mod for TBDev 09 by pdq **/
  require_once(MODS_DIR.'freeslots_inc.php');
  /** end **/
  require_once(INCL_DIR.'benc.php');
  
  $mc1->delete_value('MyPeers_'.$CURUSER['id']);
  $mc1->delete_value('user'.$CURUSER['id']);
  $mc1->delete_value('top5_tor_');
  $mc1->delete_value('last5_tor_');
  if (!isset($CURUSER['passkey']) || strlen($CURUSER['passkey']) != 32) 
  {
    $CURUSER['passkey'] = md5($CURUSER['username'].time().$CURUSER['passhash']);
    sql_query("UPDATE users SET passkey='{$CURUSER['passkey']}' WHERE id={$CURUSER['id']}");
    $mc1->begin_transaction('MyUser_'.$CURUSER['id']);
    $mc1->update_row(false, array('passkey' => $CURUSER['passkey']));
    $mc1->commit_transaction($INSTALLER09['expires']['curuser']);
    $mc1->begin_transaction('user'.$CURUSER['id']);
    $mc1->update_row(false, array('passkey' => $CURUSER['passkey']));
    $mc1->commit_transaction($INSTALLER09['expires']['user_cache']);
  }

  $dict = bdec_file($fn, filesize($fn));
  //$dict['value']['announce']['value'] = "{$INSTALLER09['announce_urls'][0]}?passkey={$CURUSER['passkey']}";
  $dict['value']['announce']['value'] = "{$INSTALLER09['announce_urls'][$ssluse]}?passkey={$CURUSER['passkey']}";
  $dict['value']['announce']['string'] = strlen($dict['value']['announce']['value']).":".$dict['value']['announce']['value'];
  $dict['value']['announce']['strlen'] = strlen($dict['value']['announce']['string']);
  $dict['value']['created by']=bdec(benc_str( "".$CURUSER['username'].""));


header('Content-Disposition: attachment; filename="['.$INSTALLER09['site_name'].']'.$row['filename'].'"');
header("Content-Type: application/x-bittorrent"); 

print(benc($dict));
?>