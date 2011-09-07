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
require_once INCL_DIR.'bbcode_functions.php';
require_once INCL_DIR.'html_functions.php';
require_once CLASS_DIR.'page_verify.php';
require_once(INCL_DIR.'function_onlinetime.php');
dbconn(false);
loggedinorreturn();
error_reporting(0);
$lang = array_merge( load_language('global'), load_language('userdetails') );

if (function_exists('parked'))
parked();

$newpage = new page_verify(); 
$newpage->create('mdk1@@9'); 

$stdfoot = array(/** include js **/'js' => array('popup','java_klappe'));

function calctime($val)
	{
		$days=intval($val / 86400);
		$val-=$days*86400;
		$hours=intval($val / 3600);
		$val-=$hours*3600;
		$mins=intval($val / 60);
		$secs=$val-($mins*60);
		return "&nbsp;$days days, $hours hrs, $mins minutes";
	}

function snatchtable($res) {
global $INSTALLER09, $lang;
$htmlout = '';
 $htmlout = "<table class='main' border='1' cellspacing='0' cellpadding='5'>
 <tr>
 <td class='colhead'>Category</td>
 <td class='colhead'>Torrent</td>
 <td class='colhead'>Up.</td>
 <td class='colhead'>Rate</td>
 <td class='colhead'>Downl.</td>
 <td class='colhead'>Rate</td>
 <td class='colhead'>Ratio</td>
 <td class='colhead'>Activity</td>
 <td class='colhead'>Finished</td>
 </tr>";

 while ($arr = mysql_fetch_assoc($res)) {

 $upspeed = ($arr["upspeed"] > 0 ? mksize($arr["upspeed"]) : ($arr["seedtime"] > 0 ? mksize($arr["uploaded"] / ($arr["seedtime"] + $arr["leechtime"])) : mksize(0)));
 $downspeed = ($arr["downspeed"] > 0 ? mksize($arr["downspeed"]) : ($arr["leechtime"] > 0 ? mksize($arr["downloaded"] / $arr["leechtime"]) : mksize(0)));
 $ratio = ($arr["downloaded"] > 0 ? number_format($arr["uploaded"] / $arr["downloaded"], 3) : ($arr["uploaded"] > 0 ? "Inf." : "---"));

 $htmlout .= "<tr>
 <td style='padding: 0px'><img src='pic/".htmlspecialchars($arr["catimg"])."' alt='".htmlspecialchars($arr["catname"])."' width='42' height='42' /></td>
 <td><a href='details.php?id=$arr[torrentid]'><b>".(strlen($arr["name"]) > 50 ? substr($arr["name"], 0, 50 - 3)."..." : $arr["name"])."</b></a></td>
 <td>".mksize($arr["uploaded"])."</td>
 <td>$upspeed/s</td>
 <td>".mksize($arr["downloaded"])."</td>
 <td>$downspeed/s</td>
 <td>$ratio</td>
 <td>".mkprettytime($arr["seedtime"] + $arr["leechtime"])."</td>
 <td>".($arr["complete_date"] <> "0" ? "<font color='green'><b>Yes</b></font>" : "<font color='red'><b>No</b></font>")."</td>
 </tr>\n";
 }
 $htmlout .= "</table>\n";

 return $htmlout;
}

function maketable($res)
    {
      global $INSTALLER09, $lang;
      
      $htmlout = '';
      
      $htmlout .= "<table class='main' border='1' cellspacing='0' cellpadding='5'>" .
        "<tr><td class='colhead' align='center'>{$lang['userdetails_type']}</td><td class='colhead'>{$lang['userdetails_name']}</td><td class='colhead' align='center'>{$lang['userdetails_size']}</td><td class='colhead' align='right'>{$lang['userdetails_se']}</td><td class='colhead' align='right'>{$lang['userdetails_le']}</td><td class='colhead' align='center'>{$lang['userdetails_upl']}</td>\n" .
        "<td class='colhead' align='center'>{$lang['userdetails_downl']}</td><td class='colhead' align='center'>{$lang['userdetails_ratio']}</td></tr>\n";
      foreach ($res as $arr)
      {
        if ($arr["downloaded"] > 0)
        {
          $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
          $ratio = "<font color='" . get_ratio_color($ratio) . "'>$ratio</font>";
        }
        else
          if ($arr["uploaded"] > 0)
            $ratio = "{$lang['userdetails_inf']}";
          else
            $ratio = "---";
      $catimage = "{$INSTALLER09['pic_base_url']}caticons/{$arr['image']}";
      $catname = htmlspecialchars($arr["catname"]);
      $catimage = "<img src=\"".htmlspecialchars($catimage) ."\" title=\"$catname\" alt=\"$catname\" width='42' height='42' />";
      $size = str_replace(" ", "<br />", mksize($arr["size"]));
      $uploaded = str_replace(" ", "<br />", mksize($arr["uploaded"]));
      $downloaded = str_replace(" ", "<br />", mksize($arr["downloaded"]));
      $seeders = number_format($arr["seeders"]);
      $leechers = number_format($arr["leechers"]);
        $htmlout .= "<tr><td style='padding: 0px'>$catimage</td>\n" .
        "<td><a href='details.php?id=$arr[torrent]&amp;hit=1'><b>" . htmlspecialchars($arr["torrentname"]) .
        "</b></a></td><td align='center'>$size</td><td align='right'>$seeders</td><td align='right'>$leechers</td><td align='center'>$uploaded</td>\n" .
        "<td align='center'>$downloaded</td><td align='center'>$ratio</td></tr>\n";
      }
      $htmlout .= "</table>\n";
      return $htmlout;
    }
    
    $id = 0 + $_GET["id"];
    if (!is_valid_id($id))
    stderr("Error", "{$lang['userdetails_bad_id']}");
    
    $user = $mc1->get_value('user'.$id);
    if ($user === false) {
    $r1 = sql_query("SELECT u.*,s.last_status,s.last_update FROM users as u LEFT JOIN ustatus as s ON u.id = s.userid WHERE u.id=$id") or sqlerr();
    $user = mysql_fetch_assoc($r1) or stderr("Error", "{$lang['userdetails_no_user']}");
    $mc1->cache_value('user'.$id, $user, $INSTALLER09['expires']['user_cache']);
    }
    
    if ($user["status"] == "pending") 
    stderr("Error","User is still pending.");
  
  //===  paranoid settings
	if ($user['paranoia'] == 3 && $CURUSER['class'] < UC_STAFF && $CURUSER['id'] <> $id) 
	stderr('Error!','<span style="font-weight: bold; text-align: center;"><img src="pic/smilies/tinfoilhat.gif" alt="I wear a tin-foil hat!" title="I wear a tin-foil hat!" /> 
	This members paranoia settings are at tinfoil hat levels!!! <img src="pic/smilies/tinfoilhat.gif" alt="I wear a tin-foil hat!" title="I wear a tin-foil hat!" /></span>');
    
  //=== delete H&R
	if(isset($_GET['delete_hit_and_run']) && $CURUSER['class'] >= UC_STAFF)
	{
		$delete_me = isset($_GET['delete_hit_and_run']) ? intval($_GET['delete_hit_and_run']) : 0;
			if (!is_valid_id($delete_me))
				stderr('Error!','Bad ID');

	sql_query('UPDATE snatched SET hit_and_run = \'0\', mark_of_cain = \'no\' WHERE id = '.$delete_me) or sqlerr(__FILE__,__LINE__);
		if (@mysql_affected_rows() === 0)
		{
		stderr('Error!','H&R not deleted!');
		}

		header('Location: ?id='.$id.'&completed=1');
	die();
	}

    $user_torrents = $mc1->get_value('user_torrents_'.$id);
    if($user_torrents === false ) {
    $a = sql_query("SELECT t.id, t.name, t.seeders, t.leechers, c.name AS cname, c.image FROM torrents t LEFT JOIN categories c ON t.category = c.id WHERE t.owner = $id ORDER BY t.name") or sqlerr(__FILE__,__LINE__);
    while($user_torrents2 = mysql_fetch_assoc($a)) 
    $user_torrents[] = $user_torrents2;
    $mc1->cache_value('user_torrents_'.$id, $user_torrents, $INSTALLER09['expires']['user_torrents']);
    }
    if (count($user_torrents) > 0)
    {
    $torrents = "<table class='main' border='1' cellspacing='0' cellpadding='5'>\n" .
    "<tr><td class='colhead'>{$lang['userdetails_type']}</td><td class='colhead'>{$lang['userdetails_name']}</td><td class='colhead'>{$lang['userdetails_seeders']}</td><td class='colhead'>{$lang['userdetails_leechers']}</td></tr>\n";
    if ($user_torrents)
    {
    foreach($user_torrents as $a) {
        $cat = "<img src=\"". htmlspecialchars("{$INSTALLER09['pic_base_url']}caticons/{$a['image']}") ."\" title=\"{$a['cname']}\" alt=\"{$a['cname']}\" />";
          $torrents .= "<tr><td style='padding: 0px'>$cat</td><td><a href='details.php?id=" . $a['id'] . "&amp;hit=1'><b>" . htmlspecialchars($a["name"]) . "</b></a></td>" .
            "<td align='right'>{$a['seeders']}</td><td align='right'>{$a['leechers']}</td></tr>\n";
      }
      $torrents .= "</table>";
    } else {
    //== If there are no torrents
    if (empty($user_torrents))
    $torrents .= "</table>";
    }
    }

    if ($user['ip'] && ($CURUSER['class'] >= UC_STAFF || $user['id'] == $CURUSER['id']))
    {
        $dom = @gethostbyaddr($user['ip']);
        $addr = ($dom == $user['ip'] || @gethostbyname($dom) != $user['ip']) ? $user['ip'] : $user['ip'].' ('.$dom.')';
    }


    if ($user['added'] == 0)
      $joindate = "{$lang['userdetails_na']}";
    else
      $joindate = get_date( $user['added'],'');
    $lastseen = $user["last_access"];
    if ($lastseen == 0)
      $lastseen = "{$lang['userdetails_never']}";
    else
    {
      $lastseen = get_date( $user['last_access'],'',0,1);
    }

      //==comments
      $torrentcomments = $mc1->get_value('torrent_comments_'.$id);
      if ($torrentcomments === false) {
      $res = sql_query("SELECT COUNT(id) FROM comments WHERE user=" . $user['id']) or sqlerr(__FILE__,__LINE__);
      list($torrentcomments) = mysql_fetch_row($res); 
      $mc1->cache_value('torrent_comments_'.$id, $torrentcomments, $INSTALLER09['expires']['torrent_comments']);
      }
      //==posts
      $forumposts = $mc1->get_value('forum_posts_'.$id);
      if ($forumposts === false) {
      $res = sql_query("SELECT COUNT(id) FROM posts WHERE user_id=" . $user['id']) or sqlerr(__FILE__,__LINE__);
      list($forumposts) = mysql_fetch_row($res); 
      $mc1->cache_value('forum_posts_'.$id, $forumposts, $INSTALLER09['expires']['forum_posts']);
      }

    //==country by pdq
    function countries() {
    global $mc1;
    $ret = $mc1->get_value('countries::arr');
    if ($ret === false) {
        $res = sql_query("SELECT id, name, flagpic FROM countries ORDER BY name ASC") or sqlerr(__FILE__, __LINE__);
        while ($row = mysql_fetch_assoc($res))
            $ret[] = $row;
        $mc1->cache_value('countries::arr', $ret, $INSTALLER09['expires']['user_flag']);
    }
    return $ret;
    }
    
    $country = '';
    $countries = countries();
    foreach ($countries as $cntry)
    if ($cntry['id'] == $user['country']) {
    $country = "<td class='embedded'><img src=\"{$INSTALLER09['pic_base_url']}flag/{$cntry['flagpic']}\" alt=\"". htmlspecialchars($cntry['name']) ."\" style='margin-left: 8pt' /></td>";
    break;
    }

    $res = sql_query("SELECT p.torrent, p.uploaded, p.downloaded, p.seeder, t.added, t.name as torrentname, t.size, t.category, t.seeders, t.leechers, c.name as catname, c.image FROM peers p LEFT JOIN torrents t ON p.torrent = t.id LEFT JOIN categories c ON t.category = c.id WHERE p.userid=$id") or sqlerr();
    while ($arr = mysql_fetch_assoc($res))
    {
        if ($arr['seeder'] == 'yes')
            $seeding[] = $arr;
        else
            $leeching[] = $arr;
    }
  
    $HTMLOUT = '';
    if ($user['anonymous'] == 'yes' && ($CURUSER['class'] < UC_STAFF && $user["id"] != $CURUSER["id"]))
    {
	  $HTMLOUT .= "<table width='100%' border='1' cellspacing='0' cellpadding='5' class='main'>";
	  $HTMLOUT .= "<tr><td colspan='2' align='center'>{$lang['userdetails_anonymous']}</td></tr>";
	  if ($user["avatar"])
	  $HTMLOUT .= "<tr><td colspan='2' align='center'><img src='" . htmlspecialchars($user["avatar"]) . "'></td></tr>\n";
	  if ($user["info"])
	  $HTMLOUT .= "<tr valign='top'><td align='left' colspan='2' class=text bgcolor='#F4F4F0'>'" . format_comment($user["info"]) . "'</td></tr>\n";
    $HTMLOUT .= "<tr><td colspan='2' align='center'><form method='get' action='{$INSTALLER09['baseurl']}/sendmessage.php'><input type='hidden' name='receiver' value='" .$user["id"] . "' /><input type='submit' value='{$lang['userdetails_sendmess']}' style='height: 23px' /></form>";
	  if ($CURUSER['class'] < UC_STAFF && $user["id"] != $CURUSER["id"])
	  {
	  $HTMLOUT .= end_main_frame();
	  echo stdhead('Anonymous user') . $HTMLOUT . stdfoot();
    die;
  	}
    $HTMLOUT .= "</td></tr></table><br />";
    }
    
    $enabled = $user["enabled"] == 'yes';
    $HTMLOUT .= "<table class='main' border='0' cellspacing='0' cellpadding='0'>".
    "<tr><td class='embedded'><h1 style='margin:0px'>" . format_username($user, true) . "</h1></td>$country</tr></table>\n";
   
    if ($user["parked"] == 'yes')
 	  $HTMLOUT .= "<p><b>{$lang['userdetails_parked']}</b></p>\n";
    
      if (!$enabled)
      $HTMLOUT .= "<p><b>{$lang['userdetails_disabled']}</b></p>\n";
      elseif ($CURUSER["id"] <> $user["id"])
      {
      $friends = $mc1->get_value('Friends_'.$id);
      if ($friends === false) {
      $r3 = sql_query("SELECT id FROM friends WHERE userid=$CURUSER[id] AND friendid=$id") or sqlerr(__FILE__, __LINE__);
      $friends = mysql_num_rows($r3);
      $mc1->cache_value('Friends_'.$id, $friends, $INSTALLER09['expires']['user_friends']);
      }
      
      $blocks = $mc1->get_value('Blocks_'.$id);
      if ($blocks === false) {
      $r4 = sql_query("SELECT id FROM blocks WHERE userid=$CURUSER[id] AND blockid=$id") or sqlerr(__FILE__, __LINE__);
      $blocks = mysql_num_rows($r4);
      $mc1->cache_value('Blocks_'.$id, $blocks, $INSTALLER09['expires']['user_blocks']);
      }
       
      if ($friends > 0)
      $HTMLOUT .= "<p>(<a href='friends.php?action=delete&amp;type=friend&amp;targetid=$id'>{$lang['userdetails_remove_friends']}</a>)</p>\n";
      /*else*/
      if($blocks > 0)
      $HTMLOUT .= "<p>(<a href='friends.php?action=delete&amp;type=block&amp;targetid=$id'>{$lang['userdetails_remove_blocks']}</a>)</p>\n";
      else
      {
      $HTMLOUT .= "<p>(<a href='friends.php?action=add&amp;type=friend&amp;targetid=$id'>{$lang['userdetails_add_friends']}</a>)";
      $HTMLOUT .= " - (<a href='friends.php?action=add&amp;type=block&amp;targetid=$id'>{$lang['userdetails_add_blocks']}</a>)</p>\n";
      }
      }
     
    //== 09 Shitlist by Sir_Snuggles
    if ($CURUSER['class'] >= UC_STAFF){
    $shitty = '';
    $shit_list = $mc1->get_value('shit_list_'.$id);
    if ($shit_list === false) {
    $check_if_theyre_shitty = sql_query("SELECT suspect FROM shit_list WHERE userid=".sqlesc($CURUSER['id'])." AND suspect=".$id) or sqlerr(__FILE__, __LINE__);
    list($shit_list) = mysql_fetch_row($check_if_theyre_shitty); 
    $mc1->cache_value('shit_list_'.$id, $shit_list, $INSTALLER09['expires']['shit_list']);
    }
    
    if ($shit_list > 0){
    $HTMLOUT .="<br /><b>This member is on your shit list click <a class='altlink' href='staffpanel.php?tool=shit_list&amp;action=shit_list'>HERE</a> to see your shit list</b>";
    $shitty = "<img src='pic/smilies/shit.gif' alt='Shit' title='Shit' />";
    }
    else		
    $HTMLOUT .="<br /><a class='altlink' href='staffpanel.php?tool=shit_list&amp;action=shit_list&amp;action2=new&amp;shit_list_id=".$id."&amp;return_to=userdetails.php?id=".$id."'><b>Add member to your shit list</b></a>";
    }

   // ===donor count down
   if ($user["donor"] && $CURUSER["id"] == $user["id"] || $CURUSER["class"] == UC_SYSOP) {
   $donoruntil = $user['donoruntil'];
   if ($donoruntil == '0')
   $HTMLOUT.= "";
   else {
   $HTMLOUT.= "<br /><b>Donated Status Until - ".get_date($user['donoruntil'], 'DATE'). "";
   $HTMLOUT.=" [ " . mkprettytime($donoruntil - TIME_NOW) . " ] To go...</b><font size=\"-2\"> To re-new your donation click <a class='altlink' href='{$INSTALLER09['baseurl']}/donate.php'>Here</a>.</font><br /><br />\n";
   }
   }
    
    if ($CURUSER['id'] == $user['id'])
    $HTMLOUT.="<h1><a href='{$INSTALLER09['baseurl']}/usercp.php'>Edit My Profile</a></h1>
 	  <h1><a href='{$INSTALLER09['baseurl']}/view_announce_history.php'>View My Announcements</a></h1>";
    
    if ($CURUSER['class'] >= UC_STAFF)
	  $HTMLOUT .= "<h1><a href='{$INSTALLER09['baseurl']}/userimages.php?user=".$user['username']."'>{$lang['userdetails_viewimages']}</a></h1>";
    
    if ($CURUSER['id'] != $user['id'])
    $HTMLOUT .="<h1><a href='{$INSTALLER09['baseurl']}/sharemarks.php?id=$id'>View sharemarks</a></h1>\n";
    
    $HTMLOUT .= begin_main_frame();

    $HTMLOUT .= "<table width='100%' border='1' cellspacing='0' cellpadding='5'>";
    
    //==Qlogin by stonebreath and laffin
    if ($CURUSER['class'] >= UC_STAFF && $id == $CURUSER['id']) {
    $hash1 = $mc1->get_value('hash1_'.$id);
    if ($hash1 === false) {
    $res = sql_query("SELECT hash1 FROM users WHERE id = ".sqlesc($CURUSER['id'])." AND class >= ".UC_STAFF) or sqlerr(__FILE__, __LINE__);
    $hash1 = mysql_fetch_assoc($res);
    $mc1->cache_value('hash1_'.$id, $hash1, $INSTALLER09['expires']['user_hash']);
    }
    $arr = $hash1;
    if ($arr['hash1'] != '') { 
    $HTMLOUT.="<tr><td class='rowhead'>Login Link<br /><a href='createlink.php?action=reset&amp;id=".$CURUSER['id']."' target='_blank'>Reset Link</a></td><td align='left'>{$INSTALLER09['baseurl']}/pagelogin.php?qlogin=".$arr['hash1']."</td></tr>";
    } else { 
    $HTMLOUT.="<tr><td class='rowhead'>Login Link</td><td align='left'><a href='createlink.php?id=".$CURUSER['id']."' target='_blank'>Create link</a></td></tr>";
    } 
    }
    //==End
    
    /* Flush all torrents mod */
    if ($CURUSER['class'] >= UC_STAFF){
    $un = $user["username"];
    $HTMLOUT .= "<tr><td class='rowhead' width='1%'>{$lang['userdetails_flush']}</td><td align='left' width='99%'>".("{$lang['userdetails_flush1']}<a href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=flush&amp;action=flush&amp;id=$id'><b>".htmlspecialchars($un)."</b></a>\n")."</td></tr>";
    }
    $HTMLOUT .= "<tr><td class='rowhead' width='1%'>{$lang['userdetails_joined']}</td><td align='left' width='99%'>{$joindate}</td></tr>
    <tr><td class='rowhead'>{$lang['userdetails_seen']}</td><td align='left'>{$lastseen}</td></tr>";
    //== Online time
    //if($user['onlinetime'] > 0)
    $onlinetime = time_return($user['onlinetime']);
    //else
    //$onlinetime = "This user has no online time recorded";
    $HTMLOUT .="<tr><td class='rowhead' width='1%'>Total Online</td><td align='left' width='99%'>{$onlinetime}</td></tr>";
    // end
    $member_reputation = get_reputation($user, 'users');
    $HTMLOUT .= "<tr><td class='rowhead' valign='top' align='right' width='1%'>{$lang['userdetails_rep']}</td><td align='left' width='99%'>{$member_reputation}<br />
    </td></tr>";
    
    //==09 Birthday mod
    $age = $birthday ='';
    if ($user['birthday'] != "0000-00-00") {
    $current = gmdate("Y-m-d", time());
    list($year2, $month2, $day2) = explode('-', $current);
    $birthday = $user["birthday"];
    $birthday = date("Y-m-d", strtotime($birthday));
    list($year1, $month1, $day1) = explode('-', $birthday);
    if ($month2 < $month1) {
        $age = $year2 - $year1 - 1;
    }
    if ($month2 == $month1) {
        if ($day2 < $day1) {
            $age = $year2 - $year1 - 1;
        } else {
            $age = $year2 - $year1;
        }
    }
    if ($month2 > $month1) {
        $age = $year2 - $year1;
    }
    $HTMLOUT .="<tr><td class='rowhead'>Age</td><td align='left'>".htmlentities($age)."</td></tr>\n";
    $birthday = date("Y-m-d", strtotime($birthday));
    $HTMLOUT .="<tr><td class='rowhead'>Birthday</td><td align='left'>".htmlentities($birthday)."</td></tr>\n";
    }
    //==End
    
    //=== member contact stuff
	  $HTMLOUT .= (($CURUSER['class'] >= UC_STAFF || $user['show_email'] === 'yes') ? '
		<tr>
			<td class="rowhead">Email</td>
			<td align="left"><a class="altlink" href="mailto:'.htmlspecialchars($user['email']).'"  title="click to email" target="_blank"><img src="pic/email.gif" alt="email" width="25" /> Send Email</a></td>
		</tr>' : '').($user['google_talk'] !== '' ? '
		<tr>
			<td class="rowhead">Google Talk</td>
			<td align="left"><a class="altlink" href="http://talkgadget.google.com/talkgadget/popout?member='.htmlspecialchars($user['google_talk']).'" title="click for google talk gadget"  target="_blank"><img src="pic/forums/google_talk.gif" alt="google_talk" /> Open</a></td>
		</tr>' : '').($user['msn'] !== '' ? '
		<tr>
			<td class="rowhead">MSN</td>
			<td align="left"><a class="altlink" href="http://members.msn.com/'.htmlspecialchars($user['msn']).'" target="_blank" title="click to see msn details"><img src="pic/forums/msn.gif" alt="msn" /> Open</a></td>
		</tr>' : '').($user['yahoo'] !== '' ? '
		<tr>
			<td class="rowhead">Yahoo</td>
			<td align="left"><a class="altlink" href="http://webmessenger.yahoo.com/?im='.htmlspecialchars($user['yahoo']).'" target="_blank" title="click to open yahoo"><img src="pic/forums/yahoo.gif" alt="yahoo" /> Open</a></td>
		</tr>' : '').($user['aim'] !== '' ? '
		<tr>
			<td class="rowhead">AIM</td>
			<td align="left"><a class="altlink" href="http://aim.search.aol.com/aol/search?s_it=searchbox.webhome&amp;q='.htmlspecialchars($user['aim']).'" target="_blank" title="click to search on aim... you will need to have an AIM account!"><img src="pic/forums/aim.gif" alt="AIM" /> Open</a></td>
		</tr>' : '').($user['icq'] !== '' ? '
		<tr>
			<td class="rowhead">ICQ</td>
			<td align="left"><a class="altlink" href="http://people.icq.com/people/&amp;uin='.htmlspecialchars($user['icq']).'" title="click to open icq page" target="_blank"><img src="pic/forums/icq.gif" alt="icq" /> Open</a></td>
		</tr>' : '').($user['website'] !== '' ? '
		<tr>
			<td class="rowhead">Website </td>
			<td align="left"><a class="altlink" href="'.htmlspecialchars($user['website']).'" target="_blank" title="click to go to website"><img src="pic/forums/www.gif" width="18" alt="website" /> '.htmlspecialchars($user['website']).'</a></td>
		</tr>' : '');	
    //== iphistory
    $iphistory = $mc1->get_value('ip_history_'.$id);
        if ($iphistory === false) {
            $ipto = sql_query("SELECT COUNT(id),enabled FROM `users` AS iplist WHERE `ip` = '" . $user["ip"] . "' group by enabled") or sqlerr(__FILE__, __LINE__);
            $row12 = mysql_fetch_row($ipto);
            $row13 = mysql_fetch_row($ipto);
            $ipuse[$row12[1]] = $row12[0];
            $ipuse[$row13[1]] = $row13[0];
            if (($ipuse['yes'] == 1 && $ipuse['no']==0) || ($ipuse['no']==1 && $ipuse['yes']==0))
                $use = "";
            else {
                $ipcheck=$user["ip"];
                $enbl  = $ipuse['yes'] ? $ipuse['yes'].' enabled ':'';
                $dbl = $ipuse['no'] ? $ipuse['no'].' disabled ':'';
                $mid = $enbl && $dbl ?'and' :'';
                $iphistory['use'] =  "<b>(<font color='red'>Warning :</font> <a href='staffpanel.php?tool=usersearch&amp;action=usersearch&amp;ip=$ipcheck'>Used by $enbl $mid $dbl users!</a>)</b>";
            }
            $resip = sql_query("SELECT ip FROM ips WHERE userid = ".sqlesc($id)." GROUP BY ip") or sqlerr(__FILE__, __LINE__);
            $iphistory['ips'] = mysql_num_rows($resip);
            $mc1->cache_value('ip_history_'.$id, $iphistory, $INSTALLER09['expires']['iphistory']);
        }
        if (isset($addr))
        if ($CURUSER['id'] == $id || $CURUSER['class'] >= UC_STAFF)
        $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_address']}</td><td align='left'>{$addr}{$iphistory['use']}&nbsp;(<a class='altlink' href='staffpanel.php?tool=iphistory&amp;action=iphistory&amp;id=$user[id]'><b>History</b></a>)&nbsp;(<a class='altlink' href='staffpanel.php?tool=iphistory&amp;action=iplist&amp;id=$user[id]'><b>List</b></a>)</td></tr>\n";

        if ($CURUSER["class"] >= UC_STAFF && $iphistory['ips'] > 0)
        $HTMLOUT .="<tr><td class='rowhead'>IP History</td><td align='left'>This user has earlier used <b><a href='{$INSTALLER09['baseurl']}/staffpanel.php?tool=iphistory&amp;action=iphistory&amp;id=" .$user['id'] ."'>{$iphistory['ips']} different IP addresses</a></b></td></tr>\n";
    
    //==Uploaded/downloaded
    if  ($user['paranoia'] < 2 || $CURUSER['id'] == $id || $CURUSER['class'] >= UC_STAFF){
    $days = round((time() - $user['added'])/86400);
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_downloaded']}</td><td align='left'>".mksize($user['downloaded'])." {$lang['userdetails_daily']}".($days > 1 ? mksize($user['downloaded']/$days) : mksize($user['downloaded']))."</td></tr>
    <tr><td class='rowhead'>{$lang['userdetails_uploaded']}</td><td align='left'>".mksize($user['uploaded'])." {$lang['userdetails_daily']}".($days > 1 ? mksize($user['uploaded']/$days) : mksize($user['uploaded']))."</td></tr>\n";
    }
    
    //=== paranoia settings
    if ($user['paranoia'] < 2 || $CURUSER['id'] == $id || $CURUSER['class'] >= UC_STAFF) 
    {
    if ($user['downloaded'] > 0)
    {
    $HTMLOUT .= '<tr>
			<td class="rowhead" style="vertical-align: middle">Share ratio</td>
			<td align="left" valign="middle" style="padding-top: 1px; padding-bottom: 0px">
	<table border="0"cellspacing="0" cellpadding="0">
		<tr>
			<td class="embedded">'.member_ratio($user['uploaded'], $user['downloaded']).'</td>
			<td class="embedded">&nbsp;&nbsp;'.get_user_ratio_image($user['uploaded'] / $user['downloaded']).'</td>
		</tr>
	</table>
			</td>
		</tr>';
    }
    }
    //=== testing concept of "share ratio"
    $cache_share_ratio = $mc1->get_value('share_ratio_'.$id);
    if ($cache_share_ratio === false) {
    $cache_share_ratio = mysql_fetch_assoc(sql_query("SELECT SUM(seedtime) AS seed_time_total, COUNT(id) AS total_number FROM snatched WHERE seedtime > '0' AND userid =".$user['id'].""))/*or sqlerr(__FILE__, __LINE__)*/;
    $cache_share_ratio['total_number'] = (int) $cache_share_ratio['total_number'];
    $cache_share_ratio['seed_time_total'] = (int) $cache_share_ratio['seed_time_total'];
    $mc1->cache_value('share_ratio_'.$id, $cache_share_ratio, $INSTALLER09['expires']['share_ratio']);
    }
    //=== get times per class
    switch (true)
    {
    //===  member
    case ($user['class'] == UC_USER):
    $days = 2;
    break;
    //=== Member +
    case ($user['class'] == UC_POWER_USER):
    $days = 1.5;
    break;
    //=== Member ++
    case ($user['class'] == UC_VIP || $user['class'] == UC_UPLOADER || $user['class'] == UC_MODERATOR || $user['class'] == UC_ADMINISTRATOR || $user['class'] == UC_SYSOP):
    $days = 0.5;
    break;
    }
    $avg_time_ratio = (($cache_share_ratio['seed_time_total'] / $cache_share_ratio['total_number']) / 86400 / $days);
    $avg_time_seeding = mkprettytime($cache_share_ratio['seed_time_total'] / $cache_share_ratio['total_number']);
    if ($user["id"] == $CURUSER["id"] || $CURUSER['class'] >= UC_STAFF) {
    $HTMLOUT .='<tr><td class="clearalt5" align="right"><b>Time Ratio:</b></td><td align="left" class="clearalt5">'.(($user['downloaded'] > 0 || $user['uploaded'] > 2147483648) ? '<font color="'.get_ratio_color(number_format($avg_time_ratio, 3)).'">'.number_format($avg_time_ratio, 3).'</font>     '.ratio_image_machine(number_format($avg_time_ratio, 3)).'     [<font color="'.get_ratio_color(number_format($avg_time_ratio, 3)).'"> '.$avg_time_seeding.'</font> per torrent average ]  Ratio based on the assumption that all torrents were New. ' : 'inf.').'</td></tr>';
    }
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_bonus_points']}</td><td align='left'><a class='altlink' href='{$INSTALLER09['baseurl']}/mybonus.php'>".(int)$user['seedbonus']."</a></td></tr>";
    if  ($user['onirc'] == 'yes'){
    $ircbonus   = (!empty($user['irctotal'])?number_format($user["irctotal"] / $INSTALLER09['autoclean_interval'], 1):'0.0');
    $HTMLOUT .="<tr><td class='rowhead' valign='top' align='right'>Irc Bonus</td><td align='left'>{$ircbonus}</td></tr>";
    $irctotal = (!empty($user['irctotal'])?calctime($user['irctotal']):$user['username'].' has never been on IRC!');
    $HTMLOUT .="<tr><td class='rowhead' valign='top' align='right'>Irc Idle Time</td><td align='left'>{$irctotal}</td></tr>";
    }
    //==Connectable and port shit
    if  ($user['paranoia'] < 1 || $CURUSER['id'] == $id || $CURUSER['class'] >= UC_STAFF) 
    {
    $port_data = $mc1->get_value('port_data_'.$id);
    if ($port_data === false) {
    $q1 = sql_query('SELECT connectable, port,agent FROM peers WHERE userid = '.$id.' LIMIT 1') or sqlerr(__FILE__,__LINE__);
    $port_data = mysql_fetch_row($q1);
    $mc1->cache_value('port_data_'.$id, $port_data, $INSTALLER09['expires']['port_data']);
    }
    if($port_data > 0){
    $connect = $port_data[0];
    $port = $port_data[1];
    $agent = $port_data[2];
    if($connect == "yes"){
    $connectable = "<img src='{$INSTALLER09['pic_base_url']}tick.png' alt='Yes' title='Sorted Yer connectable' style='border:none;padding:2px;' /><font color='green'><b>{$lang['userdetails_yes']}</b></font>";
    }else{
    $connectable = "<img src='{$INSTALLER09['pic_base_url']}cross.png' alt='No' title='Contact Site Staff' style='border:none;padding:2px;' /><font color='red'><b>{$lang['userdetails_no']}</b></font>";
    }
    }else{
    $connectable = "<img src='{$INSTALLER09['pic_base_url']}smilies/unsure.gif' alt='Unknown' title='Not connected To Peers' style='border:none;padding:2px;' /><font color='blue'><b>{$lang['userdetails_unknown']}</b></font>";
    }
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_connectable']}</td><td align='left'>".$connectable."</td></tr>";
    if (!empty($port))
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_port']}</td><td class='tablea' align='left'>".htmlentities($port)."</td></tr>
    <tr><td class='rowhead'>{$lang['userdetails_client']}</td><td class='tablea' align='left'>".htmlentities($agent)."</td></tr>";
    }
    //==End
    if ($user["avatar"])
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_avatar']}</td><td align='left'><img src='" . htmlspecialchars($user["avatar"]) . "' width='{$user['av_w']}' height='{$user['av_h']}' alt='' /></td></tr>\n";
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_class']}</td><td align='left'>" . get_user_class_name($user["class"]) . "</td></tr>\n";
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_gender']}</td><td align='left'>" . htmlspecialchars($user["gender"]) . "</td></tr>\n";
    $HTMLOUT .= "<tr><td class='rowhead'>Freeleech Slots</td>
                 <td align='left'>".(int)$user['freeslots']."</td></tr>";
    $HTMLOUT .= "<tr><td class='rowhead'>Freeleech Status</td>
                 <td align='left'>".($user['free_switch'] != 0 ? 'FREE Status '.($user['free_switch'] > 1 ? 'Expires: '.get_date($user['free_switch'], 'DATE').' ('.mkprettytime($user['free_switch'] - time()).' to go) <br />':'Unlimited<br />'):'None')."</td></tr>";
    
    if  ($user['paranoia'] < 2 || $CURUSER['id'] == $id || $CURUSER['class'] >= UC_STAFF) 
    {
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_comments']}</td>";
    if ($torrentcomments && (($user["class"] >= UC_POWER_USER && $user["id"] == $CURUSER["id"]) || $CURUSER['class'] >= UC_STAFF))
    $HTMLOUT .= "<td align='left'><a href='userhistory.php?action=viewcomments&amp;id=$id'>$torrentcomments</a></td></tr>\n";
    else
    $HTMLOUT .= "<td align='left'>$torrentcomments</td></tr>\n";
    }
    
    if  ($user['paranoia'] < 2 || $CURUSER['id'] == $id || $CURUSER['class'] >= UC_STAFF) 
    {
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_posts']}</td>";
    if ($forumposts && (($user["class"] >= UC_POWER_USER && $user["id"] == $CURUSER["id"]) || $CURUSER['class'] >= UC_STAFF))
    $HTMLOUT .= "<td align='left'><a href='userhistory.php?action=viewposts&amp;id=$id'>$forumposts</a></td></tr>\n";
    else
    $HTMLOUT .= "<td align='left'>$forumposts</td></tr>\n";
    }
    //==Memcached invited_by
    if($user["invitedby"] ==	 '' || $user["id"] == $CURUSER["id"] || $CURUSER['class'] >= UC_STAFF)	{	 
    $invitedby = $mc1->get_value('invited_by_'.$id);
    if($invitedby === false ) {
    $invitee = sql_query('SELECT id,username FROM users WHERE invitedby = '.$user['id'] .' AND status="confirmed"');
    while($invitee2 = mysql_fetch_assoc($invitee)) 
    $invitedby[] = $invitee2;
    $mc1->cache_value('invited_by_'.$id, $invitedby, $INSTALLER09['expires']['invited_by']);
    }
    if (count($invitedby) > 0)
    {
    $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_invee']}</td><td align='left'>";
    if ($invitedby)
    {
    foreach($invitedby as $invitee) {
    $HTMLOUT .= "<a href='userdetails.php?id=".$invitee['id']."'>".$invitee['username']."</a>&nbsp;"; 
    }
    $HTMLOUT .="</td></tr>";
    } else {
    //== If there are no invited users
    if (empty($invitedby))
    $HTMLOUT .= "Currently no invited members.</td></tr>";
    }
    }
    }
    //==End
    if  ($user['paranoia'] < 2 ||  $user['hidecur'] == "yes" || $CURUSER['id'] == $id || $CURUSER['class'] >= UC_STAFF) 
    {
    if (isset($torrents))
    $HTMLOUT .= "<tr valign=\"top\"><td class=\"rowhead\" width=\"10%\">{$lang['userdetails_uploaded_t']}</td><td align=\"left\" width=\"90%\"><a href=\"javascript: klappe_news('a')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica\" alt=\"Show/Hide\" /></a><div id=\"ka\" style=\"display: none;\">$torrents</div></td></tr>\n";
    if (isset($seeding))
    $HTMLOUT .= "<tr valign=\"top\"><td class=\"rowhead\" width=\"10%\">{$lang['userdetails_cur_seed']}</td><td align=\"left\" width=\"90%\"><a href=\"javascript: klappe_news('a1')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica1\" alt=\"Show/Hide\" /></a><div id=\"ka1\" style=\"display: none;\">".maketable($seeding)."</div></td></tr>\n";
    if (isset($leeching))
    $HTMLOUT .= "<tr valign=\"top\"><td class=\"rowhead\" width=\"10%\">{$lang['userdetails_cur_leech']}</td><td align=\"left\" width=\"90%\"><a href=\"javascript: klappe_news('a2')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica2\" alt=\"Show/Hide\" /></a><div id=\"ka2\" style=\"display: none;\">".maketable($leeching)."</div></td></tr>\n";
    //==Snatched
    $user_snatches_data = $mc1->get_value('user_snatches_data_'.$id);
    if ($user_snatches_data === false) {
    $ressnatch = sql_query("SELECT s.*, t.name AS name, c.name AS catname, c.image AS catimg FROM snatched AS s INNER JOIN torrents AS t ON s.torrentid = t.id LEFT JOIN categories AS c ON t.category = c.id WHERE s.userid = $user[id]") or sqlerr(__FILE__, __LINE__);
    $user_snatches_data = snatchtable($ressnatch);
    $mc1->cache_value('user_snatches_data_'.$id, $user_snatches_data, $INSTALLER09['expires']['user_snatches_data']);
    }
    if (isset($user_snatches_data))
    $HTMLOUT .= "<tr valign=\"top\"><td class=\"rowhead\" width=\"10%\">{$lang['userdetails_cur_snatched']}</td><td align=\"left\" width=\"90%\"><a href=\"javascript: klappe_news('a3')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pica3\" alt=\"Show/Hide\" /></a><div id=\"ka3\" style=\"display: none;\">$user_snatches_data</div></td></tr>\n";
    //==End
    }

    //=== start snatched
    $count_snatched = $count2 ='';
    if ($CURUSER['class'] >= UC_STAFF){
    if (isset($_GET["snatched_table"])){
    $HTMLOUT .="<tr><td class='one' align='right' valign='top'><b>Snatched:</b><br />[ <a href=\"userdetails.php?id=$id\" class=\"sublink\">Hide list</a> ]</td><td class='one'>";
    $res = sql_query(
    "SELECT sn.start_date AS s, sn.complete_date AS c, sn.last_action AS l_a, sn.seedtime AS s_t, sn.seedtime, sn.leechtime AS l_t, sn.leechtime, sn.downspeed, sn.upspeed, sn.uploaded, sn.downloaded, sn.torrentid, sn.start_date, sn.complete_date, sn.seeder, sn.last_action, sn.connectable, sn.agent, sn.seedtime, sn.port, cat.name, cat.image, t.size, t.seeders, t.leechers, t.owner, t.name AS torrent_name ".
    "FROM snatched AS sn ".
    "LEFT JOIN torrents AS t ON t.id = sn.torrentid ".
    "LEFT JOIN categories AS cat ON cat.id = t.category ".
    "WHERE sn.userid=$id ORDER BY sn.start_date DESC") or sqlerr(__FILE__,__LINE__);
    $HTMLOUT .= "<table border='1' cellspacing='0' cellpadding='5' align='center'><tr><td class='colhead' align='center'>Category</td><td class='colhead' align='left'>Torrent</td>".
    "<td class='colhead' align='center'>S / L</td><td class='colhead' align='center'>Up / Down</td><td class='colhead' align='center'>Torrent Size</td>".
    "<td class='colhead' align='center'>Ratio</td><td class='colhead' align='center'>Client</td></tr>";
    while ($arr = mysql_fetch_assoc($res)){
    //=======change colors
    $count2= (++$count2)%2;
    $class = ($count2==0?'one':'two');
    //=== speed color red fast green slow ;)
    if ($arr["upspeed"] > 0)
    $ul_speed = ($arr["upspeed"] > 0 ? mksize($arr["upspeed"]) : ($arr["seedtime"] > 0 ? mksize($arr["uploaded"] / ($arr["seedtime"] + $arr["leechtime"])) : mksize(0)));
    else
    $ul_speed = mksize(($arr["uploaded"] / ( $arr['l_a'] - $arr['s'] + 1 )));
    if ($arr["downspeed"] > 0)
    $dl_speed = ($arr["downspeed"] > 0 ? mksize($arr["downspeed"]) : ($arr["leechtime"] > 0 ? mksize($arr["downloaded"] / $arr["leechtime"]) : mksize(0)));
    else
    $dl_speed = mksize(($arr["downloaded"] / ( $arr['c'] - $arr['s'] + 1 )));
    
    $dlc="";
    switch (true){
    case ($dl_speed > 600):
    $dlc = 'red';
    break;
    case ($dl_speed > 300 ):
    $dlc = 'orange';
    break;
    case ($dl_speed > 200 ):
    $dlc = 'yellow';
    break;
    case ($dl_speed < 100 ):
    $dlc = 'Chartreuse';
    break;
    }

    if ($arr["downloaded"] > 0){
    $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
    $ratio = "<font color='" . get_ratio_color($ratio) . "'><b>Ratio:</b><br />$ratio</font>";
    }
    else
    if ($arr["uploaded"] > 0)
    $ratio = "Inf.";
    else
    $ratio = "N/A"; 
 
    $HTMLOUT .= "<tr><td class='$class' align='center'>".($arr['owner'] == $id ? "<b><font color='orange'>Torrent owner</font></b><br />" : "".($arr['complete_date'] != '0'  ? "<b><font color='lightgreen'>Finished</font></b><br />" : "<b><font color='red'>Not Finished</font></b><br />")."")."<img src='{$INSTALLER09['pic_base_url']}caticons/$arr[image]' alt='$arr[name]' title='$arr[name]' /></td>"."
    <td class='$class'><a class='altlink' href='{$INSTALLER09['baseurl']}/details.php?id=$arr[torrentid]'><b>$arr[torrent_name]</b></a>".($arr['complete_date'] != '0'  ?"<br />"."
    <font color='yellow'>started: ".get_date($arr['start_date'], 0,1) ."</font><br />
    " : " "."<font color='yellow'>started:".get_date($arr['start_date'], 0,1) ."</font><br /><font color='orange'>Last Action:".get_date($arr['last_action'], 0,1) ."</font>"." 
    ".get_date($arr['complete_date'], 0,1) ." ".($arr['complete_date'] == '0'  ? "".($arr['owner'] == $id ? "" : "[ ".mksize($arr["size"] - $arr["downloaded"])." still to go ]")."" : "")."")." ".get_date($arr['complete_date'], 0,1) ." ".($arr['complete_date'] != '0'  ? "<br />"."
    <font color='silver'>Time to download: ".($arr['leechtime'] != '0' ? mkprettytime($arr['leechtime']) : mkprettytime($arr['c'] - $arr['s'])."")."</font> <font color='$dlc'>[ DLed at: $dl_speed ]</font>"."
    <br />" : "<br />")."<font color='lightblue'>".($arr['seedtime'] != '0' ? "Total seeding time: ".mkprettytime($arr['seedtime'])." </font><font color='$dlc'> " : "Total seeding time: N/A").""."
    </font><font color='lightgreen'> [ up speed: ".$ul_speed." ] </font>".get_date($arr['complete_date'], 0,1) ."".($arr['complete_date'] == '0'  ? "<br /><font color='$dlc'>Download speed: $dl_speed</font>" : "")."</td>"."
    <td align='center' class='$class'>Seeds: ".$arr['seeders']."<br />Leech: ".$arr['leechers']."</td><td align='center' class='$class'><font color='lightgreen'>Uploaded:<br />"."
    <b>".$uploaded = mksize($arr["uploaded"])."</b></font><br /><font color='orange'>Downloaded:<br /><b>".$downloaded = mksize($arr["downloaded"])."</b></font></td>"."
    <td align='center' class='$class'>".mksize($arr["size"])."<br />Difference of:<br /><font color='orange'><b>".mksize($arr['size'] - $arr["downloaded"])."</b></font></td>"."
    <td align='center' class='$class'>".$ratio."<br />".($arr['seeder'] == 'yes' ? "<font color='lightgreen'><b>seeding</b></font>" : "<font color='red'><b>Not seeding</b></font>").""."
    </td><td align='center' class='$class'>".$arr["agent"]."<br />port: ".$arr["port"]."<br />".($arr["connectable"] == 'yes' ? "<b>Connectable:</b> <font color='lightgreen'>Yes</font>"."
    " : "<b>Connectable:</b> <font color='red'><b>no</b></font>")."</td></tr>\n";
    }
    $HTMLOUT .= "</table></td></tr>\n";
    }
    else
    $HTMLOUT .= tr("<b>Snatched:</b><br />","[ <a href=\"userdetails.php?id=$id&amp;snatched_table=1\" class=\"sublink\">Show</a> ]  - $count_snatched <font color='red'><b>staff only!!!</b></font>", 1);
    }
    //=== end snatched
  
    //==09 Hnr mod - sir_snugglebunny
    if  ($user['paranoia'] < 2 || $CURUSER['id'] == $id || $CURUSER['class'] >= UC_STAFF) 
    {
    $completed = "";
    $r = sql_query("SELECT torrents.name,torrents.added AS torrent_added, snatched.start_date AS s, snatched.complete_date AS c, snatched.downspeed, snatched.seedtime, snatched.seeder, snatched.torrentid as tid, snatched.id, categories.id as category, categories.image, categories.name as catname, snatched.uploaded, snatched.downloaded, snatched.hit_and_run, snatched.mark_of_cain, snatched.complete_date, snatched.last_action, torrents.seeders, torrents.leechers, torrents.owner, snatched.start_date AS st, snatched.start_date FROM snatched JOIN torrents ON torrents.id = snatched.torrentid JOIN categories ON categories.id = torrents.category WHERE snatched.finished='yes' AND userid=$id AND torrents.owner != $id ORDER BY snatched.id DESC") or sqlerr(__FILE__, __LINE__);
    //=== completed
    if (mysql_num_rows($r) > 0){ 
    $completed .= "<table class='main' border='1' cellspacing='0' cellpadding='3'>
    <tr>
    <td class='colhead'>{$lang['userdetails_type']}</td>
    <td class='colhead'>{$lang['userdetails_name']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_s']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_l']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_ul']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_dl']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_ratio']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_wcompleted']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_laction']}</td>
    <td class='colhead' align='center'>{$lang['userdetails_speed']}</td></tr>";
   $count2='';
    while ($a = mysql_fetch_assoc($r)){
    //=======change colors
    $count2= (++$count2)%2;
    $class = ($count2 == 0 ? 'one' : 'two');
    $torrent_needed_seed_time = ($a['st'] - $a['torrent_added']);
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

    //=== times per torrent based on age
    switch(true) 
    {
    case (($a['st'] - $a['torrent_added']) < 7*86400):
    $minus_ratio = ($days_3 - $a['seedtime']) - ($a['uploaded'] / $a['downloaded'] * 3 * 86400);
    break;
    case (($a['st'] - $a['torrent_added']) < 21*86400):
    $minus_ratio = ($days_14 - $a['seedtime']) - ($a['uploaded'] / $a['downloaded'] * 2 * 86400);
    break;
    case (($a['st'] - $a['torrent_added']) >= 21*86400):
    $minus_ratio = ($days_over_14 - $a['seedtime']) - ($a['uploaded'] / $a['downloaded'] * 86400);
    break;
    }
    $color = (($minus_ratio > 0 && $a['uploaded'] < $a['downloaded']) ? get_ratio_color($minus_ratio) : 'limegreen');
    $minus_ratio = mkprettytime($minus_ratio); 
    //=== speed color red fast green slow ;)
    if ($a["downspeed"] > 0)
    $dl_speed = ($a["downspeed"] > 0 ? mksize($a["downspeed"]) : ($a["leechtime"] > 0 ? mksize($a["downloaded"] / $a["leechtime"]) : mksize(0)));
    else
    $dl_speed = mksize(($a["downloaded"] / ( $a['c'] - $a['s'] + 1 )));
    $dlc="";
    switch (true){
    case ($dl_speed > 600):
    $dlc = 'red';
    break;
    case ($dl_speed > 300 ):
    $dlc = 'orange';
    break;
    case ($dl_speed > 200 ):
    $dlc = 'yellow';
    break;
    case ($dl_speed < 100 ):
    $dlc = 'Chartreuse';
    break;
    }
    
    //=== mark of cain / hit and run
    $checkbox_for_delete = ($CURUSER['class'] >=  UC_STAFF ? " [<a href='".$INSTALLER09['baseurl']."/userdetails.php?id=".$id."&amp;delete_hit_and_run=".$a['id']."'>Remove</a>]" : '');
    $mark_of_cain = ($a['mark_of_cain'] == 'yes' ? "<img src='{$INSTALLER09['pic_base_url']}moc.gif' alt='Mark Of Cain' title='The mark of Cain!' />".$checkbox_for_delete : '');
    $hit_n_run = ($a['hit_and_run'] > 0 ? "<img src='{$INSTALLER09['pic_base_url']}hnr.gif' alt='Hit and run' title='Hit and run!' />" : '');
    $completed .= "<tr><td style='padding: 0px' class='$class'><img src='{$INSTALLER09['pic_base_url']}caticons/$a[image]' alt='$a[name]' title='$a[name]' /></td>
    <td class='$class'><a class='altlink' href='{$INSTALLER09['baseurl']}/details.php?id=".$a['tid']."&amp;hit=1'><b>".htmlspecialchars($a['name'])."</b></a>
    <br /><font color='.$color.'>  ".(($CURUSER['class'] >= UC_STAFF || $user['id'] == $CURUSER['id']) ? "seeded for</font>: ".mkprettytime($a['seedtime']).(($minus_ratio != '0:00' && $a['uploaded'] < $a['downloaded']) ? "<br />should still seed for: ".$minus_ratio."&nbsp;&nbsp;" : '').
    ($a['seeder'] == 'yes' ? "&nbsp;<font color='limegreen'> [<b>seeding</b>]</font>" : $hit_n_run."&nbsp;".$mark_of_cain) : '')."</td>
    <td align='center' class='$class'>".$a['seeders']."</td>
    <td align='center' class='$class'>".$a['leechers']."</td>
    <td align='center' class='$class'>".mksize($a['uploaded'])."</td>
    <td align='center' class='$class'>".mksize($a['downloaded'])."</td>
    <td align='center' class='$class'>".($a['downloaded'] > 0 ? "<font color='" . get_ratio_color(number_format($a['uploaded'] / $a['downloaded'], 3)) . "'>".number_format($a['uploaded'] / $a['downloaded'], 3)."</font>" : ($a['uploaded'] > 0 ? 'Inf.' : '---'))."<br /></td>
    <td align='center' class='$class'>".get_date($a['complete_date'], 'DATE')."</td>
    <td align='center' class='$class'>".get_date($a['last_action'], 'DATE')."</td>
    <td align='center' class='$class'><font color='$dlc'>[ DLed at: $dl_speed ]</font></td></tr>";
    }
    $completed .= "</table>\n";
    }
    if ($completed && $CURUSER['class'] >= UC_POWER_USER || $completed && $user['id'] == $CURUSER['id']){ 
    if (!isset($_GET['completed']))
    $HTMLOUT .= tr('<b>'.$lang['userdetails_completedt'].'</b><br />','[ <a href=\'./userdetails.php?id='.$id.'&amp;completed=1#completed\' class=\'sublink\'>Show</a> ]&nbsp;&nbsp;-&nbsp;'.mysql_num_rows($r), 1);
    elseif (mysql_num_rows($r) == 0)
    $HTMLOUT .= tr('<b>'.$lang['userdetails_completedt'].'</b><br />','[ <a href=\'./userdetails.php?id='.$id.'&amp;completed=1\' class=\'sublink\'>Show</a> ]&nbsp;&nbsp;-&nbsp;'.mysql_num_rows($r), 1);
    else
    $HTMLOUT .= tr('<a name=\'completed\'><b>'.$lang['userdetails_completedt'].'</b></a><br />[ <a href=\'./userdetails.php?id='.$id.'#history\' class=\'sublink\'>Hide list</a> ]', $completed, 1);
    } 
    }
    //==End hnr
    if ($user["info"])
     $HTMLOUT .= "<tr valign='top'><td align='left' colspan='2' class='text' bgcolor='#F4F4F0'>" . format_comment($user["info"]) . "</td></tr>\n";

    if ($CURUSER["id"] != $user["id"])
      if ($CURUSER['class'] >= UC_STAFF)
        $showpmbutton = 1;
      elseif ($user["acceptpms"] == "yes")
      {
        $r = sql_query("SELECT id FROM blocks WHERE userid={$user['id']} AND blockid={$CURUSER['id']}") or sqlerr(__FILE__,__LINE__);
        $showpmbutton = (mysql_num_rows($r) == 1 ? 0 : 1);
      }
      elseif ($user["acceptpms"] == "friends")
      {
        $r = sql_query("SELECT id FROM friends WHERE userid=$user[id] AND friendid=$CURUSER[id]") or sqlerr(__FILE__,__LINE__);
        $showpmbutton = (mysql_num_rows($r) == 1 ? 1 : 0);
      }
    if (isset($showpmbutton))
      $HTMLOUT .= "<tr>
      <td colspan='2' align='center'>
      <form method='get' action='sendmessage.php'>
        <input type='hidden' name='receiver' value='{$user["id"]}' />
        <input type='submit' value='{$lang['userdetails_msg_btn']}' class='btn' />
      </form>
      </td></tr>";
    //==Report User
    $HTMLOUT .= tr("Report User","<form method='post' action='report.php?type=User&amp;id={$id}'><input type='submit' value='Report User' class='button' /> Click to Report this user for Breaking the rules.</form>", 1);
    //==End
    if  ($user['paranoia'] < 1 || $CURUSER['id'] == $id || $CURUSER['class'] >= UC_STAFF) 
    {
    if(isset($user['last_status']))
    $HTMLOUT .="<tr valign='top'><td class='rowhead'>Status</td><td align='left'>".format_urls($user['last_status'])."<br/><small>added ".get_date($user['last_update'],'',0,1)."</small></td></tr>\n";
    }
    $HTMLOUT .= "</table>\n";
    
    $HTMLOUT .="<script type='text/javascript'>
    /*<![CDATA[*/
    function togglepic(bu, picid, formid){
	  var pic = document.getElementById(picid);
	  var form = document.getElementById(formid);
	
	  if(pic.src == bu + '/pic/plus.gif')	{
		pic.src = bu + '/pic/minus.gif';
		form.value = 'minus';
	  }else{
		pic.src = bu + '/pic/plus.gif';
		form.value = 'plus';
	  }
    }
    /*]]>*/
    </script>";

    if ($CURUSER['class'] >= UC_STAFF && $user["class"] < $CURUSER['class'])
    {
      $HTMLOUT .= begin_frame("Edit User", true);
      $HTMLOUT .= "<form method='post' action='modtask.php'>\n";
      require_once CLASS_DIR.'validator.php';
      $HTMLOUT .= validatorForm("ModTask_$user[id]");
      $HTMLOUT .= "<input type='hidden' name='action' value='edituser' />\n";
      $HTMLOUT .= "<input type='hidden' name='userid' value='$id' />\n";
      $HTMLOUT .= "<input type='hidden' name='returnto' value='userdetails.php?id=$id' />\n";
      $HTMLOUT .= "
      <table class='main' border='1' cellspacing='0' cellpadding='5'>\n";
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_title']}</td><td colspan='2' align='left'><input type='text' size='60' name='title' value='" . htmlspecialchars($user['title']) . "' /></td></tr>\n";
      $avatar = htmlspecialchars($user["avatar"]);
      $HTMLOUT .="<tr><td class='rowhead'>{$lang['userdetails_avatar_url']}</td><td colspan='2' align='left'><input type='text' size='60' name='avatar' value='$avatar' /></td></tr>\n";
      $HTMLOUT .="<tr>
		  <td class='rowhead'>Signature Rights</td>
		  <td colspan='2' align='left'><input name='signature_post' value='yes' type='radio'".($user['signature_post'] == "yes" ? " checked='checked'" : "")." />Yes 
		  <input name='signature_post' value='no' type='radio'".($user['signature_post'] == "no" ? " checked='checked'" : "")." />No Disable this members signature rights.</td>
	    </tr>
	    <tr>
		  <td class='rowhead'>View Signatures</td>
		  <td colspan='2' align='left'><input name='signatures' value='yes' type='radio'".($user['signatures'] == "yes" ? " checked='checked'" : "")." />Yes 
		  <input name='signatures' value='no' type='radio'".($user['signatures'] == "no" ? " checked='checked'" : "")." /></td>
	    </tr>
	    <tr>
		  <td class='rowhead'>Signature</td>
		  <td colspan='2' align='left'><textarea cols='60' rows='2' name='signature'>".htmlspecialchars($user['signature'])."</textarea></td>
	    </tr>
	    <tr>
		  <td class='rowhead'>Google Talk</td>
		  <td colspan='2' align='left'><input type='text' size='60' name='google_talk' value='".htmlspecialchars($user['google_talk'])."' /></td>
	    </tr>
	    <tr>
		  <td class='rowhead'>MSN</td>
		  <td colspan='2' align='left'><input type='text' size='60' name='msn' value='".htmlspecialchars($user['msn'])."' /></td>
	    </tr>
	    <tr>
		  <td class='rowhead'>AIM</td>
		  <td colspan='2' align='left'><input type='text' size='60' name='aim' value='".htmlspecialchars($user['aim'])."' /></td>
	    </tr>
	    <tr>
		  <td class='rowhead'>Yahoo</td>
		  <td colspan='2' align='left'><input type='text' size='60' name='yahoo' value='".htmlspecialchars($user['yahoo'])."' /></td>
	    </tr>
	    <tr>
		  <td class='rowhead'>ICQ</td>
	 	  <td colspan='2' align='left'><input type='text' size='60' name='icq' value='".htmlspecialchars($user['icq'])."' /></td>
	    </tr>
	    <tr>
		  <td class='rowhead'>Website</td>
		  <td colspan='2' align='left'><input type='text' size='60' name='website' value='".htmlspecialchars($user['website'])."' /></td>
	    </tr>";
      //== we do not want mods to be able to change user classes or amount donated...
      // === Donor mod time based by snuggles
     if ($CURUSER["class"] == UC_SYSOP) {
     $donor = $user["donor"] == "yes";
     $HTMLOUT .="<tr><td class='rowhead' align='right'><b>{$lang['userdetails_donor']}</b></td><td colspan='2' align='center'>";
     if ($donor) {
     $donoruntil = $user['donoruntil'];
     if ($donoruntil == '0')
     $HTMLOUT .="Arbitrary duration";
     else {
     $HTMLOUT .="<b>".$lang['userdetails_donor2']."</b> ".get_date($user['donoruntil'], 'DATE'). " ";
     $HTMLOUT .=" [ " . mkprettytime($donoruntil - TIME_NOW) . " ] To go\n";
     }
     } else {
     $HTMLOUT .="{$lang['userdetails_dfor']}<select name='donorlength'><option value='0'>------</option><option value='4'>1 month</option>" .
     "<option value='6'>6 weeks</option><option value='8'>2 months</option><option value='10'>10 weeks</option>" .
     "<option value='12'>3 months</option><option value='255'>Unlimited</option></select>\n";
     }
     $HTMLOUT .="<br /><b>{$lang['userdetails_cdonation']}</b><input type='text' size='6' name='donated' value=\"" .htmlspecialchars($user["donated"]) . "\" />" . "<b>{$lang['userdetails_tdonations']}</b>" . htmlspecialchars($user["total_donated"]) . "";
     if ($donor) {
     $HTMLOUT .="<br /><b>{$lang['userdetails_adonor']}</b> <select name='donorlengthadd'><option value='0'>------</option><option value='4'>1 month</option>" .
     "<option value='6'>6 weeks</option><option value='8'>2 months</option><option value='10'>10 weeks</option>" .
     "<option value='12'>3 months</option><option value='255'>Unlimited</option></select>\n";
     $HTMLOUT .="<br /><b>{$lang['userdetails_rdonor']}</b><input name='donor' value='no' type='checkbox' /> [ If they were bad ]";
     }
     $HTMLOUT .="</td></tr>\n";
     }
     // ====End
     
      if ($CURUSER['class'] == UC_STAFF && $user["class"] > UC_VIP)
        $HTMLOUT .= "<input type='hidden' name='class' value='{$user['class']}' />\n";
      else
      {
        $HTMLOUT .= "<tr><td class='rowhead'>Class</td><td colspan='2' align='left'><select name='class'>\n";
        if ($CURUSER['class'] == UC_STAFF)
          $maxclass = UC_VIP;
        else
          $maxclass = $CURUSER['class'] - 1;
        for ($i = 0; $i <= $maxclass; ++$i)
          $HTMLOUT .= "<option value='$i'" . ($user["class"] == $i ? " selected='selected'" : "") . ">" . get_user_class_name($i) . "</option>\n";
        $HTMLOUT .= "</select></td></tr>\n";
      }
      $supportfor = htmlspecialchars($user["supportfor"]);
      $HTMLOUT .="<tr><td class='rowhead'>{$lang['userdetails_support']}</td><td colspan='2' align='left'><input type='radio' name='support' value='yes'" .($user["support"] == "yes" ? " checked='checked'" : "")." />{$lang['userdetails_yes']}<input type='radio' name='support' value='no'" .($user["support"] == "no" ? " checked='checked'" : "")." />{$lang['userdetails_no']}</td></tr>\n";
      $HTMLOUT .="<tr><td class='rowhead'>{$lang['userdetails_supportfor']}</td><td colspan='2' align='left'><textarea cols='60' rows='2' name='supportfor'>{$supportfor}</textarea></td></tr>\n";
      $modcomment = htmlspecialchars($user["modcomment"]);
      if ($CURUSER["class"] < UC_SYSOP) {
      $HTMLOUT .="<tr><td class='rowhead'>{$lang['userdetails_comment']}</td><td colspan='2' align='left'><textarea cols='60' rows='6' name='modcomment' readonly='readonly'>$modcomment</textarea></td></tr>\n";
      }
      else {
      $HTMLOUT .="<tr><td class='rowhead'>{$lang['userdetails_comment']}</td><td colspan='2' align='left'><textarea cols='60' rows='6' name='modcomment'>$modcomment</textarea></td></tr>\n";
      }
      $HTMLOUT .="<tr><td class='rowhead'>{$lang['userdetails_add_comment']}</td><td colspan='2' align='left'><textarea cols='60' rows='2' name='addcomment'></textarea></td></tr>\n";
      //=== bonus comment 
      $bonuscomment = htmlspecialchars($user["bonuscomment"]);
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_bonus_comment']}</td><td colspan='2' align='left'><textarea cols='60' rows='6' name='bonuscomment' readonly='readonly' style='background:purple;color:yellow;'>$bonuscomment</textarea></td></tr>\n";
      //==end
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_enabled']}</td><td colspan='2' align='left'><input name='enabled' value='yes' type='radio'" . ($enabled ? " checked='checked'" : "") . " />{$lang['userdetails_yes']} <input name='enabled' value='no' type='radio'" . (!$enabled ? " checked='checked'" : "") . " />{$lang['userdetails_no']}</td></tr>\n";
      if ($CURUSER['class'] >= UC_STAFF)
      $HTMLOUT .= "<tr><td class='rowhead'>Freeleech Slots:</td><td colspan='2' align='left'>
      <input type='text' size='6' name='freeslots' value='".(int)$user['freeslots']."' /></td></tr>";
      if ($CURUSER['class'] >= UC_ADMINISTRATOR) {
	    $free_switch = $user['free_switch'] != 0;
      $HTMLOUT .= "<tr><td class='rowhead'".(!$free_switch ? ' rowspan="2"' : '').">Freeleech Status</td>
 	    <td align='left' width='20%'>".($free_switch ?
      "<input name='free_switch' value='42' type='radio' />Remove Freeleech Status" :
      "No Freeleech Status Set")."</td>\n";
      if ($free_switch)
      {
      if ($user['free_switch'] == 1)
      $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
      else
      $HTMLOUT .= "<td align='center'>Until ".get_date($user['free_switch'], 'DATE'). " (". mkprettytime($user['free_switch'] - time()). " to go)</td></tr>";
      } else
      {
      $HTMLOUT .= '<td>Freeleech for <select name="free_switch">
      <option value="0">------</option>
      <option value="1">1 week</option>
      <option value="2">2 weeks</option>
      <option value="4">4 weeks</option>
      <option value="8">8 weeks</option>
      <option value="255">Unlimited</option>
      </select></td></tr>
      <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="free_pm" /></td></tr>';
      }
      }
     //==Download disable
     if ($CURUSER['class'] >= UC_STAFF) {
	   $downloadpos = $user['downloadpos'] != 1;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$downloadpos ? ' rowspan="2"' : '').">{$lang['userdetails_dpos']}</td>
 	   <td align='left' width='20%'>".($downloadpos ? "<input name='downloadpos' value='42' type='radio' />Remove download disablement" : "No disablement Status Set")."</td>\n";

     if ($downloadpos)
     {
     if ($user['downloadpos'] == 0)
     $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
     else
     $HTMLOUT .= "<td align='center'>Until ".get_date($user['downloadpos'], 'DATE'). " (".mkprettytime($user['downloadpos'] - time()). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>Disable for <select name="downloadpos">
     <option value="0">------</option>
     <option value="1">1 week</option>
     <option value="2">2 weeks</option>
     <option value="4">4 weeks</option>
     <option value="8">8 weeks</option>
     <option value="255">Unlimited</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="disable_pm" /></td></tr>';
     }
     }
     //==Upload disable
     if ($CURUSER['class'] >= UC_STAFF) {
	   $uploadpos = $user['uploadpos'] != 1;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$uploadpos ? ' rowspan="2"' : '').">{$lang['userdetails_upos']}</td>
 	   <td align='left' width='20%'>".($uploadpos ? "<input name='uploadpos' value='42' type='radio' />Remove upload disablement" : "No disablement Status Set")."</td>\n";

     if ($uploadpos)
     {
     if ($user['uploadpos'] == 0)
     $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
     else
     $HTMLOUT .= "<td align='center'>Until ".get_date($user['uploadpos'], 'DATE'). " (".mkprettytime($user['uploadpos'] - time()). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>Disable for <select name="uploadpos">
     <option value="0">------</option>
     <option value="1">1 week</option>
     <option value="2">2 weeks</option>
     <option value="4">4 weeks</option>
     <option value="8">8 weeks</option>
     <option value="255">Unlimited</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="updisable_pm" /></td></tr>';
     }
     }
     //==Pm disable
     if ($CURUSER['class'] >= UC_STAFF) {
	   $sendpmpos = $user['sendpmpos'] != 1;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$sendpmpos ? ' rowspan="2"' : '').">{$lang['userdetails_pmpos']}</td>
 	   <td align='left' width='20%'>".($sendpmpos ? "<input name='sendpmpos' value='42' type='radio' />Remove pm disablement" : "No disablement Status Set")."</td>\n";

     if ($sendpmpos)
     {
     if ($user['sendpmpos'] == 0)
     $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
     else
     $HTMLOUT .= "<td align='center'>Until ".get_date($user['sendpmpos'], 'DATE'). " (".mkprettytime($user['sendpmpos'] - time()). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>Disable for <select name="sendpmpos">
     <option value="0">------</option>
     <option value="1">1 week</option>
     <option value="2">2 weeks</option>
     <option value="4">4 weeks</option>
     <option value="8">8 weeks</option>
     <option value="255">Unlimited</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="pmdisable_pm" /></td></tr>';
     }
     }
     //==Shoutbox disable
     if ($CURUSER['class'] >= UC_STAFF) {
	   $chatpost = $user['chatpost'] != 1;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$chatpost ? ' rowspan="2"' : '').">{$lang['userdetails_chatpos']}</td>
 	   <td align='left' width='20%'>".($chatpost ? "<input name='chatpost' value='42' type='radio' />Remove Shout disablement" : "No disablement Status Set")."</td>\n";

     if ($chatpost)
     {
     if ($user['chatpost'] == 0)
     $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
     else
     $HTMLOUT .= "<td align='center'>Until ".get_date($user['chatpost'], 'DATE'). " (".mkprettytime($user['chatpost'] - time()). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>Disable for <select name="chatpost">
     <option value="0">------</option>
     <option value="1">1 week</option>
     <option value="2">2 weeks</option>
     <option value="4">4 weeks</option>
     <option value="8">8 weeks</option>
     <option value="255">Unlimited</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="chatdisable_pm" /></td></tr>';
     }
     }
     //==Immunity
     if ($CURUSER['class'] >= UC_STAFF) {
	   $immunity = $user['immunity'] != 0;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$immunity ? ' rowspan="2"' : '').">{$lang['userdetails_immunity']}</td>
 	   <td align='left' width='20%'>".($immunity ? "<input name='immunity' value='42' type='radio' />Remove immune Status" : "No immunity Status Set")."</td>\n";

      if ($immunity)
      {
      if ($user['immunity'] == 1)
      $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
      else
      $HTMLOUT .= "<td align='center'>Until ".get_date($user['immunity'], 'DATE'). " (".
            mkprettytime($user['immunity'] - time()). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>Immunity for <select name="immunity">
     <option value="0">------</option>
     <option value="1">1 week</option>
     <option value="2">2 weeks</option>
     <option value="4">4 weeks</option>
     <option value="8">8 weeks</option>
     <option value="255">Unlimited</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="immunity_pm" /></td></tr>';
     }
     }
     //==End
     //==Leech Warnings
     if ($CURUSER['class'] >= UC_STAFF) {
	   $leechwarn = $user['leechwarn'] != 0;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$leechwarn ? ' rowspan="2"' : '').">{$lang['userdetails_leechwarn']}</td>
 	   <td align='left' width='20%'>".($leechwarn ? "<input name='leechwarn' value='42' type='radio' />Remove Leechwarn Status" : "No leech warning Status Set")."</td>\n";

      if ($leechwarn)
      {
      if ($user['leechwarn'] == 1)
      $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
      else
      $HTMLOUT .= "<td align='center'>Until ".get_date($user['leechwarn'], 'DATE'). " (".
            mkprettytime($user['leechwarn'] - time()). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>leechwarn for <select name="leechwarn">
     <option value="0">------</option>
     <option value="1">1 week</option>
     <option value="2">2 weeks</option>
     <option value="4">4 weeks</option>
     <option value="8">8 weeks</option>
     <option value="255">Unlimited</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">PM comment:<input type="text" size="60" name="leechwarn_pm" /></td></tr>';
     }
     }
     //==End
     //==Warnings
     if ($CURUSER['class'] >= UC_STAFF) {
	   $warned = $user['warned'] != 0;
     $HTMLOUT .= "<tr><td class='rowhead'".(!$warned ? ' rowspan="2"' : '').">{$lang['userdetails_warned']}</td>
 	   <td align='left' width='20%'>".($warned ? "<input name='warned' value='42' type='radio' />Remove warned Status" : "No warning Status Set")."</td>\n";

      if ($warned)
      {
      if ($user['warned'] == 1)
      $HTMLOUT .= '<td align="center">(Unlimited Duration)</td></tr>';
      else
      $HTMLOUT .= "<td align='center'>Until ".get_date($user['warned'], 'DATE'). " (".
            mkprettytime($user['warned'] - time()). " to go)</td></tr>";
     } else
     {
     $HTMLOUT .= '<td>'.$lang['userdetails_warn_for'].'<select name="warned">
     <option value="0">'.$lang['userdetails_warn0'].'</option>
     <option value="1">'.$lang['userdetails_warn1'].'</option>
     <option value="2">'.$lang['userdetails_warn2'].'</option>
     <option value="4">'.$lang['userdetails_warn4'].'</option>
     <option value="8">'.$lang['userdetails_warn8'].'</option>
     <option value="255">'.$lang['userdetails_warninf'].'</option>
     </select></td></tr>
     <tr><td colspan="2" align="left">'.$lang['userdetails_pm_comm'].'<input type="text" size="60" name="warned_pm" /></td></tr>';
     }
     }
     //==End
          
      //==High speed
      if ($CURUSER["class"] < UC_SYSOP)
      $HTMLOUT .="<input type=\"hidden\" name=\"highspeed\" value=\"$user[highspeed]\" />\n";
      else {
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_highspeed']}</td><td class='row' colspan='2' align='left'><input type='radio' name='highspeed' value='yes' " .($user["highspeed"] == "yes" ? " checked='checked'" : "") ." />Yes <input type='radio' name='highspeed' value='no' " . ($user["highspeed"] == "no" ? " checked='checked'" : "") . " />No</td></tr>\n";
      }
     $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_park']}</td><td colspan='2' align='left'><input name='parked' value='yes' type='radio'" .
	   ($user["parked"] == "yes" ? " checked='checked'" : "") . " />{$lang['userdetails_yes']} <input name='parked' value='no' type='radio'" .
	   ($user["parked"] == "no" ? " checked='checked'" : "") . " />{$lang['userdetails_no']}</td></tr>\n";
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_reset']}</td><td colspan='2'><input type='checkbox' name='resetpasskey' value='1' /><font class='small'>{$lang['userdetails_pass_msg']}</font></td></tr>";
      // == seedbonus
      if ($CURUSER['class'] >= UC_STAFF)
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_bonus_points']}</td><td colspan='2' align='left'><input type='text' size='6' name='seedbonus' value='".(int)$user['seedbonus']."' /></td></tr>";
      // ==end
      // == rep
      if ($CURUSER['class'] >= UC_STAFF)
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_rep_points']}</td><td colspan='2' align='left'><input type='text' size='6' name='reputation' value='".(int)$user['reputation']."' /></td></tr>";
      // ==end
      //==Invites
      $HTMLOUT .= "<tr><td class='rowhead'>{$lang['userdetails_invright']}</td><td colspan='2' align='left'><input type='radio' name='invite_on' value='yes'" .($user["invite_on"]=="yes" ? " checked='checked'" : "") . " />{$lang['userdetails_yes']}<input type='radio' name='invite_on' value='no'" .($user["invite_on"]=="no" ? " checked='checked'" : "") . " />{$lang['userdetails_no']}</td></tr>\n";
      $HTMLOUT .= "<tr><td class='rowhead'><b>{$lang['userdetails_invites']}</b></td><td colspan='2' align='left'><input type='text' size='3' name='invites' value='" . htmlspecialchars($user['invites']) . "' /></td></tr>\n";
      
      $HTMLOUT.="<tr>
		  <td class='rowhead'>Avatar Rights</td>
		  <td colspan='2' align='left'><input name='view_offensive_avatar' value='yes' type='radio'".($user['view_offensive_avatar'] == "yes" ? " checked='checked'" : "")." />Yes 
		  <input name='view_offensive_avatar' value='no' type='radio'".($user['view_offensive_avatar'] == "no" ? " checked='checked'" : "")." />No </td>
	    </tr>	
	    <tr>
		  <td class='rowhead'>Offensive Avatar</td>
		  <td colspan='2' align='left'><input name='offensive_avatar' value='yes' type='radio'".($user['offensive_avatar'] == "yes" ? " checked='checked'" : "")." />Yes 
		  <input name='offensive_avatar' value='no' type='radio'".($user['offensive_avatar'] == "no" ? " checked='checked'" : "")." />No </td>
	    </tr>
	    <tr>
		  <td class='rowhead'>View Offensive Avatars</td>
		  <td colspan='2' align='left'><input name='avatar_rights' value='yes' type='radio'".($user['avatar_rights'] == "yes" ? " checked='checked'" : "")." />Yes 
		  <input name='avatar_rights' value='no' type='radio'".($user['avatar_rights'] == "no" ? " checked='checked'" : "")." />No </td>
	    </tr>";
      $HTMLOUT .= 
	    '<tr>
		  <td class="rowhead">Hit and Runs</td>
		  <td colspan="2" align="left"><input type="text" size="3" name="hit_and_run_total" value="'.$user['hit_and_run_total'].'" /></td>
	    </tr>
	    <tr>
		  <td class="rowhead">Suspended</td>
		  <td colspan="2" align="left"><input name="suspended" value="yes" type="radio"'.($user['suspended'] == 'yes' ? ' checked="checked"' : '').' />Yes 
		  <input name="suspended" value="no" type="radio"'.($user['suspended'] == 'no' ? ' checked="checked"' : '').' />No 
		  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Please enter the reason, it will be PMed to them<br />
		  <input type="text" size="60" name="suspended_reason" /></td>
	    </tr>';
      $HTMLOUT .="<tr>
		  <td class='rowhead'>Paranoia</td>
		  <td colspan='2' align='left'>
		  <select name='paranoia'>
		  <option value='0'".($user['paranoia'] == 0 ? " selected='selected'" : "").">Totally relaxed</option>
		  <option value='1'".($user['paranoia'] == 1 ? " selected='selected'" : "").">Sort of relaxed</option>
		  <option value='2'".($user['paranoia'] == 2 ? " selected='selected'" : "").">Paranoid</option>
		  <option value='3'".($user['paranoia'] == 3 ? " selected='selected'" : "").">Wears a tin-foil hat</option>
		  </select></td>
	    </tr> 
	    <tr>
		  <td class='rowhead'>Forum Rights</td>
		  <td colspan='2' align='left'><input name='forum_post' value='yes' type='radio'".($user['forum_post'] == "yes" ? " checked='checked'" : "")." />Yes 
		  <input name='forum_post' value='no' type='radio'".($user['forum_post'] == "no" ? " checked='checked'" : "")." />No Disable this members forum rights.</td>
	    </tr>
	    ";
      //Adjust up/down
      if ($CURUSER['class']>= UC_ADMINISTRATOR){
      $HTMLOUT .="<tr>
      <td class='rowhead'>{$lang['userdetails_addupload']}</td>
      <td align='center'>
      <img src='{$INSTALLER09['pic_base_url']}plus.gif' alt='Change Ratio' title='Change Ratio !' id='uppic' onclick=\"togglepic('{$INSTALLER09['baseurl']}', 'uppic','upchange')\" /> 
      <input type='text' name='amountup' size='10' />
      </td>
      <td>
      <select name='formatup'>\n
      <option value='mb'>{$lang['userdetails_MB']}</option>\n
      <option value='gb'>{$lang['userdetails_GB']}</option></select>\n
      <input type='hidden' id='upchange' name='upchange' value='plus' />
      </td>
      </tr>
      <tr>
      <td class='rowhead'>{$lang['userdetails_adddownload']}</td>
      <td align='center'>
      <img src='{$INSTALLER09['pic_base_url']}plus.gif' alt='Change Ratio' title='Change Ratio !' id='downpic' onclick=\"togglepic('{$INSTALLER09['baseurl']}','downpic','downchange')\" /> 
      <input type='text' name='amountdown' size='10' />
      </td>
      <td>
      <select name='formatdown'>\n
      <option value='mb'>{$lang['userdetails_MB']}</option>\n
      <option value='gb'>{$lang['userdetails_GB']}</option></select>\n
      <input type='hidden' id='downchange' name='downchange' value='plus' />
      </td></tr>";
      }
      $HTMLOUT .= "<tr><td colspan='3' align='center'><input type='submit' class='btn' value='{$lang['userdetails_okay']}' /></td></tr>\n";
      $HTMLOUT .= "</table>\n";
      $HTMLOUT .= "</form>\n";
      $HTMLOUT .= end_frame();
      }
      $HTMLOUT .= end_main_frame();
    
echo stdhead("{$lang['userdetails_details']} " . $user["username"]) . $HTMLOUT . stdfoot($stdfoot);
?>