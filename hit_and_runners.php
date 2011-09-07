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
require_once(INCL_DIR.'pager_functions.php');
dbconn(false);
loggedinorreturn();
parked();

$lang = array_merge( load_language('global'));

$HTMLOUT ="";

	if ($CURUSER['class'] < UC_MODERATOR) 
	{
	stderr('Error', 'Hey ostie d’enfoiré, espèce de rectum suintant castré. Je me caresse l’entre-jambes juste à envisager de te voir pisser sur un rail 
	de métro pis te voir te prendre 10 milles volts par la graine au festival western de St-Tite, espèce de face de rectum de moufette.');
	}



 $res = sql_query('SELECT COUNT(*) FROM snatched WHERE hit_and_run != \'0\' AND finished = \'yes\'') or sqlerr(__FILE__, __LINE__);
 $row = mysql_fetch_row($res);
 $count = $row[0];
 $perpage = 15;
 $pager = pager($perpage, $count, "hit_and_runners.php?&amp;");
 $hit_and_run_rez = sql_query('SELECT torrentid, userid, hit_and_run FROM snatched WHERE hit_and_run != \'0\' AND finished = \'yes\' ORDER BY userid '.$pager['limit'].'') or sqlerr(__FILE__, __LINE__);
 $HTMLOUT .= $pager['pagertop'];

	$HTMLOUT.="<h2>".(!isset($_GET['really_bad']) ? "Current Hit and Run MoFos who still have a chance" : "Hit and Run MoFos with at least one mark on their perminant record" )."</h2><br /> 
	<a class='altlink' href='?'>Show all current hit and runners</a> || 
	<a class='altlink' href='?really_bad=show_them'>Show only 'real' hit and runners</a><br /><br />
	<table>".(mysql_num_rows($hit_and_run_rez) > 0 ? "
	<tr>
		<td  class='colhead'></td>
		<td  class='colhead'><b>Member</b></td>
		<td class='colhead'><b>On Torrent</b></td>		
		<td class='colhead'><b>Times</b></td>
		<td class='colhead'><b>Stats</b></td>
		<td class='colhead'><b>Add to shit list</b></td>
		<td class='colhead'><b>PM</b></td>" : "<tr>
		<td>No hit and runners at the moment...</td>")."</tr>";
  
  $count2='';
	while ($hit_and_run_arr = mysql_fetch_assoc($hit_and_run_rez)) 
	{

	//=== peers
	$peer_rez = sql_query('SELECT seeder FROM peers WHERE userid='.$hit_and_run_arr['userid'].' AND torrent='.$hit_and_run_arr['torrentid']) or sqlerr(__FILE__, __LINE__);
	$peer_arr = mysql_fetch_assoc($peer_rez);

		//=== if really seeding list them
		if ($peer_arr['seeder'] !== 'yes')
		{

		//=== make sure they are NOT the torrent owner
		$res_check_owner = sql_query('SELECT owner,name,added AS torrent_added FROM torrents WHERE id = '.$hit_and_run_arr['torrentid']) or sqlerr(__FILE__, __LINE__);
		$arr_check_owner  = mysql_fetch_assoc($res_check_owner);
		if ($hit_and_run_arr['userid'] !== $arr_check_owner['owner'])
		{
		//=======change colors
		$count2= (++$count2)%2;
		$class = 'clearalt'.($count2==0?6:7);

		//=== then check to see if there are still seeders / leechers on that torrent
		$res_leechers = sql_query('SELECT COUNT(id)  FROM peers WHERE torrent = '.$hit_and_run_arr['torrentid'].' AND seeder = \'no\' AND to_go > 0 AND userid <> '.$hit_and_run_arr['userid']) or sqlerr(__FILE__, __LINE__);
		$arr_leechers = mysql_fetch_row($res_leechers);
	   
		$res_seeders = sql_query('SELECT COUNT(id)  FROM peers WHERE torrent = '.$hit_and_run_arr['torrentid'].' AND seeder = \'yes\' AND userid != '.$hit_and_run_arr['userid']) or sqlerr(__FILE__, __LINE__);
		$arr_seeders = mysql_fetch_row($res_seeders);
          
		//=== get snatched info
		$snatched_rez = sql_query('SELECT *, snatched.start_date  AS st FROM snatched WHERE torrentid='.$hit_and_run_arr['torrentid'].' AND userid='.$hit_and_run_arr['userid']) or sqlerr(__FILE__, __LINE__);
		$snatched_arr = mysql_fetch_assoc($snatched_rez);
		
	//=== get user info
    $user_rez = sql_query('SELECT id, avatar, username, uploaded, downloaded, class, hit_and_run_total, donor, warned, enabled, chatpost, leechwarn, pirate, king FROM users WHERE id = '.$hit_and_run_arr['userid']) or sqlerr(__FILE__, __LINE__);    
    $user_arr = mysql_fetch_assoc($user_rez);
    
	//=== get count of hit and runs by member
	$num_hit_and_runs = sql_query('SELECT COUNT(id) FROM snatched WHERE mark_of_cain = \'yes\' AND userid ='.$hit_and_run_arr['userid']) or sqlerr(__FILE__, __LINE__);  
	$arr_hit_and_runs = mysql_fetch_row($num_hit_and_runs);
  $ratio_site = member_ratio($user_arr['uploaded'], $user_arr['downloaded']);
  $ratio_torrent = member_ratio($snatched_arr['uploaded'], $snatched_arr['downloaded']);
  $avatar = avatar_stuff($user_arr);
  //=== get times per class
  $torrent_needed_seed_time = ($snatched_arr['st'] - $arr_check_owner['torrent_added']);
  switch ($user_arr['class'])
  { 
  case UC_USER:
  $days_3 = 3*86400; //== 3 days
  $days_14 = 2*86400; //== 2 days
  $days_over_14 = 86400; //== 1 day
  break;
  case UC_POWER_USER:
  $days_3 = 2*86400; //== 2 days
  $days_14 = 129600; //== 36 hours
  $days_over_14 = 64800; //== 18 hours
  break;
  case UC_UPLOADER:
  $days_3 = 129600; //== 36 hours
  $days_14 = 86400; //== 24 hours
  $days_over_14 = 43200; //== 12 hours
  break;
  case UC_VIP || UC_MODERATOR || UC_ADMINISTRATOR || UC_SYSOP:
  $days_3 = 86400; //== 24 hours
  $days_14 = 43200; //== 12 hours
  $days_over_14 = 21600; //== 6 hours
  break;
  }

  switch(true) 
  {
  case (($snatched_arr['st'] - $arr_check_owner['torrent_added']) < 7*86400):
  $minus_ratio = ($days_3 - $snatched_arr['seedtime']);
  break;
  case (($snatched_arr['st'] - $arr_check_owner['torrent_added']) < 21*86400):
  $minus_ratio = ($days_14 - $snatched_arr['seedtime']);
  break;
  case (($snatched_arr['st'] - $arr_check_owner['torrent_added']) >= 21*86400):
  $minus_ratio = ($days_over_14 - $snatched_arr['seedtime']);
  break;
  }
  $minus_ratio = (preg_match('/-/i',$minus_ratio) ? 0 : $minus_ratio); 
  $color = ($minus_ratio > 0 ? get_ratio_color($minus_ratio) : 'limegreen');

	if ($minus_ratio > 0)
	{
	$HTMLOUT.="<tr>
	<td class='".$class."' valign='middle' align='left'>".$avatar."</td>
	<td  class='".$class."' valign='middle' align='left'><a class='altlink' href='userdetails.php?id=".$hit_and_run_arr['userid']."&amp;completed=1'></a>
	[ ".get_user_class_name($user_arr['class'])." ] " . format_username($user_arr) . "<br />
	Total Hit & Runs: <b>".$arr_hit_and_runs[0]." </b></td>
	<td  class='".$class."' valign='middle' align='left'><a class='altlink' href='details.php?id=".$hit_and_run_arr['torrentid']."'>".$arr_check_owner['name']."</a><br />
	Seeding: No <br /><font color='red'>Currently: ".($arr_leechers[0] != 1 ? $arr_leechers[0].' Others still leeching this torrent' : $arr_leechers[0].' Other still leeching this torrent')."<br />
	</font><font color='limegreen'>Currently: ".($arr_seeders[0] != 1 ? $arr_seeders[0].' Others still seeding this torrent' : $arr_seeders[0].' Other still seeding this torrent')."</font><br /><br />
	**Should still seed for: ".mkprettytime($minus_ratio)."</td>
	<td class='".$class."' valign='middle' align='left'><b><font class='small' color='red'>Finished DL at: ".get_date($snatched_arr['complete_date'], 'DATE',0,1)." </font><br />
	<font class='small' color='orange'>Stopped seeding at: ".get_date($hit_and_run_arr['hit_and_run'], 'DATE',0,1)."</font><br />
	<font class='small' color='limegreen'>Seeded for: ".mkprettytime($snatched_arr['seedtime'])."</font><br />
	<font class='small' color='pink'>Last torrent action: ".get_date($snatched_arr['last_action'], 'DATE',0,1) . "</font></b> </td>
	<td class='".$class."' valign='middle' align='left'><font class='small' color='limegreen'>Uploaded: ".mksize($snatched_arr['uploaded'])."</font><br />
	<font class='small' color='red'>Downloaded  ".mksize($snatched_arr['downloaded'])."</font><br />
	Torrent ratio:  <font class='small' color='".get_ratio_color($ratio_torrent)."'>".$ratio_torrent."</font><br />
	Site ratio:  <font class='small' color='".get_ratio_color($ratio_site)."' title='Includes all bonus and karma stuff'>".$ratio_site."</font></td>
	<td class='".$class."' valign='middle' align='left'><a class='altlink' href='staffpanel.php?tool=shit_list&amp;action=new&amp;shit_list_id=".$hit_and_run_arr['userid']."&amp;return_to=hit_and_runners.php'>
	Add to shit list</a></td>
	<td class='".$class."' valign='middle' align='left'><a class='altlink' href='sendmessage.php?receiver=".$hit_and_run_arr['userid']."'>PM</a> </td>
	</tr>"; 			       
	}
	
}//=== end if not owner
}//=== if not seeding list them
}//=== end of while loop
$HTMLOUT.="</table>";
$HTMLOUT .= $pager['pagerbottom'];
print stdhead('Hit & Run Mofo\'s', false) . $HTMLOUT . stdfoot();
?>