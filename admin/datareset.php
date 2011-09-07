<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
/*
+------------------------------------------------
|   $Date$ 030810
|   $Revision$ 2.0
|   $Author$ putyn,Bigjoos
|   $URL$
|   $datareset
|   
+------------------------------------------------
*/
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
require_once(INCL_DIR.'html_functions.php');
require_once(CLASS_DIR.'class_check.php');
class_check(UC_ADMINISTRATOR);

$lang = array_merge( $lang );
$HTMLOUT="";
 
function deletetorrent($id) {
    global $INSTALLER09, $mc1, $CURUSER, $lang;
    sql_query("DELETE FROM torrents WHERE id = $id");
    sql_query("DELETE FROM coins WHERE torrentid = $id");
    sql_query("DELETE FROM bookmarks WHERE torrentid = $id");
    sql_query("DELETE FROM snatched WHERE torrentid = $id");
    foreach(explode(".","peers.files.comments.ratings") as $x)
        @mysql_query("DELETE FROM $x WHERE torrent = $id");
    unlink("{$INSTALLER09['torrent_dir']}/$id.torrent");
    $mc1->delete('MyPeers_'.$CURUSER['id']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$tid = (isset($_POST["tid"]) ? 0 + $_POST["tid"] : 0);
	if($tid == 0)
		stderr(":w00t:","wtf are your trying to do!?");
	if (get_row_count("torrents","where id=".$tid) != 1)
		stderr(":w00t:","That is not a torrent !!!!");
	
	$q1 = sql_query("SELECT s.downloaded as sd , t.id as tid, t.name,t.size, u.username,u.id as uid,u.downloaded as ud FROM torrents as t LEFT JOIN snatched as s ON s.torrentid = t.id LEFT JOIN users as u ON u.id = s.userid WHERE t.id =".$tid) or sqlerr(__FILE__, __LINE__);
	while ($a = mysql_fetch_assoc($q1))
	{
		$newd = ($a["ud"] > 0 ? $a["ud"]-$a["sd"] : 0 );
		$new_download[] = "(".$a["uid"].",".$newd.")";
		$tname = $a["name"];
		$msg = "Hey , ".$a["username"]."\n";
		$msg .= "Looks like torrent [b]".$a["name"]."[/b] is nuked and we want to take back the data you downloaded\n";
		$msg .= "So you downloaded ".mksize($a["sd"])." your new download will be ".mksize($newd)."\n";
		$pms[] = "(0,".$a["uid"].",".sqlesc(time()).",".sqlesc($msg).")";
	}
	//==Send the pm !!
	sql_query("INSERT into messages (sender, receiver, added, msg) VALUES ".join(",",$pms)) or sqlerr(__FILE__, __LINE__);
	//==Update user download amount
	sql_query("INSERT INTO users (id,downloaded) VALUES ".join(",",$new_download)." ON DUPLICATE key UPDATE downloaded=values(downloaded)") or sqlerr(__FILE__, __LINE__);
	deletetorrent($tid);
	write_log("Torrent $tname was deleted by ".$CURUSER["username"]." and all users were Re-Paid Download credit");
	header("Refresh: 3; url=staffpanel.php?tool=datareset");
	stderr(":w00t:","it worked! long live Tbdev - Please wait while you are re-directed !");
}
else
{	
$HTMLOUT .= begin_frame();
$HTMLOUT .="<form action='staffpanel.php?tool=datareset&amp;action=datareset' method='post'>
	<fieldset>
	<legend> Reset Ratio for nuked torrents</legend>
    <table width='500' border='1' cellpadding='10' cellspacing='0' style='border-collapse:collapse' align='center'>
    	<tr><td align='right' nowrap='nowrap'>Torrent id</td><td align='left' width='100%'><input type='text' name='tid' size='20' /></td></tr>
        <tr><td style='background:#990033; color:#CCCCCC;' colspan='2'>
        	<ul>
					<li>Torrent id must be a number and only a number!!!</li>
					<li>If the torrent is not nuked or there is not problem with it , don't use this as it will delete the torrent and any other entrys associated with it !</li>
					<li>If you don't know what this will do , <b>go play somewere else</b></li>
				</ul>
			</td></tr>
			<tr><td colspan='2' align='center'><input type='submit' value='Re-pay!' /></td></tr>
		</table>
	</fieldset>
	</form>";

$HTMLOUT .= end_frame();
echo stdhead('Data Reset Manager') . $HTMLOUT . stdfoot();
}
?>