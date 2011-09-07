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
|   $Date$
|   $Revision$
|   $Author$
|   $URL$
|   $top_uploaders
|   $Sir_Snugglebunny,Bigjoos
+------------------------------------------------
*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'bittorrent.php');
require_once(INCL_DIR.'user_functions.php');
require_once(INCL_DIR.'pager_functions.php');

dbconn(false);
loggedinorreturn();

$lang = array_merge( load_language('global') );


$HTMLOUT='';

require(CLASS_DIR.'class_check.php');
class_check(UC_STAFF);
     
  $count1 = get_row_count('torrents');
  $perpage = 15;
  $pager = pager($perpage, $count1, 'staffpanel.php?tool=uploaders&amp;action=uploaders&amp;');
    
   //=== main query
   $res = mysql_query('SELECT COUNT(t.id) as how_many_torrents, t.owner, t.added, u.username, u.id, u.donor, u.suspended, u.class, u.warned, u.enabled, u.chatpost, u.leechwarn, u.pirate, u.king
            FROM torrents AS t LEFT JOIN users as u ON u.id = t.owner GROUP BY t.owner ORDER BY how_many_torrents DESC '.$pager['limit'].'');

if ($count1 > $perpage)
$HTMLOUT .= $pager['pagertop'];

$HTMLOUT .='<table border="0" cellspacing="0" cellpadding="5">
   <tr><td class="colhead" align="center">Rank</td><td class="colhead" align="center">#Torrents</td><td class="colhead" align="left">Member</td><td class="colhead" align="left">Class</td><td class="colhead" align="left">Last Upload</td><td class="colhead" align="center">Send Pm</td></tr>';
$i = 0; 
$count='';
while ($arr = mysql_fetch_assoc($res))
{
$i++;
      //=== change colors
      $count= (++$count)%2;
      $class = ($count == 0 ? 'one' : 'two');
      
$HTMLOUT .='<tr>
<td class="'.$class.'" align="center">'.$i.'</td>
<td class="'.$class.'" align="center">'.$arr ['how_many_torrents'].'</td>
<td class="'.$class.'" align="left">'.format_username($arr).'</td>
<td class="'.$class.'" align="left">'.get_user_class_name($arr ['class']).'</td>
<td class="'.$class.'" align="left">'.get_date($arr ['added'], 'DATE',0,1).'</td>
<td class="'.$class.'" align="center"><a href="sendmessage.php?receiver='.$arr['id'].'"><img src="'.$INSTALLER09['pic_base_url'].'/button_pm.gif" alt="Pm" title="Pm" border="0" /></a></td>
</tr>';
}
$HTMLOUT .='</table>'; 

if ($count1 > $perpage)
$HTMLOUT .= $pager['pagerbottom'];
print stdhead('Uploader Stats') . $HTMLOUT . stdfoot();
?>