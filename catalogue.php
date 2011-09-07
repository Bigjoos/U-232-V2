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
|   $Author$ EnzoF1,putyn,Bigjoos
|   $URL$
|   $catalogue
|   
+------------------------------------------------
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
require_once(INCL_DIR.'bbcode_functions.php');
require_once INCL_DIR.'html_functions.php';
require_once INCL_DIR.'pager_functions.php';
dbconn(false);
loggedinorreturn();

$lang = array_merge( load_language('global'));

$htmlout ='';

function readMore($text, $char, $link)
{
return (strlen($text) > $char ? substr(htmlspecialchars($text), 0, $char-1) . "...<br /><a href='$link'>Read more</a>": htmlspecialchars($text));
}

            function peer_list($array)
            {
            global $INSTALLER09, $lang;
            $htmlout = '';
		        $htmlout.="<table width='100%' border='1' cellpadding='5' style='border-collapse:collapse'>
		        <tr>
       		  <td align='center' class='colhead'>User</td>
            <td align='center' class='colhead'>Port&amp;Ip</td>
            <td align='center' class='colhead'>Ratio</td>
            <td align='center' class='colhead'>Downloaded</td>
            <td align='center' class='colhead'>Uploaded</td>
            <td align='center' class='colhead'>Started</td>
            <td align='center' class='colhead'>Finished</td>
       </tr>";
       foreach ($array as $p) {
       $time = max(1, (time() - $p["started"]) - (time() - $p["last_action"]));
			 $htmlout.="<tr>
       		<td align='center'><a href='userdetails.php?id={$p["p_uid"]}' >{$p["p_user"]}</a></td>
            <td align='center'>".($CURUSER['class'] >= UC_MODERATOR ? $p["ip"] . ":" . $p["port"] : "xx.xx.xx.xx:xxxx")."</td>
            <td align='center'>".($p["downloaded"] > 0 ? number_format(($p["uploaded"] / $p["downloaded"]), 2) : ($p["uploaded"] > 0 ? "&infin;" : "---"))."</td>
            <td align='center'>".($p["downloaded"] > 0 ? mksize($p["downloaded"]) . " @" . (mksize(($p["downloaded"] - $p["downloadoffset"]) / $time)) . "s": "0kb")."</td>
            <td align='center'>".($p["uploaded"] > 0 ? mksize($p["uploaded"]) . " @" . (mksize(($p["uploaded"] - $p["uploadoffset"]) / $time)) . "s": "0kb")."</td>
            <td align='center'>".(get_date($p["started"], 'LONG',0,1))."</td>
            <td align='center'>".(get_date($p["finishedat"], 'LONG',0,1))."</td>
			</tr>";
    }
    $htmlout.="</table>";
    return $htmlout;
}

$letter = (isset($_GET["letter"]) ? $_GET["letter"] : "");
$search = (isset($_GET["search"]) ? htmlspecialchars($_GET["search"]) : "");

if (strlen($search) > 4) {
    $where = "WHERE t.name LIKE" . sqlesc("%" . $search . "%");
    $p = "search=" . $search . "&amp;";
} elseif (strlen($letter) == 1 && strpos("abcdefghijklmnopqrstuvwxyz", $letter) !== false) {
    $where = "WHERE t.name LIKE '" . $letter . "%'";
    $p = "letter=" . $letter . "&amp;";
} else {
    $where = "WHERE t.name LIKE 'a%'";
    $p = "letter=a&amp;";
    $letter = "a";
}

$count = mysql_fetch_row(sql_query("SELECT count(*) from torrents as t $where"));
$perpage = 10;

$pager = pager($perpage, $count[0], $_SERVER["PHP_SELF"] . "?" . $p);
//$tid='';
//$rows='';
$top='';
$bottom='';
$rows = array();
$tids = array(); 

$t = sql_query("SELECT t.id,t.name,t.leechers,t.seeders,t.poster,t.times_completed as snatched,t.owner,t.size,t.added,t.descr, u.username as user FROM torrents as t LEFT JOIN users AS u on u.id=t.owner $where ORDER BY t.name ASC ".$pager['limit']."") or sqlerr(__FILE__, __LINE__);
while ($ta = mysql_fetch_assoc($t)) {
    $rows[] = $ta;
    $tid[] = $ta["id"];
}
if(isset($tids) && count($tids)) {

    $p = sql_query("SELECT p.id,p.torrent as tid,p.seeder, p.finishedat, p.downloadoffset, p.uploadoffset, p.ip, p.port, p.uploaded, p.downloaded, p.started AS started, p.last_action AS last_action, u.id as p_uid , u.username as p_user FROM peers AS p LEFT JOIN users as u on u.id=p.userid WHERE p.torrent IN (" . join(",", $tid) . ") AND p.seeder = 'yes' AND to_go=0 LIMIT 5") or sqlerr(__FILE__, __LINE__);
    while ($pa = mysql_fetch_assoc($p))
    $peers[$pa["tid"]][] = $pa;
}



		$htmlout.="<div align='center' style='width:90%'>
		<fieldset style='border:2px solid #333333;'>
			<legend style='padding:5xp 0px 0px 5px;'>Search</legend>
				<form  action='".$_SERVER["PHP_SELF"]."' method='get' style='margin:10px;'>
					<input type='text' size='50' name='search' value='".($search ? $search : "Search for a torrent")."' onblur=\"if (this.value == '') this.value='Search for a torrent';\" onfocus=\"if (this.value == 'Search for a torrent') this.value='';\" />&nbsp;<input type='submit' value='search!' />
				</form>";

     for ($i = 97; $i < 123; ++$i) {
    $l = chr($i);
    $L = chr($i - 32);
    if ($l == $letter)
    $htmlout.="<font class=\"sublink-active\">$L</font>\n";
    else
    $htmlout.="<a class=\"sublink\" href=\"" . $_SERVER["PHP_SELF"] . "?letter=" . $l . "\">" . $L . "</a>\n";
}

			$htmlout.="</fieldset></div><br />";

$htmlout .= begin_frame();

if (count($rows) > 0) {
		$htmlout.="<table width='95%' border='1' cellpadding='5' style='border-collapse:collapse'>
    	<tr><td align='left' colspan='2' class='colhead'>Catalogue</td></tr>
    	<tr><td align='left' colspan='2' >{$top}</td></tr>";
    foreach($rows as $row) {
		$htmlout.="<tr>
		 <td align='center' valign='top' nowrap='nowrap'>
        	<table align='center' width='160' border='1' cellpadding='2'>
            	<tr><td align='center' class='colhead'>Upper : <a href='userdetails.php?id=".$row["owner"]."'>".($row["user"] ? $row["user"] : "unknown[" . $row["owner"] . "]")."</a></td></tr>
                <tr><td align='center'>".($row["poster"] ? "<a href=\"" . $row["poster"] . "\"><img src=\"" . $row["poster"] . "\" border=\"0\" width=\"150\" height=\"195\" alt=\"No Poster\" title=\"No Poster\" /></a>" : "<img src=\"pic/noposter.png\" border=\"0\" width=\"150\" alt=\"No Poster\" title=\"No Poster\" />")."</td></tr>
            </table>

        </td>

            <td align='center' width='100%' valign='top'>
			<table width='100%' cellpadding='3' cellspacing='0' border='1' style='border-collapse:collapse;font-weight:bold;'>
			<tr>
				<td align='center' width='100%' rowspan='2' ><a href='details.php?id=".$row["id"]."&amp;hit=1'><b>".substr($row["name"], 0, 60)."</b></a></td>
				<td align='center' class='colhead'>Added</td>
				<td align='center' class='colhead'>Size</td>
				<td align='center' class='colhead'>Snatched</td>
				<td align='center' class='colhead'>S.</td>
				<td align='center' class='colhead'>L.</td>
			</tr>
			<tr>
				<td align='center'>".get_date($row["added"], 'LONG',0,1)."</td>
				<td align='center' nowrap='nowrap'>".(mksize($row["size"]))."</td>
				<td align='center' nowrap='nowrap'>".($row["snatched"] > 0 ? ($row["snatched"] == 1 ? $row["snatched"] . " time" : $row["snatched"] . " times") : 0)."</td>
				<td align='center'>".$row["seeders"]."</td>
				<td align='center'>".$row["leechers"]."</td>
			</tr>
			<tr><td width='100%' colspan='6' class='colhead' >Info.</td></tr>
			<tr><td width='100%' colspan='6' style='font-weight:normal;' >".readMore($row["descr"], 500, "details.php?id=" . $row["id"] . "&amp;hit=1")."</td></tr>
			<tr><td width='100%' colspan='6' class='colhead'>Seeder Info (Top 5 Seeders)</td></tr>
			<tr><td width='100%' colspan='6' style='font-weight:normal;' >".(isset($peers[$row["id"]]) ? peer_list($peers[$row["id"]]) : "No information to show")."</td></tr>
			</table></td></tr>";
		 }
  
		$htmlout.="<tr><td align='left' colspan='2' >{$bottom}</td></tr>
		<tr><td align='right' colspan='2' class='colhead'>Original By EnzoF1 recoded by Putyn, Updated for 09 by Bigjoos</td></tr>
		</table>";
} else
    $htmlout.="<h2>Nothing found!</h2>";

$htmlout .= end_frame();

print stdhead('Catalogue') . $htmlout . stdfoot();
?>