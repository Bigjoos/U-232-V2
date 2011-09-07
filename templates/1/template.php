<?php
/**
 *   https://09source.kicks-ass.net:8443/svn/installer09/
 *   Licence Info: GPL
 *   Copyright (C) 2010 Installer09 v.2
 *   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.
 *   Project Leaders: Mindless,putyn,kidvision.
 **/
function stdhead($title = "", $msgalert = true, $stdhead = false) {
    global $CURUSER, $INSTALLER09, $lang, $free, $_NO_COMPRESS, $querytime, $query_stat, $q, $mc1, $BLOCKS, $CURBLOCK;
    if (!$INSTALLER09['site_online'])
    die("Site is down for maintenance, please check back again later... thanks<br />");
    require CACHE_DIR.'block_settings_cache.php';
    if ($title == "")
    $title = $INSTALLER09['site_name'] .(isset($_GET['tbv'])?" (".TBVERSION.")":'');
    else
    $title = $INSTALLER09['site_name'].(isset($_GET['tbv'])?" (".TBVERSION.")":''). " :: " . htmlspecialchars($title);  
    if ($CURUSER)
    {
    $INSTALLER09['stylesheet'] = isset($CURUSER['stylesheet']) ? "{$CURUSER['stylesheet']}.css" : $INSTALLER09['stylesheet'];
    }
    /** ZZZZZZZZZZZZZZZZZZZZZZZZZZip it! **/
    if (!isset($_NO_COMPRESS))
    if (!ob_start('ob_gzhandler'))
    ob_start();
    //== Include js files needed only for the page being used by pdq
    $js_incl = '';
    if ($stdhead['js'] != false) {
    foreach ($stdhead['js'] as $JS)
    $js_incl .= "<script type='text/javascript' src='{$INSTALLER09['baseurl']}/scripts/".$JS.".js'></script>";
    }
    if (isset($INSTALLER09['xhtml_strict'])) { //== Use strict mime type/doctype
    //== Only if browser/user agent supports xhtml
    if (stristr($_SERVER['HTTP_ACCEPT'],'application/xhtml+xml') && ($INSTALLER09['xhtml_strict'] === 1 || $INSTALLER09['xhtml_strict'] == $CURUSER['username'])) {
    header('Content-type:application/xhtml+xml; charset='.$INSTALLER09['char_set']);
    $doctype ='<?xml version="1.0" encoding="'.$INSTALLER09['char_set'].'"?>'.'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" '.'"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$INSTALLER09['language'].'">';
    }
    }
    if (!isset($doctype)) {
    header('Content-type:text/html; charset='.$INSTALLER09['char_set']);
    $doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"'.'"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.'<html xmlns="http://www.w3.org/1999/xhtml">';
    }
    $htmlout = $doctype."<head>
        <meta http-equiv='Content-Language' content='en-us' />
        <!-- ####################################################### -->
        <!-- #   This website is powered by installer09 source     # -->
        <!-- #   Download and support at: https://09source.kicks-ass.net # -->
        <!-- #   http://installer.me # -->
        <!-- ####################################################### -->
        <title>{$title}</title>
        <link rel='alternate' type='application/rss+xml' title='Latest Torrents' href='./rss.php?passkey={$CURUSER['passkey']}' />
        <link rel='stylesheet' href='templates/1/1.css' type='text/css' />
        <link rel='shortcut icon' href='favicon.ico' />
        <link rel='stylesheet' type='text/css' href='bbcode/markitup/skins/markitup/style.css' />
        <link rel='stylesheet' type='text/css' href='bbcode/markitup/sets/bbcode/style.css' />
        <script type='text/javascript' src='./scripts/jquery.js'></script>
        <script type='text/javascript' src='./scripts/jquery.lightbox-0.5.min.js'></script>
        <link rel='stylesheet' type='text/css' href='css/jquery.lightbox-0.5.css' media='screen' />
        <script type='text/javascript' src='./scripts/jquery.status.js'></script>
        <script type='text/javascript'>
        /*<![CDATA[*/
        function themes() {
        window.open('take_theme.php','My themes','height=150,width=200,resizable=no,scrollbars=no,toolbar=no,menubar=no');
        }
        /*]]>*/
        </script>
        <script type='text/javascript'>
        /*<![CDATA[*/
        function radio() {
        window.open('radio_popup.php','My Radio','height=700,width=800,resizable=no,scrollbars=no,toolbar=no,menubar=no');
        }
        /*]]>*/
       </script>
        <script type='text/javascript'>
        /*<![CDATA[*/
        $('document').ready(function () { 
        $(\"a[rel='lightbox']\").lightBox(); // Select all links that contains lightbox in the attribute rel 
        });
        /*]]>*/
        </script>
        {$js_incl}</head>
        <body>
        <!-- Installer09 Source - Print Content Holder (Margin site) -->
        <div id='base_around'>
        <!-- Installer09 Source - Print Global Content -->
        <div id='base_content'>
        <!-- Installer09 Source - Print Header -->
        <div id='base_header_line'></div>
        <div id='base_header'>";
        if ($CURUSER) 
        {
        $htmlout .= StatusBar();
        }
        if ($CURUSER) 
       {
       $htmlout .= "
       <!-- Installer09 Source - Print Logo (CSS Controled) -->
       <div id='base_logo'>
       <img src='templates/1/images/logo.png' alt='' />
       </div>";
       }
       if ($CURUSER) 
       {
       $htmlout .= " </div>
       <!-- Installer09 Source - Print Navigation -->
       <div id='base_menu'><div id='mover'>
       <ul class='navigation'>
       <li><a href='index.php'><span class='nav'>HOME</span></a></li>
       <li><a href='browse.php'><span class='nav'>TORRENTS</span></a></li>
        <li><a href='viewrequests.php'><span class='nav'>REQUEST</span></a></li>
         <li><a href='upload.php'><span class='nav'>UPLOAD</span></a></li>
          <li><a href='search.php'><span class='nav'>SEARCH</span></a></li>
           <li><a href='forums.php'><span class='nav'>FORUMS</span></a></li>
          <li><a href='chat.php'><span class='nav'>IRC</span></a></li>
         <li><a href='topten.php'><span class='nav'>STATISTIC</span></a></li>
        <li><a href='rules.php'><span class='nav'>RULES</span></a></li>
       <li><a href='faq.php'><span class='nav'>FAQ</span></a></li>
      <li><a href='staff.php'><span class='nav'>STAFF</span></a></li>
      </ul>
      </div></div>
      <!-- Installer09 Source - Print Global Messages -->
      <div id='base_globelmessage'>
      <div id='gm_taps'>
      <ul class='gm_taps'>
      <li><b>Current Site Alerts:</b></li>";

	    if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_REPORTS && $BLOCKS['global_staff_report_on']){
	    require(BLOCK_DIR.'global/report.php');
	    }

	    if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_UPLOADAPP && $BLOCKS['global_staff_uploadapp_on']){
	    require(BLOCK_DIR.'global/uploadapp.php');
	    }

      if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_HAPPYHOUR && $BLOCKS['global_happyhour_on']){
      require(BLOCK_DIR.'global/happyhour.php');
      }

	    if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_STAFF_MESSAGE && $BLOCKS['global_staff_warn_on']){
	    require(BLOCK_DIR.'global/staffmessages.php');
	    }

      if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_NEWPM && $BLOCKS['global_message_on']){
      require(BLOCK_DIR.'global/message.php');
      }

	    if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_DEMOTION && $BLOCKS['global_demotion_on']){
	    require(BLOCK_DIR.'global/demotion.php');
	    } 

      if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_FREELEECH && $BLOCKS['global_freeleech_on']){
      require(BLOCK_DIR.'global/freeleech.php');
      }

      if (curuser::$blocks['global_stdhead'] & block_stdhead::STDHEAD_CRAZYHOUR && $BLOCKS['global_crazyhour_on']){
      require(BLOCK_DIR.'global/crazyhour.php');
      }
      $htmlout .="</ul></div>";
      }
      $htmlout .="</div>   
      <!-- OLD DESING BELOW -->   
      <table class='mainouter' width='100%' border='0' cellspacing='0' cellpadding='10'>
      <tr><td align='center' class='outer' style='padding-bottom: 20px'>";
      return $htmlout;
      } // stdhead


function stdfoot($stdfoot = false) {
global $querytime, $CURUSER, $INSTALLER09, $q, $queries, $query_stat, $mc1, $start;
   
    $debug       = array(1,10,14,15,2);
    $debug_ids   = (SQL_DEBUG && in_array($CURUSER['id'], $debug) ? 1 : 0);
    $cachetime   = ($mc1->Time/1000);
    $seconds     = microtime(true) - $q['start'];
    $phptime     = $seconds - $querytime;
    $phptime     = $phptime - $cachetime;
    $queries     = (!empty($queries) ? $queries : 0);
    $percentphp  = number_format(($phptime / $seconds) * 100, 2);
    $percentsql  = number_format(($querytime / $seconds) * 100, 2);
    $percentmc   = number_format(($cachetime / $seconds) * 100, 2);
    $howmany     = ($queries != 1 ? 's ' : ' ');
    $serverkillers = $queries > 4 ? '<br />'.($queries/2).' Server killers ran to show you this page :) ! =[' : '=]';
    
    $MemStats = $mc1->get_value('mc_hits');
    if ($MemStats === false) {
    $MemStats = $mc1->getStats();
    $MemStats['Hits'] = (($MemStats['get_hits']/$MemStats['cmd_get'] < 0.7) ? '' : number_format(($MemStats['get_hits']/$MemStats['cmd_get'])*100, 3));
    $mc1->cache_value('mc_hits', $MemStats, 10);
    }
    
    $header = ''; 
    $header .= number_format($cachetime, 5).'s';
    $header = round($percentmc, 2).'&#37; Memcached: '.number_format($cachetime, 5).'s Hits: '.$MemStats['Hits'].'% Misses: '.(100 - $MemStats['Hits']).'% Items: '.number_format($MemStats['curr_items']);
    
    $htmlfoot = ''; 
    /** query stats **/
    /** include js files needed only for the page being used by pdq **/
    $htmlfoot .= '<!-- javascript goes here -->';
    if ($stdfoot['js'] != false) {
    foreach ($stdfoot['js'] as $JS)
    $htmlfoot .= '<script type="text/javascript" src="'.$INSTALLER09['baseurl'].'/scripts/'.$JS.'.js"></script>';
    }
    
    if ($debug_ids) {
    if ($q['query_stat']) {
    $htmlfoot .= "<br />
	  <div align='center' class='headline'>Querys</div>
	  <div class='headbody'>
	  <table width='100%' align='center' cellspacing='5' cellpadding='5' border='0'>
		<tr>
		<td class='colhead' width='5%'  align='center'>ID</td>
		<td class='colhead' width='10%' align='center'>Query Time</td>
		<td class='colhead' width='85%' align='left'>Query String</td>
		</tr>";
    foreach ($q['query_stat'] as $key => $value) {
    $htmlfoot  .= "<tr>
		<td align='center'>".($key + 1)."</td>
		<td align='center'><b>". ($value['seconds'] > 0.01 ?
		"<font color='red' title='You should optimize this query.'>".
    $value['seconds']."</font>" : "<font color='green' title='Query good.'>".
	  $value['seconds']."</font>")."</b></td>
		<td align='left'>".htmlspecialchars($value['query'])."<br /></td>
		</tr>";	   		   
    }
    $htmlfoot .='</table></div>';
    }
    }
    $htmlfoot .="</td></tr></table>";
    if ($CURUSER) 
    {
    $htmlfoot .= "
       </div> <!-- Ends Page Content -->
       </div> <!-- Ends Content holder -->
       <div id='footer'><div id='footer_left'>
        Generated in ".(round($seconds, 4))." Seconds<br /> 
        Server Raided <b>".$queries."</b> Time<b>'".$howmany."</b>Using&nbsp;:&nbsp;<b>".$percentphp."</b>&nbsp;&#37;&nbsp;php&nbsp;&#38;&nbsp;<b>".$percentsql."</b>&nbsp;&#37;&nbsp;sql <b>".$serverkillers."</b>.<br /><b>".$header."</b>
       </div>
      <div id='footer_right'>
       Powered by <a href='https://09source.kicks-ass.net'>Installer09</a><br />
       Using Valid <b>CSS3, HTML & PHP</b><br />
       Support Forum <b>Click <a href='https://09source.kicks-ass.net/smf/index.php'>here</a></b>";
       }

    $htmlfoot .="</div>
    </div> <!-- Ends Footer -->
    </body></html>\n";
    return $htmlfoot;
    }

function stdmsg($heading, $text)
{
    $htmlout = "<table class='main' width='750' border='0' cellpadding='0' cellspacing='0'>
    <tr><td class='embedded'>\n";
    if ($heading)
      $htmlout .= "<h2>$heading</h2>\n";
    $htmlout .= "<table width='100%' border='1' cellspacing='0' cellpadding='10'><tr><td class='text'>\n";
    $htmlout .= "{$text}</td></tr></table></td></tr></table>\n";
    return $htmlout;
}
function StatusBar() {
	global $CURUSER, $INSTALLER09, $lang, $rep_is_on, $mc1, $msgalert;
	if (!$CURUSER)
	return "";
	$upped = mksize($CURUSER['uploaded']);
	$downed = mksize($CURUSER['downloaded']);

  //==Memcache unread pms
	$PMCount=0;
	$unread1 = $mc1->get_value('inbox_new_sb_'.$CURUSER['id']);
  if ($unread1 === false) {
	$res1 = sql_query("SELECT COUNT(id) FROM messages WHERE receiver='".$CURUSER['id']."' AND unread = 'yes' AND location='1'") or sqlerr(__LINE__,__FILE__);
	list($PMCount) = mysql_fetch_row($res1); 
  $PMCount= (int)$PMCount;
  $unread1 = $mc1->cache_value('inbox_new_sb_'.$CURUSER['id'], $PMCount, $INSTALLER09['expires']['unread']);
  }
	$inbox = ($unread1 == 1 ? "$unread1&nbsp;{$lang['gl_msg_singular']}" : "$unread1&nbsp;{$lang['gl_msg_plural']}");
  //==Memcache peers
  $MyPeersCache = $mc1->get_value('MyPeers_'.$CURUSER['id']);
if ($MyPeersCache == false) {
    $seed['yes'] = $seed['no'] = 0;
    $seed['conn'] = 3;
    
      $r = sql_query("select count(id) as count, seeder, connectable FROM peers WHERE userid=".$CURUSER['id']." group by seeder") ; 
       while($a = mysql_fetch_assoc($r)) {
        $key = $a['seeder'] == 'yes' ? 'yes' : 'no'; 
        $seed[$key] = number_format(0+$a['count']);    
        $seed['conn'] = $a['connectable'] == 'no' ? 1 : 2;
    }  
   $mc1->cache_value('MyPeers_'.$CURUSER['id'], $seed, $INSTALLER09['expires']['MyPeers_']);
   unset($r, $a);        
} else {
    $seed = $MyPeersCache;
}
// for display connectable  1 / 2 / 3 
 if (!empty($seed['conn'])) {
       switch ($seed['conn']){ 
       case 1:
         $connectable = "<img src='{$INSTALLER09['pic_base_url']}notcon.png' alt='Not Connectable' title='Not Connectable' />";
       break;
       case 2:
         $connectable = "<img src='{$INSTALLER09['pic_base_url']}yescon.png' alt='Connectable' title='Connectable' />";
       break;
       default :
         $connectable = "N/A";
       }
    }
    else
        $connectable = 'N/A';
	//////////// REP SYSTEM /////////////
    $member_reputation = get_reputation($CURUSER);
    ////////////// REP SYSTEM END //////////
    $usrclass="";
    if ($CURUSER['override_class'] != 255) $usrclass = "&nbsp;<b>(".get_user_class_name($CURUSER['class']).")</b>&nbsp;";
    else
    if ($CURUSER['class'] >= UC_STAFF)
    $usrclass = "&nbsp;<a href='./setclass.php'><b>(".get_user_class_name($CURUSER['class']).")</b></a>&nbsp;";
	  $StatusBar = '';
		$StatusBar = "
       <!-- Installer09 Source - Print Statusbar/User Menu -->
       <script type='text/javascript'>
       //<![CDATA[
       function showSlidingDiv(){
       $('#slidingDiv').animate({'height': 'toggle'}, { duration: 1000 });
       }
       //]]>
       </script>
      <div id='base_header_fly'>
       <div id='base_usermenu'>{$lang['gl_msg_welcome']},&nbsp;".format_username($CURUSER)."<span class='base_usermenu_arrow'><a href='#' onclick='showSlidingDiv(); return false;'><img src='templates/1/images/usermenu_arrow.png' alt='' /></a></span></div>
        <div id='slidingDiv'>
         <div class='slide_head'>:: Personal Stats</div>
         <div class='slide_a'>User Class</div><div class='slide_b'>{$usrclass}</div>
         <div class='slide_c'>Reputation</div><div class='slide_d'>$member_reputation</div>
         <div class='slide_a'>Invites</div><div class='slide_b'><a href='./invite.php'>{$CURUSER['invites']}</a></div>
         <div class='slide_c'>Bonus Points</div><div class='slide_d'><a href='./mybonus.php'>{$CURUSER['seedbonus']}</a></div>
         <div class='slide_head'>:: Torrent Stats</div>
         <div class='slide_a'>Share Ratio</div><div class='slide_b'>".member_ratio($CURUSER['uploaded'], $CURUSER['downloaded'])."</div>
         <div class='slide_c'>Uploaded</div><div class='slide_d'>$upped</div>
         <div class='slide_a'>Downloaded</div><div class='slide_b'>$downed</div>
         <div class='slide_c'>Uploading Files</div><div class='slide_d'>{$seed['yes']}</div>
         <div class='slide_a'>Downloading Files</div><div class='slide_b'>{$seed['no']}</div>
         <div class='slide_c'>Connectable</div><div class='slide_d'>{$connectable}</div>
         <div class='slide_head'>:: Games &amp; Playhouse</div>
         <div class='slide_a'>Play Blackjack</div><div class='slide_b'><a href='./blackjack.php'>Play here</a></div>
         <div class='slide_c'>Play Casino</div><div class='slide_d'><a href='./casino.php'>Play here</a></div>
         <div class='slide_head'>:: Information</div>
         <div class='slide_a'>Contact Staff</div><div class='slide_b'><a href='./contactstaff.php'>Send Message</a></div>
         <div class='slide_c'>Change Theme</div><div class='slide_d'><a href='#' onclick='themes();'>Click here</a></div>
         <div class='slide_a'>Radio</div><div class='slide_b'><a href='#' onclick='radio();'>Click here</a></div>
         <div class='slide_c'>Donate us</div><div class='slide_d'><a href='./donate.php'>Click here</a></div>
         <div class='slide_a'>Torrent Freak News</div><div class='slide_b'><a href='./rsstfreak.php'>Click here</a></div>
         ".(isset($CURUSER) && $CURUSER['class'] <= UC_VIP ? "
         <div class='slide_c'>Uploader App</div><div class='slide_d'><a href='uploadapp.php'>Send Application</a></div>":"")."
        ".(isset($CURUSER) && $CURUSER['got_blocks'] == 'yes' ? "
         <div class='slide_head'>:: Site Config</div>
         <div class='slide_a'>My Blocks</div><div class='slide_b'><a href='./user_blocks.php'>Click here</a></div>":"")."
         </div>
         <div id='base_icons'>
         <ul class='um_menu'>
         <li><a href='messages.php'><img src='templates/1/images/main.jpg' alt='' title='Your Private Messages' /></a></li>
         <li><a href='usercp.php'><img src='templates/1/images/settings.jpg' alt='Settings' title='Personal Settings' /></a></li>
         ".(isset($CURUSER) && $CURUSER['class'] >= UC_STAFF ? "<li><a href='staffpanel.php'><img src='templates/1/images/staff.png' alt='Staff' title='Staffpanel' /></a></li>":"")."
        <li><a href='logout.php'><img src='templates/1/images/signout.jpg' alt='Logout' title='SignOut' /></a></li>
        </ul>
       </div>
      </div>";
    return $StatusBar;
    }
?>