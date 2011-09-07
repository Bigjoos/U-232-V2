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
require_once(INCL_DIR.'html_functions.php');
dbconn();
error_reporting(0);


$lang = array_merge( load_language('global'), load_language('staff'));

$HTMLOUT ="";
$firstline='';
$staff_table   = array();
$col = '';
// Get current datetime
$dt = sqlesc(time() - 60);
// Search User Database for Moderators and above and display in alphabetical order
$res = sql_query("SELECT * FROM users WHERE class >= ".UC_UPLOADER." AND status='confirmed' ORDER BY username") or sqlerr();

while ($arr = mysql_fetch_assoc($res)) { 
    $staff_table   = ($staff_table   ? $staff_table   : ''); 
    $staff_table[$arr['class']] = $staff_table[$arr['class']] . "<td class='staffembedded'><a class='altlink' href='{$INSTALLER09['baseurl']}/userdetails.php?id=" . $arr['id'] . "'>" . $arr['username'] . "</a></td><td class='staffembedded'> " . ("'" . $arr['last_access'] . "'" > $dt ? "<img src='" . $INSTALLER09['pic_base_url'] . "user_online.gif' border='0' alt='online' />":"<img src='" . $INSTALLER09['pic_base_url'] . "user_offline.gif' border='0' alt='offline' />") . "</td>" . "<td class='staffembedded'><a href='{$INSTALLER09['baseurl']}/sendmessage.php?receiver=" . $arr['id'] . "'>" . "<img src='" . $INSTALLER09['pic_base_url'] . "pm.gif' alt='Pm'  border='0' /></a></td>" . " ";
    // Show 3 staff per row, separated by an empty column
     ++ $col[$arr['class']];
    if ($col[$arr['class']] <= 2)
        $staff_table[$arr['class']] = $staff_table[$arr['class']] . "<td class='staffembedded'>&nbsp;</td>";
    else {
        $staff_table[$arr['class']] = $staff_table[$arr['class']] . "</tr><tr style='height:15px'>";
        $col[$arr['class']] = 0;
    }

}

$HTMLOUT .= begin_main_frame();


$HTMLOUT .="<table width='725' cellspacing='0' align='center'>
<tr>
<td class='colhead' colspan='15'>General support question's should preferably be directed to these user's. Note that they are volunteer's, giving away their time and effort to help you.</td></tr>
<!-- Define table column widths -->
<tr>
<td class='staffembedded' width='105'>&nbsp;</td>
<td class='staffembedded' width='25'>&nbsp;</td>
<td class='staffembedded' width='35'>&nbsp;</td>
<td class='staffembedded' width='85'>&nbsp;</td>
<td class='staffembedded' width='105'>&nbsp;</td>
<td class='staffembedded' width='25'>&nbsp;</td>
<td class='staffembedded' width='35'>&nbsp;</td>
<td class='staffembedded' width='85'>&nbsp;</td>
<td class='staffembedded' width='105'>&nbsp;</td>
<td class='staffembedded' width='25'>&nbsp;</td>
<td class='staffembedded' width='35'>&nbsp;</td>
</tr>

<tr><td class='staffembedded' colspan='15'>&nbsp;</td></tr>
<tr><td class='staffembedded' colspan='15'><b>Sys0ps</b></td></tr>
<tr><td class='staffembedded' colspan='15'><hr style='color:#A83838' size='1' /></td></tr>
<tr style='height:15px'>{$staff_table[UC_SYSOP]}</tr>

<tr><td class='staffembedded' colspan='15'>&nbsp;</td></tr>
<tr><td class='staffembedded' colspan='15'><b>Admin</b></td></tr>
<tr><td class='staffembedded' colspan='15'><hr style='color:#A83838' size='1' /></td></tr>
<tr style='height:15px'>{$staff_table[UC_ADMINISTRATOR]}</tr>

<tr><td class='staffembedded' colspan='15'>&nbsp;</td></tr>
<tr><td class='staffembedded' colspan='15'><b>Mods</b></td></tr>
<tr><td class='staffembedded' colspan='15'><hr style='color:#A83838' size='1' /></td></tr>
<tr style='height:15px'>{$staff_table[UC_MODERATOR]}</tr>

<tr><td class='staffembedded' colspan='15'>&nbsp;</td></tr>
<tr><td class='staffembedded' colspan='15'><b>Uploader</b></td></tr>
<tr><td class='staffembedded' colspan='15'><hr style='color:#A83838' size='1' /></td></tr>
<tr style='height:15px'>{$staff_table[UC_UPLOADER]}</tr></table>";

$HTMLOUT .= end_main_frame();

$HTMLOUT .= begin_main_frame();
$dt = sqlesc(time() - 180);
$res = sql_query("SELECT id,username,last_access,supportfor,country FROM users WHERE support='yes' AND status='confirmed' ORDER BY username LIMIT 10") or sqlerr();
while ($arr = mysql_fetch_assoc($res)) {
require_once(CACHE_DIR.'countries.php');
foreach ($countries as $c)
if ($arr["country"] == $c["id"]) {
$flag = $c["flagpic"];
$cname = $c["name"];
}


$firstline .= "<tr style=\"height:15px\"><td class=\"embedded\"><a class=\"altlink\" href=\"{$INSTALLER09['baseurl']}/userdetails.php?id=" . $arr['id'] . "\">" . $arr['username'] . "</a></td>
<td class=\"embedded\"> " . ("'" . $arr['last_access'] . "'" > $dt ?" <img src=\"" . $INSTALLER09['pic_base_url'] . "user_online.gif\"  border=\"0\" alt=\"online\" />":"<img src=\"" . $INSTALLER09['pic_base_url'] . "user_offline.gif\" border=\"0\" alt=\"offline\" />") . "</td>" . "<td class=\"embedded\"><a href=\"{$INSTALLER09['baseurl']}/sendmessage.php?receiver=" . $arr['id'] . "\">" . "<img src=\"" . $INSTALLER09['pic_base_url'] . "pm.gif\" alt=\"Pm\" border=\"0\" /></a></td>" . "<td class=\"embedded\"><img src=\"" . $INSTALLER09['pic_base_url'] . "flag/$flag\" alt=\"" . $cname . "\" title=\"" . $cname . "\" border=\"0\" /></td>" . "<td class=\"embedded\">" . $arr['supportfor'] . "</td></tr>\n";
}
$HTMLOUT .="<table width='725' cellspacing='0' align='center'>
<tr>
<td class='embedded' colspan='11'><br /><b>{$lang['staff_fls']}</b><br /><br /><b>{$lang['staff_asup']}</b><br /><br /></td></tr>
<!-- Define table column widths -->
<tr>
<td class='embedded' width='30'><b>{$lang['staff_username']}</b></td>
<td class='embedded' width='5'><b>{$lang['staff_online']}</b></td>
<td class='embedded' width='5'><b>{$lang['staff_pm']}</b></td>
<td class='embedded' width='85'><b>{$lang['staff_lang']}</b></td>
<td class='embedded' width='200'><b>{$lang['staff_supportfor']}</b></td>
</tr>
<tr><td class='embedded' colspan='11'><hr style='color:#A83838' size='1' /></td></tr>
{$firstline}
</table>";

$HTMLOUT .= end_main_frame();

print stdhead('Staff') . $HTMLOUT . stdfoot();

?>